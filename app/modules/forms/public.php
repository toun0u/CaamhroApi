<?php
dims_init_module('forms');

$view = view::getInstance();
$view->set_tpl_webpath('modules/forms/views/');
//$view->setLayout('layouts/index_layout.tpl.php'); //déclaration du layout principal
if(!isset($_SESSION['flashes']))$_SESSION['flashes'] = array();
$view->initFlashStructure($_SESSION['flashes']);

$view->set_static_version('hoh4ohF9nochahb9eeshie4choo0Aiko6touquae');

$view->clear();//permet de réinitialiser les données en sessions liées aux tpls à utiliser

$view->setLayout('layouts/module_layout.tpl.php');

$styles = $view->getStylesManager();
$styles->loadRessource('modules', 'forms');

$scripts = $view->getScriptsManager();
$scripts->loadRessource('modules', 'forms');

$c = dims_load_securvalue('c', dims_const::_DIMS_CHAR_INPUT, true, true);

if($c == ''){
	$c="index";
};

$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);

$view->assign('c', $c);
$view->assign('a', $a);

switch($c){
	default:
	case 'index':
		include_once DIMS_APP_PATH.'modules/forms/controllers/index.php';
		break;
	case 'form':
		include_once DIMS_APP_PATH.'modules/forms/controllers/form.php';
		break;
	case 'answer':
		include_once DIMS_APP_PATH.'modules/forms/controllers/answer.php';
		break;
}

$view->compute(); //affiche la page
