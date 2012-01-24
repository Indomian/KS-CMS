<h2>Подписка</h2>
{if $error==1}
	На сайте не найдено рассылок доступных для вас
{elseif $error==2}
	На сайте не найдено рассылок
{else}
	{if $data.activated}
		<h4>На ваш адрес выслано письмо, перейдите по ссылке для активации рассылки</h4>
	{else}
		{if $data.finish}
			<h4>Поздравляем Вы успешно подписались на рассылку!</h4>
		{/if}
		<form action="{get_posturl}" method="post">
			<table border="0" cellspacing="1" cellpadding="0" class="table">
				<tr>
					<th colspan="2">Темы рассылок</th>
				</tr>
				{foreach from=$newsletters item=oItem}
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
				<tr>
					<td>Ваш Email</td><td><input type="text" name="email" value="{$data.email|default:$USER.email}" style="width:150px"/></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="subscribe" value="{if $data.email}Изменить{else}Подписаться{/if}"/>
					</td>
				</tr>
			</table>
		</form>
	{/if}
{/if}

