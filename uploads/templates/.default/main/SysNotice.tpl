{foreach from=list item=arItem}
	{if $arItem.type==1}
		<div class="warning" style="background:#FFF6C4 url('/uploads/templates/admin/images/atention.gif') left 50% no-repeat; color:#D13B00; border: 1px solid #CC0000; margin: 0 0 6px; padding: 11px 0 11px 59px;">
			{$arItem.error|default:$arItem.msg} <b>{$arItem.text}</b>
		</div>
	{elseif $arItem.type==2}
		<div class="message" style="background:#EAFFDB url('/uploads/templates/admin/images/ok.gif') left 50% no-repeat; color:#D13B00; border: 1px solid #57D300; margin: 0 0 6px; padding: 11px 0 11px 59px;">
			{$arItem.error|default:$arItem.msg} <b>{$arItem.text}</b>
		</div>
	{/if}
{/foreach}