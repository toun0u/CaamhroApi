<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
		require_once module_wce::getTemplatePath('/list_articles/list_articles.tpl.php');
		break;
}
?>