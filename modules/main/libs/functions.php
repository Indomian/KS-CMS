<?php
/**
 * \file functions.php
 * В файле находятся различные полезные функции
 * Файл проекта kolos-cms.
 *
 * Создан 29.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Данная функция преобразует текст из кодировки win-1251 в UTF-8
 */
function win2utf($s)    {
   for($i=0, $m=strlen($s); $i<$m; $i++)    {
       $c=ord($s[$i]);
       if ($c<=127) {$t.=chr($c); continue; }
       if ($c>=192 && $c<=207)    {$t.=chr(208).chr($c-48); continue; }
       if ($c>=208 && $c<=239) {$t.=chr(208).chr($c-48); continue; }
       if ($c>=240 && $c<=255) {$t.=chr(209).chr($c-112); continue; }
       if ($c==184) { $t.=chr(209).chr(209); continue; };
            if ($c==168) { $t.=chr(208).chr(129);  continue; };
            if ($c==184) { $t.=chr(209).chr(145); continue; }; #ё
            if ($c==168) { $t.=chr(208).chr(129); continue; }; #Ё
            if ($c==179) { $t.=chr(209).chr(150); continue; }; #і
            if ($c==178) { $t.=chr(208).chr(134); continue; }; #І
            if ($c==191) { $t.=chr(209).chr(151); continue; }; #ї
            if ($c==175) { $t.=chr(208).chr(135); continue; }; #ї
            if ($c==186) { $t.=chr(209).chr(148); continue; }; #є
            if ($c==170) { $t.=chr(208).chr(132); continue; }; #Є
            if ($c==180) { $t.=chr(210).chr(145); continue; }; #ґ
            if ($c==165) { $t.=chr(210).chr(144); continue; }; #Ґ
            if ($c==184) { $t.=chr(209).chr(145); continue; }; #Ґ
   }
   return $t;
}

/**
 * Функция конвертирует десятичное число в число с любым основанием
 * http://ru2.php.net/manual/en/function.base-convert.php#52450
 */
function dec2any( $num, $base=62, $index=false ) {
    if (! $base ) {
        $base = strlen( $index );
    } else if (! $index ) {
        $index = substr( "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,0 ,$base );
    }
    $out = "";
    for ( $t = floor( log10( $num ) / log10( $base ) ); $t >= 0; $t-- ) {
        $a = floor( $num / pow( $base, $t ) );
        $out = $out . substr( $index, $a, 1 );
        $num = $num - ( $a * pow( $base, $t ) );
    }
    return $out;
}

function hex2bin($num)
{
	$arBins=array(
		'0'=>'0000',
		'1'=>'0001',
		'2'=>'0010',
		'3'=>'0011',
		'4'=>'0100',
		'5'=>'0101',
		'6'=>'0110',
		'7'=>'0111',
		'8'=>'1000',
		'9'=>'1001',
		'a'=>'1010',
		'b'=>'1011',
		'c'=>'1100',
		'd'=>'1101',
		'e'=>'1110',
		'f'=>'1111',
	);
	$len = strlen( $num ) - 1;
    for ( $t = 0; $t <= $len; $t++ )
        $out = $out.$arBins[substr($num,$t,1 )];
   	return $out;
}

function any2dec( $num, $base=62, $index=false ) {
    if (! $base ) {
        $base = strlen( $index );
    } else if (! $index ) {
        $index = substr( "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 0, $base );
    }
    $out = 0;
    $len = strlen( $num ) - 1;
    for ( $t = 0; $t <= $len; $t++ ) {
        $out = $out + strpos( $index, substr( $num, $t, 1 ) ) * pow( $base, $len - $t );
    }
    return $out;
}

function pre_print($variable)
{
	echo "<pre>";
	print_r($variable);
	echo "</pre>";
}

/**
Функция выполняет вывод значения переменной и выполняет остановку выполнения скрипта
*/
function debugStop($var)
{
	pre_print($var);
	die();
}

/**
 * Функция преобразования цвета hsv в rgb
 * Исходный код http://code.activestate.com/recipes/576554/
 * Исходный язык - python
 * Параметры
 * $h - цвет 0-360
 * $s - интенсивность 0-1
 * $v - яркость 0-1
 */
function hsv2rgb($h,$s=false,$v=false)
{
	if(is_array($h))
	{
		$s=$h[1];
		$v=$h[2];
		$h=$h[0];
	}
	$hi = floor($h/60)%6;
    $f = ($h/60) - floor($h/60);
    $p = $v * (1 - $s);
    $q = $v * (1 - ($f*$s));
    $t = $v * (1 - ((1 - $f) * $s));
    $arResult=array(
    	array('r'=>$v, 'g'=>$t, 'b'=>$p),
    	array('r'=>$q, 'g'=>$v, 'b'=>$p),
    	array('r'=>$p, 'g'=>$v, 'b'=>$t),
    	array('r'=>$p, 'g'=>$q, 'b'=>$v),
    	array('r'=>$t, 'g'=>$p, 'b'=>$v),
    	array('r'=>$v, 'g'=>$p, 'b'=>$q)
    );
    return $arResult[$hi];
}

/**
 * Функция приобразует html цвет в rgb массив.
 */
function html2rgb($html)
{
	if(preg_match('#\#?([0-9a-f]{2,2})([0-9a-f]{2,2})([0-9a-f]{2,2})#i',$html,$matches))
	{
		$r=hexdec($matches[1])/65536;
		$g=hexdec($matches[2])/65536;
		$b=hexdec($matches[3])/65536;
	}
	else
	{
		$r=0;$g=0;$b=0;
	}
	return array('r'=>$r,'g'=>$g,'b'=>$b);
}

/**
 * Функция преобразования цвета rgb в hsv
 * Исходный код http://code.activestate.com/recipes/576554/
 * Исходный язык - python
 * Параметры
 * $r - красный 0-1
 * $g - зеленый 0-1
 * $b - голубой 0-1
 */
function rgb2hsv($r,$g=false,$b=false)
{
	if(!is_array($r)&&is_string($r)&&($g===false))
	{
		if(preg_match('#\#?([0-9a-f]{2,2})([0-9a-f]{2,2})([0-9a-f]{2,2})#i',$r,$matches))
		{
			$r=hexdec($matches[1])/65536;
			$g=hexdec($matches[2])/65536;
			$b=hexdec($matches[3])/65536;
		}
		else
		{
			return array(0,0,0);
		}
	}
	elseif(is_array($r))
	{
		$g=$r[1];
		$b=$r[2];
		$r=$r[0];
	}
	$maxc = max($r, $g, $b);
    $minc = min($r, $g, $b);
    $colorMap =array(
    	$r=>'r',
    	$g=>'g',
    	$b=>'b',
    );

    if((($maxc==$minc)&&$maxc==$r)||
    	(($maxc==$minc)&&$maxc==$g)||
    	(($maxc==$minc)&&$maxc==$b))
        $h = 0;
    elseif($maxc == $r)
        $h = 60 * (($g - $b) / ($maxc - $minc)) % 360;
    elseif($maxc == $g)
        $h = 60 * (($b - $r) / ($maxc - $minc)) + 120;
    elseif($maxc == $b)
        $h = 60 * (($r - $g) / ($maxc - $minc)) + 240;
    $v = $maxc;
    if ($maxc == 0)
        $s = 0;
    else
        $s = 1 - ($minc / $maxc);
    return array($h, $s, $v);
}

/**
 * Функция проверяет, является ли указанная строка адресом электронной почты
 */
function IsEmail($email)
{
	return preg_match('#^[a-z\.0-9\-_]+@[a-z0-9\-_]+\.[a-z]+$#i',$email);
}

/**
 * Функция проверяет является ли строка url адресом;
 */
function IsUrl($url, $with_http = true){
	$http = ($with_http) ? "http:\/\/" : '';
	return preg_match("#^".$http."(\w+\.){1,}(\w){2,}#mi", $url);
}

/**
 * Функция проверяет является ли строка текстовым идентификатором;
 */
function IsTextIdent($str)
{
	return preg_match('#^[a-z0-9\-_]+$#i',$str);
}

function IsIp($ip){
	//проверим длину и вхождение посторонних символов
	if(strlen($ip)<7 || preg_match("#[^0-9\.]+#mi",$ip))return false;
	//разобьем на части
	$ip = explode('.', $ip);
	//проверим сколько кусков получилось
	if(count($ip)<4)return false;
	if(in_array('',$ip))return false;
	//проверим значения(IPv6 не рассматриваем)
	if($ip[0] == 0 || $ip[0] >= 255 || $ip[3] == 0 || $ip[3] >= 255 || $ip[1] >= 255 || $ip[2] >= 255) return false;
	return true;
}

/**
 * Функция формирует переменную для записи в файл
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
			$output .= OutputVar($array_var, $array_value, $tabs_count + 1);
			if ($var_number < count($value))
				$output .= ",";
			$output .= "\n";
		}
	}
	$output .= $tabs . ")";
	return $output;
}

/**
 * Функция сохраняет массив данных в файл
 * @param $filename - имя файла куда писать переменную
 * @param $varname - имя переменной в файле
 * @param $data - значение переменной
 */
function SaveToFile($filename,$varname,$data)
{
	$result = "<?php\n\n";
	$result .= "/**\n";
	$result .= " * Автоматически созданный файл\n";
	$result .= " * Последнее изменение: " . date("d.m.Y, H:i:s", time()) . "\n";
	$result .= " */\n\n";

	/* Запись конфигурационного массива */
	$var_number = 0;
	$result .= "$varname = array\n";
	$result .= "(\n";
	foreach ($data as $key => $value)
	{
		$var_number++;
  		$result .= OutputVar($key, $value, 1);
  		if ($var_number < count($data))
			$result .= ",";
		$result .= "\n";
  	}
  	$result .= ");\n";
	$result .= "\n?>";
	$path=dirname($filename);
	if(!file_exists($path))
	{
		if(!@mkdir($path,0755,true))
		{
			throw new CError("SYSTEM_DIR_CREATE_ERROR",1,$path);
		}
	}
	$size = @file_put_contents($filename, $result);
	if ($size == 0)
		throw new CError("SYSTEM_FILE_WRITE_ERROR",0);
}

/**
 * Функция возвращает максимальное количество памяти доступное скрипту
 */
function GetMaxMemory()
{
	$val=ini_get('memory_limit');
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/**
 * Функция урезает урл к странице
 */
function ShorterUrl($full_url,$length=50)
{
	$side_length=round(($length-3)/2);
	$full_url_length = strlen($full_url);
	if ($full_url_length > $side_length * 2)
		$short_url = substr($full_url, 0, $side_length) . "..." . substr($full_url, $full_url_length - $side_length, $side_length);
	else
		$short_url = $full_url;
	return $short_url;
}




/*!Функция MenuItem генерирует элемент меню по заданным параметрам.
	\param $name - имя пункта меню
	\param $module - модуль к которому относиться меню
	\param $href - ссылка
	\param $title - заголовок пункта меню
	\param $class - класс для отображения (или изображение для пунтка меню)
	\param $parent_module - модуль, пункт меню которого нужно разворачивать если выбран данный пункт
*/
function MenuItem($name,$module,$href="",$title="menuitem",$class="", $parent_module="")
{
	$res=Array();
	$res[$name]['module']=strtolower($module);
	$res[$name]['href']=$href;
	$res[$name]['title']=$title;
	$res[$name]['class']=$class;
	$res[$name]['parent_module']=strtolower($parent_module);
	return $res;
}

/*!Функция Lang - выводит сообщения для текущего модуля из текущей локали
	mess - код сообщения;
	module - название модуля (необязательно)
	\todo избавиться от необходимости передавать название модуля
*/
function Lang($mess,$module="")
{
	global $KS_MODULES;
	if($module=="") $module=$KS_MODULES->current;
	return $mess;
}

/**
 * Функция выполняет транслитерацию переданной строки.
 * \param $input входная строка требующая транслитерации.
 * \param $is_filename является ли переданная строка именем файла
 * \return строку где все русские буквы заменены на латинские эквиваленты.
 */

function Translit($input, $is_filename = false)
{
	$arBad=array(
		"!",
		"^",
		"%",
		"#",
		"@",
		"&",
		"*",
		"?",
		",",
		":",
		"`",
		"=",
		"\\",
		"/",
		">",
		"<",
		"|",
		"'"
	);
	$arLetters=Array("а"=>"a",
					 "б"=>"b",
					 "в"=>"v",
					 "г"=>"g",
					 "д"=>"d",
					 "е"=>"e",
					 "ё"=>"yo",
					 "ж"=>"zh",
					 "з"=>"z",
					 "и"=>"i",
					 "й"=>"ji",
					 "к"=>"k",
					 "л"=>"l",
					 "м"=>"m",
					 "н"=>"n",
					 "о"=>"o",
					 "п"=>"p",
					 "р"=>"r",
					 "с"=>"s",
					 "т"=>"t",
					 "у"=>"u",
					 "ф"=>"f",
					 "х"=>"h",
					 "ц"=>"ts",
					 "ч"=>"ch",
					 "ш"=>"sh",
					 "щ"=>"sch",
					 "ь"=>"",
					 "ы"=>"y",
					 "ъ"=>"",
					 "э"=>"e",
					 "ю"=>"yu",
					 "я"=>"ya",
					 " "=>"_",
					 "("=>"_",
					 ")"=>"_");
	if (!$is_filename)
		$arLetters["."] = "_";
	return str_replace(array_keys($arLetters),array_values($arLetters),str_replace($arBad,"",mb_strtolower($input,'UTF-8')));
}

/**
 * Функция проверяет строку на пустоту.
 * @todo Определить пустоту точнее
 */
function IsEmpty($item)
{
	return $item=='';
}

/**
 * Обертка для функции htmlspecialchars
 */
function EscapeHTML($sHtml)
{
	return htmlspecialchars($sHtml,ENT_QUOTES,'utf-8',true);
}

/**
 * Функция очишает массив от пустых элементов, в качестве аргумента принимает массив
 */
function ClearArray($ar)
{
	return array_filter($ar,'IsEmpty');
}
?>
