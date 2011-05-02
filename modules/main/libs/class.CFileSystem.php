<?php
/*
 * CMS-remote
 *
 * Created on 17.10.2008
 *
 * Developed by blade39
 *
 * Используется в качестве заготовки для внутренней файловой системы. На данный момент просто содержит ряд полезных
 * функций.
 */

abstract class CFileSystem extends CBaseObject
{

}

/*!Класс CSimpleFs выполняет работу, со стандартной файловой системой. Является оберткой для
 * функций пхп связанных с работой с СФ.*/
class CSimpleFs extends CFileSystem
{
	/**
	 * Функция makedir создает указанную папки или весь путь. В зависимости от существования на
	 * диске указанного пути происходит создание папок. Если создать папку не удалось возвращается
	 * false. Если все ок - true.
	 * @param $path -- путь который требуется создать.
	 * @return true -- в случае успеха
	 * Если каталог создать не удалось выбрасывает исключение CFileRror
	 * TODO: Сделать нормальный возврат достоверных результатов из функции(и наверное обработку в случае ошибки)
	 */
	function makedir($path)
	{
		if(!@mkdir($path,0755,true))
		{
			throw new CFileError('SYSTEM_CANT_CREATE_DIR',1,$path);
		}
		return true;
	}

	/**
	 * Метод удаляет файл или папку по указанному пути
	 */
	function Remove($path)
	{
		if(file_exists($path))
		{
			if(is_dir($path))
			{
				$this->cleardir($path);
				return @rmdir($path);
			}
			else
			{
				return @unlink($path);
			}
		}
		throw new CError('SYSTEM_FILE_NOT_FOUND');
	}

	/**
	 * Функция переименовывает указанную папку. В зависимости от существования на
	 * диске указанного пути происходит создание папок. Если создать папку не удалось возвращается
	 * false. Если все ок - true.
	 * @param $path -- путь который требуется создать.
	 * @return true
	 * Если не удалось - выбрасывает исключение CFileError
	 */
	function renamedir($old, $new)
	{
		$old = str_replace('//','/',$old);
		$new = str_replace('//','/',$new);
		if(!@rename($old,$new))
		{
			throw new CFileError('SYSTEM_CANT_RENAME',1,$old.' '.$new);
		}
		return true;
	}


	/*!Функция рекурсивно удаляет подкаталоги указанного пути.
	 * \param $path -- удаляемый каталог.*/
	function remdir($path)
	{
		$origipath = $path;
		$handler = @opendir($path);
		if(!$handler) throw new CFileError("SYSTEM_FOLDER_NOT_EXIST", 0);
		while (true)
		{
			$item = readdir($handler);
			if ($item == "." or $item == "..")
			{
				continue;
			}
			elseif (gettype($item) == "boolean")
			{
				closedir($handler);
				if (!@rmdir($path))
				{
					throw new CFileError('SYSTEM_FILE_DELETE_ERROR',0,$path);
				}
				if ($path == $origipath)
				{
					break;
				}
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
			{
				if(!@unlink($path."/".$item)) throw new CFileError('SYSTEM_FILE_DELETE_ERROR',0,$path.'/'.$item);
			}
		}
		return true;
	}

	/*!Выполняет копирование всех файлов из одной папки в другую.
	 * \param $srcdir -- исходная папка;
	 * \param $dstdir -- папка назначения;
	 * \param $offset -- с какого по счету файла выполнять копирование;
	 * \param $verbose -- выводить сообщения о работе.*/
	function dircopy($srcdir, $dstdir, $offset=0, $verbose = false)
	{
		if(!isset($offset)) $offset=0;
		$num = 0;
		$fail = 0;
		$sizetotal = 0;
		$fifail = '';
		$ret='0,0,0,0';
		if(!is_dir($dstdir)) self::makedir($dstdir);
		if($curdir = opendir($srcdir))
		{
			while($file = readdir($curdir))
			{
				if($file != '.' && $file != '..')
				{
					$srcfile = $srcdir . '/' . $file;
					$dstfile = $dstdir . '/' . $file;
					if(is_file($srcfile))
					{
						if(is_file($dstfile)) $ow = filemtime($srcfile) - filemtime($dstfile); else $ow = 1;
						if($ow > 0)
						{
							if($verbose) echo "Copying '$srcfile' to '$dstfile'...";
							if(copy($srcfile, $dstfile))
							{
								touch($dstfile, filemtime($srcfile));
								$num++;
								$sizetotal = ($sizetotal + filesize($dstfile));
								if($verbose) echo "OK\n";
							}
							else
							{
								throw new CError("SYSTEM_UNABLE_COPY_FILE", 0, $srcfile);
								$fail++;
								$fifail = $fifail.$srcfile."|";
							}
						}
					}
					else if(is_dir($srcfile))
					{
						$res = explode(",",$ret);
						$ret = self::dircopy($srcfile, $dstfile, $verbose);
						$mod = explode(",",$ret);
						$imp = array($res[0] + $mod[0],$mod[1] + $res[1],$mod[2] + $res[2],$mod[3].$res[3]);
						$ret = implode(",",$imp);
					}
				}
			}
			closedir($curdir);
		}
		$red = explode(",",$ret);
		if(is_array($red) && count($red)>2)
		{
			$ret = ($num + $red[0]).",".(($fail-$offset) + $red[1]).",".($sizetotal + $red[2]).",".$fifail.$red[3];
		}
		else
		{
			$ret = ($num).",".(($fail-$offset)).",".($sizetotal).",".$fifail;
		}
		return $ret;
	}

	/**
	 * Метод осуществляет копирование двух файлов
	 */
	function CopyFile($from,$to,$absolute=false)
	{
		if($absolute===false)
		{
			$from=ROOT_DIR.$from;
			$to=ROOT_DIR.$to;
		}
		else
		{
			$from=$absolute.$from;
			$to=$absolute.$to;
		}
		if(is_dir($from))
		{
			$this->dircopy($from,$to);
		}
		else
		{
			return @copy($from, $to);
		}
	}

	/*!Функция CmpPath выполняет сравнение двух путей и возвращает их расходение в виде массив начиная
	 * с первого элемента пути, где встречается расхождение.*/
	function CmpPath($old,$new)
	{
		$oldpath=explode('/',$old);
		$newpath=explode('/',$new);
		$num=(count($oldpath)>count($newpath))?count($oldpath):count($newpath);
		$path='';
		for($i=1;$i<$num;$i++)
		{
			if($oldpath[$i]!=$newpath[$i])
			{
				return $path.='/'.$oldpath[$i];
			}
			else
			{
				$path.='/'.$oldpath[$i];
			}
		}
		return $path;
	}

	/*!Функция ChangePath выполняет полный перенос файлов из одного пути в другой. При работе этой
	 * функции происходит создание нового пути (указанного в параметре $new) и перенос всех файлов
	 * из папки $old в папку $new. Путь $new создается полностью, все расхождения между, $old и $new,
	 * удаляются. Внешне это выглядит как простая замена пути к файлу.*/
	function ChangePath($old,$new)
	{
		$oldpath=explode('/',$old);
		$newpath=explode('/',$new);
		try
		{
			self::makedir($new);
			self::dircopy($old,$new);
			$delpath=self::CmpPath($old,$new);
			if($delpath!=$old)
			{
				self::remdir($delpath);
			}
		} catch (CError $e)
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
	 *
	 * @param string $dir Абсолютный путь к директории
	 * @return array
	 */
	function GetDirItems($dir)
	{
		$dir_items = array();
		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($dir_item = readdir($dh)) !== false)
				{
					if ($dir_item != "." && $dir_item != "..")
						$dir_items[] = $dir_item;
				}
				closedir($dh);

				return $dir_items;
			}
			else
				throw new CError("SYSTEM_ERROR_OPENING_DIRECTORY", 0, $dir);
		}

		return false;
	}

	/**
	 * Метод выполняет подсчет всех файлов в дериктории с учетом вложенных
	 */
	function CountDirFiles($dir)
	{
		$total=0;
		$list=self::GetDirItems($dir);
		foreach($list as $file)
		{
			if(is_dir($dir.'/'.$file))
			{
				$total+=self::CountDirFiles($dir.'/'.$file);
			}
			elseif(is_file($dir.'/'.$file))
			{
				$total++;
			}
		}
		return $total;
	}

	/**
	 * Метод выполняет построение списка всех файлов дериктории
	 */
	function GetDirList($dir)
	{
		$arList=array();
		$list=self::GetDirItems($dir);
		foreach($list as $file)
		{
			if(is_dir($dir.'/'.$file))
			{
				$arList=array_merge($arList,self::GetDirList($dir.'/'.$file));
			}
			elseif(is_file($dir.'/'.$file))
			{
				$arList[]=$dir.'/'.$file;
			}
		}
		return $arList;
	}

	/**
	 * Функция рекурсивно удаляет подкаталоги указанного пути.
	 * @param $path -- удаляемый каталог.*/
	function cleardir($path)
	{
		$origipath = $path;
		$handler = @opendir($path);
		if(!$handler) throw new Exception('SYSTEM_FILE_NOT_FOUND');
		while (true)
		{
			$item = readdir($handler);
			if ($item == "." or $item == "..")
			{
				continue;
			}
			elseif (gettype($item) == "boolean")
			{
				closedir($handler);
				if($path!=$origipath)
				{
					if (!@rmdir($path))
					{
						return false;
					}
				}
				else
				{
					break;
				}
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
			{
				unlink($path."/".$item);
			}
		}
		return true;
	}
}

?>