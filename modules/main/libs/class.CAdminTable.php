<?php
/**
 * Класс выполняет работу со страницами табличной структуры в система администрирования
 */
class CAdminTable extends CModuleAdmin
{
	protected $arColumns; /**<Массив описывающий колонки таблицы*/
	protected $obConfigParser; /**<Объект работы с файлами конфигурации*/
	protected $sTableName;	/**<Название таблицы*/

	function __construct($module_name,&$smarty,&$parent)
	{
		parent::__construct($module_name,$smarty,$parent);
		$this->arColumns=array();
		$this->obConfigParser=new CConfigParser($module_name);
		$this->obConfigParser->LoadConfig();
	}

	/**
	 * Метод выполняет подготовку параметров колонок
	 */
	protected function PrepareColumns()
	{
		$arCols=$this->obModules->GetConfigVar($this->module,'table'.$this->sTableName);
		foreach($this->arColumns as $key=>$arColumn)
		{
			if($arCols[$key]!='')
				$this->arColumns[$key]['show']=intval($arCols[$key]);
			else
				$this->arColumns[$key]['show']=intval($arColumn['default']);
		}
	}

	/**
	 * Метод выполняет отрисовку таблицы
	 */
	function Table()
	{
		$this->PrepareColumns();
	}

	/**
	 * Метод выполняет обработку операций
	 */
	function Run($action='')
	{
		global $KS_URL;
		$this->ParseAction($action);
		switch($this->sAction)
		{
			case 'confcols':
				//Выполняем конфигурацию колонок
				$this->PrepareColumns();
				$this->smarty->assign('columns',$this->arColumns);
				$this->obModules->AddChainItem('title_config_columns',$KS_URL->Url());
				$page='confcols';
			break;
			case 'savecols':
				if($_SERVER['REQUEST_METHOD']=='POST' && !array_key_exists('cancel',$_POST))
				{
					$arResult=array();
					foreach($this->arColumns as $key=>$arColumn)
					{
						$arResult[$key]=intval($_POST['show'][$key]);
					}
					$this->obConfigParser->Set('table'.$this->sTableName,$arResult);
					$this->obConfigParser->WriteConfig();
				}
				$KS_URL->redirect($KS_URL->Url(array('action')));
			break;
			default:
				$page=parent::Run();
		}
		return $page;
	}
}
