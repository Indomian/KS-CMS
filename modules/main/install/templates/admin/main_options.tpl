<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>
<form action="{get_url _CLEAR="ACTION"}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
	{ksTabs NAME=main_options head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td><div id="hint_home_url"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_home_url'),
    						'{#field_home_url_hint#}');">{#field_home_url#}</div>
    					</td>
    					<td><input type="text" name="sc_home_url" value="{$data.home_url|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/></td>
					</tr>
    				<tr>
    					<td><div id="hint_home_title"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_home_title'),
    						'{#field_home_title_hint#}');">{#field_home_title#}</div>
    					</td>
    					<td><input type="text" name="sc_home_title" value="{$data.home_title|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/></td>
    				</tr>
					<tr>
    					<td><div id="hint_home_descr"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_home_descr'),
    						'{#field_home_descr_hint#}');">{#field_home_descr#}</div>
    					</td>
    					<td><input type="text" name="sc_home_descr" value="{$data.home_descr|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/></td>
    				</tr>
    				<tr>
    					<td><div id="hint_home_keywrds"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_home_keywrds'),
    						'{#field_home_keywrds_hint#}');">{#field_home_keywrds#}</div>
    					</td>
    					<td><input type="text" name="sc_home_keywrds" value="{$data.home_keywrds|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/></td>
    				</tr>
    				<tr>
    					<td><div id="hint_copyright"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_copyright'),
    						'{#field_copyright_hint#}');">{#field_copyright#}</div>
    					</td>
    					<td><input type="text" name="sc_copyright" value="{$data.copyright|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/></td>
    				</tr>
					<tr>
    					<td><div id="hint_admin_email"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_admin_email'),
    						'{#field_admin_email_hint#}');">{#field_admin_email#}</div>
    					</td>
    					<td><input type="text" name="sc_admin_email" value="{$data.admin_email}" style="width:95%" class="form_input"/></td>
    				</tr>
    				<tr>
    					<td>{Title field="emailFrom"}</td>
    					<td><input type="text" name="sc_emailFrom" value="{$data.emailFrom}" style="width:95%" class="form_input"/></td>
    				</tr>
					<tr>
    					<td><div id="hint_time_format"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_time_format'),
    						'{#field_time_format_hint#}');">{#field_time_format#}</div>
    					</td>
    					<td><input type="text" name="sc_time_format" value="{$data.time_format}" style="width:95%" class="form_input"/></td>
    				</tr>
    				{*
    				<tr>
				    	<td><div id="hint_debugmode"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_debugmode'),
    						'{#field_debugmode_hint#}');">{#field_debugmode#}</div></td>
						<td>
							<select name="sc_debugmode" class="form_input">
								<option value="1" {if $data.debugmode==1} selected="selected"{/if}>{#short_error_mode#}</option>
								<option value="0" {if $data.debugmode==0} selected="selected"{/if}>{#full_error_mode#}</option>
							</select>
						</td>
					</tr>*}
					<tr>
						<td><div id="hint_start_adminpage"
    						style="cursor: pointer;"
    						onmouseover="floatMessage.showMessage(document.getElementById('hint_start_adminpage'),
    						'{#field_start_adminpage_hint#}');">{#field_start_adminpage#}</div></td>
						<td>
							<select name="sc_start_adminpage" class="form_input">
								<option value="main" {if $data.start_adminpage=="main"} selected="selected"{/if}>{#main_page_main#}</option>
								<option value="users" {if $data.start_adminpage=="users"} selected="selected"{/if}>{#main_page_users#}</option>
								{if $showTreeView=='Y'}
								<option value="lite" {if $data.start_adminpage=="lite"} selected="selected"{/if}>{#main_page_tree#}</option>
								{/if}
							</select>
						</td>
					</tr>
					<tr>
						<td>{Title field="text_ident_length"}</td>
						<td>
							<input type="text" name="sc_text_ident_length" value="{$data.text_ident_length|intval}" class="form_input" style="width:100px"/>
						</td>
					</tr>
					<tr>
						<td>{Title field="highlight_new_elements"}</td>
						<td>
							<select name="sc_highlight_new_elements" class="form_input">
								<option value="no" {if $data.highlight_new_elements=="no"}selected="selected"{/if}>{#no#}</option>
								<option value="all" {if $data.highlight_new_elements=="all"}selected="selected"{/if}>{#highlight_all#}</option>
								<option value="my" {if $data.highlight_new_elements=="my"}selected="selected"{/if}>{#highlight_my#}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>{Title field="user_inactive_time"}</td>
						<td><input type="text" name="sc_user_inactive_time" value="{$data.user_inactive_time|default:"3600"}" class="form_input"/></td>
					</tr>
					<tr>
						<td>{Title field="lifetime"}</td>
						<td><input type="text" name="sc_lifetime" value="{$data.lifetime|default:"864000"}" class="form_input"/></td>
					</tr>
    			</table>
    		</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_language|default:"Lang"}{strip}
    	<div class="form">
    		<table class="layout">
    			<tr>
    				<th width="30%">{#header_field#}</th>
    				<th width="70%">{#header_value#}</th>
    			</tr>
    			<tr>
    				<td>{Title field="admin_lang"}</td>
    				<td><input type="text" name="admin_lang" value="{$data.admin_lang}" size="2"/></td>
    			</tr>
    		</table>
    	</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_update}{strip}
    	<div class="form">
    		<table class="layout">
    			<tr>
    				<th width="30%">{#header_field#}</th>
    				<th width="70%">{#header_value#}</th>
    			</tr>
    			<tr>
    				<td>{Title field="pkey"}</td>
    				<td><input type="text" name="sc_pkey" value="{$data.pkey}" size="30"/></td>
    			</tr>
    			<tr>
    				<td>{Title field="update_server"}</td>
    				<td><input type="text" name="sc_update_server" value="{$data.update_server}" size="30"/></td>
    			</tr>
    		</table>
    	</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_services}{strip}
    	<div class="form">
    		<table class="layout">
    			<tr>
    				<th width="30%">{#header_field#}</th>
    				<th width="70%">{#header_action#}</th>
    			</tr>
    			<tr>
    				<td>{Title field="drop_cache"}</td>
    				<td><input type="submit" name="act_drop_cache" value="{#action_drop_cache#}"/></td>
    			</tr>
				<tr>
    				<td>{Title field="check_tables"}</td>
    				<td><input type="submit" name="act_check_tables" value="{#action_check_tables#}"/></td>
    			</tr>
    			<tr>
    				<td>{Title field="update_language_files"}</td>
    				<td><input type="submit" name="act_update_lng" value="{#action_update_language_files#}"/></td>
    			</tr>
    			<tr>
    				<td>{Title field="drop_images_cache"}</td>
    				<td><input type="submit" name="act_drop_images_cache" value="{#action_drop_images_cache#}"/></td>
    			</tr>
    			<tr>
    				<td>{Title field="update_templates"}</td>
    				<td><input type="submit" name="act_update_templates" value="{#action_update_templates#}"/></td>
    			</tr>
    		</table>
    	</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_access}{strip}
    	<div class="form">
			<table class="layout">
    			<tr>
					<th width="30%">{#header_group#}</th>
					<th width="70%">{#header_level#}</th>
				</tr>
				{foreach from=$access.groups item=oGroup}
				{assign var="checked" value=""}
				<tr>
					<td>{$oGroup.title}</td>
					<td>
						<ul class="levelSelector">
						{foreach from=$access.module key=oKey item=oItem}
							{if $access.levels[$oGroup.id].level==$oKey}{assign var="checked" value="checked=\"checked\""}{/if}
							<li {if $oKey<10}class="{if $checked!=""}access_available{else}access_denied{/if}{/if}">
							<label>
							<input type="checkbox" name="sc_groupLevel[{$oGroup.id}][]" value="{$oKey}"
								{if $oKey==10}
									{if $access.levels[$oGroup.id].level==10}
										checked="checked"
									{/if}
								{else}
									{$checked}
								{/if}
							onclick="document.obAccessLevels.onClick(this);"/> {$oItem}</label></li>
						{/foreach}
						</ul>
					</td>
				</tr>
				{/foreach}
			</table>
		</div>
    	{/strip}{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
   	</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title#}</dt>
<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}