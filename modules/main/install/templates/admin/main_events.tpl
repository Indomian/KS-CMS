{literal}
<script type="text/javascript">
	function showContent(param){
		var tr = document.getElementById('content_'+param);
		var img = document.getElementById('plus_'+param);
		if(tr.className=='invisible'){
			{/literal}
			img.src='{#images_path#}/icons_menu/minus.gif';
			tr.className='visible';
			{literal}
		}else{
			{/literal}
			img.src='{#images_path#}/icons_menu/plus.gif';
			tr.className='invisible';
			{literal}
		}
	}
</script>
{/literal}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="action id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				{if $level<7}
				<div>
					<form action="{get_url action=new}" method="post">
						<input type="submit" class="add_div2" value="{#create#}"/>
					</form>
				</div>
				{/if}
			</td>
			<td width="100%">
				<span>{#small_hint#}</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}
<form action="{get_url}" method="POST" name="form1">
<input type="hidden" name="action" value="common"/>
<div class="users">
	<table class="layout">
		<col width="0%"/>
		<col width="0%"/>
		<col width="60%"/>
		<col width="15%"/>
		<col width="5%"/>
		<col width="0%"/>
		<col width="0%"/>
	    <tr>
    		<th>
    			<input type="checkbox" name="sel[ALL]" value="ALL" onClick="checkAll(this.form,this.checked)">
    		</th>
    		{TableHead field="id" order=$order}
			{TableHead field="title" order=$order}
			{TableHead field="address" order=$order}
			{TableHead field="status" order=$order}
			{TableHead field="type" order=$order}
    		<th></th>
		</tr>
		{if $data!=''}
		{foreach from=$data item=oItem key=oKey name=fList}
    	<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    		<td>
	    		<input type="checkbox" name="sel[elm][]" value="{$oItem.id}">
    		</td>
    		<td>{$oItem.id}</td>
    		<td>
	    		<a onclick="showContent('{$oItem.id}'); return false;" href="#"><img width="13" height="13" id="plus_{$oItem.id}" alt="icon" src="{#images_path#}/icons_menu/plus.gif"/></a>
				&nbsp;
				<a onclick="showContent('{$oItem.id}'); return false;" href="#">{$oItem.title}</a>
			</td>
    		<td>{$oItem.address}</td>
    		<td>
    			{$oItem.status}
    		</td>
    		<td>{$oItem.type}</td>
    		<td>
    			<div style="width:80px; text-align: right;">
    				{if $oItem.status=='error'}
	    				<a href="{get_url _CLEAR="CU_order.*" action=activate id=$oItem.id}" title="{#activate#}"><img src="{#images_path#}/icons_menu/email_template.gif" alt="{#activate#}" title="{#activate#}" /></a>
	    			{/if}
	    			<a href="{get_url _CLEAR="CU_order.*" action=delete id=$oItem.id}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" title="{#delete#}" /></a>
    			</div>
    		</td>
		</tr>
		<tr class="invisible" id="content_{$oItem.id}">
			<td colspan="7">
				<div>{$oItem.content}</div>
			</td>
		</tr>
		{/foreach}
		{else}
			<tr><td colspan="7">{#nothing_selected#}</td></tr>
		{/if}
	</table>
</div>
{if $level<7}
<div class="manage">
    <table class="layout">
    	<tr class="titles">
    		<td>{#selected#}
				<input type="submit" name="comdel" value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');">
				<input type="submit" name="comact" value="{#activate#}">
			</td>
    	</tr>
    </table>
</div>
{/if}
</form>
{include file='admin/navigation_pagecounter.tpl' pages=$pages}

{include file='admin/common/hint.tpl' title=$smarty.config.title description=$smarty.config.hint icon="/big_icons/settings.gif"}
