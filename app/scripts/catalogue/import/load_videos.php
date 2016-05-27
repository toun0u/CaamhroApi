<?php

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

$dims->initAllModules();//parce que l'open sur les tiers consomme à mort sur la requête de récupération du module_type_id avec à chaque fois une requête sur le module
$dims->init_metabase();


require_once DIMS_APP_PATH.'include/class_csv_import.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_article_reference.php';


$_SESSION['dims']['userid'] 		= 65;
$_SESSION['dims']['moduleid'] 		= 355;
$_SESSION['dims']['workspaceid'] 	= 64;

// Dossier contenant les fichiers à importer
$input_dir = DIMS_APP_PATH.'data/synchro/input/videos/';

$finfo = finfo_open(FILEINFO_MIME_ENCODING);
foreach (glob($input_dir.'*.csv') as $filename) {

	if (file_exists($filename)) {

		echo "Traitement de $filename\n";
		ob_flush();

		$change_encoding = null;
		if (finfo_file($finfo, $filename) == 'iso-8859-1') {
			$change_encoding = 'latin1';
		}

		// Chargement du fichier CSV dans la table temporaire
		$csv = new dims_csv_import($filename);
		$csv->setfield_separate_char(";");
		$csv->setuse_csv_header(false);
		if (!is_null($change_encoding)) {
			$csv->set_file_encoding($change_encoding);
		}
		$csv->import();

		// On vire les lignes d'entete
		$db->query('DELETE FROM `'.$csv->getTableTemp().'` WHERE `column1` LIKE "Fournisseur"');

		$rs = $db->query("SELECT * FROM ".$csv->getTableTemp());
		while($fields = $db->fetchrow($rs)) {
			foreach ($fields as $k => $v) { $fields[$k] = trim($v); }

			$position = 1;

			// Recherche de l'article
			$article = new article();
			if ($article->findByRef($fields['column2'])) {
				// On supprime tout l'existant concernant cette référence et ce type d'association
				$db->query('DELETE FROM `'.article_reference::TABLE_NAME.'`
					WHERE 	`id_article` = '.$article->get('id').'
					AND 	`type` = '.article_reference::TYPE_VIDEO);

				if ($fields['column5'] != '' && $fields['column6'] != '') {
					$article_reference = article_reference::build(array(
						'id_article' 	=> $article->get('id'),
						'position' 		=> $position,
						'name' 			=> $fields['column5'],
						'type' 			=> article_reference::TYPE_VIDEO,
						'url' 			=> $fields['column6']
						));
					$article_reference->save();
					$position++;
				}

				if ($fields['column8'] != '' && $fields['column9'] != '') {
					$article_reference = article_reference::build(array(
						'id_article' 	=> $article->get('id'),
						'position' 		=> $position,
						'name' 			=> $fields['column8'],
						'type' 			=> article_reference::TYPE_VIDEO,
						'url' 			=> $fields['column9']
						));
					$article_reference->save();
					$position++;
				}
			}
		}

		dims_csv_import::deleteTableTemp($csv->getTableTemp());
	}
}

finfo_close($finfo);
