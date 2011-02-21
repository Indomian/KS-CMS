<?php
/**
 * Класс с набором необходимых функций для работы структурного дерева
 * /author Ilya Doroshko <ilya@kolosstudio.ru>
 * /version 0.1
 * /создан 23.03.2009
 */
class Tree extends CObject
{
	function __construct($Table)
	{
		parent::__construct($Table);
		$this->arFields = array('id','parent_id','text_ident');
	}
	
	/**
	*Функция GetParents рекурсивно производит выборку родительских категорий
	*и формирует их в массив
	*/
	function GetParents($pid,&$array=false)
	{
		if (!$array)
			$array = array();

		$arResult = $this->GetList(false, array('id' => $pid), 1);
		$array[] = $arResult[0]['text_ident'];
		//echo $arResult[0]["text_ident"];
		if($arResult[0]['parent_id'] != 0)
		{
			$this->GetParents($arResult[0]['parent_id'],$array);
		}
		
		krsort($array);
		return $array;
	}
}
?>
