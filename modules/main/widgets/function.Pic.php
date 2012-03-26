<?php
/**
 * \file function.PicResize.php
 * Виджет для изменения размеров картинки
 * Файл проекта Cvetok-info.
 *
 * Создан 23.10.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
function smarty_function_Pic($params, &$subsmarty)
{
	global $KS_MODULES,$KS_FS;
	if($params['src']=='') return '';
	if(isset($params['only_url'])) $params['only_url']=$params['only_url']?true:false;
	$sSizeFile='';
	if($params['width']!='') $sSizeFile.=intval($params['width']);
	$sSizeFile.='x';
	if($params['height']!='') $sSizeFile.=intval($params['height']);
	$cacheDir=ROOT_DIR.'/uploads/PicCache'.$params['src'].'/';
	$cacheFile='/uploads/PicCache'.$params['src'].'/'.$sSizeFile.'.png';
	if(file_exists(ROOT_DIR.$cacheFile)&&is_file(ROOT_DIR.$cacheFile))
		$res=$cacheFile;
	else
	{
		//Такой файл не был закеширован, значит надо его создавать
		if(file_exists(ROOT_DIR.$params['src']) && is_file(ROOT_DIR.$params['src']))
		{
			include_once(MODULES_DIR.'/main/libs/class.CImageResizer.php');
			try
			{
				$obImage=new CImageResizer(ROOT_DIR.$params['src']);
				$obMode=new CScale(intval($params['width']),intval($params['height']));
				if($params['mode']=='stretch')
					$obMode=new CRectGenerator(intval($params['width']),intval($params['height']));
				elseif($params['mode']=='crop')
					$obMode=new CCropToCenter(intval($params['width']),intval($params['height']));
				elseif($params['mode']=='croptop')
					$obMode=new CCropToTop(intval($params['width']),intval($params['height']));
				if($obImage->Resize($obMode))
				{
					if(!file_exists($cacheDir)) $KS_FS->makedir($cacheDir);
					if($obImage->SavePNG(ROOT_DIR.$cacheFile))
					{
						if(file_exists(ROOT_DIR.$cacheFile))
						{
							chmod(ROOT_DIR.$cacheFile,0655);
							$res=$cacheFile;
						}
						else
							throw new CError('SYSTEM_NOT_FOUND_AFTER_SAVE');
					}
					else
						throw new CError('SYSTEM_CANT_SAVE');
				}
				else
					throw new CError('SYSTEM_CANT_RESIZE');
			}
			catch (CError $e)
			{
				if($e->getMessage()=='SYSTEM_WRONG_FILE')
					$res=$params['src'];
				else
					return $e->__toString();
			}
		}
		elseif($params['default']!='')
			$res=$params['default'];
		else
			throw new CError('SYSTEM_FILE_NOT_FOUND',0,$params['src']);
	}
	if($res!='' && !$params['only_url'])
	{
		$res='<img src="'.$res.'"';
		foreach($params as $key=>$value)
		{
			if($params['keepSmall']=='Y' && ($key=='width' || $key=='height')) continue;
			if($key!='mode'&&$key!='default')
				$res.=' '.$key.'="'.$value.'"';
		}
		$res.='/>';
	}
	return $res;
}
