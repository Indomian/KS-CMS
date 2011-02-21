function filechange(sender)
{
	var num = sender.name;
	num  = num.substr(num.lastIndexOf("_")+1);	var str_file=sender.value;	str_file = str_file.replace(/\\/g, '/');
	filename = str_file.substr(str_file.lastIndexOf("/")+1);
	var oName=document.getElementById('CKS_file_name_'+num);
	oName.value=filename;
	if (document.fileForm.jNum.value==num)
	{		num++;
		var table = document.getElementById("fileTable");
		var cnt = table.rows.length;
		var oRow = table.insertRow(cnt);
		var oCell = oRow.insertCell(0);
		oCell.innerHTML = '<input type="text" id="CKS_file_name_'+num+'" name="CKS_file_name_'+num+'" maxlength="255" value="" style="width:70%">';
		var oCell = oRow.insertCell(1);
		oCell.innerHTML = '<input type="file" id="CKS_file_'+num+'" name="CKS_file_'+num+'" maxlength="255" value="" onChange="filechange(this)" style="width:100%">';
  		document.fileForm.jNum.value = num;	}
}