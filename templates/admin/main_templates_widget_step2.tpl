{config_load file=admin.conf section=templates}
<div class="chose">
	<table class="layout">
		<tr>
			<th>
				<div id="step_hint" style="float: right; cursor: pointer;" onmouseover="floatMessage.showMessage(document.getElementById('step_hint'), 'Выберите из списка виджетов модуля <b>{$module_name}</b> виджет, который Вы хотите вставить в шаблон. Для того, чтобы узнать, для чего предназначен тот или иной виджет, наведите на него курсор мыши.', 194, -180, 5);">
					<img src="{#images_path#}/help.gif" border="0" width="14px" height="14px" alt="hint" />
				</div>
				<b>Шаг 2.</b> Выберите виджет
			</th>
 		</tr>
		<tr>
			<td>
			<font color="red">{$error}</font>
			<ul class="chose_list">
			{foreach from=$data item=oItem key=oKey}
				<li>
					<a id="widget_hint_{$oKey}" onmouseover="floatMessage.showMessage(document.getElementById('widget_hint_{$oKey}'), '{$oItem.widgetdescr}', 180, 0, 5);" onclick="nextStep('widget','wmod={$module}&w={$oItem.widget}')" style="cursor: pointer;">{$oItem.widgetname}</a>
				</li>
			{/foreach}
			</ul><br/>
			<a onclick="nextStep('');" style="cursor: pointer;">&lt;&lt; Назад</a> 
  			</td>
		</tr> 
	</table>
</div>