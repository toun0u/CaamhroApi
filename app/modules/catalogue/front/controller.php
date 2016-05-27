<?php
include_once DIMS_APP_PATH.'modules/catalogue/admin/helpers/application_helpers.php';
$view = view::getInstance();
$view->setLayout('layouts/default_layout.tpl.php'); //déclaration du layout principal
$flashes = &get_sessparam($_SESSION['cata']['articles']['flashes'], array());
$view->initFlashStructure($flashes);

$view->set_tpl_webpath('modules/catalogue/front/views/');
$view->set_static_version('c8cb39400e01695b48c590dc2cc41269fa6bf33e');

$view->clear();//permet de réinitialiser les données en sessions liées aux tpls à utiliser

$styles = $view->getStylesManager();
$styles->loadRessource('modules', 'catalogue');

$scripts = $view->getScriptsManager();
$scripts->loadRessource('modules', 'catalogue');

setlocale(LC_MONETARY, 'fr_FR.UTF-8'); # TODO - A terme il faudra qu'on aille chercher le paramètre correspondant

$c = dims_load_securvalue('c', dims_const::_DIMS_CHAR_INPUT, true, true);
$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);
if ($c == '') {
	$c = 'billets';
	$a = 'index';
}

$view->assign('c',$c);
$view->assign('a',$a);

switch($c){
	default:
	case 'billets':
		include_once DIMS_APP_PATH.'modules/catalogue/front/controllers/billets_controller.php';
		break;
	case 'espace':
		include_once DIMS_APP_PATH.'modules/catalogue/front/controllers/espace_controller.php';
		break;
}

$view->compute();
?>
