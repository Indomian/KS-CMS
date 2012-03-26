<h2 class="alt">{$menuType.name}</h2>
{if $menu}
<ul class="menu">
{foreach key=key item=item from=$menu}
<li><a href="{$item.link}" {if $item.target!=""}target="{$item.target}"{/if}><img src="{$templates_files_folder}/{$glb_tpl}/images/t.gif" alt="{$item.anchor}" /> {$item.anchor}</a></li>
{/foreach}
</ul>
{/if}
