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
					<tr>
						<td>{Title field="title"}</td>
						<td><input type="text" name="CU_title" value="{$userdata.title|htmlspecialchars:2:"UTF-8":false}" class="form_input" style="width:80%"/></td>
					</tr>
					<tr>
						<td>{Title field="new_password"}</td>
						<td><input type="password" name="CU_password" value="" class="form_input" style="width:80%"/></td>
					</tr>
					<tr>
						<td>{Title field="repeat_password"}</td>
						<td><input type="password" name="CU_password_c" value="" class="form_input" style="width:80%"/></td>
					</tr>
					<tr>
						<td>{Title field="email"}</td>
						<td><input type="text" name="CU_email" value="{$userdata.email}" class="form_input"  style="width:80%"/></td>
					</tr>
					<tr>
						<td>{Title field="active"}</td>
						<td><select name="CU_active" style="width:100%"  class="form_input">
    						<option value="1" {if $userdata.active==1}SELECTED{/if}>[{#active#}]</option>
    						<option value="0" {if $userdata.active==0}SELECTED{/if}>[{#inactive#}]</option>
    						</select>
						</td>
					</tr>
					<tr>
						<td>{Title field="img"}</td>
						<td>
							<input type="file" name="CU_img" value="" style="width:100%"/><br/>
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
					{foreach from=$groupslist item=oItem key=oKey}
					<tr>
						<td>
							<input type="checkbox" name="CU_groups[]" value="{$oItem.id}" id="groupidcb{$oItem.id}" {if $userdata.GROUPS[$oItem.id]!=''}checked="checked"{/if}/>
						</td>
						<td>
							<label for="groupidcb{$oItem.id}">{$oItem.title}</label>
						</td>
						<td>{#from#}&nbsp;
							<input type="text" name="CU_groups_from{$oItem.id}" id="f_date_c{$oItem.id}" readonly="readonly" {if $userdata.GROUPS[$oItem.id].date_start!=0}value="{$userdata.GROUPS[$oItem.id].date_start|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if}/>
							<img src="{#images_path#}/calendar/img.gif" id="f_trigger_c{$oItem.id}" style="border: 0pt none ; cursor: pointer;" title="{#select_date#}" align="absmiddle"/>
							<script type="text/javascript">
							$(document).bind("InitCalendar",function(){ldelim}
								$("#f_date_c{$oItem.id}").datetimepicker({ldelim}dateFormat:{#date_format#},timeFormat:{#time_format#},dayNames:{#days#},dayNamesMin:{#daysMin#},dayNamesShort:{#daysShort#},monthNames:{#monthes#}{rdelim});
								$("#f_date_c1{$oItem.id}").datetimepicker({ldelim}dateFormat:{#date_format#},timeFormat:{#time_format#},dayNames:{#days#},dayNamesMin:{#daysMin#},dayNamesShort:{#daysShort#},monthNames:{#monthes#}{rdelim});
								$("#f_trigger_c{$oItem.id}").click(function(){ldelim}$("#f_date_c{$oItem.id}").datetimepicker('show'){rdelim});
								$("#f_trigger_c1{$oItem.id}").click(function(){ldelim}$("#f_date_c1{$oItem.id}").datetimepicker('show'){rdelim});
							{rdelim});
							$(document).ready(function(){ldelim}$(document).trigger("InitCalendar");{rdelim});
							</script>
						</td>
						<td>{#till#}&nbsp;
							<input type="text" name="CU_groups_to{$oItem.id}" id="f_date_c1{$oItem.id}" readonly="readonly" {if $userdata.GROUPS[$oItem.id].date_end!=0}value="{$userdata.GROUPS[$oItem.id].date_end|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if}/>
						    <img src="{#images_path#}/calendar/img.gif" id="f_trigger_c1{$oItem.id}" style="border: 0pt none ; cursor: pointer;" title="{#select_date#}" align="absmiddle"/>
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
						<th width="30%">{#header_field#}</th>
						<th width="70%" colspan="2">{#header_value#}</th>
					</tr>
					<tr>
						<td>{Title field="blocked"}</td>
						<td>{#from#}&nbsp;
							<input type="text" name="CU_blocked_from" id="f_blocked_from" readonly="readonly" {if $userdata.blocked_from!=0}value="{$userdata.blocked_from|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if}/>
							<img src="{#images_path#}/calendar/img.gif" id="t_blocked_from" style="border: 0pt none ; cursor: pointer;" title="{#select_date#}" align="absmiddle"/>
							<script type="text/javascript">
							$(document).bind("InitCalendar",function(){ldelim}
								$("#f_blocked_from").datetimepicker({ldelim}dateFormat:{#date_format#},timeFormat:{#time_format#},dayNames:{#days#},dayNamesMin:{#daysMin#},dayNamesShort:{#daysShort#},monthNames:{#monthes#}{rdelim});
								$("#f_blocked_till").datetimepicker({ldelim}dateFormat:{#date_format#},timeFormat:{#time_format#},dayNames:{#days#},dayNamesMin:{#daysMin#},dayNamesShort:{#daysShort#},monthNames:{#monthes#}{rdelim});
								$("#t_blocked_from").click(function(){ldelim}$("#f_blocked_from").datetimepicker('show'){rdelim});
								$("#t_blocked_till").click(function(){ldelim}$("#f_blocked_till").datetimepicker('show'){rdelim});
							{rdelim});
							$(document).ready(function(){ldelim}$(document).trigger("InitCalendar");{rdelim});
							</script>
						</td>
						<td>{#till#}&nbsp;
							<input type="text" name="CU_blocked_till" id="f_blocked_till" readonly="readonly" {if $userdata.blocked_till!=0}value="{$userdata.blocked_till|date_format:"%d.%m.%Y %H:%M":""}"{else}value=""{/if}/>
						    <img src="{#images_path#}/calendar/img.gif" id="t_blocked_till" style="border: 0pt none ; cursor: pointer;" title="{#select_date#}" align="absmiddle"/>
						</td></td>
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
		{if $userdata.FORUM!=''}
			{ksTab NAME=Форум}{strip}
			<input type="hidden" name="ff_id" value="{$userdata.FORUM.id}"/>
			<div class="form">
				<table class="layout">
					<tr class="titles">
						<th width=30%>Поле</th>
						<th width=70%>Значение</th>
					</tr>
					<tr>
						<td>Разрешить ББ код</td>
						<td><select name="ff_bbcode">
							<option value="1" {if $userdata.FORUM.bbcode==1}selected="selected"{/if}>{#yes#}</option>
							<option value="0" {if $userdata.FORUM.bbcode!=1}selected="selected"{/if}>{#no#}</option>
						</select>
						</td>
					</tr>
					<tr>
						<td>Разрешить HTML</td>
						<td><select name="ff_html">
							<option value="1" {if $userdata.FORUM.html==1}selected="selected"{/if}>{#yes#}</option>
							<option value="0" {if $userdata.FORUM.html!=1}selected="selected"{/if}>{#no#}</option>
						</select>
						</td>
					</tr>
					<tr>
						<td>Уведомлять о новых сообщениях</td>
						<td><select name="ff_notify">
							<option value="1" {if $userdata.FORUM.notify==1}selected="selected"{/if}>{#yes#}</option>
							<option value="0" {if $userdata.FORUM.notify!=1}selected="selected"{/if}>{#no#}</option>
						</select>
						</td>
					</tr>
					<tr>
						<td>Подпись пользователя</td>
						<td><input type="text" name="ff_signature" value="{$userdata.FORUM.signature|htmlspecialchars:2:"UTF-8":false}" style="width:100%;" class="form_input"/>
						</td>
					</tr>
				</table>
			</div>
			{/strip}{/ksTab}
		{/if}
		{if $userdata.MESSAGES!=''}
			{ksTab NAME="Личные сообщения"}{strip}
			<input type="hidden" name="mp_id" value="{$userdata.MESSAGES.id}"/>
			<div class="form">
				<table class="layout">
					<tr class="titles">
						<th width=30%>Поле</th>
						<th width=70%>Значение</th>
					</tr>
					<tr>
						<td>Разрешить личные сообщения</td>
						<td><select name="mp_active">
							<option value="1" {if $userdata.MESSAGES.active==1}selected="selected"{/if}>{#yes#}</option>
							<option value="0" {if $userdata.MESSAGES.active!=1}selected="selected"{/if}>{#no#}</option>
						</select>
						</td>
					</tr>
					<tr>
						<td>Уведомлять о новых сообщениях</td>
						<td><select name="mp_notify">
							<option value="1" {if $userdata.MESSAGES.notify==1}selected="selected"{/if}>{#yes#}</option>
							<option value="0" {if $userdata.MESSAGES.notify!=1}selected="selected"{/if}>{#no#}</option>
						</select>
						</td>
					</tr>
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