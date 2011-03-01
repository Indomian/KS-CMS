<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=guestbook2"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="/admin.php?module=guestbook2&page=options"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_options#}</span></a></li>
</ul>
<h1>{#title_options#}</h1>
<form action="{get_url _CLEAR="ACTION"}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
	{ksTabs NAME=guestbook_options head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td>{Title field="use_captcha"}</td>
    					<td><input type="checkbox" name="sc_use_captcha" value="1" {if $data.use_captcha==1}checked="checked"{/if}/></td>
					</tr>
					<tr>
    					<td>{Title field="use_tags"}</td>
    					<td><input type="checkbox" name="sc_use_tags" value="1" {if $data.use_tags==1}checked="checked"{/if}/></td>
					</tr>
					<tr>
						<td>{Title field="no_empty_category"}</td>
						<td><input type="checkbox" name="sc_no_empty_category" value="1" {if $data.no_empty_category==1}checked="checked"{/if}/></td>
					</tr>
					<tr>
						<td>{Title field="restricted_guest_names"}</td>
						<td>
							<textarea name="sc_restricted_guest_names" style="width:200px;height:100px;">
								{$data.restricted_guest_names}
							</textarea>
						</td>
					</tr>
				</table>
    		</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_interface}{strip}
    		<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td>{Title field="int_htmleditor"}</td>
    					<td><input type="checkbox" name="sc_int_htmleditor" value="1" {if $data.int_htmleditor==1}checked="checked"{/if}/></td>
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
	<dt>{#title_options#}</dt>
	<dd>{#hint_options#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}