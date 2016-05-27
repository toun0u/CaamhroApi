<?php

// Resaisit le lien famille / champ depuis les filtres importés via des fichiers csv
// Pour ca, on s'appuie sur le lien article / famille et sur les champs libres des articles
// pour retrouver nos correspondances

define('AUTHORIZED_ENTRY_POINT', true);

chdir(dirname($argv[0]));
chdir('../../../../www/');


if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
	$_SERVER['HTTP_USER_AGENT'] = 'cli';
}


// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

include(DIMS_APP_PATH."include/start.php");

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
if (file_exists(DIMS_APP_PATH . '/db/class_db_'._DIMS_SQL_LAYER.'.php')) include_once DIMS_APP_PATH . '/db/class_db_'._DIMS_SQL_LAYER.'.php';
global $db;

$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);

$dims->setDb($db);

dims::setInstance($dims);

if ($dims->getHttpHost()=="") $dims->setHttpHost($http_host);
// initialisation des workspaces
$dims->initWorkspaces();
$webworkspaces=$dims->getWebWorkspaces();

$dims->init_metabase();


ini_set('memory_limit', '512M');


define('NB_FIELDS', 150);

$a_links = array();

$sql = 'SELECT af.`id_famille`';
for ($i = 1; $i <= NB_FIELDS; $i++) {
	$sql .= ', a.`fields'.$i.'`';
}
$sql .= '
	FROM 	`dims_mod_cata_article` a
	INNER JOIN 	`dims_mod_cata_article_famille` af
	ON 			af.`id_article` = a.`id`';

$rs = $db->query($sql);
while ($row = $db->fetchrow($rs)) {
	if (!isset($a_links[$row['id_famille']])) {
		$a_links[$row['id_famille']] = array();
	}
	for ($i = 1; $i <= NB_FIELDS; $i++) {
		if ( !is_null($row['fields'.$i]) && !isset($a_links[$row['id_famille']][$i]) ) {
			$a_links[$row['id_famille']][$i] = 1;
		}
	}
}

// print_r($a_links);

$db->query('TRUNCATE TABLE `dims_mod_cata_champ_famille`');
foreach ($a_links as $id_famille => $champs) {
	$position = 1;
	foreach ($champs as $id_champ => $empty) {
		$db->query('INSERT INTO `dims_mod_cata_champ_famille` (
				`id_famille`,
				`id_champ`,
				`fiche`,
				`filtre`,
				`inherited`,
				`status`,
				`position`
			) VALUES (
				'.$id_famille.',
				'.$id_champ.',
				1,
				1,
				"L",
				0,
				'.$position.'
			)');
		$position++;
	}
}

echo "\n\n";
