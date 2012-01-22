{strip}
<span class="small grey">{$data.date}</span>
{if $data.img!=''}
	<div style="float: left; padding-right: 8px; padding-bottom: 8px;">
	<img src="/uploads/{$data.img}" class="img"/>
	</div>
{/if}
{$data.content}
<div style="clear:both;"><!-- --></div>
{if array_key_exists('ext_rating',$data)}
	{widget name=interfaces action=RatingVote rate_module="catsubcat" material_type="catsubcat_element" rate_field="rating" material_id=$data.main_content.id value=$data.main_content.ext_rating votelife="10" isAjax="Y"}
{/if}	
{if $data.ext_comment=='Y'}
	<a name="comment"></a>
	{widget name=wave action=WavePosts hash="CE`$data.id`" count="100" order="asc"}
{/if}
{/strip}