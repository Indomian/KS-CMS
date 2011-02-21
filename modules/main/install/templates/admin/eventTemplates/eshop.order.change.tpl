Ваш заказ №{$data.orderId} изменен
------------------------------
Здравствуйте, {$data.order.name}, вы оформили заказ в электронном магазине сайта {$SITE.home_title} ({$SITE.home_url}).
Ваш заказ был изменен. 
------------------------------
{if $data.goods!=''}
Список товаров в заказе

{foreach from=$data.goods item=oItem name=goods}
{$smarty.foreach.goods.iteration}	|{$oItem.title}, стоимостью {$oItem.price} за шт, всего {$oItem.count} шт, на сумму {$oItem.totalPrice}.
{/foreach}
Всего заказано {$data.order.count} товаров, на сумму {$data.order.total_price}
{/if}
------------------------------
Новый статус заказа:

Дата изменения: {$data.order.statuses[0].date|date_format:"%H:%M %d.%m.%Y"}
Статус: {$data.order.statuses[0].eshop_status_title}
Коментарий: {$data.order.statuses[0].comment}
Изменил: {$data.order.statuses[0].users_title}
------------------------------
Вы можете посмотреть состояние своего заказа по адресу: {$SITE.home_url}{$data.path.viewOrderUrl}{$data.orderId}.html.
Код для доступа к заказу: {$data.order.code}

Спасибо за использование нашего магазина!
