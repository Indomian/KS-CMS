<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
</ul>
<h1>{#title_setup#}</h1>
<div class="manage">
	<b id="status">{#status_prepare_unpack#}</b>
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
function step1()
{
	$.post("/admin.php?module=main&modpage=update",
		{"ACTION": "setup"},
   		function(data){
     		if(data.error)
     		{
     			$('#status').html(data.error);
     			return false;
     		}
     		else
     		{
     			if(data.step=='precopy')
     			{
     				$('#status').html(data.status);
     				setTimeout('step2()',100);
     			}
     			else
     			{
     				$('#status').html('{/literal}{#server_answer_error#}{literal}');
     			}
     		}
   		},
   		"json"
   	);
}
function step2()
{
	$.post("/admin.php?module=main&modpage=update",
		{"ACTION": "setup"},
   		function(data){
     		if(data.error)
     		{
     			$('#status').html(data.error);
     			return false;
     		}
     		else
     		{
     			if(data.step=='copy')
     			{
     				$('#progressbar').html(data.done+'/'+data.totalFiles);
     				setTimeout('step3()',100);
     			}
     			else
     			{
     				if(data.step=='before')
     				{
     					$('#status').html(data.before);
     					setTimeout('before()',100);
     				}
     				else
     				{
     					$('#status').html('{/literal}{#server_answer_error#}{literal}');
     				}
     			}
     		}
   		},
   		"json"
   	);
}

function before()
{
	$.post("/admin.php?module=main&modpage=update",
		{"ACTION": "before"},
   		function(data){
     		if(data.error)
     		{
     			$('#status').html(data.error);
     			return false;
     		}
     		else
     		{
     			if(data.step=='copy')
     			{
     				$('#progressbar').html(data.done+'/'+data.totalFiles);
     				if(data.status) $('#status').html(data.status);
     				setTimeout('step3()',100);
     			}
     			else
     			{
     				$('#status').html('{/literal}{#server_answer_error#}{literal}');
     			}
     		}
   		},
   		"json"
   	);
}

function step3()
{
	$.get("/admin.php?update&mode=ajax", null,
   		function(data){
     		if(data.error)
     		{
     			$('#status').html(data.error);
     			return false;
     		}
     		else
     		{
     			percent=Math.round(data.done/data.total*100)+'%';
     			$('#progressbar').css('width',percent).html(percent);
     			$('#progressbar').html(data.done+'/'+data.total);
     			if(data.done<data.total)
     			{
     				setTimeout('step3()',100);
     			}
     			else
     			{
     				setTimeout('step4()',100);
     			}
     		}
   		},
   		"json"
   	);
}

function step4()
{
	$.post("/admin.php?module=main&modpage=update",
		{"ACTION": "after"},
   		function(data){
     		if(data.error)
     		{
     			$('#status').html(data.error);
     			return false;
     		}
     		else
     		{
     			$('#status').html(data.status);
     			if(data.ok)
     			{
     				document.location='/admin.php?module=main&modpage=update';
     			}
     		}
   		},
   		"json"
   	);
}
$().ready(function(){
	setTimeout('step1()',100);
});
{/literal}
</script>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title_setup#}</dt>
<dd>{#hint_setup#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}