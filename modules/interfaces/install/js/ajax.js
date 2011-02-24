/**
 * Функция используется для отображения ширмы закрывающей
 * компонент пока он получает данные с сервера
 * @param block_id - id дива в котором находится HTML код компонента.
 */
function ajaxShadow(block_id)
{
	obDiv=$('#'+block_id);
	if(obDiv.length>0)
	{
		obShadow=$('#ajaxShadow');
		if(obShadow.length==0)
		{
			$('body').append('<div class="ajaxLoad" id="ajaxShadow"></div>');
			obShadow=$('#ajaxShadow');
		}
		coords=obDiv.offset();
		obShadow.css({'left':coords.left,'top':coords.top,'width':obDiv.outerWidth(),'height':obDiv.outerHeight(),'z-index':60000}).hide();
		setTimeout('$(\'#ajaxShadow\').fadeIn(500)',500);
	}
}

function ajaxHideShadow(block_id)
{
	$('#ajaxShadow').remove();
}

function ajaxGetFormData(form)
{
	var arParams=new Array();
	list=$('input, select, textarea',form);
	for(var i=0;i<list.length;i++)
	{
		cur=list.eq(i);
		if(cur.attr('name')=='') continue;
		if(cur.attr('type')=='submit'||
		   cur.attr('type')=='image')
		{
			if(cur.attr('isActive')!=true) continue;
		}
		var sParam=encodeURIComponent(cur.attr('name'));
		sParam+="=";
		sParam+=encodeURIComponent(cur.val());
		arParams.push(sParam);
	}
	return arParams.join('&');
}

function objectToURIString(obj)
{
	var arParams=new Array();
	for(ii in obj)
	{
		var sParam=encodeURIComponent(ii);
		sParam+="=";
		sParam+=encodeURIComponent(obj[ii]);
		arParams.push(sParam);
	}
	return arParams.join('&');
}

function absPosition(obj) 
{
	var x = y = 0;
		while(obj) {
    	x += obj.offsetLeft;
    	y += obj.offsetTop;
    	obj = obj.offsetParent;
		}
		return {x:x, y:y};
}