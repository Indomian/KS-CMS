$(document).ready(function(){
	$(document).trigger("InitCalendar");
	$('select[name=SB_newsletter]').change(function(){
		if($(this).val()>0)
			$('ul.tabs2>[id$=_tab1]').hide();
		else
			$('ul.tabs2>[id$=_tab1]').show();
	});
	$('textarea[name=SB_list]').click(function(){
		$('#news input[type=checkbox],#groups input[type=checkbox]').attr('checked',false);
	});
	$('#allnews').click(function(){
		if($(this).attr('checked'))
		{
			$('#news input[type=checkbox]').attr('checked',true);
			$('textarea[name=SB_list]').val('');
		}
	});
	$('#allgroups').click(function(){
		if($(this).attr('checked'))
		{
			$('#groups input[type=checkbox]').attr('checked',true);
			$('textarea[name=SB_list]').val('');
		}
	});
	$('#news input[type=checkbox]').click(function(){
		$('#groups input[type=checkbox]').attr('checked',false);
	});
	$('#groups input[type=checkbox]').click(function(){
		$('#news input[type=checkbox]').attr('checked',false);
	});
});