<?php
$sResult='';
if($params['value']!='')
{
	$ext=substr(basename($params['value']),strrpos(basename($params['value']),".")+1);
	if(in_array(strtolower($ext),array('jpeg','jpg','gif','bmp','png')))
	{
		$sResult='<img src="/uploads'.$params['value'].'"/><br/>';
	}
	else
	{
		$sResult='<a href="/uploads'.$params['value'].'">Скачать '.basename($params['value']).'</a><br/>';
	}
}
$sResult.="<input type=\"file\" class=\"form_input\" id=\"".$params['prefix']."ext_".$params['field']['title']."\" name=\"".$params['prefix']."ext_".$params['field']['title']."\">";
if($params['value']!='')
{
	$sResult.="<br/><input type=\"checkbox\" name=\"".$params['prefix']."ext_".$params['field']['title']."_del\" value=\"1\"/> Удалить";
	$sResult.="<input type=\"hidden\" name=\"".$params['prefix']."ext_".$params['field']['title']."_path\" value=\"".$params['value']."\"/>";
}
?>
