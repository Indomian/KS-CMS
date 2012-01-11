/**
 * В этом файле размещаются функции которые наобходимы на всех страницах системы
 */

var _KS_CMS_ADMIN={
	"menu":{
		"toggleMenu":function(e){
			e.preventDefault();
			if($(this).hasClass('menu_arrow_up'))
				$(this).removeClass('menu_arrow_up').addClass('menu_arrow_down').prev().slideUp(300);
			else
				$(this).addClass('menu_arrow_up').removeClass('menu_arrow_down').prev().slideDown(300);
		}
	},
	"content":{
		"toggelHint":function(e){
			e.preventDefault();
			var obPrev=$(this).prev();
			var cookieTime=new Date();
			cookieTime.setTime(cookieTime.getTime()+360000000);
			if($(this).hasClass('content_arrow_up'))
			{
				obPrev.slideUp(300);
				$(this).removeClass('content_arrow_up').addClass('content_arrow_down');
				_KS_CMS_ADMIN.setCookie('showHelpBar',0,cookieTime.toGMTString());
			}
			else
			{
				obPrev.slideDown(300);
				$(this).addClass('content_arrow_up').removeClass('content_arrow_down');
				_KS_CMS_ADMIN.setCookie('showHelpBar',1,cookieTime.toGMTString());
				
			}
		}
	},
	/**
	 * Метод позволяет установить печеньку на указанные параметры
	 * взято http://www.codenet.ru/webmast/js/Cookies.php
	 */
	"setCookie":function (name, value, expires, path, domain, secure)
	{
	      document.cookie = name + "=" + escape(value) +
	        ((expires) ? "; expires=" + expires : "") +
	        ((path) ? "; path=" + path : "") +
	        ((domain) ? "; domain=" + domain : "") +
	        ((secure) ? "; secure" : "");
	}
}


var adminDate=new Date();
/**
 * Метод позволяет установить печеньку на указанные параметры
 * взято http://www.codenet.ru/webmast/js/Cookies.php
 */
function setCookie (name, value, expires, path, domain, secure)
{
      document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

jQuery.fn.extend({
	"simulateClick":function ()
	{
		this.each(function(){
			var evt = document.createEvent("MouseEvents");
			evt.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, false, false, false, false, 0, null);
			this.dispatchEvent(evt);
		});
	}
}
);

Array.max = function( array ){
    return Math.max.apply( Math, array );
};

Array.min = function( array ){
    return Math.min.apply( Math, array );
};


/**
 * Метод позволяет получить значение печеньки по указанному имени
 * взято http://www.codenet.ru/webmast/js/Cookies.php
 */
function getCookie(name)
{
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}

/**
 * Функция выполняет выделение всех записей в указанной форме
 * @param oForm
 * @param checked
 * @return
 */
function checkAll(oForm, checked)
{
	for (var i=0; i < oForm.length; i++)
	{
		oForm[i].checked = checked;
	}
}

function isAnythingChecked(oForm)
{
	isChecked = false;
	var regExpression = /^sel.*$/;
	for (var i = 0; i < oForm.length; i++)
	{
		if (oForm.elements[i].tagName == 'INPUT')
		{
			if (regExpression.test(oForm.elements[i].getAttribute('name')))
				if (oForm.elements[i].checked == true)
					isChecked = true;
		}
	}
	if (isChecked)
	{
		document.getElementById('commove').disabled = false;
		document.getElementById('move_selected_to').disabled = false;
		document.getElementById('comdel').disabled = false;
		document.getElementById('comact').disabled = false;
		document.getElementById('comdea').disabled = false;
	}
	else
	{
		document.getElementById('commove').disabled = true;
		document.getElementById('move_selected_to').disabled = true;
		document.getElementById('comdel').disabled = true;
		document.getElementById('comact').disabled = true;
		document.getElementById('comdea').disabled = true;
	}
}

/**
 * Функция вывода файлового менеджера для tinyMCE
 * @param field_name
 * @param url
 * @param type
 * @param win
 * @return
 */
function myFileBrowser (field_name, url, type, win)
{
	var cmsURL = '/admin.php?module=kscommander&mode=ajax';    // script URL - use an absolute path!
    if (cmsURL.indexOf("?") < 0)
	{
        //add the type as the only query parameter
	    cmsURL = cmsURL + "?type=" + type;
	}
	else
	{
	    cmsURL = cmsURL + "&type=" + type;
	}

	tinyMCE.activeEditor.windowManager.open(
		{
	        file : cmsURL,
	        title : 'My File Browser',
	        width : 800,  // Your dimensions may differ - toy around with them!
	        height : 400,
	        resizable : "yes",
	        scrollbars: "no",
	        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
	        close_previous : "no"
		}, {
    		window : win,
    		input : field_name
		});
	return false;
}

/* Функция выполняет ajax-запрос на получение списка вложенных элементов пункта меню */
function selectType(type_id, parent_id, div)
{
	var current_tag = document.getElementById("item"+parent_id);
	if ((current_tag.lastChild && current_tag.lastChild.tagName!='UL') || parent_id==0)
	{
		document.myloading=ShowLoading();
		document.div=div;
		document.type_id=type_id;
		document.parent_id=parent_id;
		$.get("/admin.php?module=navigation&mode=ajax&action=getElements&CSC_id="+type_id+"&CSC_parid="+parent_id,null,function(oData)
		{
		   	if (oData)
		    {
		        var parent = document.div;

		        /* Очищаем тэг для вывода нового меню */
			    if(document.parent_id==0)
			       	parent.innerHTML='';

		        var ul=document.createElement("UL");
		        for(i=1; i<oData.length;i++)
            	{
            		var li=document.createElement("li");
            		li.id="item"+oData[i].id;
            		$(li).append($('<input type="radio" name="CM_parent_id"/>').attr('value',oData[i].id));
            		var obA=$('<a href="#">').attr('rel',oData[i].id);
            		$(li).append(obA);
            		obA.append($('<img src="/uploads/templates/admin/images/icons_menu/plus.gif" alt="plus" width="13" height="13"/>'));
            		obA.click(function(event){
            			selectType(document.type_id,this.rel,document.getElementById('item'+this.rel));
            			event.stopImmediatePropagation();
            		});
            		$(li).append('<img src="/uploads/templates/admin/images/icons2/folder.gif" alt="icon" height="20" width="20"/>&nbsp;')
            		$(li).append(oData[i].anchor);
            		$(ul).append($(li));
            	}
            	parent.appendChild(ul);
		  	}
		},"json");
		return false;
	}
}

function togglePanel(elm)
{
	elm.form.CM_anchor.value=elm.form.CSC_title.value;
	if(elm.value==1)
	{
		selectType($(':select[name=CM_type_id]').attr('value'), 0, document.getElementById('item0'));
		document.getElementById('panel').style.display='';

	}
	else
	{
		document.getElementById('panel').style.display='none';
	}
}

/**
 * Функция отображает окно загрузки в случае ajax запроса
 * @return
 */
function ShowLoading()
{
	if($('#loadingBar').length==0)
		$('body').append('<div id="loadingBar"><img src="/uploads/templates/admin/images/loading.gif" border="0"> Обновление</div>');
	$('#loadingBar').show();
}

/**
 * Функция скрывает окно загрузки
 * @return
 */
function HideLoading()
{
	$('#loadingBar').hide();
}

/**
 * Функция преобразует шестнадцетеричное число в десятичное
 * @param hex_string
 * @return
 */
function hexdec (hex_string) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philippe Baumann
    // *     example 1: hexdec('that');
    // *     returns 1: 10
    // *     example 2: hexdec('a0');
    // *     returns 2: 160

    hex_string = (hex_string+'').replace(/[^a-f0-9]/gi, '');
    return parseInt(hex_string, 16);
}

/**
 * Функция преобразует цвет rgb в hsv
 * @return
 */
function rgb2hsv(r,g,b)
{
	if(!g&&!b)
	{
		var arColor=r.match(/#?([0-9a-f]{2,2})([0-9a-f]{2,2})([0-9a-f]{2,2})/i);
		r=hexdec(arColor[1])/65536;
		g=hexdec(arColor[2])/65536;
		b=hexdec(arColor[3])/65536;
	}
	maxc = Math.max(Math.max(r, g), b);
    minc = Math.min(Math.min(r, g), $b);
    if(((maxc==minc)&&maxc==r)||
    	((maxc==minc)&&maxc==g)||
    	((maxc==minc)&&maxc==b))
        h = 0;
    else if(maxc == r)
        h = 60 * ((g - b) / (maxc - minc)) % 360;
    else if(maxc == g)
        h = 60 * ((b - r) / (maxc - minc)) + 120;
    else if(maxc == b)
        h = 60 * ((r - g) / (maxc - minc)) + 240;
    v = maxc;
    if (maxc == 0)
        s = 0;
    else
        s = 1 - (minc / maxc);
    return [h, s, v];
}

CAccessLevels.prototype=new Object();
function CAccessLevels(){};

CAccessLevels.prototype.onClick=function(ob)
{
	var obInput = ob;
	if (typeof(obInput) != 'object')
		return false;

	var obUL = obInput.parentNode.parentNode.parentNode;
	var index = parseInt(ob.value);
	var j=0;
	for(i=0;i<obUL.childNodes.length;i++)
	{
		j=parseInt(obUL.childNodes[i].firstChild.firstChild.value);
		if(obUL.childNodes[i].firstChild.firstChild!=ob)
		{
			if(index<=j)
			{
				if(j!=10)
				{
					obUL.childNodes[i].firstChild.firstChild.checked=true;
					obUL.childNodes[i].className='access_available';
				}
				else
				{
					obUL.childNodes[i].firstChild.firstChild.checked=false;
				}
			}
			else
			{
				if(j!=10)
				{
					obUL.childNodes[i].firstChild.firstChild.checked=false;
					obUL.childNodes[i].className='access_denied';
				}
			}
		}
		else
		{
			obUL.childNodes[i].firstChild.firstChild.checked=true;
			if(j!=10)
			{
				obUL.childNodes[i].className='access_available';
			}
		}
	}
	return false;
};

CAccessLevels.prototype.onClickForum=function(ob)
{
	var obInput=ob;
	if(typeof(obInput)!='object') return false;
	var obUL=obInput.parentNode.parentNode.parentNode;
	var index=parseInt(ob.value);
	var j=0;
	for(i=0;i<obUL.childNodes.length;i++)
	{
		j=parseInt(obUL.childNodes[i].firstChild.firstChild.value);
		if(obUL.childNodes[i].firstChild.firstChild!=ob)
		{
			if(index<=j)
			{
				if(j!=11)
				{
					obUL.childNodes[i].firstChild.firstChild.checked=true;
					obUL.childNodes[i].className='access_available';
				}
				else
				{
					obUL.childNodes[i].firstChild.firstChild.checked=false;
				}
			}
			else
			{
				if(j!=11)
				{
					obUL.childNodes[i].firstChild.firstChild.checked=false;
					obUL.childNodes[i].className='access_denied';
				}
			}
		}
		else
		{
			obUL.childNodes[i].firstChild.firstChild.checked=true;
			if(j!=11)
			{
				obUL.childNodes[i].className='access_available';
			}
		}
	}
	return false;
};

document.obAccessLevels=new CAccessLevels();

/**
 * Функция производит опрос сервера наличие новых уведомлений,и выполняет различные операции
 * в случае необходимости
 */
function adminAskServer()
{
	params={
		'module':'main',
		'modpage':'password',
		'action':'ping',
		'ajax':adminDate.getTime(),
		'ishit':'no'
	};
	$.getJSON('/admin.php',params,function(data){
		if(data.result=='ok')
		{
			if(data.action)
			{
				if(data.action=='relog')
				{
					adminShowLoginWindow();
					return;
				}
			}
			setTimeout("adminAskServer()",10000);
		}
	});
}

/**
 * Функция производит загрузку окна авторизации.
 * @return
 */
function adminShowLoginWindow()
{
	kstb_show('Авторизация','/admin.php?CU_ACTION=logout&modal=true&width=515&height=250&mode=small',false,adminOnShowLoginWindow);
}

/**
 * Функция вызывается при отображении окна авторизации
 * @return
 */
function adminOnShowLoginWindow(e,data)
{
	$("#TB_ajaxContent").css('padding','0').css('overflow','hidden').css('width','545px').css('background-color','#D13B00');
	$("#login_form").unbind('submit').submit(function(ev){
		$.post('/admin.php',$("#login_form").serialize(),function(data){
			try
			{
				eval('var arData='+data+';');
				$("input[name=CU_LOGIN],input[name=CU_PASSWORD]").val('');
				$("div.lbottom2").empty().append('<div class="login_atention" style="color:white;">'+arData.text+'<br/>'+arData.error+'</div>');
			}
			catch(er)
			{
				kstb_remove();
				setTimeout("adminAskServer()",10000);
			}
		});
		ev.preventDefault();
		ev.stopPropagation();
		return false;
	});
	$("div.lbottom2").empty().append('<div class="login_atention" style="color:white;">Ваша сессия истекла, пожалуйста авторизуйтесь.</div>');
	$("div.login_forgot>a").unbind('click').click(function(){
		document.location="/admin.php?lostpwd=Y";
	});
	$("div.login_logo>a").unbind('click').click(function(){
		document.location="/admin.php";
	});
}

$(document).ready(function()
{
	$('input.checkall').click(function(){
		$('input.checkItem').attr('checked',$(this).attr('checked'));
		if($('input.checkItem:checked').length>0)
		{
			$('input.check_depend').attr('disabled',false);
		}
		else
		{
			$('input.check_depend').attr('disabled',true);
		}
	});

	$('input.checkItem').click(function(){
		if($('input.checkItem:checked').length>0)
		{
			$('input.check_depend').attr('disabled',false);
		}
		else
		{
			$('input.check_depend').attr('disabled',true);
		}
	});

	$('input.check_depend').attr('disabled',true);

	//Прикольные навигационные цепочки
	$('a.hasDropDown').mouseover(
		function(e)
		{
			$(this).parent().children('div.navDropDown').show();
			e.preventDefault();
		}
	);
	$('div.navDropDown').mouseleave(
		function(e)
		{
			$(this).hide();
			e.preventDefault();
		}
	);
	setTimeout("adminAskServer()",10000);
	$('a.menu_toggle').click(_KS_CMS_ADMIN.menu.toggleMenu);
	$('div.helpbar').click(_KS_CMS_ADMIN.content.toggelHint);
	$('#topError').ajaxError(function(event, jqXHR, ajaxSettings, thrownError){
		$(this).html('Ошибка при выполнении Ajax запроса<br/>'+jqXHR.responseText).show();
		HideLoading();
	});
});

