function filechange(sender)
{
	var num = sender.name;
	num  = num.substr(num.lastIndexOf("_")+1);
	var str_file=sender.value;
	str_file = str_file.replace(/\\/g, '/');
	filename = str_file.substr(str_file.lastIndexOf("/")+1);
	var oName=document.getElementById('file_name_'+num);
	oName.value=filename;
}