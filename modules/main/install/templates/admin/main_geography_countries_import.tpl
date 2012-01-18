<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=main&modpage=geography"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_countries#}</span></a></li>
	<li><a href="/admin.php?module=main&modpage=geography&action=import_countries"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_import#}</span></a></li>
</ul>
<h1>{#title_import#}</h1>
<form action="{get_url _CLEAR="action id country_id"}" method="POST">
	<input type="hidden" name="action" value="do_import_countries"/>
	{ksTabs NAME=geography_import head_class=tabs2 title_class=bold}
		{ksTab selected="1" NAME=$smarty.config.tabs_import}{strip}
		<div class="form">
			<table class="layout">
				<tr>
					<th>{#header_field#}</th>
					<th>{#header_value#}</th>
				</tr>
				<tr>
					<td>{Title field="clear"}</td>
					<td><input type="checkbox" name="clear" value="1" checked="checked"></td>
				</tr>
				<tr>
					<td>{Title field="mode"}</td>
					<td>
						<label><input type="radio" name="mode" value="self"> {#import_self#}</label><br/>
						{*<label><input type="radio" name="mode" value="file"> {#import_file#}</label>*}
					</td>
				</tr>
				{*<tr>
					<td>{Title field="file"}</td>
					<td><input type="file" name="values"></td>
				</tr>*}
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
{include file='admin/common/hint.tpl' title=$smarty.config.title_import description=$smarty.config.hint_import icon="/big_icons/settings.gif"}