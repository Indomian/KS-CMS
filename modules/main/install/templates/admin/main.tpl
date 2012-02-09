<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
</ul>

<h1>{#title_site_structure#}</h1>

<div class="tree">
	{include file='admin/main_tree_ajax.tpl'}
</div>

{include file='admin/common/hint.tpl' title=$smarty.config.title_site_structure description=$smarty.config.hint_site_structure icon="/big_icons/folder.gif"}
