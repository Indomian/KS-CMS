<!DOCTYPE html>
<html>
	<head>
		<meta name="description" content="{#control_system#} {$VERSION.TITLE}">
		<meta name="keywords" content="CMS, {$VERSION.TITLE}, {$VERSION.ID}">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>{#control_panel#} {$VERSION.TITLE}</title>
		<link rel="stylesheet" href="/uploads/templates/admin/css/adminmain.css" type="text/css">
		<link rel="stylesheet" href="/uploads/templates/admin/css/ui.all.css" type="text/css">
		<script type="text/javascript" src="/js/jquery/jquery.js"></script>
		<script type="text/javascript" src="/js/main/floatmessage.js"></script>
		<script type="text/javascript" src="/js/main/admin.js"></script>
		<script type="text/javascript" src="/js/main/ksWindow.js"></script>
		<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="/js/tiny_mce/jquery.tinymce.js"></script>
		<!--[if lt IE 8]><link rel=stylesheet href="css/adminmain_ie.css"><![endif]-->
		<script type="text/javascript" src="/js/jquery/ui.datetimepicker.js"></script>
		<!-- header scripts -->
		{MainHeadStrings}
	</head>
	<body>
		<div id="Ruler">&nbsp;</div>
		<div class="wrap">
			<table class="layout_nw">
				<tr>
					<td class="sidebar_td">
						<div class="sidebar">
							<div class="name">{#control_system#}</div>
							<div class="logo"><a href="/admin.php"><img src="{#images_path#}/logo.gif" alt="{$VERSION.TITLE}" height="67" width="249"></a></div>
							<div class="menu">
								<ul>
									{foreach from=$left_menu key=oKey item=oItem name=menu}
									<li>
										<a class="menu_link {$oItem.class}" href="/admin.php?{$oItem.href}">{$oItem.title}</a>
										<ul style="{if $module.current!=$oItem.module}display: none;{/if}">
											{foreach from=$oItem.items key=soKey item=soItem}
											<li><img src="{#images_path#}/icons_menu/{$soItem.class}" alt="{$soItem.title|escape}"><a href="/admin.php?{$soItem.href}">{$soItem.title}</a></li>
											{/foreach}
										</ul>
										<a href="#" class="menu_toggle {if $module.current!=$oItem.module}menu_arrow_down{else}menu_arrow_up{/if}">&nbsp;</a>
									</li>
									{/foreach}
								</ul>
							</div>
						</div>
					</td>
					<td>
						<div class="top_menu">
							<ul>
								<li class="u_s{if $modpage!='users'}_a{/if}"><a href="/admin.php?module=main">{#top_menu_1#}</a></li>
								<li class="u_pl{if $modpage=='users'}_a{/if}"><a href="/admin.php?module=main&amp;page=users">{#top_menu_2#}</a></li>
							</ul>
							<div class="hint">
								<a href="/admin.php?module=help" class="help_request"><img src="{#images_path#}/bulb.gif" alt="{#hint#}" height="66" width="122"></a>
							</div>
						</div>
						<div class="top_links">
							<ul>
								<li><a href="/admin.php?module=main&amp;page=users&amp;action=edit&amp;id={$USER.id}" class="profile">{#my_profile#}</a></li>
								<li><a class="exit" href="/admin.php?CU_ACTION=logout">{#logout#}</a></li>
							</ul>
						</div>
						<div class="content">
							{$last_error}
							{SysNotice}