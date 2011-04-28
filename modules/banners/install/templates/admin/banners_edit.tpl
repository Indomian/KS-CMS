{config_load file=admin.conf section=banners}
{ShowEditor object="textarea[name=OS_content]" theme="advanced"}
<script type="text/javascript">
<!--
$(document).bind("InitCalendar",function(){ldelim}
	$("#f_active_from").datetimepicker({ldelim}dateFormat:{#date_format#},timeFormat:{#time_format#},dayNames:{#days#},dayNamesMin:{#daysMin#},dayNamesShort:{#daysShort#},monthNames:{#monthes#}{rdelim});
	$("#f_trigger_active_from").click(function(){ldelim}$("#f_active_from").datetimepicker('show'){rdelim});
	$("#f_active_to").datetimepicker({ldelim}dateFormat:{#date_format#},timeFormat:{#time_format#},dayNames:{#days#},dayNamesMin:{#daysMin#},dayNamesShort:{#daysShort#},monthNames:{#monthes#}{rdelim});
	$("#f_trigger_active_to").click(function(){ldelim}$("#f_active_from").datetimepicker('show'){rdelim});
{rdelim});
$(document).ready(function(){ldelim}$(document).trigger("InitCalendar");{rdelim});
//-->
</script>

<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=banners"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	{strip}
	<li><a href="{get_url}">
	<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
	&nbsp;<span>
	{if $data.id<1}	{#create_title#}
	{else}
		{#edit_title#} <b>{$data.title}</b>
	{/if}</span></a>
	{/strip}
</ul>
<h1>{if $data.id<1}{#create_title#}{else}{#edit_title#} {$data.title}{/if}</h1>
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
					<td><input type="text" name="OS_title" value="{$data.title|escape:"html":"UTF-8"}" style="width:100%"/></td>
				</tr>
				<tr>
					<td>{Title field="type_id"}</td>
					<td>
						<select name="OS_type_id">
						{foreach from=$TYPES item=oItem}
							<option value="{$oItem.id}" {if $oItem.id==$data.type_id}selected="selected"{/if}>{$oItem.title}</option>
						{/foreach}
						</select>
					</td>
				</tr>
				<tr class="is_necessary_light">
					<td>{Title field="text_ident"}</td>
					<td><input type="text" name="OS_text_ident" value="{$data.text_ident}" style="width:100%"/></td>
				</tr>
				<tr>
					<td>{Title field="active_from"}</td>
					<td>
						<input type="text" name="OS_active_from" id="f_active_from" readonly="readonly" {if $data.active_from!=0}value="{$data.active_from|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if}/>
						<img src="{#images_path#}/calendar/img.gif" id="f_trigger_active_from" style="border: 0pt none ; cursor: pointer;" title="Выбор даты с помощью календаря" align="absmiddle"/>
					</td>					
				</tr>
				<tr>
					<td>{Title field="active_to"}</td>
					<td>
						<input type="text" name="OS_active_to" id="f_active_to" readonly="readonly" {if $data.active_to!=0}value="{$data.active_to|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if}/>
						<img src="{#images_path#}/calendar/img.gif" id="f_trigger_active_to" style="border: 0pt none ; cursor: pointer;" title="Выбор даты с помощью календаря" align="absmiddle"/>
					</td>					
				</tr>
				<tr>
					<td>{Title field="content"}
					<td><textarea  name="OS_content" style="width:100%;height:150px;">{$data.content|escape:"html":"UTF-8"}</textarea></td>
				</tr>
				<tr>
					<td>{Title field="img"}
					<td>
						<input type="file" name="OS_img" value="" style="width:100%"/><br/>
						{if $data.img!=""}
							<img src="/uploads/{$data.img}"><br/>
							<input type="checkbox" name="OS_img_del" value="1"/> Удалить
						{/if}
					</td>
				</tr>
				<tr>
					<td>{Title field="href"}
					<td><input type="text" name="OS_href" value="{$data.href|escape:"html":"UTF-8"}" style="width:100%;"/></td>
				</tr>
				<tr>
					<td>{Title field="active"}</td>
					<td><input type="checkbox" name="OS_active" value="1" {if $data.active!=0}checked="checked"{/if}/></td>
				</tr>
			</table>
		</div>
	{/ksTab}
	{ksTab NAME=$smarty.config.tabs_comment}
		<div class="form">
			<table class="layout">
				<tr>
					<td><textarea name="OS_comment" style="width:100%;height:300px;">{$data.comment|escape:"html":"UTF-8"}</textarea></td>
				</tr>
			</table>
		</div>
	{/ksTab}
	{ksTab NAME=$smarty.config.tabs_targets}
		<div class="form">
			<table class="layout">
				{if $is_ajax_frame!=1}
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				{/if}
				{*<tr>
					<td>{Title field="inc_keywords"}
					<td><textarea name="OS_inc_keywords" style="width:100%;height:100px;">{$data.inc_keywords|escape:"html":"UTF-8"}</textarea></td>
				</tr>
				<tr>
					<td>{Title field="exc_keywords"}
					<td><textarea name="OS_exc_keywords" style="width:100%;height:100px;">{$data.exc_keywords|escape:"html":"UTF-8"}</textarea></td>
				</tr>*}
				<tr>
					<td>{Title field="inc_path"}
					<td><textarea name="OS_inc_path" style="width:100%;height:100px;">{$data.inc_path|escape:"html":"UTF-8"}</textarea></td>
				</tr>
				{*<tr>
					<td>{Title field="exc_path"}
					<td><textarea name="OS_exc_path" style="width:100%;height:100px;">{$data.exc_path|escape:"html":"UTF-8"}</textarea></td>
				</tr>*}
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
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_edit#}</dt>
	<dd>{#hint_edit#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
