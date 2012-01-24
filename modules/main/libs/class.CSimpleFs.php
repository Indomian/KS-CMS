<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CFileSystem.php';
/**
 * Класс CSimpleFs выполняет работу, со стандартной файловой системой. Является оберткой для функций пхп связанных с работой с СФ.
 * @project ks-cms
 * @version 2.6
 * @since 17.10.2008
 * @author blade39 <blade39@kolosstudio.ru>,  D. Konev, <d.konev@kolosstudio.ru>
 */
class CSimpleFs extends CFileSystem
{
	/**
	 * Функция makedir создает указанную папки или весь путь. В зависимости от существования на диске указанного пути происходит создание папок.
	 * Если каталог создать не удалось выбрасывает исключение CFileRror
	 * @param string - путь который требуется создать.
	 * @return bool
	 * @todo Сделать нормальный возврат достоверных результатов из функции(и наверное обработку в случае ошибки)
	 */
	function MakeDir($path)
	{
		if(!@mkdir($path,0755,true))
			throw new CFileError('SYSTEM_CANT_CREATE_DIR',1,$path);

		return true;
	}

	/**
	 * Метод удаляет файл или папку по указанному пути
	 * @param string - путь, согласно которому происходит удаление
	 * @return bool
	 */
	function Remove($path)
	{
		if(file_exists($path))
		{
			if(is_dir($path))
			{
				$this->ClearDir($path);
				return @rmdir($path);
			}
			else
				return @unlink($path);
		}
		throw new CError('SYSTEM_FILE_NOT_FOUND');
	}

	/**
	 * Функция переименовывает указанную папку. В зависимости от существования на диске указанного пути происходит создание папок.
	 * Если не удалось - выбрасывает исключение CFileError
	 * @param string - путь который требуется создать.
	 * @return bool
	 */
	function Rename($old, $new)
	{
		$old = str_replace('//','/',$old);
		$new = str_replace('//','/',$new);
		if(!@rename($old,$new))
			throw new CFileError('SYSTEM_CANT_RENAME',1,$old.' '.$new);

		return true;
	}


	/*
	 * Это есть алиас для Remove. Оставлен ввиду того, что система очень зависима от методов данного класса
	 * @param string - удаляемый каталог
	 * @see Remove
	 */
	function RemDir($path)
	{
		return $this->Remove($path);
	}

	/**
	 * Выполняет копирование всех файлов из одной папки в другую.
	 * @param string - исходная папка;
	 * @param string - папка назначения;
	 * @return string - данные о скопированных элементах
	 */
	function DirCopy($srcdir, $dstdir)
	{
		if(!is_dir($dstdir))
			$this->MakeDir($dstdir);
		if($curdir = opendir($srcdir))
		{
			while($file = readdir($curdir))
				if($file != '.' && $file != '..')
				{
					$srcfile = $srcdir . '/' . $file;
					$dstfile = $dstdir . '/' . $file;
					if(is_file($srcfile))
					{
						if(is_file($dstfile))
							$ow = filemtime($srcfile) - filemtime($dstfile);
						else
							$ow = 1;
						if($ow > 0)
						{
							if(copy($srcfile, $dstfile))
								touch($dstfile, filemtime($srcfile));
							else
								throw new CError("SYSTEM_UNABLE_COPY_FILE", 0, $srcfile);
						}
					}
					else if(is_dir($srcfile))
						$ret = self::DirCopy($srcfile, $dstfile);
				}
			closedir($curdir);
		}
		return true;
	}

	/**
	 * Метод осуществляет копирование двух файлов
	 * @param string - путь до файла, который копируется
	 * @param string - путь, куда осуществляется копирование
	 * @param string - абсолютный путь
	 * @return bool
	 */
	function CopyFile($from,$to,$absolute='')
	{
		$from=$absolute.$from;
		$to=$absolute.$to;
		if(is_dir($from))
			return $this->DirCopy($from,$to);
		else
			return @copy($from, $to);
	}

	/**
	 * Функция CmpPath выполняет сравнение двух путей
	 * @param string - один из путей, которые сравниваются
	 * @param string - другой путь
	 * @return string - расхождение путей
	 */
	function CmpPath($old,$new)
	{
		$oldpath=explode('/',$old);
		$newpath=explode('/',$new);
		$num=(count($oldpath)>count($newpath))?count($oldpath):count($newpath);
		$path='';
		for($i=1;$i<$num;$i++)
		{
			if($oldpath[$i]!=$newpath[$i])
				return $path.='/'.$oldpath[$i];
			else
				$path.='/'.$oldpath[$i];
		}
		return $path;
	}

	/**
	 * Функия возвразщает новый путь, сформированный из расхождений
	 * @param string - старый путь
	 * @param string - новый путь
	 * @return string - строка результата
	 */
	function DiffPath($sOld,$sNew)
	{

	}

	/**
	 * Функция ChangePath выполняет полный перенос файлов из одного пути в другой. При работе этой
	 * функции происходит создание нового пути (указанного в параметре $new) и перенос всех файлов
	 * из папки $old в папку $new. Путь $new создается полностью, все расхождения между, $old и $new,
	 * удаляются. Внешне это выглядит как простая замена пути к файлу.
	 * @param string - старый путь
	 * @param string - новый путь
	 * @return bool
	 */
	function ChangePath($old,$new)
	{
		$oldpath=explode('/',$old);
		$newpath=explode('/',$new);
		try
		{
			$this->MakeDir($new);
			$this->DirCopy($old,$new);
			$delpath=$this->CmpPath($old,$new);
			if($delpath!=$old)
				$this->RemDir($delpath);
		}
		catch (CError $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
		return true;
	}

	/**
	 * Метод возвращает список файлов директории
	 * @param string - абсолютный путь к директории
	 * @return array
	 */
	function GetDirItems($dir)
	{
		$dir_items = array();
		if(file_exists($dir) && is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($dir_item = readdir($dh)) !== false)
				{
					if ($dir_item != "." && $dir_item != "..")
						$dir_items[] = $dir_item;
				}
				closedir($dh);
				if(count($dir_items)==0)
					return false;
				return $dir_items;
			}
			else
				throw new CError("SYSTEM_ERROR_OPENING_DIRECTORY", 0, $dir);
		}
		return false;
	}

	/**
	 * Метод выполняет подсчет всех файлов в дериктории с учетом вложенных
	 * @param string - абсолютный путь к директории
	 * @return int - число файлов в директории
	 */
	function CountDirFiles($dir)
	{
		$total=0;
		$list=$this->GetDirItems($dir);
		foreach($list as $file)
		{
			if(is_dir($dir.'/'.$file))
				$total+=$this->CountDirFiles($dir.'/'.$file);
			elseif(is_file($dir.'/'.$file))
				$total++;
		}
		return $total;
	}

	/**
	 * Метод выполняет построение списка всех файлов дериктории
	 * @param string - абсолютный путь к директории
	 * @return array
	 */
	function GetDirList($dir)
	{
		$arList=array();
		$list=$this->GetDirItems($dir);
		foreach($list as $file)
		{
			if(is_dir($dir.'/'.$file))
				$arList=array_merge($arList,$this->GetDirList($dir.'/'.$file));
			elseif(is_file($dir.'/'.$file))
				$arList[]=$dir.'/'.$file;
		}
		return $arList;
	}

	/**
	 * Функция рекурсивно удаляет подкаталоги указанного пути.
	 * @param string - удаляемый каталог
	 * @return bool
	 */
	function ClearDir($path)
	{
		$origipath = $path;
		$handler = @opendir($path);
		if(!$handler)
			throw new Exception('SYSTEM_FILE_NOT_FOUND');
		while (true)
		{
			$item = readdir($handler);
			if ($item == "." or $item == "..")
				continue;
			elseif (gettype($item) == "boolean")
			{
				closedir($handler);
				if($path!=$origipath)
				{
					if (!@rmdir($path))
						return false;
				}
				else
					break;
				$path = substr($path, 0, strrpos($path, "/"));
				$handler = opendir($path);
			}
			elseif (is_dir($path."/".$item))
			{
				closedir($handler);
				$path = $path."/".$item;
				$handler = opendir($path);
			}
			else
				unlink($path."/".$item);
		}
		return true;
	}
}