<script language="javascript" type="text/javascript">
function setField(from,to)
{ldelim}
    document.getElementById(to).value=document.getElementById(from).value;
    onModuleChange(document.getElementById(from).value);
{rdelim}
{literal}
function onModuleChange(module)
{
	$.get("/admin.php?module=main&modpage=fields&ACTION=onmodulechange&mod="+module,null,function(data)
		{
			$('#CM_type_root').html(data);
		});
	return false;
}

function onFieldChange(field)
{
	$.get("/admin.php?module=main&modpage=fields&ACTION=onfieldchange&field="+field,null,function(data)
		{
			$('#CM_default_root').html(data);
		});
	return false;
}
$(document).ready(function(){
	$("#CM_title").keyup(function(event){
		var regexp=new RegExp("^[a-z0-9_]+$","i");
		regexp.ignoreCase=true;
		if(!regexp.test(this.value)) $(this).addClass("invalid"); else $(this).removeClass("invalid");
		return true;}).trigger('keyup');
});
{/literal}
</script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{if $data.id<1}{#title_create#}{else}{#title_edit#} {$data.title}{/if}</span></a></li>
</ul>

<h1>{if $data.id<1}{#title_create#}{else}{#title_edit#} "{$data.title}"{/if}</h1>

<form action="{get_url _CLEAR="ACTION id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="module" value="main"/>
	<input type="hidden" name="modpage" value="fields"/>
	<input type="hidden" name="id" value="{$data.id}"/>
	<input type="hidden" name="CM_id" value="{$data.id}"/>
	<input type="hidden" name="ACTION" value="save"/>
	{ksTabs NAME=main_fields_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
    				<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr class="is_necessary_light">
    					<td>{Title field="title"}</td>
    					<td>
    						<input type="text" name="CM_title" id="CM_title" value="{$data.title|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/>
    					</td>
    				</tr>
    				<tr>
    					<td>{Title field="description"}</td>
    					<td>
    						<input type="text" name="CM_description" value="{$data.description|htmlspecialchars:2:"UTF-8":false}" style="width:98%" class="form_input"/>
    					</td>
    				</tr>
    				<tr class="is_necessary_light">
    					<td>{Title field="script"}</td>
    					<td>
    						<select name="CM_script" onchange="onFieldChange(this.value)" style="width:98%" class="form_input">
    							{foreach from=$types item=oItem key=oKey}
    								<option value="{$oKey}" {if $oKey==$data.script}selected="selected"{/if}>{$oItem}</option>
    							{/foreach}
    						</select>
    					</td>
    				</tr>
    				<tr class="is_necessary_light">
    					<td>{Title field="module"}</td>
    					<td>
    						<input type="text" name="CM_module" id="CM_module" value="{$data.module|htmlspecialchars:2:"UTF-8":false}" class="form_input"/>
    						&lt;&lt;
    						<select id="CM_module_list" onchange="setField('CM_module_list','CM_module');" class="form_input">
        						<option value="">{#select_from_list#}</option>
        						{foreach from=$modules item=oItem}
        							<option value="{$oItem.directory}">{$oItem.name} [{$oItem.directory}]</option>
        						{/foreach}
							</select>
						</td>
    				</tr>
    				<tr class="is_necessary_light">
    					<td>{Title field="type"}</td>
    					<td id="CM_type_root">{$type}</td>
    				</tr>
    				<tr>
    					<td colspan="2">{Title field="options"}</div></td>
    				</tr>
    				<tr>
    					<td id="CM_default_root" colspan="2">{configField field=$data value=$data.default prefix="CM_"}</td>
    				</tr>
    			</table>
    		</div>
    	{/strip}{/ksTab}
    {/ksTabs}
    <div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
    	<div>
    		<input type="submit" name="update" value="{#apply#}"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="ACTION id"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>
