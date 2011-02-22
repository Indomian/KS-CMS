{if $data.main_content.img!=''}
	<div style="float: left; padding-right: 8px; padding-bottom: 8px;"><img src="/uploads/{$data.main_content.img}" class="img"/></div>
{/if}
{$data.main_content.content}
{widget name=wave action=WavePosts hash="c`$data.main_content.id`"}
<div style="clear:both;"><!-- --></div>
{if array_key_exists('ext_rating',$data.main_content)}
{widget name=interfaces action=RatingVote rate_module="catsubcat" material_type="catsubcat_category" rate_field="rating" material_id=$data.main_content.id value=$data.main_content.ext_rating votelife="10" isAjax="Y"}
{/if}
{widget name=catsubcat action=CatSubcategoriesList parent_id=$data.main_content.id orderby="date_add"}
{widget name=catsubcat action=CatAnnounce isAjax=Y parent_id=$data.main_content.id select_from_children="Y" announces_count="10" use_page_navigation="Y" orderby="date_add"}