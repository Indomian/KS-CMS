<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=subscribe"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url _CLEAR="action id i p1"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_releases#}</span></a></li>
    {strip}
    <li>
		<a href="{get_url}">
			<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;
			<span>
				{if $data.id<0}
					{#title_add_release#} 
				{else}
					{#title_edit_release#} <b>{$data.title}</b>
				{/if}
			</span>
		</a>
	</li>
    {/strip}
</ul>
<h1>{if $data.id<0}{#title_add_release#}{else}{#title_edit_release#} {$data.title}{/if}</h1>

<form action="{get_url _CLEAR="action id"}" method="POST" enctype="multipart/form-data">
	{ksTabs NAME=releases_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}
			<input type="hidden" name="module" value="subscribe">
			<input type="hidden" name="SB_id" value="{$data.id}">
			<input type="hidden" name="action" value="save">
			<div class="form">
				<table class="layout">
					<tr class="titles">
						<th width=30%><h3>{#header_field#}</h3></th>
						<th width=70%><h3>{#header_value#}</h3></th>
					</tr>
					<tr class="is_necessary_light">
						<td>{Title field="theme"}</td>
						<td><input type="text" name="SB_theme" value="{$data.theme|htmlspecialchars:2:"UTF-8":false}" style="width:98%;" class="form_input"/></td>
					</tr>
					<tr>
						<td colspan="2">{Title field="content"}</td>
					</tr>
					<tr>
						<td colspan="2"><textarea name="SB_content" style="width:100%;height:200px;" class="form_input"/>{ksParseText}{$data.content}{/ksParseText}</textarea></td>
					</tr>
					<tr class="is_necessary_light">
						<td>{Title field="release_from"}</td>
						<td><input type="text" name="SB_from" value="{$data.from|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/></td>
					</tr>
					<tr class="is_necessary_light">
						<td>{Title field="release_to"}</td>
						<td><input type="text" name="SB_to" value="{$data.to|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/></td>
					</tr>
					<tr>
						<td>{Title field="newsletter"}</td>
						<td>
							<select name="SB_newsletter" style="width:30%" class="form_input">
								<option value="-1" {if $data.newsletter==-1}selected="selected"{/if}>{#no_link#}</option>
								{foreach from=$data.newsletters item=oItem}
									<option value="{$oItem.id}" {if $oItem.id==$data.newsletter}selected="selected"{/if}>{$oItem.name}</option>
								{/foreach}
							</select>
						</td>
					</tr>
				</table>
			</div>
		{/ksTab}
		{ksTab NAME=$smarty.config.tabs_subscribes hide=1}
			<div class="form" id="suscribes" {if $data.newsletter!=-1}style="visibility:hidden;"{/if}>
				<table class="layout" id="list">
					<tr class="titles">
						<th width=30%><h3>{#header_field#}</h3></th>
						<th width=70%><h3>{#header_value#}</h3></th>
					</tr>
					<tr>
						<td>{Title field="subscribes_type1"}</td>
						<td>
							<div id="news">
								<label><input type="checkbox" id="allnews"/>&nbsp;&nbsp;{#all#}</label><br>
								{foreach from=$data.newsletters item=oItem key=oKey}
									<label><input name="SB_news[]" type="checkbox" value="{$oItem.id}" {if $oItem.select}checked{/if}/> &nbsp;&nbsp;{$oItem.name}</label><br>
								{/foreach}
							</div>
						</td>
					</tr>
					<tr>
						<td>{Title field="subscribes_type2"}</td>
						<td>
							<div id="groups">
								<label><input type="checkbox" id="allgroups"/>&nbsp;&nbsp;{#all#}</label><br>
								{foreach from=$data.groupslist item=oItem key=oKey}
									<label><input name="SB_groups[]" type="checkbox" value="{$oItem.id}" {if $oItem.select}checked{/if}/> &nbsp;&nbsp;{$oItem.title}</label><br>
								{/foreach}
							</div>
						</td>
					</tr>
					<tr>
						<td>{Title field="subscribes_type3"}</td>
						<td>
							<textarea id="lists" cols="45" name="SB_list" class="form_input" style="width:98%;height:200px;">{foreach from=$data.list item=oItem key=oKey name=List}{if !$smarty.foreach.List.last}{$oItem|cat:"\n"}{else}{$oItem}{/if}{/foreach}</textarea>
						</td>
					</tr>
				</table>
			</div>
		{/ksTab}
		{ksTab NAME=$smarty.config.tabs_encryption hide=1}
		<div class="form">
			<table class="layout">
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				<tr>
					<td>{Title field="encryption"}</td>
					<td><select name="SB_encryption" style="width:100%">
							<option value="UTF-8" {if $data.encryption=='UTF-8'}selected="selected"{/if}>UTF-8</option>
							<option value="CP1251" {if $data.encryption=='CP1251'}selected="selected"{/if}>CP-1251</option>
							<option value="KOI8-R" {if $data.encryption=='KOI8-R'}selected="selected"{/if}>ASCII</option>
						</select>
					</td>
				</tr>
			</table>
		</div>
		{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
		<div><input type="submit" name="send" value="{#send#}"/></div>
		<div><input type="submit" class="save" value="{#save#}"/></div>
	    <div><input type="submit" name="update" value="{#apply#}"/></div>
	    <div><a href="{get_url _CLEAR="action id"}" class="cancel_button">{#cancel#}</a></div>
	</div>
</form>

{include file='admin/common/hint.tpl' title=$smarty.config.title_edit description=$smarty.config.hint_edit icon="/big_icons/feedback.gif"}

<script type="text/javascript" src="/js/catsubcat/admin.js"></script>
<script type="text/javascript">
	$(document).bind("InitCalendar",function()
		{ldelim}
		$("#date_add").datetimepicker(
			{ldelim}
				dateFormat:{#date_format#},
				timeFormat:{#time_format#},
				dayNames:{#days#},
				dayNamesMin:{#daysMin#},
				dayNamesShort:{#daysShort#},
				monthNames:{#monthes#}
			{rdelim}
		);
		$("#date_add_btn").click(function()
			{ldelim}
				$("#date_add").datetimepicker('show')
			{rdelim}
		);
		{rdelim}
	);
	$(document).ready(function(){ldelim}
		$(document).trigger("InitCalendar");
		$('select[name=SB_newsletter]').change(function(){ldelim}
			if($(this).val()>0)
				$('ul.tabs2>[id$=_tab1]').hide();
			else
				$('ul.tabs2>[id$=_tab1]').show();
		{rdelim});
		$('textarea[name=SB_list]').click(function(){ldelim}
			$('#news input[type=checkbox],#groups input[type=checkbox]').attr('checked',false);
		{rdelim});
		$('#allnews').click(function(){ldelim}
			if($(this).attr('checked'))
			{ldelim}
				$('#news input[type=checkbox]').attr('checked',true);
				$('textarea[name=SB_list]').val('');
			{rdelim}
		{rdelim});
		$('#allgroups').click(function(){ldelim}
			if($(this).attr('checked'))
			{ldelim}
				$('#groups input[type=checkbox]').attr('checked',true);
				$('textarea[name=SB_list]').val('');
			{rdelim}
		{rdelim});
		$('#news input[type=checkbox]').click(function(){ldelim}
			$('#groups input[type=checkbox]').attr('checked',false);
		{rdelim});
		$('#groups input[type=checkbox]').click(function(){ldelim}
			$('#news input[type=checkbox]').attr('checked',false);
		{rdelim});
	{rdelim});
</script>