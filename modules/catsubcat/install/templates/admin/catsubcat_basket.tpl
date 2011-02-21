<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=catsubcat"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_basket#}</span></a></li>
</ul>

<h1>{#title_basket#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form method="post" action="{get_url}">
						<input type="hidden" name="deleteAll" value="1"/>
						<input type="hidden" name="ACTION" value="common"/>
						<input type="submit" value="{#clear_busket#}" name="comdel" onclick="return confirm('{#delete_all_confirm#}');"/>
					</form>
				</div>
			</td>
			<td width="100%">
				<span>{#categories_found#}  &ndash; <b>{$dataList.CATEGORIES}</b>, {#pages#} &ndash; <b>{$dataList.ELEMENTS}</b>.</span>
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
		<col />
		<col width="40%"/>
		<col width="30%"/>
		<col width="15%"/>
		<col width="15%"/>
		<col/>
    <tr>
    	<th>
    		<input type="checkbox" name="sel[ALL]" value="ALL" class="checkall">
    	</th>
    	{TableHead field="title" order=$order}
		{TableHead field="base_url"}
		{TableHead field="deleted" order=$order}
		{TableHead field="date_add" order=$order}
    	<th></th>
    </tr>
	{if $dataList.ITEMS!=0}
	{foreach from=$dataList.ITEMS item=oItem key=oKey name=fList}
    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    	<td>
    		<input type="checkbox" name="sel[{$oItem.TYPE}][]" value="{$oItem.id}" class="checkItem"/>
    	</td>
    	<td class="namet">
    		<img src="{#images_path#}/icons2/{if $oItem.TYPE=='cat' and $oItem.id!=0}{if $oItem.active}folder{else}folder_inactive{/if}{else}{if $oItem.active}file{else}file_inactive{/if}{/if}.gif" alt="{if $oItem.active}{#active#}{else}{#inactive#}{/if}">&nbsp;
    		{$oItem.title}
    	</td>
    	<td>
    		<div id="hint_ident_{$oItem.text_ident}" onmouseover="floatMessage.showMessage(document.getElementById('hint_ident_{$oItem.text_ident}'), '{$oItem.full_url}', 220);">
    		{$oItem.short_url}
    		</div>
    	</td>
	<td>{if $oItem.deleted}{$oItem.deleted|date_format:"%d.%m.%Y %H:%M"}{else}{#unknown#}{/if}</td>
	<td>{if $oItem.date_add}{$oItem.date_add|date_format:"%d.%m.%Y %H:%M"}{else}{#unknown#}{/if}</td>
    	<td align="center">
    		<div style="width:80px;">
    		<a href="{get_url ACTION=restore CSC_id=$oItem.id type=$oItem.TYPE}"
    			{if $oItem.TYPE=='elm'}onclick="return confirm('{#restore_confirm_element#}')"
    			{else}
    			onclick="return confirm('{#restore_confirm_category#}')"
    			{/if}
    			title="{#restore_from_basket#}">
    			<img src="{#images_path#}/icons2/restore.gif" alt="{#restore_from_basket#}" />
    		</a>
    		{if $userLevel==0}
    		<a href="{get_url ACTION=delete CSC_id=$oItem.id type=$oItem.TYPE}" onclick="return confirm('{if $oItem.TYPE=='elm'}{#delete_page_confirm#}{else}{#delete_section_confirm#}{/if}?');" title="{#delete_from_basket#}">
    			<img src="{#images_path#}/icons2/delete.gif" alt="{#delete_from_basket#}" />
    		</a>
    		{/if}
    		</div>
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
    		<td>{#selected#}
    			<input type="submit" id="comres" name="comres" class="check_depend" disabled value="{#restore#}"/>
    			{if $userLevel==0}
    			<input type="submit" id="comdel" name="comdel" class="check_depend" disabled value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');" />&nbsp;
    			{/if}
    		</td>
    	</tr>
    </table>
</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/basket1.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_basket#}</dt>
	<dd>{#hint_basket#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}