<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
</ul>
<h1>{#titles#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url}" method="POST">
						<input type="hidden" name="ACTION" value="clearCache"/>
						<input type="hidden" name="module" value="main"/>
						<input type="hidden" name="modpage" value="templates"/>
						<input type="submit" class="add_div2" style="background-image:url({#images_path#}/icons2/reload.gif);background-position:5px 3px;" value="{#clear_cache#}"/>
					</form>
				</div>
			</td>
			<td>
				<div>
					<form action="{get_url}" method="POST">
						<input type="hidden" name="ACTION" value="clearPicCache"/>
						<input type="hidden" name="module" value="main"/>
						<input type="hidden" name="modpage" value="templates"/>
						<input type="submit" class="add_div2" style="background-image:url({#images_path#}/icons2/reload.gif);background-position:5px 3px;" value="{#clear_images_cache#}"/>
					</form>
				</div>
			</td>
			<td width="100%">
				<span>{#small_hint#}</span>
			</td>
		</tr>
	</table>
</div>
{ksTabs NAME=main_templates head_class=tabs2 title_class=bold}
	{ksTab NAME=$smarty.config.tabs_templates}
		<div class="users">
			<table class="layout">
    		<tr>
    			<th width="100%">{#header_title#}</th>
    		</tr>
			{foreach from=$dataList item=oItem key=oKey name=fList}
    		<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
	    		<td>{$oItem}</td>
	    	</tr>
			{/foreach}
    	</table>
		</div>
	{/ksTab}
	{ksTab NAME=$smarty.config.tabs_template_links selected=1}
    <form action="{get_url _CLEAR="action"}" method="POST">
    	<div class="form">
    		<input type="hidden" name="action" value="saveLinks">
    		<table class="layout">
			    <tr>
			    	<th colspan="5">
				    	<h3>{#links_title#}</h3>
			    		<p>{#links_hint#}</p>
			       	</th>
			    </th>
			    <tr>
			    	<th>{#template#}</th>
			    	<th>{#condition#}</th>
			    	<th>{#path#}</th>
			    	<th>{#orderation#}</th>
			    	<th>{#del#}</th>
			    </tr>
    			{if $linksList.LINKS}
					{foreach from=$linksList.LINKS item=oTemplate key=oPath}{strip}
						<td>
							<select name="links[{$oTemplate.id}][template_path]" style="width:100%" class="form_input">
							{foreach from=$linksList.TEMPLATES item=oTpl}
								<option value="{$oTpl}" {if $oTemplate.template_path==$oTpl}selected="selected"{/if}>{$oTpl}</option>
							{/foreach}
							</select>
						</td>
						{*Здесь происходит определение вывода условия*}
						<td>
							<select name="links[{$oTemplate.id}][type]" style="width:100%" class="form_input changeType" onchange="document.obTemplates.TypeChanged(this,'links[{$oTemplate.id}]');">
								<option value="" {if $oTemplate.type==''}selected="selected"{/if}>{#mode_all_inner#}</option>
								<option value="=" {if $oTemplate.type=='='}selected="selected"{/if} >{#mode_exactly#}</option>
								<option value="get" {if $oTemplate.type=='get'}selected="selected"{/if}>{#mode_get_param#}</option>
								<option value="reg" {if $oTemplate.type=='reg'}selected="selected"{/if}>{#mode_regexp#}</option>
								<option value="userGroup" {if $oTemplate.type=='userGroup'}selected="selected"{/if}>{#mode_user_group#}</option>
							</select>
						</td>
						<td id="realTD{$oTemplate.id}">
							{if $oTemplate.type=='userGroup'}
							<select name="links[{$oTemplate.id}][function1]" class="form_input">
									<option value="">{#any#}</option>
								{foreach from=$groups item=oItem}
									<option value="{$oItem.id}" {if $oItem.id==$oTemplate.function1}selected="selected"{/if}>{$oItem.title}</option>
								{/foreach}
							</select>
							{else}
							<input type="text" name="links[{$oTemplate.id}][url_path]" value="{$oTemplate.url_path}" style="width:95%" class="form_input"/>
							{/if}
						</td>
						<td>
							<input type="text" name="links[{$oTemplate.id}][orderation]" value="{$oTemplate.orderation|intval}" class="form_input" style="100px;"/></td>
						</td>
						<td>
							<input type="checkbox" name="delete[{$oTemplate.id}]" value="1"/>
						</td>
					</tr>{/strip}
					{/foreach}
				{/if}
				<tr>{strip}
					<td>
						<select name="newlinks[{counter assign=step}{$step}][template_path]" style="width:100%" class="form_input">
						{foreach from=$linksList.TEMPLATES item=oTpl}
							<option value="{$oTpl}">{$oTpl}</option>
						{/foreach}
						</select>
					</td>
					{*Здесь происходит определение вывода условия*}
					<td>
						<select name="newlinks[{$step}][type]" style="width:100%" class="form_input changeType">
							<option value="" selected="selected">{#mode_all_inner#}</option>
							<option value="=">{#mode_exactly#}</option>
							<option value="reg">{#mode_regexp#}</option>
							<option value="get">{#mode_get_param#}</option>
							<option value="userGroup">{#mode_user_group#}</option>
						</select>
					</td>
					<td id="newTD{$step}">
						<input type="text" name="newlinks[{$step}][url_path]" value="" style="width:95%" class="form_input"/>
					</td>
					<td>
						<input type="text" name="newlinks[{$step}][orderation]" value="" class="form_input" style="100px;"/></td>
					</td>
				{/strip}</tr>
			</table>
		</div>
		<div class="form_buttons">
	    	<div>
	    		<input type="submit" value="{#save#}" class="save"/>
	    	</div>
	   	</div>
	</form>
	{/ksTab}
{/ksTabs}
{include file='admin/common/hint.tpl' title=$smarty.config.titles description=$smarty.config.hint_list icon="/big_icons/settings.gif"}
