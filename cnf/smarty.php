<?
/**
 * Файл настроек смарти
 */

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$smarty->php_handling	= SMARTY_PHP_REMOVE;
$smarty->security		= true;
$smarty->security_settings=array(
	'PHP_HANDLING'    => false,
	'IF_FUNCS'        => array('array', 'list',
							'isset', 'empty',
							'count', 'sizeof',
							'in_array', 'is_array',
							'true', 'false', 'null','round',
							'array_key_exists','strlen'),
	'INCLUDE_ANY'     => false,
	'PHP_TAGS'        => false,
	'MODIFIER_FUNCS'  => array(
		'count',
		'round',
		'regex_replace',
		'json_encode',
		'count',
		'htmlspecialchars',
		'intval',
		'floatval',
		'str_repeat',
		'urlencode'
	),
	'ALLOW_CONSTANTS'  => false,
	'ALLOW_SUPER_GLOBALS' => true
);
$smarty->secure_dir = array(TEMPLATES_DIR.'/',SYS_TEMPLATES_DIR);
