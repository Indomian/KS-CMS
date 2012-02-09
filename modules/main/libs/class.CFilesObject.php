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

require_once MODULES_DIR."/main/libs/class.CObject.php";
require_once MODULES_DIR.'/main/libs/class.CFileUploader.php';

class CFilesObject extends CObject
{
	protected $arFileFields;	/*!<поля значения которых - файлы.*/
	protected $sUploadPath; 	/*!<Путь для загрузки файлов через функцию save.*/
	private $obUploadManager;

	function __construct($sTable='',$sUploadPath='')
	{
		parent::__construct($sTable);
		$this->arFileFields=array();
		$this->sUploadPath=$sUploadPath;
		$this->obUploadManager=false;
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
	 * Метод возвращает текущий путь сохранения файлов
	 */
	function GetUploadFolder()
	{
		return $this->sUploadPath;
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
	 * @deprecated 17.03.2010 используйте CFileUploader::Upload
	 */
	protected function _DoFileUpload($key)
	{
		throw new CError('SYSTEM_DERECATED');
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
				$obUploadManager=new CFileUploader($prefix.$field,$this->sTable);
				if($obUploadManager->IsReady())
					$arResult[$field]=$obUploadManager->Upload($this->sUploadPath.'/'.$this->_GenFileName($obUploadManager->GetFileName()),false);
			}
			elseif(isset($data[$prefix.$field]))
				$arResult[$field]=$data[$prefix.$field];
			else
				$arResult[$field]='';
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

	function GenFileName($filename)
	{
		return $this->_GenFileName($filename);
	}

	/**
	 * Метод обрабатывает одно поле перед сохранением в базу данных
	 * @param $prefix - префикс имени поля в массиве данных
	 * @param $key - имя поля
	 * @param $input - массив входных данных
	 * @param $value - массив описывающий поле
	 */
	protected function _ParseField($prefix,$key,array &$input,&$value)
	{
		global $KS_FS;
		$sResult=parent::_ParseField($prefix,$key,$input,$value);
		/* Преобразование входных данных в соответствии с форматом полей таблицы для записи */
		/* Считаем входными данными также закачанные на сервер файлы-картинки */
		if(in_array($key,$this->arFileFields))
		{
			$obUploadManager=new CFileUploader($prefix.$key,$this->sTable);
			if($obUploadManager->IsReady())
			{
				$sResult=$obUploadManager->Upload($this->sUploadPath.'/'.$this->_GenFileName($obUploadManager->GetFileName()),false);
				$obUploadManager->UploadDone();
				if($sResult)
				{
					//Если загрузили файл, пробуем заменить его
					if(isset($input[$prefix.'id']))
					{
						$arItem=$this->GetRecord(array('id'=>$input[$prefix . 'id']));
						if(is_array($arItem)&&($arItem['id']==$input[$prefix . 'id']) && $sResult!=$arItem[$key])
							if (file_exists(UPLOADS_DIR.$arItem[$key])&&is_file(UPLOADS_DIR.$arItem[$key]))
								unlink(UPLOADS_DIR.$arItem[$key]);
					}
				}
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
	function DeleteItems(array $arFilter)
	{
		if($arItems=$this->GetList(array('id'=>'asc'),$arFilter))
		{
			foreach($arItems as $key=>$item)
				foreach($this->arFileFields as $field)
					if($item[$field]!='')
						@unlink(UPLOADS_DIR.$item['img']);
			return parent::DeleteItems($arFilter);
		}
		return false;
	}
}

