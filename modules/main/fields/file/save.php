<?php
$max_size=ini_get('upload_max_filesize');
if(array_key_exists($prefix.'ext_'.$arField['title'].'_del',$_POST) && $_POST[$prefix.'ext_'.$arField['title'].'_del']==1)
{
	if(array_key_exists($prefix.'ext_'.$arField['title'].'_path',$_POST) && $_POST[$prefix.'ext_'.$arField['title'].'_path']!='')
	{
		$path=str_replace('/../','/',$_POST[$prefix.'ext_'.$arField['title'].'_path']);
		if(file_exists(UPLOADS_DIR.$path))
		{
			@unlink(UPLOADS_DIR.$path);
		}
	}
	$sValue='clear';
}
elseif($value['error']==0 && is_array($value))
{
	if($value['size']>$arField['option_1'] && $arField['option_1']) throw new CError("SYSTEM_BIG_FILE_SIZE", 0, $arField['option_1']);
	if($value['name']!='')
	{
		global $KS_FS;
		$ext=strtolower(substr(basename($value['name']),strrpos(basename($value['name']),".")+1));
		$arAvailable=explode("\n",$arField['option_2']);
		foreach($arAvailable as $key=>$value1)
			$arAvailable[$key]=trim($value1);

		if(is_array($arAvailable)&&count($arAvailable)>0)
		{
			if(!in_array(strtolower($ext),$arAvailable)&&($arAvailable[0]))
				throw new CError("SYSTEM_THIS_FILE_TYPE_NOT_LOAD", 0, $ext);
		}
		if(!file_exists(ROOT_DIR.'/uploads/filefield'))
		{
			$KS_FS->makedir(ROOT_DIR.'/uploads/filefield');
		}
		$filename='/filefield/'.md5($value['name'].time()).'.'.$ext;
		$upload_to=ROOT_DIR."/uploads".$filename;
		if(!move_uploaded_file($value['tmp_name'],$upload_to)) throw new CError("SYSTEM_UPLOAD_FILE_ERROR", 0, $arField['description']);
		chmod($upload_to,0644);
		$sValue=$filename;
	}
}
else
{
	switch($value['error'])
	{
		case UPLOAD_ERR_FORM_SIZE:
		case UPLOAD_ERR_INI_SIZE:
		throw new CError("SYSTEM_BIG_FILE_SIZE", 0, $max_size);
		break;
		case  UPLOAD_ERR_NO_FILE:
		break;
		default:
		throw new CError('SYSTEM_FILE_NOT_FOUND_OR_NOT_WRITABLE');
	}
	$sValue='no';
}

