<?php
if(strlen($value)>0)
{
	if(preg_match('#([0-9]{2,2})\.([0-9]{2,2})\.([0-9]{4,4}) ([0-9]{2,2}):([0-9]{2,2})#',$value,$time))				
		$sValue=mktime(intval($time[4]),intval($time[5]),0,intval($time[2]),intval($time[1]),intval($time[3]));
}
else
{
	$sValue=0;
}
?>