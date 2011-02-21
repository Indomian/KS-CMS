<ul class="nav_pages">
	<li>
		{if $data.active==1}
			<a name="first" title="Назад">&larr;</a></li>
		{else}
			<a href="{get_url _CLEAR="p.*" i=$data.index}&p{$data.index}={$data.active-1}" title="Назад">&larr;</a>
		{/if}
	</li>
	{foreach from=$data.pages item=oItem key=oKey}
		<li>
		{if $oKey==$data.active}
			<em>{$oKey}</em>
		{else}
			{if $oKey==1 or $oKey==$data.num}
				{if $oKey==$data.num and $data.active<=$data.num-3}
					<a name="middle">...</a>
				{/if}
				<a href="{get_url _CLEAR="p.*" i=$data.index}&p{$data.index}={$oItem}">{$oKey}</a>
				{if $oKey==1 and $data.active>=4}
					<a name="middle">...</a>
				{/if}
			{else}
				{if $oKey>=$data.active-1 and $oKey<=$data.active+1}
					<a href="{get_url _CLEAR="p.*" i=$data.index}&p{$data.index}={$oItem}">{$oKey}</a>
				{/if}
			{/if}
		{/if}
		</li>
	{/foreach}
	<li>
		{if $data.active==$data.num}
			<a name="last" title="Вперед">&rarr;</a>
		{else}
			<a href="{get_url _CLEAR="p.*" i=$data.index}&p{$data.index}={$data.active+1}" title="Вперед">&rarr;</a>
		{/if}
	</li>	
</ul>