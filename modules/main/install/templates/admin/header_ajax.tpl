<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta name="description" content="Система управления сайтом {$VERSION.TITLE}" />
    <meta name="keywords" content="CMS, {$VERSION.TITLE}, {$VERSION.ID}" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
    Панель администрирования {$VERSION.TITLE}
    </title>
    <link rel="stylesheet" href="/uploads/templates/admin/css/adminmain.css" type="text/css" />
 	<link rel="stylesheet" href="/uploads/templates/admin/css/interface.css" type="text/css" />
 	<link rel="stylesheet" href="/uploads/templates/admin/css/lite_interface.css" type="text/css" />
 	<link rel="stylesheet" href="/uploads/templates/admin/css/ui.all.css" type="text/css" />
 	<link rel="stylesheet" href="/uploads/templates/admin/css/imgareaselect-animated.css" type="text/css" />
	<script type="text/javascript" src="/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="/js/main/floatmessage.js"></script>
	<script type="text/javascript" src="/js/main/admin.js"></script>
	<script type="text/javascript" src="/js/tiny_mce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="/js/jquery/ui.datetimepicker.js"></script>
    <!--[if lt IE 8]><link rel=stylesheet href="css/adminmain_ie.css"><![endif]-->
    {MainHeadStrings}
    {assign var="isLight" value="1"}
  </head>
    <script type="text/javascript">
   	function dis(sender,obj) {ldelim}
		if (document.getElementById(obj).style.display == 'none')
	  	{ldelim}
   			document.getElementById(obj).style.display = 'block';
        	document.getElementById(sender).className = 'menu_arrow_up';
		{rdelim}
        else
        {ldelim}
            document.getElementById(obj).style.display = 'none';
            document.getElementById(sender).className = 'menu_arrow_down';
        {rdelim}
	{rdelim}
	$(document).ready(function(){ldelim}liteData(null,$("body").get(0)){rdelim});
    </script>
 <body>
  <div class="lite_content">
	{$last_error}
	{SysNotice}