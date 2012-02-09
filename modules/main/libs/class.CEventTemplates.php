<?php
/*
 * CMS-local
 *
 * Created on 10.11.2008
 *
 * Developed by blade39
 *
 */

/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CTemplates.php';
include_once MODULES_DIR.'/main/libs/class.CObject.php';

class CEventTemplates extends CTemplates
{
	private $obTemplates;

	function __construct()
	{
		$this->sTemplatesPath=ROOT_DIR.'/templates/admin/eventTemplates/';
		$this->obTemplates=new CObject('main_eventtemplates');
	}

	function _ParseItem(&$item)
	{
		$itemname=explode('.',$item);
		$res=array('title'=>$item,'module'=>$itemname[0],'date_edit'=>filemtime($this->sTemplatesPath.$item));
		$item=$res;
		return true;
	}

	function GetTemplate($tpl)
	{
		$sPath=$this->sTemplatesPath;
		$sContent='';
		if(!preg_match('#^[\w\d\.]+\.tpl#',$tpl))
			throw new CError("MAIN_FAIL_EDIT_TEMPLATE_NAME");
		if (file_exists($sPath.$tpl))
		{
			$hFile=@fopen($sPath.$tpl,"r");
			if ($hFile)
			{
				while(!feof($hFile))
					$sContent.=fgets($hFile);
				fclose($hFile);
				if($data=$this->obTemplates->GetRecord(array('file_id'=>$tpl)))
				{
					$data['content']=$sContent;
					return $data;
				}
				else
					throw new CError("MAIN_TEMPLATE_NOT_REGISTERED");
			}
			else
				throw new CError("MAIN_NOT_READ_TEMPLATE");
		}
		else
			throw new CError("MAIN_TEMPLATE_NOT_FOUND");
	}

	function Delete($id)
	{
		$this->obDB->begin();
		try
		{
			$this->obTemplates->DeleteItems(array('file_id'=>$id));
			if(!@unlink($this->sTemplatesPath.$id))
				throw new CError("SYSTEM_FILE_NOT_FOUND");
		}
		catch (CError $e)
		{
			$this->obDB->rollback();
			throw $e;
		}
		$this->obDB->commit();
	}

	function SaveTemplate($name='',$scheme='index')
	{
		$this->obDB->begin();
		try
		{
			if(!$this->obTemplates->GetRecord(array('file_id'=>$_POST['KS_file_id'])))
			{
				if(!$this->obTemplates->Save('KS_'))
					throw new CError("DB_MYSQL_WRITE_ERROR");
				$sPath=$this->sTemplatesPath;
	        	$sTemplate=$_POST['template_file'];
	        	if(ini_get('magic_quotes_gpc')==1)
					$sTemplate=stripslashes($sTemplate);
		        if (!file_exists($sPath))
		        	mkdir($sPath);
	        	if(!preg_match('#^[\w\d\.]+\.tpl#',$_POST['KS_file_id']))
					throw new CError("MAIN_FAIL_EDIT_TEMPLATE_NAME");
			    if ((!file_exists($sPath.$_POST['KS_file_id']))||is_writable($sPath.$_POST['KS_file_id']))
	        	{
			       	$hFile=@fopen($sPath.$_POST['KS_file_id'],"w");
			       	if ($hFile)
			       	{
			       		if (!fwrite($hFile,$sTemplate))
		       				throw new CError("SYSTEM_NOT_WRITE_TO_FILE", 0, '.template.tpl');
			       	}
		       		else
		       			throw new CError("SYSTEM_NOT_OPEN_WRITE_TO_FILE");
		    	}
	       		else
		       		throw new CError("SYSTEM_FILE_NOT_FOUND_OR_NOT_WRITABLE");
			}
			else
				throw new CError("MAIN_TEMPLATE_ALREADY_EXISTS");
		}
		catch (CError $e)
		{
			$this->obDB->rollback();
			throw $e;
		}
		$this->obDB->commit();
	}

	/**
	 * Извлекает из шаблона переменные смарти
	 * @param string $tpl - имя файла шаблона
	 * @return array - список переменных смарти или false
	 */
	function GetTemplateVarNames($tpl){
		$result=false;
		$data = $this->GetTemplate($tpl);
		$data = $data['content'];
		preg_match_all('#{\$[^}]*?}#mi',$data,$result);
		return $result[0];
	}
}
