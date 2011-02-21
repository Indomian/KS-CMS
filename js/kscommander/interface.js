var ksCommander={
	"path":"/uploads/",
	"totalSelected":0,
	"Mode":'',
	"tree":null,
	"loading":null,
	"treeNode":null,
	"isAjax":0,
	"page":1,
	"visible":10,
	"order":"name",
	"selected":null,
	"selectedPath":null,
	"selectedTotal":0,
	"dir":"asc",
	"arFilesList":{},
	"imagePath":"/uploads/templates/admin/images",
	/**
	 * Метод отображает список папок по переданным данным
	 */
	"ShowSubDirs":function (data)
	{
		if(ksCommander.treeNode!=null)
		{
			var obUL=document.createElement('UL');
			obUL=ksCommander.treeNode.object.appendChild(obUL);
			if(data.length>1)
			{
				ksCommander.treeNode.children=new Array();
				ksCommander.treeNode.object.childNodes[1].src="/uploads/templates/admin/images/icons2/folder_open.gif";
				for(var i=1;i<data.length;i++)
				{
					var obLI=document.createElement('LI');
					obLI=obUL.appendChild(obLI);
					var obImg=document.createElement('IMG');
					obImg.src=ksCommander.imagePath+"/icons_menu/plus.gif";
					obImg.alt="plus";
					obImg.width=13;
					obImg.height=13;
					obImg=obLI.appendChild(obImg);
					obImg.ksFullPath=data[i].Full_path
					var obImg1=document.createElement('IMG');
					obImg1=obLI.appendChild(obImg1);
					if(data[i].Type=='dir')
					{
						obImg1.src=ksCommander.imagePath+"/icons2/folder.gif";
					}
					else
					{
						if(data[i].Type=='zip')
						{
							obImg1.src=ksCommander.imagePath+"/icons2/zip_folder.gif";
						}
					}
					obImg1.width=20;
					obImg1.height=20;
					obImg1.alt="folder";
					var obA=document.createElement('A');
					obA=obLI.appendChild(obA);
					obA.innerHTML=data[i].Name;
					obA.href="/admin.php?module=kscommander&CKS_path="+data[i].Full_path;
					obA.ksFullPath=data[i].Full_path;
					$(obA).click(function(){ksCommander.LoadFiles(this.ksFullPath,"")});
					ksCommander.treeNode.children.push({
							"name":data[i].Name,
							"object":obLI,
							"children":null
					});
					$(obImg).click(function(){ksCommander.LoadSubDirs(this.ksFullPath,ksCommander.SearchTreeLeaf(this.ksFullPath));});
				}
			}
			else
			{
				ksCommander.treeNode.object.childNodes[0].style.visibility='hidden';
			}
		}
		ksCommander.HideLoading();
	},
	"LoadSubDirs":function (path,node,canNotClose)
	{
		if(node&& node.children!=null)
		{
			if(node.object.lastChild.style.display=="")
			{
				if(!canNotClose)
				{
					node.object.lastChild.style.display="none";
					node.object.firstChild.src="/uploads/templates/admin/images/icons_menu/plus.gif";
					node.object.childNodes[1].src="/uploads/templates/admin/images/icons2/folder.gif";
				}
			}
			else
			{
				node.object.lastChild.style.display="";
				node.object.firstChild.src="/uploads/templates/admin/images/icons_menu/minus.gif";
				node.object.childNodes[1].src="/uploads/templates/admin/images/icons2/folder_open.gif";

			}
			return false;
		}
		

		this.treeNode=node;

		//Если такая папка не найдена то надо скачать её контент
		$.get("/admin.php",{module:"kscommander",mode:"ajax",page:"getdirs","path":path},ksCommander.ShowSubDirs,"json");
		this.ShowLoading();
		return false;
	},
	"GetSubItems":function(node)
	{
		var arItems=new Array();
		var arItem;
		if(node.tagName=='LI')
		{
			for(var i=0;i<node.childNodes.length;i++)
			{
				if(node.childNodes[i].tagName=='UL')
				{
					node=node.childNodes[i];
					break;
				}
			}
		}
		if(node.tagName!='UL') return null;
		if(node.childNodes.length>0)
		{
			for(var i=0;i<node.childNodes.length;i++)
			{
				if(node.childNodes[i].tagName=='LI')
				{	
					//Это действительно элемент дерева
					arItem={
							"name":node.childNodes[i].childNodes[2].innerHTML,
							"object":node.childNodes[i],
							"children":this.GetSubItems(node.childNodes[i])
					};
					arItems.push(arItem);
				}
			}
		}
		return arItems;
	},
	"SearchTreeLeaf":function (path)
	{
		var res=null;
		//Разбиваем путь на элементы
		arPath=new Array();
		var arPathOld=path.split('/');
		strRes=new String();
		arPathOld.splice(1,1);
		for(i=0;i<arPathOld.length;i++)
		{
			if(arPathOld[i]!='') arPath.push(arPathOld[i]);
		}
		//Ищем корень списка дерева
		if(this.tree==null)
		{
			var obDiv=document.getElementById('dirTree');
			var obUl;
			for(i=0;i<obDiv.childNodes.length;i++)
			{
				if(obDiv.childNodes[i].tagName=='UL')
				{	
					obUl=obDiv.childNodes[i];
					break;
				}
			}
			this.tree=this.GetSubItems(obUl);
		}
		if(this.tree)
		{
			//Начинаем сверять первые элементы пути с элементами списка
			obUl=this.tree;
			var j=0;
			var found=true;
			while(obUl&&(j<arPath.length)&&found)
			{
				found=false;
				for(var i=0;i<obUl.length;i++)
				{
					if((arPath[j]!='undefined')&&(obUl[i].name==arPath[j]))
					{
						res=obUl[i];
						found=true;
						if(j<arPath.length)
						{
							j++;
							if(obUl[i].children!=null)
							{
								obUl=obUl[i].children;
							}
							else
							{
								obUl=null;
								j=arPath.length+1;
							}
							break;
						}
					}
				}
			}
		}
		return res;
	},
	"ShowLoading":function ()
	{
		if(this.loading==null)
		{
			var oFrame=document.createElement('div');
			oFrame.id='fixme';
			oFrame.className='loadingBar';
			oFrame.innerHTML='<img src="/uploads/templates/admin/images/loading.gif" border="0"/> Обновление';
			oFrame.style.display='block';
			document.body.appendChild(oFrame);
			this.loading=oFrame;
		}
	},
	"HideLoading":function ()
	{
		if (ksCommander.loading!=null)
		{
			ksCommander.loading.style.display='none';
			document.body.removeChild(ksCommander.loading);
			ksCommander.loading=null;
		}
	},
	"SelectRow":function (row)
	{
		if(!row.cells[0].firstChild) return;
		if(row.className=='odd')
		{
			row.className='hl2';
			row.cells[0].firstChild.checked=true;
		}
		else
		{
			if(row.className=='')
			{
				row.className='hl';
				row.cells[0].firstChild.checked=true;
			}
			else
			{
				if(row.className=='hl')
				{
					row.className='';
					row.cells[0].firstChild.checked=false;
				}
				else
				{
					if(row.className=='hl2')
					{
						row.className='odd';
						row.cells[0].firstChild.checked=false;
					}
				}
			}
		}
		this.fileChecked(row.cells[0].firstChild);
	},
	"selectAll":function (node)
	{
		var form=document.getElementById('fileSelector');
		for(i=0;i<form.elements.length;i++)
		{
			if(form.elements[i].type=='checkbox')
			{
				form.elements[i].checked=node.checked;
				this.SelectRow(form.elements[i].parentNode.parentNode);
			}
		}
	},
	"fileChecked":function(node)
	{
		if(ksCommander.totalSelected<1) ksCommander.totalSelected=0;
		if(node.checked)
			ksCommander.totalSelected++;
		else
			ksCommander.totalSelected--;
		$('#selectedCount').html('Всего выбрано: '+ksCommander.totalSelected);
		if(ksCommander.totalSelected>0)
			$('#cutButton,#copyButton,#renameButton,#deleteButton').removeClass('inactive');
		else
			$('#cutButton,#copyButton,#renameButton,#deleteButton').addClass('inactive');
		if(ksCommander.totalSelected==1)
			$('#downloadButton').removeClass('inactive');
		else
			$('#downloadButton').addClass('inactive');
	},
	"ShowPreview":function(data)
	{
		if(data&&data.href)
		{
			$('#filePreview >.logo_cont_name > #preview_name').empty().append(data.name.substring(0,20));
			$('#imgcont').empty();
			// загружаем изображение
			var prevImg = document.createElement('img');
			prevImg.style.visibility='hidden';
			prevImg.style.position='absolute';
			prevImg.onload=function()
			{
				prevImg=document.getElementById('imgcont').lastChild;
				w = prevImg.width;
				h = prevImg.height;
				if(w>h)
				{
					if(w>187)
					{
						scale=187/w;
						w=187;
						h=Math.round(h*scale);
						prevImg.style.width=w+'px';
						prevImg.style.height=h+'px';
					}
				} 	
				else
				{
					if(h>150)
					{
						scale=150/h;
						h=150;
						w=Math.round(w*scale);
						prevImg.style.width=w+'px';
						prevImg.style.height=h+'px';
					}
				}
				prevImg.style.visibility='visible';
				prevImg.style.position='static';
			}
			prevImg.src = data.href;
			var obTable=$('#filePreview > .logo_cont_table').get(0);
			obTable.rows[0].cells[1].innerHTML=data.type;
			obTable.rows[1].cells[1].innerHTML=data.SizeText;
			var mydate=new Date();
			mydate.setTime(data.date*1000);
			obTable.rows[2].cells[1].innerHTML=mydate.getHours()+':'+mydate.getSeconds()+' '+mydate.getDate()+'.'+mydate.getMonth()+'.'+mydate.getFullYear();
			document.getElementById('imgcont').appendChild(prevImg);
		}
		ksCommander.HideLoading();
	},
	"LoadPreview":function (path)
	{
		$.get("/admin.php",{module:"kscommander",mode:"ajax",page:"getpreview","path":path},ksCommander.ShowPreview,"json");
		this.ShowLoading();
		return false;
	},
	"ShowNavChain":function(data)
	{
		var obNavChain=document.getElementById('navChain');
		if(data && obNavChain && (data.length>0))
		{
			obNavChain.innerHTML="";
			//obNavChain.appendChild(document.createTextNode(''));
			for(var i=0;i<data.length;i++)
			{
				
				var obA=document.createElement('A');
				obA.href="/admin.php?module=kscommander&CKS_path="+data[i].Full_path;
				obA.ksFullPath=data[i].Full_path;
				$(obA).click(function(event){ksCommander.LoadFiles(this.ksFullPath);event.preventDefault()});
				if(i==0)
				{
				  obA.innerHTML='<img src="/uploads/templates/admin/images/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;';
				}
				else
				{  
				  obA.innerHTML=data[i].Name;
				}
				obNavChain.appendChild(obA);
				obNavChain.appendChild(document.createTextNode('/'))
			}
		}
	},
	"UpdateNavChain":function()
	{
		//Если такая папка не найдена то надо скачать её контент
		$.get("/admin.php",{module:"kscommander",mode:"ajax",page:"getnavchain","path":this.path},ksCommander.ShowNavChain,"json");
		this.ShowLoading();
		return false;
	},
	"ChangeTotal":function(value)
	{
		var obSelect=document.getElementById('show_num1');
		for(var i=0;i<obSelect.childNodes.length;i++)
		{
			if(obSelect.childNodes[i].tagName=='OPTION')
			{
				if(obSelect.childNodes[i].value==value)
				{
					obSelect.value=value;
					break;
				}
			}
		}
		return false;
	},
	"GetCurUrl":function(add)
	{
		return "/admin.php?module=kscommander&CKS_path="+this.path+"&"+add;
	},
	"GeneratePages":function(pages)
	{
		if($('#pagesBar').children().length>3)
		{
			$('#pagesBar').children().toggle().remove('.manage');
			
		}
		var obDiv=$('#pagesBar>.pages_nav');
		if(obDiv)
		{
			obDiv.empty();
			obDiv.append('Страница: ');
			if(pages.active==1)
			{
				obDiv.append('<a><img src="'+this.imagePath+'/icons2/first_disabled.gif" alt="&lt;" height="9" width="8"></a><a><img src="'+this.imagePath+'/icons2/previous_disabled.gif" alt="&lt;" height="9" width="8"></a>');
			}
			else
			{
				var obA=document.createElement('A');
				obA.href=this.GetCurUrl('p1=1');
				$(obA).click(function(event){ksCommander.page=1;return ksCommander.LoadFiles(ksCommander.path)}).append('<img src="'+this.imagePath+'/icons2/first.gif" alt="&lt;" height="9" width="8">');
				obDiv.append(obA);
				obA=document.createElement('A');
				obA.href=this.GetCurUrl('p1='+(ksCommander.page-1));
				$(obA).click(function(event){ksCommander.page-=1;return ksCommander.LoadFiles(ksCommander.path)}).append('<img src="'+this.imagePath+'/icons2/previous.gif" alt="&lt;" height="9" width="8">');
				obDiv.append(obA);
			}
			for(ii in pages.pages)
			{	
				if ((ii>=(pages.active-3)) && (ii<=(pages.active + 3)))
				{
					if(ii==ksCommander.page)
					{
						obDiv.append('<span>'+ii+'</span>');
					}
					else
					{
						var obA=document.createElement('A');
						obA.href=this.GetCurUrl('p1='+ii);
						obA.ksIndex=parseInt(ii);
						$(obA).click(function(event){ksCommander.page=this.ksIndex;return ksCommander.LoadFiles(ksCommander.path)}).append(ii);
						obDiv.append(obA);
					}
				}
			}
			if(pages.active<pages.num)
			{
				var obA=document.createElement('A');
				obA.href=this.GetCurUrl('p1='+(ksCommander.page+1));
				$(obA).click(function(event){ksCommander.page+=1;return ksCommander.LoadFiles(ksCommander.path)}).append('<img src="'+this.imagePath+'/icons2/next.gif" alt="&lt;" height="9" width="8">');
				obDiv.append(obA);
				obA=document.createElement('A');
				obA.href=this.GetCurUrl('p1='+(pages.num));
				obA.ksIndex=pages.num;
				$(obA).click(function(event){ksCommander.page=this.ksIndex;return ksCommander.LoadFiles(ksCommander.path)}).append('<img src="'+this.imagePath+'/icons2/last.gif" alt="&lt;" height="9" width="8">');
				obDiv.append(obA);
			}
			else
			{
				obDiv.append('<a><img src="'+this.imagePath+'/icons2/next_disabled.gif" alt="&lt;" height="9" width="8"></a><a><img src="'+this.imagePath+'/icons2/last_disabled.gif" alt="&lt;" height="9" width="8"></a>');
			}
		}
	},
	"ShowFiles":function(mydata)
	{
		var obDiv=document.getElementById('fileList');
		obDiv.innerHTML='';
		if(!mydata){ksCommander.HideLoading(); return;}
		data=mydata.list;
		ksCommander.arFilesList={};
		if(data.length>0)
		{
			var obTable=document.createElement('TABLE');
			obTable.className="layout";
			var obBody=document.createElement('TBODY');
			obBody=obTable.appendChild(obBody);
			for(var i=0;i<data.length;i++)
			{
				var obRow=document.createElement('TR');
				if(i%2==0) obRow.className='odd';
				var obCell=document.createElement('TD');
				obCell.width='5%';
				if(data[i].Size>0)
				{
					$(obCell).click(function(event){ksCommander.SelectRow(event.target.parentNode);});
					var obCheckbox=document.createElement('INPUT');
					obCheckbox.type='checkbox';
					obCheckbox.name='select[]';
					obCheckbox.value=data[i].Name;
					$(obCheckbox).click(function(event){ksCommander.SelectRow(this.parentNode.parentNode);event.stopPropagation();});
					obCell.appendChild(obCheckbox);
				}
				ksCommander.arFilesList[data[i].Name]=data[i].Type;
				obRow.appendChild(obCell);
				obCell=document.createElement('TD');
				$(obCell).addClass('namet');
				if(data[i].Size>0)
				{
					$(obCell).click(function(){ksCommander.SelectRow(this.parentNode);});
				}
				var obIcon=document.createElement('IMG');
				obIcon.src=ksCommander.imagePath+'/'+data[i].Icon;
				obIcon.border="0";
				obIcon.alt=data[i].Type;
				obIcon.style.marginRight='10px';
				obCell.appendChild(obIcon);
				if(data[i].Action_type!='')
				{
					var obA=document.createElement('A');
					obA.appendChild(document.createTextNode(data[i].Name));
					if(data[i].Action_type=='CKS_view')
					{
						obA.href=data[i].Full_path;
						obA.target="_blank";
						obA.ksFullPath=data[i].Full_path;
						$(obA).click(function(event){ksCommander.LoadPreview(this.ksFullPath);event.stopPropagation();event.preventDefault();});
						obCell.appendChild(obA);
						obCell.appendChild(document.createElement('BR'));
						var obFont=document.createElement('FONT');
						obFont.style.fontSize='10px';
						obFont.style.color='#A7A7A7';
						obFont.innerHTML='Полное имя файла: '+data[i].Full_path;
						obCell.appendChild(obFont);
					}
					else
					{
						if(data[i].Action_type=='CKS_open')
						{
							obA.href="/admin.php?module=kscommander&CKS_path="+data[i].Full_path+'&isAjax='+ksCommander.isAjax;
							obA.ksFullPath=data[i].Full_path;
							$(obA).click(function(event){ksCommander.OpenFolder(this.ksFullPath);event.stopPropagation();event.preventDefault();});
							obCell.appendChild(obA);
						}
					}
				}
				else
				{
					obCell.appendChild(document.createTextNode(data[i].Name));
				}
				var obECell=document.createElement('TD');
				var obSCell=document.createElement('TD');
				var obDCell=document.createElement('TD');
				if(data[i].Size>0)
				{
					$([obECell,obSCell,obDCell]).click(function(event){ksCommander.SelectRow(this.parentNode);});
				}
				if(!data[i].Extension)
				{
					obECell.appendChild(document.createTextNode(data[i].Type));
				}
				else
				{
					obECell.appendChild(document.createTextNode(data[i].Extension));
				}
				if(data[i].SizeText)
				{
					obSCell.appendChild(document.createTextNode(data[i].SizeText));
				}
				if(mydata.view_url)
				{
					if(data[i].Action_type=='CKS_view')
					{
						var obButton=document.createElement('INPUT');
						obButton.type='button';
						obButton.value="Выбрать";
						obButton.ksFullPath=data[i].Full_path;
						$(obButton).click(function(event){FileBrowserDialogue.mySubmit(this.ksFullPath);event.stopPropagation();event.preventDefault();});
						obDCell.appendChild(obButton);
					}
				}
				else
				{
					if(data[i].Date)
					{
						var mydate=new Date();
						mydate.setTime(data[i].Date*1000);
						obDCell.appendChild(document.createTextNode(mydate.getHours()+':'+mydate.getSeconds()+' '+mydate.getDate()+'.'+mydate.getMonth()+'.'+mydate.getFullYear()));
					}
				}
				obCell.width="30%";
				obECell.width="10%";
				obSCell.width="10%";
				obDCell.width="20%";
				obRow.appendChild(obCell);
				obRow.appendChild(obECell);
				obRow.appendChild(obSCell);
				obRow.appendChild(obDCell);
				obBody.appendChild(obRow);
			}
			obDiv.appendChild(obTable);
			ksCommander.ChangeTotal(mydata.num_visible);
			ksCommander.GeneratePages(mydata.pages);
			ksCommander.LoadSubDirs(ksCommander.path,ksCommander.SearchTreeLeaf(ksCommander.path),true);
			ksCommander.UpdateNavChain();
		}
		ksCommander.HideLoading();
	},
	"LoadFiles":function(path,params)
	{
		if(!path) path=this.path;
		this.path = path;
		this.totalSelected = 0;
		document.getElementById('totalChecker').checked = false;
		this.ShowLoading();
		$.get("/admin.php",{module:"kscommander",mode:"ajax",page:"getfiles",path:path,type:this.ksMode,isAjax:this.isAjax,p1:this.page,count:this.visible,order:this.order,dir:this.dir},ksCommander.ShowFiles,"json");
		return false;
	},
	"OpenFolder":function(path)
	{
		this.path=path;
		this.totalSelected=0;
		this.page=1;
		this.LoadFiles(path,'');
		return false;
	},
	"ChangeSort":function (field,node)
	{
		for(var i=0;i<node.parentNode.childNodes.length;i++)
		{
			if(node.parentNode.childNodes[i].lastChild.tagName=='IMG')
			{
				node.parentNode.childNodes[i].lastChild.style.display='none';
			}
		}
		if(this.dir=='desc')
		{
			this.dir='asc';
			node.lastChild.src="/uploads/templates/admin/images/arrows/06.gif";
			node.lastChild.style.display='';
		}
		else
		{
			this.dir='desc';
			node.lastChild.src="/uploads/templates/admin/images/arrows/05.gif";
			node.lastChild.style.display='';
		}
		this.LoadFiles(this.path);
		return false;
	},
	"GetSelectedFiles":function ()
	{
		var res=Array();
		form=document.getElementById('fileSelector');
		for(i=0;i<form.elements.length;i++)
		{
			if(form.elements[i].checked)
			{
				res.push(form.elements[i].value);
			}
		}
		return res;
	},
	"GetInputData":function ()
	{
		var res=new Array;
		form=document.getElementById('fileSelector');
		for(i=0;i<form.elements.length;i++)
		{
			if(form.elements[i].type=='text')
			{
				res.push('rename['+form.elements[i].name+']='+encodeURIComponent(form.elements[i].value));
			}
		}
		return res;
	},
	"onDeleteFiles":function(data)
	{
		if(data)
		{
			var res='';
			var obDiv=document.createElement('DIV');
			obDiv.style.width="300px";
			obDiv.style.borderWidth="1px";
			obDiv.style.borderColor="#a0afa0";
			obDiv.style.borderStyle="solid";
			obDiv.style.backgroundColor="#F0FFF0";
			obDiv.style.paddingLeft=obDiv.style.paddingRight=obDiv.style.paddingTop=obDiv.style.paddingBottom='4px';
			for(ii in data)
			{
				res+=ii+':'+data[ii]+'\n';
				var obFont=document.createElement('FONT');
				obFont.color=data[ii].color;
				obFont.innerHTML=data[ii].text;
				obDiv.appendChild(obFont);
			}
			ksCommander.ShowMessage(obDiv);
			ksCommander.LoadFiles(ksCommander.path);
		}
		ksCommander.HideLoading();
	},
	"DeleteFiles":function (link)
	{
		if($(link).hasClass('inactive')) return false;
		var arData={"selected[]":this.GetSelectedFiles(),action:"delete",module:"kscommander",mode:"ajax",page:"deleteFiles",path:this.path};
		$.post("/admin.php?module=kscommander&page=deleteFiles",arData,ksCommander.onDeleteFiles,"json");
		ksCommander.ShowLoading();
		return false;
	},
	/* Функция показывает сообщение в новом окне */
	"ShowMessage":function (message,time)
	{
		/* Время отображения */
		if (!time)
			time=10000;
		
		while (document.getElementById('fixme'))
			document.body.removeChild(document.getElementById('fixme'));
			
		ksCommander.loading=null;
		
		var fixmes = document.getElementsByTagName('div');
		var names = '';
		var frame_id = '';
		var startsymb = -1;
		for (i = 0; i < fixmes.length; i++)
		{
			frame_id = fixmes[i].id;
			startsymb = frame_id.indexOf('fixme');
			if (startsymb == 0)
				if (document.getElementById(frame_id))
					document.body.removeChild(document.getElementById(frame_id));
		}
				
		var oFrame=document.createElement('div');
		oFrame.id = 'fixme' + Math.round(Math.random() * 100000);
		oFrame.className="fixmess";
		if(typeof(message)=='object')
		{
			oFrame.appendChild(message);
		}
		else
		{
			oFrame.innerHTML=message;
		}
		oFrame.style.display='block';
		document.body.appendChild(oFrame);
		setTimeout("ksCommander.closeme('"+oFrame.id+"')", time);
	},
	"closeme":function(frame_id)
	{
		var ob=document.getElementById(frame_id)
		if (ob)
		{
			document.body.removeChild(ob);
		}
	},
	"onCutFiles":function(data)
	{
		if(data)
		{
			var obDiv=document.createElement('DIV');
			obDiv.style.width="300px";
			obDiv.style.borderWidth="1px";
			obDiv.style.borderColor="#a0afa0";
			obDiv.style.borderStyle="solid";
			obDiv.style.backgroundColor="#F0FFF0";
			obDiv.style.paddingLeft=obDiv.style.paddingRight=obDiv.style.paddingTop=obDiv.style.paddingBottom='4px';
			for(ii in data.list)
			{
				var obFont=document.createElement('FONT');
				obFont.color=data.list[ii].color;
				obFont.innerHTML=data.list[ii].text;
				obDiv.appendChild(obFont);
			}
			ksCommander.ShowMessage(obDiv,10000);
			ksCommander.selectedTotal=parseInt(data.total);
			ksCommander.LoadFiles(ksCommander.path);
		}
		ksCommander.HideLoading();
	},
	"CutFiles":function (link)
	{
		if($(link).hasClass('inactive')) return false;
		var arData={"selected[]":this.GetSelectedFiles(),action:"cut"};
		this.selected=arData.selected;
		this.selectedPath=this.path;
		$.post("/admin.php?module=kscommander&mode=ajax&page=addToPaste&path="+this.path,arData,ksCommander.onCutFiles,"json");
		this.ShowLoading();
		return false;
	},
	"CopyFiles":function (link)
	{
		if($(link).hasClass('inactive')) return false;
		var arData={"selected[]":this.GetSelectedFiles(),action:"copy"};
		this.selected=arData.selected;
		this.selectedPath=this.path;
		$.post("/admin.php?module=kscommander&mode=ajax&page=addToPaste&path="+this.path,arData,ksCommander.onCutFiles,"json");
		this.ShowLoading();
		return false;
	},
	"DownloadFile":function(link)
	{
		if($(link).hasClass('inactive')) return false;
		this.ShowLoading();
		var arData=this.GetSelectedFiles();
		if(ksCommander.arFilesList[arData[0]]=='dir') 
		{
			alert('На данный момент скачать этот объект нельзя');
			this.HideLoading();
			return false;
		}
		link.href=this.path+arData[0];
		this.HideLoading();
		return true;
	},
	"pasteFiles":function (link)
	{
		$.get("/admin.php?module=kscommander&mode=ajax&page=doPaste",{path:this.path},ksCommander.onDeleteFiles,"json");
		this.ShowLoading();
		return false;
	},
	"RenameFiles":function (link)
	{
		if($(link).hasClass('inactive')) return false;
		var obDiv=document.getElementById('fileList');
		if(obDiv)
		{
			var obTable=$(obDiv).children("table").get(0);
			var bShowButton=false;
			for(var i=0;i<obTable.rows.length;i++)
			{
				if(obTable.rows && (obTable.rows[i].cells[0].firstChild!=null))
				{
					if(obTable.rows[i].cells[0].firstChild.checked)
					{
						var obInput=document.createElement('INPUT');
						bShowButton=true;
						obInput.type='text';
						var obImg=obTable.rows[i].cells[1].firstChild.cloneNode(false);
						if(obTable.rows[i].cells[1].childNodes[1].nodeType==3)
						{
							obInput.name=obTable.rows[i].cells[1].textContent;
						}
						else
						{
							obInput.name=obTable.rows[i].cells[1].childNodes[1].innerHTML;
						}
						obInput.value=obInput.name;
						obInput.style.display='';
						$(obTable.rows[i].cells[1]).empty().append(obImg).append(obInput);
					}
				}
			}
			if(bShowButton)
			{
				document.getElementById('fileSelector').onsubmit=function(){ksCommander.doRename();return false;};
				$('#pagesBar').children().hide();
				$('#pagesBar').append('<div class="manage"><input type="button" onclick="ksCommander.doRename()" value="Переименовать" class="refresh"/><input type="button" value="Отмена" onclick="return ksCommander.LoadFiles(ksCommander.path);"/></div>');
			}
		}
		return false;
	},
	"onRename":function (data)
	{
		if(data)
		{
			ksCommander.onDeleteFiles(data);
		}
		document.getElementById('fileSelector').onsubmit=function(){return true;};
		ksCommander.HideLoading();
	},
	"doRename":function ()
	{
		var sData=this.GetInputData().join('&')+'&action=doRename';
		document.getElementById('totalChecker').checked=false;
		$.post("/admin.php?module=kscommander&mode=ajax&page=renameFiles",sData,ksCommander.onRename,"json");
		this.ShowLoading();
		return false;
	}
}

function toggleActionBar(node)
{
	var bar=document.getElementById('actionBar');
	if(bar)
	{
		if(bar.style.display=='')
		{
			bar.style.display='none';
			node.className='content_arrow_down';
		}
		else
		{
			bar.style.display='';
			node.className='content_arrow_up';
		}
	}
}