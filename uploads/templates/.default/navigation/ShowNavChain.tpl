<div class="nav"><div class="nav_in">
<ul>
	<li><a href="/">{$SITE.home_title}</a></li>
	{foreach key=key item=item name=nch from=$items}
	<li><a href="{$item.uri}">{$item.title}</a></li>
	{/foreach}
</ul>
</div></div>