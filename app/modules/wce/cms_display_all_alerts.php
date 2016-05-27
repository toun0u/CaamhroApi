<?php
require_once DIMS_APP_PATH.'/templates/objects/alertes/AlertesController.php';
$smarty = new Smarty();

if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='')
	$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";

$smartypath=$_SESSION['dims']['smarty_path'];
$smarty->cache_dir = $smartypath.'/cache';
$smarty->config_dir = $smartypath.'/configs';
$controller = new AlertesController($obj['object_id'], $smarty);
$template=$controller->object->fields['template'].'_full';

if (!file_exists($smartypath.'/templates_c/'.$template_name.'/'.$template)) {
	dims_makedir ($smartypath."/templates_c/".$template_name."/".$template.'/', 0777, true);
}
$smarty->compile_dir = $smartypath."/templates_c/".$template_name."/".$template.'/';

$controller->addParam('mode', 'full_index');
$path = $controller->buildIHM();

if( ! is_null($path) ){
	$smarty->display('file:'.$path);
}
?>