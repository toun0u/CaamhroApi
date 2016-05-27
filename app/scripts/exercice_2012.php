<?php
// ------------------------------------------------------------------------
// Déplacement des suivis créés depuis le 1er juillet 2012 sur l'exercice 2012
// ------------------------------------------------------------------------
chdir ('..');
require_once 'config.php'; // load config (mysql, path, etc.)
require_once DIMS_APP_PATH . '/include/default_config.php'; // load config (mysql, path, etc.)

include_once(DIMS_APP_PATH . "/modules/system/class_dims.php");
include_once(DIMS_APP_PATH . "/modules/system/class_workspace.php");

//Charge la class des gestions d'exceptions
require(DIMS_APP_PATH."include/class_exception.php");

try {
	// INITIALIZE DIMS OBJECT
	$dims = new dims();

	include_once DIMS_APP_PATH . '/include/errors.php';

	// load DIMS global classes
	include_once DIMS_APP_PATH . '/include/class_dims_data_object.php';

	// initialize DIMS
	include_once DIMS_APP_PATH . '/include/global.php'; 		// load dims global functions & constants
	include_once DIMS_APP_PATH . '/modules/system/class_module.php';
	/**
	* Database connection
	*
	* Don't forget to param db connection in ./include/config.php
	*/
	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) include_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	global $db;

	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	if(!$db->connection_id) trigger_error(_DIMS_MSG_DBERROR, E_USER_ERROR);

	$dims->setDb($db);


	// ------------------------------------------------------------------------
	// Init
	// ------------------------------------------------------------------------
	$_SESSION['dims']['workspaceid'] = $id_workspace = 64;

	require DIMS_APP_PATH.'modules/system/suivi/class_suivi.php';

	// Recherche des suivis créés après le 1er juillet 2012
	$rs = $db->query("SELECT * FROM `dims_mod_business_suivi` WHERE `type` IN ('Facture','Avoir') AND `datejour` > '2012-07'");
	while ($row = $db->fetchrow($rs)) {
		$suivi = new suivi();
		$suivi->openFromResultSet($row);

		$oldId = $suivi->getId();
		$oldType = $suivi->getType();
		$oldExercice = $suivi->getExercice();

		$suivi->setExercice(2012);
		$suivi->setNextId();
		$suivi->save();

		$suivi->updateLinesId($oldId, $oldType, $oldExercice);
		$suivi->updateVersementsId($oldId, $oldType, $oldExercice);
	}

	// mise à jour de l'exercice
	$db->query("UPDATE dims_mod_business_params SET value = 2012 WHERE param = 'exercice' AND id_workspace = $id_workspace");

	// ------------------------------------------------------------------------
	// FIN
	// ------------------------------------------------------------------------
}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
