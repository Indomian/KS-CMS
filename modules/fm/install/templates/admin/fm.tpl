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
<form action="{get_url}" method="POST" name="form1">
<div class="manage">
	<table class="layout" style="width:auto">
		<tr>
			<td>
				<input type="submit" id="comupl" name="comupl" value="{#upload#}" />
				<input type="submit" id="comdel" name="comdel" class="check_depend" value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');" />
			</td>
		</tr>
	</table>
</div>
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
				<input type="checkbox" name="title[ALL]" value="ALL" class="checkall"/>
			</th>
			{TableHead field="title" order=$order}
			{TableHead field="date_access" order=$order}
			{TableHead field="type" order=$order}
			{TableHead field="size" order=$order}
			{TableHead field="mode" order=$order}
			<th></th>
		</tr>
		{if $fm.parent}
		<tr class="odd">
			<td></td>
			<td colspan="6">
				<img src="{#images_path#}/icons2/folder_open.gif">
				<a href="{get_url _CLEAR="a" fm_path=$fm.parent}">...</a>
			</td>
		</tr>
		{/if}
{if $fm.items!=0}
{foreach from=$fm.items item=oItem key=oKey name=fList}
    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    	<td{Highlight date=$oItem.date_access assign=highlight i=$smarty.foreach.fList.iteration}>
    		<input type="checkbox" name="title[]" value="{$oItem.title}" class="checkItem"/>
    	</td>
    	<td class="namet"{$highlight}>
			{if $oItem.type=='dir'}
				<a href="{get_url _CLEAR="a" fm_path=$oItem.folder}">
			{else}
				<a href="{get_url _CLEAR="fm_path" a=open t=$oItem.type}">
			{/if}
			{strip}
    			<img src="{#images_path#}/
					{if $oItem.type=='dir'}
						icons2/folder.gif
					{elseif $oItem.type=='tpl'}
						icons2/file.gif
					{else}
						fm/images/file_icons/file_extension_{$oItem.type}.png
					{/if}" alt="{$oItem.title}">&nbsp;
    		{/strip}
				{$oItem.title}
    		</a>
    	</td>
		<td{$highlight}>{if $oItem.date_access}{$oItem.date_access|date_format:"%d.%m.%Y %H:%M"}{else}Неизвестно{/if}</td>
		<td{$highlight}>{if $oItem.type}{$oItem.type}{else}Неизвестно{/if}</td>
		<td{$highlight}>{$oItem.size}</td>
		<td{$highlight}>{$oItem.mode}</td>
    	<td align="center"{$highlight}>
    		<div style="width:80px;">
    		
    		</div>
    	</td>
	</tr>
{/foreach}
{/if}
    </table>
    <script type="text/javascript">{$highlightScript}</script>
</div>
{/strip}
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/folder.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title#}</dt>
	<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}