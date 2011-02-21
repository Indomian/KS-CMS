{config_load file=admin.conf section=main_errors}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="/admin.php?module=main&modpage=errors"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_errors#}</span></a></li>
</ul>
<h1>{#title_errors#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td width="100%">
				<span>{#small_hint_errors#}</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<form action="{get_url}" method="POST" name="form1">
{strip}
<div class="users">
    <table class="layout">
    <tr>
    	<th width="30%">
    		<a href="{get_url _CLEAR="PAGE" order=text_ident dir=$order.newdir}">{#field_text_ident#}</a>{if $order.field=='text_ident'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th width="30%">
    		<a href="{get_url _CLEAR="PAGE" order=ru dir=$order.newdir}">{#field_ru#}</a>{if $order.field=='ru'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th>
    		{#field_user#}
    	</th>
    </tr>
	{if $ITEMS!=0}
	{foreach from=$ITEMS item=oItem key=oKey name=fList}
    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    	<td>
    		{$oItem.text_ident}
    	</td>
    	<td>{$oItem.ru}</td>
    	<td>
    		<input type="text" name="user[{$oItem.text_ident}]" value="{$oItem.user|htmlspecialchars:2:"UTF-8":false}" style="width:95%"/>
    	</td>
	</tr>
	{/foreach}
	{/if}
    </table>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<div class="manage">
    <table class="layout">
    	<tr class="titles">
    		<td>
    			<input type="hidden" name="action" value="save"/>
    			{#select_locale#}<input type="text" name="locale" value="{$locale|htmlspecialchars:2:"UTF-8":false}"/>
    			<input type="submit" name="save" value="{#save#}"/>&nbsp;
    		</td>
    	</tr>
    </table>
</div>
</form>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_errors#}</dt>
	<dd>{#hint_errors#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}