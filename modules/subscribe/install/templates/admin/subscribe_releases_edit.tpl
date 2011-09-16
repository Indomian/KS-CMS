{config_load file=admin.conf section=subscribe}
<script type="text/javascript" src="/js/catsubcat/admin.js"></script>
{ShowEditor object="textarea[name=SB_content]" theme="advanced" path=$data.URL}
<script type="text/javascript">

{literal}
	function check_tab(t)
	{
		if(t.value==-1)
		{
			document.getElementById('suscribes').style.visibility="visible";
		}
		else
		{
			document.getElementById('suscribes').style.visibility="hidden";
		}
	}
	function clearText()
	{
		var list = document.getElementById('lists');
		list.value='';
	}
	function clearAll(list_name)
	{
		var list_table = document.getElementById(list_name);
		var input_elements = list_table.getElementsByTagName('INPUT');
		for (i = 0; i < input_elements.length; i++)
		{
			if (input_elements[i].getAttribute('type') == "checkbox")
				input_elements[i].checked = false;
		}
	}
	function checkAll(general_checkbox,list_name,clear_list)
	{
		var list_table = document.getElementById(list_name);
		var input_elements = list_table.getElementsByTagName('INPUT');
		for (i = 0; i < input_elements.length; i++)
		{
			if (input_elements[i].getAttribute('type') == "checkbox")
				input_elements[i].checked = general_checkbox.checked;
		}
		isAnythingChecked(list_name,clear_list);
		//clearAll(clear_list);
	}
		function isAnythingChecked(list_name,clear_list)
	{
		var isChecked = false;
		var list_table = document.getElementById(list_name);
		var input_elements = list_table.getElementsByTagName('INPUT');
		for (i = 0; i < input_elements.length; i++)
		{
			if (input_elements[i].getAttribute('type') == "checkbox")
				if (input_elements[i].checked == true)
					isChecked = true;
		}
		clearAll(clear_list);
		
	}
{/literal}	
	$(document).bind("InitCalendar",function()
		{ldelim}
		$("#date_add").datetimepicker(
			{ldelim}
				dateFormat:{#date_format#},
				timeFormat:{#time_format#},
				dayNames:{#days#},
				dayNamesMin:{#daysMin#},
				dayNamesShort:{#daysShort#},
				monthNames:{#monthes#}
			{rdelim}
		);
		$("#date_add_btn").click(function()
			{ldelim}
				$("#date_add").datetimepicker('show')
			{rdelim}
		);
		{rdelim}
	);
	$(document).ready(function(){ldelim}$(document).trigger("InitCalendar");{rdelim});
	
	
</script>
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="{get_url _CLEAR="ACTION id i p1"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_releases#}</span></a></li>
    {foreach from=$navChain item=oItem}
    {if $oItem.id!=0}
    <li><a href="{get_url _CLEAR="ACTION i p1 id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$oItem.title}</span></a></li>
    {/if}
    {/foreach}
    {strip}
    <li><a href="{get_url}">
    <img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />
    &nbsp;<span>
    {if $data.id<0}
    	{#title_add_release#} 
    {else}
    	{#title_edit_release#} <b>{$data.title}</b>
    {/if}</span></a>
    {/strip}
</ul>
<h1>{if $data.id<0}
    	{#title_add_release#}
    {else}
    	{#title_edit_release#} {$data.title}
    {/if}</h1>

<form action="{get_url _CLEAR="ACTION id"}" method="POST" enctype="multipart/form-data">
{ksTabs NAME=releases_edit head_class=tabs2 title_class=bold}
	{ksTab NAME=$smarty.config.tabs_common selected=1}
	<input type="hidden" name="module" value="subscribe">
	<input type="hidden" name="SB_id" value="{$data.id}">
	<input type="hidden" name="ACTION" value="save">
	<div class="form">
    <table class="layout">
    	{if $is_ajax_frame!=1}
	    <tr class="titles">
	    	<th width=30%><h3>{#header_field#}</h3></th>
	    	<th width=70%><h3>{#header_value#}</h3></th>
	    </tr>
    	{/if}
	    <tr class="is_necessary_light">
	    	<td>{#theme#}</td>
	    	<td><input type="text" name="SB_theme" value="{$data.theme|htmlspecialchars:2:"UTF-8":false}" style="width:100%"/></td>
	    </tr>
	    <tr>
	    	<td colspan="2">{#content#}</td>
	    </tr>
	    <tr>
	    	<td colspan="2"><textarea name="SB_content" style="width:100%;height:200px;"/>{ksParseText}{$data.content}{/ksParseText}</textarea></td>
	    </tr>
	    <tr class="is_necessary_light">
	    	<td>{#release_from#}</td>
	    	<td><input type="text" name="SB_from" value="{$data.from|htmlspecialchars:2:"UTF-8":false}" style="width:100%"/></td>
	    </tr>
	    <tr class="is_necessary_light">
	    	<td>{#release_to#}</td>
	    	<td><input type="text" name="SB_to" value="{$data.to|htmlspecialchars:2:"UTF-8":false}" style="width:100%"/></td>
	    </tr>
	    <tr>
    		<td>{#newsletter#}</td>
    		<td><select name="SB_newsletter" style="width:30%" onChange="check_tab(this)">
    			<option value="-1" {if $data.newsletter==-1}selected="selected"{/if}>Нет привязки</option>
    			{foreach from=$data.newsletters item=oItem}
    				<option value="{$oItem.id}" {if $oItem.id==$data.newsletter}selected="selected"{/if}>{$oItem.name}</option>
       			{/foreach}
        		</select>
    		</td>
    	</tr>
    </table>
    </div>
	{/ksTab}
	
	
	{ksTab NAME=$smarty.config.tabs_subscribes hide=1}
	<div class="form" id="suscribes" {if $data.newsletter!=-1}style="visibility:hidden;"{/if}>
	<table class="layout" id="list">
    	{if $is_ajax_frame!=1}
	    <tr class="titles">
	    	<th width=30%><h3>{#header_field#}</h3></th>
	    	<th width=70%><h3>{#header_value#}</h3></th>
	    </tr>
    	{/if}
    	<tr>
    		<td>{#subscribes_type1#}</td>
    		<td> 	
    		    <div id="news">	
    		    <input type="checkbox" onclick="checkAll(this,'news','groups');" />&nbsp;&nbsp;{#all#}<br>
    			{foreach from=$data.newsletters item=oItem key=oKey}
    				<input name="SB_news[]" type="checkbox" value="{$oItem.id}" {if $oItem.select}checked{/if} onclick="isAnythingChecked('news','groups');" />&nbsp;&nbsp;{$oItem.name}<br>
        		{/foreach}
        		</div>
    		</td>
    	</tr>
    	<tr>
    		<td>{#subscribes_type2#}</td>
    		<td> 	
    		    <div id="groups">
    		    <input type="checkbox" onclick="checkAll(this,'groups','news');clearText();" />&nbsp;&nbsp;{#all#}<br>
    			{foreach from=$data.groupslist item=oItem key=oKey}
    				<input name="SB_groups[]" type="checkbox" value="{$oItem.id}" {if $oItem.select}checked{/if} onclick="isAnythingChecked('groups','news');" />&nbsp;&nbsp;{$oItem.title}<br>
        		{/foreach}
        		</div>
    		</td>
    	</tr>
    	<tr>
    		<td>{#subscribes_type3#}</td>
    		<td> 	
    		   <textarea id="lists" cols="45" name="SB_list" onclick="clearAll('groups')">{foreach from=$data.list item=oItem key=oKey name=List}{if !$smarty.foreach.List.last}{$oItem|cat:"\n"}{else}{$oItem}{/if}{/foreach}</textarea>
    		</td>
    	</tr>
    	
    </table>
    </div>
    {/ksTab}
    {ksTab NAME=$smarty.config.tabs_encryption hide=1}
	<div class="form">
	<table class="layout">
    	{if $is_ajax_frame!=1}
	    <tr class="titles">
	    	<th width=30%><h3>{#header_field#}</h3></th>
	    	<th width=70%><h3>{#header_value#}</h3></th>
	    </tr>
    	{/if}
    	<tr>
    		<td>{#encryption#}</td>
    		<td><select name="SB_encryption" style="width:100%">
        			<option value="UTF-8" {if $data.encryption=='UTF-8'}selected="selected"{/if}>UTF-8</option>
        			<option value="CP1251" {if $data.encryption=='CP1251'}selected="selected"{/if}>CP-1251</option>
        			<option value="KOI8-R" {if $data.encryption=='KOI8-R'}selected="selected"{/if}>ASCII</option>
        		</select>
    		</td>
    	</tr>
    </table>
    </div>
    {/ksTab}
	{/ksTabs}
	<div class="form_buttons">
		<div><input type="submit" name="send" value="{#send#}"/></div>
		<div><input type="submit" class="save" value="{#save#}"/></div>
	    <div><input type="submit" name="update" value="{#apply#}"/></div>
	    <div><a href="{get_url _CLEAR="ACTION id"}" class="cancel_button">{#cancel#}</a></div>
	</div>
</form>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title_edit#}</dt>
<dd>{#hint_edit#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}