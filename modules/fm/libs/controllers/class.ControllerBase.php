<?php
/**
 * Базовый контроллер распределяет действия между контроллером, работающим с файлами и с директориями
 * @author Dmitry Konev <d.konev@kolosstudio.ru>,
 * @version 2.6
 * @since 11.01.12
 */
 
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ControllerBase {
	private static $obInstance;
	private $obControllerDir;
	private $obControllerFile;
	private $sAction;
	private $sType;
	private $sFile;
	private $obHelper;
	
	function __construct(){
		$this->obControllerFile=new ControllerFile();
		$this->obControllerDir=new ControllerDir($this->obControllerFile);
		$this->obHelper=Helper::Instance();
		$this->sAction=(!empty($_REQUEST['a'])) ? strip_tags($_REQUEST['a']) : '';
		$this->sType=(!empty($_REQUEST['t'])) ? strip_tags($_REQUEST['t']) : '';
		$this->sFile=(!empty($_REQUEST['fm_file'])) ? $_REQUEST['fm_file'] : '';
		$this->ParseCommonActions();
	}
	
	static function Instance(){
		if(!self::$obInstance)
			self::$obInstance=new self();
		return self::$obInstance;
	}
	
	public function Run(){
		
		switch($this->sAction){
			case 'upload':
				$obView=$this->SelectController()->Upload();
				break;
			case 'delete':
				$arTitles=(!empty($_POST['title'])) ? $_POST['title'] : array();
				$obView=$this->obControllerDir->Delete($arTitles);
				break;
			case 'copy':
				$arTitles=(!empty($_POST['title'])) ? $_POST['title'] : array();
				$obView=$this->SelectController()->Copy($arTitles);
				break;
			case 'cut':
				$arTitles=(!empty($_POST['title'])) ? $_POST['title'] : array();
				$obView=$this->SelectController()->Cut($arTitles);
				break;
			case 'paste':
				$obView=$this->obControllerDir->Paste();
				break;
			case 'download':
				$sFile=(!empty($_REQUEST['title'])) ? strip_tags($_REQUEST['title']) : '';
				$obView=$this->obControllerFile->Download($sFile);
				break;
			case 'edit':
				$obView=$this->SelectController()->Edit($this->sFile);
				break;
			case 'cancel':
				$obView=$this->obControllerDir->Cancel();
				break;
			case 'open':
			default:
				$obView=$this->SelectController()->Open($this->sFile);
				break;
		}
		return $obView;
	}
	
	public function SelectController(){
		if($this->sType=='' || $this->sType=='dir')
			return $this->obControllerDir;
		else
			return $this->obControllerFile;
	}

	public function ParseCommonActions(){
		if(isset($_REQUEST['comdel']))
			$this->sAction='delete';
		elseif(isset($_REQUEST['comupl']))
			$this->sAction='upload';
		elseif(isset($_REQUEST['comсopy']))
			$this->sAction='copy';
		elseif(isset($_REQUEST['comcut']))
			$this->sAction='cut';
		elseif(isset($_REQUEST['compaste']))
			$this->sAction='paste';
		elseif(isset($_REQUEST['cancel']))
			$this->sAction='cancel';
	}
}
