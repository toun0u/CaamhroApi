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


$a_accounts = array();

// On commence par recherche les users qui ont le meme nom pour un meme client
$rs = $db->query('SELECT lastname, LEFT(login, 6) AS code_client
	FROM dims_user
	GROUP BY lastname, left(login, 6)
	HAVING COUNT(*) > 1');
while ($row = $db->fetchrow($rs)) {
	// Pour chacun de ces comptes, on ne veut conserver que le 1er qui a été créé
	// et récupérer son erp_id sur le compte ou c'est renseigné
	// On ne doit pas supprimer de compte qui s'est déjà connecté
	$rs2 = $db->query('SELECT u.id, u.lastname, u.login, u.lastconnexion, c.id AS id_contact, c.erp_id
		FROM dims_user u
		INNER JOIN dims_mod_business_contact c
		ON c.account_id = u.id
		WHERE u.lastname = "'.$row['lastname'].'"
		AND u.login LIKE "'.$row['code_client'].'%"
		ORDER BY u.id');
	while ($row2 = $db->fetchrow($rs2)) {
		// Pour chaque compte, on doit recréer un couple lastname / erp_id.
		// On doit parcourir tous les comptes pour etre sur d'avoir l'erp_id
		$code_client = substr($row2['login'], 0, 6);

		if (!isset($a_accounts[$code_client])) {
			$a_accounts[$code_client] = array(
				'id' 				=> $row2['id'],
				'login' 			=> $row2['login'],
				'id_contact' 		=> $row2['id_contact'],
				'lastname' 			=> $row2['lastname'],
				'lastconnexion' 	=> $row2['lastconnexion']
				);
		}
		if (!empty($row2['erp_id'])) {
			$a_accounts[$code_client]['erp_id'] = $row2['erp_id'];
		}
	}
}

// Une fois qu'on a une liste propre, il faut mettre à jour l'erp_id
// dans le contact du usser à conserver et supprimer tous les autres
foreach ($a_accounts as $account) {
	if (!empty($account['erp_id'])) {
		$db->query('UPDATE dims_mod_business_contact SET erp_id = '.$account['erp_id'].' WHERE id = '.$account['id_contact']);
	}

	echo "Suppression de ".$account['lastname']." : Login = ".$account['login']."\n";

	$db->query('DELETE FROM dims_mod_business_contact WHERE lastname LIKE "'.$account['lastname'].'%" AND id != '.$account['id_contact']);
	$db->query('DELETE FROM dims_user WHERE lastname LIKE "'.$account['lastname'].'%" AND id != '.$account['id']);
}
