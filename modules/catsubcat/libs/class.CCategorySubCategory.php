<?php
/**
 * @filesource catsubcat/libs/class.CCategorySubCategory.php
 * Контейнер для класса CCategorySubCategory
 * Файл проекта kolos-cms.
 *
 * @since 25.02.2010
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CRestorable.php';
/**
 * Родительский класс для модуля категорий, подкатегорий. В общем обычно абстрактен и сам по себе нигде не используется.
 * Рекомендуется не применять этот класс а пользоваться его потомками CElement, CCategory. Начиная с версии 2.0
 * присутствует поддержка пользовательских полей.
 * @version 2.1
 * @author blade39 <blade39@kolosstudio.ru>
 */
abstract class CCategorySubCategory extends CRestorable
{
	/**
	 * Метод генерирует уникальный ключ для записи
	 * @param array $arRecord массив по которому генерируется ключ записи
	 */
	function GenHash($arRecord)
	{
		return 1;
	}

	/**
	 * @copydoc CObject::_GenFileName()
	 * Возвращает md5-хеш полученного имени файла
	 */

	protected function _GenFileName($filename)
	{
		return md5($filename.time()).'.'.substr($filename,strrpos($filename,'.')+1);
	}

	/**
	 * Метод выполняется перед сохранением записи
	 * @param $arData
	 */
	protected function _BeforeSave(&$arData)
	{
		global $KS_MODULES;
		if(array_key_exists('text_ident',$arData))
		{
			$arData['text_ident']=Translit($arData['text_ident']);
			$arData['text_ident']=preg_replace('#\s#i','_',$arData['text_ident']);
			$arData['text_ident']=preg_replace('#[^a-z0-9_\-]#i','',$arData['text_ident']);
			$length=$KS_MODULES->GetConfigVar('main','text_ident_length',20);
			$arData['text_ident']=substr($arData['text_ident'],0,$length);
			$arRow=$this->GetRecord(array('text_ident'=>$arData['text_ident'],'!id'=>$arData['id'],'parent_id'=>$arData['parent_id']));
			if(is_array($arRow)&&($arRow['id']>0))
			{
				//Уже есть запись с таким обрезанным идентификатором
				$code=substr($arData['text_ident'],0,$length-MAX_TEXT_IDENT_NUMBERS);
				$arFilter=array(
					'~text_ident'=>$code,
					'!id'=>$arRow['id'],
				);
				$arSort=array(
					'text_ident'=>'desc',
				);
				$iNext=1;
				if($arList=$this->GetList($arSort,$arFilter,100))
					foreach($arList as $arItem)
						if(preg_match('#^'.$code.'([0-9]{'.MAX_TEXT_IDENT_NUMBERS.','.MAX_TEXT_IDENT_NUMBERS.'})$#i',$arItem['text_ident'],$matches))
							$iNext=intval($matches[1])+1;
							break;
				$arData['text_ident']=$code.str_repeat(0,MAX_TEXT_IDENT_NUMBERS-strlen($iNext)).$iNext;
			}
			if(strlen($arData['text_ident'])<1) $arData['text_ident']=time();
		}
		if(!isset($arData['id']) || $arData['id']<0)
		{
			$arData['date_add']=time();
		}
		elseif($arData['id']==0)
		{
			$arData['text_ident']='';
		}
		$arData['date_edit']=time();
		return true;
	}

	/**
	 * Метод выполняет обновление записи. Если при обновлении не была указана дата обновления
	 * она будет определена автоматически.
	 * @param $id integer - номер записи для обновления
	 * @param $arFields array - массив со списком полей и их новых значений
	 */
	function Update($id,$arFields,$bFiltered=false)
	{
		if(in_array('date_edit',$this->arFields) && !array_key_exists('date_edit',$arFields))
			$arFields['date_edit']=time();
		return parent::Update($id,$arFields,$bFiltered);
	}
}

