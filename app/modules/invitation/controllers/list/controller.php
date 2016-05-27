<?php
$view = view::getInstance();
$view->render('headers/list_header.tpl.php', 'header');

switch ($a) {
	default:
	case 'view':
		$view->assign('invitations',invitation::getInvitation());
		$view->render('content/list.tpl.php');
		break;
}
