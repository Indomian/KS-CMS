function DrawDropDownList(data,elemId,replace)
{
	var arUsers=JSON.parse(data);
	if(arUsers.length>0)
	{
		var obInput=document.getElementById(elemId);
		if(!obInput) return;
		var obDiv=obInput.parentNode.lastChild;
		if(obDiv.tagName!='DIV')
		{
			obDiv=document.createElement('div');
			obDiv.style.position='absolute';
			obDiv.style.zIndex=1000;
			obDiv.className='dropDown';
			obDiv=obInput.parentNode.appendChild(obDiv);
		}
		obDiv.innerHTML='';
		var obA;
		for(var i=0;i<arUsers.length;i++)
		{
			obA=document.createElement('A');
			obA.innerHTML=arUsers[i].name;
			obA.href='#';
			obA.elemId=elemId;
			if(!replace)
			{
				obA.onclick=function()
				{
					var sName=document.getElementById(this.elemId).value;
					var arNames=sName.split(',');
					if(arNames.length>1)
					{
						var arResNames=new Array();
						for(var i=0;i<arNames.length-1;i++)
						{
							if (window.RegExp) 
							{
			    				var regexp = new RegExp("[ ,;]", "g");
			    				arResNames[i] = arNames[i].replace(regexp, "");
			  				}
						}
						sName=arResNames.join(', ')+', '+this.innerHTML;
					}
					else
					{
						sName=this.innerHTML;
					}
					document.getElementById(this.elemId).value=sName+', ';
					this.parentNode.style.display='none';
					return false;
				}
			}
			else
			{
				obA.onclick=function()
				{
					sName=this.innerHTML;
					document.getElementById(this.elemId).value=sName;
					this.parentNode.style.display='none';
					return false;
				}
			}
			obDiv.appendChild(obA);
		}
		obA=document.createElement('A');
		obA.innerHTML="Закрыть";
		obA.href='#';
		obA.onclick=function(){this.parentNode.style.display='none';return false;}
		obDiv.appendChild(obA);
		obDiv.style.display='block';
	}
}