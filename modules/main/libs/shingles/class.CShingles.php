<?php

/**
 * Класс, реализующий сравнение текстов по алгоритму шинглов
 * 
 * @author North-E <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 31.07.2009
 */
class CShingles
{
	/**
	 * Исходный текст
	 * 
	 * @var string
	 */
	private $original_text;
	
	/**
	 * Текст-дубликат
	 * 
	 * @var string
	 */
	private $copied_text;
	
	/**
	 * Количество слов в одном шингле
	 * 
	 * @var int
	 */
	private $words_in_shingle;
	
	/**
	 * Функция, вычисляющая контрольную сумму шингла
	 * 
	 * @var string
	 */
	private $checksum_func;
	
	/**
	 * Кодировка сравниваемых текстов
	 * 
	 * @var string
	 */
	private $text_encoding;
	
	/**
	 * Массив стоп-символов
	 * 
	 * @var array
	 */
	private $stop_symbols;
	
	/**
	 * Массив стоп-слов
	 * 
	 * @var array
	 */
	private $stop_words;
	
	/**
	 * Конструктор объекта класса
	 * 
	 * @param string $original_text Исходный текст
	 * @param string $copied_text Текст-дубликат
	 * @param int $words_in_shingle Количество слов в одном шингле
	 * @param string $checksum_func Имя функции, вычисляющей контрольную сумму шингла
	 * @param string $text_encoding Кодировка сравниваемых текстов
	 */
	function __construct($original_text = false, $copied_text = false, $words_in_shingle = 10, $checksum_func = "md5", $text_encoding = "UTF-8")
	{
		/* Производим инициализацию объекта класса */
		$this->SetOriginalText($original_text);
		$this->SetCopiedText($copied_text);
		$this->SetWordsInShingle($words_in_shingle);
		$this->SetChecksumFunc($checksum_func);
		$this->SetTextEncoding($text_encoding);
		
		/* Подключаем файл со стоп-символами и стоп-словами */
		$stop_symbols = array();
		$stop_words = array();
		$stop_symbols_filename = dirname(__FILE__) . "/stop_symbols.php";
		if (file_exists($stop_symbols_filename))
			include($stop_symbols_filename);
		$this->stop_symbols = $stop_symbols;
		$this->stop_words = $stop_words;
	}
	
	function __destruct()
	{
		
	}
	
	/**
	 * Устанавливает исходный текст
	 * 
	 * @param string $original_text
	 * @return bool
	 */
	function SetOriginalText($original_text)
	{
		if (is_string($original_text))
		{
			$this->original_text = $original_text;
			return true;
		}
		$this->original_text = null;
		return false;
	}
	
	/**
	 * Возвращает исходный текст
	 * 
	 * @return string
	 */
	function GetOriginalText()
	{
		return $this->original_text;
	}
	
	/**
	 * Устанавливает текст-дубликат
	 * 
	 * @param string $copied_text
	 * @return bool
	 */
	function SetCopiedText($copied_text)
	{
		if (is_string($copied_text))
		{
			$this->copied_text = $copied_text;
			return true;
		}
		$this->copied_text = null;
		return false;
	}
	
	/**
	 * Возвращает текст-дубликат
	 * 
	 * @return string
	 */
	function GetCopiedText()
	{
		return $this->copied_text;
	}
	
	/**
	 * Устанавливает количество слов в одном шингле
	 * 
	 * @param int $words_in_shingle
	 */
	function SetWordsInShingle($words_in_shingle)
	{
		if (is_int($words_in_shingle) && $words_in_shingle > 0)
			$this->words_in_shingle = $words_in_shingle;
		else
			$this->words_in_shingle = 10;
	}
	
	/**
	 * Возвращает количество слов в одном шингле
	 * 
	 * @return int
	 */
	function GetWordsInShingle()
	{
		return $this->words_in_shingle;
	}
	
	/**
	 * Устанавливает функцию, которая будет вычислять контрольные суммы шинглов
	 * 
	 * @param string $checksum_func
	 */
	function SetChecksumFunc($checksum_func)
	{
		if (in_array($checksum_func, array("md5", "crc32", "sha1")))
			$this->checksum_func = $checksum_func;
		else
			$this->checksum_func = "md5";
	}
	
	/**
	 * Возвращает функцию, используемую для вычисления контрольных сумм шинглов
	 * 
	 * @return string
	 */
	function GetChecksumFunc()
	{
		return $this->checksum_func;
	}
	
	/**
	 * Устанавливает кодировку сравниваемых текстов
	 * 
	 * @param string $text_encoding
	 * @return bool
	 */
	function SetTextEncoding($text_encoding)
	{
		if (is_string($text_encoding))
			$this->text_encoding = $text_encoding;
		else
			$this->text_encoding = "UTF-8";
	}
	
	/**
	 * Возвращает кодировку сравниваемых текстов
	 * 
	 * @return string
	 */
	function GetTextEncoding()
	{
		return $this->text_encoding;
	}
	
	/**
	 * Функция приводит текст к канонизированному виду
	 * 
	 * @param string $text Канонизируемый текст
	 * @param string $text_encoding Кодировка канонизируемого текста
	 */
	function CanonizeText($text, $text_encoding = false, &$canonization_stats = false)
	{
		$canonization_start = microtime(true);
		
		/* Инициализируем переменную возврата */
		$canonized_text = $text;
			
		/* Определяем кодировку канонизируемого текста */
		if (!is_string($text_encoding))
			$text_encoding = $this->GetTextEncoding();
		
		/* Для начала сделаем все буквы прописными */
		$canonized_text = mb_strtolower(trim($canonized_text), $text_encoding);
		
		/* Избавимся от html-тегов */
		$canonized_text = strip_tags($canonized_text);
		
		/* Массив, в котором для статистики сохраним количество убранных символов различного типа */
		$number_of_replacements = array
		(
			'html_symbols' => 0,
			'stop_symbols' => 0,
			'stop_words' => 0
		);
		
		/* Дальше неплохо было бы избавиться от последовательностей вида &...; */
		$canonized_text = preg_replace("#\&(.+);#", " ", $canonized_text, -1, $number_of_replacements['html_symbols']);
		
		/* Избавляемся от стоп-символов */
		if (count($this->stop_symbols) > 0)
			$canonized_text = str_replace($this->stop_symbols, " ", $canonized_text, $number_of_replacements['stop_symbols']);
		
		/* Формируем массив из слов текста */
		$canonized_text_words = array();
		if (mb_strlen($canonized_text, $text_encoding) > 0)
		{
			$text_words = explode(" ", $canonized_text);
			
			/* Проверим все слова текста и уберём все стоп-слова */
			foreach ($text_words as $text_word)
			{
				$word = trim($text_word);
				$word = str_replace("ё", "е", $word);
				
				if (mb_strlen($word, $text_encoding) > 0)
					if (!in_array($word, $this->stop_words))
						$canonized_text_words[] = $word;
					else
						$number_of_replacements['stop_words']++;
			}
			$canonized_text = implode(" ", $canonized_text_words);
		}
		
		$canonization_end = microtime(true);
		
		if (is_array($canonization_stats))
		{
			$canonization_stats['canonized_text'] = $canonized_text;
			$canonization_stats['canonized_text_words'] = $canonized_text_words;
			$canonization_stats['number_of_replacements'] = $number_of_replacements;
			$canonization_stats['required_time'] = $canonization_end - $canonization_start;
		}
		
		return $canonized_text;
	}
	
	/**
	 * Сравнивает два текста на схожесть
	 * 
	 * @param string $original_text Исходный текст
	 * @param string $copied_text Текст-дубликат
	 * @return float Числовая оценка схожести текстов
	 */
	function CompareTexts($original_text = false, $copied_text = false, $checksum_func = false, $text_encoding = false, &$comparing_stats = false)
	{	
		if (!is_string($original_text))
			$original_text = $this->GetOriginalText();
		if (!is_string($copied_text))
			$copied_text = $this->GetCopiedText();
		if (!is_string($checksum_func))
			$checksum_func = $this->GetChecksumFunc();
		if (!is_string($text_encoding))
			$text_encoding = $this->GetTextEncoding();
		
		if (is_string($original_text) && is_string($copied_text))
			if (mb_strlen($original_text, $text_encoding) && mb_strlen($copied_text, $text_encoding))
			{
				$comparing_start = microtime(true);
				
				/* Массивы с параметрами канонизированных текстов */
				$original_text_params = array();
				$copied_text_params = array();
				
				/* Канонизируем сравниваемые тексты */
				$canonized_original_text = $this->CanonizeText($original_text, $text_encoding, $original_text_params);
				$canonized_copied_text = $this->CanonizeText($copied_text, $text_encoding, $copied_text_params);
				
				/* Определяем количество слов в одном шингле */
				$words_in_shingle = $this->GetWordsInShingle();
				
				for ($text_id = 0; $text_id < 2; $text_id++)
				{
					/* Определяем текущий текст */
					if ($text_id == 0)
						$canonized_text_words = $original_text_params['canonized_text_words'];
					else
						$canonized_text_words = $copied_text_params['canonized_text_words'];
					$shingles_count = count($canonized_text_words) - $words_in_shingle + 1;
					
					if ($shingles_count < 1)
					{
						/* Мы не можем сравнить тексты по методу шинглов, так как длина одного из текстов меньше количества слов в одном шингле */
						return false;
					}
					
					$shingles = array();
					$shingles_checksums = array();
					for ($i = 0; $i < $shingles_count; $i++)
					{
						$current_shingle = array_slice($canonized_text_words, $i, $words_in_shingle);
						$current_shingle_checksum = call_user_func($checksum_func, implode(" ", $current_shingle));
						$shingles[] = $current_shingle;
						$shingles_checksums[] = $current_shingle_checksum;
					}
					
					if ($text_id == 0)
					{
						$original_shingles_count = $shingles_count;
						$original_shingles = $shingles;
						$original_shingles_checksums = $shingles_checksums;
					}
					else
					{
						$copied_shingles_count = $shingles_count;
						$copied_shingles = $shingles;
						$copied_shingles_checksums = $shingles_checksums;
					}
				}
				
				/* Теперь считаем количество шинглов исходного текста в дубликате */
				$similar_shingles_count = 0;
				foreach ($original_shingles_checksums as $original_shingle_checksum)
				{
					if (in_array($original_shingle_checksum, $copied_shingles_checksums))
					{
						/* Этот шингл есть в дубликате */
						$similar_shingles_count++;
					}
				}
				
				/* Уникальность дубликата */
				$unique_percent = round(($copied_shingles_count - $similar_shingles_count) / $copied_shingles_count, 3);
				
				/* Полнота (целостность) исходного текста в дубликате */
				$safe_percent = round($similar_shingles_count / $original_shingles_count, 3);
				
				$comparing_end = microtime(true);
				
				if (is_array($comparing_stats))
				{ 
					$comparing_stats['similar_shingles_count'] = $similar_shingles_count;
					$comparing_stats['unique_percent'] = $unique_percent;
					$comparing_stats['safe_percent'] = $safe_percent;
					$comparing_stats['shingles_count'] = array('original' => $original_shingles_count, 'copied' => $copied_shingles_count);
					$comparing_stats['shingles'] = array('original' => $original_shingles, 'copied' => $copied_shingles);
					$comparing_stats['checksums'] = array('original' => $original_shingles_checksums, 'copied' => $copied_shingles_checksums);
					$comparing_stats['required_time'] = $comparing_end - $comparing_start;
				}
				
				/* Возвращаем результат сравнения */
				return $unique_percent;
			}
		
		return false;		 
	}
	
}

?>