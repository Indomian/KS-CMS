function _KSAdminTree(id){
	var arRequest={
		'module':'main',
		'action':'l',
		'key':id
	};
	ShowLoading();
	$.getJSON('/admin.php',arRequest,function(data){
		HideLoading();
		if('parent' in data)
		{
			var root=$('#tree_'+data.parent).empty();
			if('list' in data && 'size' in data)
			{
				if(data.size>0)
				{
					root.parent().children('.show').hide().prev().show();
					for(ii in data.list)
					{
						var obItem=root.append('<li id="'+ii+'"><span title="'+data.list[ii].href+'">'+data.list[ii].title+'</span></li>').children('li:last');
						if('icon' in data.list[ii])
							obItem.prepend('<img src="/uploads/templates/admin/images'+data.list[ii].icon+'" alt="" class="icon">');
						if('children' in data.list[ii])
						{
							obItem.addClass('dir');
							obItem.prepend('<img src="/uploads/templates/admin/images/icons_menu/plus.gif" alt="Развернуть" class="show">');
							obItem.prepend('<img src="/uploads/templates/admin/images/icons_menu/minus.gif" alt="Свернуть" class="hide" style="display:none;">');
							obItem.children('.show').click(function(){
								var obParent=$(this).parent();
								if(obParent.children('ul').children('li').length>0)
								{
									obParent.children('ul').children('li').show();
									obParent.children('.show').hide().prev().show();
								}
								else
									_KSAdminTree(obParent.attr('id'));
							});
							obItem.children('.hide').click(function(){
								var obParent=$(this).parent();
								obParent.children('ul').children('li').hide();
								obParent.children('.show').show().prev().hide();
							});
						}
						else
							obItem.addClass('file');
						obItem.append('<ul id="tree_'+ii+'"></ul>');
					}
				}
				else
					root.parent().children('.show').hide();
			}
			else
				root.parent().children('.show').hide();
		}
	});
}

$(document).ready(function(){
	_KSAdminTree();
});
