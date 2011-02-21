<?php

/**
 * Виджет, предоставляющий пользователям возможность голосовать за материалы сайта
 *
 * @filesource function.RatingVote.php
 * @author North-E <pushkov@kolosstudio.ru>, BlaDe39 <blade39@kolosstudio.ru>
 * @version 1.0
 * @since 03.06.2009
 */

/**
 * Функция, выполняющая работу виджета в пользовательской части, в качестве результата возвращает шаблон
 *
 * @param array $params Массив входных параметров виджета
 * @param object &$subsmarty Ссылка на объект Смарти
 * @return string Шаблон
 */
function smarty_function_RatingVote($params, &$subsmarty)
{
	global $global_template,$KS_MODULES;

	/* Подключаем поддержку дополнительных полей */
	include_once(MODULES_DIR . '/main/libs/class.CFields.php');

	/* Включаем поддержку работу в режиме ajax */
	include_once(MODULES_DIR . '/interfaces/libs/class.CAjax.php');

	/* Инициализируем режим ajax, если указан соответствующий параметр */
	if ($params['isAjax'] == 'Y')
	{
		/* Ключ о том, это ajax-запрос или нет */
		$oldAjax = false;
		$obAjax = new CAjax('RatingVote', $params);
		if (array_key_exists('ajaxMode', $_GET))
		{
			if ($obAjax->CheckHash($_GET['ajaxMode']))
			{
				$oldAjax = true;
				ob_clean();
			}
			else
				return '';
		}
	}

	/* Проверка прав */
	/* ------------- */

	/* Получаем параметры, переданные виджету */
	$rate_field = $params["rate_field"];
	$rate_module = $params["rate_module"];
	$material_type = $params["material_type"];
	$material_id = intval($params["material_id"]);
	$votelife = intval($params["votelife"]);
	$value=$params['value'];

	/* По id пользовательского поля определяем значение */
	$obFields = new CFields();
	$arFilter = array("title" => $rate_field, "script" => "rating", "module" => $rate_module, "type" => $material_type);
	$arRatingField = $obFields->GetRecord($arFilter);
	if(!is_array($arRatingField)) throw new CError('INTERFACES_RATING_FIELD_NOT_FOUND',0,$rate_field);
	/* Если уже голосовали, то будем работать с имеющимися данными */
	if ($value!='')
	{
		$rating_expl = explode("|", str_replace(",", ".", $value));
		$current_rating = floatval($rating_expl[0]);
		$current_count = intval($rating_expl[1]);
	}
	else
	{
		$current_rating = $arRatingField['default'];
		$current_count = 1;
	}

	/* Ищем печеньку */
	$voted = 0;
	$fieldId=md5($rate_field . "_" . $rate_module . "_" . $material_type . "_" . $material_id);
	$cookie_name = "ksrate_".$fieldId;
	$mark_key="material_mark_" .$fieldId;
	if (isset($_COOKIE[$cookie_name])) $voted = 1;
	/* Если пользователь голосует, то пытаемся обработать его результат */
	if(array_key_exists($mark_key,$_REQUEST)&&($voted==0))
	{
		/* Получаем оценку пользователя и проверяем на правильность */
		$material_mark = intval($_REQUEST[$mark_key]);
		//Если считаем рейтинг
		if($arRatingField['option_2']=='rate')
		{
			if($arRatingField['option_1']>0)
				if($material_mark>$arRatingField['option_1']) throw new CError('INTERFACES_RATING_TOO_BIG');

			$current_rating = round(($current_rating * $current_count + $material_mark) / ($current_count + 1), 5);
			$current_count++;
			$new_rating_line = implode("|", array($current_rating, $current_count));
		}
		else
		{
			$new_rating_line = ++$current_rating;
		}

		/* Сохраняем */
		$obFieldsValues=new CFieldsValues();
		if ($obFieldsValues->SetValue($material_id,$arRatingField['id'],$new_rating_line))
		{
			$voted=1;
			if (!setcookie($cookie_name, $material_mark, time() + 60 * 60 * 24 * $votelife))
			{
				throw new CError('INTERFACES_RATING_COOKIE_ERROR');
			}
		}
	}

	/* Формируем массив оценок для Смарти (значением является имя класса css для каждой из оценок) */
	$marks = array("1" => "one", "2" => "two", "3" => "three", "4" => "four", "5" => "five");
	$subsmarty->assign("marks", $marks);
	$subsmarty->assign('markId',$mark_key);
	$subsmarty->assign("current_rating_stars", round($current_rating));
	$subsmarty->assign("current_rating_string", str_replace(".", ",", $current_rating));
	$subsmarty->assign("current_count", $current_count);
	$subsmarty->assign('current_rating',$current_rating);
	$subsmarty->assign('mode',$arRatingField['option_2']);
	$subsmarty->assign("voted", $voted);

	/* Поиск шаблона для виджета и возвращение результата */
	$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/interfaces/RatingVote',$params['global_template'],$params['tpl']);
	/* Завершение работы в режиме ajax */
	if ($params['isAjax'] == 'Y') $sResult = $obAjax->GetCode($sResult, $oldAjax);
	if ($oldAjax)
	{
		echo $sResult;
		die();
	}
	return $sResult;
}

/**
 * Функция, предоставляющая данные для настройки виджета
 */
function widget_params_RatingVote($extra_params)
{
	global $KS_MODULES,$smarty;

	/* Подключаем поддержку дополнительных полей */
	include_once(MODULES_DIR . '/main/libs/class.CFields.php');

	/* Создаём объект для работы с дополнительными полями */
	$obFields = new CFields();

	/* Выбор модуля */
	$modules = $KS_MODULES->GetList(array("name" => "ASC"), array("!URL_ident" => ""));
	$rate_module_value = array();
	if (is_array($modules))
		foreach ($modules as $module_key => $module)
			$rate_module_value[$module["directory"]] = $module["name"];

	/* Выставляем имя модуля по умолчанию */
	if (count($rate_module_value))
	{
		$supported_modules = array_keys($rate_module_value);
		$default_module = $supported_modules[0];
		if (isset($extra_params["selected_module"]))
			$default_module = $extra_params["selected_module"];
	}

	$rate_module = array
	(
		"title" => "Выберите модуль, материалы которого должен оценивать виджет",
		"type" => "select",
		"value" => $rate_module_value,
		"onchange" => "nextStep('widget', 'wmod=interfaces&w=RatingVote', 'selected_module=' + this.value);"
	);
	if (isset($default_module))
		$rate_module["default_value"] = $default_module;

	/* Определение типа материалов для выбранного модуля */
	$arFilter = array("script" => "rating", "module" => $rate_module["default_value"]);
	$types_exist = $obFields->Count($arFilter, "type");
	$smarty->config_load("admin.conf","main_fields");

	$material_types = array();

	if(is_array($types_exist))
	{
		foreach($types_exist as $type=>$count)
		{
			$material_types[$type] = ($smarty->get_config_vars($type)?$smarty->get_config_vars($type):$type);
		}
	}

	/* Выставляем тип материала по умолчанию */
	if (count($material_types))
	{
		$supported_types = array_keys($material_types);
		$default_type = $supported_types[0];
		if (isset($extra_params["selected_type"]))
			$default_type = $extra_params["selected_type"];
	}

	$material_type = array
	(
		"title" => "Выберите тип материалов указанного модуля, которые должен оценивать виджет",
		"type" => "select",
		"value" => $material_types,
		"onchange" => "nextStep('widget', 'wmod=interfaces&w=RatingVote', 'selected_module=" . $rate_module["default_value"] . "&selected_type=' + this.value);"
	);
	if (isset($default_type))
		$material_type["default_value"] = $default_type;

	/* Выбор дополнительного поля */
	$arFilter["type"] = $material_type["default_value"];
	$rate_fields = $obFields->GetList(array("title" => "ASC"), $arFilter);
	$rate_fields_value = array();
	if (is_array($rate_fields))
		foreach ($rate_fields as $rate_field)
			$rate_fields_value[$rate_field["title"]] = $rate_field["description"];

	$rate_field = array
	(
		"title" => "Выберите дополнительное поле для оценки материалов",
		"type" => "select",
		"value" => $rate_fields_value
	);

	$arFields = array
	(
		"rate_module" => $rate_module,
		"material_type" => $material_type,
		"rate_field" => $rate_field,
		"material_id" => array
		(
			"title" => "Выберите id оцениваемого материала (при вставке в виджет отображения материала может быть использована переменная, содержащая id этого материала)",
			"type" => "text",
			"value" => ""
		),
		"votelife" => array
		(
			"title" => "Через какое время можно голосовать вновь",
			"type" => "select",
			"value" => array(0 => "Без ограничений", 1 => "1 день", 2 => "2 дня", 3 => "3 дня", 5 => "5 дней", 7 => "1 неделя", 14 => "2 недели", 21 => "3 недели", 30 => "1 месяц")
		),
		"isAjax" => array
		(
			"title" => "Режим AJAX",
			"type" => "select",
			"value" => array("Y" => "Да", "N" => "Нет")
		)
	);

	return array
	(
		"fields" => $arFields,
	);
}

?>