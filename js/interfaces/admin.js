ksGUI.prototype=new Object();
function ksGUI(){};

ksGUI.prototype.parse=function(obGui,obj)
{
	var type,newobj;
	if(typeof(obGui)=='object')
	{
		for(ii in obGui)
		{
			if(obGui[ii]&&obGui[ii].type)
			{
				type=obGui[ii].type;
				if(type=='table')
				{
					newobj=this.createTable(ii, obGui[ii]);
				}
				if(type=='label')
				{
					newobj=this.createLabel(ii,obGui[ii]);
				}
				if(type=='text')
				{
					newobj=this.createText(ii,obGui[ii]);
				}
				if(type=='select')
				{
					newobj=this.createSelect(ii,obGui[ii]);
				}
				if(type=='checkbox') newobj=this.createCheckbox(ii,obGui[ii]); 
				obj.append(newobj);
			}
		}
	}
	return obj;
};

ksGUI.prototype.createLabel=function(id,params)
{
	return $('<span>'+params.message+'</span>').attr('id',id);
};

ksGUI.prototype.createText=function(id,params)
{
	return $('<input type="text">').attr('id',id).attr('name','OS_'+id).val(params.value).addClass('form_input').css('width',"100%");
};

ksGUI.prototype.createCheckbox=function(id,params)
{
	return $('<input type="checkbox">').attr('id',id).attr('name','OS_'+id).val(params.value).attr('checked',params.checked);
};

ksGUI.prototype.createSelect=function(id,params)
{
	var ob=$('<select>').attr('name','OS_'+id).addClass('form_input');
	if(params.values)
	{
		for(var i=0;i<params.values.length;i++)
		{
			ob.append($('<option value="'+params.values[i].id+'">').attr('selected',params.values[i].id==params.value).html(params.values[i].title));
		}
	}
	return ob;
};

ksGUI.prototype.createTable=function(id,params)
{
	var res,tr,td,row;
	res=$('<table class="layout">').attr('id',id);
	if(typeof(params.children)=='object')
	{
		for(ii in params.children)
		{
			row={};
			tr=$('<tr></tr>');
			td=$('<td width="30%"></td>');
			tr.append(td);
			if(params.children[ii].title)
			{
				td.html(params.children[ii].title);
			}
			td=$('<td width="70%"></td>');
			tr.append(td);
			row[ii]=params.children[ii];
			this.parse(row, td);
			res.append(tr);
		}
	}
	return res;
};

var GUI=new ksGUI;
