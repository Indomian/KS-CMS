$(document).ready(function(){
	$('body').append('<div id="hint_box"><div class="hint_box_in"><div class="hint_box_in_in"><div class="hint_box_content"></div></div></div>');
	$('#hint_box').mouseleave(function(){
		$(this).hide();//fadeTo(200,0,function(){$(this).hide();});
	}).fadeTo(0,0,function(){$(this).hide();});
	$('.hint_icon').mouseenter(function(){
		id=$(this).attr('id');
		id=id.substr(5,id.length);
		if(typeof(arHints)=='object' && arHints[id])
		{
			$('#hint_box').fadeTo(0,0);
			myPos=$(this).offset();
			$('.hint_box_content').html(arHints[id]);
			$('#hint_box').css({left:myPos.left,top:myPos.top-$('#hint_box').outerHeight()+$(this).height()}).fadeTo(200,1);
		}
	});
});
