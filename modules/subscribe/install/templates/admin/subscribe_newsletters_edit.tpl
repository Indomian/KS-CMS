{config_load file=admin.conf section=subscribe}
<script type="text/javascript" src="/js/catsubcat/admin.js"></script>
{ShowEditor object="textarea[name=SB_description]" theme="advanced" path=$data.URL}
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
	$(document).ready(function(){ldelim}$(document).trigger("InitCalendar");{rdelim});
</script>
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="{get_url _CLEAR="ACTION id i p1"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    {foreach from=$navChain item=oItem}
    {if $oItem.id!=0}
    <li><a href="{get_url _CLEAR="ACTION i p1 id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$oItem.title}</span></a></li>
    {/if}
    {/foreach}
    {strip}
    <li><a href="{get_url}">
    <img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
    &nbsp;<span>
    {if $data.id<0}
    	{#title_new_theme#} 
    {else}
    	{#title_edit_theme#} <b>{$data.title}</b>
    {/if}</span></a>
    {/strip}
</ul>
<h1>{if $data.id<0}
    	{#title_new_theme#}
    {else}
    	{#title_edit_theme#} {$data.title}
    {/if}</h1>

<form action="{get_url _CLEAR="ACTION id"}" method="POST" enctype="multipart/form-data">
{ksTabs NAME=newsletter_edit head_class=tabs2 title_class=bold}
	{ksTab NAME=$smarty.config.tabs_common selected=1}
	<input type="hidden" name="module" value="subscribe">
	<input type="hidden" name="SB_id" value="{$data.id}">
	<input type="hidden" name="ACTION" value="save">
	<div class="form">
    <table class="layout">
    	{if $is_ajax_frame!=1}
	    <tr class="titles">
	    	<th width=30%><h3>{#header_field#}</h3></th>
	    	<th width=70%><h3>{#header_value#}</h3></th>
	    </tr>
    	{/if}
	    <tr class="is_necessary_light">
	    	<td>{#name#}</td>
	    	<td><input type="text" name="SB_name" value="{$data.name|htmlspecialchars:2:"UTF-8":false}" style="width:100%"/></td>
	    </tr>
	    <tr>
	    	<td colspan="2">{#description#}</td>
	    </tr>
	    <tr>
	    	<td colspan="2"><textarea name="SB_description" style="width:100%;height:200px;"/>{ksParseText}{$data.description}{/ksParseText}</textarea></td>
	    </tr>
	    <tr>
    		<td>{#send_type#}</td>
    		<td><select readonly="readonly" disabled name="SB_send_type" style="width:30%">
        			<option value="2"  {if $data.send_type==2}selected="selected"{/if}>{#send_type2#}</option>
        			<option value="1"  {if $data.send_type==1}selected="selected"{/if}>{#send_type1#}</option>
        		</select>
    		</td>
    	</tr>
    	<tr>
    		<td>{#activity#}</td>
    		<td><select name="SB_active" style="width:30%">
    				<option value="1" {if $data.active==1}selected="selected"{/if}>{#active#}</option>
    				<option value="0" {if $data.active==0}selected="selected"{/if}>{#inactive#}</option>
			    </select>
    		</td>
    	</tr>
    </table>
    </div>
	{/ksTab}
	
	
	{ksTab NAME="Доступы"}
			{strip}
			<div class="form">
				<table class="layout">
					<tr>
						<th width="30%">{#header_group#}</th>
						<th width="70%">{#header_level#}</th>
					</tr>
					{foreach from=$access.usergroups key=usergroup_key item=usergroup_item}
					{assign var="checked" value=""}
					<tr>
						<td>{$usergroup_item.title}</td>
						<td>
							<ul class="levelSelector">
							{foreach from=$access.levels key=access_level item=access_level_title}
							{if $access.usergroups_levels[$usergroup_item.id].level == $access_level}
								{assign var="checked" value="checked=\"checked\""}
							{/if}
							<li {if $access_level < 10}class="{if $checked != ""}access_available{else}access_denied{/if}"{/if}>
								<label>
									<input type="checkbox" name="SB_groupLevel[{$usergroup_item.id}][]" value="{$access_level}" 
										{if $access_level==10}
											{if $access.usergroups_levels[$usergroup_item.id].level == 10} checked="checked"{/if}
										{else}
											{$checked}
										{/if}
										onclick="document.obAccessLevels.onClick(this);"/> {$access_level_title}
								</label>
							</li>
							{/foreach}
							</ul>
						</td>
					</tr>
					{/foreach}
					<tr>
						<td>
						</td>
					</tr>
				</table>
			</div>
			{/strip}
			{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
		<div><input type="submit" class="save" value="{#save#}"/></div>
	    <div><input type="submit" name="update" value="{#apply#}"/></div>
	    <div><a href="{get_url _CLEAR="ACTION id"}" class="cancel_button">{#cancel#}</a></div>
	</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title_edit#}</dt>
<dd>{#hint_edit#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}