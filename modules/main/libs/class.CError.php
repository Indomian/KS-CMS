<?php
/**
 * \file class.CError.php
 * В файле находятся классы системы обработки ошибок (исключений).
 * Файл проекта CMS-local.
 *
 * Создан 21.10.2008
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.1
 * \todo прокомментировать все методы
 */

/*!Класс CError наследуется от системного класса Exception и предназначен для обработки разнообразных
 * исключений. Фактически в нем регистрируется вывод исключений в определенном стиле.*/

class CError extends Exception
{
	var $error;
	var $error_text;
	/**
	 * Шаблон вывода ошибки
	 */
	protected $errorTpl;

	/*!Конструктор класса, создает новое исключение.*/
	function __construct($message="",$code=0,$text='')
	{
		parent::__construct($message,intval($code));
		$this->error=$code;
		$this->error_text=$text;
		$this->errorTpl='error.tpl';
	}

	/**
	 * Магический метод выполняющий преобразование объекта ошибки в строку при выводе.
	 */
	function __toString()
	{
		global $smarty,$global_template;
		$msg=$this->getMessage();
		$code=$this->getCode();
		$text='';
		if(KS_DEBUG==1)
		{
			$arTrace=$this->getTrace();
			$text.='<table border="1"><tr><td>#</td><td>File</td><td>Line</td><td>function</td></tr>';
			foreach($arTrace as $i=>$arRow)
				$text.='<tr><td>'.$i.'</td><td>'.$arRow['file'].'</td><td>'.$arRow['line'].'</td><td>'.$arRow['function'].'</td></tr>';
			$text.='</table>';
		}
		if(!IS_ADMIN&&is_object($smarty))
		{
			$smarty->assign('error',$msg);
			$smarty->assign('text',$this->error_text);
			$smarty->assign('code',$code);
			if($smarty->template_exists($global_template.'/main/'.$this->errorTpl))
				return $smarty->fetch($global_template.'/main/'.$this->errorTpl).$text;
			elseif($smarty->template_exists('.default/main/'.$this->errorTpl))
				return $smarty->fetch('.default/main/'.$this->errorTpl).$text;
		}
		else
			$msg=$this->GetErrorText();
		return $this->_error_form($msg." ".$this->error_text,$this->getCode()).$text;
	}

	function GetErrorText()
	{
		global $smarty,$KS_MODULES;
		if(is_object($KS_MODULES))
		{
			return $KS_MODULES->GetErrorText($this->getMessage());
		}
		elseif(is_object($smarty))
		{
			$smarty->config_load('error.conf');
			return $smarty->get_config_vars($this->getMessage());
		}
		else
		{
			return $this->getMessage();
		}
	}

	/**
	 * Метод возвращает значение дополнительного поля ошибки
	 */
	function GetAdditionalText()
	{
		return $this->error_text;
	}

	/**
	 * Метод оформляет вывод ошибки в рамку, используется в административном разделе.
	 */
	function _error_form($message,$code)
	{
		$content='';
		if (strlen($message)>0)
		{
			$content="<div class=\"atention\" style=\"background:#FFF6C4 url('/uploads/templates/admin/images/error.gif') left 50% no-repeat; color:#D13B00; border: 1px solid #CC0000; margin: 0 0 6px; padding: 11px 0 11px 59px;\">";
			if(ERROR_LEVEL==0)
			{
				$content.="Ошибка № <b>$code</b>: ".$message;
				$content.=' произошла в файле: '.$this->getFile().' на строке '.$this->getLine();
				$content.='<br/>';
			}
			else
			{
				$content.='Ошибка: '.$message;
			}
			$content.="</div>";
		}
		return $content;
	}

	/**
	 * Статический метод, используется для обработки обычных ошибок пхп
	 */
	static function PhpErrorHandler($errno, $errstr, $errfile, $errline)
	{
		switch ($errno)
		{
			case E_USER_ERROR:
				throw new CError($errstr,$errno);
				break;

			case E_USER_WARNING:
				echo "<div style=\"border:3px solid #ffb400;padding:5px;background:#ffefc9;\"><b>Внимание</b>: $errstr</div>";
				break;

			case E_USER_NOTICE:
				echo "Замечание: [$errno] $errstr";
				break;

			default:
				return false;
		}
		/* Don't execute PHP internal error handler */
		return true;
	}
}

/**
 * Критическая ошибка, её вывод происходит в тех случаях когда не был подключен смарти
 */
class CCriticalError extends CError
{
	/**
	 * Магический метод выполняющий преобразование объекта ошибки в строку при выводе.
	 * Изменен принцип вывода, вместо шаблона смарти отдает код страницы и прерывает дальнейший вывод.
	 */
	function __toString()
	{
		global $smarty,$global_template;
		$msg=$this->getMessage();
		$code=$this->getCode();
		$text='';
		if(KS_DEBUG==1)
		{
			$arTrace=$this->getTrace();
			$text.='<table border="1"><tr><td>#</td><td>File</td><td>Line</td><td>function</td></tr>';
			foreach($arTrace as $i=>$arRow)
			{
				$text.='<tr><td>'.$i.'</td><td>'.$arRow['file'].'</td><td>'.$arRow['line'].'</td><td>'.$arRow['function'].'</td></tr>';
			}
			$text.='</table>';
		}
		return '<div style="border:2px solid red;background:#ffe38d;padding:10px;color:red;">Critical error #'.$code.':'.$msg.'</div>'.$text;
	}
}

/**
 * Класс, используемый для выброса HTTP-ошибок
 */
class CHTTPError extends CError
{
	private $sHeader;

	/*!Конструктор класса, создает новое исключение.*/
	function __construct($message="",$code=0,$text='',$sHeader='')
	{
		parent::__construct($message,intval($code));
		$this->error=$code;
		$this->error_text=$text;
		$this->errorTpl='error.tpl';
		if($sHeader!='')
			$this->sHeader=$sHeader;
		else
			$this->sHeader='HTTP/1.0 404 Not found';
	}

	function GetHeader()
	{
		return $this->sHeader;
	}
}

/**
 * Класс использующийся для обозначения ошибок при работе с файловой системой
 * \sa class.CFileSystem.php, CFileSystem, CSimpleFs.
 */

class CFileError extends CError
{
	/*!Конструктор класса, создает новое исключение.*/
	function __construct($message,$code=0,$text='')
	{
		parent::__construct($message,$code,$text);
	}
}

/**
 * Класс использующий для обозначения ошибок при работе с модулями
 * \todo найти к чему относиться и где выбрасывается ошибка
 */
class CModuleError extends CError
{
	function __construct($message,$code=0,$text='')
	{
		parent::__construct($message,$code,$text);
	}
}

/**
 * Класс для обозначения ошибок доступа, имеет другую функцию вывода сообщения об ошибке.
 * \todo возможно стоит привязать шаблоны.
 * прокоментировать все методы класса
 */
class CAccessError extends CError
{
	function __construct($message,$code=0,$text='')
	{
		parent::__construct($message,$code,$text);
		$this->errorTpl='errorAccess.tpl';
	}
}

/**
 * Класс для обработки ошибок, возникающих при работе с пользователем
 *
 * @author north-e <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 13.04.2009
 */
class CUserError extends CError
{
	function __construct($message, $code = 0,$text='')
	{
		parent::__construct($message, $code,$text);
	}
}

/**
 * Класс ошибок данных
 */

class CDataError extends CError
{
	function __construct($message,$code=100,$text='')
	{
		parent::__construct($message,$code,$text);
	}
}

