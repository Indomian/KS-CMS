function ChangeActive(caller,list)
{
	var oCaller=document.getElementById(caller);
	var oList=document.getElementById(list);
	for(i=0;i<oList.childNodes.length;i++)
	{
		var oTab=oList.childNodes[i];
		if(oTab.id==caller)
		{
			$(oTab).addClass('active');
			if(document.getElementById(oTab.id+'cont'))
			{
				oDiv=document.getElementById(oTab.id+'cont');
				oDiv.style.display='block';
			}
			//Меняем ссылку в окне браузера
			var ss = window.location.href;
			var linx = new Array();
			linx = ss.split('#');
			window.location.href = linx[0] + '#'+$(oTab).attr('id');
			//Вешаем печеньку
			var cookieTime=new Date();
			cookieTime.setTime(cookieTime.getTime()+360000000);
			setCookie('lastSelectedTab',oTab.id,cookieTime.toGMTString());
        }
        else
        {
        	$(oTab).removeClass('active');
			if(document.getElementById(oTab.id+'cont'))
			{
				oDiv=document.getElementById(oTab.id+'cont');
				oDiv.style.display='none';
			}
        }
    }
}

function ShowTabs(ulname)
{
	var oList=document.getElementById(ulname);
	if(typeof(oList)!='object') return false;
	for(i=0;i<oList.childNodes.length;i++)
	{
		var oTab=oList.childNodes[i];
		if(oTab.className.indexOf('hide')>-1)
			oTab.className=oTab.className.replace('hide','visible');
		else
			oTab.className=oTab.className.replace('visible','hide');
    }
	return true;
}

/**
 * Переключение вкладки по запросу из адресной строки
 */
$(document).ready(function(){
	addr=location.href;
    linx = addr.split('#');
	anchor=linx[1];
	R=/^([a-z0-9_]+)_[0-9]{11}_tab([0-9]{1,2})$/i;
	if(R.test(anchor))
	{
		matches=anchor.match(R);
		tabs=$('ul.tabs2[id^='+matches[1]+']');
		if(tabs.length>0)
		{
			tab=$('li[id$=tab'+matches[2]+']');
			if(tab.length>0)
			{
				ChangeActive(tab.attr('id'),tabs.attr('id'));
			}
		}
	}
});