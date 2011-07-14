/**
 * В этом файле размещаются функции которые наобходимы на всех страницах системы
 */
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
 * Функция выполняет переключение статуса отображения блока подсказок к модулю
 * @param div - объект переключатель
 * @return
 */
function ToggleHelpBar(div)
{
	if(typeof(div)!='object') return false;
	if(typeof(div.previousSibling)=='object')
	{
		var cookieTime=new Date();
		cookieTime.setTime(cookieTime.getTime()+360000000);
		if(div.previousSibling.style.display=='none')
		{
			div.previousSibling.style.display='';
			if(typeof(div)=='object') div.className='content_arrow_up';
			setCookie('showHelpBar',0,cookieTime.toGMTString());
		}
		else
		{
			div.previousSibling.style.display='none';
			if(typeof(div)=='object') div.className='content_arrow_down';
			setCookie('showHelpBar',1,cookieTime.toGMTString());
		}
    }
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

function ShowLoading()
{
	var oFrame=document.createElement('div');
	oFrame.id='fixme';
	oFrame.className='loadingBar';
	oFrame.innerHTML='<img src="/uploads/templates/admin/images/loading.gif" border="0"> Обновление';
	oFrame.style.display='block';
	document.body.appendChild(oFrame);
	return oFrame;
}

function HideLoading(sender)
{
	if(sender)
	{
		document.body.removeChild(sender);
	}
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

CTemplates.prototype=new Object();
function CTemplates(){};

CTemplates.prototype.collapseSub=function(key, to_collapse)
{
	var src = $('#sub_list_img_'+key);
	if(!src.attr('src')) return '';
	var src_arr = src.attr('src').split("/");

	if (to_collapse == undefined)
	{
		if (src_arr[src_arr.length-1] == "plus.gif")to_collapse = true;	else to_collapse = false;
	}

	if (to_collapse)
	{
		src_arr[src_arr.length-1] = "minus.gif";
		$('#sub_list_' + key).css('display','block');
	}
	else
	{
		src_arr[src_arr.length-1] = "plus.gif";
		$('#sub_list_' + key).css('display','none');
	}
	src.attr('src',src_arr.join("/"));
};

CTemplates.prototype.collapseAll=function(to_collapse)
{
	var sub_lists = $('[name=sub_list]');
	if (sub_lists.length > 0)
	{
		for (var i = 0; i < sub_lists.length; i++)
		{
			var arData=/^[a-z_]+_([0-9]+)$/.exec(sub_lists.eq(i).attr('id'));
			if((arData!=0)&&(arData.length>1))
			{
				this.collapseSub(arData[1], to_collapse);
			}
		}
	}
};

window.obTemplates=new CTemplates();

var ksUtils =
{
	arEvents: Array(),

	addEvent: function(el, evname, func, capture)
	{
		if(el.attachEvent) // IE
			//Добавлена поддержка сложного вызова
			el.attachEvent("on" + evname,  function() { func.call(el) });
		else if(el.addEventListener) // Gecko / W3C
			el.addEventListener(evname, func, false);
		else
			el["on" + evname] = func;
		this.arEvents[this.arEvents.length] = {'element': el, 'event': evname, 'fn': func};
	},

	removeEvent: function(el, evname, func)
	{
		if(el.detachEvent) // IE
			el.detachEvent("on" + evname, func);
		else if(el.removeEventListener) // Gecko / W3C
			el.removeEventListener(evname, func, false);
		else
			el["on" + evname] = null;
	},

	removeAllEvents: function(el)
	{
		for(var i in this.arEvents) // possible unnecessary iterations using prototype.js
		{
			if(this.arEvents[i] && (el==false || el==this.arEvents[i].element))
			{
				jsUtils.removeEvent(this.arEvents[i].element, this.arEvents[i].event, this.arEvents[i].fn);
				this.arEvents[i] = null;
			}
		}
		if(el==false)
			this.arEvents.length = 0;
	},

	//Исправляем обработку событий
	FixEvent:function(event)
	{
	  // получить объект события
	  event = event || window.event

	  // один объект события может передаваться по цепочке разным обработчикам
	  // при этом кроссбраузерная обработка будет вызвана только 1 раз
	  if ( event.isFixed ) {
	    return event
	  }
	  event.isFixed = true // пометить событие как обработанное

	  // добавить preventDefault/stopPropagation для IE
	  event.preventDefault = event.preventDefault || function(){this.returnValue = false}
	  event.stopPropagation = event.stopPropagaton || function(){this.cancelBubble = true}

	  // добавить target для IE
	  if (!event.target) {
	      event.target = event.srcElement
	  }

	  // добавить relatedTarget в IE, если это нужно
	  if (!event.relatedTarget && event.fromElement) {
	      event.relatedTarget = event.fromElement == event.target ? event.toElement : event.fromElement;
	  }

	  // вычислить pageX/pageY для IE
	  if ( event.pageX == null && event.clientX != null ) {
	      var html = document.documentElement, body = document.body;
	      event.pageX = event.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0);
	      event.pageY = event.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0);
	  }

	  // записать нажатую кнопку мыши в which для IE
	  // 1 == левая; 2 == средняя; 3 == правая
	  if ( !event.which && event.button ) {
	      event.which = (event.button & 1 ? 1 : ( event.button & 2 ? 3 : ( event.button & 4 ? 2 : 0 ) ));
	  }

	  return event
	},

	GetWindowInnerSize:function(pDoc)
	{
		var width, height,stop,sleft;
		if (!pDoc)
			pDoc = document;

		if (self.innerHeight) // all except Explorer
		{
			width = self.innerWidth;
			height = self.innerHeight;
		}
		else if (pDoc.documentElement && pDoc.documentElement.clientHeight) // Explorer 6 Strict Mode
		{
			width = pDoc.documentElement.clientWidth;
			height = pDoc.documentElement.clientHeight;
		}
		else if (pDoc.body) // other Explorers
		{
			width = pDoc.body.clientWidth;
			height = pDoc.body.clientHeight;
		}
		//смешение по высоте
		stop=self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
		sleft=self.pageXOffset || (document.documentElement && document.documentElement.scrollLeft) || (document.body && document.body.scrollLeft);
		return {sTop:stop, sLeft:sleft,innerWidth : width, innerHeight : height};
	},

	showShadow:function()
	{
	},

	hideShadow:function()
	{
	},

	IsIE: function()
	{
		return (document.attachEvent && !this.IsOpera());
	},

	IsOpera: function()
	{
		return (navigator.userAgent.toLowerCase().indexOf('opera') != -1);
	},

	IsSafari: function()
	{
		var userAgent = navigator.userAgent.toLowerCase();
		return (/webkit/.test(userAgent));
	},

	urlencode: function(s)
	{
		return escape(s).replace(new RegExp('\\+','g'), '%2B');
	},

	/**
	 * Функция для получения абсолютных координат элемента
	 * @param obj - объект
	 * @return
	 */
	ksbAbsPosition:function (obj){
	    var x = y = 0;
	    while(obj) {
	          x += obj.offsetLeft;
	          y += obj.offsetTop;
	          obj = obj.offsetParent;
	    }
	    return {x:x, y:y};
	},

	setElementOpacity:function (nOpacity,elem)
	{
	  var opacityProp = this.getOpacityProperty();

	  if (!elem || !opacityProp) return;

	  if (opacityProp=="filter")  // Internet Exploder 5.5+
	  {
	    nOpacity *= 100;

	    var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
	    if (oAlpha) oAlpha.opacity = nOpacity;
	    else elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")";
	  }
	  else
	    	elem.style[opacityProp] = nOpacity;
	},

	getOpacityProperty:function()
	{
	  if (typeof document.body.style.opacity == 'string') // CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
	    return 'opacity';
	  else if (typeof document.body.style.MozOpacity == 'string') // Mozilla 1.6 Х ЛКЮДЬЕ, Firefox 0.8
	    return 'MozOpacity';
	  else if (typeof document.body.style.KhtmlOpacity == 'string') // Konqueror 3.1, Safari 1.1
	    return 'KhtmlOpacity';
	  else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) // Internet Exploder 5.5+
	    return 'filter';
	     return false; //МЕР ОПНГПЮВМНЯРХ
	},

	ksbAddDocumentDragEventHandlers:function() {
		document.ondragstart = document.body.onselectstart = function() {return false}
	},
	ksbRemoveDocumentDragEventHandlers:function () {
		document.onmousemove = document.onmouseup = document.ondragstart = document.body.onselectstart = null
	},

	searchStyle:function(style)
	{
		for(var i=0,ii=document.styleSheets.length;i<ii;i++)
		{
			if(document.styleSheets[i].rules.length>0)
			{
				for(var j=0;j<document.styleSheets[i].rules.length;j++)
				{
					if(document.styleSheets[i].rules[j].selectorText==style)
					{
						return document.styleSheets[i].rules[j].style;
					}
				}
			}
		}
		return null;
	}

};

var boxPrev;
var pHover;

function LineHover(id,action)
{
	myP = document.getElementById('line'+id);
	if(!myP) return;
	if(typeof(myP)!='object') return;
	if(action == 'hover' && pHover!=myP)
	{
		myP.className='tree_line_hover';
	}
	if(action == 'normal' && pHover!=myP)
	{
		myP.className='tree_line_normal';
	}
	if(action == 'hoverBlock')
	{
		if(myP != pHover)
		{
			myP.className='tree_line_hover_block';
			if(pHover != null) { pHover.className='tree_line_normal'; }
			pHover=myP;
		}
		else
		{
			myP.className='tree_line_normal';
			pHover=null;
		}
	}
}

function AddBox(id)
{
	var box = document.getElementById(id);
	if(box.style.display=='none')
	{
		//alert(boxPrev);
		box.style.display = '';
		if(box != boxPrev)
		{
			if(boxPrev==null)
			{
				boxPrev = box;
			}
			else
			{
				boxPrev.style.display='none';
				boxPrev = box;
			}
		}
	}
	else
	{
		box.style.display='none';
	}
	return false;
}

function thisnode(param)
{
	MyNode = param;
	//alert(MyNode);
}

function dis(sender,obj)
{
	if (document.getElementById(obj).style.display == 'none')
	{
		document.getElementById(obj).style.display = 'block';
		document.getElementById(sender).className = 'menu_arrow_up';
	}
	else
	{
		document.getElementById(obj).style.display = 'none';
		document.getElementById(sender).className = 'menu_arrow_down';
	}
}

/**
 * Функция устанавливает обработчик кнопки Отмена в лайт версии
 * @param e - событие
 * @param data - дом элемент полученный в ответ на запрос
 * @return
 */
function liteData(e,data)
{
	$("#navChain",data).hide();
	$("a.cancel_button",data).unbind('click').click(function(e){self.parent.kstb_remove();e.preventDefault();});
	$("input[name=update]",data).hide();
}

/**
 *
 */
function liteDeleteItem(e)
{
	if(confirm('Вы действительно хотите удалить эту запись?'))
	{
		var ksRef=this.href;
		$.get(this.href,null,function(data){
			var arParams=kstb_parseQuery(ksRef);
			if(arParams)
			{
				nextStep(arParams.module,arParams.ajaxreq,arParams.liid,true);
			}
		})
	}
	e.preventDefault();
}

/**
 * Функция отображает панель виджетов
 * @return
 */
function showWidgetPanel()
{
	$('#widgetCont').show();
	document.getElementById('widgetCont').style.width="220px";
	document.getElementById('widgetCont').firstChild.style.width="220px";
	document.getElementById('rarrow').style.display='';
	document.getElementById('larrow').style.display='none';
}

/**
 * Функция скрывает панель виджетов
 * @return
 */
function hideWidgetPanel()
{
	$('#widgetCont').hide();
	document.getElementById('widgetCont').style.width="0px";
	document.getElementById('widgetCont').firstChild.style.width="0px";
	document.getElementById('rarrow').style.display='none';
	document.getElementById('larrow').style.display='';
}

//Определение поддержки браузеров положения окна FIXED
function isPositionFixed()
{
	var isSupported = null;
	if (document.createElement)
	{
	    var el = document.createElement('div');
	    if (el && el.style)
	    {
	      el.style.width = '1px';
	      el.style.height = '1px';
	      el.style.position = 'fixed';
	      el.style.top = '10px';
	      var root = document.body;
	      if (root &&
	          root.appendChild &&
	          root.removeChild) {
	        root.appendChild(el);
	        isSupported = (el.offsetTop === 10);
	        root.removeChild(el);
	      }
	      el = null;
	    }
	}
	return isSupported;
}

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

	$.support.positionFixed=isPositionFixed()
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
});

