var obButton;

function AddGood(td)
{
	kstb_show('Выбрать товар',"/admin.php?module=production&mode=small&width=800&height=480",null,OnGoodLoad);
	obButton=td;
}

function OnGoodLoad(e,data)
{
	var obData=$(":checkbox[name='sel\[elm\]\[\]']",data);
	for(var i=0;i<obData.length;i++)
	{
		obData.eq(i).replaceWith($('<input type="button" name="'+obData.eq(i).attr('value')+'" value="Выбрать"/>').click(function(event){
			AddGoodRow(this.name);
			kstb_remove();
		}));
	}
	$("#navChain>:first-child",data).remove();
	//$(document).trigger("InitCalendar");
	$(document).trigger("InitTiny");
}

function AddGoodRow(id)
{
	if(!obButton) return;
	$.get('/admin.php?module=production&mode=ajax&action=getElement&id='+id,null,function(data)
			{
				if(data)
				{
					var oButtonRow=obButton.parentNode.parentNode;
					var oTableBody=oButtonRow.parentNode;
					var oRow=document.createElement('tr');
					var oCell=document.createElement('td');
					oCell.innerHTML='<input type="checkbox" name="select[]" value="'+data.id+'"/>';
					oRow.appendChild(oCell);
					oCell=document.createElement('td');
					oCell.innerHTML=id;
					oRow.appendChild(oCell);
					oCell=document.createElement('td');
					var oA=document.createElement('a');
					oA.href="/admin.php?module=production&CSC_catid="+data.parent_id+"&ACTION=edit&CSC_id="+data.id+"&type=elm";
					oA.innerHTML=data.title;
					oCell.appendChild(oA);
					oRow.appendChild(oCell);
					oCell=document.createElement('td');
					oCell.innerHTML='<input type="text" name="newcount['+data.id+']" value="1" style="width:50px;"/>';
					oRow.appendChild(oCell);
					oCell=document.createElement('td');
					oCell.innerHTML=data.price;
					oRow.appendChild(oCell);
					oCell=document.createElement('td');
					oCell.innerHTML=data.price;
					oRow.appendChild(oCell);
					oTableBody.insertBefore(oRow,oButtonRow);
				}
			},"json");
}

function OnUserLoad(e,data)
{
	var obData=$(":checkbox[name='sel\[elm\]\[\]']",data);
	for(var i=0;i<obData.length;i++)
	{
		var ob=obData.get(i);
		$(ob).replaceWith($('<input type="button" name="'+ob.value+'" value="Выбрать"/>').click(function(event){
			$("input[name='O_user_id']").get(0).value=this.name;
			kstb_remove();
		}));
	}
	$("#navChain>:first-child",data).remove();
	
}

function SelectUser()
{
	kstb_show('Выберите пользователя',"/admin.php?module=main&modpage=users&mode=small&width=800&height=480",null,OnUserLoad);
}