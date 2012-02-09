{strip}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="ACTION type CSC_catid id i p1 CSC_id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    {foreach from=$navChain item=oItem}
    {if $oItem.id!=0}
    <li><a href="{get_url _CLEAR="ACTION i p1 type id CSC_id" CSC_catid=$oItem.id}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$oItem.title}</span></a></li>
    {/if}
    {/foreach}
</ul>
{/strip}
<h1>{#title#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url ACTION=new CSC_catid=$dataList.SECTION.id type=cat}" method="post">
					<input type="submit" class="add_div" value="{#add_category#}"/>
					</form>
				</div>
			</td>
			<td>
				<div>
					<form action="{get_url ACTION=new CSC_catid=$dataList.SECTION.id type=elm}" method="post">
					<input type="submit" class="create" value="{#add_element#}"/>
					</form>
				</div>
			</td>
			<td width="100%">
				<span>{#categories_found#} &ndash; <b>{$dataList.CATEGORIES}</b>, {#pages#} &ndash; <b>{$dataList.ELEMENTS}</b>.</span>
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
		<col width="45%"/>
		<col width="20%"/>
		<col width="20%"/>
		<col />
		<col />
		<tr>
			<th>
				<input type="checkbox" name="sel[ALL]" value="ALL" class="checkall"/>
			</th>
			{TableHead field="title" order=$order}
			{TableHead field="date_add" order=$order}
			{TableHead field="date_edit" order=$order}
			{TableHead field="orderation" order=$order}
			<th></th>
		</tr>
 	{if $dataList.SECTION.id>0}
    <tr class="odd">
    	<td></td>
    	<td colspan="6"><img src="{#images_path#}/icons2/folder_open.gif"><a href="{get_url _CLEAR="ACTION p1 i type" CSC_catid=$dataList.SECTION.parent_id}">...</a></td>
	   </tr>
 	{/if}
	{if $dataList.ITEMS!=0}
	{foreach from=$dataList.ITEMS item=oItem key=oKey name=fList}
    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    	<td{Highlight date=$oItem.date_add assign=highlight i=$smarty.foreach.fList.iteration}>
    		<input type="checkbox" name="sel[{$oItem.TYPE}][]" value="{$oItem.id}" class="checkItem">
    		<input type="hidden" name="title[{$oItem.id}]" value="{$oItem.title}"/>
    	</td>
    	<td class="namet"{$highlight}>
    		<div id="hint_ident_{$oItem.text_ident}" onmouseover="floatMessage.showMessage(document.getElementById('hint_ident_{$oItem.text_ident}'), '{$oItem.short_url}', 220);">
    		{strip}
    			<img src="{#images_path#}/icons2/
    				{if $oItem.TYPE=='cat' and $oItem.id!=0}
    					{if $oItem.active}
    						folder
    					{else}
    						folder_inactive
    					{/if}
    				{else}
    					{if $oItem.parent_id!=$dataList.SECTION.id}
    						included
    					{else}
	    					{if $oItem.active}
    							file
    						{else}
    							file_inactive
    						{/if}
    					{/if}
    				{/if}.gif" alt="{if $oItem.active}{#active#}{else}{#inactive#}{/if}">&nbsp;
    		{/strip}
    		{if $oItem.TYPE=='elm'}
    			<a href="{get_url ACTION=edit CSC_id=$oItem.id CSC_catid=$oItem.parent_id type=$oItem.TYPE}">
    		{else}
    			{if $oItem.id==0}
    			<a href="{get_url ACTION=edit CSC_id=$oItem.id CSC_catid=$oItem.parent_id type=$oItem.TYPE}">
    			{else}
    			<a href="{get_url _CLEAR="ACTION type" CSC_catid=$oItem.id}">
    			{/if}
			{/if}
    			{$oItem.title}
    			</a>
    		</div>
    	</td>
		<td{$highlight}>{if $oItem.date_add}{$oItem.date_add|date_format:"%d.%m.%Y %H:%M"}{else}{#not_set#}{/if}</td>
		<td{$highlight}>{if $oItem.date_edit}{$oItem.date_edit|date_format:"%d.%m.%Y %H:%M"}{else}{#not_set#}{/if}</td>
		<td{$highlight}>{$oItem.orderation}</td>
    	<td align="center"{$highlight}>
    		<div style="width:80px;">
    		{if $oItem.TYPE=='elm'}
    			<a href="{get_url ACTION=edit CSC_id=$oItem.id CSC_catid=$oItem.parent_id type=$oItem.TYPE}">
    		{else}
    			<a href="{get_url ACTION=edit CSC_catid=$oItem.id type=$oItem.TYPE}">
			{/if}
				<img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" /></a>
    		{if $oItem.TYPE=='elm'}
    			<a href="{get_url ACTION=delete CSC_id=$oItem.id CSC_catid=$oItem.parent_id type=$oItem.TYPE}" onclick="return confirm('{#delete_element_confirm#}');">
    		{else}
    			{if $oItem.id!=0}
    			<a href="{get_url ACTION=delete CSC_catid=$oItem.id type=$oItem.TYPE}" onclick="return confirm('{#delete_category_confirm#}');">
    			{/if}
			{/if}
				{if $oItem.id>0}
    			<img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
    			{/if}
    			<a href="{$oItem.full_url}" target="_blank" title="{#view#}"><img src="{#images_path#}/icons2/view.gif" alt="{#view#}" /></a>
    		</div>
    	</td>
	</tr>
{/foreach}
{/if}
    </table>
    <script type="text/javascript">{$highlightScript}</script>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}

<div class="manage">
    <table class="layout">
    	<tr class="titles">
    		<td>{#selected#}
    			<input type="submit" id="commove" name="commove" class="check_depend" value="{#move#}" onclick="return confirm('{#move_common_confirm#}')" />
    			<select id="move_selected_to" name="move_selected_to" class="check_depend" style="width: 200px;">
    			{foreach from=$tree_to_move_to key=tree_leaf_key item=tree_leaf}
    				<option value="{$tree_leaf.id}">{$tree_leaf.list_title}</option>
    			{/foreach}
    			</select>&nbsp;
    			<input type="submit" id="comdel" name="comdel" class="check_depend" value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');" />&nbsp;
    			<input type="submit" id="comact" name="comact" class="check_depend" value="{#activate#}" />&nbsp;
    			<input type="submit" id="comdea" name="comdea" class="check_depend" value="{#deactivate#}" />
    		</td>
    	</tr>
    </table>
</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/folder.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>Текстовые страницы</dt>
	<dd>Модуль предоставляет возможность управлять текстовыми разделами и страницами сайта.</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}