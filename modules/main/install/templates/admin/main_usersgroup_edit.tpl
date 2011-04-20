<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;
	{strip}
	{if $userdata.id>-1}
		<span>{#group_edit#} <b>{$userdata.title}</b></span>
	{else}
		<span>{#group_add#}</span>
	{/if}
	{/strip}
	</a></li>
</ul>
<h1>{if $userdata.id>-1}
		<span>{#group_edit#} <b>{$userdata.title}</b></span>
	{else}
		<span>{#group_add#}</span>
	{/if}</h1>

<form action="{get_url _CLEAR="ACTION"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="module" value="main"/>
	<input type="hidden" name="modpage" value="usergroups"/>
	<input type="hidden" name="ACTION" value="save"/>
	<input type="hidden" name="CUG_id" value="{$userdata.id}"/>
	{ksTabs NAME=usersgroups_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
    			<table class="layout">
    				<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr class="is_necessary_light">
    					<td>{Title field="title"}</td>
    					<td><input type="text" name="CUG_title"  class="form_input"  value="{$userdata.title|htmlspecialchars:2:"UTF-8":false}" style="width:98%"></td>
    				</tr>
    				<tr>
    					<td>{Title field="description"}</td>
    					<td><textarea name="CUG_description" class="form_textarea" style="width:98%; height:100px;">{$userdata.description}</textarea></td>
    				</tr>
    				<tr>
    					<td>{Title field="login_tries"}</td>
    					<td><input type="text" name="CUG_number_of_log_tries" class="form_input" style="width:100px;" value="{$userdata.number_of_log_tries|intval}"></td>
    				</tr>
    			</table>
    		</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_access}{strip}
    		<div class="form">
     			<table class="layout">
    				<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				{foreach from=$userdata.MODULES item=oItem}
    				{assign var="checked" value=""}
				{if $oItem.LEVELS}
    				<tr>
    					<td>{$oItem.name}</td>
    					<td>
    						<ul class="levelSelector">
						{foreach from=$oItem.LEVELS key=oKey item=osubItem}
							{assign var="iLevel" value=$userdata.ACCESS[$oItem.directory].level}
							{if $userdata.ACCESS[$oItem.directory]==''}{assign var="iLevel" value=10}{/if}
							{if $iLevel==$oKey}{assign var="checked" value="checked=\"checked\""}{/if}
							<li {if $oKey<10}class="{if $checked!=""}access_available{else}access_denied{/if}{/if}">
							<label>
							<input type="checkbox" name="CUG_level[{$oItem.directory}][]" value="{$oKey}"
								{if $oKey==10}
									{if $iLevel==10}
										checked="checked"
									{/if}
								{else}
									{$checked}
								{/if}
							onclick="document.obAccessLevels.onClick(this);"/> {$osubItem}</label></li>
						{/foreach}
						</ul>
    						{*<select name="CUG_level[{$oItem.directory}]" class="form_input">
    						{foreach from=$oItem.LEVELS key=oKey item=osubItem}
    							<option value="{$oKey}"  {if $userdata.ACCESS[$oItem.directory].level==$oKey}selected="selected"{/if}>{$osubItem}</option>
    						{/foreach}
    						</select>*}
   						</td>
    				</tr>
				{/if}
    				{/foreach}
    			</table>
    		</div>
    	{/strip}{/ksTab}
    {/ksTabs}
    <div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
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
	<dd>{#hint_edit#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}