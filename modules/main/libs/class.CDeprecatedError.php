<?php
/* Проверка легальности подключения файла */
if (!defined("KS_ENGINE"))	die("Hacking attempt!");

/**
 * Класс для обработки ошибок, типа CDeprecatedError указывающей на то, что функционал устарел и удалён из текущей версии и выбросит фатальную ошибку в следующей версии
 *
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.7
 * @since 27.12.2011
 */
class CUserError extends CError
{
	function __construct($message, $code = 0,$text='')
	{
		parent::__construct($message, $code,$text);
	}

	/**
	 * Магический метод выполняющий преобразование объекта ошибки в строку при выводе.
	 * @return string - текст для оформления ошибки
	 */
	function __toString()
	{
		$msg=$this->getMessage();
		$code=$this->getCode();
		$text='';
		if(KS_DEBUG==1)
		{
			$arTrace=$this->getTrace();
			$text.='<table border="1"><tr><td>#</td><td>File</td><td>Line</td><td>function</td></tr>';
			foreach($arTrace as $i=>$arRow)
				$text.='<tr><td>'.$i.'</td><td>'.(isset($arRow['file'])?$arRow['file']:'Unknow').'</td><td>'.(isset($arRow['line'])?$arRow['line']:'').'</td><td>'.$arRow['function'].'</td></tr>';
			$text.='</table>';
		}
		$msg=$this->GetErrorText();
		return $this->_error_form($msg." ".$this->error_text,$this->getCode()).$text;
	}
}