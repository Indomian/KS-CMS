{config_load file=admin.conf section=subscribe}
<script type="text/javascript" src="/js/catsubcat/admin.js"></script>
{ShowEditor object="textarea[name=description]" theme="advanced" path=$data.URL}
{literal}
<script type="text/javascript">
	
	function checkAll(general_checkbox)
	{
		var list_table = document.getElementById('list_table');
		var input_elements = list_table.getElementsByTagName('INPUT');
		for (i = 0; i < input_elements.length; i++)
		{
			if (input_elements[i].getAttribute('type') == "checkbox")
				input_elements[i].checked = general_checkbox.checked;
		}
		isAnythingChecked();
	}
	
	function isAnythingChecked()
	{
		var isChecked = false;
		var list_table = document.getElementById('list_table');
		var input_elements = list_table.getElementsByTagName('INPUT');
		for (i = 0; i < input_elements.length; i++)
		{
			if (input_elements[i].getAttribute('type') == "checkbox")
				if (input_elements[i].checked == true)
					isChecked = true;
		}
		
		
	}
	
</script>
{/literal}
<script type="text/javascript">
	$(document).bind("InitCalendar",function()
		{ldelim}
		$("#date_active").datetimepicker(
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
				$("#date_active").datetimepicker('show')
			{rdelim}
		);
		{rdelim}
	);
	$(document).ready(function(){ldelim}$(document).trigger("InitCalendar");{rdelim});
</script>
<ul class="nav" id="navChain">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="{get_url _CLEAR="ACTION id i p1"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_subscribe#}</span></a></li>
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
    	{#title_new_subscribe#} 
    {else}
    	{#title_edit_subscribe#} <b>{$data.title}</b>
    {/if}</span></a>
    {/strip}
</ul>
<h1>{if $data.id<0}
    	{#title_new_subscribe#}
    {else}
    	{#title_edit_subscribe#} {$data.title}
    {/if}</h1>

<form action="{get_url _CLEAR="ACTION id"}" method="POST" enctype="multipart/form-data">
{ksTabs NAME=subscribe_edit head_class=tabs2 title_class=bold}
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
	    	<td>{#subscribe_type#}</td>
    		<td>
    		<input type="text" name="SB_uin" style="width:100px;" {$isReadonly} value="{$data.uin|intval|default:"-1"}"/>
    		<input type="button" onclick="SelectUser()" value="..." {$isDisabled}/>
    		{#user_id_hint#}
    		</td>
	    	
	    </tr>
	    <tr>
	    	<td>{#email#}</td>
	    	<td><input type="text" name="SB_email" value="{$data.email}" style="width:100%"/></td>
	    </tr>
	    <td>
    		{#date_add#}
    	</td>
    	<td>
    		<input type="text" readonly="readonly" value="{if $data.date_add}{$data.date_add|date_format:"%d.%m.%Y"}{else}{$smarty.now|date_format:"%d.%m.%Y"}{/if}" style="width:50%"/>
    	</td>
	   	<tr>
    		<td>{#active_subscribe#}</td>
    		<td><select name="SB_active" style="width:30%">
    				<option value="0" {if $data.active==0}selected="selected"{/if}>{#no#}</option>
    				<option value="1" {if $data.active==1}selected="selected"{/if}>{#yes#}</option>
			    </select>
    		</td>
    	</tr>
    	<tr>
    	<td>
    		{#date_active#}
    	</td>
    	<td>
    	{if $data.date_active && $data.active}
    		<input type="text" id="date_active" readonly="readonly" name="SB_date_active" value="{$data.date_active|date_format:"%d.%m.%Y"}" style="width:50%"/>
    		<img src="{#images_path#}/calendar/img.gif" id="date_add_btn" style="border: 0pt none ; cursor: pointer;" title="{#calendar_hint#}" align="absmiddle"/>
    	{else}
    	{#no_active_subscribe#}
    	{/if}	
    	</td>
    	</tr>
    </table>
    </div>
	{/ksTab}
	
	
	{ksTab NAME=$smarty.config.subscribe hide=1}
	<div class="form">
	<table class="layout">
    	{if $is_ajax_frame!=1}
	    <tr class="titles">
	    	<th width=30%><h3>{#header_field#}</h3></th>
	    	<th width=70%><h3>{#header_value#}</h3></th>
	    </tr>
    	{/if}
    	<tr>
    		
    		<td>{#newsletters#}</td>
    		
    		<td>
    			<input type="checkbox" onclick="checkAll(this);" />&nbsp;&nbsp;{#all#}<br>
    			<div id="list_table">	
    			{foreach from=$data.newsletters item=oItem key=oKey}
    				<input name="SB_news[]" type="checkbox" value="{$oItem.id}" {if $oItem.select}checked{/if} onclick="isAnythingChecked();" />&nbsp;&nbsp;{$oItem.name}<br>
        		{/foreach}
        		</div>
    		</td>
    	</tr>
    	<tr>
    		<td>{#format#}</td>
    		<td><select name="SB_format" style="width:20%">
        			
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