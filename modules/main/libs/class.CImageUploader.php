<?php
/**
 * Класс выполняет операции связанные с обеспечением загрузки графических файлов
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CFileUploader.php';

class CImageUploader extends CFileUploader
{
	protected $iMaxSize;
	protected $iMaxWidth;
	protected $iMaxHeight;

	function __construct($sFieldName,$sSaverName='FILE_UPLOADER',$maxW=1000,$maxH=1000,$maxSize=1048578)
	{
		parent::__construct($sFieldName,$sSaverName);
		$this->iMaxSize=$maxSize;
		$this->iMaxWidth=$maxW;
		$this->iMaxHeight=$maxH;
	}

	/**
	 * Метод устанавливает ограничения по размеру
	 */
	function SetMaxDimension($width,$height)
	{
		$this->iMaxWidth=$width;
		$this->iMaxHeight=$height;
	}

	function SetMaxFileSize($iSize)
	{
		$this->iMaxSize=$iSize;
	}

	/**
	 * Метод проверяет текущий файл на соответствие требованиям
	 */
	function CheckFile($filename=false)
	{
		if(!$filename)
			$arFile=$_FILES[$this->sField];
		else
			$arFile=GenFileArray($this->sRootDir.$filename);
		if($arFile['size']>$this->iMaxSize)
			throw new CError('SYSTEM_IMAGE_FILESIZE_TO_BIG',1,$this->iMaxSize);
		$sFileType=GetFileType($arFile['tmp_name']);
		if($sFileType=='image/gif'||$sFileType=='image/jpeg'||$sFileType=='image/png')
		{
			if($arParams=@getimagesize($arFile['tmp_name']))
			{
				$width=$arParams[0];
				$height=$arParams[1];
				if($width>$this->iMaxWidth) throw new CError('SYSTEM_IMAGE_TOO_WIDE',2);
				if($height>$this->iMaxHeight) throw new CError('SYSTEM_IMAGE_TOO_HIGH',3);
			}
			else
			{
				throw new CError('SYSTEM_IMAGE_NOT_IMAGE_OR_UNSUPPORTED',10,$sFileType);
			}
		}
		else
		{
			throw new CError('SYSTEM_IMAGE_NOT_IMAGE_OR_UNSUPPORTED',10,$sFileType);
		}
		return true;
	}

	/**
	 * Метод возращает true если файл загружен, также проверяет файл на соответствие требованиям
	 * и в случае если файл не соответствует требованиям, выбрасывает исключение.
	 */
	function IsReady()
	{
		if (array_key_exists($this->sField, $_FILES))
		{
			if($_FILES[$this->sField]['error']==UPLOAD_ERR_OK)
			{
				if($_FILES[$this->sField]['size'] > 0)
				{
					return $this->CheckFile();
				}
				else
				{
					return false;
				}
			}
			elseif($_FILES[$this->sField]['error']==UPLOAD_ERR_NO_FILE && array_key_exists($this->sField,$_SESSION[$this->sSaverName]))
			{
				//Значит файл уже загружали и он лежит в папочки и прописан в сессии
				return $this->CheckFile($_SESSION[$this->sSaverName][$this->sField]);
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
}

