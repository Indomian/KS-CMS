{literal}
<script type="text/javascript">
/* Функция, выполняющая ajax-запрос на отображение следующего шага вставки виджета в шаблон */
function nextStep(action, param, post)
{
	var options={};
	document.loading=ShowLoading();
	if (post && (post.length > 0))
	{
		options.contentType='application/x-www-form-urlencoded';
		options.type="POST";
		options.data=post;
	}
	else
	{
		options.type="GET";
		options.contentType='text/plain';
	}
	options.success=function(data){
		$('#widgetCont>div').html(data);
	    HideLoading(document.loading);
	};
	options.url="/admin.php?module=main&modpage=widget&action=" + action + "&" + param;
	$.ajax(options);
	return false;
}

/* Функция преобразования параметров (добавлена поддержка множественных списков) */
function parseData(obList, module, widget)
{
	var aParams = new Array();
	for (i = 0; i < obList.childNodes.length; i++)
	{
		if ((obList.childNodes[i].tagName == 'SELECT') ||
		   (obList.childNodes[i].tagName=='INPUT') ||
		   (obList.childNodes[i].tagName=='TEXTAREA'))
		{
			/* Необходимо собрать все значения select'а в одну переменную */
			var sValue = '';
			if (obList.childNodes[i].tagName == 'SELECT')
			{
				var sOptions = obList.childNodes[i].options;
				for (q = 0; q < sOptions.length; q++)
				{
					if (sOptions[q].selected == 1)
					{
						if (sValue != '')
							sValue += ';';
						sValue += sOptions[q].value;
					}
				}
			}
			else
				sValue = obList.childNodes[i].value;
			var sParam = encodeURIComponent(obList.childNodes[i].name);
			sParam += "=";
			sParam += encodeURIComponent(sValue);
			aParams.push(sParam);
		}
	}
	nextStep('code','wmod='+module+'&w='+widget,aParams.join('&'));
}

$(document).ready(function(){nextStep('');});
</script>

{/literal}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url _CLEAR="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#titles#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{if $data.is_sub==1}{$data.name}/{$data.sub_name}{else}{$data.name}{/if}</span></a></li>
</ul>
<h1>{#title_edit#} {if $data.is_sub==1}{$data.name}/{$data.sub_name}{else}{$data.name}{/if}</h1>
{ksTabs NAME=templates head_class=tabs2 title_class=bold}
	{ksTab selected="1" NAME=$smarty.config.tabs_template}
<div class="form">
	<table border="0" width="100%" style="padding:0px;margin:0px;border:0px;">
		<tr><td style="padding:0px;margin:0px;border:0px;" width="100%">
		{if $data.is_sub!=1}
			{ksTabs NAME=schems head_class=tabs2 title_class=bold}
			{foreach from=$data.templates item=oItem key=oKey name=schemes}
				{ksTab selected=$smarty.foreach.schemes.iteration NAME=$oKey}
					<form action="{get_url _CLEAR="ACTION template id"}" method="POST">
						<input type="hidden" name="ACTION" {if $data.is_sub==1}value="savesub"{else}value="save"{/if}/>
						<input type="hidden" name="id" value="{$data.name}"/>
						{if $data.is_sub==1}
						<input type="hidden" name="s_module" value="{$data.module}"/>
						<input type="hidden" name="file" value="{$data.file}"/>
						{/if}
						<input type="hidden" name="scheme" value="{$oKey}"/>
						<table class="layout">
						{if $data.name==""}
							<tr>
								<td>{Title field="name"}</td>
								<td><input type="text" name="id" value="" style="width:95%" class="form_input"/>
								<input type="hidden" name="is_new" value="1"/>
							</td></tr>
						{/if}
						{if $data.is_sub==1 AND $data.make_copy==1}
							<tr>
								<td>{Title field="name"}</td>
								<td><input type="text" name="subid" value="{$data.new_name|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/>
								<input type="hidden" name="copysub" value="1"/>
							</td></tr>
						{/if}
						<tr>
							<th colspan="2">{Title field="template"} <b>{$oKey}</b></th>
						</tr>
						<tr>
							<td colspan="2">
								<textarea name="template_file" style="width:100%;height:300px;" class="form_textarea">{$oItem}</textarea>
							</td>
						</tr>
						</table>
						<div class="form_buttons">
			    			<div><input type="submit" value="{#save#}" class="save"/></div>
			    			<div><input type="submit" name="update" value="{#apply#}"/></div>
			    			<div><a href="{get_url _CLEAR="ACTION id type template"}" class="cancel_button">{#cancel#}</a></div>
	   					</div>
					</form>
				{/ksTab}
				{/foreach}
			{/ksTabs}
		{else}
			<form action="{get_url _CLEAR="ACTION template id"}" method="POST">
				<input type="hidden" name="ACTION" value="savesub"/>
				<input type="hidden" name="id" value="{$data.name}"/>
				<input type="hidden" name="s_module" value="{$data.module}"/>
				<input type="hidden" name="file" value="{$data.file}"/>
				<table class="layout">
				{if $data.is_sub==1 AND $data.make_copy==1}
					<tr>
						<td>{Title field="name"}</td>
						<td>
							<input type="text" name="subid" value="{$data.new_name|htmlspecialchars:2:"UTF-8":false}" style="width:95%" class="form_input"/>
							<input type="hidden" name="copysub" value="1"/>
						</td>
					</tr>
				{/if}
				<tr>
					<th colspan="2">{Title field="template"}</th>
				</tr>
				<tr>
					<td colspan="2">
						<textarea name="template_file" style="width:100%;height:300px;" class="form_textarea">{$data.template}</textarea>
					</td>
				</tr>
				</table>
				<div class="form_buttons">
	    			<div><input type="submit" value="{#save#}" class="save"/></div>
	    			<div><input type="submit" name="update" value="{#apply#}"/></div>
	    			<div><a href="{get_url _CLEAR="ACTION id type template"}" class="cancel_button">{#cancel#}</a></div>
				</div>
			</form>
		{/if}
		</td>
		{strip}
		<td valign="center" style="width:5px;padding:0px;border:0px;margin:0px;vertical-align:middle;">
			<a onclick="showWidgetPanel()" id="larrow"><img src="{#images_path#}/arrows/02v.gif" border="0"/></a>
			<a onclick="hideWidgetPanel()" style="display:none;" id="rarrow"><img src="{#images_path#}/arrows/02v2.gif" border="0"/></a>
		</td>
		<td width="0" valign="top" id="widgetCont" style="padding:0px;margin:0px;border:0px;width:0px;overflow:hidden;display:none;">
			<div style="width:0px;overflow:auto;height:100%;">&nbsp;</div>
		</td></tr>
		{/strip}
	</table>
</div>
{/ksTab}
{ksTab NAME=$smarty.config.tabs_modules}
<div class="form">
	<table class="layout">
	{if $data.is_sub==1}
		<tr>
			<td colspan="2">
				<img src="{#images_path#}/{if $oTemplate.is_default==0}02{else}11{/if}.gif" border="0" alt="{if $oTemplate.is_default==0}{#from_default_template#}{else}{#from_local_copy#}{/if}" title="{if $oTemplate.is_default==0}{#from_default_template#}{else}{#from_local_copy#}{/if}"/>
				<a href="{get_url _CLEAR='template' ACTION=edit}">{#global_template#}</a>
			</td>
			<td>{#global_template_content#}</td>
			<td></td>
		</tr>
	{/if}

	<tr>
		<td colspan="4">
			<a href="#" onclick="obTemplates.collapseAll(true); return false;">{#show_all#}</a> | <a href="#" onclick="obTemplates.collapseAll(false); return false;">{#hide_all#}</a>
		</td>
	</tr>

	<!-- Начало вывода списка шаблонов для очередного модуля -->
	{foreach from=$data.modules item=oItem key=oKey name=modules}
	<tr>
		<th colspan="4">
		{if $data.module!=$oKey}
			<img style="position: relative; top: 2px;" id="sub_list_img_{$smarty.foreach.modules.iteration}" width="13" height="13" alt="icon" src="{#images_path#}/icons_menu/plus.gif" onclick="obTemplates.collapseSub({$smarty.foreach.modules.iteration});" />
		{else}
			<img style="position: relative; top: 2px;" id="sub_list_img_{$smarty.foreach.modules.iteration}" width="13" height="13" alt="icon" src="{#images_path#}/icons_menu/minus.gif" onclick="obTemplates.collapseSub({$smarty.foreach.modules.iteration});" />
		{/if}
		&nbsp;{$oItem.name}
		</th>
	</tr>
	<tr>
		<td>
			<div name="sub_list" id="sub_list_{$smarty.foreach.modules.iteration}" {if $data.module!=$oKey}style="display: none;"{/if}>
			<table class="layout">

			{foreach from=$oItem.widgets item=oTemplate name=fList}
			<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
				{if $oTemplate.is_user==1}<td width="40">&nbsp;</td>{/if}
				<td {if $oTemplate.is_user!=1}colspan="2"{/if}>
					<img src="{#images_path#}/icons2/{if $oTemplate.is_default==0}local{else}included{/if}.gif" border="0" alt="{if $oTemplate.is_default==0}{#from_local_copy#}{else}{#from_default_template#}{/if}"/>
					<a href="{get_url ACTION=editsub template=$oTemplate.url}">{if $oTemplate.is_user==1}{$oTemplate.name}{else}{$oTemplate.description}{/if}</a>
				</td>
				<td>{if $oTemplate.is_user==1}{#user_template#}{else}{#standart_template#}{/if}</td>
				<td>
					<a href="{get_url ACTION=copysub template=$oTemplate.url}">{#make_copy#}</a>
					{if $oTemplate.is_user==1}
					<br/><a href="{get_url ACTION=deletesub template=$oTemplate.url}">{#delete#}</a>
					{/if}
				</td>
			</tr>
			{/foreach}
			</table>
			</div>

		</td>
	</tr>
	{/foreach}
	</table>
</div>
{/ksTab}
{/ksTabs}
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
{if $data.is_sub==1}
	{if $data.help}
		<dt>{#widget_edit_info#} "{$data.widget_name}"</dt>
		<dd>{$data.help}</dd>
	{/if}
{else}
 	<dt>{#template_vars#}Переменные, которые могут быть использованы в шаблоне</dt>
	<table class="tbl_info">
		<tr>
			<th width="30%">{#header_title#}</th>
			<th width="70%">{#header_var#}</th>
		</tr>
		<tr><td>{#var_main_content#}</td><td>{ldelim}$output.main_content{rdelim}</td></tr>
		<tr>
			<td>{#var_site_url#}</td>
			<td>{ldelim}$SITE.home_url{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_home_title#}</td>
			<td>{ldelim}$SITE.home_title{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_home_description#}</td>
			<td>{ldelim}$SITE.home_descr{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_home_keywords#}</td>
			<td>{ldelim}$SITE.home_keywrds{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_copyright#}</td>
			<td>{ldelim}$SITE.copyright{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_admin_email#}</td>
			<td>{ldelim}$SITE.admin_email{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_time_format#}</td>
			<td>{ldelim}$SITE.time_format{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_cur_url#}</td>
			<td>{ldelim}get_url{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_title#}</td>
			<td>{ldelim}$TITLE{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_templates_path#}</td>
			<td>{ldelim}$templates_files_folder{rdelim}</td>
		</tr>
		<tr>
			<td>{#var_current_global_template#}</td>
			<td>{ldelim}$glb_tpl{rdelim}</td>
		</tr>
	</table>
{/if}
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}