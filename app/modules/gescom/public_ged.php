<?php

$view = view::getInstance();
$view->set_tpl_webpath('modules/gescom/views/');
//$view->setLayout('layouts/index_layout.tpl.php'); //déclaration du layout principal
if(!isset($_SESSION['flashes']))$_SESSION['flashes'] = array();
$view->initFlashStructure($_SESSION['flashes']);

$view->set_static_version('d8cb39400e01695b48c590dc2cc41269fa6bf33e');

$view->clear();//permet de réinitialiser les données en sessions liées aux tpls à utiliser

$c = dims_load_securvalue('c', dims_const::_DIMS_CHAR_INPUT, true, true);

if ($c == '') {
	$c = "desktop";
};

$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);
$aa = dims_load_securvalue('aa', dims_const::_DIMS_CHAR_INPUT, true, true);

$view->assign('c', $c);
$view->assign('a', $a);
$view->assign('aa', $aa);

$view->setLayout('layouts/ged_layout.tpl.php');

include_once DIMS_APP_PATH . 'modules/gescom/controllers/ged_controller.php';

$view->compute(); //affiche la page
