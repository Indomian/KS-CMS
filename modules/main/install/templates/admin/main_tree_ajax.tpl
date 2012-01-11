{strip}
<ul>
	{foreach from=$tree.list item=oItem}
		{math equation="rand()" var=1 assign="liid"}
	   	<li style="padding-top: 1px; padding-bottom: 1px;">
	   		<p id="line{$liid}" onMouseOver="LineHover('{$liid}','hover')" onMouseOut="LineHover('{$liid}','normal')" class="tree_line_normal">
	   		<!-- Иконки для работы с элементами -->
			<span class="lite_icons">
			{if $oItem.admin_url!=""}
		    	<a href="/admin.php{$oItem.admin_url}&disp_design=1&ajaxreq={$smarty.request.q}&liid={if !$smarty.request.curid}root{else}{$smarty.request.curid}{/if}&keepThis=true&TB_iframe=true&width=930&height=500" class="thickbox{$randid}" title="{$oItem.title}"><img height="16" width="16" src="{#images_path#}/icons2/edit.gif" alt="{#edit#}" title="{#edit#}" /></a>
	    	{/if}
	    	{if $oItem.admin_watch_url!=""}
	    		<a href="/admin.php{$oItem.admin_watch_url}&disp_design=1&ajaxreq={$smarty.request.q}&liid={$smarty.request.curid}&keepThis=true&TB_iframe=true&width=930&height=500" class="thickbox{$randid}" title="{$oItem.title}"><img height="16" width="16" src="{#images_path#}/icons2/view.gif" alt="{#view#}" title="{#view#}" /></a>
	    	{/if}
	    	{if $oItem.watch_url!=""}
				<a href="{$oItem.watch_url}" target="_blank"><img height="16" width="16" src="{#images_path#}/icons2/view.gif" alt="{#view#}" title="{#view#}" /></a>
			{/if}
			{if $oItem.delete_url!=""}
				<a class="delete" title="{$oItem.title}" href="/admin.php?module={$oItem.module}{$oItem.delete_url}&ajaxreq={$smarty.request.q}&liid={if !$smarty.request.curid}root{else}{$smarty.request.curid}{/if}"><img height="16" width="16" src="{#images_path#}/icons2/delete.gif" alt="{#delete#}" title="{#delete#}" /></a>
			{/if}
			</span>
			<font onClick="LineHover('{$oItem.liid}','hoverBlock'); return false;" style="display:block;">
			<!-- Раскрывающиеся элементы -->
			{if $oItem.type=='folder'}
	   			{if $oItem.ajax_req!=''}
	   				<a href="#" onclick="return nextStep('{$oItem.module}','{$oItem.ajax_req}',{$oItem.liid},false); LineHover('{$oItem.liid}','hoverBlock'); return false;">
	   				<span id="img{$oItem.liid}"><img src="{#images_path#}/icons_menu/plus.gif" alt="icon" height="13" width="13" /></a></span>&nbsp;
	   			{else}
	   				<img src="{#images_path#}/transparent.gif" alt="icon" height="13" width="13"/>&nbsp;
	   			{/if}
	   			<img src="{$oItem.ico}" alt="icon" height="16" width="16" />&nbsp;
	   		{else}
	   			<img src="{#images_path#}/transparent.gif" alt="icon" height="13" width="13"/>&nbsp;
	   			<img src="{$oItem.ico}" alt="icon" height="16" width="16" />&nbsp;
	   		{/if}
			<span style="height:15px;overflow:hidden;display:block;margin-left:37px;margin-top:-15px;background:transparent;">{$oItem.title|escape:"html"|truncate:255:"..."}</span>
			</font>
			</p>
			<span style="display:none;" id="{$oItem.liid}"></span>
	   	</li>
	{/foreach}


	<!-- Ссылки на добавление новых разделов и элементов -->
	{if ($tree.ui.add_cat_url!='') || ($tree.ui.add_elm_url!='')}
	<li style="padding-top: 1px; padding-bottom: 1px; padding-left: 20px;" class="addButton">
		<p>
		<span class="add_01">
		<img height="16" width="16" alt="icon" src="{#images_path#}/icons2/create.gif"/>&nbsp;
		{if ($tree.ui.add_cat_url=='') || ($tree.ui.add_elm_url=='')}
			{if $tree.ui.add_cat_url==""}
				<a class="thickbox{$randid}" href="{$tree.ui.add_elm_url}&disp_design=1&ajaxreq={$smarty.request.q}&liid={if !$smarty.request.curid}root{else}{$smarty.request.curid}{/if}&keepThis=true&TB_iframe=true&width=900&height=500">{if $tree.ui.add_elm_text!=''}{$tree.ui.add_elm_text}{else}{#add_element#}{/if}</a>
			{else}
				{if $tree.ui.add_elm_ulr==""}
				<a class="thickbox{$randid}" href="{$tree.ui.add_cat_url}&disp_design=1&ajaxreq={$smarty.request.q}&liid={if !$smarty.request.curid}root{else}{$smarty.request.curid}{/if}&keepThis=true&TB_iframe=true&width=900&height=500">{if $tree.ui.add_cat_text!=''}{$tree.ui.add_cat_text}{else}{#add_element#}{/if}</a>
				{/if}
			{/if}
		{else}
			<a href="#" onclick="return AddBox('box{$randid}');">{#add#}</a>
		{/if}
		</span>
		{if $tree.ui.add_cat_url!='' && $tree.ui.add_elm_url!=''}
		<span id="box{$randid}" class="add_02" style="display:none;">
		<ins><img height="20" width="20" alt="icon" src="{#images_path#}/icons_tree/create_folder.gif"/> <a class="thickbox{$randid}" title="{if $tree.ui.add_cat_text!=''}{$tree.ui.add_cat_text}{else}{#section#}{/if}" href="{if !$tree.ui.add_cat_url}?module=catsubcat&ACTION=new&CSC_catid=0{else}{$tree.ui.add_cat_url}{/if}&type=cat&disp_design=1&ajaxreq={$smarty.request.q}&liid={if !$smarty.request.curid}root{else}{$smarty.request.curid}{/if}&keepThis=true&TB_iframe=true&width=900&height=500">{if $tree.ui.add_cat_text!=''}{$tree.ui.add_cat_text}{else}{#section#}{/if}</a></ins>
		<ins><img height="20" width="20" alt="icon" src="{#images_path#}/icons_tree/create_file.gif"/> <a class="thickbox{$randid}" title="{if $tree.ui.add_elm_text!=''}{$tree.ui.add_elm_text}{else}{#element#}{/if}" href="{if !$tree.ui.add_elm_url}?module=catsubcat&ACTION=new&CSC_catid=0{else}{$tree.ui.add_elm_url}{/if}&type=elm&disp_design=1&ajaxreq={$smarty.request.q}&liid={if !$smarty.request.curid}root{else}{$smarty.request.curid}{/if}&keepThis=true&TB_iframe=true&width=900&height=500">{if $tree.ui.add_elm_text!=''}{$tree.ui.add_elm_text}{else}{#element#}{/if}</a></ins>
		</span>
		{/if}
		</p>
	</li>
	{/if}
</ul>
{/strip}