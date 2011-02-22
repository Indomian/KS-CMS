<?php
/**
 * @file class.CWavePosts.php
 * Класс для работы с сообщениями модуля wave
 * Файл проекта kolos-cms.
 *
 * @since 27.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}


define('KS_ACCESS_WAVE_FULL',0);
define('KS_ACCESS_WAVE_MODERATE',4);
define('KS_ACCESS_WAVE_ANSWER',6);
define('KS_ACCESS_WAVE_ADD',7);
define('KS_ACCESS_WAVE_ADD_GUEST',8);
define('KS_ACCESS_WAVE_VIEW',9);
define('KS_ACCESS_WAVE_DENIED',10);

define('SMALL_STEP',1);
define('BIG_STEP',10);

class CWavePosts extends CFieldsObject
{
	function __construct($sTable='wave_posts')
	{
		parent::__construct($sTable);
		$this->sFieldsModule='wave';
		//Подключаем работу с пользовательскими полями.
		if (class_exists(CFields))
		{
			$this->bFields=true;
			$obFields=new CFields();
			$this->arUserFields=$obFields->GetFields($this->sFieldsModule,$this->sTable);
			foreach($this->arUserFields as $item)
			{
				$this->arFields[]='ext_'.$item['title'];
			}
		}
		//Устанавливаем папку для загрузки
		$this->sUploadPath='wave/';
	}

	/**
	 * Метод выполняет пересчет дерева
	 */
	function RecountTree($sHash,$parent_id=0,$iCurrentMargin=1,$depth=0)
	{
		if($parent_id==0 || $this->GetRecord(array('id'=>$parent_id,'hash'=>$sHash)))
		{
			$iBaseMargin=$iCurrentMargin;
			if($arSubItems=$this->GetList(array('date_add'=>'asc'),array('parent_id'=>$parent_id,'hash'=>$sHash),false,array('id')))
			{
				foreach($arSubItems as $arItem)
				{
					//echo str_repeat('-',$depth).$arItem['id'].'<br/>';
					$iCurrentMargin=$this->RecountTree($sHash,$arItem['id'],$iCurrentMargin+SMALL_STEP,$depth+1);
				}
				$iCurrentMargin+=BIG_STEP;
			}
			if($parent_id>0)
			{
				$this->Update($parent_id,array('left_margin'=>$iBaseMargin,'right_margin'=>$iCurrentMargin,'depth'=>$depth));
			}
		}
		return $iCurrentMargin+SMALL_STEP;
	}

	function Save($prefix='',$data=false)
	{
		global $KS_EVENTS_HANDLER;
		$id=parent::Save($prefix,$data);
		if($arPost=$this->GetRecord(array('id'=>$id)))
		{
			$this->RecountTree($arPost['hash']);
			if(!$KS_EVENTS_HANDLER->Execute('wave','onAfterAdd',$arPost))
				throw new CError('MAIN_HANDLER_ERROR','wave:onAfterAdd');
		}
		return $id;
	}
}
?>
