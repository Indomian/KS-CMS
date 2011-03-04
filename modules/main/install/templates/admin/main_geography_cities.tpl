<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=main&modpage=geography"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_countries#}</span></a></li>
    <li><a href="{get_url _CLEAR="id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$country.title}</span></a></li>
</ul>
<h1>{#title_cities#} {$country.title}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<input type="button" class="add_div2" onclick="document.location='{get_url action=edit_city city_id=-1}';" value="{#add_city#}"/>
				</div>
			</td>
			<td width="100%">
				<span>{#small_hint_cities#} {$pages.TOTAL}</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<div class="users">
	<table class="layout">
	    <tr>
    		<th width="100%">{#field_city_title#}</th>
    		<th width="0%"></th>
		</tr>
		{if $data}
			{foreach from=$data item=oItem key=oKey name=fList}
			<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
				<td><a href="/admin.php?module=main&modpage=geography&action=edit_city&country_id={$oItem.country_id}&city_id={$oItem.id}">{$oItem.title}</a></td>
				<td>
					<div style="width:60px; text-align: center;">
						<a href="{get_url ACTION=edit_city country_id=$oItem.country_id city_id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" title="{#edit#}" /></a>
						<a href="{get_url ACTION=delete_city country_id=$oItem.country_id city_id=$oItem.id}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" title="{#delete#}" /></a>
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
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title_cities#}</dt>
<dd>{#hint_cities#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}

