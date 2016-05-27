<?php

require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
require_once DIMS_APP_PATH.'/modules/catalogue/include/class_facture.php';

$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);

if ($id > 0) {
	if (!isset($_SESSION['catalogue']['moduleid'])) {
		$mods = $dims->getModuleByType('catalogue');
		$_SESSION['catalogue']['moduleid'] = $mods[0]['instanceid'];
	}

	$param = new cata_param();
	if ($param->getByName('cata_documents_template', $_SESSION['catalogue']['moduleid'])) {
		$doc = new docfile();
		if ($doc->open($param->getValue())) {
			$modele = $doc->getfilepath();
			$format = 'PDF';

			$document = new cata_facture();
			$document->open($id);

			// On vérifie que le document appartient bien au client
			if ($document->getClientCode() == $_SESSION['catalogue']['code_client']) {
				ob_clean();
				$document->printout($modele, $format);
			}
		}
	}
	die();
}
