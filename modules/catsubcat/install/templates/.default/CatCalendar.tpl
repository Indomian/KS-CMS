<div class="block_holder">
	<div class="block">
		<div class="block_h">
			<div class="block_ribbon_calendar">&nbsp;</div>
			<div class="block_ribbon"><div><h2>Архив</h2></div></div>     
		</div>
		<div class="block_in">
			<div class="calendar">
				<div class="calendar_in">
					<div class="calendar_header">
						<table cellspacing="0" cellpadding="0">
							 <tr>
								  <td><a href="{get_url month=$data.month.num-1 year=$data.year}">&larr;</a></td>
								  <td>{$data.month.title}, {$data.year}</td>
								  <td><a href="{get_url month=$data.month.num+1 year=$data.year}">&rarr;</a></td>
						 	</tr>
						</table>

					</div>
					<table class="calendar_table" cellspacing="5" cellpadding="0">
						<tr>
							<th><span>ПН</span></th>
							<th><span>ВТ</span></th>
							<th><span>СР</span></th>
							<th><span>ЧТ</span></th>
							<th><span>ПТ</span></th>
							<th><span>СБ</span></th>
							<th><span>ВС</span></th>
						</tr>
						{foreach from=$data.weeks item=Week key=iWeek}
						<tr>
							{foreach from=$Week item=Day key=wDay}
							<td>{if !$Day.cur}&nbsp;{else}
							{if $Day.count>0}<a href="/arhiv/index.html?day={$Day.day}&month={$data.month.num}&year={$data.year}" target="_self">{$Day.day}</a>
							{else}{$Day.day}{/if}{/if}</td>
							{/foreach}
						</tr>
						{/foreach}
						
					</table>
				</div>
			</div>
		</div>
	</div>
</div>