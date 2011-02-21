<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url}" method="post">
						<input type="hidden" name="ACTION" value="check"/>
						<input type="submit" class="refresh" value="{#check_update#}"/>
					</form>
				</div>
			</td>
			<td width="100%">
				<span>{#last_check#} <b>{if $data.last_update_check>0}{$data.last_update_check|date_format:"%d.%m.%Y %H:%M"}{else}{#not_checked#}{/if}</b>;
				{#current_version#} <b>{$VERSION.ID}-{$VERSION.BUILD}</b>.
				</span>
			</td>
		</tr>
	</table>
</div>
{if $updates!=''}
<h2>{#available_updates#}</h2>
<div class="users">
    <table class="layout">
    <tr>
    	<th>
    		{#field_version#}
    	</th>
    	<th width="30%">
    		{#field_title#}
    	</th>
    	<th width="15%">
    		{#field_update_type#}
    	</th>
    	<th width="20%">
    		{#field_description#}
    	</th>
    	<th></th>
    </tr>
	{foreach from=$updates key=oKey item=oItem name="fList"}
		<tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			<td>{$oKey}</td>
			<td>{$oItem.title}</td>
			<td>{if $oItem.type=='d'}{#distributive_update#}{else}{#distributive_fix#}{/if}</td>
			<td>{if $oItem.description!=''}<a href="#TB_inline?height=300&width=600&inlineId=upddes{$smarty.foreach.fList.iteration}" class="thickbox" title="{$oKey}">{#more_info#}</a>
				<div id="upddes{$smarty.foreach.fList.iteration}" style="display:none;">
				<div>{$oItem.description}</div>
				</div>
				{/if}
			</td>
			<td>
				<form action="{get_url}" method="POST">
					<input type="hidden" name="ACTION" value="update"/>
					<input type="hidden" name="build" value="{$oKey}"/>
					<input type="submit" class="refresh" value="{#update#}"/>
				</form>
			</td>
		</tr>
	{/foreach}
	</table>
</div>
{/if}
<script type="text/javascript">
$().ready(function(){ldelim}
$('a.thickbox').click(function(){ldelim}kstb_show(this.title,this.href);return false;{rdelim});
{rdelim});
</script>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title#}</dt>
<dd>{#hint#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}