$(document).bind("InitCalendar",function(){
	$(".date_time_input").datetimepicker(dateStrings);
	$(".date_input").datepicker(dateStrings);
});
$(document).ready(function(){$(document).trigger("InitCalendar");});
