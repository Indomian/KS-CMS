<html><head><title>Выбор категории</title>
 <link rel="stylesheet" href="/uploads/templates/admin/css/adminmain.css" type="text/css" />
 <link rel="stylesheet" href="/uploads/templates/admin/css/interface.css" type="text/css" />
</head>
<body onload="showSubItems(0,1,'baseTable')">
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/json.js"></script>
{literal}
<script type="text/javascript">
function ShowLoading()
{
	var oFrame=document.createElement('div');
	oFrame.id='fixme';
	oFrame.className='loadingBar';
	oFrame.innerHTML='<img src="{#images_path#}/loading.gif" border="0"> Обновление';
	oFrame.style.display='block';
	document.body.appendChild(oFrame);
	return oFrame;
}

function HideLoading(sender)
{
	sender.style.display='none';
	document.body.removeChild(sender);
}

function showData()
{
	var oXmlHttp=document.oXmlHttp;
	var tbl=document.oReq.tbl;
    var pos=document.oReq.pos;
    var elId=document.oReq.elId;
	if (oXmlHttp.readyState==4)
	{
		if (oXmlHttp.status==200)
		{
			//alert(oXmlHttp.responseText);
		    var oData=JSON.parse(oXmlHttp.responseText);
		    document.data=oData;
		    if (oData!=null)
		    {
	    	var table = document.getElementById(tbl);
        	var cnt = table.rows.length;
        	pos=1;
        	for(i=0;i<cnt-1;i++)
        	{
        		table.deleteRow(1);
        	}
        	from=0;
        	if (elId!=0)
        	{
                var oRow=table.insertRow(pos);
                var oCell = oRow.insertCell(0);
	        	oCell.align="left";
	            oCell.innerHTML = oData[0].parent_id;
	            var oCell = oRow.insertCell(1);
	        	oCell.align="left";
	            oCell.innerHTML = '<a href="#" onclick="showSubItems('+oData[0].parent_id+',1,\''+tbl+'\');return false;">Назад...</a>';
	            var oCell = oRow.insertCell(2);
	        	oCell.align="right";
	            oCell.innerHTML = "<input type=\"button\" value=\"Выбрать\" disabled=\"disabled\">";
	            from++
        	}
	       	for(i=from;i<oData.length;i++)
	     	{
	     	    if ((i%2)==0)
	     	    {
	     	    	color='#ffffff';
	     		}
	     		else
	     		{
	     			color='#f0f0f0';
	     		}
	     		var oRow = table.insertRow(pos+i);
	        	var oCell = oRow.insertCell(0);
	        	oCell.align="left";
	            oCell.innerHTML = oData[i].id;
	            oCell.style.backgroundColor=color;
	            var oCell = oRow.insertCell(1);
	        	oCell.align="left";
	            oCell.innerHTML = '<a href="#" onclick="showSubItems('+oData[i].id+',1,\''+tbl+'\');return false;">'+oData[i].title+'</a>';
	            oCell.style.backgroundColor=color;
	            var oCell = oRow.insertCell(2);
	        	oCell.align="right";
	            oCell.innerHTML = "<input type=\"button\" value=\"Выбрать\" onclick=\"doSelect("+oData[i].id+");\">";
	            oCell.style.backgroundColor=color;
	     	}
        	//document.getElementById('plus'+elId).src="{#images_path#}/minus.gif";
  			//document.getElementById('plus'+elId).value="1";
  			}
  			HideLoading(document.loading);
  		}
 		else
 		{
  			var str="Возникла ошибка:\n"+
  			        "Описание:"+oXmlDom.parseError.reason+"\n"+
  		    	    "Файл:"+oXmlDom.parseError.url+"\n"+
  		        	"Строка:"+oXmlDom.parseError.line+"\n"+
   			        "Позиция в строке:"+oXmlDom.parseError.lonePos+"\n"+
  			        "Исходный код:"+oXmlDom.parseError.srcText;
  			alert(str);
   		}
	}
}

function doSelect(id)
{
    var fieldId=window.name.substr(13,window.name.length-13);
    //alert(fieldId);
	//alert(opener.document.getElementById('id'+fieldId).value);
	opener.document.getElementById('id'+fieldId).value=id;
	opener.document.getElementById('module'+fieldId).innerHTML=document.getElementById('module').value;
	var fieldName=opener.document.getElementById('fieldName'+fieldId).value
	opener.document.getElementById(fieldName).value=document.getElementById('module').value+'|'+id;
	close();
}

function showSubItems(elId,pos,tbl,module)
{
	if (!module)
	{
		module=document.getElementById('module').value;
		//module='blogs';
	}
	if(!document.oReq)
	{
	 document.oReq=new Object();
	}
	var oReq=document.oReq;
	oReq.tbl=tbl;
	if(oReq.elParent=='undefined')
	{
		oReq.elParent=0;
    }
    else
    {
		oReq.elParent=oReq.elId;
	}
	oReq.elId=elId;
	oReq.pos=pos;
	document.oXmlHttp=zXmlHttp.createRequest();
	var oXmlHttp=document.oXmlHttp;
	oXmlHttp.open("get","/admin.php?module="+module+"&mode=ajax&action=getSubCatList&id="+elId,true);
	document.loading=ShowLoading();
	oXmlHttp.onreadystatechange=function(){showData()};
	oXmlHttp.send('');
	document.oReq=oReq;
	//alert(window.name);
	//alert(window.opener.name);
}

function selModule()
{
	var id=document.getElementById('module').value;
	//alert(id);
	showSubItems(0,1,'baseTable',id);
}
</script>
{/literal}
<h1>Выберите категорию</h1><br/>
<div style="clear:both;"><!-- --></div>
<div class="right">
Модуль: <select id="module" onchange="selModule();">
{foreach from=$dataList item=oItem}
	<option value="{$oItem.id}" {$oItem.sel}>{$oItem.title}</option>
{/foreach}
</select><br/>
<img id="plus0" src="" style="width:0px;height:0px;">
<table id="baseTable" class="layout" width="100%">
    <tr class="titles">
    <td style="background-color: #fffacd;"><h3>ID</h3></td>
    <td style="background-color: #fffacd;"><h3>Категория</h3></td>
    <td style="background-color: #fffacd;"></td>
    </tr>
</table>
</div>
</body></html>
