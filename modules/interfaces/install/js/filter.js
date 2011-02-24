function ToggleFilterPanel(sender)
{
	var cookieTime=new Date();
	cookieTime.setTime(cookieTime.getTime()+360000000);
	if(document.getElementById('filterPanel').style.display=='none')
	{
		document.getElementById('filterPanel').style.display='';
		if(typeof(sender)=='object')sender.className='content_arrow_up';
		setCookie('showFilter',1,cookieTime.toGMTString());
	}
	else 
	{
		document.getElementById('filterPanel').style.display='none';
		if(typeof(sender)=='object')sender.className='content_arrow_down';
		setCookie('showFilter',0,cookieTime.toGMTString());
	}
}