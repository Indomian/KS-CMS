$(document).bind("InitCalendar",function(){
	$(".date_time_input").datetimepicker({
		beforeShow: function(input, inst) {
			$(input).parent().children('.date_button').addClass('shown')
		},
		onClose: function(dateText, inst) {
			$(this).parent().children('.date_button').removeClass('shown');
		}
	});
	$(".date_input").datepicker({
		beforeShow: function(input, inst) {
			$(input).parent().children('.date_button').addClass('shown')
		},
		onClose: function(dateText, inst) {
			$(this).parent().children('.date_button').removeClass('shown');
		}
	});
	$(".date_button").click(function(){
		if(!$(this).hasClass('shown'))
		{
			$(this).parent().children('.date_time_input').datetimepicker('show')
			$(this).parent().children('.date_input').datepicker('show')
			$(this).addClass('shown');
		}
		else
		{
			$(this).parent().children('.date_time_input').datetimepicker('hide')
			$(this).parent().children('.date_input').datepicker('hide')
			$(this).removeClass('shown');
		}
	});
});
$(document).ready(function(){$(document).trigger("InitCalendar");});

