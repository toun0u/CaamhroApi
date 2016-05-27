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
			$current->set('label', "Gestion commerciale");

		//$view->render('dashboard/dashboard.tpl.php');
		break;
}
