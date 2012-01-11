function _KSAdminTree(){
	var arRequest={
		'module':'main',
		'action':'l',
		'k':''
	};
	ShowLoading();
	$.getJSON('/admin.php',arRequest,function(data){
		HideLoading();
		alert(data);
	});
}

$(document).ready(function(){
	_KSAdminTree();
});
/*function nextStep(module,param,node,relo)
{
	//alert(node);
	var mydiv = document.getElementById(node);
	if(node != 'root')
	{
		var myimg = document.getElementById('img'+node);
	}
	if(mydiv.style.display=='none' || relo==true)
	{
		if(node != 'root')
		{
			myimg.innerHTML='<img src="{/literal}{#images_path#}{literal}/transparent.gif" />';
			myimg.innerHTML='<img src="{/literal}{#images_path#}{literal}/loading.gif" width="13" height="13" />';
		}
		$.get("/admin.php?module=main&mode=ajax&modpage=lite&m="+module+"&q="+param+"&curid="+node,null,function(data)
		{
			if(data.length>0)
			{
				mydiv.innerHTML = data;
				$('a.delete').click(liteDeleteItem);
				if(node != 'root')
				{
					myimg.innerHTML='<img src="{/literal}{#images_path#}{literal}/transparent.gif" />';
					myimg.innerHTML='<img src="{/literal}{#images_path#}{literal}/icons_menu/minus.gif" />';
				}
			}
		});
		mydiv.style.display='';
	}
	else
	{
		mydiv.style.display='none'
		if(node != 'root')
		{
			myimg.innerHTML='<img src="{/literal}{#images_path#}{literal}/transparent.gif" />';
			myimg.innerHTML='<img src="{/literal}{#images_path#}{literal}/icons_menu/plus.gif" />';
		}
	}
	return false;
}*/