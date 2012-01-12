function _KSAdminTreeLoadBrunch(id)
{
	var arRequest={
		'module':'main',
		'action':'l',
		'key':id
	};
	ShowLoading();
	$.getJSON('/admin.php',arRequest,function(data){
		HideLoading();
		if('list' in data)
		{
			var root=$('#tree_'+data.parent).empty();
			for(ii in data.list)
			{
				var obItem=root.append('<li id="root_'+ii+'"><span>'+data.list[ii].title+'</span></li>').children('li:last');
				if('children' in data.list[ii])
				{
					obItem.addClass('dir');
					obItem.prepend('<img src="/uploads/templates/admin/images/icons_menu/plus.gif" alt="Развернуть" class="show">');
					obItem.prepend('<img src="/uploads/templates/admin/images/icons_menu/minus.gif" alt="Свернуть" class="hide" style="display:none;">');
				}
				else
				{
					obItem.addClass('file');
				}
				obItem.append('<ul id="tree_'+ii+'"></ul>');
			}
		}
	});
}

function _KSAdminTree(){
	var arRequest={
		'module':'main',
		'action':'l',
		'key':''
	};
	ShowLoading();
	$.getJSON('/admin.php',arRequest,function(data){
		HideLoading();
		if('list' in data)
		{
			var root=$('#tree_root');
			for(ii in data.list)
			{
				var obItem=root.append('<li id="'+ii+'"><span>'+data.list[ii].title+'</span></li>').children('li:last');
				if('children' in data.list[ii])
				{
					obItem.addClass('dir');
					obItem.prepend('<img src="/uploads/templates/admin/images/icons_menu/plus.gif" alt="Развернуть" class="show">');
					obItem.prepend('<img src="/uploads/templates/admin/images/icons_menu/minus.gif" alt="Свернуть" class="hide" style="display:none;">');
					obItem.children('.show').click(function(){
						//$(this).hide().prev().show();
						var id=$(this).parent().attr('id');
						_KSAdminTreeLoadBrunch(id);
					});
				}
				else
				{
					obItem.addClass('file');
				}
				obItem.append('<ul id="tree_'+ii+'"></ul>');
			}
		}
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