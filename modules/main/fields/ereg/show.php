<?php
$sResult="<input type=\"text\" class=\"form_input\" id=\"".$params['prefix']."ext_".$params['field']['title']."\" name=\"".$params['prefix']."ext_".$params['field']['title']."\" value=\"".$params['value']."\" style=\"width:100%\">";
$sResult.='<script type="text/javascript">$(document).ready(function(){' .
			'$("#'.$params['prefix'].'ext_'.$params['field']['title'].'").keyup(' .
				'function(event){' ."\n".
				'var regexp=new RegExp("'.addslashes($params['field']['option_1']).'","i");' ."\n".
					'regexp.ignoreCase=true;'."\n".
					'if(!regexp.test(this.value)) $(this).addClass("invalid"); else $(this).removeClass("invalid");'."\n".
					'return true;});});'."\n".
		'</script>';
?>