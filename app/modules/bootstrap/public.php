<?php
$module_name = 'bootstrap';
dims_init_module($module_name);

$view = view::getInstance();
$view->set_tpl_webpath('modules/'.$module_name.'/views/');

//$view->setLayout('layouts/index_layout.tpl.php'); //déclaration du layout principal
if(!isset($_SESSION['flashes']))$_SESSION['flashes'] = array();
$view->initFlashStructure($_SESSION['flashes']);

$view->set_static_version('c8cb39400e01695b48c590dc2cc41269fa6bf33e');

$view->clear();//permet de réinitialiser les données en sessions liées aux tpls à utiliser

$c = dims_load_securvalue('c', dims_const::_DIMS_CHAR_INPUT, true, true, false, $c);

if($c == ''){
	$c="default";
};

$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);

$view->assign('c', $c);
$view->assign('a', $a);

$view->setLayout('layouts/default_layout.tpl.php');

$styles = $view->getStylesManager();
$styles->loadRessource('modules', $module_name);

$scripts = $view->getScriptsManager();
$scripts->loadRessource('modules', $module_name);


switch($c){
	default:
	case 'default':
		echo 'Hello World !';
		break;
}

$view->compute(); //affiche la page
