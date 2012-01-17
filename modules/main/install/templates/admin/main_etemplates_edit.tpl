<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{if $data.title==""}{#template_create#}{else}{#template_edit#} "{$data.title}"{/if}</span></a></li>
</ul>
<h1>{if $data.title==""}{#template_create#}{else}{#template_edit#} "{$data.title}"{/if}</h1>
<form action="{get_url _CLEAR="ACTION template id"}" method="POST">
	<input type="hidden" name="ACTION" value="save"/>
	<input type="hidden" name="KS_id" value="{$data.id}"/>
	<input type="hidden" name="KS_file_id" value="{$data.file_id}"/>
	{ksTabs NAME=mail_tpl_edit head_class=tabs2 title_class=bold}
		{ksTab selected="1" NAME=$smarty.config.tabs_template}{strip}
		<div class="form">
			<table class="layout">
				<tr><th colspan="2">{Title field="template_file"}</th></tr>
				<tr>
					<td colspan="2">
						<textarea name="template_file" style="width:98%;height:300px;" class="form_textarea">{$data.content}</textarea>
					</td>
				</tr>
			</table>
		</div>
		{/strip}{/ksTab}
		{ksTab NAME=$smarty.config.tabs_template_fields}{strip}
		<div class="form">
			<table class="layout">
				<tr>
					<td>{Title field="title"}</td>
					<td><input type="text" name="KS_title" value="{$data.title|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/></td>
				</tr>
				<tr>
					<td>{Title field="address"}</td>
					<td><input type="text" name="KS_address" value="{$data.address}" style="width:98%" class="form_input"/></td>
				</tr>
				<tr>
					<td>{Title field="copy"}</td>
					<td><input type="text" name="KS_copy" value="{$data.copy}" style="width:98%" class="form_input"/></td>
				</tr>
			</table>
		</div>
		{/strip}{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="ACTION type id template"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>

{include file='admin/common/hint.tpl' title=$smarty.config.titles description=$smarty.config.hint_list icon="/big_icons/settings.gif"}