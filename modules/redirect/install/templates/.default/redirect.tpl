{$content}
{if $pause>0 && $use_pause>0}
<div class="timer">{$pause}</div>
{literal}<script type="text/javascript">
	function TimerTick()
	{
		value=parseInt($('.timer').html());
		value-=1;
		$('.timer').html(value);
		if(value<2) {/literal}document.location='{$url}';{literal}
		setTimeout("TimerTick()",1000);
	}
	$(document).ready(function(){
		setTimeout("TimerTick()",1000);
	});
</script>
{/literal}{/if}
