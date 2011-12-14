<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=main&page=modules"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_uninstall#}</span></a></li>
</ul>

<h1>{#title_uninstall#}</h1>

<form action="{get_url}" method="post">
	<input type="hidden" name="install_{$module_name}" value="1"/>
	<input type="hidden" name="CM_ACTION" value="uninstall"/>
	<div class="form">
		<table class="layout">
			<tr><th colspan="2">{#title_uninstall#} {$module_name}</th></tr>
		{if $fields!=''}
			{foreach from=$fields item=oItem key=oKey}
				{if $oItem.type=='checkbox'}
				<tr><td colspan="2"><label><input type="checkbox" name="{$oKey}" value="{$oItem.value}"/> {$oItem.title}</label></td></tr>
				{elseif $oItem.type=='label'}
				<tr><td colspan="2"><span>{$oItem.title}</span></td></tr>
				{/if}
			{/foreach}
		{/if}
		</table>
	</div>
	{if $canInstall==1}
	<div class="form_buttons">
		<div><input type="submit" name="go" value="{#delete#}"/></div>
		<div><a href="/admin.php?module=main&modpage=modules" class="cancel_button">{#cancel#}</a></div>
	</div>
	{/if}
</form>