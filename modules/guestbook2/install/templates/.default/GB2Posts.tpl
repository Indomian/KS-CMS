{if $posts!=''}
<div class="comments">
{foreach from=$posts item=oItem key=oKey}
	<div class="text_line text_line_with_an_image_alt">
		<span class="text_line_image_alt"><a name="#com{$oKey}">
			{if $oItem.users_img!=''}
				{widget name=interfaces action=Pic width="49" height="49" src="/uploads/`$oItem.users_img`" alt=$oItem.user_name class="img"}
  			{else}
  				{widget name=interfaces action=Pic width="49" height="49" src="`$templates_files_folder``$glb_tpl`/images/noavatar.png" alt=$oItem.user_name class="img"}
  			{/if}
		</a></span>
		<div class="header_holder">
			<h4 class="alt"><a name="#com{$oItem.id}">{$oItem.users_title|default:$oItem.user_name}</a></h4>
			<span class="small grey">
				{$oItem.date_shown|date_format:"%d.%m.%Y %H:%M"}
			</span>
		</div>
		<p>{$oItem.content}</p>
		{if $data.level<4}
		<form action="{get_posturl}" method="post" onsubmit="return true;">
			{gen_post_hash}
			<input type="hidden" name="GB_id" value="{$oItem.id}"/>
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
		{if $oItem.answer!=''}
			<div class="text_line text_line_with_an_image_alt">
				<span class="text_line_image_alt"><a name="#com{$oKey}">
					{if $oItem.answer.users_img!=''}
						{widget name=interfaces action=Pic width="49" height="49" src="/uploads/`$oItem.answer.users_img`" alt=$oItem.users_title class="img"}
  					{else}
  						{widget name=interfaces action=Pic width="49" height="49" src="`$templates_files_folder``$glb_tpl`/images/noavatar.png" alt=$oItem.answer.users_title class="img"}
  					{/if}
				</a></span>
				<div class="header_holder">
					Дата ответа: <span class="small grey">
						{$oItem.date_answer|date_format:"%d.%m.%Y %H:%M"}
					</span>
				</div>
				<p>{$oItem.answer.content}</p>
			</div>
		{/if}
	</div>
	<div class="comment_links">
		<p><a href="{get_url}#com{$oItem.id}">ссылка на сообщение</a> <span class="grey">(#com{$oItem.id})</span></p>
	</div>
{/foreach}
</div>
{widget name=navigation action=PageNav pages=$pages}
{/if}
{if $data.level<9 && not $showAddText}
<a name="newcomment"></a>
<form action="{get_posturl}" method="post">
	{gen_post_hash}
	<h2>Ваше сообщение</h2>
	<div class="comments">
		<table width="100%" border="0" cellpadding="0">
		{if $USER.title==''}
			<tr><td width="200">Ваше имя</td><td width="90%"><input type="text" name="GB_user_name" style="width:100%;" value="{$post.user_name}"/></td></tr>
			<tr><td width="200">Ваш email</td><td width="90%"><input type="text" name="GB_user_email" style="width:100%;" value="{$post.user_email}"/></td></tr>
		{/if}
			<tr><td width="200">Тема сообщения</td><td width="90%"><input type="text" name="GB_title" style="width:100%;" value="{$post.title}"/></td></tr>
			<tr>
				<td width="200">Категория сообщения</td>
				<td>
					<select name="GB_cat">
						{foreach from=$categories item=oItem}
						<option value="{$oItem.id}" {if $currentCat==$oItem.id}selected="selected"{/if}>{$oItem.title}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		</table>
		<div>
			<textarea class="textarea" cols="5" rows="5" name="GB_content" style="width:100%">{$post.content}</textarea>
		</div>
		{if $data.showCaptcha==1}
		<table width="100%" border="0" cellpadding="0">
			<tr>
			<td>Введите текст с картинки</td>
			<td>
				<img src="{captchaImageUrl}" border="0"/><br/>
				<input type="text" name="c" value=""/>
			</td>
		</tr>
		</table>
		{/if}
		<div class="button"><span>
			<input class="but" type="submit" name="addpost" value="Отправить"/>
		</span></div>
	</div>
</form>
{/if}
{if $showAddText}
Ваше сообщение было отправлено на премодерацию
{/if}
<br />