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

class _CEventTemplates extends CObject
{
	function __construct()
	{
		parent::__construct('main_eventtemplates');
		$this->arFields=array('id','file_id','title','address','copy');
	}
}

class CEventTemplates extends CTemplates
{
	function __construct()
	{
		$this->sTemplatesPath=ROOT_DIR.'/templates/admin/eventTemplates/';
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
		{
			throw new CError("MAIN_FAIL_EDIT_TEMPLATE_NAME");
		}
		if (file_exists($sPath.$tpl))
		{
			$hFile=@fopen($sPath.$tpl,"r");
			if ($hFile)
			{
				while(!feof($hFile))
				{
					$sContent.=fgets($hFile);
				}
				fclose($hFile);
				$ob=new _CEventTemplates();
				if($data=$ob->GetRecord(array('file_id'=>$tpl)))
				{
					$data['content']=$sContent;
					return $data;
				}
				else
				{
					throw new CError("MAIN_TEMPLATE_NOT_REGISTERED");
				}
			}
			else
			{
				throw new CError("MAIN_NOT_READ_TEMPLATE");
			}
		}
		else
		{
			throw new CError("MAIN_TEMPLATE_NOT_FOUND");
		}
	}

	function Delete($id)
	{
		global $ks_db;
		$ob=new _CEventTemplates();
		$ks_db->begin();
		try
		{
			$ob->DeleteItems(array('file_id'=>$id));
			if(!@unlink($this->sTemplatesPath.$id))
			{
				throw new CError("SYSTEM_FILE_NOT_FOUND");
			}
		}
		catch (CError $e)
		{
			$ks_db->rollback();
			throw $e;
		}
		$ks_db->commit();
	}

	function SaveTemplate($name='',$scheme='index')
	{
		global $ks_db;
		$ks_db->begin();
		try
		{
			$ob=new _CEventTemplates();
			$ob->AddCheckField('file_id');
			$ob->AddAutoField('id');
			if(!$ob->Save('KS_'))
			{
				throw new CError("DB_MYSQL_WRITE_ERROR");
			}
			$sPath=$this->sTemplatesPath;
        	$sTemplate=$_POST['template_file'];
        	if(ini_get('magic_quotes_gpc')==1)
			{
				$sTemplate=stripslashes($sTemplate);
			}
	        if (!file_exists($sPath))
        	{
	        	mkdir($sPath);
        	}
        	if(!preg_match('#^[\w\d\.]+\.tpl#',$_POST['KS_file_id']))
			{
				throw new CError("MAIN_FAIL_EDIT_TEMPLATE_NAME");
			}
		    if ((!file_exists($sPath.$_POST['KS_file_id']))||is_writable($sPath.$_POST['KS_file_id']))
        	{
		       	$hFile=@fopen($sPath.$_POST['KS_file_id'],"w");
		       	if ($hFile)
	       		{
		       		if (!fwrite($hFile,$sTemplate))
		       		{
	       				throw new CError("SYSTEM_NOT_WRITE_TO_FILE", 0, '.template.tpl');
	       			}
	       			else
	       			{
		       			return 0;
		       		}
	       		}
	       		else
	       		{
	       			throw new CError("SYSTEM_NOT_OPEN_WRITE_TO_FILE");
	       		}
	    	}
       		else
       		{
	       		throw new CError("SYSTEM_FILE_NOT_FOUND_OR_NOT_WRITABLE");
    	   	}
		}
		catch (CError $e)
		{
			$ks_db->rollback();
			throw $e;
		}
		$ks_db->commit();

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
?>
