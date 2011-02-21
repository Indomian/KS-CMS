{literal}
<script type="text/javascript">
function nextStep(module,param,node,relo)
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
}
</script>
{/literal}
{if $module.current=="main" AND $modpage=="lite"}
<h1>{#title_easy_manage#}</h1>
{else}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
</ul>

<h1>{#title_site_structure#}</h1>
{/if}
{strip}
<div class="tree">
	{include file='admin/main_tree_ajax.tpl'}
</div>
{/strip}
{if $module.current=="main" AND $modpage=="lite"}
{else}
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/folder.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_site_structure#}</dt>
<dd>{#hint_site_structure#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}
{/if}

