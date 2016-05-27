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
require_once DIMS_APP_PATH.'modules/catalogue/include/class_champ.php';


$_SESSION['dims']['userid'] 		= 65;
$_SESSION['dims']['moduleid'] 		= 355;
$_SESSION['dims']['workspaceid'] 	= 64;

// Dossier contenant les fichiers à importer
$input_dir = DIMS_APP_PATH.'data/synchro/input/articles_links/';

// $db->query('TRUNCATE TABLE `dims_mod_cata_article_link`');

// Chargement de la liste des types de liens
$a_link_types = array();
foreach (link_type::all() as $link_type) {
	$a_link_types[$link_type->get('code')] = $link_type;
}

// Articles "BIO"
$a_bio_articles = array();

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
		$db->query('DELETE FROM `'.$csv->getTableTemp().'` WHERE `column1` = ""');

		// Si on a pas de catégorie, on vire la ligne
		$db->query('DELETE FROM `'.$csv->getTableTemp().'` WHERE `column4` = ""');

		// Traitement du fichier
		$rs = $db->query("SELECT * FROM ".$csv->getTableTemp());
		while($fields = $db->fetchrow($rs)) {
			foreach ($fields as $k => $v) { $fields[$k] = trim($v); }

			// On vérifie que le type de lien est bien renseigné
			if (isset($a_link_types[$fields['column4']])) {
				$article_from = new article();
				if ($article_from->findByRef($fields['column1'])) {
					// On supprime tout l'existant concernant cette référence et ce type d'association
					$a_links = article_link::find_by(array(
						'id_article_from' 	=> $article_from->get('id'),
						'type' 				=> $a_link_types[$fields['column4']]->get('id')
						));
					foreach ($a_links as $link) {
						$link->delete();
					}

					// On boucle sur toutes les colonnes sur lesquelles on a une référence de renseignée
					for ($i = 5; $i < sizeof($fields) + 1; $i++) {
						if ($fields['column'.$i] != '') {
							$article_to = new article();
							if ($article_to->findByRef($fields['column'.$i])) {
								$link = new article_link();
								$link->init_description(true);
								$link->setugm();
								$link->setArticleFrom($article_from->get('id'));
								$link->setArticleTo($article_to->get('id'));
								$link->setType($a_link_types[$fields['column4']]->get('id'));
								if ($fields['column3'] == 'S') {
									$link->setSymetric(article_link::SYM_LINK);
								}
								else {
									$link->setSymetric(article_link::ASYM_LINK);
								}
								$link->save(false);

								// Si le lien de l'article est en type 4,
								// on renseigne le filtre "BIO" à OUI (filtre global)
								if ($fields['column4'] == 4 && !isset($a_bio_articles[$article_to->get('id')])) {
									$a_bio_articles[$article_to->get('id')] = 1;
								}
							}
						}
					}
				}
			}
		}
		dims_csv_import::deleteTableTemp($csv->getTableTemp());
	}
}

finfo_close($finfo);


// Chargement du filtre "BIO"
if (sizeof($a_bio_articles)) {
	$filter = new cata_champ();
	$bio_filter = $filter->find_by(array('global_filter' => 1), null, 0, 1);
	$db->query('UPDATE `dims_mod_cata_article`
		SET `fields'.$bio_filter->get('id').'` = '.$bio_filter->get('global_filter_value').'
		WHERE `id` IN ('.implode(',', array_keys($a_bio_articles)).')');
}
