<?php
//

// init
$soap_wsdl = '';
$soap_class = '';

$soap_op = dims_load_securvalue('soap_op', dims_const::_DIMS_CHAR_INPUT, true, true);

if (isset($soap_op) && $soap_op != "") {

	ob_clean();
    ini_set('soap.wsdl_cache_enabled', 0);

	$mods = $dims->getModules($_SESSION['dims']['workspaceid']);
	$moduleid = dims_load_securvalue("moduleid", dims_const::_DIMS_NUM_INPUT, true, true);

	if (!empty($mods)) {
		foreach ($mods as $struct) {
			$idm = $struct['instanceid'];

			if ($struct['active'] && ($moduleid == 0 || ($moduleid > 0 && $moduleid == $idm))) {
				$dims_mod_soapfile = "./common/modules/{$struct['label']}/soap.php";

				if (file_exists($dims_mod_soapfile)) {
					require_once $dims_mod_soapfile;
				}
			}
		}
	}

	if ($soap_wsdl != '') {
		include DIMS_APP_PATH.'include/class_soap_server.php';
		$soap_server = new dims_soap_server();
		$soap_server->setWSDL($soap_wsdl);

		if ($soap_class != '') {
			$soap_server->setMyClass($soap_class);
		}

		$soap_server->listen();
		die();
	}
}
