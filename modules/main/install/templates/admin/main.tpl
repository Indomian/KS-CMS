<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
</ul>

<h1>{#title_desktop#}</h1>

{foreach from=$data item=oItem key=oKey}
<div class="desktop_block">
	<div class="desktop_block_header"><img src="{#images_path#}/icons32/{$oItem.class}" alt="{$oItem.title}"><b>{$oItem.title}</b></div>
	{if $oItem.items}
		<ul>
		{foreach from=$oItem.items item=soItem}
			<li><img src="{#images_path#}/icons_menu/{$soItem.class}" alt="{$soItem.title|escape}"><a href="/admin.php?{$soItem.href}">{$soItem.title}</a></li>
		{/foreach}
		</ul>
	{/if}
</div>
{/foreach}
<div style="clear:both;"><!-- --></div>