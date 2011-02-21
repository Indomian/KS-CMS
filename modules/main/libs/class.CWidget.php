<?php

/**
 * Модуль для работы с виджетами
 * 
 * Класс управляет работой виджетов, используется в административной части для создания кода вывода виджетов
 *
 * @filesource class.CWidget.php
 * @author blade39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @version 1.1
 * @since 05.05.2009
 * 
 * Добавлен новый метод CWidget::GetWidgetTemplates()
 * Дополнены методы CWidget::GetParams(), CWidget::GenSmartyCode(), CWidget::GetList()
 */

include_once MODULES_DIR . '/interfaces/libs/CInterface.php';
 
class CWidget extends CInterface
{
	var $sModule;
	var $sWidget;
	var $subsmarty;
	var $bInit;
	
	/**
	 * Конструктор объекта класса. Принимает название виджета, производит
	 * проверку на его существование, ищет инициализирован ли виджет в смарти,
	 * если нет производит его подключение.
	 * @param $module - текстовый идентификатор модуля.
	 * @param $widget - название требуемого виджета.
	 * @param $subsmarty - указатель на объект смарти.
	 */
	function __construct($module,$widget,&$subsmarty=NULL)
	{
		$this->bInit=false;
		$ob=new CModuleManagment();
		if($ob->IsModule($module))
		{
			if(file_exists(MODULES_DIR.'/'.$module.'/widgets/function.'.$widget.'.php'))
			{
				$this->sModule=$module;
				$this->sWidget=$widget;
				$this->subsmarty=&$subsmarty;
				include_once(MODULES_DIR.'/'.$module.'/widgets/function.'.$widget.'.php');
				$subsmarty->register_function($widget,"smarty_function_".$widget);
				$this->bInit=true;
				return true;
			}
			else
			{
				throw new CError("MAIN_WIDGET_NOT_REGISTERED");
			}
		}
		else
		{
			throw new CError("MAIN_MODULE_NOT_REGISTERED");
		}
	}
	
	/**
	 * Метод получает список шаблонов виджета для заданного глобального шаблона
	 *
	 * @version 1.0
	 * @since 04.05.2009
	 * 
	 * @param string $global_template Имя глобального шаблона
	 * @return array
	 */
	function GetWidgetTemplates($global_template = ".default")
	{
		if ($this->sWidget == '')
			return false;
		$widgetList = array();
		$tpl_path = ROOT_DIR . "/templates/" . $global_template . "/" . $this->sModule . "/";
		if (file_exists($tpl_path))
		{
			$dh = opendir($tpl_path);
			while (false !== ($file = readdir($dh)))
			{
				if (preg_match("#" . $this->sWidget . "(.*)\.tpl#", $file, $matches))
				{
					if ($matches[1])
						$widgetList[$matches[1]] = $matches[1];
					else 
						$widgetList[''] = 'Стандартный';
				}
			}
			closedir($dh);
		}
		return $widgetList;
	}
	
	/**
	 * Метод получает параметры виджета в виде массива
	 * 
	 * @version 1.1
	 * @since 05.05.2009
	 * 
	 * Вынесено из функций получения параметров виджетов получение глобальных шаблонов и шаблонов виджетов,
	 * добавлено получение названий виджетов из файла .widgets
	 * 
	 * @return array
	 */
	function GetParams($extra_params = array())
	{
		if ($this->bInit)
		{	
			/* Вызов функции, генерирующей массив параметров виджета */
			$arData = call_user_func('widget_params_' . $this->sWidget, $extra_params);
			
			/* Установка параметров, общих для всех виджетов */
			$arData['module'] = $this->sModule;
			$arData['widget'] = $this->sWidget;
			
			/* Получаем список глобальных шаблонов */
			$obTpl = new CTemplates();
			$res = $obTpl->GetList();
			foreach($res as $item)
				$tplList[$item] = $item;
				
			$arData['fields']['global_template'] = array
			(
				'title' => 'Глобальный шаблон',
				'type' =>'select',
				'value' => $tplList,
			);
			$arData['fields']['tpl'] = array
			(
				'title' => 'Шаблон вывода виджета',
				'type' => 'select',
				'value' => $this->GetWidgetTemplates(),
			);
			
			/* Получаем название виджета и описание */
			$descrfile = MODULES_DIR . "/" . $this->sModule . "/widgets/.widgets.php";
			if (file_exists($descrfile))
				include($descrfile);
			if (isset($arWidgets[$this->sWidget]))
				$arData['title'] = $arWidgets[$this->sWidget]['name'];
			else 
				$arData['title'] = 'Пользовательский';
			
			/* Возвращаем результат */
			return $arData;
		}
		return false;
	}
	
	/**
	 * Метод получает параметры введенные пользователем и проверяет их на валидность.
	 * 
	 * @param $params - массив параметров
	 */
	function ParseParams($params)
	{
		return $params;
	}
	
	/**
	 * Выполняет отображение виджета средствами смарти. Функция дублирует механизм
	 * вызова смарти, требуется для вывода превью виджета в административной части.
	 * @param $params - параметры.
	 */
	function Show($params)
	{
		if($this->bInit)
		{
			$res=call_user_func('smarty_function_'.$this->sWidget,$params,$this->subsmarty);
			return $res;
		}
	}
	
	/**
	 * Метод возвращает шаблон настройки параметров вставляемого виджета
	 *
	 * @param string $tpl Имя шаблона
	 * @return string
	 */
	function GetParamsHTML($tpl = false, $extra_params = array())
	{
		/* Установка шаблона по умолчанию */
		if (!$tpl)
			$tpl = 'main_templates_widget_step3.tpl';
		$tpl = 'admin/' . $tpl;
		$this->subsmarty->assign('data', $this->GetParams($extra_params));
		return $this->subsmarty->fetch($tpl);
	}
	
	/**
	 * Метод генерирует строку виджета для шаблона Смарти
	 * 
	 * @version 1.1
	 * @since 29.04.2009
	 * 
	 * Добавлена поддержка переменных Смарти
	 * 
	 * @param array $params Список параметров виджета
	 * @return string
	 */
	function GenSmartyCode($params)
	{
		$sParams = '';
		foreach($params as $item => $value)
		{
			if($value=='') continue;
			if($item=='global_template' && $value=='.default') continue;
			/* Если вставляем значение переменной из Смарти, то кавычки добавлять не нужно */
			if (preg_match('#^\$#', $value))
				$sParams .= $item . '=' . $value . ' ';
			else
				$sParams .= $item . '="' . $value . '" ';
		}
		return '{widget name=' . $this->sModule . ' action=' . $this->sWidget . ' ' . trim($sParams) . '}';
	}
	
	/**
	 * Метод возвращает список доступных в модуле виджетов (можно использовать без создания объекта функции)
	 * 
	 * @version 1.1
	 * @since 29.04.2009
	 * 
	 * Добавлена фильтрация некорректных виджетов
	 * 
	 * @param string $module Строковый идентификатор модуля
	 * @return array
	 */
	function GetList($module)
	{
		/* Выходной массив */
		$arResult = array();
				
		/* Проверяем существование модуля с переданным идентификатором */
		$ob = new CModuleManagment();
		if ($ob->IsModule($module))
		{
			/* Путь к каталогу с виджетами */
			$sTemplatesPath = MODULES_DIR . '/' . $module . '/widgets/';
			
			// Каталоги, которые не причислять к каталогам шаблонов 
			$arNotTemplates = array('.', '..', 'admin', 'cache', 'configs', 'templates_c');
			if (is_dir($sTemplatesPath))
			{
				if (file_exists($sTemplatesPath . ".widgets.php"))
				{
					$arWidgets = array();
					include($sTemplatesPath . ".widgets.php");
					
					// Чтение каталога с виджетами 
					if ($hDir = @opendir($sTemplatesPath))
	    			{
		        		while (($file = readdir($hDir)) !== false)
			        	{
		        			if (!in_array($file, $arNotTemplates) && preg_match('#^function\.([\w]+)\.php$#', $file, $matches))
		        			{
		        				if ($arWidgets[$matches[1]]["has_widget"])
		        				{
			        				$arResult[$file]["widget"] = $matches[1];
			        				$arResult[$file]["widgetname"] = isset($arWidgets[$matches[1]]["name"]) ? $arWidgets[$matches[1]]["name"] : $matches[1];
			        				$arResult[$file]["widgetdescr"] = isset($arWidgets[$matches[1]]["descr"]) ? $arWidgets[$matches[1]]["descr"] : "Нет описания";
		        				}
		        			}
		        		}
		      		}
				}
			}
		}
		return $arResult;
	}
}

?>
