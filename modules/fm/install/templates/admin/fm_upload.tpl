<script type="text/javascript" src="/js/fm/loader.js"></script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="/admin.php?module=fm"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_upload#}</span></li>
</ul>

<h1>{#title_upload#}</h1>

<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
  		<td valign="middle">
			<div style="width: 10px;">&nbsp;</div>  
  		</td>  
 		<td valign="top" width="100%">
  			{ksTabs NAME=ksc_upload head_class=tabs2 title_class=bold}
  				{ksTab NAME=$smarty.config.tabs_standart_upload selected="1"}
  					<div class="form">
  						<form action="{get_url _CLEAR=action}" method="POST" enctype="multipart/form-data" name="fileForm" id="fileForm">
							<input type="hidden" name="a" value="upload" />
							<input type="hidden" name="t" value="file" />
						    <table class="layout" id="fileTable">
								<tr class="titles">
									<td width=50%><h3>{#file_name#}</h3></td>
									<td width=50%><h3>{#file_select#}</h3></td>
								</tr>
								<tr>
									<td><input type="text" class="form_input" id="file_name_1" name="file_name[file_1]" value="" style="width:70%;"></td>
									<td><input type="file" id="file_1" name="file_1" style="width:100%" onchange="filechange(this);"></td>
								</tr>
								<tr>
									<td><input type="text" class="form_input" id="file_name_2" name="file_name[file_2]" value="" style="width:70%;"></td>
									<td><input type="file" id="file_2" name="file_2" style="width:100%" onchange="filechange(this);"></td>
								</tr>
								<tr>
									<td><input type="text" class="form_input" id="file_name_3" name="file_name[file_3]" value="" style="width:70%;"></td>
									<td><input type="file" id="file_3" name="file_3" style="width:100%" onchange="filechange(this);"></td>
								</tr>
								<tr>
									<td><input type="text" class="form_input" id="file_name_4" name="file_name[file_4]" value="" style="width:70%;"></td>
									<td><input type="file" id="file_4" name="file_4" style="width:100%" onchange="filechange(this);"></td>
								</tr>
								<tr>
									<td><input type="text" class="form_input" id="file_name_5" name="file_name[file_5]" value="" style="width:70%;"></td>
									<td><input type="file" id="file_5" name="file_5" style="width:100%" onchange="filechange(this);"></td>
								</tr>
						    </table>
						    <div class="manage">
						    	<input type="submit" value="{#save#}"/>
						    	<input type="submit" name="cancel" value="{#cancel#}"/>
						    </div>
						</form>
					</div>
  				{/ksTab}
  				{ksTab NAME=$smarty.config.tabs_flash_upload hide="1"}
					<div class="form">
						<p>
						Массовая загрузка недоступна на данный момент
						</p>
					</div>
  				{/ksTab}
  			{/ksTabs}
		</td>
	</tr>
</table>

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
 <dt>{#title_upload#}</dt>
  <dd>{#hint_upload#}</dd>
 </dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip} 