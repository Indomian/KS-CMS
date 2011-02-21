{config_load file=admin.conf section=templates}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="{get_url _CLEAR="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_rename#}</span></a></li>
</ul>
<h1>{#title_rename#}</h1>
<form action="{get_url _CLEAR="template id" ACTION=$action}" id="inputForm" method="POST">
	<input type="hidden" name="ACTION" value="{$action}"/>
	<input type="hidden" name="id" value="{$templateId}">
	<div class="form" style="border-top-width:1px;">
		<table border="0" width="100%" style="padding:0px;margin:0px;border:0px;">
			<tr>
				<td width="30%">{Title field="newId"}</td>
				<td width="70%"><input type="text" name="newId" value="{$templateName|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/>
				</td>
			</tr>
		</table>
	</div>
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="ACTION id type template"}" {if $smarty.get.mode=='small'}onclick="kstb_remove();"{/if} class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_rename#}</dt>
	<dd>{#hint_rename#}</dd>
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}