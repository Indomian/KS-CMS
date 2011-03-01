<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=guestbook2"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td width="100%">
				<span>{#small_hint#} {#selected#} {$pages.TOTAL}</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<form action="{get_url}" method="POST" name="form1">
{strip}
<div class="users">
    <input type="hidden" name="ACTION" value="common">
    <table class="layout">
    <tr>
    	<th>
    		<input type="checkbox" name="sel[ALL]" value="ALL" class="checkall"/>
    	</th>
    	<th>
    		<a href="{get_url _CLEAR="PAGE" order=id dir=$order.newdir}">{#field_id#}</a>{if $order.field=='id'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th>
    		<a href="{get_url _CLEAR="PAGE" order=active dir=$order.newdir}">{#field_active#}</a>{if $order.field=='active'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th width="10%">
    		<a href="{get_url _CLEAR="PAGE" order=title dir=$order.newdir}">{#field_title#}</a>{if $order.field=='title'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th width="70%">
    		<a href="{get_url _CLEAR="PAGE" order=content dir=$order.newdir}">{#field_content#}</a>{if $order.field=='content'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th>
    		{#field_category#}
    	</th>
    	<th width="10%">
    		{#field_user_name#}
    	</th>
    	<th width="10%">
    		<a href="{get_url _CLEAR="PAGE" order=date_answer dir=$order.newdir}">{#field_date_answer#}</a>{if $order.field=='date_answer'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th></th>
    </tr>
	{if $ITEMS!=0}
	{foreach from=$ITEMS item=oItem key=oKey name=fList}
    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    	<td>
    		<input type="checkbox" name="sel[elm][]" value="{$oItem.id}" class="checkItem"/>
    	</td>
    	<td>{$oItem.id}</td>
    	<td><img src="{#images_path#}/active{$oItem.active|default:"0"}.gif" border="0" alt="{if $oItem.active==1}{#active#}{else}{#inactive#}{/if}"/></td>
    	<td class="namet">{$oItem.title}</td>
    	<td>{$oItem.content}</td>
    	{assign var=catid value=$oItem.category_id}
    	<td>{$categories[$catid]}</td>
    	<td>{if $oItem.user_id>0}<a href="/admin.php?module=main&modpage=users&ACTION=edit&id={$oItem.user_id}">{/if}{$oItem.user_name}{if $oItem.user_id>0}</a>{/if}</td>
		<td>{if $oItem.date_answer>0}{$oItem.date_answer|date_format:"%d.%m.%Y"}{else}Нет ответа{/if}</td>
    	<td align="center">
    		<div style="width:60px;">
    			<a href="{get_url action=edit id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" /></a>
    			<a href="{get_url action=delete id=$oItem.id}" onclick="return confirm('{#delete_element_confirm#}');"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
			</div>
    	</td>
	</tr>
	{/foreach}
	{else}
	<tr>
		<td colspan="9">
			{#nothing_selected#}
		</td>
	</tr>
	{/if}
    </table>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<div class="manage">
    <table class="layout">
    	<tr class="titles">
    		<td>{#selected#}
    			<input type="submit" id="comdel" name="comdel" class="check_depend" value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');" />&nbsp;
    			<input type="submit" id="comact" name="comact" class="check_depend" value="{#activate#}"/>&nbsp;
    			<input type="submit" id="comdea" name="comdea" class="check_depend" value="{#deactivate#}"/>&nbsp;
    		</td>
    	</tr>
    </table>
</div>
</form>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/people.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title#}</dt>
	<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
