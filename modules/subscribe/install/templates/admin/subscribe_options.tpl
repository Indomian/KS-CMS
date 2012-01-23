<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=subscribe"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_options#}</span></a></li>
</ul>
<h1>{#title_options#}</h1>
<form action="{get_url _CLEAR="ACTION"}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save"/>
	{ksTabs NAME=subscribe_options head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td>{Title field="encryption"}</td>
    					<td>
							<select name="SB_encryption" style="width:98%;" class="form_input">
								<option value="utf8" {if $data.encryption=='utf8'}selected="selected"{/if}>UTF-8</option>
								<option value="cp1251" {if $data.encryption=='cp1251'}selected="selected"{/if}>CP-1251</option>
								<option value="ascci" {if $data.encryption=='ascii'}selected="selected"{/if}>ASCII</option>
							</select>
    					</td>
					</tr>
					<tr>
    					<td>{Title field="release_from"}</td>
    					<td>
    						<input type="text" name="SB_from" value="{$data.from|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/>
						</td>
					</tr>
    				<tr>
    					<td>{Title field="format"}</td>
    					<td>
    						<select name="SB_format" style="width:20%" class="form_input">
        						<option value="1" {if $data.format==1}selected="selected"{/if}>{#html#}</option>
        						<option value="2" {if $data.format==2}selected="selected"{/if}>{#text#}</option>
        					</select>
    					</td>
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

{include file='admin/common/hint.tpl' title=$smarty.config.title_options description=$smarty.config.hint_options icon="/big_icons/settings.gif"}