<?php
/**
 * @file update.php
 * Страница работы системы обновления
 * Файл проекта kolos-cms.
 * 
 * Создан 18.02.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4
 * @todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CHTTPInterface.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';
global $arVersion,$smarty,$KS_FS;
$page='_update';
$smarty->config_load('admin.conf','main_update');
try
{
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
		if($_POST['ACTION']=='check')
		{
			$obHTTP=new CHTTPInterface($KS_MODULES->GetConfigVar('main','update_server','updateserver.kolos').'/update.php');
			$obHTTP->SetData('pkey',$KS_MODULES->GetConfigVar('main','pkey',''));
			$obHTTP->SetData('build',$arVersion['ID'].'-'.$arVersion['BUILD']);
			if($arResult=$obHTTP->Send())
			{
				$arAnswer=json_decode($arResult['body'],true);
				$obConfig=new CConfigParser('main');
				$ks_config=$obConfig->LoadConfig();
				$obConfig->Set('last_update_check',time());
				$obConfig->WriteConfig();
				if(count($arAnswer['ERRORS'])==0)
				{
					if(count($arAnswer['UPDATES'])>0)
					{
						$smarty->assign('updates',$arAnswer['UPDATES']);
					}
				}
				else
				{
					$sErrorText='';
					foreach($arAnswer['ERRORS'] as $arError)
					{
						$sErrorText.=$arError['ru'].'<br/>';
					}
					throw new CError('SYSTEM_UPDATE_ERROR',231,$sErrorText);
				}
			}
			else
			{
				throw new CError('SYSTEM_UPDATE_ERROR');
			}
		}
		elseif($_POST['ACTION']=='update')
		{
			$obHTTP=new CHTTPInterface($KS_MODULES->GetConfigVar('main','update_server','updateserver.kolos').'/update.php');
			$obHTTP->SetData('pkey',$KS_MODULES->GetConfigVar('main','pkey',''));
			$obHTTP->SetData('build',$arVersion['ID'].'-'.$arVersion['BUILD']);
			$obHTTP->SetData('update',$_POST['build']);
			$_SESSION['update']['download']=array();
			if(!file_exists(MODULES_DIR.'/main/updates/download/')) $KS_FS->makedir(MODULES_DIR.'/main/updates/download/');
			if($arResult=$obHTTP->Send())
			{
				$arAnswer=json_decode($arResult['body'],true);
				if(count($arAnswer['ERRORS'])==0)
				{
					if(count($arAnswer['FILES'])>0)
					{
						foreach($arAnswer['FILES'] as $arFile)
						{
							$arFile['downloaded']=0;
							$_SESSION['update']['download'][]=$arFile;
							if(file_exists(MODULES_DIR.'/main/updates/download/'.$arFile['filename']))
							{
								unlink(MODULES_DIR.'/main/updates/download/'.$arFile['filename']);
							}
						}
						$page='_update_download';
					}
				}
				else
				{
					$sErrorText='';
					foreach($arAnswer['ERRORS'] as $arError)
					{
						$sErrorText.=$arError['ru'].'<br/>';
					}
					throw new CError('SYSTEM_UPDATE_ERROR',231,$sErrorText);
				}
			}
			else
			{
				throw new CError('SYSTEM_UPDATE_ERROR');
			}
		}
		elseif($_POST['ACTION']=='download')
		{
			$_SESSION['update']['setup']=0;
			$arResult=array();
			try
			{
				//Выкачивание файлов обновлений
				if(is_array($_SESSION['update']['download'])&&count($_SESSION['update']['download'])>0)
				{
					foreach($_SESSION['update']['download'] as $id=>$arFile)
					{
						$sFilename=MODULES_DIR.'/main/updates/download/'.$arFile['filename'];
						if($arFile['size']>$arFile['downloaded'])
						{
							$obHTTP=new CHTTPInterface($arFile['url']);
							$size=$obHTTP->Download($sFilename,$arFile['downloaded'],5);
							if($size>0)
							{
								$_SESSION['update']['download'][$id]['downloaded']=$size;
								$arResult['downloads'][$arFile['filename']]=array(
									'done'=>$size,
									'size'=>$arFile['size'],
									'name'=>$arFile['filename']
								);
							}
							else
							{
								throw new CError('SYSTEM_UPDATE_DOWNLOAD_ERROR');
							}
						}
						if($arFile['size']==$arFile['downloaded'])
						{
							if($arFile['md5']!=md5_file($sFilename))
							{
								throw new CError('SYSTEM_UPDATE_FILE_CHECKSUM_ERROR');
							}
						}
						if($arFile['downloaded']>$arFile['size'])
						{
							throw new CError('SYSTEM_UPDATE_DOWNLOAD_ERROR');
						}
					}
					$arResult['status']=$smarty->get_config_vars('status_downloading')!=''?$smarty->get_config_vars('status_downloading'):'status_downloading';
				}
				else
				{
					throw new CError('SYSTEM_UPDATE_NOTHING_DOWNLOAD');	
				}
			}
			catch(CDataError $e)
			{
				$arResult['warning']=$e->__toString();
			}
			catch(CError $e)
			{
				$arResult['error']=$e->__toString();
			}
			//Кодируем данные и умираем
			echo json_encode($arResult);
			die();
		}
		elseif($_POST['ACTION']=='setup')
		{
			//Это вывод страницы установки обновления
			if($_SESSION['update']['setup']==0)
			{
				$_SESSION['update']['setup']=1;
				$_SESSION['update']['build']=$arVersion['ID'].'-'.$arVersion['BUILD'];
				$page='_update_setup';
			}
			elseif($_SESSION['update']['setup']==1)
			{
				if(!file_exists(MODULES_DIR.'/main/updates/setup/')) $KS_FS->makedir(MODULES_DIR.'/main/updates/setup/');
				try
				{
					$_SESSION['update']['setup']=1;
					$begin=time();
					$arFiles=$_SESSION['update']['download'];
					foreach($arFiles as $id=>$arFile)
					{
						$sFilename=MODULES_DIR.'/main/updates/download/'.$arFile['filename'];
						if($arFile['md5']!=md5_file($sFilename))
						{
							throw new CError('SYSTEM_UPDATE_FILE_CHECKSUM_ERROR');
						}
						$query='tar -xzf '.$sFilename.' -C '.MODULES_DIR.'/main/updates/setup';
						shell_exec($query);
						unset($_SESSION['update']['download'][$id]);
						if($begin+5<time())
						{
							break;
						}
					}
					if(count($_SESSION['update']['download'])==0)
					{	
						$arResult['step']='precopy';
						$arResult['status']=$smarty->get_config_vars('status_copy')!=''?$smarty->get_config_vars('status_copy'):'status_copy';
						$_SESSION['update']['setup']=2;
					}
				}
				catch(CError $e)
				{
					$arResult['error']=$e->__toString();	
				}
				echo json_encode($arResult);
				die();
			}
			elseif($_SESSION['update']['setup']==2)
			{
				//Проверям список полученных файлов
				try
				{
					//Проверяем наличие файла предварительной установки
					if(file_exists(MODULES_DIR.'/main_updates/setup/scripts/before.php'))
					{
						$arResult['step']='before';
						$arResult['before']=$smarty->get_config_vars('status_before')!=''?$smarty->get_config_vars('status_before'):'status_before';
					}
					$fileCount=CSimpleFs::CountDirFiles(MODULES_DIR.'/main/updates/setup/cms');
					if($fileCount>0)
					{
						$_SESSION['update']['totalFiles']=$fileCount;
						$_SESSION['update']['setup']=3;
						$_SESSION['update']['doneFiles']=0;
						$_SESSION['update']['files']=CSimpleFs::GetDirList(MODULES_DIR.'/main/updates/setup/cms');
						$arResult['totalFiles']=$fileCount;
						$arResult['done']=0;
						$arResult['step']='copy';
					}
					else
					{
						throw new CError('SYSTEM_UPDATE_NO_FILES');
					}
				}
				catch(CError $e)
				{
					$arResult['error']=$e->__toString();
				}
				echo json_encode($arResult);
				die();
			}
		}
		elseif($_POST['ACTION']=='before')
		{
			try
			{
				if(file_exists(MODULES_DIR.'/main/updates/setup/scripts/before.php'))
				{
					$arResult=array();
					$arResult['step']='copy';
					$arResult['totalFiles']=$_SESSION['update']['totalFiles'];
					$arResult['done']=0;
					include(MODULES_DIR.'/main/updates/setup/scripts/before.php');
				}
			}
			catch(CError $e)
			{
				$arResult['error']=$e->__toString();
			}
			echo json_encode($arResult);
			die();
		}
		elseif($_POST['ACTION']=='after')
		{
			try
			{
				$arResult=array();
				$arResult['step']='done';
				$arResult['ok']='1';
				if(file_exists(MODULES_DIR.'/main/updates/setup/scripts/after.php'))
				{
					include(MODULES_DIR.'/main/updates/setup/scripts/after.php');
				}
				if($arResult['ok']=='1' && $arResult['step']=='done')
				{
					$this->RecountDBStructure();
					$this->RecountTextStructure();
					$obConfig=new CConfigParser('main');
					$obConfig->LoadConfig();
					$obConfig->Set('update_db',1);
					$obConfig->WriteConfig();
					$obHTTP=new CHTTPInterface($KS_MODULES->GetConfigVar('main','update_server','updateserver.kolos').'/update.php');
					$obHTTP->SetData('pkey',$KS_MODULES->GetConfigVar('main','pkey',''));
					$obHTTP->SetData('build',$_SESSION['update']['build']);
					$obHTTP->SetData('newbuild',$arVersion['ID'].'-'.$arVersion['BUILD']);
					$obHTTP->Send();
					global $KS_FS;
					$KS_FS->remdir(MODULES_DIR.'/main/updates/restore/');
					$KS_FS->cleardir(MODULES_DIR.'/main/updates/download/');
					$KS_FS->cleardir(MODULES_DIR.'/main/updates/setup/');
				}
			}
			catch(CError $e)
			{
				$arResult['error']=$e->__toString();
			}
			echo json_encode($arResult);
			die();
		}
	}
}
catch(CError $e)
{
	$page='_update';
	$smarty->assign('last_error',$e);
}
$arData['last_update_check']=$KS_MODULES->GetConfigVar('main','last_update_check');
$smarty->assign('data',$arData);
?>
