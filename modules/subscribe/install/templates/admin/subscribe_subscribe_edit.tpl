<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=subscribe"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	<li><a href="{get_url _CLEAR="action type id i p1"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_subscribe#}</span></a></li>
	{strip}
	<li>
		<a href="{get_url}">
			<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
			&nbsp;<span>
			{if $data.id<0}
				{#title_new_subscribe#}
			{else}
				{#title_edit_subscribe#} <b>{$data.title}</b>
			{/if}
			</span>
		</a>
	</li>
	{/strip}
</ul>
<h1>{if $data.id<0}{#title_new_subscribe#}{else}{#title_edit_subscribe#}{$data.title}{/if}</h1>

<form action="{get_url _CLEAR="action id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="module" value="subscribe"/>
	<input type="hidden" name="SB_id" value="{$data.id}">
	<input type="hidden" name="action" value="save">
	{ksTabs NAME=subscribe_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}
			<div class="form">
				<table class="layout">
					{if $is_ajax_frame!=1}
					<tr class="titles">
						<th width=30%><h3>{#header_field#}</h3></th>
						<th width=70%><h3>{#header_value#}</h3></th>
					</tr>
					{/if}
					<tr class="is_necessary_light">
						<td>{Title field="uin"}</td>
						<td>
							<input type="text" name="SB_uin" style="width:100px;" class="form_input" {$isReadonly} value="{$data.uin|intval|default:"-1"}" id="uid"/>
							<input type="button" value="..." {$isDisabled} id="uid_select"/>
						</td>
					</tr>
					<tr>
						<td>{Title field="email"}</td>
						<td><input type="text" name="SB_email" value="{$data.email}" style="width:98%" class="form_input"/></td>
					</tr>
					<tr>
						<td>{Title field="date_add"}</td>
				    	<td>
							<b>{if $data.date_add}{$data.date_add|date_format:"%d.%m.%Y"}{else}{$smarty.now|date_format:"%d.%m.%Y"}{/if}</b>
						</td>
					</tr>
					<tr>
						<td>{Title field="active"}</td>
						<td><input type="checkbox" name="SB_active" value="1" {if $data.active==1}checked="checked"{/if}></td>
					</tr>
					<tr>
						<td>{Title field="date_active"}</td>
						<td>
							{if $data.active}
								{ShowCalendar field="SB_date_active" title=$smarty.config.select_date value=$data.date_active}
							{else}
								{#no_active_subscribe#}
							{/if}
						</td>
					</tr>
				</table>
			</div>
		{/ksTab}
		{ksTab NAME=$smarty.config.tabs_subscribe}
			<div class="form">
				<table class="layout">
					{if $is_ajax_frame!=1}
					<tr class="titles">
						<th width=30%><h3>{#header_field#}</h3></th>
						<th width=70%><h3>{#header_value#}</h3></th>
					</tr>
					{/if}
					<tr>
						<td>{Title field="newsletters"}</td>
						<td>
							<label><input type="checkbox" id="listCheck"/>&nbsp;&nbsp;{#all#}</label><br>
							<div id="list_table">
								{foreach from=$data.newsletters item=oItem key=oKey}
									<label><input name="SB_news[]" type="checkbox" class="nlItem" value="{$oItem.id}" {if $oItem.select}checked{/if}/> &nbsp;&nbsp;{$oItem.name}</label><br>
								{/foreach}
							</div>
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
		{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
		<div><input type="submit" class="save" value="{#save#}"/></div>
		<div><input type="submit" name="update" value="{#apply#}"/></div>
	    <div><a href="{get_url _CLEAR="action id"}" class="cancel_button">{#cancel#}</a></div>
	</div>
</form>

{include file='admin/common/hint.tpl' title=$smarty.config.title_edit_subscribe description=$smarty.config.hint_edit_subscribe icon="/big_icons/people.gif"}