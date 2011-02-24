{if $posts!=''}
<h2>Комментарии к статье</h2>
<div class="comments">
	{foreach from=$posts item=oItem key=oKey name=com}
		<div class="comment level{$oItem.depth}">
			<div class="comment-header">
				<b>{$oItem.users_title}</b> <a name="#com{$oKey}">#{$oKey}</a>
				<span class="small grey">
					{$oItem.date_shown|date_format:"%d.%m.%Y %H:%M"}
				</span>
			</div>


			</div>
			<p>{$oItem.content|nl2br}</p>
			{if $data.level<=4}
				<form action="{get_posturl}" method="post" onsubmit="return true;">
					{gen_post_hash}
					<input type="hidden" name="WV_id" value="{$oItem.id}"/>
					<table><tr>
					{if $data.level<4}
					<td><div class="button"><span><input class="but" type="submit" name="delete" value="Удалить пост" onclick="return confirm('Вы действительно хотите удалить это сообщение?');"/></span></div></td>
					{/if}
					{if $oItem.active==1}
					<td><div class="button"><span><input class="but" type="submit" name="hide" value="Скрыть пост"/></span></div></td>
					{else}
					<td><div class="button"><span><input class="but" type="submit" name="show" value="Показать пост"/></span></div></td>
					{/if}
					</tr></table>
				</form>
			{/if}
		</div>
	{/foreach}
</div>
{widget name=navigation action=PageNav pages=$pages}
{/if}
<h2><span class="l"><span class="red">Написать комментарий</span></span></h2>
{if $data.level<9}
	<form action="{get_posturl}" method="post">
		{gen_post_hash}
		{SysNotice}
		<h2>Ваше сообщение</h2>
		<div class="comments">
			<table width="100%" border="0" cellpadding="0">
			{if $USER.title==''}
				<tr><td width="200">Ваше имя</td><td width="90%"><input type="text" name="WV_user_name" style="width:100%;" value="{$post.user_name}"/></td></tr>
				<tr><td width="200">Ваш email</td><td width="90%"><input type="text" name="WV_user_email" style="width:100%;" value="{$post.user_email}"/></td></tr>
			{/if}
			</table>
			<div>
				<textarea class="textarea" cols="5" rows="5" name="WV_content" style="width:100%"></textarea>
			</div>
			<div class="button"><span>
				<input class="but" type="submit" name="addpost" value="Отправить"/>
			</span></div>
		</div>
	</form>
{else}
	<p>У вас нет доступа к добавлению комментариев. Для добавления комментариев вам необходимо:
	<a href="/user/authorize.html">авторизоваться</a> или <a href="/user/register.html">зарегистрироваться</a> на сайте.</p>
{/if}