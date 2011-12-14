<?php
/**
 * @filesource main/libs/class.CBaseObject.php
 * Файл контейнер для класса абстрактного базового объекта
 * Файл проекта Update Server Dev.
 *
 * @since 17.02.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.7
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Класс CBaseObject - абстрактный класс от которого происходит наследование всех других классов.
 */
abstract class CBaseObject
{
	/**
	 * Вспомагательный метод предназначен исключительно для дебага
	 * выводит стек вызова с параметрами функций.
	 * TODO: доработать нормальный вывод информации
	 */
	public function callStack(){
		try{
			throw new Exception();
		}catch (Exception $e){
			$call = $e->getTrace();
			$error = array();
			foreach($call as $index => $arr){
				$error[$index]['file'] = $arr['file'].'('.$arr['line'].')';
				$error[$index]['function'] = $arr['class'].$arr['type'].$arr['function'];
				$error[$index]['param'] = $arr['args'];
			}
			unset($error[0]);
			$error = array_values($error);
			var_dump($error);
			die;
		}
	}
}

