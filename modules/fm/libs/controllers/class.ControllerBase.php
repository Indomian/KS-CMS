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
	private $obHelper;
	
	function __construct(){
		$this->obControllerFile=new ControllerFile();
		$this->obControllerDir=new ControllerDir($this->obControllerFile);
		$this->obHelper=Helper::Instance();
		$this->sAction=(!empty($_REQUEST['a'])) ? strip_tags($_REQUEST['a']) : '';
		$this->sType=(!empty($_REQUEST['t'])) ? strip_tags($_REQUEST['t']) : '';
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
				$obView=$this->SelectController()->Copy();
				break;
			case 'cut':
				$obView=$this->SelectController()->Cut();
				break;
			case 'paste':
				$obView=$this->obControllerDir->Paste();
				break;
			case 'download':
				$obView=$this->obControllerFile->Download();
				break;
			case 'edit':
				$obView=$this->SelectController()->Edit();
				break;
			case 'open':
			default:
				$obView=$this->SelectController()->Open();
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
	}
}
