{if $smarty.get.type!='AJAX'}<div id="banners{$type.text_ident}" class="bannerRotation" style="width:175px;height:550px;overflow:hidden;">{else}{$type.text_ident}[||]{$duration}[||]{$count}[||]{/if}
<div class="inner">
{foreach from=$list item=data}
	{if $data.content==''}
	<p><a href="{$data.path}?go={$data.href}" title="{$data.title}" target="_blank"><img src="/uploads/{$data.img}" alt="{$data.title}"/></a></p>
	{else}
		{$data.content}
	{/if}
{/foreach}
</div>
{if $smarty.get.type!='AJAX'}
	</div>
	<script type="text/javascript">
		__BannerTime_{$type.text_ident}={$duration};
		__BannerCount_{$type.text_ident}={$count};
		__BannerPath='{$currentPath}';
	</script>
{/if}