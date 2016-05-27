<?php
dims_init_module('gescom');

$view = view::getInstance();
$view->set_tpl_webpath('modules/gescom/views/');
$view->setLayout('layouts/default_layout.tpl.php'); //déclaration du layout principal
if(!isset($_SESSION['gescom']['flashes']))$_SESSION['gescom']['flashes'] = array();
$view->initFlashStructure($_SESSION['gescom']['flashes']);
$view->set_static_version('c8cb39400e01695b48c590dc2cc41269fa6bf33e');

$view->clear();//permet de réinitialiser les données en sessions liées aux tpls à utiliser

$manager = $view->getStylesManager();
$manager->loadRessource('modules', 'gescom');

$manager = $view->getScriptsManager();
$manager->loadRessource('modules', 'gescom');

$c = dims_load_securvalue('c', dims_const::_DIMS_CHAR_INPUT, true, true);
$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);


$menu = array(
	'dashboard' 	=> array(
		"label"			=> "Dashboard",
		"sub-menu"		=> array(), // '<url>'=>'<label>'
	),
	'dossier' 	=> array(
		"label"			=> "Dossiers",
		"sub-menu"		=> array(),
	),
);

if(file_exists(DIMS_APP_PATH.'/modules/catalogue/public.php') && dims::getInstance()->isModuleTypeEnabled('catalogue')) {
	include_once DIMS_APP_PATH . 'modules/catalogue/admin/helpers/application_helpers.php';
	$menu += array(
		'' 	=> array(
			"label"			=> "Gestion Commerciale",
			"sub-menu"		=> array(
				\get_path('articles',   'edit',     array('dims_mainmenu' => 'catalogue')) => dims_constant::getVal('ARTICLES'),
				\get_path('familles',   'index',    array('dims_mainmenu' => 'catalogue')) => dims_constant::getVal('FAMILIES'),
				\get_path('clients',    'index',    array('dims_mainmenu' => 'catalogue')) => dims_constant::getVal('CATA_CLIENTS'),
				\get_path('commandes',  'index',    array('dims_mainmenu' => 'catalogue')) => dims_constant::getVal('ORDERS'),
				\get_path('quotations', 'list',     array('dims_mainmenu' => 'catalogue')) => dims_constant::getVal('QUOTATION'),
			),
		),
	);
}

$menu += array(
	'admin' 	=> array(
		"label"			=> $_SESSION['cste']['_DIMS_LABEL_ADMIN'],
		"sub-menu"		=> array(
			Gescom\get_path(array('c'=>'admin','a'=>'form')) => 'Formulaire demande web',
			Gescom\get_path(array('c'=>'admin','a'=>'workflow')) => 'Workflow',
		),
	),
);
$view->assign('menu',$menu);

if ($c == '') {
	$c = 'dashboard';
	$a = 'index';
}

$view->assign('c',$c);
$view->assign('a',$a);


if(!empty($_SESSION['dims_tabs'])) {
	$layoutTab = $_SESSION['dims_tabs']->get('tabs');
	$view->assign('layoutTab', $layoutTab);
}

switch($c){
	default :
	case 'dashboard':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/dashboard_controller.php';
		break;
	case 'telephony':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/telephony_controller.php';
		break;
	case 'dossier':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/dossier_controller.php';
		break;
	case 'gescom':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/gescom_controller.php';
		break;
	case 'admin':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/admin_controller.php';
		break;
	case 'tab':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/tab_controller.php';
		break;
	case 'web_ask':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/web_ask.php';
		break;
	case 'todo':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/todo.php';
		break;
	case 'document':
		include_once DIMS_APP_PATH.'modules/gescom/controllers/document.php';
		break;
}

$view->compute(); //affiche la page
