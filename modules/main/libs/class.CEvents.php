<?php
/**
 * @filesource main/libs/class.CEvents.php
 *
 * В файле содержится класс обеспечивающий управление очередью сообщений
 *
 * @since 10.11.2008
 * @version 2.6
 * @author BlaDe39 <blade39@kolosstudio.ru>
 *
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CMessage.php';

/**
 * Класс обеспечивает управление очередью сообщений, её выполнение (рассылку) и переактивацию
 * @author blade39 <blade39@kolosstudio.ru>
 */
class CEvents extends CObject
{
	private $arList;
	private $step;

	function __construct($sTable='main_events')
	{
		parent::__construct($sTable);
		$this->step=5;
		$this->arList=array();
	}

	/**
	 * Метод выполняет загрузку сообщений предназначенных к отправке
	 */
	function Init()
	{
		if($arList=$this->GetList(false,array('status'=>'new','auto'=>1),$this->step))
			$this->arList=$arList;
	}

	/**
	 * Метод выполняет рассылку сообщений
	 */
	function Run()
	{
		try
		{
			foreach($this->arList as $item)
			{
				$className='C'.$item['type'].'message';
				if(class_exists($className))
					$obj=new $className;
				else
					$obj=new CMessage();
				$obj->Run($item);
				unset($obj);
			}
		}
		catch (CError $e)
		{
			return false;
		}
		return true;
	}

	/**
	 * Метод выполняется после отправки сообщений
	 */
	function Done(){}

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
		if($arEvent=$this->GetEvents(array('id'=>$iId)))
			if($arEvent['status']=='error')
				return ($this->Update($iId, array('status'=>'new'))!=-1);
			else
				throw new CError('MAIN_EVENTS_MESSAGE_ACTIVATE_ERROR');
		else
			throw new CError('MAIN_EVENTS_MESSAGE_NOT_FOUND');
	}
}
