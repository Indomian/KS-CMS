{config_load file=admin.conf section=templates}
<div class="chose">
	<table class="layout">
		<tr>
			<th>
				<div id="step_hint" style="float: right; cursor: pointer;" onmouseover="floatMessage.showMessage(document.getElementById('step_hint'), 'Настройте виджет, задав для него характерные параметры. У каждого виджета всегда есть как минимум два параметра, которые присущи всем виджетам системы: <b>глобальный шаблон</b> - определяет путь для поиска шаблона самого виджета; <b>шаблон вывода виджета</b> - непосредственно сам шаблон для отображения виджета.', 194, -180, 5);">
					<img src="{#images_path#}/help.gif" border="0" width="14px" height="14px" alt="hint" />
				</div>
				<b>Шаг 3.</b> Настройте виджет <b>{$data.title}</b>
			</th>
 		</tr>
		<tr>
  			<td>
				<span id="fields">
				{foreach from=$data.fields item=oItem key=oKey}
					{if $oItem.type!='hidden'}{$oItem.title}{/if}<br/>
					{if $oItem.type=='text'}
						<input type="text" name="{$oKey}" value="{$oItem.value|htmlspecialchars:2:"UTF-8":false}" style="width: 190px;" /><br/>
					{/if}
					{if $oItem.type=='hidden'}
						<input type="hidden" name="{$oKey}" value="{$oItem.value}" />
					{/if}
					{if $oItem.type=='select'}
						<select name="{$oKey}" style="width: 190px;"{if $oItem.onchange!=''}onchange="{$oItem.onchange}"{/if}>
						{foreach from=$oItem.value item=soItem key=soKey}
							<option value="{$soKey}"{if $oItem.default_value==$soKey} selected="selected"{/if}>{$soItem}</option>
						{/foreach}
						</select><br/>
					{elseif $oItem.type=='checklist'}
						{foreach from=$oItem.value item=soItem key=soKey}
							<label><input type="checkbox" name="{$oKey}[]" value="{$soKey}" {if $oItem.onchange!=''}onchange="{$oItem.onchange}"{/if} {if is_array($oItem.default_value) and in_array($soKey,$oItem.default_value)}checked="checked"{/if}/> {$soItem}</label><br/>
						{/foreach}
					{/if}
					{if $oItem.type=='textarea'}
						<textarea name="{$oKey}" style="width: 190px;">{$oItem.value}"</textarea><br/>
					{/if}
					{if $oItem.type!='hidden'}<br/>{/if}
				{/foreach}
				</span>
				<a onclick="nextStep('wlist','wmod={$data.module}');" style="cursor: pointer;">&lt;&lt; Назад</a>
				<a onclick="parseData(document.getElementById('fields'),'{$data.module}','{$data.widget}');" style="cursor: pointer;">Далее &gt;&gt;</a> 
			</td>
		</tr>
	</table>
</div>