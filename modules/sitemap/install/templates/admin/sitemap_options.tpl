<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=sitemap"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="/admin.php?module=sitemap&page=options"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_options#}</span></a></li>
</ul>
<h1>{#title_options#}</h1>
<form action="{get_url _CLEAR="ACTION"}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
	{ksTabs NAME=sitemap_options head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td>{Title field="cacheTime"}</td>
    					<td><input type="input" name="sc_cacheTime"  class="form_input" value="{$data.cacheTime}"/></td>
					</tr>
				</table>
    		</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_modules}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_module#}</th>
    					<th width="30%">{#header_maxLevel#}</th>
    					<th width="30%">{#header_show#}</th>
    				</tr>
    				{foreach from=$modules key=oKey item=oItem}
    				<tr>
    					<td>{$oItem.name}</td>
    					<td>{if $oItem.maxLevel>0}
    						<select name="level[{$oItem.directory}]"  class="form_input">
    							{section name=level loop=$oItem.maxLevel}
    								<option value="{$smarty.section.level.iteration}" {if $data.modules[$oItem.directory]==$smarty.section.level.iteration}selected="selected"{/if} $>{$smarty.section.level.iteration}</option>
    							{/section}
    						</select>
    						{else}
    						{#no_inner_pages#}
    						{/if}
    					</td>
    					<td><input type="checkbox" name="show[{$oItem.directory}]" value="1" {if $data.modules[$oItem.directory]!=''}checked="checked"{/if}/></td>
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