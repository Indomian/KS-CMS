<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_options#}</span></a></li>
</ul>

<h1>{#title_options#}</h1>
<form action="{get_url _CLEAR="ACTION"}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
	{ksTabs NAME=catsubcat_options head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td>{#field_show_nav_chain#}</td>
    					<td><select name="sc_show_nav_chain" class="form_input">
								<option value="1" {if $data.show_nav_chain==1} selected="selected"{/if}>{#yes#}</option>
								<option value="0" {if $data.show_nav_chain==0} selected="selected"{/if}>{#no#}</option>
							</select></td>
					</tr>
    				<tr>
    					<td>{#field_set_title#}</td>
    					<td><select name="sc_set_title" class="form_input">
								<option value="1" {if $data.set_title==1} selected="selected"{/if}>{#yes#}</option>
								<option value="0" {if $data.set_title==0} selected="selected"{/if}>{#no#}</option>
							</select></td>
    				</tr>
    				<tr>
    					<td>{#field_title_default#}</td>
    					<td><input type="text" name="sc_title_default" class="form_input" style="width:95%" value="{$data.title_default|htmlspecialchars:2:"UTF-8":false}"/></td>
    				</tr>
    			</table>
    		</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_admin_options}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td>{Title field="admin_sort_order"}</td>
    					<td><select name="sc_admin_sort_by" class="form_input">
    					{foreach from=$adminSort key=oKey item=oItem}
    						<option value="{$oKey}" {if $data.admin_sort_by==$oKey}selected="selected"{/if}>{$oItem}</option>
    					{/foreach}
    					</select></td>
    				</tr>
    				<tr>
    					<td>{Title field="admin_sort_dir"}</td>
    					<td><select name="sc_admin_sort_dir" class="form_input">
    					<option value="asc" {if $data.admin_sort_dir=='asc'}selected="selected"{/if}>{#asc#}</option>
    					<option value="desc" {if $data.admin_sort_dir=='desc'}selected="selected"{/if}>{#desc#}</option>
    					</select></td>
    				</tr>
    			</table>
    		</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_rss_options}{strip}
    		<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td>{#field_count#}</td>
    					<td><input type="text" name="sc_count" class="form_input" style="width:100px;" value="{$data.count|intval}"/></td>
    				</tr>
    				<tr>
    					<td>{#field_sort_order#}</td>
    					<td><select name="sc_sort_by" class="form_input">
    					{foreach from=$sort key=oKey item=oItem}
    						<option value="{$oKey}" {if $data.sort_by==$oKey}selected="selected"{/if}>{$oItem}</option>
    					{/foreach}
    					</select></td>
    				</tr>
    				<tr>
    					<td>{#field_sort_dir#}</td>
    					<td><select name="sc_sort_dir" class="form_input">
    					<option value="asc" {if $data.sort_dir=='asc'}selected="selected"{/if}>{#asc#}</option>
    					<option value="desc" {if $data.sort_dir=='desc'}selected="selected"{/if}>{#desc#}</option>
    					</select></td>
    				</tr>
    				<tr>
    					<td>{#field_select_from_children#}</td>
    					<td><select name="sc_select_from_children" class="form_input">
								<option value="Y" {if $data.select_from_children=='Y'} selected="selected"{/if}>{#yes#}</option>
								<option value="N" {if $data.select_from_children!='Y'} selected="selected"{/if}>{#no#}</option>
							</select></td>
    				</tr>
				</table>
			</div>
    	{/strip}
    	{/ksTab}
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