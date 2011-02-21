var bChart=false;
function drawChartMoves(froms,tos,moves)
{
	if(bChart) return;
	bChart=true;
	// Draw
	var chartDiv=document.getElementById('chart');
	var width = chartDiv.offsetWidth,
		paddingLeft=100,
		paddingRight=50,
		paddingTop=50,
		paddingBottom=50,
		boxPadding=5,
		boxRound=3,
		maxLetters=10,
		hintStringLetters=30,
		r=300,
		height = r*2+paddingTop+paddingBottom+100,
		cx=width/2,
		cy=height/2,
		rad=Math.PI/180,
	    paper = Raphael("chart", width, height),
	    back=paper.rect(0,0,paper.width,paper.height).attr({"fill-opacity":0,fill:"#0f0"}),
	    chart = paper.set(),
	    startPosition,
	    hintbox=paper.rect(0,0,100,100,5).attr({opacity:0}).hide();
	    hinttext=paper.text(0,0,'Hello').attr({opacity:0}).hide();
	chartDiv.style.height=height+'px';
	for(i in froms)
	{
		var page=paper.set();
		page.code=i;
		var txt=paper.text(0,0,froms[i]);
		var bbox=txt.getBBox();
		var rect=paper.rect(bbox.x-boxPadding,bbox.y-boxPadding,bbox.width+boxPadding*2,bbox.height+boxPadding*2,3);
		page.push(rect);
		page.push(txt);
		page.translate(cx,cy);
		//page.attr({x:100,y:100});
		page.ksType='from';
		chart.push(page);
	}
	
	var j=1;
	var angle=0;
	var step=360/tos.length;
	for(i=0;i<tos.length;i++)
	{
		var page=paper.set();
		var tmp=tos[i].text,
			hint=tos[i].text;
		page.code=tos[i].code;
		if(tos[i].text.length>maxLetters)
		{
			tmp=tos[i].text.substring(0,maxLetters);
			if(tos[i].text.length>hintStringLetters)
			{
				var lines=Math.round(tos[i].text.length/hintStringLetters);
				hint='';
				for(var j=0;j<lines;j++)
				{
					hint+=tos[i].text.substring(j*hintStringLetters,(j+1)*hintStringLetters)+'\n';
				}
			}
		}

		var txt=paper.text(0,0,tmp),
			bbox=txt.getBBox();
		var rect=paper.rect(bbox.x-boxPadding,bbox.y-boxPadding,bbox.width+boxPadding*2,bbox.height+boxPadding*2,3);
		txt.hintText=hint;
		txt.mouseover(function () {
				var x=this.attrs.x,
					y=this.attrs.y+this.getBBox().height;
				hinttext.attr({text:this.hintText});
				var size=hinttext.getBBox();
				hinttext.show().animate({opacity:1},500);
                hintbox.attr({x:x,y:y,width:size.width,height:size.height}).show().animate({opacity:1},500);
                hinttext.attr({x:x+size.width/2,y:y+size.height/2});
            }).mouseout(function () {
            	hintbox.hide();
            	hinttext.hide();
            	hintbox.attr({opacity:0});
            	hinttext.attr({opacity:0}); 
            });
		page.push(rect);
		page.push(txt);
		page.translate(cx+r*Math.cos(angle*rad),cy+r*Math.sin(angle*rad));
		angle+=step;
		page.ksType='to';
		chart.push(page);
		j++;
	}
	
	for(i=0;i<moves.length;i++)
	{
		var from=null,
			to=null;
		for(j=0;j<chart.length;j++)
		{
			if(chart[j].ksType=='from')
			{
				if(chart[j].code==moves[i].from)
				{
					from=chart[j];
				}
			}
			if(chart[j].ksType=='to')
			{
				if(chart[j].code==moves[i].to)
				{
					to=chart[j];
				}
			}
			if((to!=null) && (from!=null))
			{
				break;
			}
		}
		if((to!=null) && (from!=null))
		{
			chart.push(paper.connection(from,to,"#f00").line);
		}
	}
	
	var isDrag = false;
	back[0].onmousedown=function(e)
	{
    	e=ksUtils.FixEvent(e);
		back.startPosition={x:e.clientX,y:e.clientY};
		ksUtils.ksbAddDocumentDragEventHandlers();
		document.onmousemove=function(elem)
		{
			elem=ksUtils.FixEvent(elem);
			var dx=back.startPosition.x-elem.clientX;
			var dy=back.startPosition.y-elem.clientY;
			back.startPosition={x:elem.clientX,y:elem.clientY};
			chart.translate(-dx,-dy);
		};
		document.onmouseup=function(elem)
		{
			ksUtils.ksbRemoveDocumentDragEventHandlers();
		};
	}
	back.toFront();
}

var sign=function(a)
{
	if(a>0) return 1;
	if(a<0) return -1;
	if(a==0) return 0;
}

var bChart2=false;
function drawChartUserMoves(moves)
{
	if(bChart2) return;
	bChart2=true;
	// Draw
	var chartDiv=document.getElementById('chart2');
	var width = chartDiv.offsetWidth,
		paddingLeft=100,
		paddingRight=50,
		paddingTop=50,
		paddingBottom=50,
		boxPadding=5,
		boxRound=3,
		maxLetters=30,
		hintStringLetters=30,
		r=300,
		height = r*2+paddingTop+paddingBottom+100,
		rad=Math.PI/180,
	    paper = Raphael("chart2", width, height),
	    back=paper.rect(0,0,paper.width,paper.height).attr({"fill-opacity":0,fill:"#0f0"}),
	    //padding=paper.rect(paddingLeft,paddingTop,width-paddingRight-paddingLeft,height-paddingTop-paddingBottom),
	    chart = paper.set(),
	    startPosition,
	    X=paddingLeft,
	    Y=100,
	    OLDX=100,
	    OLDY=100,
	    DX=100,
	    DY=100,
	    pos=0;
	    cx=(width-paddingLeft-paddingTop)/2,
	    cy=100,
	    hintbox=paper.rect(0,0,100,100,5).attr({opacity:1,"fill":"#fff","stroke-width":2,"fill-opacity":1}).hide(),
	    hinttext=paper.text(0,0,'Hello').attr({opacity:0}).hide();
	chartDiv.style.height=height+'px';
	
	
	for(i=0;i<moves.length;i++)
	{
		var path=paper.path({stroke:"#f00"});
		var page=paper.set();
		var tmp=moves[i].url,
			hint=moves[i].url;
		if(moves[i].url.length>maxLetters)
		{
			tmp=moves[i].url.substring(0,maxLetters);
			if(moves[i].url.length>hintStringLetters)
			{
				var lines=Math.round(moves[i].url.length/hintStringLetters);
				hint='';
				for(var j=0;j<lines;j++)
				{
					hint+=moves[i].url.substring(j*hintStringLetters,(j+1)*hintStringLetters)+'\n';
				}
			}
		}

		var txt=paper.text(0,0,hint),
			bbox=txt.getBBox();
		var rect=paper.rect(bbox.x-boxPadding,bbox.y-boxPadding,bbox.width+boxPadding*2,bbox.height+boxPadding*2,3);
		txt.data=moves[i];
		txt.mouseover(function () {
				var x=this.attrs.x,
					y=this.attrs.y+this.getBBox().height;
				//Обрабатываем время на странице
				var date=new Date();
				date.setTime(this.data.length*1000);
				if(this.data.length>30000)
				{
					hinttext.attr({text:'Последний раз пользователь был:\n'+date.toString()});
				}
				else
				{
					var timeText='';
					var hours=Math.floor(this.data.length/3600);
					var minutes=Math.floor((this.data.length%3600)/60);
					var seconds=(this.data.length%3600)%60;
					if(hours>0) timeText+=hours+' час. ';
					if(minutes>0) timeText+=minutes+' мин. ';
					if(seconds>0) timeText+=seconds+' сек. ';
					hinttext.attr({text:'Находился на странице:\n'+timeText});
				}
				var size=hinttext.getBBox();
				hinttext.show().animate({opacity:1},500);
                hintbox.attr({x:x-boxPadding,y:y-boxPadding,width:size.width+boxPadding*2,height:size.height+boxPadding*2}).show().toFront().animate({opacity:1},500);
                hinttext.attr({x:x+size.width/2,y:y+size.height/2}).toFront();
            }).mouseout(function () {
            	//hintbox.hide();
            	//hinttext.hide();
            	hintbox.animate({opacity:0},500);
            	hinttext.animate({opacity:0},500); 
            });
		page.push(rect);
		page.push(txt);
		var wSize=rect.getBBox();
		if(Y!=OLDY)
		{
			Y=Y+DY;
			path.moveTo(OLDX,OLDY);
			X=OLDX+DX;
			path.qcurveTo(OLDX,Y,X-sign(DX)*wSize.width/2,Y);
			OLDY=Y;
			OLDX=X+sign(DX)*wSize.width/2;
		}
		else
		{
			X=OLDX+DX+sign(DX)*wSize.width/2;
			Y=OLDY;
			path.moveTo(OLDX,OLDY);
			OLDY=Y;
			OLDX=X+sign(DX)*wSize.width/2;
		}
		if((X-wSize.width/2)<paddingLeft || X+wSize.width/2>paper.width-paddingRight)
		{
			if((X-wSize.width/2)>paddingLeft)
			{
				X=paper.width-paddingRight-wSize.width/2;
			}
			else
			{
				X=paddingLeft+wSize.width/2;
			}
			DX=-DX;
			Y=Y+DY;
			path.qcurveTo(X,OLDY,X,Y-wSize.height/2);
			OLDY=Y+wSize.height/2;
			OLDX=X;
		}
		else
		{
			path.lineTo(X-sign(DX)*wSize.width/2,Y);
		}
		if(moves[i].length>30000) path.attr({"stroke":"#0f0","stroke-dasharray":"-"});
		path.attr({"stroke-width":Math.ceil(moves[i].hits/10)});
		page.translate(X,Y);
		chart.push(page);
	}
	chartDiv.style.height=OLDY+100+'px';
	paper.setSize(width,OLDY+100);
	back.attr({height:OLDY+100});
}

Raphael.fn.connection = function (obj1, obj2, line, bg) {
    if (obj1.line && obj1.from && obj1.to) {
        line = obj1;
        obj1 = line.from;
        obj2 = line.to;
    }
    var bb1 = obj1.getBBox();
    var bb2 = obj2.getBBox();
    var p = [{x: bb1.x + bb1.width / 2, y: bb1.y - 1},
        {x: bb1.x + bb1.width / 2, y: bb1.y + bb1.height + 1},
        {x: bb1.x - 1, y: bb1.y + bb1.height / 2},
        {x: bb1.x + bb1.width + 1, y: bb1.y + bb1.height / 2},
        {x: bb2.x + bb2.width / 2, y: bb2.y - 1},
        {x: bb2.x + bb2.width / 2, y: bb2.y + bb2.height + 1},
        {x: bb2.x - 1, y: bb2.y + bb2.height / 2},
        {x: bb2.x + bb2.width + 1, y: bb2.y + bb2.height / 2}];
    var d = {}, dis = [];
    for (var i = 0; i < 4; i++) {
        for (var j = 4; j < 8; j++) {
            var dx = Math.abs(p[i].x - p[j].x),
                dy = Math.abs(p[i].y - p[j].y);
            if ((i == j - 4) || (((i != 3 && j != 6) || p[i].x < p[j].x) && ((i != 2 && j != 7) || p[i].x > p[j].x) && ((i != 0 && j != 5) || p[i].y > p[j].y) && ((i != 1 && j != 4) || p[i].y < p[j].y))) {
                dis.push(dx + dy);
                d[dis[dis.length - 1]] = [i, j];
            }
        }
    }
    if (dis.length == 0) {
        var res = [0, 4];
    } else {
        var res = d[Math.min.apply(Math, dis)];
    }
    var x1 = p[res[0]].x,
        y1 = p[res[0]].y,
        x4 = p[res[1]].x,
        y4 = p[res[1]].y,
        dx = Math.max(Math.abs(x1 - x4) / 2, 10),
        dy = Math.max(Math.abs(y1 - y4) / 2, 10),
        x2 = [x1, x1, x1 - dx, x1 + dx][res[0]].toFixed(3),
        y2 = [y1 - dy, y1 + dy, y1, y1][res[0]].toFixed(3),
        x3 = [0, 0, 0, 0, x4, x4, x4 - dx, x4 + dx][res[1]].toFixed(3),
        y3 = [0, 0, 0, 0, y1 + dy, y1 - dy, y4, y4][res[1]].toFixed(3);
    var path = ["M", x1.toFixed(3), y1.toFixed(3), "C", x2, y2, x3, y3, x4.toFixed(3), y4.toFixed(3)].join(",");
    if (line && line.line) {
        line.bg && line.bg.attr({path: path});
        line.line.attr({path: path});
    } else {
        var color = typeof line == "string" ? line : "#000";
        return {
            bg: bg && bg.split && this.path({stroke: bg.split("|")[0], fill: "none", "stroke-width": bg.split("|")[1] || 3}, path),
            line: this.path({stroke: color, fill: "none"}, path),
            from: obj1,
            to: obj2
        };
    }
};
