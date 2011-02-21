$(document).ready(function(){
	$("div.form#of :checkbox").click(function(e){
		var arResult=this.name.match(/^order_(show|necessary)_([a-z]+)/);
		if(arResult.length>0)
		{
			if(arResult[1]=='show')
			{
				if(this.checked==false)
				{
					$(":checkbox[name=order_necessary_"+arResult[2]+"]").attr('checked',false);
				}
			}
			if(arResult[1]=='necessary')
			{
				if(this.checked==true)
				{
					$(":checkbox[name=order_show_"+arResult[2]+"]").attr('checked',true);
				}
			}
		}
	});
});