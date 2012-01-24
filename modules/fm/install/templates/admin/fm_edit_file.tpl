<script type="text/javascript" src="/js/fm/loader.js"></script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	{foreach from=$fm.file.chain item=cItem key=cKey name=chain}
		<li>
			<a href="{get_url _CLEAR="a fm_file t" fm_path=$cItem.path}">
				<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_home" height="13" width="13" />&nbsp;
				<span>{if $smarty.foreach.chain.index==0}{#title#}{else}{$cItem.title}{/if}</span>
			</a>
		</li>
	{/foreach}
</ul>

<h1>{#title_edit_file#} {$fm.file.title}</h1>

<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
  		<td valign="middle">
			<div style="width: 10px;">&nbsp;</div>  
  		</td>
 		<td valign="top" width="100%">
 		{ksTabs NAME=ksc_upload head_class=tabs2 title_class=bold}
  				{ksTab NAME=$smarty.config.tabs_edit_content selected="1"}
					<div class="form">
						<form action="{get_url _CLEAR=action}" method="POST" enctype="multipart/form-data" name="fileForm" id="fileForm">
							<input type="hidden" name="a" value="edit" />
							<input type="hidden" name="t" value="file" />
							<input type="hidden" name="fm_file" value={$fm.file.title} />
							<table class="layout" id="fileTable">
								<tr>
									<td>
										<textarea name="content" class="form_input" style="width:100%;height:500px;">{$fm.file.content}</textarea>
									</td>
								</tr>
							</table>
							<div class="manage">
								<input type="submit" value="{#save#}"/>
								<input type="submit" name="apply" value="{#apply#}"/>
								<input type="submit" name="cancel" value="{#cancel#}"/>
							</div>
						</form>
					</div>
				{/ksTab}
		{/ksTabs}
		</td>
	</tr>
</table>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
 <dt>{#title_edit_file#}</dt>
  <dd>{#hint_edit_file#}</dd>
 </dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip} 