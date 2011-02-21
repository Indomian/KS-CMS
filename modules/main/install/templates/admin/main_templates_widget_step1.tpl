{config_load file=admin.conf section=templates}
<div class="chose">
	<table class="layout">
		<tr>
			<th>
				<div id="step_hint" style="float: right; cursor: pointer;" onmouseover="floatMessage.showMessage(document.getElementById('step_hint'), 'Выберите из списка, представленного ниже, модуль, виджет которого Вы хотите вставить. В данном списке отображены модули, имеющие хотя бы один виджет.', 194, -180, 5);">
					<img src="{#images_path#}/help.gif" border="0" width="14px" height="14px" alt="hint" />
				</div>
				<b>Шаг 1.</b> Выберите модуль</th>
 		</tr>
 		<tr>
  			<td>
				<ul class="chose_list">
				{foreach from=$data item=oItem key=oKey}
					<li><a onclick="nextStep('wlist', 'wmod={$oItem.directory}')" style="cursor: pointer;">{$oItem.name}</a></li>
				{/foreach}
				</ul> 
			</td>
		</tr>
	</table>
</div>