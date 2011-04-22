<script type="text/javascript" src="/js/wave/admin/options.js"></script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=wave&page=options"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_options#}</span></a></li>
</ul>
<h1>{#title_options#}</h1>
<form action="{get_url _CLEAR="ACTION"}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
	{ksTabs NAME=wave_options head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected="1"}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
						<td>{Title field="use_captcha"}</td>
						<td><input type="checkbox" name="use_captcha" value="1" {if $data.use_captcha==1}checked="checked"{/if}/></td>
					</tr>
					<tr>
						<td>{Title field="mode"}</td>
						<td><select name="mode" class="form_input">
							<option value="list"{if $data.mode=='list'} selected="selected"{/if}>{#mode_list#}</option>
							<option value="tree"{if $data.mode=='tree'} selected="selected"{/if}>{#mode_tree#}</option>
							<option value="answer"{if $data.mode=='answer'} selected="selected"{/if}>{#mode_answer#}</option>
						</select></td>
					</tr>
					<tr {if $data.mode!='tree'}style="display:none;"{/if}>
						<td>{Title field="max_depth"}</td>
						<td><input type="text" name="max_depth" size="3" value="{$data.max_depth|default:0}" class="form_input"/></td>
					</tr>
					<tr>
						<td>{Title field="use_ratings"}</td>
						<td>
							<select name="use_ratings" class="form_input">
								<option value="no"{if $data.use_ratings=='no'} selected="selected"{/if}>{#not_use_ratings#}</option>
								<option value="usefullness" {if $data.use_ratings=='usefullness'} selected="selected"{/if}>{#use_ratings_usefullness#}</option>
							</select>
						</td>
					</tr>
					<tr {if $data.use_ratings!='usefullness'}style="display:none;"{/if}>
						<td>{Title field="usefullness_useless_min"}</td>
						<td><input type="text" name="usefullness_useless_min" size="3" value="{$data.usefullness_useless_min|default:0}" class="form_input"/></td>
					</tr>
					<tr {if $data.use_ratings!='usefullness'}style="display:none;"{/if}>
						<td>{Title field="usefullness_disallow_votes_repeat"}</td>
						<td><input type="checkbox" name="usefullness_dvr" value="1" {if $data.usefullness_dvr==1}checked="checked"{/if}/></td>
					</tr>
					<tr {if $data.use_ratings!='usefullness'}style="display:none;"{/if}>
						<td>{Title field="usefullness_disallow_self_vote"}</td>
						<td><input type="checkbox" name="usefullness_dsv" value="1" {if $data.usefullness_dsv==1}checked="checked"{/if}/></td>
					</tr>
    			</table>
    		</div>
		{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_fields}{strip}
    	<div class="form" id="of">
    		<table class="layout">
				<tr>
					<th>{#header_description#}</th>
					<th>{#header_title#}</th>
					<th>{#header_show#}</th>
					<th>{#header_neccessary#}</th>
				</tr>
    			{foreach from=$fields item=oItem}
    			<tr>
    				<td>{$oItem.description}</td>
    				{assign var=temp value=field_title_`$oItem.title`}
    				<td><input type="text" name="field_title_user[{$oItem.title}]" value="{$data[$temp]|htmlspecialchars:2:"UTF-8":false|default:$oItem.description}" style="width:100%" class="form_input"/></td>
    				{assign var=temp value=field_show_`$oItem.title`}
    				<td><input type="checkbox" name="field_show_user[{$oItem.title}]" value="1" {if $data[$temp]!="0"}checked="checked"{/if}/></td>
    				{assign var=temp value=field_necessary_`$oItem.title`}
    				<td><input type="checkbox" name="field_necessary_user[{$oItem.title}]" value="1" {if $data[$temp]!="0"}checked="checked"{/if}/></td>
    			</tr>
    			{/foreach}
    		</table>
    	</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_access}{strip}
    	<div class="form">
			<table class="layout">
    			<tr>
					<th width="30%">{#header_group#}</th>
					<th width="70%">{#header_level#}</th>
				</tr>
				{foreach from=$access.groups item=oGroup}
				{assign var="checked" value=""}
				<tr>
					<td>{$oGroup.title}</td>
					<td>
						<ul class="levelSelector">
						{foreach from=$access.module key=oKey item=oItem}
							{if $access.levels[$oGroup.id].level==$oKey}{assign var="checked" value="checked=\"checked\""}{/if}
							<li {if $oKey<10}class="{if $checked!=""}access_available{else}access_denied{/if}{/if}">
							<label>
							<input type="checkbox" name="sc_groupLevel[{$oGroup.id}][]" value="{$oKey}"
								{if $oKey==10}
									{if $access.levels[$oGroup.id].level==10}
										checked="checked"
									{/if}
								{else}
									{$checked}
								{/if}
							onclick="document.obAccessLevels.onClick(this);"/> {$oItem}</label></li>
						{/foreach}
						</ul>
					</td>
				</tr>
				{/foreach}
			</table>
		</div>
    	{/strip}{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
   	</div>
</form>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_options#}</dt>
	<dd>{#hint_options#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
