<?php

define('AUTHORIZED_ENTRY_POINT', true);
ob_start();
if (substr($_SERVER["DOCUMENT_ROOT"],strlen($_SERVER["DOCUMENT_ROOT"])-1,1)!="/") $_SERVER["DOCUMENT_ROOT"].="/";

// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

//session_start();
require_once DIMS_APP_PATH . '/include/start.php';
require_once DIMS_APP_PATH . '/include/errors.php';

require_once DIMS_APP_PATH . '/include/class_timer.php' ;

// get vars from GET, POST, REQUEST
require_once DIMS_APP_PATH . '/include/import_gpr.php';

// initialize DIMS
if (file_exists(DIMS_APP_PATH . '/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH . '/db/class_db_'._DIMS_SQL_LAYER.'.php';

//$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
//if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);
// INIT VARIABLES
$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
if(!$db->isconnected()) {
	trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);
}

$dims_op = dims_load_securvalue('dims_op', dims_const::_DIMS_CHAR_INPUT, true, true);

if (isset($dims_op) && $dims_op !== "") {
	require_once DIMS_APP_PATH.'include/op.php';
}

?>
