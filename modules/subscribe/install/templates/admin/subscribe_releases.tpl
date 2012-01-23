{strip}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=subscribe"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url _CLEAR="action id i p1"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_releases#}</span></a></li>
</ul>
{/strip}
<h1>{#title_releases#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url action=new}" method="post">
						<input type="submit" class="create" value="{#add_release#}"/>
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
			<col width="1%"/>
			<col width="1%"/>
			<col width="25%"/>
			<col width="20%"/>
			<col width="20%"/>
			<col width="18%"/>
			<tr>
				<th><input type="checkbox" onclick="checkAll(this);" /></th>
				{TableHead field="id" order=$order}
				{TableHead field="theme" order=$order}
				{TableHead field="from" order=$order}
				<th>{#field_newsletter#}</th>
				{TableHead field="send" order=$order}
				{TableHead field="date_add" order=$order}
				<th></th>
			</tr>
			{if $list!=0}
				{foreach from=$list item=oItem key=oKey name=fList}
					<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
						<td><input name="common_{$oItem.id}" type="checkbox" onclick="isAnythingChecked();" /></th>
						<td>{if !$oItem.send}<a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}">{/if}{$oItem.id}{if !$oItem.send}</a>{/if}</td>
						<td>{if !$oItem.send}<a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}">{/if}{$oItem.theme}{if !$oItem.send}</a>{/if}</td>
						<td>{if !$oItem.send}<a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}">{/if}{$oItem.from}{if !$oItem.send}</a>{/if}</td>
						<td>{if !$oItem.send}<a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}">{/if}{if $oItem.newsletter}{$oItem.newsletter}{else}{#no_newsletter#}{/if}{if !$oItem.send}</a>{/if}</td>
						<td>{if !$oItem.send}<a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}">{/if}{if $oItem.send}{#sended#}{else}{#no_send#}{/if}{if !$oItem.send}</a>{/if}</td>
						<td>{if !$oItem.send}<a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}">{/if}{$oItem.date_add|date_format:"%d.%m.%Y"}{if !$oItem.send}</a>{/if}</td>
						<td style="text-align: center;">
							<div style="width:60px;">
								<a onclick="return confirm('{#delete_confirm#}')" href="{get_url _CLEAR="CU_order.*" action="delete" id=$oItem.id back_url=get_url}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
							</div>
						</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="8">{#nothing_found#}</td>
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
					<input type="submit" id="comdel" name="comdel" disabled value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');" />&nbsp;
					<input type="hidden" id="action" name="action" value="common" />
				</td>
			</tr>
		</table>
	</div>
</form>

{include file='admin/common/hint.tpl' title=$smarty.config.title_releases description=$smarty.config.hint_releases icon="/big_icons/feedback.gif"}