<?php
/**
 * Файл обеспечивает функции обновления системы
 *
 * @since 08.09.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */

if (!defined('KS_ENGINE')) die("Hacking attempt!");
//Операции по обновлению системы

class CUpdate extends CBaseObject
{
	private $sUpdatesPath;
	private $sBackupPath;

	function __construct()
	{
		$this->sUpdatesPath='/main/updates/setup/cms/';
		$this->sBackupPath='/main/updates/restore/';
	}

	/**
	 * Метод выполняет шаг обновления
	 */
	function Go()
	{
		if(!file_exists(MODULES_DIR.$this->sUpdatesPath)) return false;
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
				$cutNew=strlen(MODULES_DIR.$this->sUpdatesPath);
				foreach($arFiles as $id=>$file)
				{
					$filename=substr($file,$cutNew);
					$to=MODULES_DIR.$this->sBackupPath.$filename;
					$path=dirname($to);
					if(!file_exists($path)) @mkdir($path,0755,true);
					if(file_exists(ROOT_DIR.'/'.$filename))
						//Делаем бэкап если файл существует
						if(!@copy(ROOT_DIR.'/'.$filename,$to))
							throw new Exception('COPY ERROR: '.ROOT_DIR.'/'.$filename.' '.$to);
					if(!@copy($file,ROOT_DIR.'/'.$filename))
						throw new Exception('COPY NEW ERROR: '.$file.' '.ROOT_DIR.'/'.$filename);
					unset($_SESSION['update']['files'][$id]);
					$_SESSION['update']['doneFiles']++;
					if($begin+5<=time()) break;
				}
				if($_SESSION['update']['doneFiles']==$_SESSION['update']['totalFiles'])
				{
					$_SESSION['update']['files']=array();
					//Очищаем архив файлов
					$origipath = MODULES_DIR.$this->sUpdatesPath;
					$path=$origipath;
					$handler = @opendir($path);
					if(!$handler) throw new Exception('Delete folder error');
					while (true)
					{
						$item = readdir($handler);
						if ($item == "." or $item == "..") continue;
						elseif (gettype($item) == "boolean")
						{
							closedir($handler);
							if($path!=$origipath)
								if (!@rmdir($path)) throw new Exception('Delete folder error'); else break;
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
							unlink($path."/".$item);
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
							<?php foreach($arResult as $key=>$value):?>
							<tr><td><?php echo $key;?></td><td><?php echo $value;?></td></tr>
							<?php endforeach;?>
						</table>
					</body>
				</html>
				<?php
				die();
			}
		}
	}

	/**
	 * Метод выполняет шаг отаката
	 */
	function Restore()
	{
		if(!file_exists(MODULES_DIR.$this->sBackupPath)) return false;
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
			<?php
			//Очищаем архив файлов
			$origipath = MODULES_DIR.$this->sBackupPath;
			$path=$origipath;
			$handler = @opendir($path);
			if($handler)
			{
				$cutNew=strlen(MODULES_DIR.$this->sBackupPath);
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
				<?php
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
				<?php
				die();
			}
		}
	}
}