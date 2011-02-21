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
			}        }    }}

function ShowTabs(ulname)
{
	var oList=document.getElementById(ulname);
	if(typeof(oList)!='object') return false;
	for(i=0;i<oList.childNodes.length;i++)
	{
		var oTab=oList.childNodes[i];
		if(oTab.className.indexOf('hide')>-1)
		{
			oTab.className=oTab.className.replace('hide','visible');
		}
		else
		{
			oTab.className=oTab.className.replace('visible','hide');
		}
    }
	return true;
}

