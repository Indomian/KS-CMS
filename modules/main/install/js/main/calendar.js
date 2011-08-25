var dateStrings={
	'dateFormat':'',
	'timeFormat':'',
	'dayNames':'',
	'dayNamesMin':'',
	'dayNamesShort':'',
	'monthNames':''
};
$(document).bind("InitCalendar",function(){
	$(".date_input").datetimepicker(dateStrings);
	$(".date_button").click(function(){
		$(this).parent().children('.date_input').datetimepicker('show')
	});
});
$(document).ready(function(){$(document).trigger("InitCalendar");});

