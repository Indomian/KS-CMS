/*
 * Modified by BlaDe39 Изменен принцип подключения окна, добавлено поведение добавления функции
 * на загрузку нового контента.
 * Thickbox 3.1 - One Box To Rule Them All.
 * By Cody Lindley (http://www.codylindley.com)
 * Copyright (c) 2007 cody lindley
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
*/
		  
var kstb_pathToImage = "/uploads/templates/admin/images/loadingAnimation.gif";

/*!!!!!!!!!!!!!!!!! edit below this line at your own risk !!!!!!!!!!!!!!!!!!!!!!!*/
var TB_MODE_BIG=false;

$(document).ready(function(){if(!window.imgLoader)
{
	imgLoader = new Image();// preload image
	imgLoader.src = kstb_pathToImage;
}});

function kstb_init(domChunk,action){
	if(domChunk.eq)
	{
		//it JQuery object
		ob=domChunk;
	}
	else
	{
		ob=$(domChunk);
	}
	ob.click(function(){
	var t = this.title || this.name || null;
	var a = this.href || this.alt;
	var g = this.rel || false;
	kstb_show(t,a,g,action||null);
	this.blur();
	return false;
	});
}

function kstb_show(caption, url, imageGroup,action,formdata) {//function called when the user clicks on a thickbox link

	try {
		$("body","html").css("overflow","hidden");
		$("body").attr('scroll','no');
		if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
			$("body","html").css({height: "100%", width: "100%"});
			if (document.getElementById("TB_HideSelect") === null) {//iframe to hide select elements in ie6
				$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
				$("#TB_overlay").click(kstb_remove);
			}
		}else{//all others
			if(document.getElementById("TB_overlay") === null){
				$("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
				$("#TB_overlay").click(kstb_remove);
			}
		}
		
		if(kstb_detectMacXFF()){
			$("#TB_overlay").addClass("TB_overlayMacFFBGHack");//use png overlay so hide flash
		}else{
			$("#TB_overlay").addClass("TB_overlayBG");//use background and opacity
		}
		
		if(caption===null){caption="";}
		$("body").append("<div id='TB_load'><img src='"+imgLoader.src+"' /></div>");//add loader to the page
		$('#TB_load').show();//show loader
		
		var baseURL;
	   if(url.indexOf("?")!==-1){ //ff there is a query string involved
			baseURL = url.substr(0, url.indexOf("?"));
	   }else{ 
	   		baseURL = url;
	   }
	   
	   var urlString = /\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/;
	   var urlType = baseURL.toLowerCase().match(urlString);

		if(urlType == '.jpg' || urlType == '.jpeg' || urlType == '.png' || urlType == '.gif' || urlType == '.bmp'){//code to show images
				
			TB_PrevCaption = "";
			TB_PrevURL = "";
			TB_PrevHTML = "";
			TB_NextCaption = "";
			TB_NextURL = "";
			TB_NextHTML = "";
			TB_imageCount = "";
			TB_FoundURL = false;
			if(imageGroup){
				TB_TempArray = $("a[@rel="+imageGroup+"]").get();
				for (TB_Counter = 0; ((TB_Counter < TB_TempArray.length) && (TB_NextHTML === "")); TB_Counter++) {
					var urlTypeTemp = TB_TempArray[TB_Counter].href.toLowerCase().match(urlString);
						if (!(TB_TempArray[TB_Counter].href == url)) {						
							if (TB_FoundURL) {
								TB_NextCaption = TB_TempArray[TB_Counter].title;
								TB_NextURL = TB_TempArray[TB_Counter].href;
								TB_NextHTML = "<span id='TB_next'>&nbsp;&nbsp;<a href='#'>Next &gt;</a></span>";
							} else {
								TB_PrevCaption = TB_TempArray[TB_Counter].title;
								TB_PrevURL = TB_TempArray[TB_Counter].href;
								TB_PrevHTML = "<span id='TB_prev'>&nbsp;&nbsp;<a href='#'>&lt; Prev</a></span>";
							}
						} else {
							TB_FoundURL = true;
							TB_imageCount = "Image " + (TB_Counter + 1) +" of "+ (TB_TempArray.length);											
						}
				}
			}

			imgPreloader = new Image();
			imgPreloader.onload = function(){		
			imgPreloader.onload = null;
				
			// Resizing large images - orginal by Christian Montoya edited by me.
			var pagesize = kstb_getPageSize();
			var x = pagesize[0] - 150;
			var y = pagesize[1] - 150;
			var imageWidth = imgPreloader.width;
			var imageHeight = imgPreloader.height;
			if (imageWidth > x) {
				imageHeight = imageHeight * (x / imageWidth); 
				imageWidth = x; 
				if (imageHeight > y) { 
					imageWidth = imageWidth * (y / imageHeight); 
					imageHeight = y; 
				}
			} else if (imageHeight > y) { 
				imageWidth = imageWidth * (y / imageHeight); 
				imageHeight = y; 
				if (imageWidth > x) { 
					imageHeight = imageHeight * (x / imageWidth); 
					imageWidth = x;
				}
			}
			// End Resizing
			
			TB_WIDTH = imageWidth + 30;
			TB_HEIGHT = imageHeight + 60;
			$("#TB_window").append("<a href='' id='TB_ImageOff' title='Close'><img id='TB_Image' src='"+url+"' width='"+imageWidth+"' height='"+imageHeight+"' alt='"+caption+"'/></a>" + "<div id='TB_caption'>"+caption+"<div id='TB_secondLine'>" + TB_imageCount + TB_PrevHTML + TB_NextHTML + "</div></div><div id='TB_closeWindow'><a href='#' id='TB_closeWindowButton' title='Close'><img src='/uploads/templates/admin/images/close.gif' width='19' height='19' /></a></div>"); 		
			
			$("#TB_closeWindowButton").click(kstb_remove);
			
			if (!(TB_PrevHTML === "")) {
				function goPrev(){
					if($(document).unbind("click",goPrev)){$(document).unbind("click",goPrev);}
					$("#TB_window").remove();
					$("body").append("<div id='TB_window'></div>");
					kstb_show(TB_PrevCaption, TB_PrevURL, imageGroup);
					return false;	
				}
				$("#TB_prev").click(goPrev);
			}
			
			if (!(TB_NextHTML === "")) {		
				function goNext(){
					$("#TB_window").remove();
					$("body").append("<div id='TB_window'></div>");
					kstb_show(TB_NextCaption, TB_NextURL, imageGroup);				
					return false;	
				}
				$("#TB_next").click(goNext);
				
			}

			document.onkeydown = function(e){ 	
				if (e == null) { // ie
					keycode = event.keyCode;
				} else { // mozilla
					keycode = e.which;
				}
				if(keycode == 27){ // close
					kstb_remove();
				} else if(keycode == 190){ // display previous image
					if(!(TB_NextHTML == "")){
						document.onkeydown = "";
						goNext();
					}
				} else if(keycode == 188){ // display next image
					if(!(TB_PrevHTML == "")){
						document.onkeydown = "";
						goPrev();
					}
				}	
			};
			
			kstb_position();
			$("#TB_load").remove();
			$("#TB_ImageOff").click(kstb_remove);
			$("#TB_window").css({display:"block"}); //for safari using css instead of show
			};
			
			imgPreloader.src = url;
		}else{//code to show html
			
			var queryString = url.replace(/^[^\?]+\??/,'');
			var params = kstb_parseQuery( queryString );

			TB_WIDTH = (params['width']*1) + 30 || 630; //defaults to 630 if no paramaters were added to URL
			TB_HEIGHT = (params['height']*1) + 40 || 440; //defaults to 440 if no paramaters were added to URL
			if(TB_MODE_BIG)
			{
				TB_WIDTH_OLD=TB_WIDTH;
				TB_HEIGHT_OLD=TB_HEIGHT;
				var arSize=kstb_getPageSize();
				TB_WIDTH=arSize[0];
				TB_HEIGHT=arSize[1];
			}
			ajaxContentW = TB_WIDTH - 30;
			ajaxContentH = TB_HEIGHT - 45;
			
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
							"</div><iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='kstb_showIframe()' style='width:"+(ajaxContentW + 29)+"px;height:"+(ajaxContentH + 17)+"px;' > </iframe>");
				}
				else
				{
					//iframe modal
					$("#TB_overlay").unbind();
					$("#TB_window").append("<iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='kstb_showIframe()' style='width:"+(ajaxContentW + 29)+"px;height:"+(ajaxContentH + 17)+"px;'> </iframe>");
				}
			}
			else{// not an iframe, ajax
					if($("#TB_window").css("display") != "block"){
						if(params['modal'] != "true"){//ajax no modal
						$("#TB_window").append("<div id='TB_title'>"+
								"<div id='TB_ajaxWindowTitle'>"+caption+
								"</div>"+
								"<div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='Закрыть'><img src='/uploads/templates/admin/images/close.gif' width='19' height='19' /></a></div>"+
								"<div id='TB_sizeAjaxWindow'><a href='#' id='TB_sizeWindowButton' title='Свернуть/развернуть'><img src='/uploads/templates/admin/images/sizeup.gif' width='19' height='19' alt='Свернуть/развернуть' /></a></div>"+
								"</div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px'></div>");
										
						}else{//ajax modal
						$("#TB_overlay").unbind();
						$("#TB_window").append("<div id='TB_ajaxContent' class='TB_modal' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>");	
						}
					}else{//this means the window is already up, we are just loading new content via ajax
						$("#TB_ajaxContent")[0].style.width = ajaxContentW +"px";
						$("#TB_ajaxContent")[0].style.height = ajaxContentH +"px";
						$("#TB_ajaxContent")[0].scrollTop = 0;
						$("#TB_ajaxWindowTitle").html(caption);
					}
			}
					
			$("#TB_closeWindowButton").click(kstb_remove);
			$("#TB_sizeWindowButton").click(kstb_triggerSize);
			
			if(url.indexOf('TB_inline') != -1){	
				$("#TB_ajaxContent").append($('#' + params['inlineId']).children());
				$("#TB_window").unload(function () {
					$('#' + params['inlineId']).append( $("#TB_ajaxContent").children() ); // move elements back when you're finished
				});
				kstb_position();
				$("#TB_load").remove();
				$("#TB_window").css({display:"block"}); 
			}else if(url.indexOf('TB_iframe') != -1){
				if(action!=null)
				{
					$("#TB_iframeContent").bind("onWindowLoad",action);
				}
				kstb_position();
				if($.browser.safari){//safari needs help because it will not fire iframe onload
					$("#TB_load").remove();
					$("#TB_window").css({display:"block"});
					//Делаем чит для сафари, предположим что за секунду мы всетаки получим данные
					setTimeout('$("#TB_iframeContent").trigger("onWindowLoad",$("#TB_iframeContent")',1000);
				}
			}else{
				if(action!=null)
				{
					//$("#TB_ajaxContent").ajaxComplete(action);
					$("#TB_window").bind("onWindowLoad",action);
				}
				if(!formdata) formdata='';
				$("#TB_ajaxContent").html('');
				$("#TB_ajaxContent").load(url += "&random=" + (new Date().getTime()),formdata,function(){//to do a post change this load method
					kstb_position();
					kstb_init("#TB_ajaxContent a");
					$("#TB_ajaxContent :submit").click(function(){
						this.isSubmit=true;
					});
					$("#TB_ajaxContent form").submit(function()
					{
						var t = this.title || this.name || null;
						var a = this.href || this.alt || this.action;
						var g = this.rel || false;
						var obData=$("input, select, textarea",this);
						var res={};
						for(var i=0;i<obData.length;i++)
						{
							var ob=obData.get(i);
							if((ob.type=='submit')&&(!ob.isSubmit)) continue;
							if(ob.name)
							{
								res[ob.name]=ob.value;
							}
						}
						kstb_show(t,a,g,null,res);
						this.blur();
						return false;
					});
					$("#TB_window").trigger("onWindowLoad",$("#TB_ajaxContent"));
					$("#TB_load").remove();
					$("#TB_window").css({display:"block"});
				});
			}
			
		}

		if(!params['modal']){
			document.onkeyup = function(e){ 	
				if (e == null) { // ie
					keycode = event.keyCode;
				} else { // mozilla
					keycode = e.which;
				}
				if(keycode == 27){ // close
					kstb_remove();
				}	
			};
		}
		
	} catch(e) {
		//nothing here
		alert(e);
	}
}

//helper functions below
function kstb_showIframe(){
	$("#TB_iframeContent").trigger("onWindowLoad",$("#TB_iframeContent"));
	$("#TB_load").remove();
	$("#TB_window").css({display:"block"});
}

//Функция разворачивает или сворачивает размер окна
function kstb_triggerSize()
{
	if(!TB_MODE_BIG) TB_MODE_BIG=true; else TB_MODE_BIG=false;
	if(TB_MODE_BIG)
	{
		TB_WIDTH_OLD=TB_WIDTH;
		TB_HEIGHT_OLD=TB_HEIGHT;
		var arSize=kstb_getPageSize();
		TB_WIDTH=arSize[0];
		TB_HEIGHT=arSize[1]+2;
	}
	else
	{
		TB_WIDTH=TB_WIDTH_OLD;
		TB_HEIGHT=TB_HEIGHT_OLD;
	}
	ajaxContentW = TB_WIDTH;
	ajaxContentH = TB_HEIGHT-31;
	$("#TB_ajaxContent").css('width',ajaxContentW +"px");
	$("#TB_iframeContent").css('width',(ajaxContentW) +"px");
	$("#TB_ajaxContent, #TB_iframeContent")[0].style.height = ajaxContentH +"px";
	$("#TB_sizeWindowButton>img").attr('src','/uploads/templates/admin/images/size'+(TB_MODE_BIG?'down':'up')+'.gif');
	kstb_position();
}

function kstb_remove() {
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

function kstb_position() {
	$("#TB_window").css({marginLeft: '-' + parseInt(((TB_WIDTH+10) / 2),10) + 'px', width: TB_WIDTH + 'px'});
	if ($.support.positionFixed) 
	{ // take away IE6
		$("#TB_window").css({marginTop: '-' + parseInt(((TB_HEIGHT+10) / 2),10) + 'px'});
	}
}

function kstb_parseQuery ( query ) {
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

function kstb_getPageSize(){
	var de = document.documentElement;
	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
	arrayPageSize = [w,h];
	return arrayPageSize;
}

function kstb_detectMacXFF() {
  var userAgent = navigator.userAgent.toLowerCase();
  if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1) {
    return true;
  }
}


