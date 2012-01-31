<?php

$testObject=new CObject('main_modules');
$iCount=100;
$fStart=microtime(true);
for($i=0;$i<$iCount;$i++)
{
	$testObject->GetTableFields();
}
echo "Total: ".(microtime(true)-$fStart)."\n";