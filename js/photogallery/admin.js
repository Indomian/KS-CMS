var ksSelector;

/**
 * Данная функция вызывается после завершения выбора области
 * @param img
 * @param selection
 * @return
 */
function afterSelect(img,selection)
{
	coord=selection;
	if(coord)
	{
		pos=$(img).offset();
		x=pos.left+coord.x1+Math.round((coord.x2-coord.x1)/2);
		y=pos.top+coord.y2;
		ShowRect(x,y);
	}
}

/**
 * данная функция вызывается перед выбором области
 * @param img
 * @return
 */
function beforeSelect(img)
{
	ksSelector.setOptions({show:true});
	ksSelector.update();
	$('#floatInput').fadeOut(500);
	$('#cropFloatInput').fadeOut(500);
}

/**
 * Данная функуия отображает панель ввода текста
 * @param x
 * @param y
 * @return
 */
function ShowRect(x,y)
{
	var div=$('#floatInput');
	div.css('position','absolute').css('left',(x-100)+'px').css('top',y+'px').fadeIn(1000);
}

/**
 * Функция выполняет вывод окна для подтверждения удаления области
 * @param img
 * @param coord
 * @return
 */
function afterCropSelect(img,coord)
{
	if(coord)
	{
		pos=$(img).offset();
		x=pos.left+coord.x1+Math.round((coord.x2-coord.x1)/2);
		y=pos.top+coord.y2;
		$('#cropFloatInput').css('position','absolute').css('left',(x-100)+'px').css('top',y+'px').fadeIn(1000);
	}
}

/**
 * Данная функция выполняет отправку координат выделенной области на сервер
 * @return
 */
function cropCoords()
{
	//Форматирование данных из формы
	if(!ksSelector) return;
	var res=Array();
	form=$('form#cropFloatInputForm');
	coord=ksSelector.getSelection();
	data=form.serialize()+'&x='+coord.x1+'&y='+coord.y1+'&x1='+coord.x2+'&y1='+coord.y2;
	//Отправка данных
	$.post("/admin.php?module=photogallery&page=cropImage",data,function(data)
	{
		var time=new Date();
		if(data)
		{
			$("#sampleid").get(0).src=data+"?random="+time.getTime();
		}
		ksSelector.setOptions({hide:true});
		ksSelector.update();
		$('#cropFloatInput').fadeOut(500);
	});
	return false;
}

/**
 * Данная функция выполняет отправку координат выделенной области на сервер
 * @return
 */
function sendCoords()
{
	//Форматирование данных из формы
	if(!ksSelector) return;
	var res=Array();
	form=$('form#floatInputForm');
	coord=ksSelector.getSelection();
	data=form.serialize()+'&x='+coord.x1+'&y='+coord.y1+'&x1='+coord.x2+'&y1='+coord.y2;
	//Отправка данных
	$.post("/admin.php?module=photogallery&page=addFrame",data,function(data)
	{
		if(data)
		{
			var oTable=document.getElementById('selectionsTable');
			var oRow=oTable.insertRow(-1);
			var oCell=oRow.insertCell(-1);
			var arData=data.split('||');
			oCell.innerHTML=arData[0];
			oCell=oRow.insertCell(-1);
			oCell.innerHTML="<a onclick=\"SelectRow(this)\">"+arData[1]+"</a>";
			oCell.coord={x:parseInt(arData[2]),y:parseInt(arData[3]),x1:parseInt(arData[4]),y1:parseInt(arData[5])};
			oCell=oRow.insertCell(-1);
			oCell.innerHTML='<img onclick="DeleteSelection(this)" src="/uploads/templates/admin/images/icons2/delete.gif" width="11" height="11">';
			ksSelector.setOptions({hide:true});
			ksSelector.update();
			$('#floatInput').fadeOut(500);
			document.getElementById('floatInputForm').text.value='';
	     }
	});
	return false;
}


function SelectRow(href)
{
	if(!ksSelector) return;
	var oTable=document.getElementById('selectionsTable');
	for(i=0;i<oTable.rows.length;i++)
	{
		for(j=0;j<oTable.rows[i].cells.length;j++)
		{
			oTable.rows[i].cells[j].style.backgroundColor='#FFFFFF';
		}
	}
	var oRow=href.parentNode.parentNode;
	var oCell=href.parentNode;
	for(i=0;i<oRow.cells.length;i++)
	{
		oRow.cells[i].style.backgroundColor='#FFFFFF';
	}
	ksSelector.setSelection(oCell.coord.x, oCell.coord.y, oCell.coord.x1, oCell.coord.y1);
	//ksSelector.update();
	ksSelector.setOptions({show:true});
	document.getElementById('floatInputForm').text.value=href.innerHTML;
	pos=$('#sampleid').offset();
	x=pos.left+oCell.coord.x+Math.round((oCell.coord.x1-oCell.coord.x)/2);
	y=pos.top+oCell.coord.y1;
	ShowRect(x,y);
}

function DeleteSelection(href)
{
	//Подготовка данных
	var oRow=href.parentNode.parentNode;
	var oCell=href.parentNode;
	var selId=oRow.cells[0].innerHTML;
	var pictureId=document.getElementById('floatInputForm').imageId.value;
	//Отправка данных
	$.get("/admin.php?module=photogallery&page=deleteFrame&id="+selId+"&imageId="+pictureId,null,function(data)
			{
				if(data)
				{
					var oTable=document.getElementById('selectionsTable');
					for(i=0;i<oTable.rows.length;i++)
					{
						if(parseInt(oTable.rows[i].cells[0].innerHTML)==parseInt(data))
						{
							oTable.deleteRow(i);
							document.Marqueetool.unselectAll();
							document.getElementById('floatInput').style.display='none';
							document.getElementById('floatInputForm').text.value='';
						}
					}
				}
  			});
	return false;
}