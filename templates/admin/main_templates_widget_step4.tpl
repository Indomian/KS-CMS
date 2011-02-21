{config_load file=admin.conf section=templates}
<div style="border: 1px solid ; padding: 10px 5px 10px 50px; width: 200px;">
<h2 style="border-bottom: 5px solid #44f; width: auto;">Шаг 4</h2>
<p>Предварительный просмотр виджета</p>
<div>
{$preview}
</div>
<a onclick="nextStep('widget','wmod={$module}&w={$widgetname}')">&lt;&lt; Назад</a>
<a onclick="parseData(document.getElementById('fields'),'{$data.module}','{$data.widget}');">Далее &gt;&gt;</a>
</div>