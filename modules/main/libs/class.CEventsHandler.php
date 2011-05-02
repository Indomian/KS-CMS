<?php
/**
 * Файл класса-обработчика событий
 *
 * Класс выполняет код переданных обработчиков при возникновении определённых событий
 *
 * @filesource class.CEventsHandler.php
 * @author north-e <pushkov@kolosstudio.ru>
 * @version 0.1
 * @since 03.03.2009
 */

 if (!defined('KS_ENGINE'))
	die("Hacking attempt!");
 include_once('class.CError.php');			// Подключение класса ошибок
 include_once('class.CMain.php');

 /**
  * Класс CEventsHandler - класс, предназначенный для вызова обработчиков событий
  *
  * Добавлен метод для работы с файлом событий, установка и удаление обработчиков событий
  *
  * @author north-e <pushkov@kolosstudio.ru>, blade39 <blade39@kolosstudio.ru>
  * @version 1.1
  * @since 03.03.2009
  */

 class CEventsHandler extends CBaseObject
 {

	/**
	 * Имя конфигурационного файла событий
	 *
	 * @var string
	 */
	protected $config_file;

	/**
	 * Конфигурационный массив
	 *
	 * Определяется при создании объекта класса из массива $KS_EVENTS, который должен быть определён в конфигурационнои
	 * файле с именем CEventsHandler::$config_file
	 *
	 * Структура массива следующая:
	 * первый ключ - имя модуля, например, 'main'
	 * второй ключ - имя события, например, 'onInit'
	 * третий ключ - ключ обработчика события, например, 0 (обработчики выполняются последовательно с помощью foreach)
	 * четвёртый ключ может иметь два значения:
	 * 'hFile' - имя файла-обработчика (файл ищется в папке MODULES_DIR . '/events/');
	 * 'hFunc' - имя функции-обработчика (может быть описана как в файле-обработчике, так и вне его)
	 * BlaDe39 : 'bOnce' - поле указывающее на то что обработчик одноразовый
	 * @var array
	 */
	protected $events;

	/**
	 * Поле, указывающее на то, в каком виде метод CEventsHandler::Execute() будет возвращать ответ
	 *
	 * Возможные значения поля:
	 * overall - результатом будет булевская переменная, равная true в случае, если все обработчики события успешно выполнены;
	 * particular - результатом будет двумерный массив, в котором будет указан результат выполнения каждого из обработчиков
	 *
	 * @var string
	 */
	private $return_type;
	private $arModeStack;

	/**
	 * Имя актуального (последнего) модуля, для которого возникло обрабатываемое событие
	 *
	 * @var string
	 */
	protected $actual_module;

	/**
	 * Имя актуального (последнего) события, по которому выполняются обработчики
	 *
	 * @var string
	 */
	protected $actual_event;

	/**
	 * Результат последнего выполнения метода CEventsHandler::Execute()
	 *
	 * @var boolean
	 */
	protected $actual_result;

	/**
	 * Конструктор объекта класса, осуществляет подключение файла с конфигурационным массивом
	 * устанавливает значения полей по умолчанию
	 *
	 * Конфигурационный массив должен быть с именем $KS_EVENTS
	 *
	 * @param string $config_file Конфигурационный файл событий, содержащий массив с именами файлов и функций,
	 * выполняющих соответствующие действия для каждого модуля
	 * @param string $return_type Определяет вид ответа метода CEventsHandler::Execute())
	 */
	function __construct($config_file, $return_type = 'overall')
	{
		/* Проверка существования константы пути к модулям */
		if (!defined('MODULES_DIR'))
			throw new CError("Unspecified constant path to modules");
		$this->return_type = $return_type;			// Указываем тип данных, возвращаемых методом CEventsHandler::Execute()
		try
		{
			$this->LoadFromFile($config_file);
		}
		catch(CError $e)
		{
			throw $e;
		}
	}

	/**
	 * Метод добавляет событие в дерево событий
	 */
	function AddEvent($module,$event,$handler)
	{
		$handler['hSave']=false;
		$this->events[$module][$event][]=$handler;
	}

	/**
	 * Метод, возвращающий имя модуля, для которого возникло последнее событие
	 */
	function GetLastEventModule()
	{
		if (strlen($this->actual_module))
			return $this->actual_module;
		return false;
	}

	/**
	 * Метод, возвращающий имя последнего события, по которому выполнялись обработчики
	 */
	function GetLastEvent()
	{
		if (strlen($this->actual_event))
			return $this->actual_event;
		return false;
	}

	/**
	 * Метод, возвращающий результат обработки последнего события
	 */
	function GetLastResult()
	{
		if (isset($this->actual_result))
			return $this->actual_result;
		return false;
	}

	public function SetMode($mode)
	{
		if($mode=='particular') $this->return_type='particular'; else $this->return_type='overall';
	}

	public function PushMode()
	{
		array_push($this->arModeStack,$this->return_type);
	}

	public function PopMode()
	{
		$this->return_type=array_pop($this->arModeStack);
	}

	/**
	 * Метод проверяет наличие обработчика для указанного модуля и события
	 * В целом проверяется реальное наличие обработчика (т.е. возможность его вызвать),
	 * а не только его регистрация в файле обработчиков
	 */
	public function HasHandler($module,$event)
	{
		if(array_key_exists($module,$this->events))
		{
			if(array_key_exists($event,$this->events[$module]))
			{
				$arHandlers=$this->events[$module][$event];
				$bHasFile=false;
				$bHasFunc=false;
				foreach($arHandlers as $arHandler)
				{
					if (isset($arHandler['hFile']) && $arHandler['hFile']!='')
					{
						/* Полное имя файла-обработчика */
						$hFileName = MODULES_DIR . '/' . $this->actual_module . '/events/' . $arHandler['hFile'];
						if (file_exists($hFileName))
						{
							$bHasFile=true;
						}
						elseif(file_exists($arHandler['hFile']))
						{
							$bHasFile=true;
						}
					}
					/* Указана функция-обработчик */
					if (isset($arHandler['hFunc']))
					{
						if(is_array($arHandler['hFunc']))
						{
							if(is_object($arHandler['hFunc'][0]))
							{
								$bHasFunc = true;
							}
							elseif(is_string($arHandler['hFunc'][0]))
							{
								if(class_exists($arHandler['hFunc'][0]))
									if(method_exists($arHandler['hFunc'][0], $arHandler['hFunc'][1]))
										$bHasFunc = true;
							}
						}
						elseif (function_exists($arHandler['hFunc']))
						{
							$bHasFunc = true;
						}
					}
					if($bHasFile || $bHasFunc) return true;
				}
			}
		}
		return false;
	}

	/**
	 * Метод, выполняющий указанные в конфигурационном массиве действия для данного модуля по установленному событию
	 *
	 * Тип ответа определяется полем CEventsHandler::$return_type
	 *
	 * Добавлена настройка обработчика bOnce - если установлено в true то после выполнения обработчик будет удален из списка
	 * Добавлена переменная обработчика $arParams - содержит дополнительные данные которые надо передать функции обработчику
	 * Добавлена поддержка сложного обработчика
	 *
	 * @param string $moduleName Имя (идентификатор) модуля, для которого будут выполняться обработчики события
	 * @param string $onEvent Возникшее событие, которое следует обработать
	 * @param mixed &$hParams Ссылка на параметры, которые необходимо передать обработчикам
	 * @return mixed
	 */
	function Execute($moduleName, $onEvent, &$hParams = false)
	{
		/* Установка имён модуля и события, с которыми будет производиться работа
		   После выполнения обработчиков по этим полям можно будет узнать последний модуль и последнее событие */
		$this->actual_module = $moduleName;
		$this->actual_event = $onEvent;

		/* Формирование массива с параметрами обработчиков и результатами их выполнения
		   Каждый элемент массива представляет собой массив со следующими возможными ключам:
		   'hFile' (имя файла-обработчика);
		   'hFunc' (имя функции-обработчика);
		   'executed' */
		$handlers = array();
		/* Поиск и выполнение соответствующих событию файлов-обработчиков и/или функций-обработчиков для данного модуля */
		if (isset($this->events[$this->actual_module]))
			if (isset($this->events[$this->actual_module][$this->actual_event]))
			{
				$handlers = $this->events[$this->actual_module][$this->actual_event];
				if (is_array($handlers))
					if (count($handlers))
						foreach ($handlers as $handlerKey => $handler)
						{
							/*Проверяем выполнять обработку один раз или много*/
							if(array_key_exists('bOnce',$handler) && $handler['bOnce']==true)
							{
								unset($this->events[$this->actual_module][$this->actual_event][$handlerKey]);
							}
							/* Если функция-обработчик задана и существует, то она выполняется,
							   и результатом её работы должна быть переменная типа boolean.
							   Если же функция-обработчик не задана, то переменная типа boolean должна быть
							   определена с именем $hResult самим файлом-обработчиком
							   Для возвращения конкретных данных можно использовать массив $hParams,
							   который передаётся по ссылке */
							$handlers[$handlerKey]['executed'] = false;		// 1, если обработчик успешно выполнен
							$hResult = false;
							$hFileExists = false;
							$hFuncExists = false;
							/* Указан файл-обработчик */
							if (isset($handler['hFile']))
							{
								if (strlen($handler['hFile']))
								{
									/* Полное имя файла-обработчика */
									$hFileName = MODULES_DIR . '/' . $this->actual_module . '/events/' . $handler['hFile'];
									if (file_exists($hFileName))
									{
										$hFileExists = true;
										include_once($hFileName);	// Подключаем файл-обработчик, если не был подключен ранее
									}
									elseif(file_exists($handler['hFile']))
									{
										$hFileExists = true;
										include_once($handler['hFile']);	// Подключаем файл-обработчик, если не был подключен ранее
									}
								}
							}
							/* Указана функция-обработчик */
							if (isset($handler['hFunc']))
							{
								if(is_array($handler['hFunc']))
								{
									if(is_object($handler['hFunc'][0]))
									{
										$hFuncExists = true;
									}
									elseif(is_string($handler['hFunc'][0]))
									{
										if(class_exists($handler['hFunc'][0]))
											if(method_exists($handler['hFunc'][0], $handler['hFunc'][1]))
												$hFuncExists = true;
									}

									if($hFuncExists)
									{
										if (!is_array($hParams) && !$hParams)
											$hResult = call_user_func($handler['hFunc']);
										elseif(!array_key_exists('arParams',$handler) || !is_array($handler['arParams']))
											$hResult = call_user_func($handler['hFunc'], $hParams);
										else
											$hResult = call_user_func($handler['hFunc'], $hParams,$handler['arParams']);
									}
								}
								elseif (function_exists($handler['hFunc']))
								{
									$hFuncExists = true;
									if (!is_array($hParams) && !$hParams)
										$hResult = call_user_func($handler['hFunc']);
									elseif(!array_key_exists('arParams',$handler) || !is_array($handler['arParams']))
										$hResult = call_user_func($handler['hFunc'], $hParams);
									elseif(array_key_exists('arParams',$handler) && is_array($handler['arParams']))
										$hResult = call_user_func($handler['hFunc'], $hParams,$handler['arParams']);
								}
							}
							/* Если обработчик не был задан, то метод должен вернуть корректный результат */
							if (!$hFileExists && !$hFuncExists)
							{
								throw new CError('SYSTEM_HANDLER_NOT_FOUND',1,(is_array($handler['hFunc'])?join(':',$handler['hFunc']):$handler['hFunc']).($handler['hFile']!=''?$handler['hFile']:''));
								$hResult = true;
							}
							/* Проверка правильности результата выполнения файла-обработчика или функции-обработчика */
							if ($hResult>0)
								$handlers[$handlerKey]['executed'] = $hResult;
						}
			}
		/* В зависимости от установленного типа возвращаемого результата формируем выходные данные */
		if ($this->return_type === 'particular')
		{
			$this->actual_result = $handlers;
			return $this->actual_result;
		}
		if (count($handlers))
		{
			$execResult = true;		// Общий результат выполнения всех обработчиков события
			foreach ($handlers as $handlerKey => $handler)
				$execResult *= $handler['executed'];
			$this->actual_result = $execResult;
			return $this->actual_result;
		}
		/* Для данного модуля не найдено ни одного обработчика */
		return true;
	}

	/**
	 * Метод выполняет загрузку списка событий из файла
	 */
	function LoadFromFile($filename="")
	{
		$this->config_file = $filename;			// Устанавливаем имя конфигурационного файла
		if (!file_exists($this->config_file))
			throw new CError("SYSTEM_CONFIG_NOT_FOUND", 0, $this->config_file);
		include_once($this->config_file);			// Попытка подключения конфигурационного файла
		/* Проверка существования конфигурационного массива */
		if (isset($KS_EVENTS))
			if (is_array($KS_EVENTS))
			{
				$this->events = $KS_EVENTS;		// Установка поля конфигурационного массива
				$this->actual_module = '';
				$this->actual_event = '';
				$this->actual_result = null;
				return true;
			}
		return false;
	}

	/**
	 * Метод выполняет сохранение обработчиков событий в файл
	 * @author blade39 <blade39@kolosstudio.ru>
	 */
	function SaveToFile($filename="")
	{
		if($filename=='')
		{
			$filename=CONFIG_DIR.'/events_config.php';
		}

		$sFilecontent="<?php\n".
		"\n/**
			 * Конфигурационный файл обработчиков событий
			*
			* В этом файле должен быть определён конфигурационный массив обработчиков событий \$KS_EVENTS,
			* структура которого описана в классе CEventsHandler
			*
			* @filesource events_config.php
			* @author north-e <pushkov@kolosstudio.ru>
			* @version 0.1
			* @since 03.03.2009
			* Файл сгенерирован автоматически
			*/\n".
		'$KS_EVENTS = array'."\n(\n";
		foreach($this->events as $module=>$arEvents)
		{
			$sFilecontent.="'$module'=>array\n(\n";
			foreach($arEvents as $sEvent=>$arActions)
			{
				$sFilecontent.="'$sEvent'=>array\n(\n";
				$arWasActions=array();
				foreach($arActions as $arAction)
				{
					if(!array_key_exists('hSave',$arAction)&&!array_key_exists('bOnce',$arAction)&&(!in_array($arAction,$arWasActions)))
					{
						$sFilecontent.="array('hFile'=>'".$arAction['hFile']."','hFunc'=>";
						if(is_array($arAction['hFunc']))
						{
							$sFilecontent.="array('".$arAction['hFunc'][0]."','".$arAction['hFunc'][1]."')";
						}
						else
						{
							$sFilecontent.="'".$arAction['hFunc']."'";
						}
						$sFilecontent.="),\n";
						$arWasActions[]=$arAction;
					}
				}
				$sFilecontent.="\n),\n";
			}
			$sFilecontent.="\n),\n";
		}
		$sFilecontent.=");?>";
		return file_put_contents($filename,$sFilecontent);
	}

	function AddStaticEvent($module,$event,$arParams)
	{
		//$arParams['hSave']=true;
		$this->events[$module][$event][]=$arParams;
	}
 }

?>