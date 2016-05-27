<?
$sub = dims_load_securvalue('sub',dims_const::_DIMS_CHAR_INPUT,true,true);
if ($sub != module_wce::_PARAM_INFOS && $sub != module_wce::_PARAM_CONF && $sub != module_wce::_PARAM_EXECUTE)
	$sub = module_wce::_PARAM_INFOS;
require_once module_wce::getTemplatePath("parameters/sub_header.tpl.php");
switch($sub){
	default:
	case module_wce::_PARAM_INFOS:
		require_once module_wce::getTemplatePath("parameters/info_generales/controller.php");
		break;
	case module_wce::_PARAM_CONF:
		require_once module_wce::getTemplatePath("parameters/conf_avancee/controller.php");
		break;
	case module_wce::_PARAM_TOOLS:
		require_once module_wce::getTemplatePath("parameters/outils/controller.php");
		break;
	case module_wce::_PARAM_EXECUTE:
		require_once module_wce::getTemplatePath("parameters/execute/controller.php");
		break;
}
?>