<script language="javascript" type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="{get_url _CLEAR="ACTION CSC_id CSC_catid type mode"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{if $data.id<1}{#title_create#}{else}{#title_edit#} <b>"{$data.name}"</b>{/if}</span></a></li>
</ul>
<h1>{if $data.id<1}{#title_create#}{else}{#title_edit#} "{$data.name}"{/if}</h1>

<form action="{get_url _CLEAR="ACTION CSC_id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="module" value="navigation">
	<input type="hidden" name="CSC_id" value="{$data.id}">
	<input type="hidden" name="CSC_catid" value="{$data.id}">
	<input type="hidden" name="ACTION" value="save">
	{ksTabs NAME=nav_type_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
    				<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr class="is_necessary_light">
    					<td>{Title field="name"}</td>
    					<td><input type="text" name="CSC_name" value="{$data.name|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/></td>
    				</tr>
    				<tr class="is_necessary_light">
    					<td>{Title field="text_ident"}
							<br><font color=#FF0000>{#change_warning#}</font></td>
    					<td>{if $data.text_ident==''}<input type="text" name="CSC_text_ident" value="" style="width:95%" class="form_input"/>{else}
    						<input type="hidden" name="CSC_text_ident" value="{$data.text_ident}"/>
        					<b>{$data.text_ident}</b>{/if}
    					</td>
    				</tr>
    				<tr>
    					<td>{Title field="description"}</td>
    					<td><textarea name="CSC_description" style="width:95%;height:200px;" class="form_textarea">{$data.description}</textarea></td>
    				</tr>
    				<tr>
    					<td>{Title field="active"}</td>
    					<td><select name="CSC_active" style="width:95%" class="form_input">
    						<option value="0" {if $data.active==0}selected="selected"{/if}>{#inactive#}</option>
    						<option value="1" {if $data.active==1}selected="selected"{/if}>{#active#}</option>
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td>{Title field="script_name"}</td>
    					<td>
    						{foreach from=$groups_list item=oItem key=oKey}
    						<label><input type="radio" name="CSC_script_name" value="{$oItem.value}" {if $oItem.value eq $data.script_name}checked="checked"{/if}/>
    						&nbsp;{$oItem.title}
    						</label><br/>
    						{/foreach}
    					</td>
    				</tr>
    			</table>
    		</div>
		{/strip}{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save" name="save"/>
    	</div>
    	<div>
    		<input type="submit" value="{#apply#}" name="update"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="ACTION type CSC_catid"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>
