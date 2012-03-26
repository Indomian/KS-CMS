{capture assign="title"}
	{if $data.id<1}
		{#create_title#}
	{else}
		{if $rights.canEdit}
			{#title_edit#}
		{else}
			{#title_view#}
		{/if}
		<b>{$data.title}</b>
	{/if}
{/capture}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=banners"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
	{strip}
	<li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$title}</span></a>
	{/strip}
</ul>
<h1>{$title}</h1>
<form action="{get_url _CLEAR="ACTION CSC_id"}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="{$data.id}"/>
	<input type="hidden" name="OS_id" value="{$data.id}"/>
	<input type="hidden" name="action" value="save"/>
	{ksTabs NAME=banners_edit head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}
		<div class="form">
			<table class="layout">
				{if $is_ajax_frame!=1}
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				{/if}
				<tr class="is_necessary_light">
					<td>{Title field="title"}</td>
					<td><input type="text" name="OS_title" value="{$data.title}" style="width:98%" class="form_input"/></td>
				</tr>
				<tr>
					<td>{Title field="active"}</td>
					<td><input type="checkbox" name="OS_active" value="1" {if $data.active!=0}checked="checked"{/if}/></td>
				</tr>
				<tr class="is_necessary_light">
					<td>{Title field="text_ident"}</td>
					<td><input type="text" name="OS_text_ident" value="{$data.text_ident}" style="width:98%" class="form_input"/></td>
				</tr>
				<tr>
					<td>{Title field="type_id"}</td>
					<td>
						<select name="OS_type_id" class="form_input" style="width:100%;">
						{foreach from=$TYPES item=oItem}
							<option value="{$oItem.id}" {if $oItem.id==$data.type_id}selected="selected"{/if}>{$oItem.title}</option>
						{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{Title field="client_id"}</td>
					<td>
						<select name="OS_client_id" class="form_input" style="width:100%;">
						{foreach from=$CLIENTS item=oItem}
							<option value="{$oItem.id}" {if $oItem.id==$data.client_id}selected="selected"{/if}>{$oItem.title}</option>
						{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{Title field="save_stats"}</td>
					<td><input type="checkbox" name="OS_save_stats" value="1" {if $data.save_stats!=0}checked="checked"{/if}/></td>
				</tr>
			</table>
		</div>
	{/ksTab}
	{ksTab NAME=$smarty.config.tabs_content|default:"Content"}
		<div class="form">
			<table class="layout">
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				<tr>
					<td>{Title field="content"}
					<td>{ShowEditor field="OS_content" theme="advanced" value=$data.content}</td>
				</tr>
				<tr>
					<td>{Title field="img"}
					<td>
						<input type="file" name="OS_img" value="" style="width:100%"/><br/>
						{if $data.img!=""}
							<div style="width:200px;height:200px;overflow:scroll;">
							<img src="/uploads{$data.img}">
							</div>
							<input type="checkbox" name="OS_img_del" value="1"/> Удалить
						{/if}
					</td>
				</tr>
				<tr>
					<td>{Title field="href"}
					<td><input type="text" name="OS_href" value="{$data.href|escape:"html":"UTF-8"}" style="width:98%;" class="form_input"/></td>
				</tr>
			</table>
		</div>
	{/ksTab}
	{ksTab NAME=$smarty.config.tabs_targets|default:"Targets"}
		<div class="form">
			<table class="layout">
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				<tr>
					<td>{Title field="active_from"}</td>
					<td>
						{ShowCalendar field="OS_active_from" title=$smarty.config.select_date value=$data.active_from}
					</td>
				</tr>
				<tr>
					<td>{Title field="active_to"}</td>
					<td>
						{ShowCalendar field="OS_active_to" title=$smarty.config.select_date value=$data.active_to}
					</td>
				</tr>
				<tr>
					<td>{Title field="inc_path"}</td>
					<td><textarea name="OS_inc_path" class="form_textarea">{$data.inc_path|escape:"html":"UTF-8"}</textarea></td>
				</tr>
				<tr>
					<td>{Title field="exc_path"}</td>
					<td><textarea name="OS_exc_path" class="form_textarea">{$data.exc_path|escape:"html":"UTF-8"}</textarea></td>
				</tr>
				<tr>
					<td>{Title field="dates"}</td>
					<td>
						<table class="calendar">
							<tr>
								<th colspan="2" align="right">Все часы</th>
								<th>Пн.</th>
								<th>Вт.</th>
								<th>Ср.</th>
								<th>Чт.</th>
								<th>Пт.</th>
								<th>Сб.</th>
								<th>Вс.</th>
							</tr>
							<tr>
								<th class="all">Весь день</th>
								<td class="all allY"><input type="checkbox"></td>
								<td class="all allD"><input type="checkbox"></td>
								<td class="all allD"><input type="checkbox"></td>
								<td class="all allD"><input type="checkbox"></td>
								<td class="all allD"><input type="checkbox"></td>
								<td class="all allD"><input type="checkbox"></td>
								<td class="all allD"><input type="checkbox"></td>
								<td class="all allD"><input type="checkbox"></td>
							</tr>
							{section loop=24 name=h}
								<tr>
									<th>{$smarty.section.h.iteration-1}:00 - {$smarty.section.h.iteration-1}:59</th>
									<td class="all allH"><input type="checkbox" value="1"></td>
									{section loop=7 name=d}
										<td {if $data.times[d][h]==1}class="checked"{else}class="unchecked"{/if}><input type="checkbox" {if $data.times[d][h]==1}checked="checked"{/if} value="1" name="times[{$smarty.section.d.iteration}][{$smarty.section.h.iteration}]"/></td>
									{/section}
								</tr>
							{/section}
						</table>
					</td>
				</tr>
			</table>
		</div>
	{/ksTab}
	{ksTab NAME=$smarty.config.tabs_comment}
		<div class="form">
			<table class="layout">
				<tr>
					<td><textarea name="OS_comment" class="form_textarea" style="height:300px;">{$data.comment|escape:"html":"UTF-8"}</textarea></td>
				</tr>
			</table>
		</div>
	{/ksTab}
	{if $data.save_stats==1}
	{ksTab NAME=$smarty.config.tabs_statistics}
		<div class="form">
			<table class="layout">
				<tr>
					<td>Показать с</td>
					<td>
						{ShowCalendar field="statisticsFrom" title=$smarty.config.select_date value=$data.statisticsFrom}
					</td>
					<td>по</td>
					<td>
						{ShowCalendar field="statisticsTo" title=$smarty.config.select_date value=$data.statisticsTo}
					</td>
					<td>
						<input type="button" class="button" id="filterStatistics" value="Выбрать данные"/>
					</td>
				</tr>
			</table>
			<table class="layout" id="statResult">
				<tr class="titles">
					<th width="18%" colspan="2"><h3>Дата</h3></th>
					<th width="3%"><h3>00</h3></th>
					<th width="3%"><h3>01</h3></th>
					<th width="3%"><h3>02</h3></th>
					<th width="3%"><h3>03</h3></th>
					<th width="3%"><h3>04</h3></th>
					<th width="3%"><h3>05</h3></th>
					<th width="3%"><h3>06</h3></th>
					<th width="3%"><h3>07</h3></th>
					<th width="3%"><h3>08</h3></th>
					<th width="3%"><h3>09</h3></th>
					<th width="3%"><h3>10</h3></th>
					<th width="3%"><h3>11</h3></th>
					<th width="3%"><h3>12</h3></th>
					<th width="3%"><h3>13</h3></th>
					<th width="3%"><h3>14</h3></th>
					<th width="3%"><h3>15</h3></th>
					<th width="3%"><h3>16</h3></th>
					<th width="3%"><h3>17</h3></th>
					<th width="3%"><h3>18</h3></th>
					<th width="3%"><h3>19</h3></th>
					<th width="3%"><h3>20</h3></th>
					<th width="3%"><h3>21</h3></th>
					<th width="3%"><h3>22</h3></th>
					<th width="3%"><h3>23</h3></th>
				</tr>

				{foreach from=$data.statistics key=sDay item=oItem}
					<tr>
						<td rowspan="2">{$sDay}</td>
						<td>Показов</td>
						{section loop=24 name=hours}
							<td>{$oItem[hours].views|default:"-"}</td>
						{/section}
					</tr>
					<tr>
						<td>Хитов</td>
						{section loop=24 name=hours}
							<td>{$oItem[hours].hits|default:"-"}</td>
						{/section}
					</tr>
				{/foreach}
			</table>
		</div>
	{/ksTab}
	{/if}
	{if $addFields!=''}
		{ksTab NAME=$smarty.config.tabs_userfields}
		<div class="form">
			<table class="layout">
				{if $is_ajax_frame!=1}
				<tr class="titles">
					<th width=30%><h3>{#header_field#}</h3></th>
					<th width=70%><h3>{#header_value#}</h3></th>
				</tr>
				{/if}
				{foreach from=$addFields item=oItem}
				<tr>
					<td>{$oItem.description}</td>
					{assign var=value value=ext_`$oItem.title`}
					<td>{showField field=$oItem value=$data[$value] prefix="OS_"}</td>
				</tr>
				{/foreach}
			</table>
		</div>
		{/ksTab}
	{/if}
{/ksTabs}
<div class="form_buttons">
	{if $rights.canEdit}
	<div><input type="submit" class="save" value="{#save#}"/></div>
	<div><input type="submit" name="update" value="{#apply#}"/></div>
	{/if}
	<div><a href="{get_url _CLEAR="action id"}" class="cancel_button">{#cancel#}</a></div>
</div>
</form>
{if $rights.canEdit}
	{include file='admin/common/hint.tpl' title=$smarty.config.title_edit description=$smarty.config.hint_edit icon="/big_icons/doc.gif"}
{else}
	{include file='admin/common/hint.tpl' title=$smarty.config.title_view description=$smarty.config.hint_view icon="/big_icons/doc.gif"}
{/if}
{if not $rights.canEdit}
{literal}
<script type="text/javascript">
	$(document).ready(function(){
		$('input,select,textarea').attr('disabled',true);
	});
</script>
{/literal}
{/if}