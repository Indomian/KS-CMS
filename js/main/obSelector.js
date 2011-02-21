var obSelector={
	"showSubItems":function (elId,pos,tbl,module)
	{
		if (!module)
		{
			module=document.getElementById('module').value;
		}
		obSelector.tbl=tbl;
		if(obSelector.elParent=='undefined')
			obSelector.elParent=0;
    	else
    		obSelector.elParent=obSelector.elId;
		obSelector.elId=elId;
		obSelector.pos=pos;
		$.get("/admin.php?module="+module+"&mode=ajax&action=getSubElmList&id="+elId,null,obSelector.showData,"json");
		obSelector.loading=ShowLoading();
	},
	"showData":function(data,status)
	{
		if(data)
		{
		    oData=data.PARSED;
		    if (oData!=null)
		    {
		    	var table = document.getElementById(obSelector.tbl);
	        	var cnt = table.rows.length;
	        	for(i=0;i<cnt-1;i++) table.deleteRow(1);
	        	from=0;
	        	pos=1;
	        	if (obSelector.elId!=0)
	        	{
	                var oRow=table.insertRow(pos);
	                var oCell = oRow.insertCell(0);
		        	oCell.align="left";
		            oCell.innerHTML = oData[0].parent_id;
		            var oCell = oRow.insertCell(1);
		        	oCell.align="left";
		            oCell.innerHTML = '<a href="#" onclick="obSelector.showSubItems('+oData[0].parent_id+',1,\''+obSelector.tbl+'\');return false;">Назад...</a>';
		            var oCell = oRow.insertCell(2);
		        	oCell.align="right";
		            oCell.innerHTML = "";
		            from++
	        	}
		       	for(i=from;i<oData.length;i++)
		     	{
		     	    if ((i%2)==0)
		     	     	color='odd';
		     		else
		     			color='';
		     		var oRow = table.insertRow(pos+i);
		     		oRow.className=color;
		        	var oCell = oRow.insertCell(0);
		        	oCell.align="left";
		            oCell.innerHTML = oData[i].id;
		            var oCell = oRow.insertCell(1);
		        	oCell.align="left";
		            oCell.innerHTML = '<a href="#" onclick="obSelector.showSubItems('+oData[i].id+',1,\''+obSelector.tbl+'\');return false;">'+oData[i].title+'</a>';
		            var oCell = oRow.insertCell(2);
		        	oCell.align="right";
		            oCell.innerHTML = "";
		     	}
		     	pos+=i;
		  		oData=data.ELEMENTS;
				if (oData!=null)
			    {
		        	from=0;
		        	for(i=from;i<oData.length;i++)
			     	{
			     	    if ((i%2)==0)
			     	       	color='odd';
			     		else
			     			color='#f0f0f0';
			     		var oRow = table.insertRow(pos+i);
			     		oRow.className=color;
			        	var oCell = oRow.insertCell(0);
			        	oCell.align="left";
			            oCell.innerHTML = oData[i].id;
			            var oCell = oRow.insertCell(1);
			        	oCell.align="left";
			            oCell.innerHTML = oData[i].title;
			            var oCell = oRow.insertCell(2);
			        	oCell.align="right";
			            oCell.innerHTML = "<input type=\"button\" value=\"Выбрать\" onclick=\"obSelector.doSelect("+oData[i].id+");\">";
			     	}
	  			}
  			}
		}
		HideLoading(obSelector.loading);
	},
	"doSelect":function(id)
	{
	    var fieldId=window.name.substr(13,window.name.length-13);
		opener.document.getElementById('id'+fieldId).value=id;
		opener.document.getElementById('module'+fieldId).innerHTML=document.getElementById('module').value;
		var fieldName=opener.document.getElementById('fieldName'+fieldId).value
		opener.document.getElementById(fieldName).value=document.getElementById('module').value+'|'+id;
		close();
	},
	"selModule":function ()
	{
		var id=document.getElementById('module').value;
		obSelector.showSubItems(0,1,'baseTable',id);
	}
};