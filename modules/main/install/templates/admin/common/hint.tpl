{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}{$icon}') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{$title}</dt>
	<dd>{$description}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if} helpbar" style="cursor:pointer;">&nbsp;</div>
{/strip}
