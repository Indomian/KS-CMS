<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta name="description" content="{#control_system#} {$VERSION.TITLE}" />
		<meta name="keywords" content="CMS, {$VERSION.TITLE}, {$VERSION.ID}" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>{#control_panel#} {$VERSION.TITLE}</title>
		<link rel="stylesheet" href="/uploads/templates/admin/css/adminmain.css" type="text/css" />
		<link rel="stylesheet" href="/uploads/templates/admin/css/interface.css" type="text/css" />
		<link rel="stylesheet" href="/uploads/templates/admin/css/thickbox.css" type="text/css" />
		<link rel="stylesheet" href="/uploads/templates/admin/css/ui.all.css" type="text/css" />
		<link rel="stylesheet" href="/uploads/templates/admin/css/imgareaselect-animated.css" type="text/css" />
		<script type="text/javascript" src="/js/jquery/jquery.js"></script>
		<script type="text/javascript" src="/js/main/floatmessage.js"></script>
		<script type="text/javascript" src="/js/main/admin.js"></script>
		<script type="text/javascript" src="/js/main/ksWindow.js"></script>
		{*Здесь подключается тинимце*}
		<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="/js/tiny_mce/jquery.tinymce.js"></script>
		<!--[if lt IE 8]><link rel=stylesheet href="css/adminmain_ie.css"><![endif]-->
		<script type="text/javascript" src="/js/jquery/ui.datetimepicker.js"></script>
		<!-- header scripts -->
		{MainHeadStrings}
	</head>
	<body>
		<script type="text/javascript"><!--
		window.ksLogoutTimeout={if $ks_config.user_inactive_time==0 || $ks_config.user_inactive_time==''}3240000{else}{$ks_config.user_inactive_time*900}{/if};
		//--></script>
		<div id="Ruler">&nbsp;</div>
		<div class="wrap">
			<table class="layout_nw" width="100%">
				<tr>
					<td class="sidebar_td" valign="top">
						<div class="sidebar">
							<div class="name">{#control_system#}</div>
							<div class="logo"><a href="/admin.php"><img src="{#images_path#}/logo.gif" alt="{$VERSION.TITLE}" height="67" width="249" /></a></div>
							<div class="menu">
								{if $module.current=="main" AND $modpage=="lite"}
									{#tree_view_help#}
								{else}
								<ul>
									{foreach from=$left_menu key=oKey item=oItem name=menu}
									<li>
										<a class="menu_link {$oItem.class}" href="/admin.php?{$oItem.href}">{$oItem.title}</a>
										<ul id="b{$smarty.foreach.menu.iteration}" style="{if $module.current!=$oItem.module}display: none;{/if}">
											{foreach from=$oItem.items key=soKey item=soItem}
											<li><img src="{#images_path#}/icons_menu/{$soItem.class}" alt="icon" />&nbsp;<a href="/admin.php?{$soItem.href}">{$soItem.title}</a></li>
											{/foreach}
										</ul>
										<span class="menu_arrow_down" id="i{$smarty.foreach.menu.iteration}" onclick="dis('i{$smarty.foreach.menu.iteration}', 'b{$smarty.foreach.menu.iteration}'); return false;">&nbsp;</span>
									</li>
									{/foreach}
								</ul>
								{/if}
							</div>
						</div>
					</td>
					<td valign="top" width="100%">
						<div class="top_menu">
							<ul style="left: -5px;">
								<li class="u_s{if $modpage!='users' and $modpage!='lite'}_a{/if}"><a href="/admin.php?module=main&modpage=main">{#top_menu_1#}</a></li>
								{if $bShowTreeView=='Y'}
								<li class="u_p{if $modpage=='users'}_a{/if}"><a href="/admin.php?module=main&modpage=users">{#top_menu_2#}</a></li>
								<li class="u_b{if $modpage=='lite'}_a{/if}"><a href="/admin.php?module=main&modpage=lite">{#top_menu_3#}</a></li>
								{else}
								<li class="u_pl{if $modpage=='users'}_a{/if}"><a href="/admin.php?module=main&modpage=users">{#top_menu_2#}</a></li>
								{/if}
							</ul>
							<div class="hint">
							{if $showHelp=='Y'}
								<a href="/admin.php?module=help&{$help_url}"><img src="{#images_path#}/bulb.gif" alt="{#hint#}" height="66" width="122" /></a>
							{else}
								<a href="mailto:{$helpEmail}"><img src="{#images_path#}/bulb.gif" alt="{#ask_for_help#}" height="66" width="122" /></a>
							{/if}</div>
						</div>
						<div class="top_links">
							<ul>
								<li><a class="post" href="#">{$USER.GROUP_NAME}</a></li>
								<li><a href="/admin.php?module=main&modpage=users&ACTION=edit&id={$USER.id}" class="profile">{#my_profile#}</a></li>
								<li><a class="exit" href="/admin.php?CU_ACTION=logout">{#logout#}</a></li>
							</ul>
						</div>
						<div class="content">
{$last_error}
{SysNotice}