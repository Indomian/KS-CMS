<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=main&modpage=geography"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_countries#}</span></a></li>
</ul>
<h1>{#title_countries#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<input type="button" class="add_div2" onclick="document.location='{get_url action=import_countries}';" value="{#import_countries#}"/>
				</div>
			</td>
			<td width="100%">
				<span>{#small_hint_countries#} {$pages.TOTAL}</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<div class="users">
	<table class="layout">
	    <tr>
    		<th width="100%">{#field_country_title#}</th>
    		<th width="0%"></th>
		</tr>
		{if $data}
			{foreach from=$data item=oItem key=oKey name=fList}
			<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
				<td><a href="/admin.php?module=main&modpage=geography&action=cities&country_id={$oItem.id}">{$oItem.title}</a></td>
				<td>
					<div style="width:60px; text-align: center;">
						<a href="{get_url action=edit_country country_id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" title="{#edit#}" /></a>
					</div>
				</td>
			</tr>
			{/foreach}
		{else}
		<tr>
			<td colspan="2">{#nothing_selected#}</td>
		</tr>
		{/if}
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
{include file='admin/common/hint.tpl' title=$smarty.config.title_countries description=$smarty.config.hint_countries icon="/big_icons/settings.gif"}