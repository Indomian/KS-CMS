<?php
/*
 * CMS-local
 *
 * Created on 20.11.2008
 *
 * Developed by blade39
 *
 * список уровней доступа к главному модулю. Заполняется от 0 до 10
 *
 */

$arLevels=array(
	0=>$this->GetText('access_full'),
	//1=>$this->GetText('access_check_php'),
	2=>$this->GetText('access_groups_and_users'),
	3=>$this->GetText('access_users'),
	6=>$this->GetText('access_mail_templates'),
	//7=>$this->GetText('access_templates'),
	8=>$this->GetText('access_fields'),
	9=>$this->GetText('access_view_users'),
	10=>$this->GetText('access_denied'),
);

