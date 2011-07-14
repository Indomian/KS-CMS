{ShowEditor object="textarea[name=content]" theme="advanced"}
{strip}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="{get_url _CLEAR="action id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	{if $data.id>0}
		<li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;
			<span>{#title_edit#} {$data.text_ident}</span>
		</a></li>
	{else}
		<li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;
			<span>{#title_create#}</span>
		</a></li>
	{/if}
</ul>
{/strip}
<h1>{if $data.id>0}{#title_edit#} {$data.text_ident}{else}{#title_create#}{/if}</h1>

<form action="{get_url _CLEAR="action id"}" method="post" enctype="multipart/form-data">
	<input type="hidden" name="module" value="hints"/>
	<input type="hidden" name="id" value="{$data.id}"/>
	<input type="hidden" name="action" value="save"/>
	{ksTabs NAME=hints head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
					<tr class="titles">
						<th width="30%">{#header_field#}</th>
						<th width="70%">{#header_value#}</th>
					</tr>
					<tr class="is_necessary_light">
						<td>{Title field="text_ident"}</td>
						<td><input type="text" name="text_ident" value="{$data.text_ident|htmlspecialchars:2:"UTF-8":false}" class="form_input" style="width:98%"/></td>
					</tr>
					<tr>
						<td>{Title field="content"}</td>
						<td><textarea name="content" style="width:100%;height:300px;"/>{$data.content|escape}</textarea></td>
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
			<a href="{get_url _CLEAR="action id"}" class="cancel_button">{#cancel#}</a>
		</div>
	</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_edit#}</dt>
	<dd>{#hint_edit#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}