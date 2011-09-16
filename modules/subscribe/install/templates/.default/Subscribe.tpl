<div id="Content">
<h2>Подписка</h2>
{if $data.finish}
<h4>Поздравляем Вы успешно подписались на рассылку!</h4>
{/if}
{if $data.activated==0}
<form action="{get_posturl}" method="post">
<table border="0" cellspacing="1" cellpadding="0" class="table">
{foreach from=$newsletters item=oItem}
<tr>
<th colspan="2">
Вы подписаны на  <span id="hint_list_record" onmouseover="floatMessage.showMessage(document.getElementById('hint_list_record'), 'Список тематик на которые Вы подписались.', 250);" style="cursor: pointer;">?</span>
</th>
</tr>
<tr>
<td valign="top">
{if $oItem.selectable}
<input class="subscribe_input" name="newsletters[]" type="checkbox" value="{$oItem.id}" {if $oItem.select}checked{/if}/>
{else}
<input disabled type="checkbox" />
{/if}

</td>
<td>
{$oItem.name}<br>
<div style="margin-left:20px;font-size:8px;">{$oItem.description}</div>
</td>
</tr>
{/foreach}
</table>
Ваш email <span id="hint_email_record" onmouseover="floatMessage.showMessage(document.getElementById('hint_email_record'), 'Ваш email на который будут отправляться рассылки.<br />Поле обязательно к заполнению.', 250);" style="cursor: pointer;">?</span><br>
<input type="text" class="subscribe_input" name="email" value="{$data.email|default:$USER.email}" style="width:150px"/>
<br><br>
<div class="form_button"><div class="form_button_in"><input type="submit" class="save" value="{if $data.email}Изменить{else}Подписаться{/if}"/></div></div>
</form>
{else}
<h4>На ваш адрес выслано письмо, перейдите по ссылке для активации.</h4>
{/if}
</div>

