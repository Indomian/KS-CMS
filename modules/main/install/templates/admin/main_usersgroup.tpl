<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<input type="button" class="add_div2" onclick="document.location='{get_url ACTION=new}';" value="{#create#}"/>
				</div>
			</td>
			<td width="100%">
				<span>{#small_hint#} <b>{$pages.TOTAL}</b>.</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
{strip}
<div class="users">
 	<table class="layout">
    	<tr>
    		<th width="0%"><a href="{get_url order=id dir=$order.newdir}">{#field_id#}</a>&nbsp;{if $order.field=='id'}<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif" width="18" height="18" alt="x" />{/if}</th>
    		<th width="100%"><a href="{get_url order=title dir=$order.newdir}">{#field_title#}</a>&nbsp;{if $order.field=='title'}<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif" width="18" height="18" alt="x" />{/if}</th>
    		<th width="0%"></th>
		</tr>
		{foreach from=$list item=oItem key=oKey name=fList}
    	<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    		<td>{$oItem.id}</td>
    		<td><a href="{get_url ACTION=edit id=$oItem.id}">{$oItem.title}</a></td>
    		<td>
				<div style="width:52px;">
    				<a href="{get_url ACTION=edit id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" /></a>
    				{if $oItem.undeletable!=1}
    				<a href="{get_url ACTION=delete id=$oItem.id}"  onclick="return confirm('{#delete_confirm#}')"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}"/></a>
    				{/if}
    			</div>
    		</td>
		</tr>
		{/foreach}
	</table>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/people.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title#}</dt>
	<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}