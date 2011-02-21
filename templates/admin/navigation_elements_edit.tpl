{config_load file=admin.conf section=navigation}
<script language="javascript" type="text/javascript" src="/js/floatmessage.js"></script>
<script language="javascript" type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="{get_url _CLEAR="ACTION CSC_id CSC_catid type mode typeid mode CSC_elmid"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>      
    <li><a href="{get_url _CLEAR="ACTION CSC_id CSC_elmid" mode=mnu}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_manage#}{if $data.SECTION.name!=''} "{$data.SECTION.name}"{/if}</span></a></li>
   	<li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{if $data.id<1}{#title_create_menu#}{else}{#title_edit_menu#} <b>"{$data.anchor}"</b>{/if}</span></a></li>
</ul>
<h1>{if $data.id<1}{#title_create_menu#}{else}{#title_edit_menu#} "{$data.anchor}"{/if}</h1>

<form action="{get_url _CLEAR="ACTION CSC_id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="module" value="navigation">
	<input type="hidden" name="CSC_id" value="{$data.id}">
	<input type="hidden" name="CSC_catid" value="{$data.id}">
	<input type="hidden" name="CSC_parent_id" value="{$data.parent_id}">
	<input type="hidden" name="CSC_type_id" value="{$data.type_id}">
	<input type="hidden" name="mode" value="mnu">
	<input type="hidden" name="typeid" value="{$data.type_id}">
	<input type="hidden" name="ACTION" value="save">
	{ksTabs NAME=nav_el_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
    				<tr class="titles">
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td><div id="hint_anchor" 
    						style="cursor: pointer;" 
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_anchor'),
    						'{#field_anchor_hint#}');">{#field_anchor#}</div>
    					</td>
    					<td><input type="text" name="CSC_anchor" value="{$data.anchor|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/></td>
    				</tr>
    				<tr>
    					<td>
    					<div id="hint_link" 
    						style="cursor: pointer;" 
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_link'),
    						'{#field_link_hint#}');">{#field_link#}</div>
    					</td>
    					<td><input type="text" name="CSC_link" value="{$data.link}" style="width:95%" class="form_input"/>
    					</td>
    				</tr>
    				<tr>
    					<td>
    					<div id="hint_orderation" 
    						style="cursor: pointer;" 
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_orderation'),
    						'{#field_orderation_hint#}');">{#field_orderation#}</div>
    					</td>
    					<td><input type="text" name="CSC_orderation" size="3" value="{$data.orderation|intval}" class="form_input"/></td>
    				</tr>
    				<tr>
    					<td>
    					<div id="hint_img" 
    						style="cursor: pointer;" 
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_img'),
    						'{#field_img_hint#}');">{#field_img#}</div>
    					</td>
    					<td><input type="file" name="CSC_img" value="" style="width:95%" class="form_input"/><br>
    						{if $data.img!=""}<img src="/uploads/{$data.img}"><br/>
    						<input type="checkbox" name="CSC_img_del" value="1"/> Удалить{/if}
    					</td>
    				</tr>
    				<tr>
    					<td>
    					<div id="hint_target" 
    						style="cursor: pointer;" 
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_target'),
    						'{#field_target_hint#}');">{#field_target#}</div>
    					</td>
    					<td><select name="CSC_target" style="width:100%">
        					<option value="" {if $data.target eq ""}selected="selected"{/if}>[{#open_unset#}]</option>
        					<option value="_blank" {if $data.target eq "_blank"}selected="selected"{/if}>[{#open_new#}]</option>
        					<option value="_self" {if $data.target eq "_self"}selected="selected"{/if}>[{#open_this#}]</option>
        					</select>
    					</td>
    				</tr>
    			</table>
    		</div>
		{/strip}{/ksTab}
		{if $addFields!=''}
		{ksTab NAME=$smarty.config.tabs_userfields}
		<div class="form">
		<table class="layout">
	    	{if $is_ajax_frame!=1}
		    <tr class="titles">
		    	<th width=30%><h3>{#header_field#}</h3></th>
		    	<th width=70%><h3>{#header_value#}</h3></th>
		    </tr>
	    	{/if}
			{foreach from=$addFields item=oItem}
			<tr>
				<td>{$oItem.description}</td>
				{assign var=value value=ext_`$oItem.title`}
				<td>{showField field=$oItem value=$data[$value]}</td>
			</tr>
			{/foreach}
		</table>
		</div>
		{/ksTab}
		{/if}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
    	<div>
    		<input type="submit" value="{#apply#}" name="update"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="ACTION type CSC_elmid"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/folder.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{if $data.id<1}{#title_create_menu#}{else}{#title_edit_menu#}{/if}</dt>
<dd>{#hint_menu#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip} 