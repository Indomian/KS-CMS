{config_load file=admin.conf section=templates}
<div class="chose">
	<table class="layout">
		<tr>
			<th>
				<div id="step_hint" style="float: right; cursor: pointer;" onmouseover="floatMessage.showMessage(document.getElementById('step_hint'), 'Скопируйте полученный код виджета и вставьте его в шаблон в том месте, в котором хотите отобразить содержимое виджета.', 194, -180, 5);">
					<img src="{#images_path#}/help.gif" border="0" width="14px" height="14px" alt="hint" />
				</div>
				<b>Шаг 4.</b> Код виджета
			</th>
		</tr>
		<tr>
			<td>
				<textarea style="width:90%; height:150px;">{$code}</textarea><br/>
				<a onclick="nextStep('widget','wmod={$module}&w={$widgetname}')" style="cursor: pointer;">&lt;&lt; Назад</a>
				<a onclick="hideWidgetPanel();nextStep('');" style="cursor: pointer;">Закончить</a>
			</td>
		</tr> 
	</table>
</div>
