<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=navigation"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<input type="button" class="add_div2" onclick="document.location='{get_url ACTION=new}';" value="{#create#}">
				</div>
			</td>
			<td width="100%">
				<span>{#header_hint#}</span>
			</td>
		</tr>
	</table>
</div>

{strip}
<form action="{get_url}" method="POST" name="form1">
	<div class="users">
	    <input type="hidden" name="ACTION" value="common">
    	<table class="layout" id="baseTable">
    		<tr>
    			<th>
    				<input type="checkbox" name="sel[ALL]" value="ALL" onClick="checkAll(this.form,this.checked)"/>
    			</th>
    			<th width="50%">
    				<a href="{get_url _CLEAR="PAGE" order=name dir=$order.newdir}">{#field_name#}</a>{if $order.field=='name'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    			</th>
    			<th width="30%">
    				{#field_description#}
    			</th>
    			<th width="20%">
    				{#field_script_name#}
    			</th>
    			<th></th>
    		</tr>
			{if $dataList.ITEMS!=0}
				{foreach from=$dataList.ITEMS item=oItem key=oKey name=fList}
    				<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
    					<td><input type="checkbox" name="sel[cat][]" value="{$oItem.id}"/></td>
    					<td><img src="{#images_path#}/active{$oItem.active}.gif"><a href="{get_url _CLEAR="ACTION type" typeid=$oItem.id page=menu}"> {$oItem.name}</a><br/>[{$oItem.text_ident}]</td>
						<td>{$oItem.description}</td>
						<td>{$oItem.script_descr}</td>
    					<td>
    						<div style="width:60px;">
    							<a href="{get_url ACTION=edit CSC_catid=$oItem.id}" title="{#edit#}">
									<img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" />
								</a>
    							<a href="{get_url ACTION=delete CSC_catid=$oItem.id}" onclick="return confirm('{#delete_confirm#}');" title="{#delete#}">
    								<img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" />
    							</a>
    						</div>
    					</td>
    				</tr>
				{/foreach}
			{/if}
    	</table>
 	</div>
	<div class="manage">
    	<table class="layout">
    		<tr class="titles">
    			<td>{#selected#}:</td>
    			<td><input type="submit" name="comdel" value="{#delete#}" onclick="return confirm('{#delete_common_confirm#}');"></td>
    			<td><input type="submit" name="comact" value="{#activate#}"></td>
    			<td><input type="submit" name="comdea" value="{#deactivate#}"></td>
    		</tr>
    	</table>
	</div>
</form>
{/strip}
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/folder.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title#}</dt>
<dd>{#hint_list#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}