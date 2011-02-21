{config_load file=admin.conf section=main_update}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title_download#}</h1>
<div class="manage">
	<b id="status">{#status_prepare#}</b>
	<div style="width:90%;border:1px solid black;background:white;height:40px;padding:0;">
		<div id="progressbar" style="width:0%;border:0px none;background:red;height:40px;padding:0;"></div>
	</div>
</div>
<form action="{get_url}" method="POST" id="goform" style="display:none;">
	<input type="hidden" name="ACTION" value="setup"/>
	<input type="submit" value="{#setup_update#}"/>
</form>
<script type="text/javascript">
{literal}
function download()
{
	$.post("/admin.php?module=main&modpage=update",
		{"ACTION": "download"},
   		function(data){
     		if(data.error)
     		{
     			$('#status').html(data.error);
     			return false;
     		}
     		else
     		{
     			var doRequest=0;
     			for(ii in data.downloads)
     			{
     				if(data.downloads[ii].done<=data.downloads[ii].size)
     				{
     					if(data.status!='')	$('#status').html(data.status+data.downloads[ii].name);
     					else $('#status').html(data.downloads[ii].name);
     					percent=Math.round(data.downloads[ii].done/data.downloads[ii].size*100)+'%';
     					$('#progressbar').css('width',percent).html(percent);
     					if(data.downloads[ii].done<data.downloads[ii].size)
     					{
     						doRequest++;
     					}
     				}
     			}
     			if(doRequest>0)
     			{
     				setTimeout('download()',100);
     			}
     			else
     			{
     				$('#goform').show();
     			}
     		}
   		},
   		"json"
   	);
}
$().ready(function(){
	setTimeout('download()',100);
});
{/literal}
</script>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title_download#}</dt>
<dd>{#hint_download#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}