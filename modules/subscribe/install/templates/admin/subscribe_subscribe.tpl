{config_load file=admin.conf section=subscribe}
{literal}
<script type="text/javascript">
	
	function checkAll(general_checkbox)
	{
		var list_table = document.getElementById('list_table');
		var input_elements = list_table.getElementsByTagName('INPUT');
		for (i = 0; i < input_elements.length; i++)
		{
			if (input_elements[i].getAttribute('type') == "checkbox")
				input_elements[i].checked = general_checkbox.checked;
		}
		isAnythingChecked();
	}
	
	function isAnythingChecked()
	{
		var isChecked = false;
		var list_table = document.getElementById('list_table');
		var input_elements = list_table.getElementsByTagName('INPUT');
		for (i = 0; i < input_elements.length; i++)
		{
			if (input_elements[i].getAttribute('type') == "checkbox")
				if (input_elements[i].checked == true)
					isChecked = true;
		}
		
		if (isChecked == true)
		{
			document.getElementById('comdel').disabled = false;
			document.getElementById('comact').disabled = false;
			document.getElementById('comdea').disabled = false;
		}
		else
		{
			document.getElementById('comdel').disabled = true;
			document.getElementById('comact').disabled = true;
			document.getElementById('comdea').disabled = true;
		}
	}
	
</script>
{/literal}
{strip}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="{get_url _CLEAR="ACTION type CSC_catid id i p1 CSC_id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_subscribe#}</span></a></li>
    {foreach from=$navChain item=oItem}
    {if $oItem.id!=0}
    <li><a href="{get_url _CLEAR="ACTION i p1 type id CSC_id" CSC_catid=$oItem.id}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$oItem.title}</span></a></li>
    {/if}
    {/foreach}
</ul>
{/strip}
<h1>{#title_subscribe#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url ACTION=new}" method="post">
					<input type="submit" class="create" value="{#add_subscribe#}"/>
					</form>
				</div>
			</td>
			
		</tr>
	</table>
</div>
{* {include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles} *}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<form action="{get_url}" method="POST" name="form1">
{strip}
<div class="users">
    <table id="list_table" class="layout">
    <tr>
      	<th><input type="checkbox" onclick="checkAll(this);" /></th>
    	<th width="5%">
    		<a href="{get_url _CLEAR="PAGE" order=id dir=$order.newdir}">ID</a>&nbsp;
	    	{if $order.field=='id'}
    			<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif" width="18" height="18" alt="x" />
    		{/if}
    	</th>
    	<th width="15%">
    		{#login#}
    	</th>
    	<th width="20%">
    		<a href="{get_url _CLEAR="PAGE" order=email dir=$order.newdir}">{#email#}</a>{if $order.field=='email'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	
    	<th width="20%">
    		<a href="{get_url _CLEAR="PAGE" order=date_add dir=$order.newdir}">{#date_add#}</a>{if $order.field=='date_add'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th width="20%">
    		<a href="{get_url _CLEAR="PAGE" order=date_active dir=$order.newdir}">{#date_active#}</a>{if $order.field=='date_active'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    	</th>
    	<th width="5%" style="text-align: center;"><a href="{get_url order=active dir=$order.newdir}">{#active_subscribe#}</a>&nbsp;{if $order.field=='active'}<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif"/>{/if}</th>
    	<th></th>
    </tr>
 	{if $dataList.SECTION.id>0}
    <tr class="odd">
    	<td></td>
    	<td colspan="6"><img src="{#images_path#}/icons2/folder_open.gif"><a href="{get_url _CLEAR="ACTION p1 i type" CSC_catid=$dataList.SECTION.parent_id}">...</a></td>
	   </tr>
 	{/if}
	{if $list!=0}
	{foreach from=$list item=oItem key=oKey name=fList}
    		<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    			<td><input name="common_{$oItem.id}" type="checkbox" onclick="isAnythingChecked();" /></th>
    			<td>{$oItem.id}</td>
    			<td><a href="{get_url _CLEAR="CU_order.*" ACTION=edit id=$oItem.id}">{if $oItem.uin}{$oItem.uin}{else}{#anonim#}{/if}</a></td>
    			<td><a href="{get_url _CLEAR="CU_order.*" ACTION=edit id=$oItem.id}">{$oItem.email}</a></td>
    			
    			<td>{$oItem.date_add|date_format:"%d.%m.%Y"}</td>
    			<td>{if $oItem.date_active}{$oItem.date_active|date_format:"%d.%m.%Y"}{else}{#no_active_subscribe#}{/if}</td>
    			<td style="text-align: center;"><img src="{#images_path#}/active{$oItem.active}.gif" border=0></td>
    			<td style="text-align: center;">
    				<div style="width:60px;">
    					<a href="{get_url _CLEAR="CU_order.*" ACTION=edit id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" /></a>
    					<a onclick="return confirm('Удалить опрос {$oItem.title}?')" href="{get_url _CLEAR="CU_order.*" ACTION=delete id=$oItem.id back_url=get_url}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
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
    			<input type="submit" id="comdel" name="comdel" disabled value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');" />&nbsp;
    			<input type="submit" id="comact" name="comact" disabled value="{#activate#}" />&nbsp;
    			<input type="submit" id="comdea" name="comdea" disabled value="{#deactivate#}" />
    			<input type="hidden" id="ACTION" name="ACTION" value="common" />
    		</td>
    	</tr>
    </table>
</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/folder.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>Управление рассылками</dt>
	<dd>Данный раздел модуля позволяет задавать новые темы рассылки, а также управлять (удалять, править, деактивировать) старыми рассылками.</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}  