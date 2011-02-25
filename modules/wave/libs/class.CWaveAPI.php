<?php
/**
 * @file /modules/wave/class.CWaveAPI.php
 * Файл содержит в себе класс АПИ модуля wave
 * Файл проекта kolos-cms.
 *
 * @since 22.02.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CBaseAPI.php';
include_once MODULES_DIR.'/wave/libs/class.CWavePosts.php';

/**
 * Класс обеспечивает высокоуровневые функции для модуля wave
 */
class CWaveAPI extends CBaseAPI
{
	static private $obInstance;
	private $obPosts;
	private $obVoteLocks;
	private $sMode;

	/**
	 * Метод заменяющий конструктор. Используется для инициализации.
	 */
	private function init()
	{
		global $KS_MODULES;
		$this->obPosts=new CWavePosts();
		$this->obVoteLocks=new CObject('wave_rating_locks');
		$this->sMode=$KS_MODULES->GetConfigVar('wave','mode','list');
	}

	/**
	 * This implements the 'singleton' design pattern
   	 *
     * @return object CMain The one and only instance
     */
  	static function get_instance()
  	{
	    if (!self::$obInstance)
	    {
    		self::$obInstance = new CWaveAPI();
      		self::$obInstance->init();  // init AFTER object was linked with self::$instance
    	}
	    return self::$obInstance;
  	}

	/**
	 * Метод возвращает объект постов
	 */
	function Posts()
	{
		return $this->obPosts;
	}

	/**
	 * Метод возвращает права текущего пользователя на доступ к различным функциям определённого поста
	 * @param $id mixed номер поста или тело поста
	 */
	function GetPostRights($id)
	{
		global $KS_EVENTS_HANDLER,$KS_MODULES,$USER;

		if(!is_array($id))
		{
			$arPost=$this->GetById($id);
		}
		else
		{
			$arPost=$id;
		}
		if(!$arPost) throw new CError('WAVE_POST_NOT_FOUND');
		$arAccess=array(
			'canAnswer'=>false,
			'canEdit'=>false,
			'canDelete'=>false,
			'canModerate'=>false,
		);
		if($this->sMode!='list')
		{
			if($this->sMode=='tree')
			{
				if($arPost['depth']<$KS_MODULES->GetConfigVar('wave','max_depth',100))
					$arAccess['canAnswer']=$USER->GetLevel('wave')<=KS_ACCESS_WAVE_ANSWER;
			}
			elseif($this->sMode=='answer')
			{
				if($arPost['depth']==1)
				{
					$arAccess['canAnswer']=$USER->GetLevel('wave')<=KS_ACCESS_WAVE_ANSWER;
					if($KS_EVENTS_HANDLER->HasHandler('wave','onGetAnswerRight'))
					{
						$arCheckArray=array(
							'parent'=>$arPost,
							'new'=>false,
						);
						$arAccess['canAnswer']=$KS_EVENTS_HANDLER->Execute("wave", "onGetAnswerRight",$arCheckArray);
					}
				}
			}
		}
		if($USER->ID()>0)
		{
			if($USER->GetLevel('wave')>KS_ACCESS_WAVE_MODERATE)
			{
				$arAccess['canEdit']=$arPost['user_id']==$USER->ID();
			}
			else
			{
				$arAccess['canEdit']=true;
				$arAccess['canDelete']=true;
				$arAccess['canModerate']=true;
			}
		}
		return $arAccess;
	}

	/**
	 * Метод добавляет новый ответ на сообщение
	 */
	function AddAnswer($hash,$parent_id,$arInFields)
	{
		global $USER,$KS_MODULES,$KS_EVENTS_HANDLER;
		if(!is_array($arInFields)) throw new CDataError('SYSTEM_WRONG_DATA_FORMAT');
		$iLevel=$USER->GetLevel('wave');
		if($iLevel>KS_ACCESS_WAVE_ADD_GUEST) throw new CAccessError('WAVE_ACCESS_POST');
		$arFields=array(
			'user_email'=>$arInFields['user_email'],
			'user_name'=>$arInFields['user_name'],
			'content'=>$arInFields['content'],
			'user_id'=>$USER->ID(),
			'date_add'=>time(),
			'hash'=>$hash,
		);
		$arUserFields=$this->obPosts->GetUserFields();
		if(is_array($arUserFields) && count($arUserFields)>0)
		{
			foreach($arUserFields as $arField)
			{
				$arFields['ext_'.$arField['title']]=$arInFields['ext_'.$arField['title']];
			}
		}
		if($this->CheckRequiredFields($arFields)>0) throw new CDataError('WAVE_FIELDS_ERROR');
		//Если код родительского сообщения больше 0 то ищем его
		if($parent_id>0)
		{
			$sMode=$KS_MODULES->GetConfigVar('wave','mode','list');
			if($sMode=='list') throw new CError('WAVE_ACCESS_ANSWER_DENIED');
			if($arParentPost=$this->obPosts->GetRecord(array('id'=>$parent_id)))
			{
				if($sMode=='answer')
				{
					if($arParentPost['depth']>1) throw new CError('WAVE_ACCESS_ANSWER_DENIED');
					$arCheckArray=array(
						'parent'=>$arParentPost,
						'new'=>$arFields,
					);
					if(!$KS_EVENTS_HANDLER->Execute("wave", "onGetAnswerRight",$arCheckArray))
						throw new CError('WAVE_ACCESS_ANSWER_DENIED');
					if($arChild=$this->obPosts->GetRecord(array('parent_id'=>$arParentPost['id'])))
					{
						return $this->obPosts->Update($arChild['id'],$arFields);
					}
				}
				if($arParentPost['depth']+1>$KS_MODULES->GetConfigVar('wave','max_depth',10))
				{
					$parent_id=$arParentPost['parent_id'];
				}
				$arFields['parent_id']=$parent_id;
			}
			else
			{
				throw new CError('WAVE_POST_NOT_FOUND');
			}
		}
		else
		{
			$arFields['parent_id']=0;
		}
		if(IsEmpty($arFields['content'])) throw new CError('WAVE_TEXT_ERROR');
		if($iLevel>=KS_ACCESS_WAVE_ADD_GUEST)
			$arFields['active']=0;
		else
			$arFields['active']=1;
		return $this->obPosts->Save('',$arFields);
	}

	/**
	 * Метод добавляет новое сообщение и возвращает его номер
	 */
  	function AddPost($hash,$arFields)
  	{
		return $this->AddAnswer($hash,0,$arFields);
	}

	/**
	 * Метод скрывает сообщение в пользовательской части
	 */
	function Hide($id)
	{
		global $USER;
		if($USER->GetLevel('wave')<=KS_ACCESS_WAVE_MODERATE && $this->obPosts->GetRecord(array('id'=>$id)))
			return $this->obPosts->Update(intval($id),array('active'=>0));
		return false;
	}

	/**
	 * Метод отображает сообщение в пользовательской части
	 */
	function Show($id)
	{
		global $USER;
		if($USER->GetLevel('wave')<=KS_ACCESS_WAVE_MODERATE && $this->obPosts->GetRecord(array('id'=>$id)))
			return $this->obPosts->Update(intval($id),array('active'=>1));
		return false;
	}

	/**
	 * Метод возвращает список полей которые могут выводиться при редактировании
	 * поста
	 */
	function GetPostFields()
	{
		global $KS_MODULES;
		$arStandartFields=array(
			array(
				'title'=>'content',
				'description'=>$KS_MODULES->GetText('field_content')
			),
			array(
				'title'=>'user_name',
				'description'=>$KS_MODULES->GetText('field_user_name')
			),
			array(
				'title'=>'user_email',
				'description'=>$KS_MODULES->GetText('field_user_email')
			),
			array(
				'title'=>'captcha',
				'description'=>$KS_MODULES->GetText('field_captcha')
			),
		);
		$arUserFields=$this->obPosts->GetUserFields();
		$arWavePosts=array_merge($arStandartFields,$arUserFields);
		return $arWavePosts;
	}

	/**
	 * Метод возвращает список полей вывода которые настроены в административной части
	 */
	function GetFormFields()
	{
		global $KS_MODULES;
		$arConfig=$KS_MODULES->GetConfigArray('wave');
		$arFields=$this->GetPostFields();
		$arResult=array();
		foreach($arFields as $arField)
		{
			if($arConfig['field_show_'.$arField['title']]==1)
				$arResult[$arField['title']]=$arConfig['field_title_'.$arField['title']]!=''?$arConfig['field_title_'.$arField['title']]:$arField['description'];
		}
		return $arResult;
	}

	/**
	 * Метод проверяет заполненность обязательных полей
	 */
	function CheckRequiredFields($arData)
	{
		global $KS_MODULES;
		$arConfig=$KS_MODULES->GetConfigArray('wave');
		$arFields=$this->GetPostFields();
		$bError=0;
		foreach($arFields as $arField)
		{
			if($arField['title']=='captcha') continue;
			if($arConfig['field_necessary_'.$arField['title']]==1)
				if(IsEmpty($arData[$arField['title']]))
				{
					$bError+=$KS_MODULES->AddNotify('WAVE_EMPTY_NECESSARY_FIELD',$arConfig['field_title_'.$arField['title']]!=''?$arConfig['field_title_'.$arField['title']]:$arField['description']);
				}
		}
		return $bError;
	}

	/**
	 * Метод удаляет сообщение и всех его потомков
	 */
	function Delete($id)
	{
		global $USER,$KS_MODULES;
		if($USER->GetLevel('wave')<=KS_ACCESS_WAVE_MODERATE)
		{
			if($arPost=$this->obPosts->GetRecord(array('id'=>$id)))
			{
				$arFilter=array(
					'hash'=>$arPost['hash'],
					'>left_margin'=>$arPost['left_margin'],
					'<=left_margin'=>$arPost['right_margin'],
				);
				if($arPosts=$this->obPosts->GetList(false,$arFilter))
				{
					if($KS_MODULES->GetConfigVar('wave','use_ratings','no')=='usefullness')
					{
						$this->obVoteLocks->DeleteItems(array('->comment_id'=>array_keys($arPosts)));
					}
					$this->obPosts->DeleteItems(array('->id'=>array_keys($arPosts)));
				}
				if($KS_MODULES->GetConfigVar('wave','use_ratings','no')=='usefullness')
				{
					$this->obVoteLocks->DeleteItems(array('comment_id'=>$arPost['id']));
				}
				return $this->obPosts->DeleteItems(array('id'=>$arPost['id']));
			}
			else
			{
				throw new CError('WAVE_POST_NOT_FOUND');
			}
		}
		return false;
	}
}