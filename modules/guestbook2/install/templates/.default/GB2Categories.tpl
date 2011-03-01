<ul>
{foreach from=$list item=oItem}
	<li><a href="{$oItem.url}">{$oItem.title}</a></li>
{/foreach}
</ul>