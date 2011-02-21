<?php
/**
 * Smarty plugin - filesize modifier
 * 
 *
 * @package Smarty
 * @subpackage plugins
 */

/**
 * 
 *
 * @param string $size
 * @param int $precision 
 * @return string
 */
function smarty_modifier_filesize($filename, $precision=2)
{
	global $Language;
	$Language['global']['decimal_separator'] = '.';
    $Language['global']['filesize_1'] = 'b';
    $Language['global']['filesize_1024'] = 'Kb';
    $Language['global']['filesize_1048576'] = 'Mb';
    $Language['global']['filesize_1073741824'] = 'Gb';
    $Language['global']['filesize_1099511627776'] = 'Tb';
    
	$size=filesize(ROOT_DIR."/uploads/".$filename);
	
	if(!is_numeric($precision) || !is_numeric($size))
	{

		trigger_error('Modifier filesize: Invalid input params');
		return '';
	}


	$result = '?';
	$multiplier = 1;
	while ($multiplier <= 1099511627776)
	{
		if ($size/$multiplier<1) 
		{
			break;
		} else {
			$result = round($size/$multiplier, $precision).'&nbsp;'.$Language['global']['filesize_'.$multiplier];
		}

		$multiplier *= 1024;
	}
	
	
	$result = str_replace('.', $Language['global']['decimal_separator'], $result);

	return $result;
}
?>