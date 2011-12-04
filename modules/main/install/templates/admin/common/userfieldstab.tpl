{ksTab NAME=$smarty.config.tabs_userfields}{strip}
<div class="form">
	<table class="layout">
		<tr class="titles">
			<th width=30%>{#header_field#}</th>
			<th width=70%>{#header_value#}</th>
		</tr>
		{foreach from=$addFields item=oItem}
		<tr>
			<td>{$oItem.description}</td>
			{assign var=value value=ext_`$oItem.title`}
			<td>{showField field=$oItem value=$data[$value] prefix=$prefix}</td>
		</tr>
		{/foreach}
	</table>
</div>
{/strip}{/ksTab}