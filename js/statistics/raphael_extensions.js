Raphael.fn.ks={
	"Arc":function(cx,cy,sr,lr,angleFrom,angleTo,params)
	{
		if(angleFrom==0) angleFrom=0.000001;
		var sAngle=-Math.PI/180*angleFrom,
			eAngle=-Math.PI/180*angleTo,
			x1= cx + (sr) * Math.cos(sAngle),
			y1= cy + (sr) * Math.sin(sAngle),
			x2=cx + (lr) * Math.cos(sAngle),
			y2=cy + (lr) * Math.sin(sAngle),
			delta1=angleTo-angleFrom,
			x3=cx + (lr) * Math.cos(eAngle),
			y3=cy + (lr) * Math.sin(eAngle),
			x4=cx + (sr) * Math.cos(eAngle),
			y4=cy + (sr) * Math.sin(eAngle);
		if(Math.abs(angleTo-angleFrom)<1) return this.path(params, ["M", x1, y1,"L",x2,y2,"L",x3,y3,"L",x4,y4,"L",x1,y1]);
		return this.path(params, ["M", x1, y1,"L",x2,y2,"A", lr, lr, 0, +(delta1>180), 0, x3, y3,"L",x4,y4,"A",sr,sr,0,+(delta1>180),1,x1,y1]);
	},
	"sector":function(cx, cy, r, startAngle, endAngle, params) 
    {
		var rad=Math.PI/180;
    	if(startAngle==endAngle) startAngle-=1;
    	if(startAngle<0) startAngle+=360;
    	if(startAngle>=360)
    	{
    		startAngle=startAngle%360;
    	}
    	if(endAngle>=360)
    	{
    		endAngle=endAngle%360;
    	}
    	if(startAngle==endAngle)
    	{
    		endAngle-=1;
    	}
    	if(endAngle<0)
    	{
    		endAngle+=360;
    	}
        var x1 = cx + r * Math.cos(-startAngle * rad),
            x2 = cx + r * Math.cos(-endAngle * rad),
            y1 = cy + r * Math.sin(-startAngle * rad),
            y2 = cy + r * Math.sin(-endAngle * rad);
        if(Math.abs(endAngle-startAngle)<1) return this.path(params, ["M", cx, cy,"L",x2,y2]);
        return this.path(params, ["M", cx, cy, "L", x1, y1, "A", r, r, 0, +(endAngle - startAngle > 180), 0, x2, y2, "z"]);
    },
    "grid":function(x,y,x1,y1,dx,dy, paramsx,paramsy)
    {
    	if(!paramsy) paramsy=paramsx;
    	var res=this.path(paramsx);
    	var sx=(x1-x)/dx;
    	var sy=(y1-y)/dy;
    	for(var i=0;i<=dx;i++)
    	{
    		res.moveTo(x+sx*i,y).lineTo(x+sx*i,y1);
    	}
    	var resy=this.path(paramsy);
    	for(var j=0;j<=dy;j++)
    	{
    		res.moveTo(x,y+sy*j).lineTo(x1,y+sy*j);
    	}
    	return {'x':res,'y':resy};
    },
    "getAngle":function(x,y,ox,oy,nx,ny)
    {
    	var dy=ny-y,
    		dx=nx-x,
    		angle,
    		beta,
    		alpha;
 	    if((dx*dx+dy*dy)==0)
 	    	beta=0;
 	    else
 	        beta=Math.asin(dy/Math.sqrt(dx*dx+dy*dy))
 	    dy=y-oy;
 	    dx=x-ox;
 	    if((dx*dx+dy*dy)==0)
 	    	alpha=0;
 	    else
 	    	alpha=Math.asin(dy/Math.sqrt(dx*dx+dy*dy))
 	    angle1=(Math.PI+beta+alpha)/2;
 	    angle=Math.PI+beta-alpha;
 	    return {'ab':angle,'ob':angle1};
    }
}