{config_load file=admin.conf}
<div id="formContainer">
{include file="admin/navigation_pagecounter.tpl" pages=$pages}
<form action="{get_url}" method="POST" name="form1">
	<input type="hidden" name="ACTION" value="common"/>
	<div class="users">
		<table class="layout">
			<col/>
			{foreach from=$heads item=oItem name=columns}
				<col width="{$width}%"/>
			{/foreach}
			<col/>
			<tr>
				<th>
					<input type="checkbox" name="sel[ALL]" value="ALL" onClick="checkAll(this.form,this.checked)">
				</th>
				{foreach from=$heads item=sHead name=heads}
					{assign var=iKey value=$smarty.foreach.heads.iteration}
				<th>
					<div class="forms_data_arrow_holder">
						<a href="{if $changeColumnList!=$sHead}{get_url _CLEAR='changeColumn to deleteColumn addColumn' changeColumnList=$sHead}{else}{get_url _CLEAR='changeColumn to deleteColumn addColumn changeColumnList'}{/if}" class="icon icon_arrow_new" style="float:left;">
							<img src="/uploads/templates/admin/images/t.gif" width="15" height="15" border="0"/>
						</a>
						<a href="{get_url _CLEAR='changeColumn to deleteColumn addColumn changeColumnList' sort_by=$sHead sort_dir=$sort_dir_new}" class="icon {if $sort_by==$sHead}icon_arrow_{if $sort_dir=='desc'}down{else}up{/if}{/if}" title="{if $sort_by==$sHead}Кликните для изменения направления сортировки{else}Кликните для сортировки по данному столбцу{/if}" style="display:block;overflow:hidden;margin-right:10px;">
							{$columns[$sHead]}
							{if $sort_by==$sHead}
								<em>&nbsp;</em>
							{/if}
						</a>
						<div class="forms_data_arrow_drop" {if $changeColumnList!=$sHead}style="display:none;"{/if}>
							<div class="forms_data_arrow_drop_head">
								<a href="{get_url _CLEAR='changeColumn to addColumn changeColumnList' deleteColumn=$sHead key=$iKey}" class="icon icon_minus_alt"><i>&nbsp;</i>Удалить столбец</a>
							</div>
							<div class="forms_data_arrow_body">
								{foreach from=$columns item=sTitle key=sKey}
								<div class="forms_data_arrow_body_item"><a href="{get_url _CLEAR='changeColumnList addColumn deleteColumn' changeColumn=$sHead to=$sKey key=$iKey}" rel="{$sKey}">{$sTitle}</a></div>
								{/foreach}
							</div>
						</div>
					</div>
				</th>
				{/foreach}
				<th>
					<div class="forms_data_arrow_holder" style="text-align:right;">
						<a href="{get_url _CLEAR='changeColumn to deleteColumn changeColumnList' addColumn=Y}" class="icon  icon_arrow_new"><img src="/uploads/templates/admin/images/t.gif" width="15" height="15" border="0"/></a>
						<div class="forms_data_arrow_drop" style="display:none;">
							<div class="forms_data_arrow_body">
								{foreach from=$columns item=sTitle key=sKey}
								<div class="forms_data_arrow_body_item">
									<a href="{get_url _CLEAR='changeColumnList deleteColumn changeColumn to key' addColumn=$sKey}" rel="{$sKey}">{$sTitle}</a>
								</div>
								{/foreach}
							</div>
						</div>
					</div>
				</th>
			</tr>
			{foreach from=$list item=oItem name=items}
			<tr {if $smarty.foreach.items.iteration is even}class="even"{/if}>
				<td{Highlight date=$oItem.date_add assign=highlight i=$smarty.foreach.items.iteration}>
					<input type="checkbox" name="sel[elm][]" value="{$oItem.id}"/>
					<input type="hidden" name="title[{$oItem.id}]" value="{$oItem.title}">
				</td>
				{foreach from=$heads item=sHead}
					<td{$highlight}>{$oItem[$sHead]}</td>
				{/foreach}
				<td align="center"{$highlight}>
					<div style="width:80px;">
					<a href="{get_url ACTION=edit CSC_id=$oItem.id}" title="{#edit#}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" /></a>
					{if $oItem.id!=0}
					<a href="{get_url ACTION=delete CSC_id=$oItem.id}" onclick="return confirm('{#delete_confirm#}');" title="{#delete#}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" /></a>
					{/if}
					<a href="{$oItem.path}{$oItem.text_ident}.html" target="_blank" title="{#view#}"><img src="{#images_path#}/icons2/view.gif" alt="{#view#}" /></a>
					</div>
				</td>
			</tr>
			{/foreach}
		</table>
	</div>
	{include file="admin/navigation_pagecounter.tpl" pages=$pages}
	<div class="manage">
		<table class="layout">
			<tr class="titles">
				<td>Выделенные:</td>
				<td><input type="submit" name="comdel" value="Удалить" onclick="return confirm('Вы действительно хотите удалить выделенные элементы?');"></td>
				<td><input type="submit" name="comact" value="Активировать"></td><td><input type="submit" name="comdea" value="Деактивировать"></td>
			</tr>
		</table>
	</div>
</form>
</div>
<script type="text/javascript">
<!--
$(document).ready(function(){ldelim}
	$('#formContainer a.icon_arrow_new').click(function(e){ldelim}
		e.stopPropagation();
		e.preventDefault();
		$(this).parent().children('div.forms_data_arrow_drop').toggle();
		if($(this).parent().children('div.forms_data_arrow_drop:visible').length==1)
		{ldelim}
			$(this).removeClass('icon_arrow_new').addClass('icon_arrow_new_alt');
		{rdelim}
		else
		{ldelim}
			$(this).addClass('icon_arrow_new').removeClass('icon_arrow_new_alt');
		{rdelim}
	{rdelim});
{rdelim});
//-->
</script>