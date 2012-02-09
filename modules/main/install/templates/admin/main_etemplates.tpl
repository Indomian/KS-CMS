<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
</ul>
<h1>{#titles#}</h1>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<div class="users">
	<table class="layout">
	    <tr>
    		<th width="60%">{#field_event#}</th>
    		<th width="40%">{#field_template#}</th>
    		<th width="0%"></th>
		</tr>
		{if $data}
			{foreach from=$data item=oItem key=oKey name=fList}
			<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
				<td>{$oItem.title}</td>
				<td>{if $oItem.deleted==1}<span style="text-decoration:line-through;">{/if}{$oItem.file_id}{if $oItem.deleted==1}</span>{/if}</td>
				<td>
					<div style="width:60px; text-align: center;">
					{if $oItem.deleted==1}
						<a href="{get_url _CLEAR="CU_order.*" ACTION=delete id=$oItem.id}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" title="{#delete#}" /></a>
					{elseif $oItem.new==1}
						<a href="{get_url _CLEAR="CU_order.*" ACTION=new id=$oItem.file_id}"><img src="{#images_path#}/icons2/add.gif" alt="{#install#}" title="{#install#}" /></a>
					{else}
						<a href="{get_url _CLEAR="CU_order.*" ACTION=edit id=$oItem.file_id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" title="{#edit#}" /></a>
					{/if}
					</div>
				</td>
			</tr>
			{/foreach}
		{else}
		<tr>
			<td colspan="3">{#nothing_selected#}</td>
		</tr>
		{/if}
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}

{include file='admin/common/hint.tpl' title=$smarty.config.titles description=$smarty.config.hint_list icon="/big_icons/settings.gif"}
