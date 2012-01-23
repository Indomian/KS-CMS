{strip}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=subscribe"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	<li><a href="{get_url _CLEAR="action id i p1"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_subscribe#}</span></a></li>
</ul>
{/strip}
<h1>{#title_subscribe#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url action=new}" method="post">
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
		<col />
		<col width="5%"/>
		<col width="15%"/>
		<col width="20%"/>
		<col width="20%"/>
		<col width="20%"/>
		<col width="5%"/>
		<col />
		<tr>
			<th>
				<input type="checkbox" name="sel[ALL]" value="ALL" class="checkall"/>
			</th>
			{TableHead field="id" order=$order}
			{TableHead field="login" order=$order}
			{TableHead field="email" order=$order}
			{TableHead field="date_add" order=$order}
			{TableHead field="date_active" order=$order}
			{TableHead field="active_subscribe" order=$order}
			<th></th>
		</tr>
		{if $list!=0}
			{foreach from=$list item=oItem key=oKey name=fList}
				<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
					<td><input name="common[{$oItem.id}]" value="1" type="checkbox" class="checkItem"/></th>
					<td>{$oItem.id}</td>
					<td><a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}">{if $oItem.uin>0}{$oItem.title}{else}{#anonim#}{/if}</a></td>
					<td>{$oItem.email}</td>
					<td>{$oItem.date_add|date_format:"%d.%m.%Y"}</td>
					<td>{if $oItem.date_active}{$oItem.date_active|date_format:"%d.%m.%Y"}{else}{#no_active_subscribe#}{/if}</td>
					<td style="text-align: center;"><img src="{#images_path#}/active{$oItem.active}.gif" border="0" alt=""></td>
					<td style="text-align: center;">
						<div style="width:60px;">
							<a href="{get_url action=edit id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" /></a>
							<a onclick="return confirm('{#delete_confirm#}')" href="{get_url action=delete id=$oItem.id}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
						</div>
					</td>
				</tr>
			{/foreach}
		{else}
			<tr><td colspan="8">{#nothing_found#}</td></tr>
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
    			<input type="submit" id="comact" name="comact" disabled value="{#activate#}" />&nbsp;
    			<input type="submit" id="comdea" name="comdea" disabled value="{#deactivate#}" />
    			<input type="hidden" id="action" name="action" value="common" />
    		</td>
    	</tr>
    </table>
</div>
</form>
{include file='admin/common/hint.tpl' title=$smarty.config.title_subscribe description=$smarty.config.hint_subscribe icon="/big_icons/people.gif"}