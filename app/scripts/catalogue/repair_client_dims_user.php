<?php
define('AUTHORIZED_ENTRY_POINT', true);

chdir(dirname($argv[0]));
chdir('../../../www/');

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
if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) include_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
global $db;

$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);

$dims->setDb($db);

if ($dims->getHttpHost()=="") $dims->setHttpHost($http_host);
// initialisation des workspaces
$dims->initWorkspaces();

$webworkspaces=$dims->getWebWorkspaces();

dims::setInstance($dims);
$dims->init_metabase();


// Répare les clients qui ont bien un dims_group, mais pas de dims_user
// alors que le contact et le users sont bien créés (seul client.dims_user = 0)

$rs = $db->query('SELECT * FROM `dims_mod_cata_client` WHERE `dims_group` > 0 AND `dims_user` = 0');
while ($row = $db->fetchrow($rs)) {
	$rs2 = $db->query('SELECT `id_user` FROM `dims_group_user` WHERE `id_group` = '.$row['dims_group']);
	if ($db->numrows($rs2) === 1) {
		$row2 = $db->fetchrow($rs2);
		$db->query('UPDATE `dims_mod_cata_client` c
			INNER JOIN 	`dims_user` u
			ON 			u.`id` = '.$row2['id_user'].'
			SET
				c.`dims_user` = u.`id`,
				c.`librcha1` = u.`login`,
				c.`librcha2` = u.`initial_password`
			WHERE `code_client` = "'.$row['code_client'].'"');
	}
	elseif ($db->numrows($rs2) > 1) {
		while ($row2 = $db->fetchrow($rs2)) {
			$db->query('UPDATE `dims_mod_cata_client` c
				INNER JOIN 	`dims_user` u
				ON 			u.`id` = '.$row2['id_user'].'
				SET
					c.`dims_user` = u.`id`,
					c.`librcha1` = u.`login`,
					c.`librcha2` = u.`initial_password`
				WHERE `code_client` = "'.$row['code_client'].'"');
			break;
		}
	}
}

// Répare les clients qui n'ont pas de dims_group, ni de dims_user
$rs = $db->query('SELECT `id_client`, `tiers_id` FROM `dims_mod_cata_client` WHERE `dims_group` = 0');
while ($row = $db->fetchrow($rs)) {
	$db->query('DELETE FROM `dims_mod_business_tiers` WHERE `id` = '.$row['tiers_id']);
	$db->query('DELETE FROM `dims_mod_cata_client` WHERE `id_client` = '.$row['id_client']);
}

$db->query('DELETE FROM `dims_mod_business_tiers_contact` WHERE `id_tiers` NOT IN (SELECT `id` FROM `dims_mod_business_tiers`)');
$db->query('DELETE FROM `dims_mod_business_contact` WHERE `id` NOT IN (SELECT `id_contact` FROM `dims_mod_business_tiers_contact`)');
