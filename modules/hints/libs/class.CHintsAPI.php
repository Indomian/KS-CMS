<?php
/**
 * @file modules/hints/libs/class.CHintsAPI.php
 * Класс API для работы с различными данными модуля Hints
 * Файл проекта kolos-cms.
 *
 * Создан 25.05.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR."/main/libs/class.CBaseAPI.php";

class CHintsAPI extends CBaseAPI
{
	protected static $obSelf;
	private $arHintsRequested;
	private $bHandlerSet;

	/**
	 * Объекты различных видов данных
	 */
	private $obHints;

	/**
	 * Конструктор класса, ничего не делает
	 */
	public function __construct()
	{
	}

	public static function get_instance()
	{
		if(!is_object(self::$obSelf))
		{
			self::$obSelf=new CHintsAPI();
			self::$obSelf->Init();
		}
		return self::$obSelf;
	}

	/**
	 * Метод производит инициализацию внутренних переменных
	 */
	private function Init()
	{
		$this->obHints=false;
		$this->arHintsRequested=array();
	}

	function Hints()
	{
		if(!$this->obHints)
		{
			$this->obHints=new CObject('hints_data');
		}
		return $this->obHints;
	}

	function Get($sCode)
	{
		global $KS_EVENTS_HANDLER;
		if(!in_array($sCode,$this->arHintsRequested))
		{
			$this->arHintsRequested[]=$sCode;
		}
		if(!$this->bHandlerSet)
		{
			$KS_EVENTS_HANDLER->AddEvent('main','onGetHeader',array('hFunc'=>array($this,"GetHints")));
			$this->bHandlerSet=true;
		}
	}

	/**
	 * Метод реально добавляет вывод подсказок на сайте
	 */
	function GetHints()
	{
		if(count($this->arHintsRequested)>0)
		{
			$sResult='';
			$arHintsFound=array();
			if($arList=$this->Hints()->GetList(false,array('->text_ident'=>$this->arHintsRequested)))
			{
				$sResult='<script type="text/javascript">var arHints={';
				$arItems=array();
				foreach($arList as $arItem)
				{
					$arItems[]="'".$arItem['text_ident']."':".json_encode($arItem['content'])."\n";
					$arHintsFound[]=$arItem['text_ident'];
				}
				$sResult.=join(",\n",$arItems).'};</script>';
			}
			if(count($this->arHintsRequested)>0 && count($arHintsFound)!=count($this->arHintsRequested))
			{
				$arNotFound=array_diff($this->arHintsRequested,$arHintsFound);
				if(count($arNotFound)>0)
					foreach($arNotFound as $sTextIdent)
					{
						$arItem=array(
							'id'=>-1,
							'text_ident'=>$sTextIdent,
							'content'=>'',
						);
						$this->Hints()->Save('',$arItem);
					}
			}
			return $sResult;
		}
		return '<!-- NO HINTS -->';
	}
}
