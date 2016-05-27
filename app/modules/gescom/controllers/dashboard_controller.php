<?php
$current = null;
if(!empty($_SESSION['dims_tabs'])) {
	if(count($_SESSION['dims_tabs']->get('tabs')) != 0) {
		$manager = $_SESSION['dims_tabs'];
		$current = $manager->findOneByState(1);
		$current->set('link', Gescom\get_path(array('c'=>$c,'a'=>$a)));
	}
}

switch($a) {
	default :
	case 'index' :
		if(!is_null($current))
			$current->set('label', 'Accueil');

		$view->assign('nbElem',_DASHBOARD_NB_ELEMS_DISPLAY);

		// Demandes web
		$view->assign('nbWebAsks',count(web_ask::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'state'=>web_ask::_STATE_WAITING))));

		// TODOS
		$view->assign('nbTodos',todo::countNbTasks($_SESSION['dims']['userid']));

		// Discussions

		// Documents
		$view->assign('nbDocfiles',docfile::countDocfileModule($_SESSION['dims']['moduleid']));

		$view->render('dashboard/dashboard.tpl.php');
		break;
	case 'list-web-ask':
		if(!is_null($current))
			$current->set('label', 'Demandes web en attente');
		$view->assign('nbWebAsks',count(web_ask::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'state'=>web_ask::_STATE_WAITING))));
		$view->assign('nbElem',0);
		$view->render('demandes_web/dashboard.tpl.php');
		break;
	case 'list-todos':
		if(!is_null($current))
			$current->set('label', 'Vos todos');
		$view->assign('nbTodos',todo::countNbTasks($_SESSION['dims']['userid']));
		$view->assign('nbElem',0);
		$view->render('todos/dashboard.tpl.php');
		break;
	case 'list-docfile':
		if(!is_null($current))
			$current->set('label', 'Documents');
		$view->assign('nbDocfiles',docfile::countDocfileModule($_SESSION['dims']['moduleid']));
		$view->assign('nbElem',0);
		$view->render('documents/dashboard.tpl.php');
		break;
}
