<?php

dims_init_module('catalogue');

############################################
##### INTITIALISATION DES RELATIONS ########
############################################
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_param.php';
include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_marque.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_catalogue.php";
$marque = new cata_marque();
$article = new article();

############################################

include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/application_helpers.php";
include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/articles_helpers.php";

$view = view::getInstance();
$view->setLayout('layouts/default_layout.tpl.php'); //déclaration du layout principal
$flashes = &get_sessparam($_SESSION['cata']['articles']['flashes'], array());
$view->initFlashStructure($flashes);

$view->set_tpl_webpath('modules/catalogue/admin/views/');
$view->set_static_version('c8cb39400e01695b48c590dc2cc41269fa6bf33e');

$view->clear();//permet de réinitialiser les données en sessions liées aux tpls à utiliser

$styles = $view->getStylesManager();
$styles->loadRessource('modules', 'catalogue');

$scripts = $view->getScriptsManager();
$scripts->loadRessource('modules', 'catalogue');

setlocale(LC_MONETARY, 'fr_FR.UTF-8'); # TODO - A terme il faudra qu'on aille chercher le paramètre correspondant

// affichage du menu par défaut
if (!isset($_SESSION['catalogue']['display_menu'])) {
	$_SESSION['catalogue']['display_menu'] = 1;
}

$c = dims_load_securvalue('c', dims_const::_DIMS_CHAR_INPUT, true, true);
$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);
if ($c == '') {
	$c = 'clients';
	$a = 'index';
}

$view->assign('c',$c);

$view->assign('lst_clipboard',get_clipboard());

switch($c){
	case 'params':
		$view->assign('selected_menu', 'params');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/params/controller.php';
		break;
	case 'articles':
		$view->assign('selected_menu', 'articles');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/articles/controller.php';
		break;
	case 'familles':
		$view->assign('selected_menu', 'familles');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/familles/controller.php';
		break;
	case 'clients':
		$view->assign('selected_menu', 'clients');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/clients/controller.php';
		break;
	case 'promotions':
		$view->assign('selected_menu', 'promotions');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/promotions/controller.php';
		break;
	case 'commandes':
		$view->assign('selected_menu', 'commandes');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/commandes/controller.php';
		break;
	/*case 'stats':
		$view->assign('selected_menu', 'stats');
		break;*/
	case 'objects':
		$view->assign('selected_menu', 'objects');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/objects/controller.php';
		break;
	case 'quotations':
		$view->assign('selected_menu', 'quotations');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/quotations/controller.php';
		break;
	case 'statistics':
		$view->assign('selected_menu', 'statistics');
		include_once DIMS_APP_PATH.'modules/catalogue/admin/controllers/statistics/controller.php';
		break;
}

$view->compute(); //affiche la page
