<?php

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined('KS_ENGINE'))
	die("Hacking attempt!");

require_once 'class.CBaseObject.php';
/**
 * Класс для редактирования файлов конфигурации, позволяет автоматически подгружать
 * и редактировать файлы конфигураций модулей
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.0
 * @since 15.07.2009
 * 1. Добавлена поддержка глобальных настроек системы (модуль main);
 *
 * @author North-E <pushkov@kolosstudio.ru>
 * @version 1.1
 * @since 10.09.2009
 * 1. Сделан читабельный вывод массивов конфигурации в файл
 * 2. Добавлена вложенность массива любого уровня
 * 3. Добавлена возможность добавления/удаления элементов конфигурационного массива любой вложенности
 */
class CConfigParser extends CBaseObject
{
	private $bReady;
	private $sFilename;
	private $sModuleName;
	public $arDBConfig;
	public $arConfig;

	/**
	 * Конструктор объекта класса для работы с конфигурационным файлом указанного модуля
	 *
	 * @param $module_name string - имя модуля конфигурационный файл которого требуется изменить
	 */
	function __construct($module_name)
	{
		$this->bReady = false;
		$this->sModuleName = $module_name;
		if($module_name=='main')
		{
			if (file_exists(ROOT_DIR . "/cnf/sys_config.php"))
			{
				$this->sFilename = ROOT_DIR . "/cnf/sys_config.php";
				$this->bReady = true;
				return;
			}
		}
		else
		{
			if (file_exists(MODULES_DIR . "/" . $module_name . "/config.php"))
			{
				$this->sFilename = MODULES_DIR . "/" . $module_name . "/config.php";
				$this->bReady = true;
				return;
			}
		}
		throw new CError("SYSTEM_CONFIG_NOT_FOUND",0);
	}

	/**
	 * Метод читает файл конфигурации модуля
	 *
	 * @return array
	 */
	function LoadConfig()
	{
		if (!$this->bReady)
			return false;

		include $this->sFilename;
		if($this->sModuleName=='main')
		{
			$this->arDBConfig=false;
			$configname = "ks_config";
			$this->arConfig = $$configname;
			if (!is_array($this->arConfig))
				$this->arConfig = array();
		}
		else
		{
			$dbname = "MODULE_" . $this->sModuleName . "_db_config";
			$this->arDBConfig = $$dbname;
			if (!is_array($this->arDBConfig))
				$this->arDBConfig = false;
			$configname = "MODULE_" . $this->sModuleName . "_config";
			$this->arConfig = $$configname;
			if (!is_array($this->arConfig))
				$this->arConfig = array();
		}
		return $this->arConfig;
	}

	/**
	 * Метод получает конфигурационный массив модуля
	 */
	function GetConfig()
	{
		return $this->arConfig;
	}

	/**
	 * Метод возвращает массив конфигураций таблиц баз данных модуля
	 */
	function GetDBConfig()
	{
		return $this->arDBConfig;
	}

	/**
	 * Метод возвращает значение переменной из конфига
	 * @param $var string - название переменной
	 */
	function Get($var)
	{
		return $this->arConfig[$var];
	}

	/**
	 * Метод устанавливает переменную для записи в конфигурационный массив
	 *
	 * @param $var string - имя переменной для записи
	 * @param $value mixed - значение переменной
	 * @param $inner_call bool - флаг внутреннего вызова
	 * @return mixed
	 */
	function Set($var, $value=null, $inner_call = false)
	{
		if(is_string($var) && $value!=null)
		{
			/* Недопустимые символы в значениях переменной */
			$bad = array('\\', "'", '"', "?>", "<?", "\.");

			$is_array = false;
			if (is_array($value))
			{
				$is_array = true;
				$result_array = array();

				if (count($value) > 0)
				{
					foreach ($value as $array_var => $array_value)
					{
						$array_value = $this->Set((string)$array_var, $array_value, true);
						$result_array[$array_var] = $array_value;
					}
				}

				if ($inner_call)
					return $result_array;

				$this->arConfig[$var] = $result_array;
			}
			else
			{
				/* Преобразовываем значение с учётом недопустимых символов */
				$value = str_replace($bad, '', $value);
			}

			/* Возвращаем результат преобразования переменной массива */
			if ($inner_call)
				return $value;

			if (!$is_array)
				$this->arConfig[$var] = $value;
		}
		elseif(is_array($var))
		{
			foreach($var as $key=>$value)
			{
				$this->Set($key,$value);
			}
		}
		elseif(is_numeric($value))
		{
			$this->arConfig[$var] = $value;
		}
		else
		{
			return false;
		}
		return true;
	}

	/**
	 * Метод выполняет установку значений переменных базы данных
	 */
	function SetDB($var,$value)
	{
		if(!$this->arDBConfig)
		{
			$this->arDBConfig=array();
		}
		$this->arDBConfig[$var]=$value;
	}

	/**
	 * Метод добавляет переменную в массив записи в конфигурационный файл
	 * (отличается от метода CConfigParser::Set тем, что можно добавлять элементы массива любой вложенности)
	 *
	 * @param string $var
	 * @param mixed $value
	 * @param array $keys
	 * @return bool
	 */
	function Put($var, $value, $keys = array())
	{
		if (!is_string($var))
			return false;

		$current_array = &$this->arConfig;
		if (count($keys) > 0)
			foreach ($keys as $key)
			{
				if (array_key_exists($key, $current_array))
					$current_array = &$current_array[$key];
			}

		/* Выполним преобразование значения переменной */
		$value = $this->Set($var, $value, true);

		$current_array[$var] = $value;

		return true;
	}

	/**
	 * Метод удаляет переменную из массива записи в конфигурационный файл
	 *
	 * @param mixed $keys Ключ или массив ключей удаляемой переменной в конфигурационном массиве
	 * @return bool
	 */
	function Remove($keys)
	{
		if (is_string($keys))
			$keys = array($keys);

		if (!is_array($keys) || count($keys) == 0)
			return false;

		/* Начинаем поиск удаляемого в массиве поля */
		$removing_variable = $keys[count($keys) - 1];
		$current_array = &$this->arConfig;
		if (count($keys) > 1)
		{
			for ($i = 0; $i < count($keys) - 1; $i++)
			{
				$key = $keys[$i];
				if (!isset($current_array[$key]))
					return false;

				$current_array = &$current_array[$key];
			}
		}

		if (array_key_exists($removing_variable, $current_array))
		{
			/* Удаляем из конфигурационного массива найденную переменную */
			unset($current_array[$removing_variable]);
			return true;
		}

		return false;
	}

	/**
	 * Метод формирует выходную строку для переменной конфигурационного файла
	 *
	 * @param string $var
	 * @param mixed $value
	 * @param int $tabs_count
	 * @return string
	 */
	function OutputVar($var, $value, $tabs_count = 0)
	{
		$tabs = "";
		$tabs_count = intval($tabs_count);
		if ($tabs_count > 0)
			$tabs = str_repeat("\t", $tabs_count);

		if (!is_array($value))
			return $tabs . "'" . $var . "' => \"" . $value . "\"";

		$output = $tabs . "'" . $var . "' => array\n";
		$output .= $tabs . "(\n";
		if (count($value) > 0)
		{
			$var_number = 0;
			foreach ($value as $array_var => $array_value)
			{
				$var_number++;
				$output .= $this->OutputVar($array_var, $array_value, $tabs_count + 1);
				if ($var_number < count($value))
					$output .= ",";
				$output .= "\n";
			}
		}
		$output .= $tabs . ")";
		return $output;
	}

	/**
	 * Метод осуществляет запись конфигурационного файла модуля
	 */
	function WriteConfig()
	{
		if (!$this->bReady)
			return false;

		$result = "<?php\n\n";

		$result .= "/**\n";
		$result .= " * Конфигурационный файл модуля \"" . $this->sModuleName . "\"\n";
		$result .= " * Последнее изменение: " . date("d.m.Y, H:i:s", time()) . "\n";
		$result .= " */\n\n";

		/* Запись конфигурационного массива */
		$var_number = 0;
		if($this->sModuleName=='main')
		{
			$result .= "\$ks_config = array\n";
		}
		else
		{
			$result .= "\$MODULE_" . $this->sModuleName . "_config = array\n";
		}
		$result .= "(\n";
  		foreach ($this->arConfig as $key => $value)
  		{
  			$var_number++;
	  		$result .= $this->OutputVar($key, $value, 1);
	  		if ($var_number < count($this->arConfig))
  				$result .= ",";
  			$result .= "\n";
	  	}
	  	$result .= ");\n";

	  	/* Запись конфигурационного массива для работы с базой данных */
		if ($this->arDBConfig)
		{
			$var_number = 0;
			$result .= "\n";
			$result .= "\$MODULE_" . $this->sModuleName . "_db_config = array\n";
			$result .= "(\n";
	  		foreach ($this->arDBConfig as $key => $value)
	  		{
	  			$var_number++;
	  			$result .= $this->OutputVar($key, $value, 1);
	  			if ($var_number < count($this->arDBConfig))
	  				$result .= ",";
	  			$result .= "\n";
	  		}
		   	$result .= ");\n";
		}

		$result .= "\n?>";

   		$size = @file_put_contents($this->sFilename, $result);
   		if ($size == 0)
   			throw new CError("SYSTEM_CONFIG_WRITE_ERROR",0);
	}
}

?>
