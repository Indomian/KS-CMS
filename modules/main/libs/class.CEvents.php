<?php
/**
 * В файле содержится класс обработки событий системы
 * @filesource main/libs/class.CEvents.php
 * @since 10.11.2008
 * @author Blade39 <blade39@kolosstudio.ru>
 * @version 2.7
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CEvents extends CObject
{
	protected $arList;
	protected $step;

	function __construct($sTable='main_events')
	{
		parent::__construct($sTable);
		$this->step=5;
		$this->arList=array();
		$this->arFields=array('id','title','address','status','type');
	}

	/**
	 * Метод выполняет выборку списка событий подлежащих обработке
	 */
	function Init()
	{
		if($arList=$this->GetList(false,array('status'=>'new'),$this->step))
			$this->arList=$arList;
	}

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
			}
		}
		catch (CError $e)
		{
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
	 * @param int $iId
	 */
	function Activate($iId)
	{
		$arEvent=$this->GetEvents(array('id'=>$iId));
		if(!empty($arEvent)&&$arEvent['status']=='error')
			return ($this->Update($iId, array('status'=>'new'))!=-1);
		else
			throw new CError('SYSTEM_EVENTS_ONLY_ERROR_CAN_BE_ACTIVE');

	}
}
