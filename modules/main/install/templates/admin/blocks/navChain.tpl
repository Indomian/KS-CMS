<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
    <li style="position:relative;overflow:visible;">
    	<a href="/admin.php?{$left_menu[$module.current].href}" class="hasDropDown"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$left_menu[$module.current].title}</span></a>
   		<div class="navDropDown" style="position:absolute;top:-2px;left:-1px;border:1px solid black;background:white;white-space:nowrap;display:none;z-index:10;">
   			<a href="/admin.php?{$left_menu[$module.current].href}" class="hasDropDown"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$left_menu[$module.current].title}</span></a><br/>
   			{foreach from=$left_menu key=oKey item=oItem name=menu}
				<a href="/admin.php?{$oItem.href}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$oItem.title}</span></a><br/>
   			{/foreach}
   		</div>
   	</li>
   	<li style="position:relative;overflow:visible;">
    	<a href="/admin.php?{$left_menu[$module.current].items[$module.page].href}" class="hasDropDown"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$left_menu[$module.current].items[$module.page].title}</span></a>
   		<div class="navDropDown" style="position:absolute;top:-2px;left:-1px;border:1px solid black;background:white;white-space:nowrap;display:none;z-index:10;">
   			<a href="/admin.php?{$left_menu[$module.current].items[$module.page].href}" class="hasDropDown"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$left_menu[$module.current].items[$module.page].title}</span></a><br/>
   			{foreach from=$left_menu[$module.current].items key=oKey item=oItem name=menu}
				<a href="/admin.php?{$oItem.href}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$oItem.title}</span></a><br/>
   			{/foreach}
   		</div>
   	</li>
   	{foreach from=$navChain item=oItem}
   	<li style="position:relative;overflow:visible;">
   		{assign var=name value=$oItem.title}
   		<a href="{$oItem.href}" {if $oItem.brothers!=''}class="hasDropDown"{/if}><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$smarty.config.$name|default:$oItem.title}</span></a>
   		{if $oItem.brothers!=''}
   		<div class="navDropDown" style="position:absolute;top:-2px;left:-1px;border:1px solid black;background:white;white-space:nowrap;display:none;z-index:10;">
   			<a href="{$oItem.href}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$smarty.config.$name|default:$oItem.title}</span></a><br/>
   			{foreach from=$oItem.brothers item=oBrother}
   			{assign var=name value=$oBrother.title}
   			<a href="{$oBrother.href}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{$smarty.config.$name|default:$oBrother.title}</span></a><br/>
   			{/foreach}
   		</div>
   		{/if}
   	</li>
    {/foreach}
</ul>