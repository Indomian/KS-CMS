<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta name="description" content="{$DESCRIPTION|default:$SITE.home_descr}" />
    	<meta name="keywords" content="{$KEYWORDS|default:$SITE.home_keywrds}" />
    	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    	<title>{$SITE.home_title}{if $TITLE} | {$TITLE}{/if} innre</title>
    	<link rel="stylesheet" href="{$templates_files_folder}/{$glb_tpl}/css/default.css" type="text/css" />
		<link rel="stylesheet" href="{$templates_files_folder}/{$glb_tpl}/css/template.css" type="text/css" />
		<link rel="stylesheet" href="{$templates_files_folder}/{$glb_tpl}/css/content.css" type="text/css" />
    	<script type="text/javascript" src="/js/main.js"></script>
    	<script type="text/javascript" src="/js/jquery/jquery.js"></script>
    	{MainHeadStrings}
  	</head>
  	<body id="inner">
  		<!--[if lte IE 6]>
		<table cellspacing="0" cellpadding="0" class="table_wrap"><tr><td class="table_wrap_max_width">
		<div class="table_wrap_min_width">&nbsp;</div>
		<![endif]-->
    	<div class="wrap">
    		<!--Шапка-->
    		<div class="header"><div class="header_in">
	    		<div class="header_sitename">
					<a href="/"><img src="{$templates_files_folder}/{$glb_tpl}/images/t.gif" alt="logo"/>{$SITE.home_title}</a>
				</div>
    			<div class="header_auth">
			    </div>
    		</div></div>
    		<!-- конец шапки -->
    		<!-- основное содержимое -->
    		<div class="content"><div class="content_in">
    			<!-- центральная колонка -->
    			<div class="main"><div class="main_in">
    				{widget name=navigation action=ShowNavChain}
    				<h1>{$TITLE}</h1>
    				{$output.main_content}
    			</div></div>
    			<!-- конец центральной колонки -->
    			<!-- левая колонка -->
    			<div class="sidebar_left"><div class="sidebar_left_in">
    				{widget name=navigation action=ShowNavMenu type="left"}
    			</div></div>
    			<!-- конец левой колонки -->
    		</div></div>
			<!-- конец основного содержимого-->
			<!-- подвал -->
			<div class="footer">
				<div class="footer_in">
					<div class="footer_copyright">
						{$SITE.copyright}
					</div>
					<div class="footer_menu">
						<ul>
							{if $USER.id>0}
							<li><a href="/user/{$USER.id}/"><img src="{$templates_files_folder}/{$glb_tpl}/images/t.gif" alt="icon" />Посетить профиль</a></li>
							{/if}
       						<li><a href="{$SITE.home_url}/" onclick="indexPage.makeHomepage(this); return false;" title="Сделать стартовой"><img src="{$templates_files_folder}{$glb_tpl}/images/t.gif" alt="icon" />Сделать стартовой</a></li>
       						<li><a href="{$SITE.home_url}/" title="Добавить в избранное" onclick="indexPage.toFavorites('{$SITE.home_url}/', '{$SITE.home_title}');return false;"><img src="{$templates_files_folder}{$glb_tpl}/images/t.gif" alt="icon" />Добавить в закладки</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- конец подвала -->
		</div>
		<!--[if lte IE 6]>
		</td></tr></table>
		<![endif]-->
	</body>
</html>