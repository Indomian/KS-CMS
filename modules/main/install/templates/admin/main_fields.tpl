<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>

<h1>{#titles#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<input type="button" class="add_div2" onclick="document.location='{get_url ACTION=new}';" value="{#create#}"/>
				</div>
			</td>
			<td width="100%">
				<span>{#hint_header#}</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
{strip}
<div class="users">
	<table class="layout">
    	<tr>
    		<th width="0%">{#field_id#}</th>
    		<th width="30%">{#field_title#}</th>
    		<th width="30%">{#field_description#}</th>
    		<th width="20%">{#field_module#}</th>
    		<th width="20%">{#field_script#}</th>
    		<th></th>
		</tr>
		{if $list}
		{foreach from=$list item=oItem key=oKey name=fList}
    	<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    		<td>{$oItem.id}</td>
    		<td><a href="{get_url ACTION=edit id=$oItem.id}">{$oItem.title}</a></td>
    		<td>{$oItem.description}</td>
    		<td>{$oItem.module}</td>
    		<td>{$oItem.script}</td>
    		<td>
    			<div style="width:50px;">
    				<a href="{get_url ACTION=edit id=$oItem.id}" title="{#edit#}">
    					<img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" title="{#edit#}" />
    				</a>
    				<a href="{get_url ACTION=delete id=$oItem.id}" onclick="return confirm('{#delete_confirm#}');" title="{#delete#}">
    					<img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" title="{#delete#}" />
    				</a>
    			</div>
    		</td>
    	</tr>
		{/foreach}
		{else}
		<tr>
			<td colspan="6">{#nothing_selected#}</td>
		</tr>
		{/if}
    </table>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#titles#}</dt>
	<dd>{#hint_list#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
