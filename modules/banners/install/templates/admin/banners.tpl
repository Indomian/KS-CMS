<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=banners"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			{if $rights.canEdit}
			<td>
				<div>
					<form action="{get_url action=new}" method="post">
					<input type="submit" class="create" value="{#create#}"/>
					</form>
				</div>
			</td>
			{/if}
			<td width="100%">
				<span>{#small_hint#} {$pages.TOTAL|default:0}</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<form action="{get_url}" method="POST" name="form1">
{strip}
<div class="users">
	<input type="hidden" name="ACTION" value="common"/>
	<table class="layout">
		<col width="0"/>
		<col width="30%"/>
		<col width="5%"/>
		<col width="15%"/>
		<col width="25%"/>
		<col width="25%"/>
		<col width="0"/>
	<tr>
		<th>
			<input type="checkbox" name="sel[ALL]" value="ALL" onClick="checkAll(this.form,this.checked)">
		</th>
		{TableHead field="title" order=$order}
		{TableHead field="active" order=$order}
		{TableHead field="date_add" order=$order}
		{TableHead field="client_title" order=$order}
		{TableHead field="type_title" order=$order}
		<th></th>
	</tr>
	{if $ITEMS and count($ITEMS)>0}
		{foreach from=$ITEMS item=oItem key=oKey name=fList}
		<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			<td>
				<input type="checkbox" name="sel[elm][]" value="{$oItem.id}" onclick="isAnythingChecked(this.form)">
			</td>
			<td class="namet"><a href="{get_url action=edit id=$oItem.id}">{$oItem.title}</a></td>
			<td><img src="{#images_path#}/active{$oItem.active|default:"0"}.gif" border="0" alt="{if $oItem.active==1}{#active#}{else}{#inactive#}{/if}"/></td>
			<td>{$oItem.date_add|date_format:"%d.%m.%Y %H:%M"}</td>
			<td class="namet">{$oItem.client_title|default:"-"}</td>
			<td class="namet">{$oItem.type_title}</td>
			<td align="center">
				<div style="width:80px;">
					<a href="{get_url action=edit id=$oItem.id}">
						{if not $rights.canEdit}
							<img src="{#images_path#}/icons2/view.gif" alt="{#view#}" title="{#view#}"/>
						{else}
							<img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" title="{#edit#}"/>
						{/if}
					</a>
					{if $rights.canEdit}
						<a href="{get_url action=delete id=$oItem.id}" onclick="return confirm('{#delete_confirm#}');" title="{#delete#}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
					{/if}
					{if $oItem.save_stats==1}
					<a href="{get_url action=edit id=$oItem.id}#banners_edit_12345678910_tab4" title="{#statistics#}"><img src="{#images_path#}/icons2/chart_up_color.png" alt="{#statistics#}" /></a>
					{/if}
				</div>
			</td>
		</tr>
		{/foreach}
	{else}
		<tr><td colspan="9">{#no_banners#}</td></tr>
	{/if}
	</table>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
{if $rights.canEdit}
<div class="manage">
	<table class="layout">
		<tr class="titles">
			<td>{#selected#}
				<input type="submit" id="comdel" name="comdel" value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');" />&nbsp;
			</td>
		</tr>
	</table>
</div>
{/if}
</form>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/statistics.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title#}</dt>
	<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
