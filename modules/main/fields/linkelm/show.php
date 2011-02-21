<?php

$value=$params['value'];
$myData=explode('|',$value);
$rand=rand(100000,999999);
$sResult.="<input type=\"text\" name=\"id".$rand."\" id=\"id".$rand."\" value=\"".$myData[1]."\">[<span id=\"module".$rand."\">".$myData[0]."</span>]\n";
$sResult.='<input type="hidden" name="'.$params['prefix']."ext_".$params['field']['title'].'" id="'.$params['prefix']."_ext_".$params['field']['title'].'" value="'.$params['value'].'" style="width:100%"/>';
$sResult.='<input type="button" id="select'.$rand.'" name="select'.$rand.'" value="...">';
$sResult.='<a id="del'.$rand.'" href="#" style="margin-left:6px;"><img style="vertical-align: middle;" src="/uploasds/templates/admin/images/icons2/delete.gif"/></a>';
$sResult.='<script type="text/javascript">' ."\n".
		'function showForm'.$rand.'(e,data){'."\n".
			'var obData=$(":checkbox[name=\'sel\[elm\]\[\]\']",data);'."\n".
			'for(var i=0;i<obData.length;i++)'."\n".
			'{'."\n".
				'obData.eq(i).replaceWith($(\'<input type="button" name="\'+obData.eq(i).attr(\'value\')+\'" kstitle="\'+obData.eq(i).next("input[type=hidden]").eq(0).val()+\'" value="Выбрать"/>\').click(' ."\n".
				'function(event){'."\n".
					'$("#id'.$rand.'").val($(this).attr("kstitle"));' ."\n".
					'$("#module'.$rand.'").html($(this).attr("name"));' ."\n".
					'$("#'.$params['prefix']."_ext_".$params['field']['title'].'").val(this.name);
					kstb_remove();
					})' ."\n".
				');' ."\n".
			'}' ."\n".
			'$("#navChain>:first-child",data).remove();'."\n";
			if($params['field']['option_1']=="blog") $sResult.='$("#navChain>:first-child",data).remove();';
			$sResult.='$(document).trigger("InitCalendar");'."\n".
			'$(document).trigger("InitTiny");'.	"\n".
		'}'.
		'$(document).ready(function(){' .
		'$("a#del'.$rand.'").click(function(event){'."\n".
			'if(confirm("Вы действительно хотите удалить эту привязку?"))'."\n".
			'{'."\n".
			'$("#id'.$rand.'").val("");' ."\n".
			'$("#module'.$rand.'").html("");' ."\n".
			'$("#'.$params['prefix']."_ext_".$params['field']['title'].'").val("")'."\n".
			'}'."\n".
		'});'.
		'$("input#select'.$rand.'").click(function(event){'."\n";
			if($params['field']['option_1']=="blog")
				$sResult.='kstb_show("Выбрать раздел","/admin.php?module='.$params['field']['option_1'].'&page=records&mode=small&width=800&height=480",null,showForm'.$rand.');';
			else
				$sResult.='kstb_show("Выбрать раздел","/admin.php?module='.$params['field']['option_1'].'&mode=small&width=800&height=480",null,showForm'.$rand.');';
		$sResult.='})});'.
	'</script>';
?>