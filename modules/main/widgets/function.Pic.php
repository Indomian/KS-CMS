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
	$sSizeFile='';
	if($params['width']!='') $sSizeFile.=intval($params['width']);
	$sSizeFile.='x';
	if($params['height']!='') $sSizeFile.=intval($params['height']);
	$cacheDir=ROOT_DIR.'/uploads/PicCache'.$params['src'].'/';
	$cacheFile='/uploads/PicCache'.$params['src'].'/'.$sSizeFile.'.jpeg';
	$attributes=array(
		'src',
		'mode',
		'default',
		'lifetime',
	);
	/**
	 * @todo Убрать эту хрень к чертям собачьим
	 */
	/*if(!isset($params['cache_time'])||intval($params['cache_time'])<=0)
	{
		$obConfig=new CConfigParser('main');
		$ks_config=$obConfig->LoadConfig();
		$params['lifetime'] = $ks_config['lifetime'];
	}*/
	try
	{
		if(file_exists(ROOT_DIR.$cacheFile))// && (filectime(ROOT_DIR.$cacheFile)+$params['lifetime'] >= time()))
		{
			$res='<img src="'.$cacheFile.'"';
			foreach($params as $key=>$value)
			{
				if(!in_array($key,$attributes))
				{
					$res.=' '.$key.'="'.$value.'"';
				}
			}
			$res.='/>';
			return $res;
		}
		else
		{
			//Такой файл не был закеширован, значит надо его создавать
			if(file_exists(ROOT_DIR.$params['src']))
			{
				include_once(MODULES_DIR.'/main/libs/class.ImageResizer.php');
				$obImage=new ImageResizer($params['src']);
				$obImage->isCreateDir=false;
				$obImage->isSave=false;
				$bKeepRatio=false;
				$bKeepRatioWb=true;
				if($params['mode']=='stretch')
				{
					$bKeepRatio=false;
					$bKeepRatioWb=false;
				}
				elseif($params['mode']=='crop')
				{
					$bKeepRatio=true;
					$bKeepRatioWb=true;
				}
				elseif($params['mode']=='resize')
				{
					$bKeepRatio=true;
					$bKeepRatioWb=false;
				}
				$obImage->Resize(intval($params['width']),intval($params['height']),$bKeepRatio,$bKeepRatioWb,false);
				if(!file_exists($cacheDir))
				{
					$KS_FS->makedir($cacheDir);
				}
				if(!$obImage->Save(ROOT_DIR.$cacheFile))
				{
					throw new CError('SYSTEM_FILE_NOT_FOUND_OR_NOT_WRITABLE',$cacheFile);
				}
				chmod(ROOT_DIR.$cacheFile,0655);
				$res='<img src="'.$cacheFile.'"';
				foreach($params as $key=>$value)
				{
					if(!in_array($key,$attributes))
					{
						$res.=' '.$key.'="'.$value.'"';
					}
				}
				$res.='/>';
				return $res;
			}
			elseif($params['default']!='')
			{
				$res='<img src="'.$params['default'].'"';
				foreach($params as $key=>$value)
				{
					if(!in_array($key,$attributes))
					{
						$res.=' '.$key.'="'.$value.'"';
					}
				}
				$res.='/>';
				return $res;
			}
			throw new CError('SYSTEM_FILE_NOT_FOUND',0,$params['src']);
		}
	}
	catch(CError $e)
	{
		return $e->__toString();
	}
}

function widget_params_Pic($params)
{

}
?>
