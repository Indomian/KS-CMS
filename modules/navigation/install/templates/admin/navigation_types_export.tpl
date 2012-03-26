<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="{get_url _CLEAR="ACTION CSC_id CSC_catid type mode"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#export#}</span></a></li>
</ul>
<h1>{#export#}</h1>

{ksTabs NAME=nav_type_export head_class=tabs2 title_class=bold}
	{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
		<div class="form">
			<table class="layout">
   				<tr>
   					<td>
						<textarea class="form_input" style="width:98%;height:400px;">{$export|escape}</textarea>
					</td>
				</tr>
			</table>
		</div>
	{/strip}{/ksTab}
{/ksTabs}
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#export#}</dt>
<dd>{#hint_export#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
