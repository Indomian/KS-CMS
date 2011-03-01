{if $announces != ''}
<h2>Анонс</h2>
{foreach from=$announces key=oKey item=oItem}
	{if $oItem.img!=''}
	<div class="text_line text_line_with_an_image">
		<span class="text_line_image">
			<a href="{$oItem.full_path}{$oItem.text_ident}.html">
			{widget action=Pic width="87" height="89" src="/uploads/`$oItem.img`" alt=$oItem.title class="img"}
			</a>
		</span>
		<div class="header_holder_with_line">
			<h4 class="alt"><a href="{$oItem.full_path}{$oItem.text_ident}.html">{$oItem.title}</a></h4>
			<span class="small grey">{$oItem.date}</span>
		</div>
		<p class="alt">{$oItem.description}</p>
	</div>
	{else}
	<div class="text_line">
		<div class="header_holder_with_line">
			<h4 class="alt"><a href="{$oItem.full_path}{$oItem.text_ident}.html">{$oItem.title}</a></h4>
			<span class="small grey">{$oItem.date}</span>
		</div>
		<p class="alt">{$oItem.description}</p>
	</div>
	{/if}
{/foreach}
{if $data.pages!=''}
	{widget name=navigation action=PageNav pages=$data.pages global_template="" tpl=""}
{/if}
{/if}