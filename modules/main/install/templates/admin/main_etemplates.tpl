<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
</ul>
<h1>{#titles#}</h1>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<div class="users">
	<table class="layout">
	    <tr>
    		<th width="60%">{#field_event#}</th>
    		<th width="40%">{#field_template#}</th>
    		<th width="0%"></th>
		</tr>
		{if $data}
			{foreach from=$data item=oItem key=oKey name=fList}
			<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
				<td>{$oItem.title}</td>
				<td>{$oItem.file_id}</td>
				<td>
					<div style="width:60px; text-align: center;">
						<a href="{get_url _CLEAR="CU_order.*" ACTION=edit id=$oItem.file_id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" title="{#edit#}" /></a>
					</div>
				</td>
			</tr>
			{/foreach}
		{else}
		<tr>
			<td colspan="3">{#nothing_selected#}</td>
		</tr>
		{/if}
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#titles#}</dt>
<dd>{#hint_list#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}