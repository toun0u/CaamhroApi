<!-- Lejal Simon module cheval liberté gescom, controller -->
<?php
$current = null;
if(!empty($_SESSION['dims_tabs'])) {
	if(count($_SESSION['dims_tabs']->get('tabs')) != 0) {
		$manager = $_SESSION['dims_tabs'];
		$current = $manager->findOneByState(1);
		$current->set('link', Gescom\get_path(array('c'=>$c,'a'=>$a)));
	}
}

$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);

switch($a){
	default:
	case 'index' :
		if(!is_null($current))
			$current->set('label', 'Journal d\'appels');
		$view->render('telephony/index.tpl.php');
		break;

	case 'form_contact' :
		if(!is_null($current))
			$current->set('label', 'Journal d\'appels');
		//formulaire d'ajout d'un business contact;
		$view->render('telephony/business_contact.tpl.php');
	break;

	case 'form_note' :
		if(!is_null($current))
			$current->set('label', 'Journal d\'appels');
		//formulaire de consultation et d'édition d'une note d'un appel;
		$view->render('telephony/notes_appel.tpl.php');
	break;

}
