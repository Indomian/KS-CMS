<?php
/**
 * @file function.WaveCount.php
 * Виджет выполняет подсчет количества комментариев по хэшу
 * Файл проекта kolos-cms.
 * 
 * @since 29.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Функция производит вывод количества комментариев
 * @param $params массив параметров.
 * @param $subsmarty - указатель на объект смарти.
 * Параметры могут быть следующими:
 * 	count - количество выводимых элементов;
 *  hash - ключ элемента к которому выводятся комментарии
 *  filter - массив для фильтрации выводимых записей;
 */
function smarty_function_WaveCount($params,&$subsmarty)
{
	global $global_template,$USER,$ks_db,$KS_MODULES,$KS_URL;
	//Проверка и инициализация аякса
	$arData=array();
	/* Проверка общих прав на просмотр тем */
	$arData['level'] = $USER->GetLevel('wave');
	if ($arData['level'] > KS_ACCESS_WAVE_VIEW) throw new CAccessError("WAVE_ACCESS_VIEW", 403);
	if($params['hash']=='') throw new CDataError("WAVE_HASH_REQUIRED");
	$obPosts=new CWavePosts();
	$arPost=array();
	//Получаем список сообщений
	$arFilter=array(
		'hash'=>$params['hash'],
	);
	if($arData['level']>KS_ACCESS_WAVE_MODERATE) 
		$arFilter['active']=1;
	if(is_array($params['filter']))
		$arFilter=array_merge($arFilter,$params['filter']);
	if($arData['level']>KS_ACCESS_WAVE_MODERATE)
	{
		$iCount=$obPosts->Count($arFilter);
	}
	else
	{
		$arCount=$obPosts->Count($arFilter,'active');
		$iCount=$arCount[1];
		$iNonActive=$arCount[0];
	}
	$subsmarty->assign('count',$iCount);
	$subsmarty->assign('new',$iNonActive);
	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
	$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/wave/WaveCount',$params['global_template'],$params['tpl']);
	return $sResult;		
}

function widget_params_WaveCount()
{
	$arFields=array(
	);
	return array(
		'fields'=>$arFields,
	);
}
?>