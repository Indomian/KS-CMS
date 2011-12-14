<?php
/**
 * В файле находится класс обеспечивающий управление шаблонами сайта
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 15.12.11
 * @version 2.7
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * CTemplates - управение шаблонами
 */

class CTemplates extends CBaseList
{
	public $sTemplatesPath;
	private $arList;
	private $arWidgets;
	protected $arNotTemplates;

	function __construct()
	{
		$this->sTemplatesPath=TEMPLATES_DIR;
		$this->arList=false;
		$this->arNotTemplates=Array('.','..','admin','cache','configs','templates_c','css','images','.sysfooter.tpl');
		$this->arWidgets=array();
	}

	/**
	 * Метод выполняет проверку папки на наличие темплейта
	 */
	protected function _ParseItem(&$item)
	{
		$sTemplatePath = $this->sTemplatesPath . '/' . $item;
		$result = file_exists($sTemplatePath . '/index.tpl') ?  true : false;
		return $result;
	}

	/**
	 * Метод выполняет очистку внутреннего списка
	 */
	public function Clear()
	{
		$this->arList=false;
	}

	/**
	 * Метод выполняет возврат количества записей
	 */
	public function Count($arFilter = false, $fGroup = false)
	{
		if(!$this->arList) $this->GetList(false,$arFilter);
		$this->iCount=count($this->arList);
		return $this->iCount;
	}

	/**
	 * Метод выполняет получение списка записей шаблонов.
	 */
	public function GetList($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false)
	{
		if($this->arList) return $this->arList;
		$sTemplatesPath=$this->sTemplatesPath;
		if (is_dir($sTemplatesPath))
		{
			if ($hDir = @opendir($sTemplatesPath))
    		{
        		while (($file = readdir($hDir)) !== false)
	        	{
	        		if (!in_array($file,$this->arNotTemplates) && is_dir($sTemplatesPath . '/' . $file))
	        		{
	        			if($this->_ParseItem($file))
	        				$arResult[]=$file;
	        		}
	        	}
	      	}
		}
		$this->arList=$arResult;
		return $arResult;
	}

	/**
	 * Метод, используемый для сортировки файлов шаблонов по имени
	 *
	 * @param string $file1 Имя первого файла
	 * @param string_type $file2 Имя второго файла
	 * @return int
	 */
	private function Compare($file1,$file2)
	{
 		return strcmp($file1['name'], $file2['name']);
	}

	/**
	 * Метод возвращает массив описания виджетов
	 */
	public function GetDescriptions($module)
	{
		$this->GetWidgetDescription($module,'');
		return $this->arWidgets[$module];
	}

	/**
	 * Метод возвращает описание указанного виджета
	 */
	private function GetWidgetDescription($module,$widget)
	{
		if(!array_key_exists($module,$this->arWidgets))
		{
			$sDescriptionPath=MODULES_DIR.'/'.$module.'/widgets/.widgets.php';
			/* Подключаем файл с описанием шаблонов данного модуля */
			if (file_exists($sDescriptionPath))
			{
				$arWidgets = array();
				include($sDescriptionPath);
				$this->arWidgets[$module]=$arWidgets;
			}
			else
			{
				$this->arWidgets[$module]=array();
			}
		}
		return $this->arWidgets[$module][$widget];
	}

	/**
	 * Метод читает список локальных шаблонов для заданного глобального
	 *
	 * @param string $sName Идентификатор глобального шаблона
	 * @return array
	 */
	function GetSubTemplates($sName)
	{
		global $KS_MODULES, $smarty;

		/* Файлы, которые не принимать за шаблоны */
		$arInvisible = array('.', '..', '.description.php');

		/* Расширения файлов шаблонов */
		$arVisible = array('.tpl');

		/* Получаем список всех установленных в системе модулей */
		$arModules = $KS_MODULES->GetInstalledList();

		/* Добавляем к списку главный модуль, который установлен всегда */
		$arModules[] = array('directory' => 'main', 'name' => 'Главный');

		/* Результирующий массив со списком шаблонов для заданного глобального шаблона */
		$arResult = array();

		/* Начинаем поиск шаблонов для каждого из модулей */
		foreach ($arModules as $arModule)
		{
			/* Путь к шаблонам модуля по умолчанию */
			$sPath = TEMPLATES_DIR.'/.default/' . $arModule['directory'] . '/';
			/* Читаем шаблоны по умолчанию */
			if ($hDir = @opendir($sPath))
			{
				$files = array();
				while(($file = readdir($hDir)) !== false)
				{
					/* Тип файла с предворяющей точкой */
					$sFType = substr($file, strrpos($file, '.'));

					/* Если имя файла соответствует формату, то добавляем его в список */
					if ((!in_array($file, $arInvisible)) && in_array($sFType, $arVisible))
						$files[]=$file;
				}
				closedir($hDir);

				/* Путь к шаблонам модуля для заданного глобального шаблона */
				$sPath = TEMPLATES_DIR.'/' . $sName . '/';
				$arResult[$arModule['directory']]=$arModule;
				if (file_exists($sPath))
				{
					/* Ищем локальные шаблоны для заданного глобального шаблона */
					foreach ($files as $FILE)
					{
						$arWidget=$this->GetWidgetDescription($arModule['directory'],str_replace('.tpl','',$FILE));
						if (file_exists($sPath . $arModule['directory'] . '/' . $FILE))
						{
							/* Добавляем локальный шаблон для глобального шаблона */
							$arResult[$arModule['directory']]['widgets'][] = array
							(
								'name' => $FILE,
								'description' => $arWidget['name'],
								'url' => $arModule['directory'] . '/' . $FILE,
								'is_default' => 0,
							);
						}
						else
						{
							/* Используем локальный шаблон по умолчанию, так как для заданного глобального шаблона он не существует */
							$arResult[$arModule['directory']]['widgets'][] = array
							(
								'name' => $FILE,
								'description' => $arWidget['name'],
								'url' => $arModule['directory'] . '/' . $FILE . '/default',
								'is_default' => 1,
							);
						}
					}

					/* Если для модуля найдены шаблоны */
					if (count($arResult[$arModule['directory']]) > 0)
					{
						/* Сортируем шаблоны по имени */
						uasort($arResult[$arModule['directory']]['widgets'], array($this, 'Compare'));

						$arRes = array();
						$sMyName = '';

						/* Определяем, какие шаблоны стандартные, а какие пользовательские */
						foreach($arResult[$arModule['directory']]['widgets'] as $item)
						{
							/* Текущий шаблон */
							$arRow = $item;

							/* Если нет описания для шаблона, значит, он создан пользователем */
							if (strlen($item['description']) == 0)
							{
								if (preg_match('#^' . $sMyName . '(.*)\.tpl#', $item['name'], $matches))
								{
									$arRow['name'] = $matches[1];
									$arRow['is_user'] = 1;
								}
							}
							else
							{
								$sMyName = substr($item['name'], 0, -4);
							}
							$arRes[] = $arRow;
						}

						/* Обновляем массив шаблонов */
						$arResult[$arModule['directory']]['widgets'] = $arRes;
					}
				}
			}
		}
		return $arResult;
	}

	/**
	 * Метод получает список схем, текущего шаблона
	 */
	function GetSchemeList($name)
	{
		$sTemplatesPath=$this->sTemplatesPath.'/'.$name.'/';
		if (is_dir($sTemplatesPath))
		{
			if ($hDir = @opendir($sTemplatesPath))
    		{
        		while (($file = readdir($hDir)) !== false)
	        	{
	        		if (preg_match('#^[a-z\.0-9]+\.tpl$#i',$file)&&!in_array($file,$this->arNotTemplates))
	        		{
	        			$arResult[]=preg_replace('#\.tpl$#i','',$file);
	        		}
	        	}
	      	}
		}
		return $arResult;
	}

	/**
	 * Метод возвращает содержимое файла указанного глобального шаблона.
	 * @param $name - имя шаблона
	 * @param $scheme - имя схемы, по умолчанию index
	 * @return string
	 */
	function GetTemplate($name)
	{
		$sPath=TEMPLATES_DIR.'/'.$name;
		$sContent='';
		$arResult=array();
		try
		{
			$arSchemes=$this->GetSchemeList($name);
			if(!empty($arSchemes)){
				foreach($arSchemes as $scheme){
					if (file_exists($sPath.'/'.$scheme.'.tpl')){
						$sContent=@file_get_contents($sPath.'/'.$scheme.'.tpl');
					} else {
						throw new CDataError('MAIN_TEMPLATE_NOT_FOUND');
					}
					$arResult[$scheme]=$sContent;
				}
			} else {
				throw new CDataError('MAIN_TEMPLATE_NOT_REGISTERED');
			}
		}
		catch(CDataError $e)
		{
			throw $e;

		}
		return $arResult;
	}

	function SubTemplate($name,$url)
	{
		global $KS_MODULES;
		$arUrl=explode('/',$url);
		$arModules=$KS_MODULES->GetInstalledList();
		$arModules[]=array('directory'=>'main','name'=>'Главный');
		$modules=Array();
		foreach($arModules as $arItem)
		{
			$modules[]=$arItem['directory'];
		}
		$template=$arUrl[0];
		if (in_array($template,$modules))
		{
			$sPath=TEMPLATES_DIR.'/'.$name.'/'.$url;
			$sContent='';
			if (file_exists($sPath))
			{
				$hFile=@fopen($sPath,"r");
				if ($hFile)
				{
					while(!feof($hFile))
					{
						$sContent.=fgets($hFile);
					}
				}
				else
				{
					$sContent='Не могу прочитать файл шаблона';
				}
			}
			else
			{
				$sPath=TEMPLATES_DIR.'/.default/'.$template.'/'.$arUrl[1];
				$hFile=@fopen($sPath,"r");
				if ($hFile)
				{
					while(!feof($hFile))
					{
						$sContent.=fgets($hFile);
					}
				}
				else
				{
					$sContent='Не могу прочитать файл шаблона по умолчанию';
				}
			}
			$arDescription=$this->GetWidgetDescription($arUrl[0],str_replace('.tpl','',$arUrl[1]));
			$description=$arDescription['name'];
		}
		else
		$sContent="Такого модуля не существует";
		return Array('content'=>$sContent,'name'=>$description,'module'=>$arUrl[0],'file'=>$arUrl[1]);
	}

	/**
	 * Метод производит сохранение корневого шаблона (глобального) по указанному имени
	 */
	function SaveTemplate($name='',$scheme='index')
	{
		try
		{
	        $sPath=TEMPLATES_DIR.'/'.$name.'/';
	        $sTemplate=$_POST['template_file'];
	        if(ini_get('magic_quotes_gpc')==1)
			{
				$sTemplate=stripslashes($sTemplate);
			}
			if($_POST['is_new']==1)
			{
				if(file_exists($sPath)) throw new CError("MAIN_TEMPLATE_ALREADY_EXISTS",0);
			}
	        if (!file_exists($sPath))
	        {
	        	mkdir($sPath,0777);
	        }
	        if (!file_exists($sPath.$scheme.'.tpl')||is_writable($sPath.$scheme.'.tpl'))
	        {
		       	$hFile=@fopen($sPath.$scheme.'.tpl',"w");
		       	if ($hFile)
		       	{
		       		if (!fwrite($hFile,$sTemplate))
		       		{
		       			throw new CError("SYSTEM_NOT_WRITE_TO_FILE",0,$sTemplate.'/'.$scheme.'.tpl');
		       		}
		       		else
		       		{
		       			return 0;
		       		}
		       	}
		    }
	       	else
	       	{
	       		throw new CError("SYSTEM_FILE_NOT_FOUND_OR_NOT_WRITABLE");
	       	}
		}
		catch(CError $e)
		{
			throw new CError("MAIN_ERROR_WRITING_TEMPLATE",0,$e->GetMessage());
		}
	}

	/**
	 * Метод создаёт копию шаблона $id с именем $new_id
	 *
	 * @version 1.0
	 * @since 04.08.2009
	 *
	 * @param string $id
	 * @param string $new_id
	 * @return bool
	 *
	 * @todo Надо тестировать
	 */
	function Copy($id, $new_id)
	{
		global $KS_FS;
		if (is_string($id) && is_string($new_id) && $id != $new_id)
			if (strlen($id) && strlen($new_id))
				if (file_exists(TEMPLATES_DIR . "/" . $id . "/") && file_exists(TEMPLATES_FILES_DIR . "/" . $id . "/"))
				{
					if (!file_exists(TEMPLATES_DIR . "/" . $new_id . "/"))
					{
						/* Копируем локальные шаблоны */
						$KS_FS->dircopy(TEMPLATES_DIR . "/" . $id . "/", TEMPLATES_DIR . "/" . $new_id . "/");

						/* Копируем css и картинки */
						$KS_FS->dircopy(TEMPLATES_FILES_DIR . "/" . $id . "/", TEMPLATES_FILES_DIR . "/". $new_id . "/");

						return true;
					}
					else
						throw new CError("MAIN_TEMPLATE_ALREADY_EXISTS", 0, $new_id);
				}
				else
					throw new CError("MAIN_TEMPLATE_NOT_EXIST_OR_CORRUPTED", 0, $id);

		return false;
	}

	function Delete($id)
	{
		global $KS_FS;
		if($id=='.default') throw new CError("MAIN_NOT_REMOVE_BASE_TEMPLATE",0);
		if(strlen($id)<1) throw new CError("MAIN_BLANK_TEMPLATE",0);
		if(file_exists(TEMPLATES_DIR."/$id/"))
		{
			return $KS_FS->remdir(TEMPLATES_DIR."/$id");
		}
		return false;
	}

	function DeleteSub($id,$url)
	{
		$arRes=$this->SubTemplate($id,$url);
		if($arRes['name']!='') throw new CError("MAIN_NOT_DELETE_SYSTEM_TEMPLATE",0);
		if(preg_match('#/\./|/\.\./|\.\.#',$url)) throw new CError("MAIN_INVALID_PATH_TEMPLATE",0);
		if(!preg_match('#^.*\.tpl$#',$url)) throw new CError("MAIN_INVALID_TEMPLATE_FILE_NAME", 0);
		if(file_exists(TEMPLATES_DIR."/$id/$url"))
		{
			return unlink(TEMPLATES_DIR."/$id/$url");;
			//return true;
		}
		throw new CError("SYSTEM_FILE_NOT_EXIST",0);
	}

	function SaveSub($name)
	{
		$sSubTemplate=$_POST['file'];
		$sTemplate=$_POST['template_file'];
		$sModule=$_POST['s_module'];
		$sPath=TEMPLATES_DIR.'/'.$name.'/';
		if(ini_get('magic_quotes_gpc')==1)
		{
			$sTemplate=stripslashes($sTemplate);
		}
		if (file_exists($sPath))
		{
			if (!file_exists($sPath.$sModule.'/'))
			{
				mkdir($sPath.$sModule,0777);
			}
			if(is_writable($sPath.$sModule.'/'.$sSubTemplate)||!file_exists($sPath.$sModule.'/'.$sSubTemplate))
        	{
				$hFile=@fopen($sPath.$sModule.'/'.$sSubTemplate,"w");
			   	if ($hFile)
			   	{
		     		if (!fwrite($hFile,$sTemplate))
		    		{
		     			throw new CError("SYSTEM_NOT_WRITE_TO_FILE", 0, $sSubTemplate);
		       		}
		       		else
		     		{
			    		fclose($hFile);
			     		if(!file_exists(TEMPLATES_DIR.'/.default/'.$sModule.'/'.$sSubTemplate))
			     		{
		    	 			$hFile=@fopen(TEMPLATES_DIR.'/.default/'.$sModule.'/'.$sSubTemplate,"w");
		     				fwrite($hFile,$sTemplate);
		     				fclose($hFile);
		       			}
		       			return true;
		       		}
		     	}
		     	else
		     	{
		     		throw new CError("MAIN_NOT_CREATE_SUBTEMPLATE_NO_WRITE", 0, $sPath.$sModule.'/'.$sSubTemplate);
		     	}
			}
			else
			{
				throw new CError("MAIN_NOT_CREATE_SUBTEMPLATE_NO_WRITE", 0, $sPath.$sModule.'/'.$sSubTemplate);
			}
		}
		throw new CError("MAIN_TEMPLATE_WITH_FIRST_NAME_NOT_EXIST", 0, $sPath);
	}
}



