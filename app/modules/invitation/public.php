<?php
dims_init_module('invitation');
include_once DIMS_APP_PATH.'modules/invitation/include/global.php';

$view = view::getInstance();
$view->set_tpl_webpath('modules/invitation/views/');
//$view->setLayout('layouts/index_layout.tpl.php'); //déclaration du layout principal
if(!isset($_SESSION['invitation']['flashes']))$_SESSION['invitation']['flashes'] = array();
$view->initFlashStructure($_SESSION['invitation']['flashes']);

$view->set_static_version(module_invitation::_STATIC_VERSION);

$view->clear();//permet de réinitialiser les données en sessions liées aux tpls à utiliser

$c = dims_load_securvalue('c', dims_const::_DIMS_CHAR_INPUT, true, true);

if($c == ''){
	$c="list";
};

$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);

$view->setLayout('layouts/default.tpl.php');

$manager = $view->getStylesManager();
$manager->loadRessource('modules', 'invitation');

$manager = $view->getScriptsManager();
$manager->loadRessource('modules', 'invitation');

switch($c){
	default:
	case 'list':
		include_once DIMS_APP_PATH.'modules/invitation/controllers/list/controller.php';
		break;
	case 'obj':
		include_once DIMS_APP_PATH.'modules/invitation/controllers/obj/controller.php';
		break;

}

$view->compute(); //affiche la page

?>