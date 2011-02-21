{strip}
<span class="small grey">{$data.main_content.date}</span>
{if $data.main_content.img!=''}
	<div style="float: left; padding-right: 8px; padding-bottom: 8px;">
	<img src="/uploads/{$data.main_content.img}" class="img"/>
	</div>
{/if}
{$data.main_content.content}
<div style="clear:both;"><!-- --></div>
{if array_key_exists('ext_rating',$data.main_content)}
{widget name=interfaces action=RatingVote rate_module="catsubcat" material_type="catsubcat_element" rate_field="rating" material_id=$data.main_content.id value=$data.main_content.ext_rating votelife="10" isAjax="Y"}
{/if}	
{if $data.main_content.ext_FORUM_COMMENT_THEME!=0}
<a name="comment"></a>
{widget name=forum action=ShowCommentsForm isAjax="Y" themeId=$data.main_content.ext_FORUM_COMMENT_THEME count="10" order="asc"}
{/if}
{/strip}