<?php

// On traite l'onglet courant
switch($_SESSION['dims']['submenuobject']) {
	case dims_const::_DIMS_SUBMENU_DETAIL:
		$desktopobjectheight = dims_load_securvalue('desktopobjectheight', dims_const::_DIMS_NUM_INPUT, true);
		require_once(DIMS_APP_PATH . "/modules/system/class_tiers.php");
		require_once(DIMS_APP_PATH . "/modules/system/class_contact.php");
		$contact= new contact();
		if ($contact_id>0) {
			$contact->open($contact_id);
			$_SESSION['business']['contact_id']=$contact_id;
			$disabledbloc=true;
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_profil.php');
			unset($disabledbloc);
		}
		break;
}
?>
