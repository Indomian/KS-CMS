{if $show_editor==1}
	{ShowEditor object="textarea[name=OS_answer]" theme="advanced" path=$data.URL}
{/if}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="/admin.php?module=guestbook2"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    {strip}
    <li>
    	<a href="{get_url}">
    		<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
    		&nbsp;<span>
    		{if $data.id<1}
    			{#create_title#}
    		{else}
    			{#edit_title#} <b>{$data.title}</b>
    		{/if}</span>
    	</a>
    </li>
    {/strip}
</ul>

<h1>{if $data.id<1}{#create_title#}{else}{#edit_title#} {$data.title}{/if}</h1>

<form action="{get_url _CLEAR="ACTION CSC_id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="{$data.id}"/>
	<input type="hidden" name="OS_id" value="{$data.id}"/>
	<input type="hidden" name="action" value="save"/>
	{ksTabs NAME=gb2_edit head_class=tabs2 title_class=bold}
	{ksTab NAME=$smarty.config.tabs_common selected=1}
	<div class="form">
    	<table class="layout">
    		{if $is_ajax_frame!=1}
	    	<tr class="titles">
	    		<th width=30%><h3>{#header_field#}</h3></th>
	    		<th width=70%><h3>{#header_value#}</h3></th>
	    	</tr>
    		{/if}
    		<tr>
    			<td>{Title field="title"}</td>
    			<td><input type="text" name="OS_title" value="{$data.title|htmlspecialchars:2:"UTF-8":false}" style="width:100%"/></td>
    		</tr>
    		<tr>
    			<td>{Title field="active"}</td>
    			<td><input type="checkbox" name="OS_active" value="1" {if $data.active==1}checked="checked"{/if}/></td>
    		</tr>
    		<tr class="is_necessary_light">
    			<td>{Title field="content"}</td>
    			<td><textarea  name="OS_content" style="width:100%;height:150px;">{$data.content}</textarea></td>
    		</tr>
    		<tr>
    			<td>{Title field="user_name"}</td>
    			<td>{$data.user_name} {if $data.user_id>0}<a href="/admin.php?module=main&modpage=users&ACTION=edit&id={$data.user_id}">[{$data.user_id}]</a>{/if}</td>
    		</tr>
    		<tr>
    			<td>{Title field="category"}</td>
    			<td>
    				<select name="OS_category_id">
    					{foreach from=$categories item=oItem}
    					<option value="{$oItem.id}" {if $data.category_id==$oItem.id}selected="selected"{/if}>{$oItem.title}</option>
    					{/foreach}
    				</select>
    			</td>
    		</tr>
    		<tr>
    			<td>{Title field="answer"}</td>
    			<td><textarea  name="OS_answer" style="width:100%;height:150px;">{$data.answer.content}</textarea></td>
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
	<dt>{#title_edit#}</dt>
	<dd>{#hint_edit#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
