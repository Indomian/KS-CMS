{config_load file=admin.conf section=interfaces}
<script language="javascript" type="text/javascript">
function setField(from,to)
{ldelim}
	document.getElementById(to).value=document.getElementById(from).value;
{rdelim}
</script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="{get_url module=interfaces}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>Интерфейс</span></a></li>
	<li><a href="{get_url module=interfaces page=smilies}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>Смайлики</span></a></li>
	<li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>Редактирование</span></a></li>
</ul>

<h1>Редактирование смайлика</h1>
{strip}
<form action="{get_url _CLEAR='CM_.*' action='save'}" method="post" enctype="multipart/form-data">
	<input type="hidden" name="module" value="interfaces">
	<input type="hidden" name="page" value="smilies">
	<input type="hidden" name="FG_id" value="{$data.id}">
	<input type="hidden" name="action" value="save">
	<div class="form">
		<table class="layout">
			<tr>
				<th width="30%">Поле</th>
				<th width="70%">Значение</th>
			</tr>
			<tr>
				<td>Символ:</td>
				<td><input type="text" name="FG_smile" value="{$data.smile|htmlspecialchars:2:'UTF-8':false}" style="width:95%" class="form_input"/></td>
			</tr>
			<tr>
				<td>Изображение:</td>
				<td><input type="file" name="FG_img" value="" style="width:100%"><br>
					{if $data.img!=""}<img src="/uploads/{$data.img}"><br/>
					<input type="checkbox" name="FG_img_del" value="1"/> Удалить{/if}
				</td>
			</tr>
			<tr>
				<td>Группа:</td>
				<td>
					<input type="text" name="FG_group" id="FG_group" value="{$data.group|htmlspecialchars:2:'UTF-8':false}" class="form_input"/>
					&lt;&lt;
					<select id="FG_group_list" onchange="setField('FG_group_list','FG_group');" class="form_input">
						<option value="">Выбрать из списка</option>
						{foreach from=$groups key=oKey item=oItem}
							<option value="{$oKey}">{$oKey} [{$oItem}]</option>
						{/foreach}
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="form_buttons">
		<div>
			<input type="submit" value="{#save#}" class="save"/>
		</div>
		<div>
			<input type="submit" name="apply" value="{#apply#}"/>
		</div>
		<div>
			<a href="{get_url _CLEAR="action id"}" class="cancel_button">{#cancel#}</a>
		</div>
	</div>
</form>
{/strip}