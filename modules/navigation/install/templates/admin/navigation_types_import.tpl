<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="{get_url _CLEAR="ACTION CSC_id CSC_catid type mode"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#import#}</span></a></li>
</ul>
<h1>{#import#}</h1>
<form action="{get_url _CLEAR="ACTION"}" method="POST">
	<input type="hidden" name="ACTION" value="import_do"/>
	{ksTabs NAME=nav_types_import head_class=tabs2 title_class=bold}
		{ksTab selected="1" NAME=$smarty.config.tabs_import}{strip}
		<div class="form">
			<table class="layout">
				<tr>
   					<td>
						<textarea class="form_input" name="importCode" style="width:98%;height:400px;">{$export|escape}</textarea>
					</td>
				</tr>
			</table>
		</div>
		{/strip}{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#import#}" class="save"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="action type id template"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#import#}</dt>
<dd>{#hint_import#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}

