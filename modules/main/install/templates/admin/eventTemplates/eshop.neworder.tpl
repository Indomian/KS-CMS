Заказ №{$data.orderId}
------------------------------
Здравствуйте, {$data.order.name}, вы оформили заказ в электронном магазине сайта {$SITE.home_title} ({$SITE.home_url}).
------------------------------
{assign var="total" value="0"}
{assign var="totalPrice" value="0"}
{if $data.goods!=''}
Вы заказали:
{foreach from=$data.goods item=oItem name=goods}
{$smarty.foreach.goods.iteration}	|{$oItem.title}, стоимостью {$oItem.price} за шт, всего {$oItem.count} шт, на сумму {$oItem.totalPrice}.
{assign var="total" value=$total+$oItem.count}
{assign var="totalPrice" value=$totalPrice+$oItem.totalPrice}
{/foreach}
Всего заказано {$total} товаров, на сумму {$totalPrice}
{/if}
------------------------------
Вы можете посмотреть состояние своего заказа по адресу: {$SITE.home_url}{$data.path.viewOrderUrl}{$data.orderId}.html.
Код для доступа к заказу: {$data.order.code}

Спасибо за использование нашего магазина!
