<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=guestbook2"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url _clear="action id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#category_title#}</span></a></li>
    {strip}
    <li>
    	<a href="{get_url}">
    		<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
    		&nbsp;
    		<span>
    			{if $data.id<1}
	    			{#category_create_title#}
    			{else}
    				{#category_edit_title#} <b>{$data.title}</b>
	    		{/if}
	    	</span>
	    </a>
    {/strip}
</ul>

<h1>{if $data.id<1}{#category_create_title#}{else}{#category_edit_title#} {$data.title}{/if}</h1>

<form action="{get_url _CLEAR="ACTION CSC_id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="{$data.id}"/>
	<input type="hidden" name="OS_id" value="{$data.id}"/>
	<input type="hidden" name="action" value="save"/>
	{ksTabs NAME=gb2_cat_edit head_class=tabs2 title_class=bold}
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
    					<td>{Title field="category_title"}</td>
    					<td><input type="text" name="OS_title" value="{$data.title|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/></td>
    				</tr>
    				<tr>
		    			<td>{Title field="category_active"}</td>
    					<td><input type="checkbox" name="OS_active" value="1" {if $data.active==1}checked="checked"{/if}/></td>
    				</tr>
    				<tr class="is_necessary_light">
    					<td>{Title field="text_ident"}</td>
    					<td><input type="text" name="OS_text_ident"  class="form_input" value="{$data.text_ident|htmlspecialchars:2:"UTF-8":false}" style="width:98%"/></td>
    				</tr>
    				<tr>
		    			<td>{Title field="category_content"}</td>
    					<td><textarea  name="OS_content"  class="form_input" style="width:98%;height:150px;">{$data.content}</textarea></td>
    				</tr>
    				<tr>
    					<td>{Title field="orderation"}</td>
    					<td><input type="text" name="OS_orderation" value="{$data.orderation|intval}" style="width:100px;"  class="form_input"/></td>
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
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#category_edit_title#}</dt>
	<dd>{#category_hint_edit#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
