<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=banners"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	<li><a href="{get_url _CLEAR="id action"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_types#}</span></a></li>
	{strip}
	<li><a href="{get_url}">
	<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
	&nbsp;<span>
	{if $data.id<1}	{#create_type_title#}
	{else}
		{#edit_type_title#} <b>{$data.title}</b>
	{/if}</span></a>
	{/strip}
</ul>
<h1>{if $data.id<1}{#create_type_title#}{else}{#edit_type_title#} "{$data.title}"{/if}</h1>
<form action="{get_url _CLEAR="ACTION CSC_id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="{$data.id}"/>
	<input type="hidden" name="OS_id" value="{$data.id}"/>
	<input type="hidden" name="action" value="save"/>
	{ksTabs NAME=banners_type_edit head_class=tabs2 title_class=bold}
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
				<tr class="is_necessary_light">
					<td>{Title field="text_ident"}</td>
					<td><input type="text" name="OS_text_ident" value="{$data.text_ident|escape:"html":"UTF-8"}" style="width:98%" class="form_input"/></td>
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
	{ksTab NAME=$smarty.config.tabs_icon}
		<div class="form">
			<table class="layout">
				<tr class="titles">
					<th colspan="2">{#select_icon#}</th>
				</tr>
				<tr>
					<td colspan="2" class="icons_list">
						<label><img src="{#images_path#}/icons32/layout_content.png" border="0" alt="{#banner_content#}"/><br/><input type="radio" name="OS_icon" value="{#icons_path#}/icons2/layout_content.png" {if $data.icon=="`$smarty.config.icons_path`/icons2/layout_content.png"}checked="checked"{/if}/></label>
						<label><img src="{#images_path#}/icons32/layout_header.png" border="0" alt="{#banner_header#}"/><br/><input type="radio" name="OS_icon" value="{#icons_path#}/icons2/layout_header.png" {if $data.icon=="`$smarty.config.icons_path`/icons2/layout_header.png"}checked="checked"{/if}/></label>
						<label><img src="{#images_path#}/icons32/layout_sidebar.png" border="0" alt="{#banner_sidebar#}"/><br/><input type="radio" name="OS_icon" value="{#icons_path#}/icons2/layout_sidebar.png" {if $data.icon=="`$smarty.config.icons_path`/icons2/layout_sidebar.png"}checked="checked"{/if}/></label>
						<label><img src="{#images_path#}/icons32/layout_footer.png" border="0" alt="{#banner_footer#}"/><br/><input type="radio" name="OS_icon" value="{#icons_path#}/icons2/layout_footer.png" {if $data.icon=="`$smarty.config.icons_path`/icons2/layout_footer.png"}checked="checked"{/if}/></label>
						<label><img src="{#images_path#}/icons32/layout_background.png" border="0" alt="{#banner_background#}"/><br/><input type="radio" name="OS_icon" value="{#icons_path#}/icons2/layout_background.png" {if $data.icon=="`$smarty.config.icons_path`/icons2/layout_background.png"}checked="checked"{/if}/></label>
					</td>
				</tr>
				<tr class="titles">
					<th colspan="2">{#upload_icon#}</th>
				</tr>
				<tr>
					<td width="30%">{Title field="upload_icon"}</td>
					<td>
						<input type="file" name="F_icon" class="form_input"/>
					</td>
				</tr>
				{if $data.icon!=''}
				<tr>
					<td>{Title field="current_icon"}</td>
					<td><img src="/uploads{$data.icon}" border="0" alt="{#icon#}"/></td>
				</tr>
				{/if}
			</table>
		</div>
	{/ksTab}
	{if $addFields && count($addFields)>0}
		{ksTab NAME=$smarty.config.tabs_userfields}
			<div class="form">
				<table class="layout">
					{if $is_ajax_frame!=1}
					<tr class="titles">
						<th width=30%><h3>{#header_field#}</h3></th>
						<th width=70%><h3>{#header_value#}</h3></th>
					</tr>
					{/if}
					{foreach from=$addFields item=oItem}
					<tr>
						<td>{$oItem.description}</td>
						{assign var=value value=ext_`$oItem.title`}
						<td>{showField field=$oItem value=$data[$value] prefix="OS_"}</td>
					</tr>
					{/foreach}
				</table>
			</div>
		{/ksTab}
	{/if}
{/ksTabs}
<div class="form_buttons">
	<div><input type="submit" class="save" value="{#save#}"/></div>
	<div><input type="submit" name="update" value="{#apply#}"/></div>
	<div><a href="{get_url _CLEAR="action id"}" class="cancel_button">{#cancel#}</a></div>
</div>
</form>
{include file='admin/common/hint.tpl' title=$smarty.config.edit_type_title description=$smarty.config.hint_edit_type icon="/big_icons/doc.gif"}
