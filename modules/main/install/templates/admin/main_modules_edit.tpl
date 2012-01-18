<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="CM_ACTION CM_id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#module#} {$data.name}</span></a></li>
</ul>

<h1>{#title#} "{$data.name}"</h1>

<form action="{get_url _CLEAR="CM_.*"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="module" value="main"/>
	<input type="hidden" name="modpage" value="modules"/>
	<input type="hidden" name="CM_id" value="{$data.id}"/>
	<input type="hidden" name="CM_ACTION" value="save"/>
	{ksTabs NAME=modules_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
		<div class="form">
    	<table class="layout">
    		<tr>
    			<th width="30%">{#header_field#}</th>
    			<th width="70%">{#header_value#}</th>
    		</tr>
		    <tr>
    			<td>{Title field='name'}</td>
    			<td><input type="text" name="CM_name" class="form_input" value="{$data.name|htmlspecialchars:2:"UTF-8":false}" style="width:98%"/></td>
    		</tr>
    		<tr>
    			<td>{Title field="URL_ident"}
    				{if $data.allow_url_edit==0}<small><font color="#ff0000">{#cant_be_changed#}</font></small>{/if}
    			</td>
    			<td>
					{if $data.URL_ident=='default'}
						<input type="text" name="CM_URL_ident" class="form_input" value="{$data.URL_ident|htmlspecialchars:2:"UTF-8":false}" style="width:98%;display:none;"{if $data.allow_url_edit==0}disabled{/if}/>
						<div id="hint_URL_ident_d" style="cursor: pointer;"
							onmouseover="floatMessage.showMessage(document.getElementById('hint_URL_ident_d'),
							'{#field_URL_ident_d_hint#}');">{#field_URL_ident_d#},&nbsp;
						<a href="#" onclick="this.parentNode.previousSibling.style.display='';this.parentNode.style.display='none';return false;">{#change#}</a>.</div>
					{else}
						<input type="text" name="CM_URL_ident" class="form_input" value="{$data.URL_ident}" style="width:98%"{if $data.allow_url_edit==0}disabled{/if} />
					{/if}
				</td>
    		</tr>
    		<tr>
    			<td>{Title field="include_global_template"}</td>
    			<td>
					<select name="CM_include_global_template" style="width:100%" class="form_input">
						<option value="1" {if $data.include_global_template==1}selected="selected"{/if}>{#use#}</option>
						<option value="0" {if $data.include_global_template==0}selected="selected"{/if}>{#not_use#}</option>
        			</select>
        		</td>
    		</tr>
   			<tr>
    			<td>{Title field="active"}</td>
    			<td><select name="CM_active" style="width:100%" class="form_input">
        			<option value="1" {if $data.active==1}selected="selected"{/if}>{#active#}</option>
        			<option value="0" {if $data.active==0}selected="selected"{/if}>{#inactive#}</option>
        			</select>
    			</td>
    		</tr>
    	</table>
    </div>
    {/strip}
    {/ksTab}
    {/ksTabs}
    <div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="CM_ACTION CM_id"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>
{include file='admin/common/hint.tpl' title=$smarty.config.title description=$smarty.config.hint icon="/big_icons/settings.gif"}