<?php
$view = view::getInstance();
switch($a){
	default:
	case 'index':
		$view->assign('forms',forms::getAllForms($_SESSION['dims']['moduleid']));
		$view->render('index/list.tpl.php');
		break;
}
