<?php

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
include_once MODULES_DIR.'/interfaces/libs/class.CInterface.php';

/**
 * Класс интерфейсов, выполняет работу по выводу объектов. В данной реализации отрисовывает
 * только закладки, механизм работы - внутренний не зависит от вызова функций.
 */
class CTabs extends CInterface
{
	var $version;
	var $tabs;
	var $jsIncluded;
	private $iTimeStart;
	private $tabsStack;

	function __construct()
	{
		global $smarty;
		$this->version=1;
		$smarty->register_block("ksTabs", array($this,"_smarty_begin_tabs"));
		$smarty->register_block("ksTab", array($this,"_smarty_add_tab"));
		$smarty->register_block("ksRoll",array($this,"_smarty_add_roll"));
		$this->tabsStack=array();
		$this->iTimeStart=time();
		$this->jsIncluded=0;
	}

	function _smarty_add_roll($params,$content,&$smarty,$repeat)
	{
		if($content)
		{

		}
	}

	function _smarty_begin_tabs($params,$content, &$smarty, $repeat)
	{
		$tabsname=$params['NAME'].'_'.$this->iTimeStart;
		if($tabsname!=$this->tabs['NAME'])
		{
			array_push($this->tabsStack,$this->tabs);
			$this->tabs=array();
			$this->tabs['NAME']=$tabsname;
		}
		if ($content)
		{
			if(!is_array($this->tabs['ITEMS'])) return '';
			$header='<script type="text/javascript" src="/js/interfaces/tabs.js"></script>'."\n";
			$header.='<ul class="'.$params['head_class'].'" clear_after" id="'.$tabsname.'">';
	       	if(count($this->tabs['ITEMS'])==1)
        	{
        		$sClass=' class="one" ';
        	}
        	else
        	{
        		$sClass='';
        	}
        	$bHideTabs=$smarty->get_template_vars('isLight')==1;
        	$bHasHidden=false;
        	$bUserTab=false;
        	if(preg_match('#^'.$params['NAME'].'_[0-9]{10,10}_tab([0-9]+)$#i',$_COOKIE['lastSelectedTab'],$matches))
        	{
        		if($matches[1]<=count($this->tabs['ITEMS']))
        		{
        			$bUserTab=true;
        		}
        	}
        	//Проверяем не оказалась ли открываемая вкладка невидимой
        	if($bHideTabs)
        	{
        		$i=0;
        		foreach ($this->tabs['ITEMS'] as $sName=>$arItem)
        		{
        			if($arItem['hide']>0)
        			{
        				if(preg_match('#^'.$params['NAME'].'_[0-9]{10,10}_tab'.$i.'$#i',$_COOKIE['lastSelectedTab']))
						{
							$bUserTab=false;
						}
        			}
        			$i++;
				}
        	}
        	$arActiveTab=array();
        	$i=0;
        	foreach ($this->tabs['ITEMS'] as $sName=>$arItem)
        	{
        		if($bHideTabs&&($arItem['hide']>0))
        		{
        			$sStyle="hide";
        			$bHasHidden=true;
        		}
        		else $sStyle="";
				if(preg_match('#^'.$params['NAME'].'_[0-9]{10,10}_tab'.$i.'$#i',$_COOKIE['lastSelectedTab']))
				{
					if($sStyle!='')
					{
						$bUserTab=false;
						$header.='<li id="'.$tabsname.'_tab'.$i.'" class="'.$sStyle.'"><a href="#'.$tabsname.'_tab'.$i.'" rel="'.$tabsname.'_tab'.$i.'" ';
	        			$header.='><span'.$sClass.'>'.$sName.'</span></a></li>';
    	    			$arItem['content']=str_replace('#DISPLAY#',' style="display:none;" ',$arItem['content']);
    	    			$arItem['id']=$tabsname.'_tab'.$i;
					}
					else
					{
						$header.='<li id="'.$tabsname.'_tab'.$i.'" class="active '.$sStyle.'"><a href="#'.$tabsname.'_tab'.$i.'" rel="'.$tabsname.'_tab'.$i.'"';
						$header.='><span'.$sClass.'>'.$sName.'</span></a></li>';
						$arItem['content']=str_replace('#DISPLAY#','',$arItem['content']);
						$arItem['id']=$tabsname.'_tab'.$i;
						$arActiveTab=$arItem;
					}
				}
    			elseif (($arItem['SELECTED']==1) && (!$bUserTab))
    			{
    				$header.='<li id="'.$tabsname.'_tab'.$i.'" class="active '.$sStyle.'"><a href="#'.$tabsname.'_tab'.$i.'" rel="'.$tabsname.'_tab'.$i.'"';
					$header.='><span'.$sClass.'>'.$sName.'</span></a></li>';
					$arItem['content']=str_replace('#DISPLAY#','',$arItem['content']);
					$arItem['id']=$tabsname.'_tab'.$i;
					$arActiveTab=$arItem;
    			}
        		else
        		{
        			$header.='<li id="'.$tabsname.'_tab'.$i.'" class="'.$sStyle.'"><a href="#'.$tabsname.'_tab'.$i.'" rel="'.$tabsname.'_tab'.$i.'" ';
        			$header.='><span'.$sClass.'>'.$sName.'</span></a></li>';
        			$arItem['content']=str_replace('#DISPLAY#',' style="display:none;" ',$arItem['content']);
        			$arItem['id']=$tabsname.'_tab'.$i;
        		}
        		$i++;
        		$data.="\n ".$arItem['content'];
        	}
        	$header.='</ul>';
        	if($bHasHidden)
        		$header='<div class="showhide_tabs"><a href="#" onclick="return ShowTabs(\''.$tabsname.'\')">Показать дополнительные параметры</a></div>'.$header;
        	$data.="\n".'<script type="text/javascript">$(document).ready(function(){var obList=$("ul#'.$tabsname.'>li>a");';
			$i=0;
			foreach($this->tabs['ITEMS'] as $sName=>$arItem)
			{
				$data.="\n	$(obList.get($i)).bind('click',null,function(event){\nChangeActive(this.rel,'$tabsname');event.stopImmediatePropagation();";
				if($arItem['ONACTIVATE']!='')
			    	$data.=$arItem['ONACTIVATE'];
			    $data.="\nreturn false;});\n";
			    $i++;
			}
			$data.='});';
			if(is_array($arActiveTab) && ($arActiveTab['ONACTIVATE']!=''))
        	{
        		$data.="\n$(document).ready(function(){".$arActiveTab['ONACTIVATE'].";});";
        	}
        	$data.='</script>';
        	$this->tabs=array_pop($this->tabsStack);
        	$repeat=false;
			return $header.$data;
		}
	}

	function _smarty_add_tab($params,$content, &$smarty, $repeat)
	{
		if ($content)
		{
			$arResult['content']=$content;
			$arResult['name']=$params['NAME'];
			if ($params['selected']==1)
			{
				$arResult['SELECTED']=1;
			}
			else
			{
				$arResult['SELECTED']=0;
			}
			$arResult['content']='<div class="'.$params['class'].'" id="'.$this->tabs['NAME'].'_tab'.intval($this->tabs['COUNT']).'cont" #DISPLAY#>'.$content.'</div>';
			if($params['onActivate']!='')
			{
				$arResult['ONACTIVATE']=$params['onActivate'];
			}
			$arResult['hide']=intval($params['hide']);
			$this->tabs['ITEMS'][$params['NAME']]=$arResult;
			$this->tabs['COUNT']++;
			$repeat=false;
		}
		return '';
	}
}
