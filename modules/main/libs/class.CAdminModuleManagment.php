<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CModuleManagment.php';

/**
 * Класс CAdminModuleManagment потомок CModulesHookUp, выполняет операции связанный с работой модулей,
 * управляет меню системы администрирования
 * @todo Попытаться сократить количество вызовов при подключении модулей;
			Добавить функции установки модулей;
			Добавить функции проверки версии модулей;
			Реализовать нормальную многоязыковую поддержку;

 * @version 2.5.5
 * @since 17.03.2010
 * @author blade39 <blade39@kolosstudio.ru>
 * Добавлена работа с навигационными цепочками в административной части системы
 */
final class CAdminModuleManagment extends CModuleManagment
{
	/*!Массив со списком подключенных модулей.*/
	private $menu;			//!<Массив в котором храняться пункты меню.
	private $arNavChain; 		/**<Массив для хранения навигационной цепочки*/
	public $current;		//!<Текущий модуль, определяетс по строке Get запроса.
	public $currentMenu; 		/**<Указывает на название текущего модуля для меню*/
	private $localTemplate;		//!<В переменой передается имя текущего шаблона. Обычно используется после подключения определенного модуля.
	private $page;				/**<В переменной хранится текущая страница системы администрирования*/
	private $sMode;			/** Режим отрисовки административного шаблона */
	private $arRunModules;
	static private $instance;

	/*!Конструктор класса (оформление в стиле ПХП 4). Производить инициализацию внутренних переменных,
	определяет запрашиваемый модуль, если модуль найден устанавливает его текущим.*/
	function __construct($sTable='main_modules')
	{
		parent::__construct($sTable);
	}

	/**
	 * Метод заменяющий конструктор. Используется для инициализации.
	 */
	private function init()
	{
		$this->menu=Array();
		$this->current='main';
		$this->currentMenu=$this->current;
		$this->sMode='full';
		$this->arRunModules=array();
		if(array_key_exists('mode',$_GET) && $_GET['mode']=='small')
		{
			$this->sMode='small';
		}
		elseif(array_key_exists('mode',$_GET) && $_GET['mode']=='content')
		{
			$this->sMode='content';
		}
		if (array_key_exists('module',$_GET))
		{
			if ($this->IsModule($_GET['module']))
			{
				$this->current=$_GET['module'];
				$this->currentMenu=$this->current;
			}
		}
	}

	/**
	 * This implements the 'singleton' design pattern
	 *
	 * @return object CMain The one and only instance
	 */
	static function get_instance()
	{
		if (!self::$instance)
		{
			self::$instance = new CAdminModuleManagment();
			self::$instance->init();  // init AFTER object was linked with self::$instance
		}
		return self::$instance;
	}

	/**
	 * Метод устанавливает режим отрисовки системы администрирования
	 */
	function SetMode($mode)
	{
		$this->sMode=$mode;
	}

	function GetMode()
	{
		return $this->sMode;
	}

	/**
	 * Метод выполняет автоматическую загрузку указанной страницы модуля.
	 * В качестве результата возвращает имя шаблона для рендеринга. Метод учитывает все запущенные
	 * копии, и не позволяет выполнить код вторично.
	 */
	function LoadModulePage($module,$sClassPage)
	{
		global $smarty;
		if(!array_key_exists($module.'_'.$sClassPage,$this->arRunModules))
		{
			if(file_exists(MODULES_DIR.'/'.$module.'/pages/'.$sClassPage.'.php'))
			{
				include  MODULES_DIR.'/'.$module.'/pages/'.$sClassPage.'.php';
				$sObjectName='C'.$module.'AI'.$sClassPage;
				if(class_exists($sObjectName))
				{
					$this->obLanguage->LoadSection($module);
					$obModule=new $sObjectName($module,$smarty,$this);
					if(array_key_exists('ajax',$_REQUEST))
					{
						$this->page=$obModule->RunAjax();
						$this->sMode='ajax';
					}
					else
					{
						$this->page=$obModule->Run();
						$this->obLanguage->LoadSection($module.$this->page);
					}
					$this->arRunModules[$module.'_'.$sClassPage]=$this->page;
					return $this->page;
				}
				else
				{
					throw new CError('MAIN_MODULE_NO_USER_PART',1116,__LINE__);
				}
			}
			else
			{
				throw new CError('MAIN_MODULE_NO_USER_PART',1121,__LINE__);
			}
		}
		else
		{
			$this->page=$this->arRunModules[$module.'_'.$sClassPage];
			return $this->page;
		}
	}

	/**
	 * Выполняет поиск и подключение модуля. $module - текстовое имя модуля. Выполняет поиск модуля в диске
	 * и если обнаруживает, производит его подключение, сохраняет в переменной класса значение текущего
	 * шаблона вывода.
	 * @param $module - string текстовый код модуля
	 * @return boolean -  Возвращает true/false в зависимости от результатов выполнения
	 */
	function AdminShowModule($module)
	{
		global $USER,$smarty;
		if($this->IsModule($module))
		{
			$KS_MODULES=$this;
			if(!array_key_exists($module,$this->arModules))
			{
				$arModule=$this->GetRecord(array('directory'=>$module));
			}
			else
			{
				$arModule=$this->arModules[$module];
			}
			$this->IncludeModule($arModule);
			try
			{
				$page='';
				if(!file_exists(MODULES_DIR.'/'.$module.'/admin.inc.php'))
				{
					if(array_key_exists('page',$_GET))
					{
						if($_GET['page']!='' && !preg_match('#^[a-z0-9_]+$#',$_GET['page']))
							throw new CError('SYSTEM_WRONG_ADMIN_PATH',1001);
						$sPage=$_GET['page'];
					}
					else
					{
						$sPage='index';
					}
					//$access_level=$USER->GetLevel($module);
					//if($access_level>0) throw new CAccessError('SYSTEM_NOT_ACCESS_MODULE');
					if(file_exists(MODULES_DIR.'/'.$module.'/pages/'.$sPage.'.php'))
					{
						$sClassPage=$sPage;
					}
					elseif(file_exists(MODULES_DIR.'/'.$module.'/pages/index.php'))
					{
						$sClassPage='index';
					}
					else
					{
						throw new CError('MAIN_MODULE_NO_ADMIN_PART');
					}
					$this->LoadModulePage($module,$sClassPage);
				}
				else
				{
					$this->obLanguage->LoadSection($module);
					include(MODULES_DIR.'/'.$module.'/admin.inc.php');
					$this->page=$page;
					$this->obLanguage->LoadSection($module.$page);
				}
				return true;
			}
			catch (CError $e)
			{
				$smarty->assign('last_error',$e);
				return false;
			}
		}
		return false;
	}

	/**
	 * Метод выполняет отображение административного раздела
	 * пользователю
	 */
	function Draw(&$smarty)
	{
		$smarty->assign('navChain',$this->arNavChain);
		$smarty->assign('ks_config',$this->GetConfigArray('main'));
		$smarty->assign('host',$_SERVER['HTTP_HOST']);
		//Передаем данные о модуле в шаблон
		$arMenu=$this->GetMenu();
		if(!array_key_exists($this->currentMenu,$arMenu)) $this->currentMenu='main';
		$smarty->assign('module',array('current'=>$this->currentMenu,'page'=>$this->page,'template'=>$this->localTemplate));
		if($this->sMode=='small')
		{
			try
			{
				$content='';
				$template_to_display = 'admin/' . $this->current . $this->page . '.tpl';
				if($smarty->template_exists($template_to_display))
					$content=$smarty->fetch($template_to_display);
				elseif($smarty->template_exists('admin/common/'.$this->page.'.tpl'))
					$content=$smarty->fetch('admin/common/'.$this->page.'.tpl');
				else
					throw new CError('SYSTEM_PAGE_NOT_FOUND',0,$this->page);
				$smarty->display('admin/header_ajax.tpl');
				echo $content;
				$smarty->display('admin/footer_ajax.tpl');
			}
			catch(CError $e)
			{
				echo $e->__toString();
			}
		}
		elseif($this->sMode=='content')
		{
			try
			{
				$content='';
				$template_to_display = 'admin/' . $this->current . $this->page . '.tpl';
				if($smarty->template_exists($template_to_display))
					$content=$smarty->fetch($template_to_display);
				elseif($smarty->template_exists('admin/common/'.$this->page.'.tpl'))
					$content=$smarty->fetch('admin/common/'.$this->page.'.tpl');
				else
					throw new CError('SYSTEM_PAGE_NOT_FOUND',0,$this->page);
				echo $content;
			}
			catch(CError $e)
			{
				echo $e->__toString();
			}
		}
		elseif($this->sMode=='ajax')
		{
			if($smarty->template_exists('admin/common/ajax.tpl'))
				$smarty->display('admin/common/ajax.tpl');
		}
		else
		{
			//Выполняем отрисовку сайта
			$smarty->assign('left_menu',$arMenu);
			if($this->IsActive('help'))
			{
				$smarty->assign('showHelp','Y');
			}
			else
			{
				$smarty->assign('helpEmail',$this->GetConfigVar('main','helpEmail','dev@kolosstudio.ru'));
			}
			$smarty->assign('bShowTreeView',$this->GetConfigVar('main','showTreeView','Y'));
			try
			{
				$content='';
				$template_to_display = 'admin/' . $this->current . $this->page . '.tpl';
				if($smarty->template_exists($template_to_display))
					$content=$smarty->fetch($template_to_display);
				elseif($smarty->template_exists('admin/common/'.$this->page.'.tpl'))
					$content=$smarty->fetch('admin/common/'.$this->page.'.tpl');
				else
					throw new CError('SYSTEM_PAGE_NOT_FOUND',0,$this->page);
			}
			catch(CError $e)
			{
				$content=$e->__toString();
			}
			$smarty->display('admin/header.tpl');
			echo $content;
			$smarty->display('admin/footer.tpl');
		}
	}

	/**
	 * Возвращает список установленных модулей.
	 * Возврат происходит в виде ассоциативного массива, структура записи массива:
	 * 	id	-	номер строки в таблице, целое;
	 * 	name	-	читабельное название модуля, строка 255 символов;
	 * 	URL_ident	-	путь в пользовательской части по которому вызывается модуль, строка 255 символов;
	 * 	directory	-	название папки в папке модулей, где храняться файлы модуля, строка 255 символов;
	 * 	include_global_template - флаг подключения глобального шаблона в пользовательской части, 1 или 0;
	 * 	active - флаг активности, 1 или 0;
	 * 	orderation - порядок подключения, целое число;
	 * 	hook_up - понятия не имею что это, но туда записывается число.
	 */
	function GetInstalledList()
	{
		return $this->GetList(array('orderation'=>'asc'));
	}

	/**
	 * Функция выполняет подключение модуля обрабатывая переданный ей массив описания модуля
	 * @param $arModule - array описывает модуль
	 */
	function InitModule($arModule)
	{
		if(is_string($arModule) && IsTextIdent($arModule))
		{
			if(array_key_exists($arModule,$this->arModules)) return;
			$arModule=$this->GetRecord(array('directory'=>$arModule));
		}
		if(is_array($arModule))
		{
			if(array_key_exists($arModule['directory'],$this->arModules)) return;
			if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/config.php'))
			{
				include(MODULES_DIR.'/'.$arModule['directory'].'/config.php');
				$var="MODULE_".$arModule['directory']."_config";
				if(isset($$var))
				{
					$arModule['config']=$$var;
				}
				else
				{
					$arModule['config']=array();
				}
			}
			if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/.access.php'))
			{
				$arLevels=array();
				$this->obLanguage->LoadSection($arModule['directory']);
				include(MODULES_DIR.'/'.$arModule['directory'].'/.access.php');
				$arModule['ALEVELS']=$arLevels;
			}
			else
			{
				$arModule['ALEVELS']=array('0'=>$this->GetText('access_full'),'10'=>$this->GetText('access_denied'));
			}
			if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/admin.init.php'))
			{
				include(MODULES_DIR.'/'.$arModule['directory'].'/admin.init.php');
			}
			$this->arModules[$arModule['directory']]=$arModule;
			return;
		}
		throw new CError('MAIN_MODULE_NOT_FOUND','',$arModule);
	}

	/**
	 * Метод выполняет подключение модуля
	 */
	function IncludeModule($arModule)
	{
		if(is_string($arModule) && IsTextIdent($arModule))
		{
			$arModule=$this->GetRecord(array('directory'=>$arModule));
		}
		if(is_array($arModule))
		{
			if(!array_key_exists($arModule['directory'],$this->arModules))
			{
				return $this->InitModule($arModule);
			}
			return true;
		}
		throw new CError('SYSTEM_MODULE_INIT_ERROR','',$arModule);
	}

	/**
	 * Функция выполняет первоначальную инициализацию модулей. Подключение файлов меню, языковых
	 * файлов, внесение модуля в список модулей. В результате работы возвращает список подключенных модулей
	 */
	function LinkModules()
	{
		if($arModules=$this->GetList(false,array('active'=>1)))
		{
			foreach($arModules as $arModule)
			{
				$this->InitModule($arModule);
			}
			return $this->arModules;
		}
		return false;
	}

	/** Возвращает сгенерированное системное меню. При этом проверяется какой
	 * модуль ялвяется текущим и если для модуля не найден соответсвующий раздел
	 * то разворачивается раздел Общие настройки.
	 * @todo Проверку нужно будет переделывать, когда будет реализована более мощная работа с меню*/
	function GetMenu()
	{
		$bIsCurrent=false;
		foreach($this->arModules as $arModule)
		{
			if (file_exists(MODULES_DIR.'/'.$arModule['directory'].'/admin.menu.inc.php'))
			{
				$this->obLanguage->LoadSection($arModule['directory'].'_menu');
				include_once(MODULES_DIR.'/'.$arModule['directory'].'/admin.menu.inc.php');
			}
		}
		foreach($this->menu as $key=>$arItem)
		{
			if($arItem['module']==$this->current){
				$bIsCurrent=true;
				break;
			} else {
				//проверим вдруг текущий активный пункт это подменю?
				if(!empty($arItem['items'])){
					foreach($arItem['items'] as $arSubItem){
						if($arSubItem['module']==$this->current){
							$this->currentMenu = $arSubItem['parent_module'];
							$bIsCurrent=true;
							break;
						}
					}
				}
			}
		}
		if(!$bIsCurrent) $this->currentMenu='main';
		return $this->menu;
	}

	/**
	 * Метод возвращает имя шаблона который используется при выводе по данному пути
	 */
	function GetTemplate($path)
	{
		$sTemplate=$this->select_global_template($path);
		$arTemplate=explode(':',$sTemplate);
		if(is_array($arTemplate) && count($arTemplate)>1)
		{
			return $arTemplate[0];
		}
		return '.default';
	}

	/**
	 * @copydoc CObject::Save()
	 */
	function Save($prefix='',$data='',$table='')
	{
		if($data=='') $data=$_POST;
		if(isset($data[$prefix.'URL_ident']) && $data[$prefix.'URL_ident']=='default') $data[$prefix.'active']=1;
		return parent::Save($prefix,$data,$table);
	}

	/**
	 * Метод добавляет пункт в навигационную цепочку
	 * @param $arItem - массив описывающий пункт меню
	 * @param $level - уровень вложенности, если не указан, то элемент добавляется в конец цепочки
	 */
	function AddChainItem($title,$href,$level=false)
	{
		$this->arNavChain[]=array('href'=>$href,'title'=>$title);
	}

	/** Добавляет элемент в меню.
	 * В параметрах передаются, массив с описанием пункта меню,
	 * имя меню родителя.
	 * На данный момент всего 2 уровня вложенности. Структура пункта меню:
	 * @param $item array -	Ассоциативный массив, ключ строки - name - имя пункта меню,
	 * 	данные строки:
	 * 	module - модуль к которому относиться меню
	 * 	href - ссылка
	 * 	title - заголовок пункта меню.
	 * @param $parent string - Меню в которое вкладывается данный пункт
	 * 	@todo Реализовать более мощную работу с меню*/
	function AddMenuItem($item,$parent="")
	{
		if ($parent!="")
		{
			$parent=strtolower($parent);
			if(array_key_exists($parent,$this->menu))
			{
				if (array_key_exists('items',$this->menu[$parent]) && is_array($this->menu[$parent]['items']))
				{
					$this->menu[$parent]['items']=array_merge($this->menu[$parent]['items'],$item);
				}
				else
				{
					$this->menu[$parent]['items']=$item;
				}
			}
			else
			{
				$this->menu[$parent]=array();
			}
		}
		else
		{
			foreach($item as $key=>$arMenu)
			{
				$this->menu[strtolower($key)]=$arMenu;
			}
		}
		}

	/**
	 * Метод добавляет стандартный пункт в систему меню
	 * @param $sModule sting - имя модуля к которому добавляем пункт
	 * @param $sPage string - страница(код) опаерации, если пуста, добавляется корневой пункт меню
	 * @param $sTitle string - заголовок пункта меню
	 * @param $arAdd array - дополнительные параметры пункта меню
	 * @return object - возвращает указатель на класс
	 */
	function Menu($sModule,$sPage,$sTitle,$arAdd=false)
	{
		if($sPage=='')
		{
			//Добавляем корневой пункт меню
			$this->menu[$sModule]=array(
				'module' => $sModule,
				'href' => 'module='.$sModule,
				'title' => $sTitle,
			);
			if(is_array($arAdd)&&count($arAdd)>0)
			{
				$this->menu[$sModule]=array_merge($this->menu[$sModule],$arAdd);
			}
		}
		else
		{
			//Добавляем корневой пункт меню
			$this->menu[$sModule]['items'][$sPage]=array(
				'module' => $sModule,
				'href' => 'module='.$sModule.'&page='.$sPage,
				'title' => $sTitle,
			);
			if(is_array($arAdd)&&count($arAdd)>0)
			{
				$this->menu[$sModule]['items'][$sPage]=array_merge($this->menu[$sModule]['items'][$sPage],$arAdd);
			}
		}
		return $this;
	}
}
