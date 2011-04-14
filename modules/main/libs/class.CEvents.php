<?php
/*
 * CMS-local
 *
 * Created on 10.11.2008
 *
 * Developed by blade39
 *
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CMessage.php';

class CEvents extends CObject
{
	var $arList;
	var $step;

	function __construct($sTable='main_events')
	{
		parent::__construct($sTable);
		$this->step=5;
		$this->arList=array();
		$this->arFields=array('id','title','address','status','type');
	}

	function Init()
	{
		global $ks_db;
		$res=$ks_db->query("SELECT * FROM ".PREFIX.$this->sTable." WHERE status='new' LIMIT ".$this->step);
		while($arRow=$ks_db->get_row($res))
		{
			$this->arList[]=$arRow;
		}
	}

	function Run()
	{
		try
		{
			foreach($this->arList as $item)
			{
				$className='C'.$item['type'].'message';
				if(class_exists($className))
				{
					$obj=new $className;
				}
				else
				{
					$obj=new CMessage();
				}
				$obj->Run($item);
			}
		}
		catch (CError $e)
		{
			//echo $e;
			return false;
		}
		return true;
	}

	function Done()
	{

	}

	/**
	 * Функция возвращает запрошеное сообщение
	 * @param $where - ассоциативный массив
	 * @return array - Сообщение
	 */
	function GetEvents($where){
		return parent::GetRecord($where);
	}

	/**
	 * Функция ставит сообщения со статусом error
	 * в очередь на повторную отправку
	 * @param unknown_type $iId
	 */
	function Activate($iId){
		$ob=new CEvents();
		$arEvent=$this->GetEvents(array('id'=>$iId));
		if(!empty($arEvent)&&$arEvent['status']=='error'){
			return ($ob->Update($iId, array('status'=>'new'))!=-1);
		}else{
			throw new CError('','','Активировать можно только сообщения со статусом error');
		}
	}
}
?>
