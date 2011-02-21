{strip}
{if $mode=='groupslist'}
<select name="{$id}[function1]" class="form_input">
<option value="">Любая</option>
{foreach from=$groups item=oItem}
<option value="{$oItem.id}">{$oItem.title}</option>
{/foreach}
</select>
{/if}
{/strip}