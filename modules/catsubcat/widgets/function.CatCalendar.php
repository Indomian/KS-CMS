<?php
/**
 * \file function.BlogCalendar.php
 * В файле находится виджет реализующий календарь по элементам текстовых страниц
 * Файл проекта kolos-cms.
 * 
 * Создан 1.03.2010
 *
 * \author fox 
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/interfaces/libs/class.CAjax.php';

/**
 * Данный виджет получает параметры напрямую из $_GET. Надо рассмотреть необходимость такого по-
 * ведения. Возможно надо передавать эти параметры через массив $params.
 */
function smarty_function_CatCalendar($params,&$smarty)
{
	global $KS_IND_matches,$USER,$KS_MODULES,$global_template,$ks_db,$ks_config;
	try
	{
		if(KS_DEBUG==1) 
		{
			$startTime=microtime(true);
			$oldMode=$ks_db->SetDebugMode(1007);
		}
		//Проверка и инициализация аякса
		if($params['isAjax']=='Y') 
		{
			/*Ключ о том это аякс запрос или нет*/
			$oldAjax=false;
			$obAjax=new CAjax('CatCalendar',$params);
			if(array_key_exists('ajaxMode',$_GET))
			{
				if($obAjax->CheckHash($_GET['ajaxMode']))
				{
					$oldAjax=true;
					ob_clean();
				}
				else
					return '';
			}
			
		}
		//Подготавливаем данные для работы
		$module_directory = MODULES_DIR . "/catsubcat/";
		include_once($module_directory . "libs/class.CCategoryEdit.php");
		include MODULES_DIR.'/catsubcat/config.php';
		
		$access_level=$USER->GetLevel('catsubcat');
		$arUserGroups=$USER->GetGroups();
		//Проверяем права на доступ к самому модулю
		if($access_level>8) throw new CAccessError('CATSUBCAT_NOT_VIEW_ANNOUNCE');
		
		
		$obRecord=new CElement();
		$obCategory = new CCategory();
		
		//Обрабатываем текущую переданную дату, определяем год и месяц.
		if(($_GET['month']=='')||($_GET['year']==''))
		{
			$arDate=getdate();
		} 
		$params['month']=($_GET['month']!=''?intval($_GET['month']):$arDate['mon']);
		$params['year']=($_GET['year']!=''?intval($_GET['year']):$arDate['year']);
		if($params['month']<1)
		{
			$params['month']=12;
			$params['year']--;
		}
		if($params['month']>12)
		{
			$params['month']=1;
			$params['year']++;
		}
		//Убрать от сюда в будущем
		$arMonthes=array(
			'январь',
			'февраль',
			'март',
			'апрель',
			'май',
			'июнь',
			'июль',
			'август',
			'сентябрь',
			'октябрь',
			'ноябрь',
			'декабрь'
		);
		$arMonthesLength=array(
			31,28,31,30,31,30,31,31,30,31,30,31
		);
		$iDate=mktime(0,0,0,$params['month'],1,$params['year']);
		$iFirstDayInMonth=idate('w',$iDate);
		if($iFirstDayInMonth==0) $iFirstDayInMonth=7;
		$iFirstDayInMonth--;
		$isLeap=idate('I',$iDate);
		if($isLeap==1) $arMonthesLength[1]++;
		$data['month']=array(
			'num'=>$params['month'],
			'title'=>$arMonthes[$params['month']-1],
			);
		$data['year']=$params['year'];
		$day=1;
		$week=0;
		$dayNextMonth=1;
		$iPrevMonthDays=$arMonthesLength[$params['month']-2>0?$params['month']-2:11];
		//Получаем записи
		$arFilter=array(
			'>date_add'=>mktime(0,0,0,$params['month'],1,$params['year']),
			'<date_add'=>mktime(0,0,0,$params['month'],$arMonthesLength[$params['month']-1],$params['year']),
			'active'=>1
		);
		if(isset($params['parent_id']))
		{
			$parent_id=intval($params['parent_id']);
			if ($params["select_from_children"] == "Y")
				{
					if($parent_id!=0)
					{
						/* Анонсируемые страницы выбираются не только из указанного раздела, но и из всех вложенных */
						$children_ids = array_merge(array($parent_id), $obCategory->GetChildrenIds($parent_id));
						$arFilter['->parent_id'] = "(" . implode(", ", $children_ids) . ")";
					}
					
				} 
				else
				{
					$arFilter['parent_id']=$parent_id;
				}
			
		}
		
		if($access_level>=8)
		{
			$arGroups=$USER->GetGroups();
			$arFilter['->access_view']='('.join(',',$arGroups).')';
		}
		$arRes=$obRecord->GetList(array('date_add'=>'asc'),$arFilter,false,array('id','date_add'));
		$arEvents=array();
		if($arRes)
		for($i=0;$i<count($arRes);$i++)
		{
			$iday=idate('d',$arRes[$i]['date_add']);
			$arEvents[$iday]++;
		}
		
		while($day<=$arMonthesLength[$params['month']-1])
		{
			for($wDay=0;$wDay<7;$wDay++)
			{
				if(($wDay<$iFirstDayInMonth)&&($day==1))
				{
					$data['weeks'][$week][$wDay]=array(
						'day'=>$iPrevMonthDays+$wDay-$iFirstDayInMonth+1,
						'cur'=>false,
					);
				}
				elseif($day>$arMonthesLength[$params['month']-1])
				{
					$data['weeks'][$week][$wDay]=array(
						'day'=>$dayNextMonth,
						'cur'=>false,);
					$dayNextMonth++;
				}
				else
				{
					$data['weeks'][$week][$wDay]=array(
						'day'=>$day,
						'cur'=>true,
						'count'=>$arEvents[$day]);
					$day++;
				}
			}
			$week++;
		}
		
		$module_url_ident = $KS_MODULES->arModules['catsubcat']["URL_ident"];
        if ($module_url_ident && $module_url_ident != "default")
           	$data['filterPage']='/'.$module_url_ident.'/';
        else
        	$data['filterPage']='/';
		
		$smarty->assign('data', $data);
    	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
    	$sResult=$KS_MODULES->RenderTemplate($smarty,'/catsubcat/CatCalendar',$params['global_template'],$params['tpl']);
		if(KS_DEBUG==1)
		{
			$sResult.='<div style="border:1px solid red;display:block;padding:5px;">';
			$sResult.='Общее время отработки компонента: '.(microtime(true)-$startTime).'<br/>';
			$sResult.='Запросы:<br/>';
			$sResult.=$ks_db->GetRequestsTable();
			$ks_db->SetDebugMode($oldMode);
		}
		if($params['isAjax']=='Y') $sResult=$obAjax->GetCode($sResult,$oldAjax);
		if($oldAjax)
		{
			echo $sResult;
			die();
		}
		return $sResult;
	}
	catch(CError $e)
	{
		return $e;
	}	
}

function widget_params_CatCalendar()
{
	$arFields = array
	(
		'isAjax'=>array(
			'title'=>'Режим AJAX',
			'type'=>'select',
			'value'=>array('Y'=>'да','N'=>'нет'),
		)
	);
	return array
	(
		'fields' => $arFields
	);
}
?>
