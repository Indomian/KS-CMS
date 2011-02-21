<?php
/**
 * \file class.CPHPCache.php
 * Файл для работы с кэшированием PHP переменных
 * Файл проекта kolos-cms.
 * 
 * Создан 12.12.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.5.3
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CCache.php';

class CPHPCache extends CCache
{
	protected $data;
	/**
	 * Конструктор инициализирует кэш по заданным параметрам и возвращает объект кэша
	 */
	function __construct($cacheId,$cacheTime,$module='common')
	{
		parent::__construct($cacheId,$cacheTime,$module);
	}
	
	/**
	 * Метод возвращает true если кэш еще не истек и false если истек (или не существует)
	 */
	function Alive()
	{
		return $this->isAlive;
	}
	
	/**
	 * Метод выполняет возвращение данных из кэша
	 */
	function GetData()
	{
		if(file_exists($this->sCacheFile))
		{
			$arData=array();
			include $this->sCacheFile;
			return $data;
		}
		return false;
	}
	
	/**
	 * Метод формирует выходную строку для переменной кэша
	 * 
	 * @param string $var
	 * @param mixed $value
	 * @param int $tabs_count
	 * @return string
	 */
	private function OutputVar($var, $value, $tabs_count = 0)
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
	 * Метод выполняет сохранение данных в кэш
	 */
	function SaveToCache($data)
	{
		$this->data=$data;
		$result = "<?php\n\n";
		
		$result .= "/**\n";
		$result .= " * Кэш файл модуля \"" . $this->module . "\"\n";
		$result .= " * Последнее изменение: " . date("d.m.Y, H:i:s", time()) . "\n";
		$result .= " * Истечет: ".date("d.m.Y, H:i:s", time()+$this->cacheTime) . "\n";
		$result .= " * Ключ кэша: ".$this->cacheId."\n";
		$result .= " */\n\n";
		
		/* Запись конфигурационного массива */
		$var_number = 0;
		$result .= "\$data = array\n";
		$result .= "(\n";
  		foreach ($data as $key => $value)
  		{
  			$var_number++;
	  		$result .= $this->OutputVar($key, $value, 1);
	  		if ($var_number < count($this->data))
  				$result .= ",";
  			$result .= "\n";
	  	}
	  	$result .= ");\n";
		$result .= "\n?>";
		if(!file_exists(MODULES_DIR.'/main/cache/'.$this->module.'/'))
		{
			if(!@mkdir(MODULES_DIR.'/main/cache/'.$this->module.'/',0755,true))
			{
				throw new CError("SYSTEM_CACHE_WRITE_ERROR",1);
			}
		}
   		$size = @file_put_contents($this->sCacheFile, $result);
   		if ($size == 0)
   			throw new CError("SYSTEM_CACHE_WRITE_ERROR",0);
	}
}
?>