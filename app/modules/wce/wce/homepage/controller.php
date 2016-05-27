<?php
$op = dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,false,false);
switch($op){
	default:
	case module_wce::_DEFAULT:
		require_once module_wce::getTemplatePath("homepage/left_bloc.tpl.php");
		require_once module_wce::getTemplatePath("homepage/center_bloc.tpl.php");
		break;
}
?>