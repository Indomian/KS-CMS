<?php

/**
 * Файл-обработчик событий инициализации модуля main для управления действиями пользователей
 * 
 * Функции-обработчики событий могут принимать ссылку на массив с параметрами $hParams
 * и должны возвращать флаг успешности выполнения типа boolean
 * 
 * @filesource user_handlers.php
 * @author north-e <pushkov@kolosstudio.ru>
 * @version 0.1
 * @since 03.03.2009
 */

/**
 * Функция-обработчик события перед инициализацией объекта класса CUser
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnBeforeInit(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события при инициализации объекта класса CUser
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnInit(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события при обновлении времени жизни сессии пользователя
 * 
 * @param mixed &$hParams Ссылка на объект класса CUser
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnSessionUpdate(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события перед активацией пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnBeforeActivate(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события при активации пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnActivate(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события перед залогиниванием пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnBeforeLogin(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события при залогинивании пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnLogin(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события перед разлогиниванием пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnBeforeLogout(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события при разлогинивании пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnLogout(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события перед созданием / редактированием пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnBeforeSave(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события при создании / редактировании пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnSave(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события перед удалением пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnBeforeDelete(&$hParams)
{
	return true;
}

/**
 * Функция-обработчик события при удалении пользователя
 * 
 * @param mixed &$hParams Ссылка на параметры, с которыми будет работать функция
 * @return boolean Флаг успешности выполнения функции
 */
function usersOnDelete(&$hParams)
{
	return true;
}
 
?>
