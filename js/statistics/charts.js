/**
 * @fileSource chart.js
 * В этом файле находятся функции выполняющие построение графиков 
 */
function drawChart(labels,rows,chartDiv,params)
{
	if(rows.length==0) return false;
	var max=0;
	for(i=0;i<rows.length;i++)
	{
		if(rows[i].max>max) max=rows[i].max;
	}
	// Draw
	var width = params.width?params.width:(chartDiv.offsetWidth-20),
	    height = params.height?params.height:400,
	    paddingLeft = params.paddingLeft?params.paddingLeft:60,
	    paddingRight = params.paddingRight?params.paddingRight:60,
	    paddingTop = params.paddingRight?params.paddingRight:60,
	    paddingBottom = params.paddingRight?params.paddingRight:60,
	    gridX=params.gridX?params.gridX:5;
	    gridY=params.gridY?params.gridY:10;
	    color2= "#54a8df",
	    color1="#5aba7f",
	    color= "#f95311",
	    lineWidth=params.lineWidth?params.lineWidth:4,
	    r = Raphael("chart", width+20, height),
	    txt = {font: '12px Fontin-Sans, Arial', fill: "#000"},
	    gridStyle={"stroke-width":1,"stroke":"#e5e5e5","stroke-dasharray":"- "},
	    axesStyle={font: '11px Tahoma, Arial', fill: "#aaaaaa"},
	    X = (width - paddingLeft-paddingRight) / (labels.length-1),
	    Y = (height - paddingBottom - paddingTop) / max;
	var path_a = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter-Y*data[0]), 
		path = r.path({stroke: color, "stroke-width": lineWidth,"opacity":1}).moveTo(leftgutter,height-bottomgutter-Y*data[0]),
	    bgp = r.path({stroke: "none", opacity: .3, fill: color}).moveTo(leftgutter, height - bottomgutter),
	    path_a1 = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter-Y*data1[0]),
	    path1 = r.path({stroke: color1, "stroke-width": 4}).moveTo(leftgutter,height-bottomgutter-Y*data1[0]),
	    bgp1 = r.path({stroke: "none", opacity: .3, fill: color1}).moveTo(leftgutter, height - bottomgutter),
	    path_a2 = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter-Y*data2[0]),
	    path2 = r.path({stroke: color2, "stroke-width": 4}).moveTo(leftgutter,height-bottomgutter-Y*data2[0]),
	    bgp2 = r.path({stroke: "none", opacity: .3, fill: color2}).moveTo(leftgutter, height - bottomgutter),
	    
	    frame = r.rect(10, 10, 100, 60, 2).attr({fill: "#fff6c4", stroke: "#f6db9a", "stroke-width": 1}).hide(),
	    label = [],
	    is_label_visible = false,
	    leave_timer,
	    blanket = r.set();
	label[0] = r.text(60, 10, "users\n24 hits").attr(txt1).hide();
	label[1] = r.text(60, 50, "22 September 2008").attr(txt1).attr({fill: "#aaaaaa"}).hide();
	dot = r.circle(60, 10, 7).attr({fill: color, stroke: "#000", opacity:0});
	//рисуем сетку по X
	var axesY=r.path({"stroke-width":1,"stroke":"#e5e5e5"}).moveTo(leftgutter,topgutter).lineTo(leftgutter,height-bottomgutter+20);
	r.text(leftgutter+30, height-bottomgutter+15, labels[0]).attr(axesStyle).toBack();
	var rgridX=r.path(gridStyle),sx=(width-rightgutter-leftgutter)/gridX,sy=(height-bottomgutter-topgutter)/gridY;
    for(var i=1;i<=gridX;i++)	
    {
    	rgridX.moveTo(leftgutter+sx*i,topgutter).lineTo(leftgutter+sx*i,height-bottomgutter);
    	axesY.moveTo(leftgutter+sx*i,height-bottomgutter).lineTo(leftgutter+sx*i,height-bottomgutter+20);
    	if(i!=gridX) r.text(leftgutter+sx*i+30, height-bottomgutter+15, labels[Math.min(Math.ceil(labels.length/gridX*i),labels.length-1)]).attr(axesStyle).toBack();
    }
    var rgridY=r.path(gridStyle);
    for(var j=0;j<=gridY;j++)
    {
    	rgridY.moveTo(leftgutter,topgutter+sy*j).lineTo(width-rightgutter,topgutter+sy*j);
    	if(j!=gridY) r.text(leftgutter/2,topgutter+sy*j,Math.round(max/gridY*(gridY-j))).attr(axesStyle);
    }
    for (var i = 0, ii = labels.length; i < ii; i++) 
	{
		var y = Math.round(height - bottomgutter - Y * data[i]),
	    	x = leftgutter + X*i;
	    bgp.lineTo(x, y);
	    path_a.lineTo(x,y);
		path.lineTo(x,y);
		r.circle(x,y,4).attr({"stroke-width":2,"stroke":"#fff","fill":color});
		
		blanket.push(r.rect(x-X/2, y-10, X, 20).attr({stroke: "none", fill: "#f00", opacity: 0}));
		var rect = blanket[blanket.length - 1];
		(function (x, y, data, lbl, dot) {
	        var timer, i = 0;
	        $(rect.node).hover(function () {
	            clearTimeout(leave_timer);
	            var newcoord = {x: +x + 7.5, y: y - 19};
	            if (newcoord.x + 100 > width) {
	                newcoord.x -= 114;
	            }
	            frame.show().animate({x: newcoord.x, y: newcoord.y}, 200 * is_label_visible);
	            label[0].attr({text: "Посетители:\n"+data + " хит" + ((data % 10 == 1) ? "" : "ов")}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 20}, 200 * is_label_visible);
	            label[1].attr({text: lbl}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 45}, 200 * is_label_visible);
	            dot.show().attr({opacity:1,cx: x, cy: y, fill:color});
	            is_label_visible = true;
	            r.safari();
	        }, function () {
	            r.safari();
	            leave_timer = setTimeout(function () {
	                frame.hide();
	                label[0].hide();
	                label[1].hide();
	                dot.hide();
	                is_label_visible = false;
	                r.safari();
	            }, 1);
	        });
	    })(x, y, data[i], labels[i], dot);
		
		var y = Math.round(height - bottomgutter - Y * data2[i]),
	    	x = leftgutter + X*i;
	   	bgp2.lineTo(x, y);
		path2.lineTo(x, y);
		path_a2.lineTo(x,y);
		r.circle(x,y,4).attr({"stroke-width":2,"stroke":"#fff","fill":color2});
		blanket.push(r.rect(x-X/2, y-10, X, 20).attr({stroke: "none", fill: "#f00", opacity: 0}));
		var rect = blanket[blanket.length - 1];
		(function (x, y, data, lbl, dot) {
            var timer, i = 0;
            $(rect.node).hover(function () {
                clearTimeout(leave_timer);
                var newcoord = {x: +x + 7.5, y: y - 19};
                if (newcoord.x + 100 > width) {
                    newcoord.x -= 114;
                }
                frame.show().animate({x: newcoord.x, y: newcoord.y}, 200 * is_label_visible);
                label[0].attr({text: "Посетители:\n"+data + " хост" + ((data % 10 == 1) ? "" : "ов")}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 20}, 200 * is_label_visible);
                label[1].attr({text: lbl}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 45}, 200 * is_label_visible);
                dot.show().attr({opacity:1,cx: x, cy: y,fill:color2});
                is_label_visible = true;
                r.safari();
            }, function () {
                r.safari();
                leave_timer = setTimeout(function () {
                    frame.hide();
                    label[0].hide();
                    label[1].hide();
                    dot.hide();
                    is_label_visible = false;
                    r.safari();
                }, 1);
            });
        })(x, y, data2[i], labels[i], dot);
        
        //Третий график
       	var y = Math.round(height - bottomgutter - Y * data1[i]),
	    	x = leftgutter + X*i;
	   	bgp1.lineTo(x, y);
		path1.lineTo(x, y);
		path_a1.lineTo(x,y);
		r.circle(x,y,4).attr({"stroke-width":2,"stroke":"#fff","fill":color1});
		blanket.push(r.rect(x-X/2, y-10, X, 20).attr({stroke: "none", fill: "#f00", opacity: 0}));
		var rect = blanket[blanket.length - 1];
		(function (x, y, data, lbl, dot) {
            var timer, i = 0;
            $(rect.node).hover(function () {
                clearTimeout(leave_timer);
                var newcoord = {x: +x + 7.5, y: y - 19};
                if (newcoord.x + 100 > width) {
                    newcoord.x -= 114;
                }
                frame.show().animate({x: newcoord.x, y: newcoord.y}, 200 * is_label_visible);
                label[0].attr({text: "Роботы:\n"+data + " хит" + ((data % 10 == 1) ? "" : "ов")}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 20}, 200 * is_label_visible);
                label[1].attr({text: lbl}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 45}, 200 * is_label_visible);
                dot.show().attr({opacity:1,cx: x, cy: y,fill:color1});
                is_label_visible = true;
                r.safari();
            }, function () {
                r.safari();
                leave_timer = setTimeout(function () {
                    frame.hide();
                    label[0].hide();
                    label[1].hide();
                    dot.hide();
                    is_label_visible = false;
                    r.safari();
                }, 1);
            });
        })(x, y, data1[i], labels[i], dot);
	}
	
	bgp1.lineTo(x, height - bottomgutter).andClose().toBack();
	bgp2.lineTo(x, height - bottomgutter).andClose().toBack();
	bgp.lineTo(x, height - bottomgutter).andClose().toBack();
	rgridY.toBack();
    rgridX.toBack();
	frame.toFront();
	label[0].toFront();
	label[1].toFront();
	blanket.toFront();
	
}