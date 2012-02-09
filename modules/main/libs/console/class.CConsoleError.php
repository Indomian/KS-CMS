<?php
/**
 * @file class.CConsoleError.php
 * В файле находятся классы системы обработки ошибок обрабатывающий ошибки при работе из консоли
 *
 * Создан 13.12.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.1
 */

/*!Класс CError наследуется от системного класса Exception и предназначен для обработки разнообразных
 * исключений. Фактически в нем регистрируется вывод исключений в определенном стиле.*/

class CError extends Exception
{
	private $error;
	private $error_text;

	/*!Конструктор класса, создает новое исключение.*/
	function __construct($message="",$code=0,$text='')
	{
		parent::__construct($message,intval($code));
		$this->error=$code;
 		$this->error_text=$text;
	}

	/**
	 * Магический метод выполняющий преобразование объекта ошибки в строку при выводе.
	 */
	function __toString()
	{
		global $global_template;
		$msg=$this->getMessage();
		$code=$this->getCode();
		$text='';
		if(KS_DEBUG==1)
		{
			$arTrace=$this->getTrace();
			$text.="#   | File   | Line    | function \n";
			foreach($arTrace as $i=>$arRow)
				$text.=$i.' | '.$arRow['file'].' | '.$arRow['line'].' | '.$arRow['function']."\n";
		}
		return $this->_error_form($msg." ".$this->error_text,$this->getCode()).$text;
	}

	/**
	 * Метод возвращает код ошибки
	 */
	function GetErrorText()
	{
		return $this->getMessage();
	}

	/**
	 * Метод оформляет вывод ошибки в рамку, используется в административном разделе.
	 */
 	function _error_form($message,$code)
 	{
 		$content='';
 		if (strlen($message)>0)
 		{
			$content.="Ошибка № $code: ".$message;
			$content.=' произошла в файле: '.$this->getFile().' на строке '.$this->getLine();
			$content.="\n";
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
		        echo "Внимание: $errstr\n";
		        break;

		    case E_USER_NOTICE:
		        echo "Замечание: [$errno] $errstr\n";
		        break;

		    default:
		        //echo "Unknown error type: [$errno] $errstr<br />\n";
		        break;
		    }

    	/* Don't execute PHP internal error handler */
    	//return true;
 	}
}

/**
 * Критическая ошибка, её вывод происходит в тех случаях когда не был подключен смарти
 */
class CCriticalError extends CError{}

/**
 * Класс, используемый для выброса HTTP-ошибок
 */
class CHTTPError extends CError{}

/**
 * Класс использующийся для обозначения ошибок при работе с файловой системой
 * \sa class.CFileSystem.php, CFileSystem, CSimpleFs.
 */

class CFileError extends CError{}

/**
 * Класс использующий для обозначения ошибок при работе с модулями
 * \todo найти к чему относиться и где выбрасывается ошибка
 */
class CModuleError extends CError{}

/**
 * Класс для обозначения ошибок доступа, имеет другую функцию вывода сообщения об ошибке.
 * \todo возможно стоит привязать шаблоны.
 * прокоментировать все методы класса
 */
class CAccessError extends CError{}

/**
 * Класс для обработки ошибок, возникающих при работе с пользователем
 *
 * @author north-e <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 13.04.2009
 */
class CUserError extends CError{}

/**
 * Класс ошибок данных
 */

class CDataError extends CError{}
 
