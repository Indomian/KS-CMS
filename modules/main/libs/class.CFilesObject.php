<?php
/**
 * \file class.CFilesObject.php
 * Контейнер для класса работающего с файлами
 * Файл проекта kolos-cms.
 *
 * Создан 13.03.2010
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.5.5
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR."/main/libs/class.CMain.php";

class CFilesObject extends CObject
{
	protected $arFileFields;	/*!<поля значения которых - файлы.*/
	public $sUploadPath; 	/*!<Путь для загрузки файлов через функцию save.*/

	function __construct($sTable='',$sUploadPath='')
	{
		parent::__construct($sTable);
		$this->arFileFields=array();
		$this->sUploadPath=$sUploadPath;
	}

	/**
	 * Метод выполняет установку папки для загрузки файлов получаемых через функцию save
	 * @param $path новый путь сохранения относительно папки /uploads/
	 */
	function SetUploadFolder($path)
	{
		$this->sUploadPath=$path;
	}

	/**
	 * Метод указывает, что поле с данным именем при сохранении должно загрузить файл
	 * @param $field - имя поля
	 * @return true|false - если поле добавлено - true иначе - false.
	 */
	function AddFileField($field)
	{
		if(in_array($field,$this->arFields))
		{
			if(!in_array($field,$this->arFileFields))
				$this->arFileFields[]=$field;
			return true;
		}
		return false;
	}

	/**
	 * Метод выполняет загрузку указанного файла
	 * @param $key string - поле для поиска откуда грузить файл
	 */
	protected function _DoFileUpload($key)
	{
		global $KS_FS;
		$sResult='';
		if (array_key_exists($key, $_FILES))
		{
			if($_FILES[$key]['error']==UPLOAD_ERR_OK)
			{
				if($_FILES[$key]['size'] > 0)
				{
					$file_ext = substr($_FILES[$key]['name'], strrpos($_FILES[$key]['name'], "."));
					if(!file_exists(UPLOADS_DIR.$this->sUploadPath))
					{
						$KS_FS->makedir(UPLOADS_DIR.$this->sUploadPath);
					}
					$filename = $this->sUploadPath.'/'.$this->_GenFileName($_FILES[$key]['name']);
					$upload_to = UPLOADS_DIR.$filename;
					move_uploaded_file($_FILES[$key]['tmp_name'], $upload_to);
					chmod($upload_to, 0644);
					$sResult = $filename;
					$_SESSION[__CLASS__][$key]=$filename;
				}
				else
				{
					throw new CFileError('SYSTEM_UPLOAD_ZERO_SIZE');
				}
			}
			elseif($_FILES[$key]['error']==UPLOAD_ERR_NO_FILE && array_key_exists($key,$_SESSION[__CLASS__]))
			{
				//Значит файл уже загружали и он лежит в папочки и прописан в сессии
				$sResult=$_SESSION[__CLASS__][$key];
			}
			elseif($_FILES[$key]['error']==UPLOAD_ERR_NO_FILE)
			{
				$sResult='';
			}
			elseif($_FILES[$key]['error']==UPLOAD_ERR_INI_SIZE)
			{
				throw new CFileError('SYSTEM_UPLOAD_TOO_BIG');
			}
			else
			{
				throw new CFileError('SYSTEM_UPLOAD_FILE_ERROR',-1);
			}
		}
		elseif(array_key_exists($key,$_SESSION[__CLASS__]))
		{
			//Значит файл уже загружали и он лежит в папочки и прописан в сессии
			$sResult=$_SESSION[__CLASS__][$key];
		}
		return $sResult;
	}

	/**
	 * Метод возвращает значения полей из массива данных. Для данного класса, производит
	 * загрузку изображений во временную папку, после чего при удачном сохранении
	 * записывает их из копии в нормальное место.
	 *
	 * Метод позволяет построить данные по правильной структуре исходя из переданных в
	 * массиве данных, также производится проверка на соответствие полей в данных и в таблице.
	 * @param $prefix - префикс для полей в массиве данных, необязательный
	 * @param $data - массив данных, необязеательный если не указан используется $_POST
	 * @return Ассоциативный массив данных
	 */
	function GetRecordFromPost($prefix='',$data='')
	{
		global $KS_FS;
		if($data=='') $data=$_POST;
		foreach ($this->arFields as $field)
		{
			if(in_array($field,$this->arFileFields))
			{
				$arResult[$field]=$this->_DoFileUpload($prefix.$field);
			}
			else
			{
				$arResult[$field]=$data[$prefix.$field];
			}
		}
		return $arResult;
	}

	/**
	 * Метод выполняет генерацию имени файла для сохранения
	 */
	protected function _GenFileName($filename)
	{
		return md5($filename.time()).'.'.substr($filename,strrpos($filename,'.')+1);
	}

	/**
	 * Метод обрабатывает одно поле перед сохранением в базу данных
	 * @param $prefix - префикс имени поля в массиве данных
	 * @param $key - имя поля
	 * @param $input - массив входных данных
	 * @param $value - массив описывающий поле
	 */
	protected function _ParseField($prefix,$key,&$input,&$value)
	{
		global $KS_FS;
		$sResult=parent::_ParseField($prefix,$key,$input,$value);
		/* Преобразование входных данных в соответствии с форматом полей таблицы для записи */
		/* Считаем входными данными также закачанные на сервер файлы-картинки */
		if(in_array($key,$this->arFileFields))
		{
			if (array_key_exists($prefix . $key, $_FILES))
			{
				$sResult=$this->_DoFileUpload($prefix.$key);
			}
			elseif(array_key_exists($prefix.$key,$_SESSION[__CLASS__]))
			{
				//Значит файл уже загружали и он лежит в папочки и прописан в сессии
				$sResult=$_SESSION[__CLASS__][$prefix.$key];
				unset($_SESSION[__CLASS__][$prefix.$key]);
			}
			if (array_key_exists($prefix . $key . '_del', $_REQUEST))
			{

				if($input[$prefix.'id']!='')
				{
					$arItem=$this->GetRecord(array('id'=>$input[$prefix . 'id']));
					if(is_array($arItem)&&($arItem['id']==$input[$prefix . 'id']))
					{
						if (file_exists(UPLOADS_DIR.$arItem[$key]))
							unlink(UPLOADS_DIR.$arItem[$key]);
					}
				}
				$sResult="";
			}
		}
		return $sResult;
	}

	/**
	 * Метод удаляет элементы, также удаляются файлы файловых полей.
	 * Переопределяет метод {@link CObject::DeleteItems() CObject::DeleteItems()}.
	 */
	function DeleteItems($arFilter)
	{
		$arItems=$this->GetList(array('id'=>'asc'),$arFilter);
		if(is_array($arItems)&&count($arItems)>0)
		{
			foreach($arItems as $key=>$item)
			{
				foreach($this->arFileFields as $field)
				{
					if($item[$field]!='')
					{
						@unlink(UPLOADS_DIR.$item['img']);
					}
				}
			}
			return parent::DeleteItems($arFilter);
		}
		return false;
	}
}
?>
