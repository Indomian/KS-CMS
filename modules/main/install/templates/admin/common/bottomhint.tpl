{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/{$icon|default:"people"}.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{$title}</dt>
	<dd>{$hint}</dd>
</dl>
<div class="helpbar content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" style="cursor:pointer;">&nbsp;</div>
{/strip}