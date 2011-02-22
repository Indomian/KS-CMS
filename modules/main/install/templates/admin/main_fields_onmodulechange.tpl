<select name="CM_type" style="width:95%" class="form_input">
{foreach from=$tables key=oKey item=oItem}
<option value="{$oItem}" {if $oItem==$data.type}selected="selected"{/if}>{$smarty.config.$oKey|default:$oKey}</option>
{/foreach}
</select>