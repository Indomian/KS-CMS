<?php
/**
 * @file index.php
 * Основной файл системы KS-CMS выполняет базовые операции по подключению главного модуля,
 * обработке навигации и т.д.
 * Также в этом файле задаются основые константы системы
 * @author Dotj <a.kolos@kolosstudio.ru>, BlaDe39 <blade39@kolosstudio.ru>
 * @since v1.0
 * @version 2.5.4-14
*/
$begin=microtime(1);

define('KS_ENGINE',		true);
define('ROOT_DIR',		dirname (__FILE__));
define('MODULES_DIR',	ROOT_DIR.'/modules');
define('CONFIG_DIR',	ROOT_DIR.'/cnf');
define('JS_DIR', '/js');
define('SITE_UPLOADS_DIR', '/uploads');
define('UPLOADS_DIR', 	ROOT_DIR.SITE_UPLOADS_DIR);
define('SITE_TEMPLATES_DIR', '/uploads/templates');
define('TEMPLATES_DIR',	ROOT_DIR.SITE_TEMPLATES_DIR);
define('SYS_TEMPLATES_DIR',ROOT_DIR.'/templates');
define('EVENT_TEMPLATES_DIR', ROOT_DIR.'/templates/admin/eventTemplates');
define('IS_ADMIN',		false);

try
{
	/* инициализация */
	try
	{
		/**
		 * Входные данные
		 * $_GET['path'] -- полный вызываемый путь вида: dir1/dir2/file.html
		 * $_GET['<другие перменные>']
		 */

		/**
		 * Структура массива $KS_IND_matches
		 * 0 - полный путь переданный пользователем
		 * 1 - массив директорий со слешем на конце ОБЯЗТЕЛЬНО (по-другому не отработает)
		 * 2 - имя файла (может отсутствовать) (с расширением)
		 * 3 - имя файла (может отсутствовать) (без расширения)
		 */
		$KS_IND_matches[0]=$_GET['path'];
		$KS_IND_matches[1]=explode('/',$_GET['path']);
		$KS_IND_matches[2]=array_pop($KS_IND_matches[1]);
		$KS_IND_matches[3]=substr($KS_IND_matches[2],0,strrpos($KS_IND_matches[2],'.'));

		if( isset($KS_IND_matches[1]) )
		{
			/* обратились к какой-либо директории относително корня */
			/**
			 * Массив $KS_IND_dir - список папок для получения адреса материала
			 * 0 - первая папка (по ней идентифицируем модуль)
			 * <$KS_IND_dir[<n>]> - последующие директории
			 */
			$KS_IND_dir = $KS_IND_matches[1];
		}
		require_once MODULES_DIR.'/main/main.inc.php';
	}
	catch(CUserError $e)
	{
		$USER->userdata["LAST_ERROR"] = $e;
		$smarty->assign('last_error',$e->getMessage());
	}

	try
	{
		if(array_key_exists('type',$_REQUEST) && $_REQUEST['type']=='AJAX')
		{
			$KS_IND_dir=$_REQUEST['module'];
			if(array_key_exists('gtpl',$_REQUEST) && IsTextIdent($_REQUEST['gtpl']))
			{
				$global_template=$_REQUEST['gtpl'];
			}
			else
			{
				$global_template='.default';
			}
			$smarty->assign("glb_tpl", $global_template);
			if($KS_MODULES->IsActive($KS_IND_dir))
			{
				$output['main_content']=$KS_MODULES->IncludeWidget($KS_IND_dir,$_REQUEST['action'],$_REQUEST);
			}
			$output['include_global_template']='0';
		}
		else
		{
			$global_template =  $KS_MODULES->GetTemplate();
			$smarty->assign("glb_tpl", $global_template);
			$output = $KS_MODULES->hook_up($KS_MODULES->GetPathPart());
		}
	}
	catch(CAccessError $e)
	{
		header('HTTP/1.0 403 Forbidden');
		$smarty->assign('error', $e->__toString());
		if($smarty->template_exists($global_template.'/403.tpl'))
		{
			$output['include_global_template']=0;
			$output['main_content']=$smarty->fetch($global_template.'/403.tpl');
		}
	}
	catch(CHTTPError $e)
	{
		header('HTTP/1.0 404 Not found');
		$smarty->assign('error', $e->__toString());
		if($smarty->template_exists($global_template.'/404.tpl'))
		{
			$output['include_global_template']=0;
			$output['main_content']=$smarty->fetch($global_template.'/404.tpl');
		}
	}
	catch(CError $e)
	{
		$output['main_content']=$e;
	}

	if(array_key_exists('include_global_template',$output) && $output['include_global_template'] != '0')
	{
		$smarty->assign('output', $output);
		$page = $smarty->fetch($global_template.'/'.$KS_MODULES->GetScheme().'.tpl');
	}
	else
	{
		$page = $output['main_content'];
	}
	$page=str_replace('#HEAD_STRINGS#',$KS_MODULES->GetHeader(),$page);//join("\n",$KS_MODULES->arHeads),$page);
	echo $page;

	/*Вроде все отработали, можно опробовать систему сообщений
	 * Теперь отсылка писем выполняется по завершении обработки страницы*/
	$obEvents->init();
	$obEvents->Run();
	$obEvents->Done();
	$end=microtime(1);
	if(KS_RELEASE!=1)
	{
		if(array_key_exists('include_global_template',$output) && $output['include_global_template'] != '0' && KS_DEBUG==1)
		{
			$sys_info['gen_tyme'] = ($end-$begin);
			$sys_info['sql_gen_tyme'] = $ks_db->MySQL_time_taken;
			$sys_info['sql_queries_quant'] = $ks_db->query_num;
			$sys_info['sql_requests']=$ks_db->GetRequests();
			$smarty->assign('sys_info', $sys_info);
			$smarty->display('.default/.sysfooter.tpl');
		}
	}
}
catch (CError $e)
{
	echo "<html><head></head><body>$e<br/><a href=\"/\">Вернуться на главную</a></body></html>";
}
catch(Exception $e)
{
	if($_SERVER['HTTP_HOST']!='kolos')
	{
		@mail('blade39@kolosstudio.ru','Ошибка исполнения на сайте:'.$_SERVER['HTTP_HOST'],$e);
		header('HTTP/1.1 503 Service Unavailable');
		?><html>
		<head>
			<title>Критическая ошибка</title>
		</head>
		<body align="center">
		<h1>Ошибка</h1>
		<p>В процессе работы произошла критическая ошибка, отчет был выслан в службу технической поддержки.
		В ближайшее время работа сайт будет восстановлена.<br>
		</p>
		<p>Спасибо за ваше терпение.</p>
		<?php echo $e;?>
		</body>
		</html><?php
	}
	else
	{
		echo $e;
	}
}
?>