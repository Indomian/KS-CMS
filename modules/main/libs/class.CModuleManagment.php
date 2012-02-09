<?php

include_once MODULES_DIR.'/main/libs/class.CObject.php';
include_once MODULES_DIR.'/main/libs/class.CGlobalTemplates.php';
include_once MODULES_DIR.'/main/libs/class.CConfigParser.php';
include_once MODULES_DIR.'/main/libs/class.CUrlParser.php';

define('NOTIFY_WARNING',1);
define('NOTIFY_MESSAGE',2);
define('NOTIFIES_LIFE',3);

/**
 * Базовый класс обеспечивающий работу с модулями
 */
abstract class CModuleManagment extends CObject
{
	protected $arModules;
	protected $arHeads;		/*!<массив строк для вывода в заголовок*/
	protected $arHeadScripts; /** Массив скриптов которые необходимо вывести в заголовке*/
	protected $arIsModules; /**<список существующих модулей*/
	protected $arNotifies; /*!<массив в котором хранятся уведомления системы*/
	protected $obUser;
	protected $obSmarty;
	protected $obLanguage;
	protected $obLanguageError;
	protected $arVersion;

	function __construct()
	{
		global $ks_config,$KS_FS;
		//Устанавливаем уровень обработки ошибок в системе
		if(KS_RELEASE==1)
			error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
		else
			error_reporting(E_ALL | E_STRICT);
		//Если самоустановка, то надо скопировать структуру БД
		if($ks_config['go_install']==1)
			if(!$KS_FS->CopyFile(MODULES_DIR.'/main/install/db_structure.php',CONFIG_DIR.'/db_structure.php',''))
				throw new CError('SYSTEM_COPY_DB_STRUCTURE_FAIL');
		parent::__construct('main_modules');
		$this->obSession=CSessionManager::get_instance();
		if(!array_key_exists('notifies',$_SESSION) || !is_array($_SESSION['notifies'])) $_SESSION['notifies']=array();
		$this->arNotifies=$_SESSION['notifies'];
		$this->arIsModules=array();
		$this->arModules=array(
			'main'=>array(
				'config'=>$ks_config,
				'directory'=>'main',
				'mess'=>'',
				'ALEVELS'=>array('0'=>'full_access','10'=>'access_denied')),
		);
		//Подгрузка версии системы
		$arVersion=array();
		include MODULES_DIR.'/main/install/version.php';
		$this->arVersion=$arVersion;
		//Надо провести самодиагностику и самоустановку
		if($ks_config['go_install']==1)
			$this->SelfInstall();
		$this->arHeads=array();
		$this->arHeadScripts=array();
	}

	/**
	 * Метод возвращает версию системы
	 * @return string строка с версией системы
	 */
	function Version()
	{
		return isset($this->arVersion['ID'])?$this->arVersion['ID']:'Uknown';
	}

	/**
	 * Метод возвращает полную информацию по версии системы
	 * @return array массив с версией системы
	 */
	function GetVersionData()
	{
		return $this->arVersion;
	}

	/**
	 * Метод "устанавливает" объект пользователя в систему, т.е. связывает
	 * созданный объект пользователя и объект системы модуля
	 */
	function SetUser(CUser $obUser)
	{
		$this->obUser=$obUser;
	}

	/**
	 * Метод привязывает к объекту класса объект шаблонизатора smarty
	 */
	function SetSmarty($obSmarty)
	{
		$this->obSmarty=$obSmarty;
	}

	/**
	 * Метод привязывает объект языковых констант к объекту управления модулями.
	 */
	function SetLanguage($obLanguage)
	{
		$this->obLanguage=$obLanguage;
		if(file_exists(MODULES_DIR.'/main/.access.php'))
		{
			$arLevels=array();
			include MODULES_DIR.'/main/.access.php';
			$this->arModules['main']['ALEVELS']=$arLevels;
		}
	}

	/**
	 * Метод возвращает значение текстовой константы
	 */
	function GetText($code)
	{
		if(!is_object($this->obLanguage)) return $code;
		if(!($this->obLanguage instanceof CLanguage)) return $code;
		return $this->obLanguage->Text($code);
	}

	/**
	 * Метод устанавливает объект языковых констант ошибок
	 */
	function SetLanguageError($obLanguage)
	{
		$this->obLanguageError=$obLanguage;
		$this->obLanguageError->LoadSection(null);
	}

	/**
	 * Метод возвращает значение текстовой константы ошибки
	 */
	function GetErrorText($code)
	{
		if(!is_object($this->obLanguageError)) return $code;
		if(!($this->obLanguageError instanceof CLanguage)) return $code;
		return $this->obLanguageError->Text($code);
	}

	/**
	 * Деструктор класса
	 * Выполняет обработку списка уведомлений, если
     * уведомление живет больше 3-х хитов - удаляем его.
	 */
	function __destruct()
	{
		$_SESSION['notifies']=array();
		foreach($this->arNotifies as $arItem)
		{
			if(array_key_exists('life',$arItem))
				$arItem['life']++;
			else
				$arItem['life']=1;
			if($arItem['life']<NOTIFIES_LIFE)
				$_SESSION['notifies'][]=$arItem;
		}
	}

	/**
	 * Метод добавляет уведомление в массив уведомлений
	 */
	function AddNotify($msg,$text='',$type=NOTIFY_WARNING)
	{
		$this->arNotifies[]=array(
			'msg'=>$msg,
			'text'=>$text,
			'type'=>$type
		);
		return 1;
	}

	/**
	 * Метод выводит список уведомлений
	 */
	function GetNotifies()
	{
		return $this->arNotifies;
	}

	/**
	 * Метод возвращает уведомления и отчишает их список
	 */
	function ShowNotifies()
	{
		$arResult=$this->arNotifies;
		$this->arNotifies=array();
		return $arResult;
	}

	/**
	 * Метод получает список еще не установленных модулей путем сканирования папки modules
	 * и проверки является ли указанный файл модулем.
	 */
	function GetUninstalledList()
	{
		$sPath=MODULES_DIR;
		$arNotModules=Array('.','..');
		$arResult=false;
		if (is_dir($sPath))
			if ($hDir = @opendir($sPath))
        		while (($file = readdir($hDir)) !== false)
	        		if (!in_array($file,$arNotModules)&&!$this->IsModule($file))
	        			if(file_exists($sPath.'/'.$file.'/install/install.php'))
	        			{
	        				$arDescription=array();
	        				if(file_exists($sPath.'/'.$file.'/install/description.php'))
	        					include $sPath.'/'.$file.'/install/description.php';
	        				$arResult[]=array(
	        					'name'=>$file,
	        					'title'=>$arDescription['title'],
	        					'description'=>$arDescription['description'],
	        				);
	        			}
		return $arResult;
	}

	/**
	 * Метод проверяет можно ли устанавливать данный модуль
	 */
	function IsInstallable($module)
	{
		$sPath=MODULES_DIR;
		if (!$this->IsModule($module))
	        return file_exists($sPath.'/'.$module.'/install/install.php');
		throw new CModuleError("MAIN_MODULE_ALREADY_INSTALLED",0);
	}

	/**
	 * Метод выполняет перерасчет стркутуры базы данных хранящейся в конфигурации сайта на основании структур
	 * таблиц различных модулей, при этом происходит сохранение сгенерированной структуры.
	 */
	function RecountDBStructure()
	{
		if($arModules=$this->GetList(array('id'=>'asc'),array('active'=>1)))
		{
			$arResultStructure=array();
			foreach($arModules as $arModule)
				if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/install/db_structure.php'))
				{
					include MODULES_DIR.'/'.$arModule['directory'].'/install/db_structure.php';
					$arResultStructure=array_merge($arResultStructure,$arStructure);
				}
			if(file_exists(MODULES_DIR.'/main/install/db_structure.php'))
			{
				include MODULES_DIR.'/main/install/db_structure.php';
				$arResultStructure=array_merge($arResultStructure,$arStructure);
			}
			SaveToFile(CONFIG_DIR.'/db_structure.php','$arStructure',$arResultStructure);
		}
	}

	/**
	 * Метод выполняет компиляцию файлов текстовых констант из файлов текстов различных модулей
	 * выполняется после установки или удаления модулей, а также после обновления системы
	 */
	function RecountTextStructure()
	{
		global $smarty,$KS_FS;
		$arModules=array(array(
			'directory'=>'main',
		));
		if($arRealModules=$this->GetList(array('id'=>'asc'),array('active'=>1)))
			$arModules=array_merge($arModules,$arRealModules);
		if(is_array($arModules) && count($arModules)>0)
		{
			$arInterfaceFiles=array();
			$arErrorFiles=array();
			foreach($arModules as $arModule)
			{
				$sInterfaceDevider="\n\n".'#'.$arModule['directory']."\n";
				$sErrorDevider="\n\n".'#Errors from module - '.$arModule['directory']."\n";
				$sModuleLangPath=MODULES_DIR.'/'.$arModule['directory'].'/install/lang/';
				if(file_exists($sModuleLangPath))
				{
					$arLanguages=$KS_FS->GetDirItems($sModuleLangPath);
					foreach($arLanguages as $sLangPath)
					{
						if(!array_key_exists($sLangPath,$arInterfaceFiles)) $arInterfaceFiles[$sLangPath]='';
						if(!array_key_exists($sLangPath,$arErrorFiles)) $arErrorFiles[$sLangPath]='';
						if(is_dir($sModuleLangPath.'/'.$sLangPath))
						{
							if(file_exists($sModuleLangPath.'/'.$sLangPath.'/admin.conf'))
								$arInterfaceFiles[$sLangPath].=$sInterfaceDevider.file_get_contents($sModuleLangPath.'/'.$sLangPath.'/admin.conf');
							if(file_exists($sModuleLangPath.'/'.$sLangPath.'/error.conf'))
								$arErrorFiles[$sLangPath].=$sErrorDevider.file_get_contents($sModuleLangPath.'/'.$sLangPath.'/error.conf');
						}
					}
				}
			}
			if(is_object($smarty) && $smarty->config_dir!='')
			{
				foreach($arInterfaceFiles as $sLang=>$sContent)
				{
					if(!file_exists($smarty->config_dir.'/'.$sLang.'/'))
						$KS_FS->makedir($smarty->config_dir.'/'.$sLang.'/');
					file_put_contents($smarty->config_dir.'/'.$sLang.'/admin.conf',$sContent);
				}
				foreach($arErrorFiles as $sLang=>$sContent)
				{
					if(!file_exists($smarty->config_dir.'/'.$sLang.'/'))
						$KS_FS->makedir($smarty->config_dir.'/'.$sLang.'/');
					file_put_contents($smarty->config_dir.'/'.$sLang.'/error.conf',$sContent);
				}
				$smarty->clear_all_cache();
				$smarty->clear_compiled_tpl();
				return true;
			}
			else
				throw new CError('SYSTEM_STRANGE_ERROR');
		}
		throw new CError('SYSTEM_STRANGE_ERROR');
	}

	/**
	 * Метод выполняет удаление всех файлов модуля из соответсвующих каталогов
	 */
	private function DeleteAllModuleFiles($module_name)
	{
		global $KS_FS;
		if(!file_exists(MODULES_DIR.'/'.$module_name)) return false;
		//Удаляем файлы административных шаблонов
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/admin/'))
			foreach($arFiles as $sFile)
				$KS_FS->Remove(SYS_TEMPLATES_DIR.'/admin/'.$sFile);
		//Удаляем файлы яваскрипта
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/js/'))
			if(file_exists(ROOT_DIR.JS_DIR.'/'.$module_name.'/'))
				$KS_FS->Remove(ROOT_DIR.JS_DIR.'/'.$module_name.'/');
		if(file_exists(TEMPLATES_DIR.'/.default/'.$module_name.'/'))
			$KS_FS->Remove(TEMPLATES_DIR.'/.default/'.$module_name.'/');
	}

	/**
	 * Метод выполняет установку всех файлов указанного модуля в соответствующие каталоги,
	 * в случае необходимости модуль создаёт новые каталоги
	 */
	private function InstallAllModuleFiles($module_name)
	{
		global $KS_FS;
		if(!file_exists(MODULES_DIR.'/'.$module_name)) return false;
		//Устанавливаем файлы административного интерфейса
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/.default/'))
		{
			if(!file_exists(TEMPLATES_DIR.'/.default/'.$module_name.'/'))
				$KS_FS->makedir(TEMPLATES_DIR.'/.default/'.$module_name.'/');
			foreach($arFiles as $sFile)
				$KS_FS->CopyFile(MODULES_DIR.'/'.$module_name.'/install/templates/.default/'.$sFile,TEMPLATES_DIR.'/.default/'.$module_name.'/'.$sFile,'');
		}
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/admin/'))
			foreach($arFiles as $sFile)
				$KS_FS->CopyFile(MODULES_DIR.'/'.$module_name.'/install/templates/admin/'.$sFile,SYS_TEMPLATES_DIR.'/admin/'.$sFile,'');
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/events/'))
		{
			if(!file_exists(SYS_TEMPLATES_DIR.'/admin/eventTemplates/'))
				$KS_FS->makedir(SYS_TEMPLATES_DIR.'/admin/eventTemplates/');
			foreach($arFiles as $sFile)
				$KS_FS->CopyFile(MODULES_DIR.'/'.$module_name.'/install/templates/events/'.$sFile,SYS_TEMPLATES_DIR.'/admin/eventTemplates/'.$sFile,'');
		}
		$this->InstallJSFiles($module_name);
	}

	/**
	 * Метод устанавливает все яваскрипт файлы модуля, скрипты модуля main устанавливаются в корень каталога JS!
	 */
	function InstallJSFiles($module_name)
	{
		global $KS_FS;
		if($this->IsActive($module_name))
		{
			if(!file_exists(MODULES_DIR.'/'.$module_name)) return false;
			//Устанавливаем скрипты модуля
			if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/js/'))
			{
				if(!file_exists(ROOT_DIR.JS_DIR))
					$KS_FS->makedir(ROOT_DIR.JS_DIR);
				if($module_name=='main')
					foreach($arFiles as $sFile)
						$KS_FS->CopyFile(MODULES_DIR.'/'.$module_name.'/install/js/'.$sFile,ROOT_DIR.JS_DIR.'/'.$sFile,'');
				else
				{
					if(!file_exists(ROOT_DIR.JS_DIR.'/'.$module_name.'/'))
						$KS_FS->makedir(ROOT_DIR.JS_DIR.'/'.$module_name.'/');
					foreach($arFiles as $sFile)
						$KS_FS->CopyFile(MODULES_DIR.'/'.$module_name.'/install/js/'.$sFile,ROOT_DIR.JS_DIR.'/'.$module_name.'/'.$sFile,'');
				}
			}
		}
	}

	/**
	 * Метод выполняет подключение установочного файла для установки
	 * нового модуля
	 */
	function Install($module)
	{
		global $smarty,$KS_FS,$KS_EVENTS_HANDLER;
		if($this->IsModule($module)) throw new CModuleError("MAIN_MODULE_ALREADY_INSTALLED",0);
		if(file_exists(MODULES_DIR.'/'.$module.'/install/install.php'))
		{
			$arFields=array();
			$showButtons=1;
			include MODULES_DIR.'/'.$module.'/install/install.php';
			$this->RecountDBStructure();
			$this->RecountTextStructure();
			$smarty->assign('fields',$arFields);
			return $showButtons;
		}
		else
			throw new CModuleError('MAIN_MODULE_CANT_INSTALL');
	}

	/**
	 * Метод выполняет установку файлов ресурсов модуля
	 * @param $module - имя модуля
	 */
	public function InstallResources($module)
	{
		global $KS_FS;
		if(!file_exists(MODULES_DIR.'/'.$module)) return false;
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module.'/install/templates/resources/'))
		{
			$sResourcesDir=TEMPLATES_DIR.'/admin/';
			if(!file_exists($sResourcesDir))
				$KS_FS->makedir($sResourcesDir);
			foreach($arFiles as $sFile)
				$KS_FS->CopyFile(MODULES_DIR.'/'.$module.'/install/templates/resources/'.$sFile,$sResourcesDir.$sFile,'');
		}
	}

	/**
	 * Метод выполняет удаление всех файлов указанного модуля из соответствующих каталогов,
	 */
	private function UnInstallAllModuleFiles($module_name)
	{
		global $KS_FS;
		if(!file_exists(MODULES_DIR.'/'.$module_name)) return false;
		//Если есть шаблоны пользовательской части
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/.default/'))
		{
			foreach($arFiles as $sFile)
				$KS_FS->Remove(TEMPLATES_DIR.'/.default/'.$module_name.'/'.$sFile);
			if(!$KS_FS->GetDirItems(TEMPLATES_DIR.'/.default/'.$module_name.'/'))
				$KS_FS->Remove(TEMPLATES_DIR.'/.default/'.$module_name.'/');
		}
		//Если есть шаблоны административной части
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/admin/'))
			foreach($arFiles as $sFile)
				$KS_FS->Remove(SYS_TEMPLATES_DIR.'/admin/'.$sFile);
		//Шаблоны событий
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/events/'))
			foreach($arFiles as $sFile)
				$KS_FS->Remove(SYS_TEMPLATES_DIR.'/admin/eventTemplates/'.$sFile);
		//Cкрипты модуля
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/js/'))
			foreach($arFiles as $sFile)
				$KS_FS->Remove(ROOT_DIR.JS_DIR.'/'.$module_name.'/'.$sFile);
	}

	/**
	 * Метод выполняет удаление указанного модуля
	 */
	function UnInstall($module)
	{
		global $smarty,$KS_FS,$KS_EVENTS_HANDLER;
		if(!$this->IsModule($module)) throw new CModuleError("MAIN_MODULE_NOT_INSTALLED",0);
		if(file_exists(MODULES_DIR.'/'.$module.'/install/uninstall.php'))
		{
			$arFields=array();
			$showButtons=1;
			include MODULES_DIR.'/'.$module.'/install/uninstall.php';
			$this->RecountDBStructure();
			$this->RecountTextStructure();
			$smarty->assign('fields',$arFields);
			return $showButtons;
		}
		else
			throw new CModuleError('MAIN_MODULE_CANT_DELETE');
	}

	/**
	 * Метод выполняет подключение указанного модуля
	 * @param string $module_name - название модуля
	 * @return array|false - если модуль подключен - массив со списком подключенных модулей, иначе false;
	 */
	function InitModule($module_name)
	{
		if(!array_key_exists($module_name,$this->arModules))
		{
			if($arModule=$this->GetRecord(array('active'=>1,'directory'=>$module_name)))
			{
				$this->IncludeModule($arModule);
				return $this->arModules;
			}
			else
				return false;
		}
		return $this->arModules;
	}

	/**
	 * Метод добавляет указанные строки в заголовок шаблона
	 * @param string $string - строка которую необходимо добавить
	 */
	function AddHeadString($string)
	{
		if(!in_array($string,$this->arHeads))
			$this->arHeads[]=$string;
		return true;
	}

	/**
	 * Метод добавляет указанный скрипт в список скриптов при рендеринге страницы
	 * @param $script имя скрипта относительно папки яваскриптов
	 * @param $position позция скрипта в шапке
	 */
	function UseJavaScript($script,$position=10)
	{
		if(!file_exists(ROOT_DIR.JS_DIR.$script)) return false;
		if(!array_key_exists($position,$this->arHeadScripts))
			$this->arHeadScripts[$position]=array();
		if(!in_array($script,$this->arHeadScripts[$position]))
			$this->arHeadScripts[$position][]=$script;
		return true;
	}

	/**
	 * Метод проверяет наличие загруженного яваскрипта
	 */
	function HasJavaScript($script,$position=10)
	{
		if(isset($this->arHeadScripts[$position]) && is_array($this->arHeadScripts[$position]))
			return in_array($script,$this->arHeadScripts[$position]);
		return false;
	}

	/**
	 * Метод возвращает строки подключения яваскрипта
	 */
	function GetJavaScript()
	{
		$sResult='';
		ksort($this->arHeadScripts);
		foreach($this->arHeadScripts as $position=>$arScripts)
			foreach($arScripts as $sScript)
				$sResult.='<script type="text/javascript" src="'.JS_DIR.$sScript.'"></script>'."\n";
		return $sResult;
	}

	/**
	 * Метод возвращает все строки которые должны быть добавлены в хэдер страницы
	 */
	function GetHeader()
	{
		global $KS_EVENTS_HANDLER;
		$sEvents='';
		if($KS_EVENTS_HANDLER->HasHandler('main','onGetHeader'))
		{
			$KS_EVENTS_HANDLER->PushMode();
			$KS_EVENTS_HANDLER->SetMode('particular');
			if($arResult=$KS_EVENTS_HANDLER->Execute('main','onGetHeader'))
				foreach($arResult as $arItem)
					if(is_string($arItem['executed']))
						$sEvents.="\n".$arItem['executed'];
		}
		return $this->GetJavaScript()."\n".join("\n",$this->arHeads)."\n".$sEvents."\n";
	}

	/** Проверяет является ли указанный модуль, модулем.
	 * Возвращает описание модуля
	 * @param $module - имя модуля (текстовый идентификатор).
	*/
	function IsModule($module)
	{
		if(!preg_match('#[\w\d]+#',$module)) return false;
		try
		{
			if(array_key_exists($module,$this->arIsModules))
				return $this->arIsModules[$module];
			elseif(array_key_exists($module,$this->arModules))
			{
				if(file_exists(MODULES_DIR.'/'.$module))
				{
					$this->arIsModules[$module]=true;
					return $this->arModules[$module];
				}
			}
			elseif($module=='main')
			{
				$this->arIsModules[$module]=true;
				return true;
			}
			elseif($res=$this->GetRecord(array('directory'=>$module)))
			{
				if(file_exists(MODULES_DIR.'/'.$module))
				{
					$this->arIsModules[$module]=true;
					return $res;
				}
			}
			else
			{
				$this->arIsModules[$module]=false;
				return false;
			}
		}
		catch (CError $e)
		{
			throw $e;
		}
		return false;
	}

	/**
	 * Метод проверяет является ли указанный модуль активным
	 */
	function IsActive($module)
	{
		if($module=='main') return true;
		$res=$this->IsModule($module);
		if($res||($res['active']==1))
		{
			//Такой модуль есть смотрим активность
			if(!array_key_exists($module,$this->arModules))
				//Если модуль не подключен - подключаем
				if(is_array($res)) $this->InitModule($res); else $this->InitModule($module);
			return $this->arModules[$module]['active']==1;
		}
		return false;
	}

	/**
	 * Метод проверяет является ли указанный модуль, модулем по умолчанию
	 */
	function IsDefault($module)
	{
		if($this->IsActive($module))
			return $this->arModules[$module]['URL_ident']=='default';
		return false;
	}

	/**
	 * Метод возвращает путь к модулю в пользовательской части сайта,
	 * также проверяет данный модуль на активность перед построением пути,
	 * если модуль неактивен возвращает false
	 * @param $module - имя модуля
	 * @return false|string - путь к модулю или false если модуль отключен
	 */
	function GetSitePath($module)
	{
		if($this->IsActive($module))
			if($this->arModules[$module]['URL_ident']!='default')
				return '/'.$this->arModules[$module]['URL_ident'].'/';
			return '/';
		return false;
	}

	/**
	 * Метод возвращает текстовое название указанного модуля
	 */
	function GetTitle($module)
	{
		if($this->IsActive($module))
			return $this->arModules[$module]['name'];
		return false;
	}

	/**
	 * Метод возвращает значение параметра настройки определенного модуля.
	 * Если модуль не был подключен, подключает его и возвращает значение соответствующего параметра.
	 *
	 * @param $module Идентификатор модуля
	 * @param $var Имя требуемого параметра
	 * @param $default значение которое необходимо использовать по умолчанию если не задано значение в конфиге
	 * @return mixed
	 */
	function GetConfigVar($module, $var, $default=false)
	{
		if (!array_key_exists($module, $this->arModules))
			$this->InitModule($module);
		if(is_array($this->arModules[$module]))
			if(array_key_exists('config',$this->arModules[$module]))
				if (array_key_exists($var,$this->arModules[$module]['config']))
					return $this->arModules[$module]['config'][$var];
			elseif (array_key_exists($var,$this->arModules[$module]))
				return $this->arModules[$module][$var];
		return $default;
	}

	/**
	 * Метод возвращает массив настроек для определенного модуля.
	 *
	 * @param string $module Имя модуля, настройки которого требуется получить.
	 * @return array
	 */
	function GetConfigArray($module)
	{
		if (!array_key_exists($module, $this->arModules))
			$this->InitModule($module);

		if (isset($this->arModules[$module]['config']))
			return $this->arModules[$module]['config'];

		return false;
	}

	/**
	 * Метод возвращает параметры модуля для работы с базой данных.
	 *
	 * @since 14.09.2009
	 *
	 * @param $module Идентификатор модуля
	 * @param $var Имя требуемого параметра
	 * @return mixed
	 */
	function GetDBVar($module, $var)
	{
		if (!array_key_exists($module, $this->arModules))
			$this->InitModule($module);

		if (isset($this->arModules[$module]['db_config'][$var]))
			return $this->arModules[$module]['db_config'][$var];

		return false;
	}

	/**
	 * Метод возвращает массив параметров модуля для работы с базой данных.
	 *
	 * @since 14.09.2009
	 *
	 * @param string $module Имя модуля, настройки которого требуется получить.
	 * @return array
	 */
	function GetDBConfigArray($module)
	{
		if (!array_key_exists($module, $this->arModules))
			$this->InitModule($module);

		if (isset($this->arModules[$module]['db_config']))
			return $this->arModules[$module]['db_config'];

		return false;
	}

	/**
	 * Функция возвращает массив названий уровней доступа для модуля.
	 * @param $module - имя модуля настройки которого требуется получить.
	 */
	function GetAccessArray($module)
	{
		if(!array_key_exists($module,$this->arModules))
			$this->InitModule($module);
		return $this->arModules[$module]['ALEVELS'];
	}

	/**
	 * Метод возвращает имя глобального шаблона, который необходимо использовать, в зависимости от url
	 * Если по данному url шаблон не удалось найти, то отдаётся имя дефолтового шаблона (.default)
	 * Изменен принцип работы, добавлены сложные варианты выбора шаблонов.
	 *
	 * @version 2.5.2
	 * @since 23.05.2009
	 *
	 * Добавлен поиск глобального шаблона для главной страницы (в базе должен быть указан uri='/')
	 * Функция сделанна
	 *
	 * @param string $full_uri Полный путь к странице
	 * @param string $jumps_quant Максимальное количество прыжков для поиска шаблона:
	 * 					'' (пусто) - не задано, 0 - не делать прыжков
	 * @param int $internal - внутренняя переменная, не использовать
	 * @return unknown
	 *
	 */
	protected function select_global_template($full_uri, $jumps_quant='4', $internal=0)
	{
		global $ks_db,$USER;

		//Разбиваем строку ввода
		if(!preg_match("#^/?((?:[\w\d\-_]+\/)*)([\w\d_\-]+\.html)?(.*)$#", $full_uri, $uri_matches) )
			return '.default';
		if($uri_matches[2]=='')
			//Значит обращение по адресу / делаем страницу индексной
			$uri_matches[2]='index.html';
		//Устанавливаем значение по умолчанию
		$sResult='.default';
		$obTpl=new CGlobalTemplates();
		$arConditions=$obTpl->GetList(array('orderation'=>'asc'));
		if(is_array($arConditions)&&(count($arConditions)>0))
		{
			if($uri_matches[2]=='index.html')
				$path='/'.$uri_matches[1];
			else
				$path='/'.$uri_matches[1].$uri_matches[2];
			foreach($arConditions as $arCondition)
			{
				if($arCondition['type']=='=')
				{
					if($arCondition['url_path']==$path)
					{
						$sResult=$arCondition['template_path'];
						break;
					}
				}
				elseif($arCondition['type']=='')
				{
					//Работаем по старому принципу, если в пути есть хотябы часть нашего адреса
					//значит мы нашли то что искали
					$regexp='#^'.addcslashes($arCondition['url_path'],'\.-^$').'#i';
					if(preg_match($regexp,$path))
					{
						$sResult=$arCondition['template_path'];
						break;
					}
				}
				elseif($arCondition['type']=='reg')
				{
					//Значит дали нам регулярку
					$regexp='#'.$arCondition['url_path'].'#i';
					if(preg_match($regexp,$path))
					{
						$sResult=$arCondition['template_path'];
						break;
					}
				}
				elseif($arCondition['type']=='get')
				{
					//Значит дали надо проверить параметр
					$arGet=explode('=',$arCondition['url_path']);
					if(array_key_exists($arGet[0],$_GET) && $_GET[$arGet[0]]==$arGet[1])
					{
						$sResult=$arCondition['template_path'];
						break;
					}
				}
				elseif($arCondition['type']=='userGroup')
				{
					//Значит надо проверить пользователя на группу
					$arGroups=$USER->GetGroups();
					if(in_array($arCondition['function1'],$arGroups))
					{
						$sResult=$arCondition['template_path'];
						break;
					}
				}
			}
		}
		return $sResult;
	}

	/**
	 * Метод выполняет копирование шаблонов из установочного каталога в рабочий каталог.
	 * @param $module string - код модуля шаблоны которого необходимо скопировать
	 */
	public function CopyModuleTemplates($module)
	{
		global $KS_FS;
		if(!$this->IsModule($module)) throw new CError('MAIN_MODULE_NOT_FOUND',0,$module);
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module.'/install/templates/.default/'))
		{
			if(!file_exists(TEMPLATES_DIR.'/.default/'.$module.'/'))
				$KS_FS->MakeDir(TEMPLATES_DIR.'/.default/'.$module.'/');
			foreach($arFiles as $sFile)
				$KS_FS->CopyFile(MODULES_DIR.'/'.$module.'/install/templates/.default/'.$sFile,TEMPLATES_DIR.'/.default/'.$module.'/'.$sFile,'');
		}
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module.'/install/templates/admin/')){
			foreach($arFiles as $sFile)
				$KS_FS->CopyFile(MODULES_DIR.'/'.$module.'/install/templates/admin/'.$sFile,SYS_TEMPLATES_DIR.'/admin/'.$sFile,'');
		}
	}

	/**
	 * Данный метод выполняет автоматическую установку системы, в случае если она не была установлена
	 */
	private function SelfInstall()
	{
		global $KS_FS;
		try
		{
			if(file_exists(MODULES_DIR.'/main/install/install.php'))
			{
				include MODULES_DIR.'/main/install/install.php';
				if(!$this->IsModule('interfaces'))
					$this->Install('interfaces');
				if(!$this->IsModule('navigation'))
					$this->Install('navigation');
				$this->RecountDBStructure();
				$this->RecountTextStructure();
				$obConfig=new CConfigParser('main');
				$obConfig->LoadConfig();
				include_once(CONFIG_DIR.'/db_structure.php');
				$this->obDB->CheckDB($arStructure);
				$obConfig->Set('go_install',0);
				$obConfig->WriteConfig();
				CUrlParser::get_instance()->Redirect('/admin.php');
			}
		}
		catch(CError $e)
		{
			echo $e->getMessage();
			die();
		}
	}
}
