{config_load file=admin.conf section=blog}
<script type="text/javascript">
{literal}
$(document).ready(function(){
	$('#tpl').change(function(){
		var tpl = $('#tpl').val();
		var post_data = {ACTION: 'tpl_selected', tpl: tpl}
		if(tpl==""){
			$('#tpl_fields').empty();
			return ;
		}else{
			$.post({/literal}"{get_url ACTION='tpl_selected'}"{literal},
				post_data,
				function(json){
					if(json.error=="no"){
						for(i=0; i< json.tpl_fields.length; i++){
							$('#tpl_fields').append('['+json.tpl_fields[i]+']<br/>');
						}
					}
				}
				,'json'
			);
		}
	});
});
{/literal}
</script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="{get_url _CLEAR="ACTION id"}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>Шаблоны почтовых событий</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{if $data.title==""}Создание нового шаблона сообщения{else}Управление шаблоном сообщений "{$data.title}"{/if}</span></a></li>
</ul>
<h1>Создание нового сообщения</h1>
<form action="{get_url _CLEAR="ACTION template id"}" method="POST">
	<input type="hidden" name="ACTION" value="save"/>
	<input type="hidden" name="KS_id" value="{$data.id}"/>
	{strip}
	<div class="form">
		<table class="layout">
			<tr>
				<td>Шаблон сообщения:</td>
				<td>
					<select name='tpl' id="tpl">
						<option value="">Выбирите шаблон сообщения</option>
						{foreach from=$data.templates item=oItem key=oKey name=myList}
						<option value="{$oItem.file_id}">{$oItem.title}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr><th colspan="2">Поля для заполнения:</th></tr>
			<tr>
				<td>Получатель:</td>
				<td><input type="text" name="KS_address" value="{$data.address}" style="width:100%" class="form_input"/></td>
			</tr>
			<tr>
				<td>Копия:</td>
				<td><input type="text" name="KS_copy" value="{$data.copy}" style="width:100%" class="form_input"/></td>
			</tr>
			<tr>
				<td colspan="2" id='tpl_fields'></td>
			</tr>
		</table>
	</div>
	{/strip}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
    	<div>
    		<a href="{get_url _CLEAR="ACTION type id template"}" class="cancel_button">{#cancel#}</a>
    	</div>
   	</div>
</form>