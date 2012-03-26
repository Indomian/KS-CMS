{if $data.img!=''}
	<div style="float: left; padding-right: 8px; padding-bottom: 8px;"><img src="/uploads/{$data.img}" class="img" alt="{$data.title}"/></div>
{/if}
{$data.content}
<div style="clear:both;"><!-- --></div>
{widget name=catsubcat action=CatSubcategoriesList parent_id=$data.id orderby="date_add"}
{widget name=catsubcat action=CatAnnounce parent_id=$data.id select_from_children="N" announces_count="10" use_page_navigation="Y" sort_by="date_add" sort_order="desc"}
