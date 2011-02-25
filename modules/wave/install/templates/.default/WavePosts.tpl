{if $posts!=''}
<h2>Комментарии к статье</h2>
<div class="comments">
	{foreach from=$posts item=oItem key=oKey name=com}
		<div class="comment level{$oItem.depth}" id="post_{$oItem.id}">
			<div class="comment-header">
				<b>{$oItem.users_title|default:$oItem.user_name}</b> <a name="com{$oItem.id}">#{$oKey}</a>
				<span class="comment-header-date">
					{$oItem.date_add|date_format:"%d.%m.%Y %H:%M"}
				</span>
			</div>
			<p>{$oItem.content|nl2br}</p>
			<ul class="comment-menu">
				<li><a href="{get_url _CLEAR="WV_a WV_id"}#com{$oItem.id}" class="comment-link">Ссылка</a></li>
				{if $oItem.access.canEdit}
					<li>|</li>
					<li><a href="{get_url WV_a="edit" WV_id=$oItem.id}" name="edt{$oItem.id}" class="comment-edit-link">Редактировать</a></li>
				{/if}
				{if $oItem.access.canAnswer}
					<li>|</li>
					<li><a href="{get_url WV_a="answer" WV_id=$oItem.id}" name="ans{$oItem.id}" class="comment-answer-link">Ответить</a></li>
				{/if}
				{if $oItem.access.canDelete}
					<li>|</li>
					<li><a href="{get_url WV_a="delete" WV_id=$oItem.id}" name="dlt{$oItem.id}" class="comment-delete-link">Удалить</a></li>
				{/if}
				{if $oItem.access.canModerate}
					<li>|</li>
					{if $oItem.active==1}
					<li><a href="{get_url WV_a="hide" WV_id=$oItem.id}" name="hid{$oItem.id}" class="comment-hide-link">Скрыть сообщение</a></li>
					{else}
					<li><a href="{get_url WV_a="show" WV_id=$oItem.id}" name="sho{$oItem.id}" class="comment-show-link">Отобразить сообщение</a></li>
					{/if}
				{/if}
			</ul>
		</div>
	{/foreach}
</div>
{widget name=navigation action=PageNav pages=$pages}
{/if}
<div id="answer">
	<h2><span class="l"><span class="red">Написать комментарий</span></span></h2>
	<form action="{get_posturl}" method="post">
		{gen_post_hash}
		{SysNotice}
		<input type="hidden" name="WV_parent_id" value="0"/>
		<h2>Ваше сообщение</h2>
		<div class="comments">
			<table width="100%" border="0" cellpadding="0">
			{if $fields.user_name}
				<tr><td width="200">{$fields.user_name}</td><td width="90%"><input type="text" name="WV_user_name" style="width:100%;" value="{$post.user_name}"/></td></tr>
			{/if}
			{if $fields.user_email}
				<tr><td width="200">{$fields.user_email}</td><td width="90%"><input type="text" name="WV_user_email" style="width:100%;" value="{$post.user_email}"/></td></tr>
			{/if}
			</table>
			{if $fields.content}
			<div>
				<textarea class="textarea" cols="5" rows="5" name="WV_content" style="width:100%"></textarea>
			</div>
			{/if}
			{if $use_captcha==1}
			<table width="100%" border="0" cellpadding="0">
			<tr>
				<td>{$fields.captcha}</td>
			</tr>
			<tr>
				<td>
					<img src="{captchaImageUrl}" border="0"/><br/>
					<input type="text" name="c" value="" class="text"/>
				</td>
			</tr>
			</table>
			{/if}
			<div class="button"><span>
				<input class="but" type="submit" name="addpost" value="Отправить"/>
			</span></div>
		</div>
	</form>
</div>
	<p>У вас нет доступа к добавлению комментариев. Для добавления комментариев вам необходимо:
	<a href="/user/authorize.html">авторизоваться</a> или <a href="/user/register.html">зарегистрироваться</a> на сайте.</p>
