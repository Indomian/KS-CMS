{strip}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=subscribe"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	<li><a href="{get_url _CLEAR="action type id i p1"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_newsletters#}</span></a></li>
</ul>
{/strip}
<h1>{#title_newsletters#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url action=new}" method="post">
					<input type="submit" class="create" value="{#add_theme#}"/>
					</form>
				</div>
			</td>

		</tr>
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<form action="{get_url}" method="POST" name="form1">
{strip}
<div class="users">
	<table id="list_table" class="layout">
		<col />
		<col width="37%"/>
		<col width="37%"/>
		<col width="20%"/>
		<col />
		<col />
		<tr>
			<th>
				<input type="checkbox" name="sel[ALL]" value="ALL" class="checkall"/>
			</th>
			{TableHead field="name" order=$order}
			{TableHead field="description" order=$order}
			{TableHead field="date_add" order=$order}
			{TableHead field="active" order=$order}
			<th></th>
		</tr>
		{if is_array($list) and count($list)>0}
			{foreach from=$list item=oItem key=oKey name=fList}
				<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
					<td><input type="checkbox" name="sel[cat][{$oItem.id}]" value="1" class="checkItem"></th>
					<td><a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}">{$oItem.name}</a></td>
					<td>{$oItem.description|truncate:100:""}</td>
					<td>{$oItem.date_add|date_format:"%d.%m.%Y"}</td>
					<td style="text-align: center;"><img src="{#images_path#}/active{$oItem.active}.gif" border="0"/></td>
					<td style="text-align: center;">
						<div style="width:60px;">
							<a href="{get_url _CLEAR="CU_order.*" action=edit id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" /></a>
							<a onclick="return confirm('{#delete_confirm#}')" href="{get_url _CLEAR="CU_order.*" action=delete id=$oItem.id back_url=get_url}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
						</div>
					</td>
				</tr>
			{/foreach}
		{else}
		<tr>
			<td colspan="6">{#nothing_found#}</td>
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
    			<input type="submit" id="comdel" name="comdel" disabled value="{#delete#}" class="check_depend" onclick="return confirm('{#delete_common_confirm#}');" />&nbsp;
    			<input type="submit" id="comact" name="comact" disabled value="{#activate#}" class="check_depend"/>&nbsp;
    			<input type="submit" id="comdea" name="comdea" disabled value="{#deactivate#}" class="check_depend"/>
    			<input type="hidden" id="action" name="action" value="common" />
    		</td>
    	</tr>
    </table>
</div>
</form>

{include file='admin/common/hint.tpl' title=$smarty.config.title_newsletters description=$smarty.config.hint icon="/big_icons/folder.gif"}