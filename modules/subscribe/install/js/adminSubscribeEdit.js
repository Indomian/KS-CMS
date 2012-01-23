/**
 * Файл содержит скрипты административного интерфейса страницы редактирования подписчика
 */
$(document).ready(function(){
	$('#listCheck').click(function(){
		if($(this).attr('checked'))
			$('.nlItem').attr('checked',true);
		else
			$('.nlItem').attr('checked',false);
	});
	$('.nlItem').click(function(){
		if($('.nlItem:checked').length==$('.nlItem').length)
			$('#listCheck').attr('checked',true);
		else
			$('#listCheck').attr('checked',false);
	});
});
