{config_load file=admin.conf}
<html>
	<head>
		<title>Выбор элемента</title>
 		<link rel="stylesheet" href="/uploads/templates/admin/css/adminmain.css" type="text/css" />
 		<link rel="stylesheet" href="/uploads/templates/admin/css/interface.css" type="text/css" />
	</head>
	<body>
		<script type="text/javascript" src="/js/jquery.js"></script>
		<script type="text/javascript" src="/js/main/admin.js"></script>
		<script type="text/javascript" src="/js/main/obSelector.js"></script>
		<h1>Выберите элемент</h1><br/>
		<div style="clear:both;"><!-- --></div>
		<div class="right">
			Модуль: <select id="module" onchange="obSelector.selModule();">
				{foreach from=$dataList item=oItem}
					<option value="{$oItem.id}" {$oItem.sel}>{$oItem.title}</option>
				{/foreach}
			</select><br/>
		</div>
		<div class="users">
			<table id="baseTable" class="layout" width="100%">
    				<tr>
    				<th><h3>ID</h3></th>
    				<th><h3>Категория</h3></th>
    				<th></th>
    			</tr>
			</table>
		</div>
	</body>
</html>
