{include file="admin/blocks/navChain.tpl"}
<h1>{$smarty.config.title_config_columns}</h1>
<form action="{get_url action="savecols"}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="savecols"/>
	{ksTabs NAME=confcols head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.title_config_columns selected=1}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_column#}</th>
    					<th width="70%">{#header_show#}</th>
    				</tr>
    				{foreach from=$columns item=oItem key=oKey}
    				<tr>
    					<td>{$oItem.title}</td>
    					<td><input type="checkbox" name="show[{$oKey}]" value="1" {if $oItem.show==1}checked="checked"{/if}/></td>
					</tr>
					{/foreach}
    			</table>
    		</div>
		{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    		<input type="submit" value="{#cancel#}" name="cancel" class="cancel"/>
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