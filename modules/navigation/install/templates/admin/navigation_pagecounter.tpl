{strip}
<div class="pages" style="overflow:visible;">
	<div class="pages_nav">
		{#page#}:
		{if $pages.active==1}
		<a>
			<img src="{#images_path#}/icons2/first_disabled.gif" alt="&lt;" height="9" width="8">
		</a> 
		<a>
			<img src="{#images_path#}/icons2/previous_disabled.gif" alt="&lt;" height="9" width="8">
		</a>
		{else} 
		<a href="{get_url _CLEAR="p[0-9]*" i=$pages.index}&p{$pages.index}=1">
			<img src="{#images_path#}/icons2/first.gif" alt="&lt;" height="9" width="8">
		</a> 
		<a href="{get_url _CLEAR="p[0-9]*" i=$pages.index}&p{$pages.index}={$pages.active-1}">
			<img src="{#images_path#}/icons2/previous.gif" alt="&lt;" height="9" width="8">
		</a>
		{/if}
		
		{foreach from=$pages.pages item=oItem key=oKey}
			{if $oKey>=$pages.active-3 and $oKey<=$pages.active + 3}
				{if $oKey==$pages.active}
					<span>{$oKey}</span>
				{else}
					<a href="{get_url _CLEAR="p[0-9]*" i=$pages.index}&p{$pages.index}={$oItem}">{$oKey}</a>
				{/if}
			{/if}
		{/foreach}
		
		{if $pages.active<$pages.num} 
		<a href="{get_url _CLEAR="p[0-9]*" i=$pages.index}&p{$pages.index}={$pages.active+1}">
			<img src="{#images_path#}/icons2/next.gif" alt="&gt;" height="9" width="8">
		</a>
		<a href="{get_url _CLEAR="p[0-9]*" i=$pages.index}&p{$pages.index}={$pages.num}">
			<img src="{#images_path#}/icons2/last.gif" alt="&gt;" height="9" width="8">
		</a>
		{else}
		<a>
			<img src="{#images_path#}/icons2/next_disabled.gif" alt="&gt;" height="9" width="8">
		</a>
		<a>
			<img src="{#images_path#}/icons2/last_disabled.gif" alt="&gt;" height="9" width="8">
		</a>
		{/if}
	</div>
	<div class="pages_qnt">
		<label>
			{#items_on_page#}:
			<div class="pseudo_select">
				<div class="pseudo_select_in">
					<div class="pseudo_select_in_in" onclick="this.nextSibling.style.display=(this.nextSibling.style.display=='none'?'':'none');">
						{if $pages.visible==$pages.TOTAL}[{#all#}]{else}{$pages.visible}{/if}
					</div>
					<div class="pseudo_select_drop_down" style="display:none;">
						<div class="pseudo_select_drop_down_item"><a href="{get_url _CLEAR="p[0-9]+ i" n=10}">10</a></div>
						<div class="pseudo_select_drop_down_item"><a href="{get_url _CLEAR="p[0-9]+ i" n=20}">20</a></div>
						<div class="pseudo_select_drop_down_item"><a href="{get_url _CLEAR="p[0-9]+ i" n=50}">50</a></div>
						<div class="pseudo_select_drop_down_item"><a href="{get_url _CLEAR="p[0-9]+ i" n=100}">100</a></div>
						<div class="pseudo_select_drop_down_item"><a href="{get_url _CLEAR="p[0-9]+ i" n=$pages.TOTAL}">[{#all#}]</a></div>
					</div>
				</div>
			</div>
		</label> 
	</div>
</div>
<div style="clear:both;"><!-- --></div>
{/strip}