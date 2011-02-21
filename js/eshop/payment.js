//Развешиваем обработчики
$(document).ready(function(){
	$("#pForm").unbind('submit').submit(function()
		{
			
		});
	$("select[name=OS_script]").change(function(){
		$.get('/admin.php?module=eshop&page=payment&action=getPayConfig&id='+this.value,null,function(data){
			if(data)
			{
				GUI.parse(data,$("#optionsDiv").empty());
			}
		},'json');
	});
});