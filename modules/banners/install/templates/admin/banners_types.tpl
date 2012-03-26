<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=banners"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	<li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_types#}</span></a></li>
</ul>
<h1>{#title_types#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url action=new}" method="post">
						<input type="submit" class="button_layout" value="{#add_position#}"/>
					</form>
				</div>
			</td>
			<td width="100%">
				<span>{#types_small_hint#} <b>{$pages.TOTAL|default:0}</b></span>
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
		<col width="0"/>
		<col width="5%"/>
		<col width="5%"/>
		<col width="45%"/>
		<col width="45%"/>
		<col />
		<tr>
			<th>
				<input type="checkbox" name="sel[ALL]" value="ALL" onClick="checkAll(this.form,this.checked)">
			</th>
			{TableHead field="id" order=$order}
			{TableHead field="active" order=$order}
			{TableHead field="title" order=$order}
			{TableHead field="text_ident" order=$order}
			<th></th>
		</tr>
		{if $ITEMS && count($ITEMS)>0}
			{foreach from=$ITEMS item=oItem key=oKey name=fList}
			<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
				<td>
					<input type="checkbox" name="sel[elm][]" value="{$oItem.id}" onclick="isAnythingChecked(this.form)">
				</td>
				<td>{$oItem.id}</td>
				<td><img src="{#images_path#}/active{$oItem.active|default:"0"}.gif" border="0" alt="{if $oItem.active==1}{#active#}{else}{#inactive#}{/if}"/></td>
				<td class="namet">{if $oItem.icon!=''}<img src="/uploads{$oItem.icon}" alt="{#icon#}"/>&nbsp;{/if}{$oItem.title}</td>
				<td>{$oItem.text_ident}</td>
				<td align="center">
					<div style="width:60px;">
						<a href="{get_url action=edit id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" /></a>
						<a href="{get_url action=delete id=$oItem.id}" onclick="return confirm('{#delete_element_confirm#}');"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
					</div>
				</td>
			</tr>
			{/foreach}
		{else}
			<tr><td colspan="6">{#no_types#}</td></tr>
		{/if}
	</table>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<div class="manage">
	<table class="layout">
		<tr class="titles">
			<td>{#selected#}
				<input type="submit" id="comdel" name="comdel" value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');" />&nbsp;
			</td>
		</tr>
	</table>
</div>
</form>
{include file='admin/common/hint.tpl' title=$smarty.config.title_types description=$smarty.config.hint_types icon="/big_icons/folder.gif"}

