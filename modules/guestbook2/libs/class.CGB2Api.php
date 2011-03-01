<?php
/**
 * \file class.CGB2Api.php
 * Сюда сделать описание файла
 * Файл проекта kolos-cms.
 * 
 * Создан 07.12.2009
 *
 * \author blade39
 * \version 
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

//==================== Блок констант для уровней доступа ==============================

define('KS_ACCESS_GB2_FULL',0);
define('KS_ACCESS_GB_CONFIG',2);
define('KS_ACCESS_GB2_REPLY',3);
define('KS_ACCESS_GB2_ANSWER',7);
define('KS_ACCESS_GB2_ANSWER_GUEST',8);
define('KS_ACCESS_GB2_VIEW',9);
define('KS_ACCESS_GB2_DENIED',10);

//==================== Блок инклудов требуемых библиотек ==============================
include_once(MODULES_DIR . "/guestbook2/libs/class.CGB2Answers.php");
include_once(MODULES_DIR . "/guestbook2/libs/class.CGB2Posts.php");
include_once(MODULES_DIR . "/guestbook2/libs/class.CGB2Categories.php");


class CGB2API extends CBaseAPI
{
	/**
	 * @todo Попробовать сделать скрытыми
	 */
	public $obPosts;
	public $obAnswers;
	public $obCategories;
	
	static private $instance;
	
	/**
	 * This implements the 'singleton' design pattern
   	 *
     * @return object CEShopAPI The one and only instance
     */
  	static function get_instance()
  	{
	    if (!self::$instance) 
	    {
    		self::$instance = new CGB2API();
      		self::$instance->startup();  // init AFTER object was linked with self::$instance
    	}
	    return self::$instance;
  	}
	
	function __construct()
	{
	}
	
	private function startup()
	{
		$this->obPosts=new CGB2Posts();
		$this->obAnswers=new CGB2Answers();
		$this->obCategories=new CGB2Categories();
	}
	
	/**
	 * Метод возращает поля сообщения
	 */
	public function GetPostFields()
	{
		return $this->obPosts->arFields;
	}
	
	/**
	 * Метод возвращает список полей категории
	 */
	public function GetCategoryFields()
	{
		return $this->obCategories->arFields;
	}
	
	/**
	 * Метод возращает поля ответа
	 */
	public function GetAnswerFields()
	{
		return $this->obAnswers->arFields;
	}
	
	/**
	 * Метод добавляет сообщение в базу данных
	 */
	public function AddPost($arPost)
	{
		global $KS_MODULES;
		$this->obPosts->AddAutoField('id');
		$arPost['date_shown']=time();
		if($KS_MODULES->GetConfigVar('guestbook2','use_tags')==0)
		{
			if(strip_tags($arPost['content'])!=$arPost['content'])
				throw new CDataError('GB2_TAGS_NOT_ALLOWED');
		}
		if($KS_MODULES->GetConfigVar('guestbook2','no_empty_category')==1)
		{
			if($arPost['category_id']==0)
				throw new CDataError('GB2_CANT_GO_EMPTY_CATEGORY');
		}
		$this->obPosts->Save('',$arPost);
	}
	
	/**
	 * Метод выполняет скрытие поста
	 */
	public function HidePost($id)
	{
		$this->obPosts->Update($id,array('active'=>0));
	}
	
	/**
	 * Метод выполняет отображение поста
	 */
	public function ShowPost($id)
	{
		$this->obPosts->Update($id,array('active'=>1));
	}
	
	/**
	 * Метод возвращает список категорий
	 */
	public function GetCategories($arSort=false)
	{
		global $KS_MODULES;
		if(!$arSort) $arSort=array('orderation'=>'asc');
		if($arCategories=$this->obCategories->GetList($arSort,array('active'=>1)))
		{
			if($KS_MODULES->GetConfigVar('guestbook2','no_empty_category')==0)
				array_unshift($arCategories,array('id'=>0,'title'=>'Не указано'));
			return $arCategories;
		}
		return false;
	}
	
	/**
	 * Метод получает данные о сообщении
	 */
	public function GetPost($id)
	{
		$arData=$this->obPosts->GetRecord(array('id'=>$id));
		if(is_array($arData)&&($arData['id']==$id))
		{
			$arData['answer']=$this->obAnswers->GetRecord(array('post_id'=>$id));
			return $arData;
		}
		return false;
	}
	
	public function UpdatePost($post_id,$prefix,$data)
	{
		if($arPost=$this->GetPost($post_id))
		{
			$data[$prefix.'id']=$post_id;
			$this->obPosts->Save($prefix,$data);
		}
	}
	
	/**
	 * Метод добавляет ответ к сообщению пользователя
	 */
	public function AddAnswer($post_id,$answer)
	{
		global $USER;
		if($arPost=$this->GetPost($post_id))
		{
			$arData=$this->obAnswers->GetRecord(array('post_id'=>$post_id));
			if(is_array($arData)&&($arData['post_id']==$post_id))
			{
				$arFields=array(
					'content'=>$answer,
					'user_id'=>$USER->ID(),
					'date'=>time(),
				);
				$this->obPosts->Update($post_id,array('date_answer'=>time()));
				$this->obAnswers->Update($arData['id'],$arFields);
			}
			else
			{
				$arFields=array(
					'content'=>$answer,
					'user_id'=>$USER->ID(),
					'date'=>time(),
					'post_id'=>$post_id,
				);
				$this->obPosts->Update($post_id,array('date_answer'=>time()));
				$this->obAnswers->Save('',$arFields);
			}
		}
	}
	
	/**
	 * Метод возвращает список сообщений и ответов на них отфильтрованных и с постраничной навигацией
	 */
	public function GetPosts($arOrder=false,$arFilter=false,&$obPages)
	{
		global $USER;
		$iCount=$this->obPosts->Count($arFilter);
		$arFilter['<?'.$this->obPosts->sTable.'.user_id']=$USER->sTable.'.id';
		$arSelect=$this->GetPostFields();
		foreach($USER->arFields as $sItem)
			$arSelect[]=$USER->sTable.'.'.$sItem;
		$arData=$this->obPosts->GetList($arOrder,$arFilter,$obPages->GetLimits($iCount),$arSelect);
		$arID2Key=array();
		foreach($arData as $key=>$arItem)
		{
			$arID2Key[$arItem['id']]=$key;
		}
		if(count($arID2Key)>0)
		{
			//Если есть хотябы один ответ, надо его приклеить
			$arAFilter=array(
				'->post_id'=>array_keys($arID2Key),
			);
			if(array_key_exists($arFilter['active']))
			{
				$arAFilter['active']=$arFilter['active'];
			}
			$arAFilter['<?'.$this->obAnswers->sTable.'.user_id']=$USER->sTable.'.id';
			$arSelect=$this->GetAnswerFields();
			foreach($USER->arFields as $sItem)
				$arSelect[]=$USER->sTable.'.'.$sItem;
			$arAnswers=$this->obAnswers->GetList(array('id'=>'asc'),$arAFilter,false,$arSelect);
			foreach($arAnswers as $arItem)
			{
				$arData[$arID2Key[$arItem['post_id']]]['answer']=$arItem;
			}
		}
		return $arData;
	}
	
	/**
	 * Метод выполняет удаление постов с указанными номерами
	 */
	function DeletePost($id)
	{
		if(is_numeric($id))
		{
			$id=array($id);
		}
		$this->obPosts->DeleteItems(array('->id'=>$id));
		$this->obAnswers->DeleteItems(array('->post_id'=>$id));
	}
	
	/**
	 * Метод выполняет удаление категорий с указанными номерами
	 */
	function DeleteCategory($id)
	{
		if(is_numeric($id)) $id=array($id);
		$arFilter=array(
			'->id'=>$id
		);
		if($arCategories=$this->obCategories->GetList(array('id'=>'asc'),$arFilter))
		{
			foreach($arCategories as $arCategory)
			{
				$arItemsFilter=array(
					'category_id'=>$arCategory['id'],
				);
				if($arRecords=$this->obPosts->GetList(array('id'=>'asc'),$arItemsFilter,false,false,'id'))
				{
					$arIDs=array();
					foreach($arRecords as $arItem)
					{
						$arIDs[]=$arItem['id'];
					}
					if(count($arIDs)>0)
					{
						$this->obPosts->DeleteItems(array('->id'=>$arIDs));
						$this->obAnswers->DeleteItems(array('->post_id'=>$arIDs));
					}
				}
			}
			return $this->obCategories->DeleteItems(array('->id'=>$id));
		}
		return false;
	}
}
?>
