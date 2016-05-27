<?php
// ------------------------------------------------------------------------
// Recalcul des totaux de tous les suivis
// ------------------------------------------------------------------------
chdir ('..');
include_once 'config.php'; // load config (mysql, path, etc.)
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
	require DIMS_APP_PATH.'modules/system/suivi/class_suivi.php';

	// On reprend tous les suivis
	$rs = $db->query("SELECT * FROM `dims_mod_business_suivi`");
	while ($row = $db->fetchrow($rs)) {
		$_SESSION['dims']['workspaceid'] = $row['id_workspace'];

		$suivi = new suivi();
		$suivi->openFromResultSet($row);
		$suivi->save();
	}

	// ------------------------------------------------------------------------
	// FIN
	// ------------------------------------------------------------------------
}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
