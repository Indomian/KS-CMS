<?php
/**
 * Класс выполняет операции связанные с обеспечением загрузки файлов
 */

class CFileUploader
{
	private $sField;
	private $sSaverName;
	private $filename;
	private $extension;
	private $maxnum;
	private $sRootDir;

	function __construct($sFieldName,$sSaverName='FILE_UPLOADER')
	{
		$this->sField=$sFieldName;
		$this->sSaverName=$sSaverName;
		$this->maxnum=0;
		$this->sRootDir=UPLOADS_DIR;
	}

	function SetRootDir($sPath)
	{
		if(file_exists($sPath)) $this->sRootDir=$sPath;
	}

	/**
	 * Метод возращает true если файл загружен
	 */
	function IsReady()
	{
		if (array_key_exists($this->sField, $_FILES))
		{
			if($_FILES[$this->sField]['error']==UPLOAD_ERR_OK)
			{
				if($_FILES[$this->sField]['size'] > 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			elseif($_FILES[$this->sField]['error']==UPLOAD_ERR_NO_FILE && array_key_exists($this->sField,$_SESSION[$this->sSaverName]))
			{
				//Значит файл уже загружали и он лежит в папочки и прописан в сессии
				return true;
			}
			elseif($_FILES[$this->sField]['error']==UPLOAD_ERR_NO_FILE)
			{
				return false;
			}
			elseif($_FILES[$this->sField]['error']==UPLOAD_ERR_INI_SIZE)
			{
				return false;
			}
			else
			{
				return false;
			}
		}
		return false;
	}

	function _FilterArray($filename)
	{
		if(preg_match('#^'.$this->filename.'([0-9]+)'.$this->extension.'$#',$filename,$matches))
		{
			$this->maxnum=intval($matches[1])>$this->maxnum?intval($matches[1]):$this->maxnum;
			return true;
		}
		return false;
	}

	function GetFileName()
	{
		if($this->IsReady())
		{
			return $_FILES[$this->sField]['name'];
		}
		return false;
	}
	/**
	 * Метод выполняет загрузку нового файла
	 */
	function Upload($sNewFilename,$bUseUserFileName=true)
	{
		global $KS_FS;
		if(!$this->IsReady())
		{
			return false;
		}
		if(array_key_exists($this->sSaverName,$_SESSION)&&array_key_exists($this->sField,$_SESSION[$this->sSaverName]))
		{
			return $_SESSION[$this->sSaverName][$this->sField];
		}
		else
		{
			if(strpos($sNewFilename,$this->sRootDir)!==false)
			{
				$sNewFilename=substr($sNewFilename,strlen($this->sRootDir));
			}
			if($bUseUserFileName)
				$sNewFilename.='/'.$_FILES[$this->sField]['name'];
			$arFile=pathinfo($sNewFilename);
			if(!IsFilename($arFile['basename']))
			{
				$arFile['filename']=Translit($arFile['filename']);
				$arFile['extension']=Translit($arFile['extension']);
				$arFile['basename']=$arFile['filename'].'.'.$arFile['extension'];
			}
			if(!file_exists($this->sRootDir.$arFile['dirname']))
			{
				$KS_FS->makedir($this->sRootDir.$arFile['dirname']);
			}
			else
			{
				if(file_exists($this->sRootDir.$arFile['dirname'].$arFile['basename']))
				{
					$this->filename=$arFile['filename'];
					$this->extension=$arFile['extension'];
					$arList=$KS_FS->GetDirItems($this->sRootDir.$arFile['dirname']);
					$arList=array_filter($arList,array($this,'_FilterArray'));
					$filename=$arFile['filename'].($this->maxnum+1).$arFile['extension'];
				}
				else
				{
					$filename=$arFile['basename'];
				}
			}
			$filename = $arFile['dirname'].'/'.$filename;
			$upload_to = $this->sRootDir.$filename;
			if(!move_uploaded_file($_FILES[$this->sField]['tmp_name'], $upload_to))
			{
				return false;
			}
			chmod($upload_to, 0644);
			$sResult = $filename;
			$_SESSION[$this->sSaverName][$this->sField]=$filename;
			return $filename;
		}
		return false;
	}

	function UploadDone()
	{
		unset($_SESSION[$this->sSaverName][$this->sField]);
	}

}