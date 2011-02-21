/**
 * В этом файле находятся функции для работы форума 
 */

function FastQuoteAnswer(from)
{
	var div=document.getElementById(from);
	if(typeof(div)!='object') return false;
	tinyMCE.activeEditor.selection.setContent('<span class="quoteStyle">'+div.innerHTML+'</span>');
	return true;
}
