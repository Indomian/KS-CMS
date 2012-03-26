<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=banners"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	<li><a href="{get_url _CLEAR="id action"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_clients#}</span></a></li>
	{strip}
	<li><a href="{get_url}">
	<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
	&nbsp;<span>
	{if $data.id<1}	{#create_client_title#}
	{else}
		{#edit_client_title#} <b>{$data.title}</b>
	{/if}</span></a>
	{/strip}
</ul>
<h1>{if $data.id<1}{#create_client_title#}{else}{#edit_client_title#} "{$data.title}"{/if}</h1>
<form action="{get_url _CLEAR="ACTION CSC_id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="{$data.id}"/>
	<input type="hidden" name="OS_id" value="{$data.id}"/>
	<input type="hidden" name="action" value="save"/>
	{ksTabs NAME=banners_clients_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}
		<div class="form">
			<table class="layout">
				{if $is_ajax_frame!=1}
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				{/if}
				<tr class="is_necessary_light">
					<td>{Title field="title"}</td>
					<td><input type="text" name="OS_title" value="{$data.title|escape:"html":"UTF-8"}" style="width:98%" class="form_input"/></td>
				</tr>
				<tr>
					<td>{Title field="description"}
					<td><textarea  name="OS_description" style="width:98%;height:150px;" class="form_input">{$data.description|escape:"html":"UTF-8"}</textarea></td>
				</tr>
				<tr>
					<td>{Title field="active"}</td>
					<td><input type="checkbox" name="OS_active" value="1" {if $data.active!=0}checked="checked"{/if}/></td>
				</tr>
			</table>
		</div>
	{/ksTab}
{/ksTabs}
<div class="form_buttons">
	<div><input type="submit" class="save" value="{#save#}"/></div>
	<div><input type="submit" name="update" value="{#apply#}"/></div>
	<div><a href="{get_url _CLEAR="action id"}" class="cancel_button">{#cancel#}</a></div>
</div>
</form>
{include file='admin/common/hint.tpl' title=$smarty.config.edit_client_title description=$smarty.config.hint_edit_client icon="/big_icons/doc.gif"}
