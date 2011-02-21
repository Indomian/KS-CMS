<?php
$sValue=$value;
if($arField['option_1']>0)
{
	$sValue=mb_substr($value,0,intval($arField['option_1']),'UTF-8');
}
?>