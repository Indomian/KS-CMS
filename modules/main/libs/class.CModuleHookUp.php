<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CModuleManagment.php';

/**
 * Класс используется для обеспечения подключения модулей в пользовательской части сайта
 * @version 2.6
 */
class CModuleHookUp extends CModuleManagment
{
	protected $arWidgetStack;
	protected $sTemplate;
	protected $sScheme;
	protected $arWidgetTimes;
	protected $arRequestData;
	static private $instance;

	function __construct($sTable='main_modules')
	{
		parent::__construct($sTable);
	}

	/**
	 * Метод заменяющий конструктор. Используется для инициализации.
	 */
	private function init()
	{
		$this->arWidgetStack=array();
		$this->sTemplate='.default';
		$this->sScheme='index';
		$this->ParseCurrentRequest();
	}

	/**
	 * Метод обрабатывает текущий запрос от пользователя
	 */
	private function ParseCurrentRequest()
	{
		/**
		 * Структура массива $KS_IND_matches
		 * 0 - полный путь переданный пользователем
		 * 1 - массив директорий со слешем на конце ОБЯЗТЕЛЬНО (по-другому не отработает)
		 * 2 - имя файла (может отсутствовать) (с расширением)
		 * 3 - имя файла (может отсутствовать) (без расширения)
		 */
		$sPath='';
		if(array_key_exists('path',$_GET))
		{
			$sPath=$_GET['path'];
		}
		else
		{
			$arData=CUrlParser::ParseUrl($_SERVER['REQUEST_URI']);
			$sPath=$arData['url'];
			if(is_array($arData['params']))
				$_GET=array_merge($_GET,$arData['params']);
		}
		if($sPath=='/')
		{
			$sPath='/index.html';
		}
		$this->arRequestData=array(
			'path'=>$sPath,
			'dirs'=>explode('/',$sPath)
		);
		if(is_array($this->arRequestData['dirs']) && count($this->arRequestData['dirs'])>0)
		{
			$this->arRequestData['page']=array_pop($this->arRequestData['dirs']);
			if(strrpos($this->arRequestData['page'],'.')>0)
			{
				$this->arRequestData['text_ident']=substr($this->arRequestData['page'],0,strrpos($this->arRequestData['page'],'.'));
			}
			else
			{
				$this->arRequestData['text_ident']=$this->arRequestData['page'];
			}
		}
		else
		{
			$this->arRequestData['page']='';
			$this->arRequestData['text_ident']='';
		}
	}

	function GetCurrentRequest()
	{
		return $this->arRequestData['path'];
	}

	/**
	 * Метод инициализирует шаблон с которым будет работать система
	 */
	public function InitTemplates()
	{
		if($sTemplate=$this->select_global_template($this->GetCurrentRequest()))
		{
			$arTemplate=explode(':',$sTemplate);
			if(is_array($arTemplate)&&count($arTemplate)>1)
			{
				$this->sTemplate=$arTemplate[0];
				$this->sScheme=$arTemplate[1];
			}
		}
	}

	/**
	 * Метод возвращает true если пользователь запросил страницу
	 */
	function IsPage()
	{
		if($this->arRequestData['page']!='') return true;
		return false;
	}

	/**
	 * Метод возвращает true если пользователь запросил каталог
	 */
	function IsCatalogue()
	{
		return !$this->IsPage();
	}

	/**
	 * Метод возвращает текущий текстовый идентификатор страницы (если это страница)
	 */
	function CurrentTextIdent()
	{
		if($this->IsPage())
		{
			return $this->arRequestData['text_ident'];
		}
		return false;
	}

	/**
	 * Метод выполняет проверку текущего адреса на соответствие тому, что есть сейчас.
	 */
	function CheckPath($sPath,$bInModule=true)
	{
		$arPath=explode('/',$sPath);
		$arResult=array();
		$sPage='';
		if(is_array($arPath) && count($arPath)>0)
		{
			$sPage=array_pop($arPath);
			if(strrpos($this->arRequestData['page'],'.')>0)
			{
				$sPage=substr($sPage,0,strrpos($sPage,'.'));
			}
			foreach($arPath as $sElement)
			{
				if($bInModule && trim($sElement)=='') continue;
				$arResult[]=trim($sElement);
			}
		}
		$iRequestCount=count($this->arRequestData['dirs']);
		if(count($arResult)>0)
		{
			$iShift=2;
			if(!$bInModule) $iShift=0;
			foreach($arResult as $i=>$sElement)
			{
				if($i+$iShift<$iRequestCount)
				{
					if($sElement!=$this->arRequestData['dirs'][$i+$iShift]) return false;
				}
				else
					return false;
			}
			if($sPage!='')
			{
				return $sPage==$this->CurrentTextIdent();
			}
		}
		return true;
	}

	/**
	 * Метод возвращает кусочек запроса с указанным номером
	 */
	function GetPathPart($iPart=1)
	{
		if(count($this->arRequestData['dirs'])<=$iPart) return '';
		return $this->arRequestData['dirs'][$iPart];
	}

	/**
	 * Метод возвращает список разделов пути
	 */
	function GetPathDirs($from=1)
	{
		if(is_array($this->arRequestData['dirs']) && count($this->arRequestData['dirs'])>=$from)
		{
			return array_slice($this->arRequestData['dirs'],$from);
		}
		return array();
	}

	/**
	 * This implements the 'singleton' design pattern
	 *
	 * @return object CModuleHookUp The one and only instance
	 */
	static function get_instance()
	{
		if (!self::$instance)
		{
			self::$instance = new CModuleHookUp();
			self::$instance->init();  // init AFTER object was linked with self::$instance
		}
		return self::$instance;
	}

	/**
	 * Метод возвращает массив времени выполнения виджетов
	 */
	function GetWidgetTimes()
	{
		return $this->arWidgetTimes;
	}

	function AddToLog($widget,$time)
	{
		if(KS_DEBUG==1)
		{
			$this->arWidgetTimes[]=array('name'=>$widget,'time'=>$time,'level'=>count($this->arWidgetStack));
		}
	}

	/**
	 * Метод выполняет подключение и инициализацию указанного модуля
	 * @param string|array $module_name - название модуля или массив его описания
	 * @return array|false - если модуль подключен - массив со списком подключенных модулей, иначе false;
	 */
	function InitModule($module_name)
	{
		global $ks_db,$smarty;
		$arModule=array('active'=>'0');
		//Смотрим какой первый параметр, если строка - ищем такой модуль
		if(is_string($module_name))
		{
			if (array_key_exists($module_name, $this->arModules)) return $this->arModules;
			if($arModule=$this->GetRecord(array('active'=>1,'directory'=>$module_name)))
				$this->arModules[$module_name] = $arModule;
			else
				return false;
		}
		//если массив, считаем что  это описание модуля
		elseif(is_array($module_name))
		{
			if (array_key_exists($module_name['directory'], $this->arModules)) return $this->arModules;
			$this->arModules[$module_name['directory']]=$module_name;
			$module_name=$module_name['directory'];
		}
		else
		{
			return false;
		}
		$arModule=$this->arModules[$module_name];
		if($arModule['active']==1)
		{
			/* Подключение конфигурационного файла */
			$module_config_file = MODULES_DIR . "/" . $arModule['directory'] . "/config.php";
			if (file_exists($module_config_file))
			{
				/* Чтение конфигурационных массивов */
				include($module_config_file);
				$config_var = "MODULE_" . $arModule['directory'] . "_config";
				$db_config_var = "MODULE_" . $arModule['directory'] . "_db_config";
				if (isset($$config_var))
					$arModule['config'] = $$config_var;
				if (isset($$db_config_var))
					$arModule['db_config'] = $$db_config_var;
			}
			else
			{
				$arModule['config']=array();
				$arModule['db_config']=array();
			}
			if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/init.inc.php'))
			{
				include_once MODULES_DIR.'/'.$arModule['directory'].'/init.inc.php';
			}
				if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/main.init.php'))
			{
				include_once MODULES_DIR.'/'.$arModule['directory'].'/main.init.php';
			}
			$smarty->plugins_dir[] = MODULES_DIR.'/'.$module_name.'/widgets/';
			$this->arModules[$module_name]=$arModule;
			return $this->arModules;
		}
		return false;
	}

	/**
	 * Метод выполняет указанный модуль и возвращает результат выполнения.
	 * Метод осуществляет подключение модуля
	 *
	 * @version 2.5.4-16
	 * @since 09.12.2010
	 * Добавлено автоматическое подключение виджетов по параметру
	 * Добавлено сохранение конфигурационных массивов модулей с параметрами для работы с базой данных
	 *
	 * @param array $arModule Массив с параметрами модуля
	 * @param string $output
	 */
	function IncludeModule($arModule,&$output)
	{
		global $KS_URL;
		if(!array_key_exists($arModule['directory'],$this->arModules))
		{
			$this->InitModule($arModule);
		}
		if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/main.inc.php') )
		{
			$module_main_file = MODULES_DIR . "/" . $arModule['directory'] . "/main.inc.php";
			if(array_key_exists('fromsearch',$_GET)&&($_GET['fromsearch']!=''))
			{
				$module_search_file = MODULES_DIR . "/" . $arModule['directory'] . "/search.inc.php";
				if (file_exists($module_search_file))
				{
					$hash = $_GET['fromsearch'];
					include ($module_search_file);
				}
				else
				{
					if ($arModule['URL_ident'] == "default")
						$KS_URL->redirect("/");
					else
						$KS_URL->redirect("/" . $arModule['URL_ident'] . "/");
				}
			}
			elseif(array_key_exists('params',$arModule) && array_key_exists('is_widget',$arModule['params']) && $arModule['params']['is_widget']==1)
			{
				return $this->IncludeWidget($arModule['directory'],$arModule['params']['action'],$arModule['params']);
			}
			else
			{
				/* Производим инициализацию пользовательской части модуля */
				if(array_key_exists('params',$arModule))
				{
					$module_parameters = $arModule['params'];
				}
				else
				{
					$module_parameters=array();
				}
				include ($module_main_file);
			}
		}
		else
		{
			/* Невозможно инициализировать модуль */
			throw new CError("MAIN_MODULE_NO_USER_PART", 404);
		}
	}

	/**
	 *	Подключение указанного модуля
	 *	@param $url_ident - путь, для которого требуется подключить модуль.
	 */
	function hook_up($url_ident='default')
	{
		global $ks_db;
		if( empty($url_ident) )
		{
			$url_ident = 'default';
		}
		$output=array(
			'include_global_template'=>1
		);
		if($arModule=$this->GetRecord(array('URL_ident'=>$url_ident,'active'=>1)))
		{
			if($arModule['include_global_template'] == 0)
				$output['include_global_template'] = 0;
			try
			{
				$this->IncludeModule($arModule,$output);
				$this->obSmarty->assign('SITE',$this->GetConfigArray('main'));
			}
			catch (CModuleError $e)
			{
				if( $url_ident != 'default' )
				{
					/* пробуем обработать дефолтовым */
					$this->hook_up();
				}
				else
				{
					/* это и есть дефолтовый, нужно отдать ошибку, что страница не найдена */
					throw new CError("SYSTEM_PAGE_NOT_FOUND",404);
				}
			}
			catch (CError $e)
			{
				throw $e;
			}
		}
		elseif($url_ident != 'default')
		{
			/* модуля нет, пробуем отработать дефолтовым */
			$output = $this->hook_up();
		}
		else
		{
			/* если нет дефолтового модуля, найти подходящий не удалось, отдаем 404 */
			throw new CError("SYSTEM_PAGE_NOT_FOUND",404);
		}
		return $output;
	}

	/**
	 * Метод возвращает имя шаблона для вывода
	 */
	function GetTemplate()
	{
		return $this->sTemplate;
	}

	/**
	 * Метод возвращает схему для вывода
	 */
	function GetScheme()
	{
		return $this->sScheme;
	}

	/**
	 * Метод проверяет доступность вызова виджета через AJAX запрос
	 */
	function CanAjaxCall($module,$widget)
	{
		if($module=='main')
		{
			//Проверка системных виджетов
			if($widget=='ConfigVar') return false;
		}
		$sMode=$this->GetConfigVar('main','ajax_mode','black_list');
		$arList=$this->GetConfigVar('main','ajax_widgets',array());
		if($sMode=='white_list')
		{
			if(array_key_exists($module,$arList) && is_array($arList[$module]))
			{
				if(array_key_exists($widget,$arList[$module]) && $arList[$module][$widget]==1)
				{
					return true;
				}
			}
		}
		elseif($sMode=='black_list')
		{
			if(array_key_exists($module,$arList) && is_array($arList[$module]))
			{
				if(array_key_exists($widget,$arList[$module]) && $arList[$module][$widget]==1)
				{
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Метод выполняет подключение виджета.
	 * @param $module - имя модуля чей виджет необходимо подключить
	 * @param $action - имя виджета
	 * @param $params - параметры перадаваемые виджету
	 *
	 * @since 2.5.4-16
	 * Добавлена проверка на подключение функции. Снижено количество обращений к жесткому диску.
	 */
	function IncludeWidget($module,$action,$params)
	{
		global $smarty;
		if(count($this->arWidgetStack)>10) throw new CError('SYSTEM_WIDGET_TOO_NESTED');
		try
		{
			if(!array_key_exists($module,$this->arModules))
			{
				//Если модуль не загружен, пробуем его загрузить
				if(!$this->InitModule($module)) throw new CError('MAIN_THIS_NOT_MODULE');
			}
			$arModule=$this->arModules[$module];
			array_push($this->arWidgetStack,$module.'_'.$action);
			if(!function_exists('smarty_function_'.$action))
			{
				//Если виджет не зарегистрирован, пробуем его найти и загрузить
				if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/widgets/function.'.$action.'.php'))
				{
					include_once(MODULES_DIR.'/'.$arModule['directory'].'/widgets/function.'.$action.'.php');
				}
				else
				{
					throw new CError('MAIN_WIDGET_NOT_REGISTERED');
				}
			}
			if(!array_key_exists('global_template',$params))
				$params['global_template']='';
			if(!array_key_exists('tpl',$params))
				$params['tpl']='';
			$sResult=call_user_func('smarty_function_'.$action,$params,$smarty);
		}
		catch(CError $e)
		{
			if(array_pop($this->arWidgetStack)!=$module.'_'.$action) throw new CError('SYSTEM_WIDGET_RETURN_ERROR',0,$module.'_'.$action);
			throw $e;
		}
		if(array_pop($this->arWidgetStack)!=$module.'_'.$action) throw new CError('SYSTEM_WIDGET_RETURN_ERROR',0,$module.'_'.$action);
		return $sResult;
	}

	/**
	 * Метод выполняет подключение модулей, которые использутся системой
	 * @todo Убрать прямые запросы к БД
	 */
	function AutoInit()
	{
		global $ks_db;
		$res_sel_modules_to_include = $ks_db->query("
			SELECT	*
			FROM	`".PREFIX."main_modules`
			WHERE	(`active` = '1') AND (`hook_up`='1')
				ORDER BY `orderation` ASC");
		while ($r_sel_modules_to_include = $ks_db->get_array($res_sel_modules_to_include))
		{
			if(!$this->InitModule($r_sel_modules_to_include)) throw new CError('SYSTEM_MODULE_INIT_ERROR',0,$r_sel_modules_to_include['directory']);
		}
		return true;
	}

	/**
	 * Метод выполняет подключение и обработку шаблона виджета. Также метод пытается
	 * найты яваскрипт файл который привязан к данному виджету и если это ему удается
	 * он подключает этот файл
	 */
	function RenderTemplate($smarty,$widget,$globalTemplate='',$localTemplate='')
	{
		global $global_template;
		//Формируем первую часть пути (глобальный шаблон)
		$sTemplate=(strlen($globalTemplate)==0)?$global_template:$globalTemplate;
		//Добавляем путь к шаблону
		$sLocalTemplate=$widget.$localTemplate.'.tpl';	// шаблон по умолчанию
		$sJsFile=$widget.$localTemplate.'.js';
		//Проверяем наличие шаблона
		if($smarty->template_exists($sTemplate.$sLocalTemplate))
		{
			$sResult=$smarty->fetch($sTemplate.$sLocalTemplate);
		}
		else
		{
			if($smarty->template_exists('.default'.$sLocalTemplate))
			{
				$sResult=$smarty->fetch('.default'.$sLocalTemplate);
			}
			else
				//Eсли шаблон так и не нашли отдаем ошибку
				throw new CError("MAIN_TEMPLATE_ERROR",0,$sTemplate.$sLocalTemplate);
		}
		if(!file_exists(ROOT_DIR.JS_DIR.$sJsFile))
		{
			if(file_exists(ROOT_DIR.JS_DIR.$widget.'.js'))
			{
				$this->UseJavaScript($widget.'.js',12);
			}
		}
		else
		{
			$this->UseJavaScript($sJsFile,13);
		}
		return $sResult;
	}

}