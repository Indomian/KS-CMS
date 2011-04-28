{if $data.content==''}
<p>
	<a href="{$data.path}?go={$data.href}" title="{$data.title}" target="_blank">
    	<img src="/uploads/{$data.img}" alt="{$data.title}"/>
	</a>
</p>
{else}
{$data.content}
{/if}