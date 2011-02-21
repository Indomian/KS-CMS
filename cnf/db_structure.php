<?php

/**
 * Автоматически созданный файл
 * Последнее изменение: 21.02.2011, 17:46:52
 */

$arStructure = array
(
	'interfaces_smilies' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'smile' => array
		(
			'Field' => "smile",
			'Type' => "char(50)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'img' => array
		(
			'Field' => "img",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'group' => array
		(
			'Field' => "group",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'navigation_menu_types' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'text_ident' => array
		(
			'Field' => "text_ident",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'name' => array
		(
			'Field' => "name",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'description' => array
		(
			'Field' => "description",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'script_name' => array
		(
			'Field' => "script_name",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'active' => array
		(
			'Field' => "active",
			'Type' => "tinyint(1)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		)
	),
	'navigation_menu_elements' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'orderation' => array
		(
			'Field' => "orderation",
			'Type' => "int(11)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'type_id' => array
		(
			'Field' => "type_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'parent_id' => array
		(
			'Field' => "parent_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'link' => array
		(
			'Field' => "link",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'anchor' => array
		(
			'Field' => "anchor",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'target' => array
		(
			'Field' => "target",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'img' => array
		(
			'Field' => "img",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'catsubcat_links' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'element_id' => array
		(
			'Field' => "element_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'category_id' => array
		(
			'Field' => "category_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		)
	),
	'catsubcat_catsubcat' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'active' => array
		(
			'Field' => "active",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'orderation' => array
		(
			'Field' => "orderation",
			'Type' => "int(11)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "1",
			'Extra' => ""
		),
		'text_ident' => array
		(
			'Field' => "text_ident",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'parent_id' => array
		(
			'Field' => "parent_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'title' => array
		(
			'Field' => "title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'description' => array
		(
			'Field' => "description",
			'Type' => "text",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'content' => array
		(
			'Field' => "content",
			'Type' => "text",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'img' => array
		(
			'Field' => "img",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'access_view' => array
		(
			'Field' => "access_view",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'access_edit' => array
		(
			'Field' => "access_edit",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "1",
			'Extra' => ""
		),
		'access_create' => array
		(
			'Field' => "access_create",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "1",
			'Extra' => ""
		),
		'date_add' => array
		(
			'Field' => "date_add",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'date_edit' => array
		(
			'Field' => "date_edit",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'views_count' => array
		(
			'Field' => "views_count",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'deleted' => array
		(
			'Field' => "deleted",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'seo_title' => array
		(
			'Field' => "seo_title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'seo_description' => array
		(
			'Field' => "seo_description",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'seo_keywords' => array
		(
			'Field' => "seo_keywords",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'catsubcat_element' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'active' => array
		(
			'Field' => "active",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'orderation' => array
		(
			'Field' => "orderation",
			'Type' => "int(11)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "1",
			'Extra' => ""
		),
		'text_ident' => array
		(
			'Field' => "text_ident",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'parent_id' => array
		(
			'Field' => "parent_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'title' => array
		(
			'Field' => "title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'description' => array
		(
			'Field' => "description",
			'Type' => "text",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'content' => array
		(
			'Field' => "content",
			'Type' => "text",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'img' => array
		(
			'Field' => "img",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'access_view' => array
		(
			'Field' => "access_view",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'access_edit' => array
		(
			'Field' => "access_edit",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "1",
			'Extra' => ""
		),
		'date_add' => array
		(
			'Field' => "date_add",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'date_edit' => array
		(
			'Field' => "date_edit",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'views_count' => array
		(
			'Field' => "views_count",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'deleted' => array
		(
			'Field' => "deleted",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'seo_title' => array
		(
			'Field' => "seo_title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'seo_description' => array
		(
			'Field' => "seo_description",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'seo_keywords' => array
		(
			'Field' => "seo_keywords",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'users' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'img' => array
		(
			'Field' => "img",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'password' => array
		(
			'Field' => "password",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'active' => array
		(
			'Field' => "active",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'date_register' => array
		(
			'Field' => "date_register",
			'Type' => "int(11)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "1",
			'Extra' => ""
		),
		'title' => array
		(
			'Field' => "title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'email' => array
		(
			'Field' => "email",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'last_visit' => array
		(
			'Field' => "last_visit",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'number_of_log_tries' => array
		(
			'Field' => "number_of_log_tries",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'pwd_updated' => array
		(
			'Field' => "pwd_updated",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'blocked_from' => array
		(
			'Field' => "blocked_from",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'blocked_till' => array
		(
			'Field' => "blocked_till",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'code' => array
		(
			'Field' => "code",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'new_email' => array
		(
			'Field' => "new_email",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'user_vars' => array
		(
			'Field' => "user_vars",
			'Type' => "text",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'main_modules' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'name' => array
		(
			'Field' => "name",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'URL_ident' => array
		(
			'Field' => "URL_ident",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'directory' => array
		(
			'Field' => "directory",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'include_global_template' => array
		(
			'Field' => "include_global_template",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'active' => array
		(
			'Field' => "active",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'orderation' => array
		(
			'Field' => "orderation",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'hook_up' => array
		(
			'Field' => "hook_up",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'allow_url_edit' => array
		(
			'Field' => "allow_url_edit",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		)
	),
	'main_path_to_template' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'url_path' => array
		(
			'Field' => "url_path",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'template_path' => array
		(
			'Field' => "template_path",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'orderation' => array
		(
			'Field' => "orderation",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'type' => array
		(
			'Field' => "type",
			'Type' => "char(10)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'function1' => array
		(
			'Field' => "function1",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'function2' => array
		(
			'Field' => "function2",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'main_fields' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'title' => array
		(
			'Field' => "title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'description' => array
		(
			'Field' => "description",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'script' => array
		(
			'Field' => "script",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'module' => array
		(
			'Field' => "module",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'type' => array
		(
			'Field' => "type",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'default' => array
		(
			'Field' => "default",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'option_1' => array
		(
			'Field' => "option_1",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'option_2' => array
		(
			'Field' => "option_2",
			'Type' => "text",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'main_events' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'title' => array
		(
			'Field' => "title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'content' => array
		(
			'Field' => "content",
			'Type' => "text",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'date_add' => array
		(
			'Field' => "date_add",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'date_end' => array
		(
			'Field' => "date_end",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'author' => array
		(
			'Field' => "author",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'address' => array
		(
			'Field' => "address",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'status' => array
		(
			'Field' => "status",
			'Type' => "enum('new', 'inwork', 'done', 'error')",
			'Null' => "NO",
			'Key' => "",
			'Default' => "new",
			'Extra' => ""
		),
		'type' => array
		(
			'Field' => "type",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'format' => array
		(
			'Field' => "format",
			'Type' => "char(30)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'encoding' => array
		(
			'Field' => "encoding",
			'Type' => "char(30)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'name_to' => array
		(
			'Field' => "name_to",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'email_from' => array
		(
			'Field' => "email_from",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'main_eventtemplates' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'file_id' => array
		(
			'Field' => "file_id",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'title' => array
		(
			'Field' => "title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'address' => array
		(
			'Field' => "address",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'copy' => array
		(
			'Field' => "copy",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		)
	),
	'usergroups_levels' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'group_id' => array
		(
			'Field' => "group_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'module' => array
		(
			'Field' => "module",
			'Type' => "char(64)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'level' => array
		(
			'Field' => "level",
			'Type' => "tinyint(2) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		)
	),
	'usergroups' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'level' => array
		(
			'Field' => "level",
			'Type' => "smallint(3) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'title' => array
		(
			'Field' => "title",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'description' => array
		(
			'Field' => "description",
			'Type' => "char(255)",
			'Null' => "NO",
			'Key' => "",
			'Default' => "",
			'Extra' => ""
		),
		'number_of_log_tries' => array
		(
			'Field' => "number_of_log_tries",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'undeletable' => array
		(
			'Field' => "undeletable",
			'Type' => "tinyint(1) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		)
	),
	'users_grouplinks' => array
	(
		'id' => array
		(
			'Field' => "id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "PRI",
			'Default' => "0",
			'Extra' => "auto_increment"
		),
		'group_id' => array
		(
			'Field' => "group_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'user_id' => array
		(
			'Field' => "user_id",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'date_start' => array
		(
			'Field' => "date_start",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		),
		'date_end' => array
		(
			'Field' => "date_end",
			'Type' => "int(11) unsigned",
			'Null' => "NO",
			'Key' => "",
			'Default' => "0",
			'Extra' => ""
		)
	)
);

?>