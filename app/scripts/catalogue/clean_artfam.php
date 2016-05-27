<?php
define('AUTHORIZED_ENTRY_POINT', true);

chdir(dirname($argv[0]));
chdir('../../../www/');

ini_set('memory_limit', -1);
// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

include_once(DIMS_APP_PATH . "/modules/system/class_dims.php");
include_once(DIMS_APP_PATH . "/modules/system/class_workspace.php");
// INITIALIZE DIMS OBJECT
$dims = new dims();

include_once DIMS_APP_PATH . '/include/errors.php';
include_once DIMS_APP_PATH . '/include/class_debug.php';

// load DIMS global classes
include_once DIMS_APP_PATH . '/include/class_dims_data_object.php';

// initialize DIMS
include_once DIMS_APP_PATH . '/include/global.php';		// load dims global functions & constants

/**
* Database connection
*
* Don't forget to param db connection in ./include/config.php
*/

if (file_exists(DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) {
	include DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
}

$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);

$dims->setDb($db);

if ($dims->getHttpHost()=="") $dims->setHttpHost($http_host);
// initialisation des workspaces
$dims->initWorkspaces();

$webworkspaces=$dims->getWebWorkspaces();

dims::setInstance($dims);
$dims->init_metabase();


$a_ids = array();
$rs = $db->query('SELECT id FROM `dims_mod_cata_article`');
while ($row = $db->fetchrow($rs)) {
	$a_ids[] = $row['id'];
}

$db->query('DELETE FROM `dims_mod_cata_article_famille` WHERE `id_article` NOT IN ('.implode(',', $a_ids).')');

$a_ids = array();
$rs = $db->query('SELECT id FROM `dims_mod_cata_famille`');
while ($row = $db->fetchrow($rs)) {
	$a_ids[] = $row['id'];
}

$db->query('DELETE FROM `dims_mod_cata_article_famille` WHERE `id_famille` NOT IN ('.implode(',', $a_ids).')');
