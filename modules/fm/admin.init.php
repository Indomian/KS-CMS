<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') )
	die("Hacking attempt!");

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/fm/libs/api/class.Helper.php';
require_once MODULES_DIR.'/fm/libs/api/class.Decorator.php';

require_once MODULES_DIR.'/fm/libs/views/interface.View.php';
require_once MODULES_DIR.'/fm/libs/views/class.ViewDir.php';

require_once MODULES_DIR.'/fm/libs/models/interface.Model.php';
require_once MODULES_DIR.'/fm/libs/models/class.ModelImage.php';
require_once MODULES_DIR.'/fm/libs/models/class.ModelDir.php';
require_once MODULES_DIR.'/fm/libs/models/class.ModelUploadForm.php';
require_once MODULES_DIR.'/fm/libs/models/class.ModelUploadResult.php';
require_once MODULES_DIR.'/fm/libs/models/class.ModelFile.php';

require_once MODULES_DIR.'/fm/libs/controllers/interface.Controller.php';
require_once MODULES_DIR.'/fm/libs/controllers/class.ControllerBase.php';
require_once MODULES_DIR.'/fm/libs/controllers/class.ControllerDir.php';
require_once MODULES_DIR.'/fm/libs/controllers/class.ControllerFile.php';