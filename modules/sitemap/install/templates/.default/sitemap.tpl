<ul>
{foreach from=$data.list item=oItem key=oKey}
{if $oItem.active}
	<li>
		<div style="width:{$oItem.level*10}px;height:20px;display:block;float:left;">&nbsp;</div>
		<a href="{$oItem.watch_url}" style="float:left;">{$oItem.title}</a>
		<div style="clear:both;"> </div>
	</li>
{/if}
{/foreach}
</ul>