if(!window.floatMessage)
{
	window.floatMessage={
		"showMessage":function(sender, msg, dwidth, shift_left, shift_top)
		{
			/* Ширина окна подсказки */
			dwidth = dwidth || 0;
			
			/* Смещение подсказки от левого и верхнег краёв */
			shift_left = shift_left || 0;
			shift_top = shift_top || 0;
			
			if(!this.div)
			{
				this.div=document.createElement('div');
				this.div.className='float_tip';
				this.div.style.display='none';
				this.div.style.position='absolute';
				if (dwidth > 0)
					this.div.style.width = dwidth + 'px';
				var top=document.createElement('div');
				top.className='float_tip_top';
				var bottom=document.createElement('div');
				bottom.className='float_tip_bottom';
				var content=document.createElement('div');
				content.className='float_tip_main';
				this.div.appendChild(top);
				this.content=this.div.appendChild(content);
				this.div.appendChild(bottom);
				this.div=document.body.appendChild(this.div);
			}
			
			var pos = this.absPosition(sender);
			
			/* Учитываем смещение по горизонтали */
			this.div.style.left = pos.x + shift_left + 'px';
			
			/* Учитываем смещение по вертикали */
			this.div.style.top = pos.y + shift_top + sender.offsetHeight + 'px';
			
			/* Отправляем сообщение тег */
			this.content.innerHTML=msg;
			
			this.div.style.display='';
			
			/* Функция при уходе с объекта, вызвавшего подсказку */
			sender.onmouseout = function()
			{
				floatMessage.div.style.display='none';
			};
			this.div.onmouseout=function()
			{
				this.style.display='none';
			};
		},
		
		/* Метод определения абсолютного положения объекта */
		"absPosition":function(obj) 
		{
			var x = y = 0;
      		while(obj) {
            	x += obj.offsetLeft;
            	y += obj.offsetTop;
            	obj = obj.offsetParent;
      		}
      		return {x:x, y:y};
      	}
	}
}