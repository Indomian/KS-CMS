function showFrame(x,y,x1,y1,imageid,text)
{
	if(!document.frameDIV)
	{
		document.frameDIV=document.createElement("DIV");
		document.frameDIV.style.display="none";
		document.frameDIV.style.position="absolute";
		document.frameDIV.style.zIndex=1000;
		document.body.appendChild(document.frameDIV);
		document.frameDIV.onmouseout=function(){ hideFrame('');};
	}
	var obIMG=document.getElementById(imageid);
	pos=absPosition(obIMG);
	document.frameDIV.innerHTML=text;
	document.frameDIV.style.left=(parseInt(pos.x)+parseInt(x))+'px';
	document.frameDIV.style.top=(parseInt(pos.y)+parseInt(y))+'px';
	document.frameDIV.style.border="1px dotted #fff";
	document.frameDIV.style.color="#FFF";
	document.frameDIV.style.weight="bold";
	document.frameDIV.style.width=(x1-x)+'px';
	document.frameDIV.style.height=(y1-y)+'px';
	document.frameDIV.style.display='block';
}

function hideFrame(imageid)
{
	if(!document.frameDIV)
	{
		document.frameDIV=document.createElement("DIV");
		document.frameDIV.style.display="none";
		document.frameDIV.style.position="absolute";
		document.frameDIV.style.zIndex=1000;
		document.body.appendChild(document.frameDIV);
	}
	document.frameDIV.style.display="none";
}