Изменен заказ №{$data.orderId}
------------------------------
Изменен заказ пользователя {$data.order.name} 
------------------------------
{if $data.goods!=''}
Список товаров в заказе

{foreach from=$data.goods item=oItem name=goods}
{$smarty.foreach.goods.iteration}	|{$oItem.title}, стоимостью {$oItem.price} за шт, всего {$oItem.count} шт, на сумму {$oItem.totalPrice}.
{/foreach}
Всего заказано {$data.order.count} товаров, на сумму {$data.order.total_price}
{/if}
------------------------------
Последний статус:

Дата изменения: {$data.order.statuses[0].date|date_format:"%H:%M %d.%m.%Y"}
Статус: {$data.order.statuses[0].eshop_status_title}
Коментарий: {$data.order.statuses[0].comment}
Административный коментарий: {$data.order.statuses[0].admin_comment}
Изменил: {$data.order.statuses[0].users_title}
------------------------------
Адрес просмотра заказ: {$SITE.home_url}/admin.php?module=eshop&page=orders&id={$data.orderId}
