<script type="text/javascript" src="/js/jquery/ui.datetimepicker.js"></script>
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;
	{strip}
	{if $userdata.id>0}
		<span>{#title_edit#} {$userdata.title}</span>
	{else}
		<span>{#title_create#}</span>
	{/if}
	{/strip}
	</a></li>
</ul>

<h1>{if $userdata.id>0}
		<span>{#title_edit#} {$userdata.title}</span>
	{else}
		<span>{#title_create#}</span>
	{/if}</h1>

<form action="{get_url _CLEAR="ACTION id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="module" value="main">
	<input type="hidden" name="modpage" value="users">
	<input type="hidden" name="id" value="{$userdata.id}">
	<input type="hidden" name="CU_id" value="{$userdata.id}">
	<input type="hidden" name="ACTION" value="save">
	{ksTabs NAME=users_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
					<tr class="titles">
						<th width="30%">{#header_field#}</th>
						<th width="70%">{#header_value#}</th>
					</tr>
					<tr class="is_necessary_light">
						<td>{Title field="title"}</td>
						<td><input type="text" name="CU_title" value="{$userdata.title|htmlspecialchars:2:"UTF-8":false}" class="form_input" style="width:80%"/></td>
					</tr>
					<tr>
						<td>{Title field="new_password"}</td>
						<td><input type="password" name="CU_password" value="" class="form_input" style="width:98%"/></td>
					</tr>
					<tr>
						<td>{Title field="repeat_password"}</td>
						<td><input type="password" name="CU_password_c" value="" class="form_input" style="width:98%"/></td>
					</tr>
					<tr class="is_necessary_light">
						<td>{Title field="email"}</td>
						<td><input type="text" name="CU_email" value="{$userdata.email}" class="form_input"  style="width:98%"/></td>
					</tr>
					<tr>
						<td>{Title field="active"}</td>
						<td><input type="checkbox" name="CU_active" value="1" {if $userdata.active==1}checked="checked"{/if}/></td>
					</tr>
					<tr>
						<td>{Title field="img"}</td>
						<td>
							<input type="file" name="CU_img" value="" style="width:98%"/><br/>
							{if $userdata.img!=""}<img src="/uploads/{$userdata.img}"><br/>
							<input type="checkbox" name="CU_img_del" value="1"/> {#delete#}{/if}</td>
						</td>
					</tr>
				</table>
			</div>
		{/strip}{/ksTab}
		{ksTab NAME=$smarty.config.tabs_groups}{strip}
			<div class="form">
				<table class="layout">
					<tr>
						<th colspan="4">{#select_user_groups#}</th>
					</tr>
					<tr>
						<th width="0"></th>
						<th width="30%">{#header_group_name#}</th>
						<th width="35%">{#header_from#}</th>
						<th width="35%">{#header_to#}</th>
					</tr>
					{foreach from=$groupslist item=oItem key=oKey}
					<tr>
						<td>
							<input type="checkbox" name="CU_groups[]" value="{$oItem.id}" id="groupidcb{$oItem.id}" {if $userdata.GROUPS[$oItem.id]!=''}checked="checked"{/if}/>
						</td>
						<td>
							<label for="groupidcb{$oItem.id}">{$oItem.title}</label>
						</td>
						<td>
							<div class="date_selector">
								<input type="text" name="CU_groups_from{$oItem.id}" readonly="readonly" {if $userdata.GROUPS[$oItem.id].date_start!=0}value="{$userdata.GROUPS[$oItem.id].date_start|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if} class="date_input"/>
								<img src="{#images_path#}/calendar/img.gif" title="{#select_date#}" class="date_button"/>
							</div>
						</td>
						<td>
							<div class="date_selector">
								<input type="text" name="CU_groups_to{$oItem.id}" readonly="readonly" {if $userdata.GROUPS[$oItem.id].date_end!=0}value="{$userdata.GROUPS[$oItem.id].date_end|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if} class="date_input"/>
								<img src="{#images_path#}/calendar/img.gif" id="f_trigger_c1{$oItem.id}" style="border: 0pt none ; cursor: pointer;" title="{#select_date#}" align="absmiddle" class="date_button"/>
							</div>
						</td>
					</tr>
					{/foreach}
				</table>
			</div>
		{/strip}{/ksTab}
		{ksTab NAME=$smarty.config.tabs_locks}{strip}
			<div class="form">
				<table class="layout">
					<tr>
						<th width="30%">{#header_block_type#}</th>
						<th width="35%">{#header_from#}</th>
						<th width="35%">{#header_to#}</th>
					</tr>
					<tr>
						<td>{Title field="blocked"}</td>
						<td>
							<div class="date_selector">
								<input type="text" name="CU_blocked_from" readonly="readonly" {if $userdata.blocked_from!=0}value="{$userdata.blocked_from|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if} class="date_input"/>
								<img src="{#images_path#}/calendar/img.gif" title="{#select_date#}" class="date_button"/>
							</div>
						</td>
						<td>
							<div class="date_selector">
								<input type="text" name="CU_blocked_till" readonly="readonly" {if $userdata.blocked_till!=0}value="{$userdata.blocked_till|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if} class="date_input"/>
								<img src="{#images_path#}/calendar/img.gif" title="{#select_date#}" class="date_button"/>
							</div>
						</td>
					</tr>
				</table>
			</div>
		{/strip}{/ksTab}
		{if $addFields!=''}
			{ksTab NAME=$smarty.config.tabs_userfields}{strip}
			<div class="form">
				<table class="layout">
					<tr class="titles">
						<th width=30%>{#header_field#}</th>
						<th width=70%>{#header_value#}</th>
					</tr>
					{foreach from=$addFields item=oItem}
					<tr>
						<td>{$oItem.description}</td>
						{assign var=value value=ext_`$oItem.title`}
						<td>{showField field=$oItem value=$userdata[$value] prefix="CU_"}</td>
					</tr>
					{/foreach}
				</table>
			</div>
			{/strip}{/ksTab}
		{/if}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save" name="save"/>
    	</div>
    	<div>
    		<input type="submit" value="{#apply#}" name="update"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="ACTION id"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/people.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_edit#}</dt>
	<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}

<script type="text/javascript">
{literal}
$(document).bind("InitCalendar",function(){
	$(".date_input").datetimepicker({{/literal}
			dateFormat:{#date_format#},
			timeFormat:{#time_format#},
			dayNames:{#days#},
			dayNamesMin:{#daysMin#},
			dayNamesShort:{#daysShort#},
			monthNames:{#monthes#}
	{literal}});
	$(".date_button").click(function(){
		$(this).parent().children('.date_input').datetimepicker('show')
	});
});
$(document).ready(function(){$(document).trigger("InitCalendar");});
{/literal}
</script>