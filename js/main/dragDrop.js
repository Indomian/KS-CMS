var dragMaster = (function() {

    var dragObject
    var mouseDownAt

	var currentDropTarget


	function mouseDown(e) {
		e = fixEvent(e)
		if (e.which!=1) return

		//Этот код предназначен для проверки на каком элементе мы щелкнули
		if(mouseDownAt)
		{
			if(mouseDownAt.element.dragObject)
			{
				return false;
			}
			else
			{
				mouseDownAt = { x: e.pageX, y: e.pageY, element: this }
			}
		}
		else
		{
			mouseDownAt = { x: e.pageX, y: e.pageY, element: this }
		}

		addDocumentEventHandlers()

		return false
	}


	function mouseMove(e){
		e = fixEvent(e)

		// (1)
		if (mouseDownAt) {
			if (Math.abs(mouseDownAt.x-e.pageX)<5 && Math.abs(mouseDownAt.y-e.pageY)<5) {
				return false
			}
			// Начать перенос
			var elem  = mouseDownAt.element
			// текущий объект для переноса
			dragObject = elem.dragObject

			// запомнить, с каких относительных координат начался перенос
			var mouseOffset = getMouseOffset(elem, mouseDownAt.x, mouseDownAt.y)
			mouseDownAt = null // запомненное значение больше не нужно, сдвиг уже вычислен

			dragObject.onDragStart(mouseOffset) // начали

		}

		if(!dragObject)
		{
			return false;
		}

		// (2)
		dragObject.onDragMove(e.pageX, e.pageY)

		// (3)
		var newTarget = getCurrentTarget(e)

		// (4)
		if ((currentDropTarget != newTarget)) {
			if (currentDropTarget) {
				currentDropTarget.onLeave()
			}
			if (newTarget) {
				newTarget.onEnter()
			}
			currentDropTarget = newTarget

		}

		// (5)
		return false
    }


    function mouseUp(){
		if (!dragObject) { // (1)
			mouseDownAt = null
		} else {
			// (2)
			var fakedragObject=dragObject;
			dragObject = null
			if (currentDropTarget) {
				if(currentDropTarget.accept(fakedragObject))
				{
					fakedragObject.onDragSuccess(currentDropTarget)
				}
				else
				{
					fakedragObject.onDragFail()
				}
			} else {
				fakedragObject.onDragFail()
			}
		}

		// (3)
		removeDocumentEventHandlers()
    }


	function getMouseOffset(target, x, y) {
		var docPos	= getOffset(target)
		return {x:x - docPos.left, y:y - docPos.top}
	}


	function getCurrentTarget(e) {
		// спрятать объект, получить элемент под ним - и тут же показать опять

		if (navigator.userAgent.match('MSIE') || navigator.userAgent.match('Gecko')) {
			var x=e.clientX, y=e.clientY
		} else {
			var x=e.pageX, y=e.pageY
		}
		// чтобы не было заметно мигание - максимально снизим время от hide до show
		dragObject.hide()
		var elem = document.elementFromPoint(x,y)
		dragObject.show()

		// найти самую вложенную dropTarget
		while (elem) {
			// которая может принять dragObject
			if (elem.dropTarget && elem.dropTarget.canAccept(dragObject)) {
				var offset=getMouseOffset(elem,x,y);
				if(offset.y>elem.clientHeight/2)
				{
					elem.dropTarget.addTo='after';
				}
				else
				{
					elem.dropTarget.addTo='before';
				}
				return elem.dropTarget
			}
			elem = elem.parentNode
		}

		// dropTarget не нашли
		return null
	}


	function addDocumentEventHandlers() {
		document.onmousemove = mouseMove
		document.onmouseup = mouseUp
		document.ondragstart = document.body.onselectstart = function() {return false}
	}
	function removeDocumentEventHandlers() {
		document.onmousemove = document.onmouseup = document.ondragstart = document.body.onselectstart = null
	}


    return {

		makeDraggable: function(element){
			element.onmousedown = mouseDown
		}
    }
}())

function DragObject(element) {
	element.dragObject = this

	dragMaster.makeDraggable(element)

	var rememberPosition
	var mouseOffset

	this.onDragStart = function(offset) {
		var s = element.style
		rememberPosition = {top: s.top, left: s.left, position: s.position}
		s.position = 'absolute'
		s.zIndex=10000;

		mouseOffset = offset
	}

	this.getElement=function() {return element;}

	this.hide = function() {
		element.style.display = 'none'
	}

	this.show = function() {
		element.style.display = ''
	}

	this.onDragMove = function(x, y) {
		element.style.top =  y - mouseOffset.y +'px'
		element.style.left = x - mouseOffset.x +'px'
	}

	this.onDragSuccess = function(dropTarget) { }

	this.onDragFail = function() {
		var s = element.style
		s.top = rememberPosition.top
		s.left = rememberPosition.left
		s.position = rememberPosition.position
		s.zIndex=0;
	}

	this.toString = function() {
		return element.id
	}
}

function DropTarget(element) {

	element.dropTarget = this
	this.addTo="";
	this.obElement=element;

	this.canAccept = function(dragObject) {
		var obUL=element.parentNode;
		if(obUL && (obUL.tagName=='UL'))
		{
			return true
		}
		return false;
	}

	this.accept = function(dragObject) {
		this.onLeave()

		var obUL=element.parentNode;
		var obj=document.getElementById(dragObject);
		var beforeId="0";
		if(obUL && (obUL.tagName=='UL'))
		{
			obj.style.position='static';
			obj.style.display='';
			if(this.addTo=='before')
			{
				obUL.insertBefore(obj,element);
				beforeId=element.id.substring(4,element.id.length);
				//obUL.removeChild(obj);
			}
			if(this.addTo=='after')
			{
				if(element.firstChild.tagName=='P')
				{
					obUL.insertBefore(obj,element);
					beforeId='last';
				}
				else
				{
					if(element.nextSibling)
					{
						if(element.nextSibling.tagName=='LI')
						{
							obUL.insertBefore(obj,element.nextSibling);
							beforeId=element.nextSibling.id.substring(4,element.nextSibling.id.length);
						}
					}
					else
					{
						obUL.appendChild(obj);
						beforeId='last';
					}
				}
			}
			//Определяем кого и куда отсортировали
			var parentId=0;
			if(obUL.parentNode.tagName=='LI')
			{
				//Значит мы находимся в родительском элементе
				parentId=parseInt(obUL.parentNode.id.substring(4,obUL.parentNode.id.length));
			}
			var id=parseInt(obj.id.substring(4,obj.id.length));

			document.loading=ShowLoading();
			$.get("/admin.php?module=navigation&page=ajax&action=sort&tid="+obj._ks_typeid+"&id="+id+"&pid="+parentId+'&bid='+beforeId,null,function(data)
				{
					HideLoading(document.loading);
					if(data>0)
					{
						alert('Ошибка переноса');
					}
				}
			);
			return true;
		}
		return false;
	}

	this.onLeave = function() {
		element.className =  ''
		element.style.borderTopWidth="0px";
		element.style.borderBottomWidth="0px";
	}

	this.onEnter = function() {
		element.className = 'uponMe';
		if(this.addTo=='before')
		{
			element.style.borderTopWidth="2px";
			element.style.borderTopStyle='solid';
		}
		if(this.addTo=='after')
		{
			element.style.borderBottomWidth="2px";
			element.style.borderBottomStyle='solid';
		}
	}

	this.toString = function() {
		return element.id
	}

	return element.dropTarget;
}

function fixEvent(e) {
	// получить объект событие для IE
	e = e || window.event

	// добавить pageX/pageY для IE
	if ( e.pageX == null && e.clientX != null ) {
		var html = document.documentElement
		var body = document.body
		e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
		e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
	}

	// добавить which для IE
	if (!e.which && e.button) {
		e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) )
	}

	return e
}

function getOffset(elem) {
    if (elem.getBoundingClientRect) {
        return getOffsetRect(elem)
    } else {
        return getOffsetSum(elem)
    }
}

function getOffsetRect(elem) {
    var box = elem.getBoundingClientRect()

    var body = document.body
    var docElem = document.documentElement

    var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop
    var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft
    var clientTop = docElem.clientTop || body.clientTop || 0
    var clientLeft = docElem.clientLeft || body.clientLeft || 0
    var top  = box.top +  scrollTop - clientTop
    var left = box.left + scrollLeft - clientLeft

    return { top: Math.round(top), left: Math.round(left) }
}

function getOffsetSum(elem) {
    var top=0, left=0
    while(elem) {
        top = top + parseInt(elem.offsetTop)
        left = left + parseInt(elem.offsetLeft)
        elem = elem.offsetParent
    }

    return {top: top, left: left}
}