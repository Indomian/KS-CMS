<span style="font-size: 11px">
		Время генерации страницы : {$sys_info.gen_tyme} . время работы mysql {$sys_info.sql_gen_tyme} . количество SQL запросов: {$sys_info.sql_queries_quant}
<table width="100%" border="1">
<tr><td></td><td width="100%">Запрос</td><td>Время</td>
{foreach from=$sys_info.sql_requests item=oItem}
<tr><td style="background-color:{TimeToColor time=$oItem.TIME};">{$i++}</td>
<td style="background-color:{TimeToColor time=$oItem.TIME};">{$oItem.QUERY}</td>
<td style="background-color:{TimeToColor time=$oItem.TIME};">{$oItem.TIME}</td></tr>
{/foreach}
</table>
		</span>