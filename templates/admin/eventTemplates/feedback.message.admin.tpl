Форма: {$data.name}
Описание: {$data.description}
{foreach from=$data.fields item=oItem name=fields}
{$oItem.name}: {$oItem.value}
{/foreach}
