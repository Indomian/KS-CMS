{strip}
<span class="small grey">{$data.date}</span>
{if $data.img!=''}
	<div style="float: left; padding-right: 8px; padding-bottom: 8px;">
	<img src="/uploads/{$data.img}" class="img" alt="{$data.title}"/>
	</div>
{/if}
{$data.content}
<div style="clear:both;"><!-- --></div>
{widget name="wave" action="WavePosts" hash="catelement`$data.id`"}
{/strip}