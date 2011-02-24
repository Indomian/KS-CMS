/**
 * Скрипт который обеспечивает создание окон для загрузки в них данных и обработки этих данных
 * основан на Thickbox 3.1 - One Box To Rule Them All.
 * By Cody Lindley (http://www.codylindley.com)
 * Copyright (c) 2007 cody lindley
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * Код частично переработан для выполнения специализированных функций
 */

var ksWin_pathToImage = "/uploads/templates/admin/images/loadingAnimation.gif";

/*!!!!!!!!!!!!!!!!! edit below this line at your own risk !!!!!!!!!!!!!!!!!!!!!!!*/
var TB_MODE_BIG=false;

$(document).ready(function(){if(!window.imgLoader)
{
	imgLoader = new Image();// preload image
	imgLoader.src = ksWin_pathToImage;
}});

/**
 * Функция выполняет вывод окна в котором отображается контент
 * @param caption string - название окна
 * @param url string - адрес который требуется открыть в окне
 * @param action function - функция обработчик выполняющаяся после открытия окна
 * @param formdata object - данные формы для отправки
 */
function ksWin(caption, url, action,formdata)
{
	try
	{
		$("body","html").css("overflow","hidden");
		$("body").attr('scroll','no');
		obWindow=$("#TB_window");
		if(obWindow.length==0)
		{
			if (typeof document.body.style.maxHeight === "undefined")
			{
				//if IE 6
				$("body","html").css({height: "100%", width: "100%"});
				if (document.getElementById("TB_HideSelect") === null)
				{
					//iframe to hide select elements in ie6
					$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
					$("#TB_overlay").click(ksWin_remove);
				}
			}
			else
			{
				//all others
				if(document.getElementById("TB_overlay") === null)
				{
					$("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
					$("#TB_overlay").click(ksWin_remove);
				}
			}

			if(ksWin_detectMacXFF())
			{
				$("#TB_overlay").addClass("TB_overlayMacFFBGHack");//use png overlay so hide flash
			}
			else
			{
				$("#TB_overlay").addClass("TB_overlayBG");//use background and opacity
			}
			obWindow=$("#TB_window");
		}
		$(window).resize(function(){ksWin_position();});

		if(caption===null){caption="";}
		$("body").append("<div id='TB_load'><img src='"+imgLoader.src+"' /></div>");//add loader to the page
		$('#TB_load').show();//show loader

		var baseURL;
		if(url.indexOf("?")!==-1) baseURL = url.substr(0, url.indexOf("?")); else baseURL = url;
		//var urlType = baseURL.toLowerCase().match(urlString);
		var queryString = url.replace(/^[^\?]+\??/,'');
		var params = ksWin_parseQuery( queryString );

		obWindow.attr({
			'TB_WIDTH':(parseInt(params['width']*1) || 630), //defaults to 630 if no paramaters were added to URL
			'TB_HEIGHT':(parseInt(params['height']*1)  || 440) //defaults to 440 if no paramaters were added to URL
		});
		obWindow.attr({
			'TB_WIDTH_OLD':parseInt(obWindow.attr('TB_WIDTH')),
			'TB_HEIGHT_OLD':parseInt(obWindow.attr('TB_HEIGHT'))
		});
		if(TB_MODE_BIG)
		{
			var obSize=ksWin_getPageSize();
			obWindow.attr({
				'TB_WIDTH':parseInt(obSize.width),
				'TB_HEIGHT':parseInt(obSize.height),
			});
		}

		ajaxContentW = parseInt(obWindow.attr('TB_WIDTH'));
		ajaxContentH = parseInt(obWindow.attr('TB_HEIGHT'))-27;

		if(url.indexOf('TB_iframe') != -1)
		{
			// either iframe or ajax window
			urlNoQuery = url.split('TB_');
			$("#TB_iframeContent").remove();
			if(params['modal'] != "true")
			{
				//iframe no modal
				$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div>" +
						"<div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='Закрыть'><img src='/uploads/templates/admin/images/close.gif' width='19' height='19' /></a></div>" +
						"<div id='TB_closeAjaxWindow'><a href='#' id='TB_sizeWindowButton' title='Свернуть/развернуть'><img src='/uploads/templates/admin/images/sizeup.gif' width='19' height='19' alt='Свернуть/развернуть'/></a></div>" +
						"</div><iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='ksWin_showIframe()' style='width:"+(ajaxContentW)+"px;height:"+(ajaxContentH)+"px;' > </iframe>");
			}
			else
			{
				//iframe modal
				$("#TB_overlay").unbind();
				$("#TB_window").append("<iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='ksWin_showIframe()' style='width:"+(ajaxContentW)+"px;height:"+(ajaxContentH)+"px;'> </iframe>");
			}
		}
		else
		{
			// not an iframe, ajax
			if($("#TB_window").css("display") != "block")
			{
				if(params['modal'] != "true")
				{
					//ajax no modal
					$("#TB_window").append("<div id='TB_title'>"+
							"<div id='TB_ajaxWindowTitle'>"+caption+
							"</div>"+
							"<div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='Закрыть'><img src='/uploads/templates/admin/images/close.gif' width='19' height='19' /></a></div>"+
							"<div id='TB_sizeAjaxWindow'><a href='#' id='TB_sizeWindowButton' title='Свернуть/развернуть'><img src='/uploads/templates/admin/images/sizeup.gif' width='19' height='19' alt='Свернуть/развернуть' /></a></div>"+
							"</div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px'></div>");
				}
				else
				{
					//ajax modal
					$("#TB_overlay").unbind();
					$("#TB_window").append("<div id='TB_ajaxContent' class='TB_modal' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>");
				}
			}
			else
			{
				//this means the window is already up, we are just loading new content via ajax
				$("#TB_ajaxContent")[0].style.width = ajaxContentW +"px";
				$("#TB_ajaxContent")[0].style.height = ajaxContentH +"px";
				$("#TB_ajaxContent")[0].scrollTop = 0;
				$("#TB_ajaxWindowTitle").html(caption);
			}
		}

		$("#TB_closeWindowButton").click(ksWin_remove);
		$("#TB_sizeWindowButton").click(ksWin_triggerSize);

		if(url.indexOf('TB_inline') != -1)
		{
			$("#TB_ajaxContent").append($('#' + params['inlineId']).children());
			$("#TB_window").unload(function () {
				$('#' + params['inlineId']).append( $("#TB_ajaxContent").children() ); // move elements back when you're finished
			});
			ksWin_position();
			$("#TB_load").remove();
			$("#TB_window").css({display:"block"});
		}
		else if(url.indexOf('TB_iframe') != -1)
		{
			if(action!=null)
			{
				$("#TB_iframeContent").bind("onWindowLoad",action);
			}
			ksWin_position();
			if($.browser.safari)
			{
				//safari needs help because it will not fire iframe onload
				$("#TB_load").remove();
				$("#TB_window").css({display:"block"});
				//Делаем чит для сафари, предположим что за секунду мы всетаки получим данные
				//Судя по всему этот чит уже не нужен
				//setTimeout('$("#TB_iframeContent").trigger("onWindowLoad",$("#TB_iframeContent"))',10000);
			}
		}
		else
		{
			if(action!=null)
			{
				$("#TB_window").bind("myEvent",action);
			}
			if(!formdata) formdata='';
			$("#TB_ajaxContent").html('');
			$("#TB_ajaxContent").load(url += "&random=" + (new Date().getTime()),formdata,function(){
				ksWin_position();
				$("#TB_ajaxContent a").click(function(e){
					var t = $(this).attr('title') || $(this).attr('name') || null;
					var a = this.href || this.alt;
					var g = this.rel || false;
					ksWin(t,a,null);
					this.blur();
					return false;
				});
				$("#TB_ajaxContent :submit").click(function(){
					$(this).attr('isSubmit',true);
				});
				$("#TB_ajaxContent form").submit(function()
				{
					var t = $(this).attr('title') || $(this).attr('name') || null;
					var a = this.href || this.alt || $(this).attr('action');
					var g = this.rel || false;
					var obData=$("input, select, textarea",this);
					var res={};
					for(var i=0;i<obData.length;i++)
					{
						var ob=obData.get(i);
						if((ob.type=='submit')&&(!$(ob).attr('isSubmit'))) continue;
						if(ob.name)
						{
							res[ob.name]=ob.value;
						}
					}
					ksWin(t,a,null,res);
					this.blur();
					return false;
				});
				action();
				$("#TB_window").trigger("myEvent",$("#TB_ajaxContent"));
				$("#TB_load").remove();
				$("#TB_window").css({display:"block"});
			});
		}
		if(!params['modal'])
		{
			document.onkeyup = function(e){
				if (e == null) { // ie
					keycode = event.keyCode;
				} else { // mozilla
					keycode = e.which;
				}
				if(keycode == 27){ // close
					ksWin_remove();
				}
			};
		}
	} catch(e) {
		//nothing here
		res='';
		for(i in e)
		{
			res+=i+'='+e[i]+'\n';
		}
		alert(res);
	}
}

/**
 * Функция выполняет вывод окна на экран
 */
function ksWin_showIframe()
{
	$("#TB_iframeContent").trigger("onWindowLoad",$("#TB_iframeContent"));
	$("#TB_load").remove();
	$("#TB_window").css({display:"block"});
}

/**
 * Функция выполняет сворачивание или разворачивание окна в зависимости от текущего состояния.
 */
function ksWin_triggerSize()
{
	if(!TB_MODE_BIG) TB_MODE_BIG=true; else TB_MODE_BIG=false;
	ksWin_position();
	$("#TB_sizeWindowButton>img").attr('src','/uploads/templates/admin/images/size'+(TB_MODE_BIG?'down':'up')+'.gif');
}

/**
 * Функция выполняет закрытие окна
 */
function ksWin_remove()
{
 	$("#TB_imageOff").unbind("click");
	$("#TB_closeWindowButton").unbind("click");
	$("#TB_window").fadeOut("fast",function(){$('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();});
	$("#TB_load").remove();
	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
		$("body","html").css({height: "auto", width: "auto"});
	}
	$("body","html").css("overflow","");
	$('body').attr('scroll','yes');
	document.onkeydown = "";
	document.onkeyup = "";
	TB_MODE_BIG=false;
	return false;
}

function ksWin_position()
{
	obWindow=$('#TB_window');
	var obSize=ksWin_getPageSize();
	if(TB_MODE_BIG)
	{
		obWindow.attr({
			'TB_WIDTH_OLD':parseInt(obWindow.attr('TB_WIDTH')),
			'TB_HEIGHT_OLD':parseInt(obWindow.attr('TB_HEIGHT'))
		});
		obWindow.attr({
			'TB_WIDTH':parseInt(obSize.width),
			'TB_HEIGHT':parseInt(obSize.height),
		});
	}
	else
	{
		obWindow.attr({
			'TB_WIDTH':parseInt(obWindow.attr('TB_WIDTH_OLD')),
			'TB_HEIGHT':parseInt(obWindow.attr('TB_HEIGHT_OLD'))
		});
	}
	var height=parseInt(obWindow.attr('TB_HEIGHT'));
	var width=parseInt(obWindow.attr('TB_WIDTH'));
	if(height>obSize.height) height=parseInt(obSize.height);
	if(width>obSize.width) width=parseInt(obSize.width);
	if($('#TB_ajaxContent').length>0)
	{
		ajaxContentW = width-(parseInt($('#TB_ajaxContent').css('padding-left'))+parseInt($('#TB_ajaxContent').css('padding-right')));
		ajaxContentH = height-(parseInt($('#TB_ajaxContent').css('padding-top'))+parseInt($('#TB_ajaxContent').css('padding-bottom')))-27;
	}
	else
	{
		ajaxContentW = width-(parseInt($('#TB_iframeContent').css('padding-left'))+parseInt($('#TB_iframeContent').css('padding-right')));
		ajaxContentH = height-(parseInt($('#TB_iframeContent').css('padding-top'))+parseInt($('#TB_iframeContent').css('padding-bottom')))-27;
	}
	$("#TB_iframeContent,#TB_ajaxContent").css('width',ajaxContentW +"px").css('height',ajaxContentH +"px");
	$("#TB_window").css({marginLeft: '-' + parseInt((width+10)/2,10) + 'px', 'width': width + 'px','height':height+'px'});
	//if ($.support.positionFixed)
	//{ // take away IE6
		$("#TB_window").css({marginTop: '-' + parseInt((height+10) / 2,10) + 'px'});
//	}
}

function ksWin_parseQuery ( query ) {
   var Params = {};
   if ( ! query ) {return Params;}// return empty object
   var Pairs = query.split(/[;&?]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if(KeyVal.length==3)
      {
      	KeyVal[1]+=KeyVal[2];
      	KeyVal.pop();
      }
      if ( ! KeyVal || KeyVal.length != 2 ) {continue;}
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}

/**
 * Функция возвращает размеры окна
 */
function ksWin_getPageSize()
{
	var de = document.documentElement;
	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
	return {'width':parseInt(w),'height':parseInt(h)};
}

function ksWin_detectMacXFF() {
  var userAgent = navigator.userAgent.toLowerCase();
  if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1) {
    return true;
  }
}


