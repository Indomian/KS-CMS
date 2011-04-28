<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="CM_ACTION CM_id mod"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
</ul>
<h1>{#titles#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
	<table class="layout">
		<tr>
			<td width="100%">
				<span>{#small_hint#}</span>
			</td>
		</tr>
	</table>
</div>

{ksTabs NAME=modules_list head_class=tabs2 title_class=bold}
	{ksTab NAME=$smarty.config.tab_installed selected="1"}
<form action="{get_url}" method="POST" name="form_main_modules">
<input type="hidden" name="ACTION" value="common"/>
<div class="users">
	<table class="layout">
    	<tr>
    		<th width="4%" align="center"><input type="checkbox" name="sel[ALL]" value="ALL" onClick="checkAll(this.form, this.checked)" /></td>
				<th width="37%" align="left">{#field_title#}</th>
    		<th width="17%" align="center">{#field_url#}</th>
    		<!--<th width="20%">Путь</th>-->
    		<th width="18%" align="center">{#field_global_template#}</th>
    		<th width="10%" align="center">{#field_active#}</th>
    		<th width="10%"></th>
		</tr>
		{assign var=bDef value=false}
		{foreach from=$list item=oItem key=oKey name=fList}
		<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			<td align="center"><input type="checkbox" name="sel[{$oItem.id}]" value="{$oItem.id}" /></td>
			<td align="left">{$oItem.name}</td>
			<td align="center">{$oItem.URL_ident}</td>
			{if $oItem.URL_ident=='default'}{assign var=bDef value=true}{/if}
			<!--<td>/modules/{$oItem.directory}/</td>-->
			<td align="left"><img src="{#images_path#}/active{$oItem.include_global_template}.gif" border="0"/>{if $oItem.include_global_template==1}{#use#}{else}{#not_use#}{/if}</td>
			<td align="center"><a href="{get_url CM_ACTION=activate ac=$oItem.active CM_id=$oItem.id}"><img src="{#images_path#}/active{$oItem.active}.gif" border="0"/></a></td>
			<td align="center">
				<div style="width:80px;">
					{strip}
					<a href="{get_url CM_ACTION=edit CM_id=$oItem.id}"><img src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" title="{#edit#}" /></a>
					{if $oItem.URL_ident!='default' and $oItem.URL_ident!=''}<a href="{get_url CM_ACTION=def CM_id=$oItem.id}"
					onclick="return confirm('{#confirm_default#}')">
					<img src="{#images_path#}/icons2/make_default.gif" alt="{#make_default#}" title="{#make_default#}"/></a>
					{/if}
					{if $oItem.URL_ident!='default'}
					<a href="{get_url CM_ACTION=uninstall mod=$oItem.directory}"><img src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" title="{#delete#}"/></a>
					{/if}
					{if $oItem.URL_ident=='default'}
					<img src="{#images_path#}/icons2/module_default.gif" alt="{#default_module#}" title="{#default_module#}"/>
					{/if}
					{/strip}
				</div>
			</td>
		</tr>
		{/foreach}
    </table>
</div>
{if  not $bDef}<div class="atention" style="background:#FFF6C4 url('{#images_path#}/atention.gif') left 50% no-repeat; color:#D13B00; border: 1px solid #CC0000; margin: 0 0 6px; padding: 11px 0 11px 59px;">{#warning_no_default#}</div>{/if}
<div class="manage">
    <table class="layout">
    	<tr class="titles">
    		<td>{#selected#}:</td>
    		<td><input type="submit" name="comact" value="{#activate#}"></td>
    		<td><input type="submit" name="comdea" value="{#deactivate#}"></td>
    	</tr>
    </table>
</div>
</form>
{/ksTab}
{if $ulist and count($ulist)>0}
	{ksTab NAME=$smarty.config.tab_uninstalled}
	<form action="{get_url}" method="POST" name="form_main_modules">
		<input type="hidden" name="CM_ACTION" value="install"/>
		<div class="users">
			<table class="layout">
    			<tr>
    				<th width="10%" align="left">{#field_title#}</th>
    				<th width="10%" align="center">ID</th>
    				<th width="70%" align="center">{#field_description#}</th>
    				<th width="10%"></th>
				</tr>
				{foreach from=$ulist item=oItem key=oKey name=fList}
				<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
					<td align="left">{$oItem.title}</td>
					<td align="left">{$oItem.name}</td>
					<td align="left">{$oItem.description}</td>
					<td align="center">
						<input type="submit" name="install_{$oItem.name}" value="{#install#}" class="button button_module_add"/>
					</td>
				</tr>
				{/foreach}
    		</table>
		</div>
	</form>
	{/ksTab}
{/if}
{/ksTabs}

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#titles#}</dt>
<dd>{#hint_list#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}