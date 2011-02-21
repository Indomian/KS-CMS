{strip}
	<div style="padding-bottom: 12px;">
	{if $subcategories != ''}
		<h2>Подразделы</h2>
		<table cellspacing="0" cellpadding="0">
		{foreach from=$subcategories key=subcategory_key item=subcategory_item}
			<tr>
				<td style="padding-right: 12px">{$subcategory_item.date}</td>
				<td><a href="{$subcategory_item.full_path}">{$subcategory_item.title}</a></td>
			</tr>
		{/foreach}
		</table>
	{/if}
	</div>
{/strip}