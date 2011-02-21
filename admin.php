<?php

/**
 * Главный административный файл ЦМС
 *
 * Производит логин пользователя и восстановление забытого пароля
 *
 * @filesource admin.php
 * @author blade39 <blade39@kolosstudio.ru>, Ilya Doroshko <ilya@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 *
 * @version 1.0
 * @since 25.03.2009
 *
 * 1. Переход к поддержке разнообразных кодировок и языковых файлов.
 * 2. Установка кодировки UTF-8 базовой кодировкой.
 *
 * @version 1.1
 * @since 11.05.2009
 *
 * Исправлены ошибки при восстановлении пароля
 *
 * @version 2.5
 * Добавлена поддержка текстовых констант из файла version.php
 *
 * @version 2.5.4
 * Добавлена поддержка команды update, и команды restore
 * для выполнения операций связанных с работой системы обновления
 * и восстановления системы в случае провала обновления
 *
 * @version 2.5.5
 * @since 17.03.2010
 * @author blade39 <blade39@kolosstudio.ru>
 * Добавлен поиск шаблона, в случае если не обнаружен шаблон с именем модуля
 * и именем страницы, шаблон ищется в списке стандартных шаблонов
 */

// Получаем время запуска скрипта
$begin=microtime(1);

//Принудительно выключаем поддержку magic_quotes_gpc
ini_set('magic_quotes_gpc','0');

define('KS_ENGINE',		true); 						//< Установка константы движка ЦМС.
define('ROOT_DIR',		dirname (__FILE__));		//< Получения корневого каталога ЦМС.
define('MODULES_DIR',	ROOT_DIR . '/modules');		//< Установка папки для модулей.
define('CONFIG_DIR',	ROOT_DIR . '/cnf');			//< Установка папки для хранения конфигурационных файлов.
define('SYS_TEMPLATES_DIR',	ROOT_DIR . '/templates'); 	//< Папка для хранения шаблонов.
define('JS_DIR', '/js');
define('UPLOADS_DIR', 	ROOT_DIR.'/uploads');
define('TEMPLATES_DIR',	UPLOADS_DIR.'/templates');
define('EVENT_TEMPLATES_DIR', ROOT_DIR.'/templates/admin/eventTemplates');
define('IS_ADMIN',		true);						//< Флаг указывающий на то что работает административный раздел.

//Устанавливаем заголовок о том что отдаем все в UTF-8
header('Content-Type: text/html; charset=UTF-8');
setlocale(LC_ALL, "ru_RU.UTF-8");

if(array_key_exists('update',$_GET))
{
	//Операции по обновлению системы
	if(file_exists(MODULES_DIR.'/main/updates/setup/cms/'))
	{
		//Если нашли файл обновлений
		session_start();
		if($_SESSION['update']['setup']==3)
		{
			//Шаг номер 3
			try
			{
				$begin=time();
				$arFiles=$_SESSION['update']['files'];
				$cut=strlen(ROOT_DIR);
				$cutNew=strlen(MODULES_DIR.'/main/updates/setup/cms/');
				foreach($arFiles as $id=>$file)
				{
					$filename=substr($file,$cutNew);
					$to=MODULES_DIR.'/main/updates/restore/'.$filename;
					$path=dirname($to);
					if(!file_exists($path))
					{
						@mkdir($path,0755,true);
					}
					if(file_exists(ROOT_DIR.'/'.$filename))
					{
						//Делаем бэкап если файл существует
						if(!@copy(ROOT_DIR.'/'.$filename,$to))
						{
							throw new Exception('COPY ERROR: '.ROOT_DIR.'/'.$filename.' '.$to);
						}
					}
					if(!@copy($file,ROOT_DIR.'/'.$filename))
					{
						throw new Exception('COPY NEW ERROR: '.$file.' '.ROOT_DIR.'/'.$filename);
					}
					unset($_SESSION['update']['files'][$id]);
					$_SESSION['update']['doneFiles']++;
					if($begin+5<=time())
					{
						break;
					}
				}
				if($_SESSION['update']['doneFiles']==$_SESSION['update']['totalFiles'])
				{
					$_SESSION['update']['files']=array();
					//Очищаем архив файлов
					$origipath = MODULES_DIR.'/main/updates/setup/cms/';
					$path=$origipath;
			    	$handler = @opendir($path);
			    	if(!$handler) throw new Exception('Delete folder error');
			    	while (true)
			    	{
			        	$item = readdir($handler);
			        	if ($item == "." or $item == "..")
			        	{
			            	continue;
			        	}
			        	elseif (gettype($item) == "boolean")
			        	{
			            	closedir($handler);
			            	if($path!=$origipath)
			            	{
			            		if (!@rmdir($path))
			            		{
				                	return false;
			    	        	}
			            	}
			            	else
			            	{
			            		break;
			            	}
			            	$path = substr($path, 0, strrpos($path, "/"));
			            	$handler = opendir($path);
			        	}
			        	elseif (is_dir($path."/".$item))
			        	{
			            	closedir($handler);
			            	$path = $path."/".$item;
			            	$handler = opendir($path);
			        	}
			        	else
			        	{
			        	    unlink($path."/".$item);
			        	}
			    	}
				}
				$arResult['done']=$_SESSION['update']['doneFiles'];
				$arResult['total']=$_SESSION['update']['totalFiles'];
			}
			catch(Exception $e)
			{
				$arResult['error']=$e->getMessage();
			}
			if($_GET['mode']=='ajax')
			{
				echo json_encode($arResult);
				die();
			}
			else
			{
				?>
				<html>
					<head>
						<title>Система обновления цмс</title>
					</head>
					<body>
						<table>
							<?foreach($arResult as $key=>$value):?>
							<tr><td><?=$key?></td><td><?=$value?></td></tr>
							<?endforeach?>
						</table>
					</body>
				</html>
				<?
				die();
			}
		}
	}
}
elseif(array_key_exists('restore',$_GET))
{
	if(file_exists(MODULES_DIR.'/main/updates/restore/'))
	{
		if(array_key_exists('key',$_GET))
		{
			//если есть ключ - начинаем восстановление
			?><html>
					<head>
						<title>Система восстановления сайта</title>
					</head>
					<body>
						<p>Выполняется восстановление предыдущей версии системы.
						Подождите 5 минут.</p>
					</body>
				</html>
			<?
			//Очищаем архив файлов
			$origipath = MODULES_DIR.'/main/updates/restore/';
			$path=$origipath;
	    	$handler = @opendir($path);
	    	if($handler)
	    	{
	    		$cutNew=strlen(MODULES_DIR.'/main/updates/restore/');
	    		while (true)
	    		{
		        	$item = readdir($handler);
		        	if ($item == "." or $item == "..")
	    	    	{
	        	    	continue;
	        		}
	        		elseif (gettype($item) == "boolean")
	        		{
	            		closedir($handler);
	            		if($path!=$origipath)
	            		{
	            			if (!@rmdir($path))
	            			{
			                	throw new Exception('DELETE ERROR: '.$path);
		    	        	}
	    	        	}
	        	    	else
	            		{
	            			break;
	            		}
	            		$path = substr($path, 0, strrpos($path, "/"));
	            		$handler = opendir($path);
	        		}
	        		elseif (is_dir($path."/".$item))
	        		{
		            	closedir($handler);
		            	$path = $path."/".$item;
	    	        	$handler = opendir($path);
	        		}
		        	else
		        	{
						$filename=substr($path."/".$item,$cutNew);
	    	    	    if(!@copy($path."/".$item,ROOT_DIR.'/'.$filename))
						{
							throw new Exception('RESTORE FILE ERROR: '.$path."/".$item.' '.ROOT_DIR.'/'.$filename);
						}
						unlink($path."/".$item);
	        		}
	    		}
	    	}
	    	@rmdir($origipath);
	    	die();
		}
		else
		{
			//ключа нету..шлем админу письмо о восстановлении
			include CONFIG_DIR.'/sys_config.php';
			if($ks_config['admin_email']!='')
			{
				mail($ks_config['admin_email'],'Site restore email',
					'You site restore system has been initialized, but no key were entered.' .
					'If you tryed to restore your site please use following url string' .
					'parameters to restore you site:'."\r\n" .
					"?restore=1&key=".$ks_config['pkey']."\r\n".
					'======================================='."\r\n");
				?>
				<html>
					<head>
						<title>Система восстановления сайта</title>
					</head>
					<body>
						<p>На почтовый адрес администратора сайта выслано уведомление о порядке
						восстановления системы.</p>
					</body>
				</html>
				<?
				die();
			}
			else
			{
				?>
				<html>
					<head>
						<title>Система восстановления сайта</title>
					</head>
					<body>
						<p>Невозможно отправить сообщение администратору. Не установлен почтовый адрес
						администратора.</p>
					</body>
				</html>
				<?
				die();
			}
		}
	}
}
if (file_exists(MODULES_DIR.'/main'))
{
	/*! инициализация констант, БД, смарти, поиск главного модуля. */
	//Подгрузка версии системы
	include CONFIG_DIR.'/version.php';
 	$KS_VERSION=$arVersion;
	try
	{
		//! Подключение главного модуля ЦМС, определение пользователя и др.
		include_once(MODULES_DIR.'/main/admin.inc.php');
		$smarty->assign('VERSION',$KS_VERSION);
	}
	catch(CUserError $e)
	{
		if(intval($_GET['ajax'])>0)
		{
			echo json_encode(array('error'=>$e->getMessage(),'text'=>$e->GetErrorText()));
			exit();
		}
		else
		{
			$smarty->assign('VERSION',$KS_VERSION);
			$smarty->assign('last_error',$e->GetErrorText());
			if($_GET['lostpwd'] == 'Y')
			{
				$page=$KS_MODULES->LoadModulePage('main','password');
			}
			$smarty->display('admin/login.tpl');
			exit();
		}
	}
	catch(CAccessError $e)
	{
		if($smarty)
		{
			$smarty->assign('isajax',intval($_GET['ajax']));
			$smarty->assign('VERSION',$KS_VERSION);
			$smarty->assign('last_error',$e->GetErrorText());
			if($_GET['lostpwd'] == 'Y')
			{
				$page=$KS_MODULES->LoadModulePage('main','password');
			}
			$smarty->display('admin/login.tpl');
			exit();
		}
		else
		{
			echo "<html><title>{$KS_VERSION['TITLE']} {$KS_VERSION['ID']} Init Error</title><body>$e</body></html>";
			die();
		}
	}
	catch(CError $e)
	{
		if($smarty)
		{
			$smarty->assign('VERSION',$KS_VERSION);
			$smarty->assign('last_error',$e->GetErrorText());
		}
		else
		{
			echo "<html><title>{$KS_VERSION['TITLE']} {$KS_VERSION['ID']} Init Error</title><body>$e</body></html>";
			die();
		}
	}

	/* Пользователь не вошел на сайт */
	if (!$USER->IsLogin())
	{
		if($_GET['lostpwd'] == 'Y')
		{
			$page=$KS_MODULES->LoadModulePage('main','password');
		}
		else
		{
			$smarty->assign('VERSION',$KS_VERSION);
			$smarty->assign('isajax',intval($_GET['ajax']));
			$smarty->assign('backurl',$_SERVER['REQUEST_URI']);
			//Отображаем окно входа и заканчиваем.
			$smarty->display('admin/login.tpl');
			exit();
		}
	}

	//Проверяем наличие модуля справки, если он установлен, вызываем генерацию ссылки помощи
	if($KS_MODULES->IsModule('help'))
	{
		include_once(MODULES_DIR.'/help/pages/getHelpUrl.php');
	}
	// Выводим текущий модуль (определяется по параметрам в УРЛ).
	$KS_MODULES->AdminShowModule($KS_MODULES->current);
	$KS_MODULES->Draw($smarty);
}
else
{
	echo "Система KS-CMS не установлена! (KS-CMS system is not setup)";
	die();
}
?>
