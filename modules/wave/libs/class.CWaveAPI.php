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

	/**
	 * Метод заменяющий конструктор. Используется для инициализации.
	 */
	private function init()
	{
		$this->obPosts=new CWavePosts();
		$this->obVoteLocks=new CObject('wave_rating_locks');
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
					if(!$KS_EVENTS_HANDLER->Execute("wave", "onGetAnswerRight",$arCheckArray)) throw new CError('WAVE_ACCESS_ANSWER_DENIED');
					if($arChild=$this->obPosts->GetRecord(array('parent_id'=>$arParentPost['id'])))
					{
						return $this->UpdatePost($arChild['id'],$arFields);
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
	 * Метод удаляет сообщение и всех его потомков
	 */
	function Delete($id)
	{
		global $USER;
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
					pre_print($arPosts);
					die();
				}
				return $this->obPosts->DeleteItems(array($arPost['id']));
			}
			else
			{
				throw new CError('WAVE_POST_NOT_FOUND');
			}
		}
		return false;
	}
}