{strip}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url clear="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				{if $level<4}
				<div>
					<form action="{get_url ACTION=new}" method="post">
					<input type="submit" class="add_div2" value="{#create#}"/>
					</form>
				</div>
				{/if}
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
<form action="{get_url}" method="POST" name="form1">
	<input type="hidden" name="ACTION" value="common"/>
	{strip}
	<div class="users">
		<table class="layout">
			{if $shortMode!='Y'}
				<col width="0%"/>
			{/if}
			<col/>
			<col width="70%"/>
			<col width="20%"/>
			<col width="5%"/>
			<col/>
			<tr>
				{if $shortMode!='Y'}
				<th width="0%">
					<input type="checkbox" name="sel[ALL]" value="ALL" onClick="checkAll(this.form,this.checked)">
				</th>
				{/if}
				{TableHead field="id" order=$order}
				{TableHead field="title" order=$order}
				{TableHead field="last_visit" order=$order}
				{TableHead field="active" order=$order}
				<th></th>
			</tr>
			{foreach from=$list item=oItem key=oKey name=fList}
			<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
				{if $shortMode!='Y'}
				<td>
					<input type="checkbox" name="sel[elm][]" value="{$oItem.id}"/>
					<input type="hidden" name="title[{$oItem.id}]" value="{$oItem.title}"/>
				</td>
				{/if}
				<td>{$oItem.id}</td>
				<td>
					<a href="{get_url _CLEAR="CU_order.*" ACTION=edit id=$oItem.id}">
						{$oItem.title}
					</a>
				</td>
				<td>{if $oItem.last_visit==0}{#never#}{else}{$oItem.last_visit|date_format:"%H:%M:%S %d.%m.%y"}{/if}</td>
				<td align="center"><img src="{#images_path#}/icons2/active{$oItem.active}.gif" border=0></td>
				<td align="center">
					<div style="width:48px;">
						{if $shortMode=='Y'}
						<a href="{get_url _CLEAR="CU_order.*" ACTION=edit id=$oItem.id}" title="{#view#}">
							<img src="{#images_path#}/icons2/view.gif" alt="{#view#}" />
						</a>
						{else}
						<a href="{get_url _CLEAR="CU_order.*" ACTION=edit id=$oItem.id}" title="{#edit#}">
							<img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" />
						</a>
						<a href="{get_url _CLEAR="CU_order.*" ACTION=delete id=$oItem.id}" onclick="return confirm('{#delete_confirm#}')" title="{#delete#}">
							<img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" />
						</a>
						{/if}
					</div>
				</td>
			</tr>
			{/foreach}
		</table>
	</div>
	{/strip}
	{include file='admin/navigation_pagecounter.tpl' pages=$pages}
	{if $shortMode!='Y'}
	<div class="manage">
		<table class="layout">
			<tr class="titles">
				<td>{#selected#}</td>
				<td><input type="submit" name="comdel" value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');"></td>
				<td><input type="submit" name="comact" value="{#activate#}"></td><td><input type="submit" name="comdea" value="{#deactivate#}"></td>
			</tr>
		</table>
	</div>
	{/if}
</form>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/people.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title#}</dt>
	<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
