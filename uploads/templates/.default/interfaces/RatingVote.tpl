<ul class="voting">
	{foreach from=$marks key=mark item=markclass}
	<li class="{$markclass}">
		<a {if $voted==0}href="{get_url _CLEAR="material_mark_.*" act=rate}&{$markId}={$mark}"{else}name="{$mark}"{/if} {if $mark==$current_rating_stars}class="cur"{/if}>
			&nbsp;
		</a>
	</li>
	{/foreach}
</ul>
