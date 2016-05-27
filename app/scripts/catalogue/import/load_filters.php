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

$dims->init_metabase();


require DIMS_APP_PATH.'include/class_csv_import.php';
require DIMS_APP_PATH.'modules/catalogue/include/class_champ.php';
require DIMS_APP_PATH.'modules/catalogue/include/class_param.php';



$_SESSION['dims']['userid'] 		= 65;
$_SESSION['dims']['moduleid'] 		= 355;
$_SESSION['dims']['workspaceid'] 	= 64;

// GO_id de la catégorie "divers"
$goCat = 96046;

// Dossier contenant les fichiers à importer
$input_dir = DIMS_APP_PATH.'data/synchro/input/filters/';

// 1ere colonne à contenir un filtre
define ('_FIRST_FILTER_COLUMN', 13);



// $db->query('TRUNCATE TABLE `dims_mod_cata_champ`');
// $db->query('TRUNCATE TABLE `dims_mod_cata_champ_lang`');
// $db->query('TRUNCATE TABLE `dims_mod_cata_champ_valeur`');
// $db->query('TRUNCATE TABLE `dims_mod_cata_champ_famille`');


// // Nettoyage des valeurs affectées
// $db->query('
// 	UPDATE `dims_mod_cata_article` SET
// 	fields1 = null,fields2 = null,fields3 = null,fields4 = null,fields5 = null,fields6 = null,fields7 = null,fields8 = null,fields9 = null,fields10 = null,
// 	fields11 = null,fields12 = null,fields13 = null,fields14 = null,fields15 = null,fields16 = null,fields17 = null,fields18 = null,fields19 = null,fields20 = null,
// 	fields21 = null,fields22 = null,fields23 = null,fields24 = null,fields25 = null,fields26 = null,fields27 = null,fields28 = null,fields29 = null,fields30 = null,
// 	fields31 = null,fields32 = null,fields33 = null,fields34 = null,fields35 = null,fields36 = null,fields37 = null,fields38 = null,fields39 = null,fields40 = null,
// 	fields41 = null,fields42 = null,fields43 = null,fields44 = null,fields45 = null,fields46 = null,fields47 = null,fields48 = null,fields49 = null,fields50 = null,
// 	fields51 = null,fields52 = null,fields53 = null,fields54 = null,fields55 = null,fields56 = null,fields57 = null,fields58 = null,fields59 = null,fields60 = null,
// 	fields61 = null,fields62 = null,fields63 = null,fields64 = null,fields65 = null,fields66 = null,fields67 = null,fields68 = null,fields69 = null,fields70 = null,
// 	fields71 = null,fields72 = null,fields73 = null,fields74 = null,fields75 = null,fields76 = null,fields77 = null,fields78 = null,fields79 = null,fields80 = null,
// 	fields81 = null,fields82 = null,fields83 = null,fields84 = null,fields85 = null,fields86 = null,fields87 = null,fields88 = null,fields89 = null,fields90 = null,
// 	fields91 = null,fields92 = null,fields93 = null,fields94 = null,fields95 = null,fields96 = null,fields97 = null,fields98 = null,fields99 = null,fields100 = null,
// 	fields101 = null,fields102 = null,fields103 = null,fields104 = null,fields105 = null,fields106 = null,fields107 = null,fields108 = null,fields109 = null,fields110 = null,
// 	fields111 = null,fields112 = null,fields113 = null,fields114 = null,fields115 = null,fields116 = null,fields117 = null,fields118 = null,fields119 = null,fields120 = null,
// 	fields121 = null,fields122 = null,fields123 = null,fields124 = null,fields125 = null,fields126 = null,fields127 = null,fields128 = null,fields129 = null,fields130 = null,
// 	fields131 = null,fields132 = null,fields133 = null,fields134 = null,fields135 = null,fields136 = null,fields137 = null,fields138 = null,fields139 = null,fields140 = null,
// 	fields141 = null,fields142 = null,fields143 = null,fields144 = null,fields145 = null,fields146 = null,fields147 = null,fields148 = null,fields149 = null,fields150 = null;');

$a_filters = array();
$a_articles = array();

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

		// Traitement du fichier
		$ligne = 0;
		$rs = $db->query("SELECT * FROM ".$csv->getTableTemp());
		while($fields = $db->fetchrow($rs)) {
			$ligne++;
			// On conserve les valeurs de filtre de chaque article
			$a_articles[$fields['column1']] = array();

			for ($i = _FIRST_FILTER_COLUMN; $i <= sizeof($fields); $i++) {
				// trim sur les colonnes
				$fields['column'.$i] = trim($fields['column'.$i]);

				if ($fields['column'.$i] != '') {
					// Les filtres sont sur les colonnes paires
					// on évite les doublons
					if ($i % 2 == _FIRST_FILTER_COLUMN % 2) {
						if (!isset($a_filters[$fields['column'.$i]])) {
							$a_filters[$fields['column'.$i]] = array();
						}
					}
					// les valeurs sont sur les colonnes impaires
					// on évite les doublons
					else {
						if (!isset($a_filters[$fields['column'.($i-1)]][$fields['column'.$i]])) {
							$a_filters[$fields['column'.($i-1)]][$fields['column'.$i]] = $fields['column'.$i];
						}
						// Valeur du filtre sur l'article
						$a_articles[$fields['column1']]['"'.$fields['column'.($i-1)].'"'] = '"'.addslashes($fields['column'.$i]).'"';
					}
				}
			}
		}

		dims_csv_import::deleteTableTemp($csv->getTableTemp());

	}
}

if (sizeof($a_filters)) {
	foreach ($a_filters as $label => $values) {
		// Tri des valeurs par ordre naturel insensible à la casse
		natcasesort($values);

		// Création du filtre
		$filter = cata_champ::getObjectBylabel($label);

		if (is_null($filter)) {
			$filter = new cata_champ();
			$filter->init_description(true);
			$filter->setugm();
			$filter->setLabel($label);
			$filter->setType('liste');
			$filter->setFiche(1);
			$filter->setFiltre(1);
			$filter->save();

			// Catégorie
			$go = $filter->getMyGlobalObject();
			$go->addLink($goCat);
		}

		// Affectation des langues et des valeurs
		foreach(cata_param::getActiveLang() as $id => $lg){
			$filter->addLibelle($id, $label);
			$filter->addValues($id, $values, false);
		}
	}
}

// Affectation des valeurs sur les articles
if (sizeof($a_articles)) {
	// Langue par défaut
	$default_lang = cata_param::getDefaultLang();

	foreach ($a_articles as $ref => $filters) {
		if (sizeof($filters)) {
			$rs = $db->query('SELECT c.`id`, c.`libelle`, cv.`id` AS id_value, cv.`valeur`
				FROM `dims_mod_cata_champ` c
				INNER JOIN `dims_mod_cata_champ_valeur` cv
				ON 			cv.`id_chp` = c.`id`
				AND 		cv.`valeur` IN ('.implode(',', $filters).')
				AND 		cv.`id_lang` = '.$default_lang.'
				WHERE c.`libelle` IN ('.implode(',', array_keys($filters)).')');
			if ($db->numrows($rs)) {
				$toUpdate = false;

				// Liste des champs à ne pas réinitialiser
				$a_filters_ids = array();

				$sql = 'UPDATE `dims_mod_cata_article` SET ';
				while ($row = $db->fetchrow($rs)) {
					if ($filters['"'.$row['libelle'].'"'] == '"'.addslashes($row['valeur']).'"') {
						$toUpdate = true;
						$sql .= '`fields'.$row['id'].'` = '.$row['id_value'].', ';
						$a_filters_ids[$row['id']] = $row['id'];
					}
				}

				for ($i = 1; $i <= 150; $i++) {
					if (!isset($a_filters_ids[$i])) {
						$sql .= '`fields'.$i.'` = NULL, ';
					}
				}

				if ($toUpdate) {
					$sql = substr($sql, 0, -2).' WHERE `reference` = "'.$ref.'"';
					$db->query($sql);
				}
			}
		}
	}
}

finfo_close($finfo);
