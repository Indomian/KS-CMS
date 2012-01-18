<script type="text/javascript" src="/js/fm/loader.js"></script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	{foreach from=$fm.image.chain item=cItem key=cKey name=chain}
		<li>
			<a href="{get_url _CLEAR="a fm_file t" fm_path=$cItem.path}">
				<img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_home" height="13" width="13" />&nbsp;
				<span>{if $smarty.foreach.chain.index==0}{#title#}{else}{$cItem.title}{/if}</span>
			</a>
		</li>
	{/foreach}
</ul>

<h1>{#title_edit_image#} {$fm.image.title}</h1>

<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
  		<td valign="middle">
			<div style="width: 10px;">&nbsp;</div>
  		</td>
 		<td valign="top" width="100%">
 		{ksTabs NAME=ksc_upload head_class=tabs2 title_class=bold}
  				{ksTab NAME=$smarty.config.tabs_edit_image selected="1"}
					<div class="form">
						<table class="layout" id="fileTable">
							<tr>
								<td>
									<img src="{$fm.image.path}" />
								</td>
							</tr>
						</table>
					</div>
				{/ksTab}
		{/ksTabs}
		</td>
	</tr>
</table>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/doc.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_edit_image#}</dt>
	<dd>{#hint_edit_image#}</dd>
 </dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip} 