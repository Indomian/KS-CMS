{if $addCalendar}
<script type="text/javascript" src="/js/jquery/ui.datetimepicker.js"></script>
{/if}
<script type="text/javascript" src="/js/interfaces/filter.js"></script>
<form action="{get_url _CLEAR="ff.* fm filter"}" method="POST" name="filter">
	<input type="hidden" name="fm" value="POST"/>
	<input type="hidden" name="filter" value="1"/>
	<fieldset class="search" style="{if $hideFilter==0}display:none;{/if}" id="filterPanel">
		<b>{#filter#}</b>
		<table border="0" cellspacing="0" cellpadding="5" class="form" style="border:0px;">
			{foreach from=$data key=oKey item=oItem}
			<tr>
				<td align="right" style="vertical-align:middle;"><label>{$titles.$oKey}</label></td>
				{if $oItem.TYPE=='SELECT'}
					<td valign="center"><select name="ff{$oItem.FIELD|replace:".":"^"}{if $oItem.METHOD=='->'}[]{/if}" class="text_field" {if $oItem.METHOD=='->'}size="3" multiple="multiple"{/if} class="text_field">
					{strip}
						{foreach from=$oItem.VALUES key=sKey item=sItem}
							<option value="{$sKey}"
								{if $oItem.METHOD=='->'}
									{if is_array($oItem.VALUE)&& in_array($sKey,$oItem.VALUE)} selected="selected"{/if}
								{else}
									{if (strlen($oItem.VALUE)==0)}
										{if  strlen($sKey)==0}
										selected="selected"
										{/if}
									{else}
										{if ($oItem.VALUE===0 AND $sKey===0)}
											selected="selected"
										{else}
											{if $oItem.VALUE==$sKey}
												selected="selected"
											{/if}
										{/if}
									{/if}
								{/if}>{$sItem}</option>
						{/foreach}
					{/strip}
					</select></td>
				{elseif $oItem.TYPE=='DATE'}
					<td>{#from#} <input type="text" id="ff{$oItem.FIELD}_1" readonly="readonly" name="ff{$oItem.FIELD|replace:".":"^"}[]" value="{$oItem.VALUE[0]|date_format:"%d.%m.%Y %H:%M"}" style="width:100px;" class="form_input"/>
					<img src="{#images_path#}/calendar/img.gif" id="ff{$oItem.FIELD}_1_btn" style="border: 0pt none ; cursor: pointer;" title="{#select_date#}" align="absmiddle"/>
					{#to#} <input type="text" id="ff{$oItem.FIELD}_2" readonly="readonly" name="ff{$oItem.FIELD|replace:".":"^"}[]" value="{$oItem.VALUE[1]|date_format:"%d.%m.%Y %H:%M"}" style="width:100px;" class="form_input" title="{#select_date#}"/>
					<img src="{#images_path#}/calendar/img.gif" id="ff{$oItem.FIELD}_2_btn" style="border: 0pt none ; cursor: pointer;" title="Выбор даты спомощью календаря" align="absmiddle"/>
					<script type="text/javascript">
						$(document).bind("InitCalendar",function(){ldelim}
							$("#ff{$oItem.FIELD}_1").datetimepicker({ldelim}dateFormat:{#date_format#},timeFormat:{#time_format#},dayNames:{#days#},dayNamesMin:{#daysMin#},dayNamesShort:{#daysShort#},monthNames:{#monthes#}{rdelim});
							$("#ff{$oItem.FIELD}_2").datetimepicker({ldelim}dateFormat:{#date_format#},timeFormat:{#time_format#},dayNames:{#days#},dayNamesMin:{#daysMin#},dayNamesShort:{#daysShort#},monthNames:{#monthes#}{rdelim});
							$("#ff{$oItem.FIELD}_1_btn").click(function(){ldelim}$("#ff{$oItem.FIELD}_1").datetimepicker('show'){rdelim});
							$("#ff{$oItem.FIELD}_2_btn").click(function(){ldelim}$("#ff{$oItem.FIELD}_2").datetimepicker('show'){rdelim});
						{rdelim});
						$(document).ready(function(){ldelim}$(document).trigger("InitCalendar");{rdelim});
					</script>
					</td>
				{else}
					<td><input type="text" name="ff{$oItem.FIELD|replace:".":"^"}" value="{$oItem.VALUE|htmlspecialchars:2:"UTF-8":false}" class="text_field"></td>
				{/if}
			</tr>
			{/foreach}
			<tr>
				<td>
					<input type="submit" value="{#go_filter#}" class="search_button" name="fdo">&nbsp;<input type="submit" value="{#cancel#}" name="fundo"/>
				</td>
			</tr>
		</table>
	</fieldset>
</form>
<div class="content_arrow_{if $hideFilter==0}down{else}up{/if}" style="cursor:pointer;" onclick="ToggleFilterPanel(this);"> </div>