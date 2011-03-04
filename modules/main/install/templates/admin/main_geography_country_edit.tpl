<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=main&modpage=geography"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_countries#}</span></a></li>
	<li><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />{#title_country_edit#} {$data.title}</li>
</ul>
<h1>{#title_country_edit#} {$data.title}</h1>
<form action="{get_url _CLEAR="action"}" method="POST">
	<input type="hidden" name="action" value="save_country"/>
	<input type="hidden" name="country_id" value="{$data.id}"/>
	{ksTabs NAME=geography_city_edit head_class=tabs2 title_class=bold}
		{ksTab selected="1" NAME=$smarty.config.tabs_common}{strip}
		<div class="form">
			<table class="layout">
				<tr>
					<th>{#header_field#}</th>
					<th>{#header_value#}</th>
				</tr>
				<tr class="is_necessary_light">
					<td>{Title field="title"}</td>
					<td><input type="input" style="width:98%" class="form_input" name="country_title" value="{$data.title}"/></td>
				</tr>
			</table>
		</div>
		{/strip}{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save" name="save"/>
    	</div>
    	<div>
    		<input type="submit" value="{#apply#}" name="update"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="city_id country_id action"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>


