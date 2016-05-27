<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);

switch($action){
	default:
	case module_wce::_PARAM_TOOLS_DEF:
		require_once module_wce::getTemplatePath("parameters/outils/accueil.tpl.php");
		break;
	case module_wce::_PARAM_TOOLS_IMPORT:

		break;
	case module_wce::_PARAM_TOOLS_EXPORT:

		break;
	case module_wce::_PARAM_TOOLS_SITEMAP:

		break;
}
?>