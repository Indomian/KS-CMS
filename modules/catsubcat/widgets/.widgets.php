<?php

/**
 * Файл с описанием виджетов и параметром, указывающем на корректность виджетов
 * 
 * @filesource .widgets.php
 * @author north-e <pushkov@kolosstudio.ru>
 * 
 * @version 1.1
 * @since 14.05.2009
 * 
 * Добавлена подсказка по переменным, используемым в шаблоне виджета
 */

$arWidgets = array
(
	"CatCategory" => array
	(
		"name" => "Вывод раздела",
		"descr" => "Виджет выводит содержимое выбранного текстового раздела",
		"has_widget" => 1,
		"help" => "В данном виджете может использоваться переменная:<br />" .
			"<b>{\$data.main_content}</b> - массив с параметрами отображаемого раздела, имеет следующие ключи:<br />" .
			"<i>id</i> - числовой идентификатор текстового раздела;<br />" .
			"<i>text_ident</i> - текстовый идентификатор раздела;<br />" .
			"<i>parent_id</i> - числовой идентификатор родительского раздела (если равен нулю, то текущий раздел является корневым);<br />" .
			"<i>title</i> - заголовок раздела;<br />" .
			"<i>description</i> - описание раздела;<br />" .
			"<i>content</i> - содержимое раздела;<br />" .
			"<i>img</i> - имя файла изображения, загруженного к разделу;<br />" .
			"<i>date_add</i> - дата добавления раздела в формате timestamp.<br />" .
			"<i>date</i> - дата добавления раздела в формате ДД.ММ.ГГГГ;<br />" .
			"<i>views_count</i> - количество просмотров данной страницы;</li>" .
			"<br />" .
			"Для того, что отобразить содержимое раздела, достаточно написать <b>{\$data.main_content.content}</b>. Обращение к какому-либо другому полю массива аналогично.<br />" .
			"Изображения для текстовых разделов сохраняются в каталоге <i>/uploads/</i>."
	),
	"CatElement" => array
	(
		"name" => "Вывод страницы",
		"descr" => "Виджет выводит содержимое выбранного текстовой страницы",
		"has_widget" => 1,
		"help" => "В данном виджете может использоваться переменная:<br />" .
			"<b>{\$data.main_content}</b> - массив с параметрами отображаемой страницы, имеет следующие ключи:<br />" .
			"<i>id</i> - числовой идентификатор текстовой страницы;<br />" .
			"<i>text_ident</i> - текстовый идентификатор страницы;<br />" .
			"<i>parent_id</i> - числовой идентификатор родительского раздела (если равен нулю, то текущий раздел является корневым);<br />" .
			"<i>title</i> - заголовок страницы;<br />" .
			"<i>description</i> - описание страницы;<br />" .
			"<i>content</i> - содержимое страницы;<br />" .
			"<i>img</i> - имя файла изображения, загруженного к странице (изображения хранятся в папке /uploads/, т.е. для того, чтобы задать верный путь в изображению нужно ввести /uploads/{\$announce_item.img}).<br /><br />" .
			"<i>date_add</i> - дата добавления страницы в формате timestamp.<br />" .
			"<i>date</i> - дата добавления страницы в формате ДД.ММ.ГГГГ;<br />" .
			"<i>views_count</i> - количество просмотров страницы.<br />" .
			"<br />" .
			"Для того, что отобразить содержимое страницы, достаточно написать <b>{\$data.main_content.content}</b>. Обращение к какому-либо другому полю массива аналогично.<br />" .
			"Изображения для текстовых страниц сохраняются в каталоге <i>/uploads/</i>."
	),
	"CatSubcategoriesList" => array
	(
		"name" => "Список вложенных разделов",
		"descr" => "Виджет выводит список вложенных разделов для указанного раздела",
		"has_widget" => 1
	),
	"CatAnnounce" => array
	(
		"name" => "Анонс",
		"descr" => "Виджет выводит анонс нескольких текстовых страниц",
		"has_widget" => 1,
		"help" => "В данном виджете могут использоваться следующие переменные:<br />" .
				"<b>{\$announces}</b> - массив анонсируемых текстовых элементов из заданного раздела, имеет следующие ключи:<br />" .
				"<i>id</i> - числовой идентификатор элемента;<br />" .
				"<i>parent_id</i> - числовой идентификатор текстового раздела, к которому относится анонсируемый элемент;<br />" .
				"<i>text_ident</i> - текстовый идентификатор элемента;<br />" .
				"<i>full_path</i> - полный url-путь к разделу, к которому относится анонсируемый элемент;<br />" .
				"<i>title</i> - заголовок элемента;<br />" .
				"<i>description</i> - описание элемента;<br />" .
				"<i>img</i> - имя файла изображения, загруженного к элементу (изображения хранятся в папке /uploads/, т.е. для того, чтобы задать верный путь в изображению нужно ввести /uploads/{\$announce_item.img}).<br />" .
				"<i>date_add</i> - дата добавления элемента в формате timestamp;<br />" .
				"<i>date</i> - дата добавления элемента в формате ДД.ММ.ГГГГ;<br />" .
				"<i>views_count</i> - количество просмотров элемента;<br />" .
				"<b>{\$announces_count}</b> - количество отобранных для анонсирования текстовых элементов;<br />" .
				"<b>{\$announce_error}</b> - текстовое сообщение об ошибке в случае неудачной попытки получения анонсируемых элементов;<br />" .
				"<b>{\$orderby}</b> - принцип отбора анонсируемых текстовых элементов, может принимать следующие значения:<br />" .
				"<i>date_add</i> - по дате добавления;<br />" .
				"<i>views_count</i> - по наибольшему количеству просмотров;<br />" .
				"<i>random</i> - в случайном порядке.<br />" .
				"<br />" .
				"Перебор всех анонсируемых элементов можно осуществить в цикле:<br />" .
				"<b>{foreach from=\$announces key=announce_key item=announce_item}<br />" .
				"...<br />" .
				"{/foreach}<br /></b>" .
				"При этом обратиться к значению какого-либо поля элемента можно как <b>{\$announce_item.id}</b>, а ссылку на страницу элемента можно записать в виде <b>{\$announce_item.full_path}{\$announce_item.text_ident}.html</b>."
	),
	"CatCalendar"=>array(
		"name"=>"Календарь",
		"descr"=>"Виджет выводит календарь текстовых элементов. С навигацией по месяцам.",
		"has_widget"=>1,
		'help'=>'В шаблоне виджета "Календарь текстовых страниц" можно использовать следующие переменные:
<ul>
	<li><b>{$data.month}></b> - информация о текущем месяце, обладает следующими полями:
		<ul>
			<li><i>num</i> - порядковый номер месяца в году;</li>
			<li><i>title</i> - название месяца.</li>
		</ul>
	</li>
	<li><b>{$data.year}</b> - текущий год.</li>
	<li><b>{$data.weeks}</b> - массив недель текущего месяца, каждая неделя содержит по 7 записей
	о днях, каждая запись о дне обладает следующими полями:
		<ul>
			<li><i>day</i> - число (номер дня) в месяце;</li>
			<li><i>cur</i> - флаг указывающий что этот день относится к текущему месяцу;</li>
			<li><i>count</i> - количество записей за этот день;</li>
		</ul>
	</li>
	<li><b>{$data.filterPage}<b/> - адрес на который совершать переход по ссылкам в календаре;</li>
</ul>'
	)
);
?>