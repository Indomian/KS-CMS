{strip}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="action id mode"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>

<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url action=new}" method="post">
					<input type="submit" class="add_div2" value="{#create#}"/>
					</form>
				</div>
			</td>
			<td width="100%">
				<span>{#small_hint#} <b>{$pages.TOTAL}</b>. </span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}

{include file='admin/navigation_pagecounter.tpl' pages=$pages}
{strip}
<div class="users">
	<table class="layout">
		<col/>
		<col width="30%"/>
		<col width="70%"/>
		<col/>
		<tr>
			{TableHead field="id" order=$order}
			{TableHead field="text_ident" order=$order}
			{TableHead field="content" order=$order}
			<th></th>
		</tr>
		{foreach from=$list item=oItem key=oKey name=fList}
		<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			<td>{$oItem.id}</td>
			<td>
				<a href="{get_url action=edit id=$oItem.id}">{$oItem.text_ident}</a>
			</td>
			<td>{$oItem.content|strip_tags}</td>
			<td align="center">
				<div style="width:50px;">
					<a href="{get_url action=edit id=$oItem.id}" title="{#edit#}">
						<img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" />
					</a>
					<a href="{get_url action=delete id=$oItem.id}" title="{#delete#}" onclick="return confirm('{#delete_confirm#}');">
						<img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" />
					</a>
				</div>
			</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="4">
				{#nothing_selected#}
			</td>
		</tr>
		{/foreach}
	</table>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title#}</dt>
	<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}


