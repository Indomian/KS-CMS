$(document).ready(function(){
	$('.answerButton').click(function(e){
		e.preventDefault();
		parents=$(this).parentsUntil('form');
		id=parents.last().parent().children('input[name=WV_id]').val();
		//id=$(this).parentsUntil('form').children('input[name=WV_id]');
		$('#answer').show().insertAfter('#post_'+id);
		$('#answer input[name=parent_id]').val(id);
	});
});
