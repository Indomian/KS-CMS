{config_load file=admin.conf section=interfaces}
<script type="text/javascript">
function checkAll(oForm, checked)
{ldelim}
for (var i=0; i < oForm.length; i++)
{ldelim}
     oForm[i].checked = checked;
{rdelim}
{rdelim}
</script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="{get_url module=interfaces}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>Интерфейс</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>Смайлики</span></a></li>
</ul>

<h1>Смайлики</h1>

<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url _CLEAR="action page"}" method="GET" name="form2">
						<input type="hidden" name="action" value="new"/>
						<input type="hidden" name="page" value="smilies"/>
						<input type="hidden" name="module" value="interfaces"/>
						<input type="submit" value="Добавить смайлик" class="add_div2"/>
					</form>
				</div>
			</td>
			<td width="100%">
				<span>Смайлики - иконки эмоций которые можно вставить в текст</span>
			</td>
		</tr>
	</table>
</div> 

<form action="{get_url}" method="POST" name="form1">
<input type="hidden" name="ACTION" value="common"/>
{strip}
<div class="users">
	<table class="layout">
		<tr>
			<th>
				<input type="checkbox" name="sel[ALL]" value="ALL" onClick="checkAll(this.form,this.checked)">
			</th>
    		<th width="40%">
    			<a href="{get_url _CLEAR="PAGE" order=smile dir=$order.newdir}">Текст</a>
    				{if $order.field=='smile'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    			</a>
    		</th>
    		<th width="40%">
    			Иконка
    		</th>
    		<th>
    			<a href="{get_url _CLEAR="PAGE" order=group dir=$order.newdir}">Группа</a>
    				{if $order.field=='group'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    			</a>
    		</th>
    		<th></th>
    	</tr>
		{if $dataList.ITEMS!=0}
			{foreach from=$dataList.ITEMS item=oItem key=oKey name=fList}
    			<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    				<td>
    					<input type="checkbox" name="sel[elm][]" value="{$oItem.id}"/>
    				</td>
    				<td>
    					<a href="{get_url action=edit id=$oItem.id}">{$oItem.smile}</a>
					</td>
    				<td>
    					<img src="/uploads/{$oItem.img}"/>
    				</td>
    				<td>{$oItem.group}</td>
					<td>
    					<div style="width:80px;">
	    					<a href="{get_url action=edit id=$oItem.id}" title="{#edit#}">
								<img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" />
							</a>
	    					<a href="{get_url action=delete id=$oItem.id}" onclick="return confirm('{#delete_confirm#}');" title="{#delete#}">
    							<img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" />
    						</a>
    					</div>
    				</td>
				</tr>
			{/foreach}
		{else}
			<tr><td colspan="4">Нет ни одной записи</td></tr>
		{/if}
	</table>
</div>
{/strip}
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<div class="manage">
    <table class="layout">
    	<tr class="titles">
    		<td>Выделенные:</td>
    		<td><input type="submit" name="comdel" value="Удалить" onclick="return confirm('Вы действительно хотите удалить выделенные элементы?');"></td>
    	</tr>
    </table>
</div>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>Смайлики</dt>
	<dd>Разнообразные иконки которые могут выразить эмоции в тексте.</dd>          
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}