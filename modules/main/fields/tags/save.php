<?php

include_once MODULES_DIR.'/search/libs/class.CSearchTags.php';
include MODULES_DIR.'/search/config.php';
$obSearch=new CSearchTags($MODULE_search_db_config['tags']);
$arTags=$obSearch->ExplodeTagString($value);
$sValue=join(', ',$arTags);

?>