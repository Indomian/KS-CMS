<script type="text/javascript" src="/js/catsubcat/admin.js"></script>
{ShowEditor object="textarea[name=CSC_content]" theme="advanced" path=$data.URL}
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="{get_url _CLEAR="ACTION type CSC_catid id i p1 CSC_id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	{foreach from=$navChain item=oItem}
	{if $oItem.id!=0}
	<li><a href="{get_url _CLEAR="ACTION i p1 type id CSC_id" CSC_catid=$oItem.id}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$oItem.title}</span></a></li>
	{/if}
	{/foreach}
	{strip}
	<li><a href="{get_url}">
	<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
	&nbsp;<span>
	{if $data.id<0}
		{#title_create#}&nbsp;
		{if $data.type=='cat'}
			{#new_category#}
		{else}
			{#new_record#}
		{/if}
	{else}
		{#title_edit#} <b>{$data.title}</b>
	{/if}</span></a>
	{/strip}
</ul>

<h1>{if $data.id<0}
		{#title_create#}
		{if $data.type=='cat'}
			{#new_category#}
		{else}
			{#new_record#}
		{/if}
	{else}
		{#title_edit#} {$data.title}
	{/if}</h1>

<form action="{get_url _CLEAR="ACTION CSC_id"}" method="POST" enctype="multipart/form-data">
{ksTabs NAME=catsubcat_edit head_class=tabs2 title_class=bold}
	{ksTab NAME=$smarty.config.tabs_common selected=1}
		<input type="hidden" name="module" value="catsubcat"/>
		<input type="hidden" name="CSC_id" value="{$data.id}"/>
		<input type="hidden" name="CSC_catid" value="{$data.parent_id}"/>
		<input type="hidden" name="CSC_parent_id" value="{$data.parent_id}"/>
		<input type="hidden" name="ACTION" value="save"/>
		<div class="form">
			<table class="layout">
				{if $is_ajax_frame!=1}
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				{/if}
				<tr class="is_necessary_light">
					<td>{if $data.type=='cat'}{Title field=title_category}{else}{Title field=title_record}{/if}</td>
					<td><input type="text" name="CSC_title" value="{$data.title|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/></td>
				</tr>
				<tr>
					<td colspan="2">{if $data.type=='cat'}{Title field=content_category}{else}{Title field=content_record}{/if}</td>
				</tr>
				<tr>
					<td colspan="2"><textarea name="CSC_content" style="width:100%;height:200px;"/>{ksParseText}{$data.content}{/ksParseText}</textarea></td>
				</tr>
				<tr>
					<td>{if $data.type=='cat'}{Title field=img_category}{else}{Title field=img_record}{/if}</td>
					<td>
						{if $data.img!=''}
							<a href="/uploads{$data.img}" alt="{#view_in_full_size#}" target="_blank">{Pic src="/uploads`$data.img`" width="100" height="100" mode="crop"}</a><br/>
							<label><input type="checkbox" name="CSC_img_del" value="1"/> {#delete#}</label><br/>
							{#replace#}<br/>
						{/if}
						<input type="file" name="CSC_img" value="" style="width:100%"/>
					</td>
				</tr>
			</table>
		</div>
	{/ksTab}
	{ksTab NAME=$smarty.config.tabs_additional hide=1}
	<div class="form">
	<table class="layout">
		{if $is_ajax_frame!=1}
		<tr class="titles">
			<th width=30%><h3>{#header_field#}</h3></th>
			<th width=70%><h3>{#header_value#}</h3></th>
		</tr>
		{/if}
		{if $data.id!=0}
		<tr class="is_necessary_light">
			<td>{if $data.type=='cat'}{Title field="text_ident_category"}{else}{Title field="text_ident_record"}{/if}</td>
			<td>{if $data.text_ident==''}
				<input type="text" name="CSC_text_ident" class="form_input" value="" style="width:98%" class="form_input"/>
				{else}
				<input type="hidden" name="CSC_text_ident" value="{$data.text_ident}"/><span id="textIdentText">{$data.text_ident}</span>
				<img src="{#images_path#}/icons2/edit.gif" alt="{#edit_text_ident#}" onclick="if(confirm('{#edit_text_ident_confirm#}')) return obCatsubcat.toggleTextIdentEdit(); else return false;"/>
				{/if}
			</td>
		</tr>
		{/if}
		<tr>
			<td>{if $data.type=='cat'}{Title field="description_category"}{else}{Title field="description_record"}{/if}</td>
			<td><textarea name="CSC_description" style="width:98%;height:100px;" class="form_input">{$data.description}</textarea></td>
		</tr>
		<tr>
			<td>{if $data.type=='cat'}{Title field="active_category"}{else}{Title field="active_record"}{/if}</td>
			<td><select name="CSC_active" style="width:98%">
					<option value="0" {if $data.active==0}selected="selected"{/if}>{#inactive#}</option>
					<option value="1" {if $data.active==1}selected="selected"{/if}>{#active#}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{if $data.type=='cat'}{Title field="orderation_category"}{else}{Title field="orderation_record"}{/if}</td>
			<td><input type="text" name="CSC_orderation" value="{$data.orderation|intval}" size="3" class="form_input"/></td>
		</tr>
		<tr>
			<td>{Title field="date_add"}</td>
			<td>
				{ShowCalendar field="CSC_date_add" title=$smarty.config.select_date value=$data.date_add}
			</td>
		</tr>
		{if $data.id > 0}
		<tr>
			<td>{if $data.type=='cat'}{Title field="parent_id_category"}{else}{Title field="parent_id_record"}{/if}</td>
			<td><select id="CSC_parent_id" name="CSC_parent_id" style="width: 200px;">
					{foreach from=$tree_to_move_to key=tree_leaf_key item=tree_leaf}
					<option value="{$tree_leaf.id}"{if $tree_leaf.id==$data.parent_id} selected="selected"{/if}>{$tree_leaf.list_title}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		{/if}
	</table>
	</div>
	{/ksTab}
	{if $data.type=='elm'}
		{ksTab NAME=$smarty.config.tabs_links}
			<div class="form">
				<table class="layout">
					{if $is_ajax_frame!=1}
					<tr class="titles">
						<th width=30%><h3>{#header_field#}</h3></th>
						<th width=70%><h3>{#header_value#}</h3></th>
					</tr>
					{/if}
					<tr>
						<td>{Title field="element_links"}</td>
						<td><select name="CSC_links[]" multiple="multiple" size="10" style="width:100%" class="input_field">
							{foreach from=$tree_to_move_to key=tree_leaf_key item=tree_leaf}
								<option value="{$tree_leaf.id}"{if is_array($data.links) and in_array($tree_leaf.id,$data.links)} selected="selected"{/if}>{$tree_leaf.list_title}</option>
							{/foreach}
							</select>
						</td>
					</tr>
				</table>
			</div>
		{/ksTab}
	{/if}
	{ksTab NAME=$smarty.config.tabs_seo}
		<div class="form">
			<table class="layout">
				{if $is_ajax_frame!=1}
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				{/if}
				<tr>
					<td>{Title field="seo_title"}</td>
					<td><input type="text" name="CSC_seo_title" value="{$data.seo_title|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/></td>
				</tr>
				<tr>
					<td>{Title field="seo_description"}</td>
					<td><input type="text" name="CSC_seo_description" value="{$data.seo_description|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/></td>
				</tr>
				<tr>
					<td>{Title field="seo_keywords"}</td>
					<td><input type="text" name="CSC_seo_keywords" value="{$data.seo_keywords|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/></td>
				</tr>
			</table>
		</div>
	{/ksTab}
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
	{if $menu!=''}
		{ksTab NAME=$smarty.config.tabs_addtomenu hide=1}
		{strip}
			<div class="form">
				<table class="layout">
					{if $is_ajax_frame!=1}
						<tr class="titles">
						<th width=30%><h3>{#header_field#}</h3></th>
						<th width=70%><h3>{#header_value#}</h3></th>
						</tr>
					{/if}
					<tr>
						<td>{if $data.type=='cat'}{Title field="add_menu_item_category"}{else}{Title field="add_menu_item_record"}{/if}</td>
						<td><select name="CM_add" onchange="obCatsubcat.togglePanel(this)">
							<option value="1">{#yes#}</option>
							<option value="0" selected="selected">{#no#}</option>
							</select>
						</td>
					</tr>
				</table>
				<table class="layout" style="display:none;" id="panel">
					<tr>
						<td width="30%">{Title field="menu_type"}</td>
						<td width="70%">
							<select name="CM_type_id" value="{$menu.types[0].id}" onchange="obCatsubcat.selectType(this.value, 0, document.getElementById('item0')); return false;">
							{foreach from=$menu.types item=oItem}
								<option value="{$oItem.id}">{$oItem.name}</option>
							{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<td>{Title field="menu_parent_id"}<br/>
							<label>
								<input type="radio" name="CM_parent_id" value="0" checked="checked"/>
								{#create_in_root#}
							</label>
						</td>
						<td><div class="tree" id="item0"></div>
						</td>
					</tr>
					<tr>
						<td>{Title field="menu_item_name"}</td>
						<td><input type="text" name="CM_anchor" value="{$data.anchor|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/></td>
					</tr>
					<tr>
						<td>{Title field="menu_item_orderation"}</td>
						<td><input type="text" name="CM_orderation" size="3" value="{$data.orderation|intval}" class="form_input"/></td>
					</tr>
					<tr>
						<td>{Title field="menu_item_image"}</td>
						<td><input type="file" name="CM_img" value="" style="width:95%" class="form_input"/><br>
							{if $data.img!=""}<img src="/uploads/{$data.img}"><br/>
							<input type="checkbox" name="CSC_img_del" value="1"/> Удалить{/if}
						</td>
					</tr>
					<tr>
						<td>{Title field="menu_item_target"}</td>
						<td><select name="CM_target" style="width:100%">
							<option value="" {if $data.target eq ""}selected="selected"{/if}>[{#target_not_set#}]</option>
							<option value="_blank" {if $data.target eq "_blank"}selected="selected"{/if}>[{#target_blank#}]</option>
							<option value="_self" {if $data.target eq "_self"}selected="selected"{/if}>[{#target_self#}]</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
		{/strip}{/ksTab}
	{/if}
	{/ksTabs}
	<div class="form_buttons">
		<div><input type="submit" class="save" value="{#save#}"/></div>
		<div><input type="submit" name="update" value="{#apply#}"/></div>
		<div><a href="{get_url _CLEAR="ACTION id type CSC_id" CSC_catid=$data.parent_id}" class="cancel_button">{#cancel#}</a></div>
	</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title_edit#}</dt>
<dd>{#hint_edit#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}