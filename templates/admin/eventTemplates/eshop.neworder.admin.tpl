Создан заказ №{$data.orderId}
------------------------------
Пользователь {$USER.title} оформил заказа на имя {$data.order.name} 
------------------------------
{assign var="total" value="0"}
{assign var="totalPrice" value="0"}
{if $data.goods!=''}
Заказано
{foreach from=$data.goods item=oItem name=goods}
{$smarty.foreach.goods.iteration}	|{$oItem.title}, стоимостью {$oItem.price} за шт, всего {$oItem.count} шт, на сумму {$oItem.totalPrice}.
{assign var="total" value=$total+$oItem.count}
{assign var="totalPrice" value=$totalPrice+$oItem.totalPrice}
{/foreach}
Всего заказано {$total} товаров, на сумму {$totalPrice}
{/if}
------------------------------
Адрес просмотра заказ: {$SITE.home_url}/admin.php?module=eshop&page=orders&id={$data.orderId}
