<?php
$sValue=$value;
if(!preg_match('#'.$arField['option_1'].'#i',$value))
{
	throw new CError("MAIN_INVALID_FIELD", 0, $arField['title']);
}
?>