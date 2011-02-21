{strip}
<ul>
	{if $dataList.ITEMS!=''}
	{foreach from=$dataList.ITEMS item=oItem key=oKey name=myList}
	{if $oItem.ITEMS!=''}{assign var="image_name" value='minus'}{else}{assign var="image_name" value='plus'}{/if}
	<li id="item{$oItem.id}" class="treeItem" _ks_typeid="{$dataList.SECTION.id}" onmouseover = "ShowButtons();">
		<span id="btn{$oItem.id}" style="overflow: hidden; display: none; float: right;" width="100%">
			<a title="{#edit#}" href="{get_url ACTION=edit CSC_elmid=$oItem.id}">
				<img width="16" height="16" alt="{#edit#}" src="{#images_path#}/icons2/edit.gif"/>
			</a>
			<a title="{#delete#}" onclick="return confirm('{#delete_confirm#}');" href="{get_url ACTION=delete CSC_elmid=$oItem.id}">
				<img width="16" height="16" alt="{#delete#}" src="{#images_path#}/icons2/delete.gif"/>
			</a>
		</span>
		<span>
			<a onclick="showSubItems('item{$oItem.id}'); return false;" href="#">
				<img width="13" height="13" id="{$image_name}{$oItem.id}" alt="icon" src="{#images_path#}/icons_menu/{$image_name}.gif"/>
			</a>
			<img width="16" height="16" alt="icon" src="{#images_path#}/icons_tree/folder.gif"/>
			<a href="{get_url ACTION=edit CSC_elmid=$oItem.id}">{$oItem.anchor}</a>
		</span>
		{if $oItem.ITEMS!=''}
 			{include file="admin/navigation_subelements.tpl" dataList=$oItem}
 		{/if}
	</li>
	{/foreach}
	{/if}
	<li id="parentadd{$oItem.parent_id}">
		<p>
			<span class="add_01">
				<img width="16" height="16" alt="icon" src="{#images_path#}/icons2/create.gif"/>
				<a href="{get_url ACTION=new}&CSC_parid={$oItem.parent_id}">{#create_menu#}</a>
			</span>
		</p>
	</li>
</ul>
{/strip}
{if $dataList.ITEMS!=''}
	{foreach from=$dataList.ITEMS item=oItem key=oKey name=myList}
		<script>
			li=document.getElementById("item{$oItem.id}");
			new DragObject(li);
			new DropTarget(li);
			{literal}
			obj=new DropTarget(li.lastChild.firstChild.firstChild);	//plus|minus img
			obj.onEnter=function(){
				this.obElement.className='overPlus';
				var id=parseInt(this.obElement.id.substring(4,this.obElement.id.length));
				showSubItems('item'+id);
			};
			obj.canAccept=function(dragObject){
				return true;
			};
			obj.accept=function(dragObject){
				return false;
			};
			{/literal}
			li=document.getElementById("parentadd{$oItem.parent_id}");
			HideLoading(document.loading);
		</script>
	{/foreach}
{/if}
