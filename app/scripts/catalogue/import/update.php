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

if ($dims->getHttpHost()=="") $dims->setHttpHost($http_host);
// initialisation des workspaces
$dims->initWorkspaces();

$webworkspaces=$dims->getWebWorkspaces();

dims::setInstance($dims);
$dims->initAllModules();//parce que l'open sur les tiers consomme à mort sur la requête de récupération du module_type_id avec à chaque fois une requête sur le module
$dims->init_metabase();



// On vérifie que le cronindex ne tourne pas
if (file_exists(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running")){
	$pid = file_get_contents(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running");
	$pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);
	if (in_array($pid, $pids)){
		echo "\n Indexation en cours... \n";
		ob_flush();
		exit();
	}
}

// On bloque le cronindex le temps de la synchro
file_put_contents(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running", getmypid());


// ATTENTION, ce script nécessite qu'on ait déjà
// la BDD syncho de chez caahmro chargée dans la BDD courante
//
// Ensuite il suffit de se connecter sur gaia
// dans /var/www/dims/caahmro/app/scripts/catalogue/import
// et de lancer : $ php update.php
//
// C'est ce que fait update.sh dans le même dossier


// Fichiers à traiter
define ('_TRUNCATE_TABLES', 		false);		// Par défaut, toujours à false
define ('_IMPORT_FAMILLES', 		false);		// Par défaut, toujours à false

define ('_IMPORT_UTILISATEURS',		true); 		// Par défaut, true
define ('_IMPORT_TVA', 				true);		// Par défaut, true
define ('_IMPORT_ARTICLES', 		true);		// Par défaut, true
define ('_IMPORT_TARIFS', 			true); 		// Par défaut, true
define ('_IMPORT_CLIENTS', 			true); 		// Par défaut, true
define ('_UPDATE_LIVRAISONS', 		true); 		// Par défaut, true
define ('_IMPORT_DOCUMENTS', 		true); 		// Par défaut, true
define ('_GENERATE_ORDERS', 		true);		// Par défaut, true
define ('_DROP_TABLES', 			true);		// Par défaut, true



// Noms de fichiers
define ('_INPUT_DIR', 				DIMS_APP_PATH.'data/synchro/input/');
define ('_FILENAME_FAMILLES',		'familles.csv');

// Tarif par défaut (prix publics)
define ('_DEFAULT_TARIF', 			'4_JARDIN');

// Correspondance Société / stock (defini par Stéphan)
$a_company_stock = array(
	'COOP' 		=> 'CAAHMRO',
	'GROUPE' 	=> 'CAAHMRO',
	'DURANTIN' 	=> 'CHARLY'
	);

// Valeurs par défaut
define ('_ID_LANG', 				1);
define ('_ID_USER', 				65);
define ('_ID_MODULE', 				355);
define ('_ID_WORKSPACE', 			64);
define ('_ID_COUNTRY', 				34); 	// France
define ('_NOW', 					dims_createtimestamp());

$_SESSION['dims']['userid'] 		= _ID_USER;
$_SESSION['dims']['moduleid'] 		= _ID_MODULE;
$_SESSION['dims']['workspaceid'] 	= _ID_WORKSPACE;

// GO_id de la catégorie "divers"
$goCat = 96046;

// Codes de retour d'erreur
define ('E_STOCK_EMPTY', 	1);
define ('E_TARIFS_EMPTY', 	2);


require_once DIMS_APP_PATH.'modules/catalogue/include/functions.php';

function loadFile($file_name, $table_name, $charset = 'utf8', $fields_delimiter = ';', $lines_delimiter = '\n') {
	$db = dims::getInstance()->getDb();
	$sql = '
		LOAD DATA LOCAL INFILE "'._INPUT_DIR.$file_name.'"
		INTO TABLE `'.$table_name.'`
		CHARACTER SET "'.$charset.'"
		FIELDS TERMINATED BY "'.$fields_delimiter.'"
		LINES TERMINATED BY "'.$lines_delimiter.'"';
	$db->query($sql);
	$db->query('OPTIMIZE TABLE `'.$table_name.'`;');
}

function table_exists($table_name) {
	if (is_null($a_tables)) {
		$db = dims::getInstance()->getDb();
		$rs = $db->query('SHOW TABLES;');
		while ($row = $db->fetchrow($rs)) {
			$a_tables[$row['Tables_in_'._DIMS_DB_DATABASE]] = 1;
		}
	}
	return isset($a_tables[$table_name]);
}

function raiseError($section, $message) {
	$error =
		'timestamp : ' . date("d/m/Y H:i:s") . "\n" .
		'script :    ' . $_SERVER['PHP_SELF'] . "\n" .
		'section :   ' . $section . "\n" .
		'error :     ' . $message . "\n\n";

	$logs_dir = DIMS_APP_PATH.'logs/';
	$log_file = $logs_dir.'error_'.date('Ymd').'.log';

	if (!file_exists($logs_dir)) {
		dims_makedir($logs_dir);
	}
	$handle = fopen($log_file, 'a');
	if ($handle) {
		fwrite($handle, $error);
		fclose($handle);
	}

	mail('benjamin@netlor.fr', "[CAAHMRO] Erreur de synchro", $error);
}

ini_set('memory_limit', -1);



require_once DIMS_APP_PATH.'modules/catalogue/include/global.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_famille.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_champ.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_param.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_market.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_prix_nets.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_depot.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_company.php';


if (_TRUNCATE_TABLES) {
	$db->query('TRUNCATE TABLE `dims_mod_cata_article`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_article_famille`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_article_kit`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_article_link`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_article_link_type`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_article_thumbnail`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_bl`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_bl_lignes`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_car`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_categ`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_cde`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_cde_lignes`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_cde_lignes_detail`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_cde_lignes_hc`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_cfa`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_champ`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_champ_famille`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_champ_lang`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_champ_valeur`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_civilite`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_client`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_client_category`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_client_cplmt`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_client_moyen_paiement`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_client_ratt`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_colis`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_commerciaux`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_conditionnement`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_depot`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_ecolabel`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_ecrits`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_export_params`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_facturation`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_facture`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_facture_det`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_famille`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_familles_selections`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_familles_selections_articles`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_famille_thumbnail`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_filtre`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_fournisseur`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_group_livraison`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_keyword`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_keyword_article`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_keyword_corresp`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_markets`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_market_restrictions`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_marque`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_modele`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_panier`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_panierstypes`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_panierstypes_details`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_panier_detail`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_pr1`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_pr2`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_prixachat`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_prix_nets`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_promo`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_promotions`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_promotion_article`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_rcs`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_reglement`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_remises`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_remise_type_article`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_rst`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_selections`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_selections_templates`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_soldes`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_tarif`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_tarif_article`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_tarif_client`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_tarqte`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_tarspe`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_transporteur`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_wce_cloud`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_wce_cloud_element`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_wce_slidart`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_wce_slidart_element`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_wce_slideshow`');
	$db->query('TRUNCATE TABLE `dims_mod_cata_wce_slideshow_element`');

	$db->query('DELETE FROM `dims_user` WHERE `id` != '._ID_USER);
	$db->query('DELETE FROM `dims_group_user` WHERE `id_user` NOT IN ( SELECT `id` FROM `dims_user` )');
	$db->query('DELETE FROM `dims_group` WHERE id NOT IN ( SELECT `id_group` FROM `dims_group_user` )');
	$db->query('DELETE FROM `dims_mod_business_contact` WHERE id NOT IN ( SELECT `id_contact` FROM `dims_user` )');
	$db->query('TRUNCATE TABLE `dims_mod_business_tiers`');

	$db->query('TRUNCATE TABLE `dims_keywords`');
	$db->query('TRUNCATE TABLE `dims_keywords_blacklist`');
	$db->query('TRUNCATE TABLE `dims_keywords_campaigncache`');
	$db->query('TRUNCATE TABLE `dims_keywords_corresp`');
	$db->query('TRUNCATE TABLE `dims_keywords_index`');
	$db->query('TRUNCATE TABLE `dims_keywords_metafield`');
	$db->query('TRUNCATE TABLE `dims_keywords_metaphone`');
	$db->query('TRUNCATE TABLE `dims_keywords_ordercache`');
	$db->query('TRUNCATE TABLE `dims_keywords_preindex`');
	$db->query('TRUNCATE TABLE `dims_keywords_sentence`');
	$db->query('TRUNCATE TABLE `dims_keywords_temp`');
	$db->query('TRUNCATE TABLE `dims_keywords_usercache`');
	$db->query('TRUNCATE TABLE `dims_error`');
}


if (_IMPORT_FAMILLES) {
	$db->query('DROP TABLE IF EXISTS `import_familles` ;');
	$db->query('CREATE TABLE `import_familles` (
			`code` int(11) unsigned NOT NULL,
			`label` varchar(255) NOT NULL,
			`parent_code` int(11) unsigned NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

	loadFile(_FILENAME_FAMILLES, 'import_familles', 'utf8');

	$id = 2;
	$a_fams = array(
		'0' => array(
			'id' 			=> 1,
			'code' 			=> '0',
			'label'		 	=> 'Catalogue',
			'id_parent' 	=> 0,
			'parents' 		=> '0',
			'depth' 		=> 1,
			'position' 		=> 1
			));
	$a_positions = array();
	$a_errors = array();

	$rs = $db->query('SELECT * FROM `import_familles`');
	while ($row = $db->fetchrow($rs)) {
		foreach ($row as $k => $v) { $row[$k] = trim($v); }

		// Le code de la famille parente est soit dans la 1e colonne,
		// soit il est dans le label
		if ($row['code'] > 0) {
			$code = $row['code'];
			$label = $row['label'];
			$parent_code = $row['parent_code'];
		}
		else {
			$code = (int)substr($row['label'], 0, strpos($row['label'], ' '));
			$label = substr($row['label'], strpos($row['label'], ' ') + 1);
			$parent_code = $row['parent_code'];
		}

		// On vérifie que la famille parente existe
		if ($parent_code > 0 && !isset($a_fams[$parent_code])) {
			die('ERREUR : '.$parent_code.' n\'existe pas dans les familles !'."\n\n");
		}

		// On vérifie que le code n'est pas déjà utilisé sur une autre famille
		if (isset($a_fams[$code])) {
			$a_errors[] = $code.' est déjà utilisé : "'.$a_fams[$code]['label'].'"';
		}

		// On gère la position par famille
		if (!isset($a_positions[$a_fams[$parent_code]['id']])) {
			$a_positions[$a_fams[$parent_code]['id']] = 1;
		}

		if (!isset($a_fams[$code])) {
			$a_fams[$code] = array(
				'id' 				=> $id++,
				'code' 				=> $code,
				'label' 			=> $label,
				'id_parent'			=> $a_fams[$parent_code]['id'],
				'parents'			=> $a_fams[$parent_code]['parents'].';'.$a_fams[$parent_code]['id'],
				'depth' 			=> $a_fams[$parent_code]['depth'] + 1,
				'position' 			=> $a_positions[$a_fams[$parent_code]['id']]++
				);
		}
	}

	if (sizeof($a_errors)) {
		print_r($a_errors);
		die();
	}

	foreach ($a_fams as $fam) {
		$famille = new cata_famille();
		$famille->open($fam['id']);

		if ($famille->isNew()) {
			$famille = new cata_famille();
			$famille->init_description(true);
			$famille->fields['date_create'] 	= _NOW;
			$famille->fields['user_create'] 	= _ID_USER;
		}

		$famille->fields['id_lang'] 		= _ID_LANG;
		$famille->fields['code'] 			= $fam['code'];
		$famille->fields['label'] 			= $fam['label'];
		$famille->fields['id_parent'] 		= $fam['id_parent'];
		$famille->fields['parents'] 		= $fam['parents'];
		$famille->fields['depth'] 			= $fam['depth'];
		$famille->fields['position'] 		= $fam['position'];
		$famille->fields['published'] 		= 1;
		$famille->fields['visible'] 		= 1;
		$famille->fields['id_user'] 		= _ID_USER;
		$famille->fields['id_module'] 		= _ID_MODULE;
		$famille->fields['id_workspace'] 	= _ID_WORKSPACE;
		$famille->fields['date_modify'] 	= _NOW;
		$famille->fields['user_modify'] 	= _ID_USER;
		$famille->save();
	}

	$db->query('DROP TABLE IF EXISTS `import_familles` ;');
}

if (_IMPORT_UTILISATEURS) {
	// Chargement de la totalité des utilisateurs de DIMS
	$a_users = array();
	$rs = $db->query('SELECT * FROM `dims_user` WHERE `erp_id` != ""');
	while ($row = $db->fetchrow($rs)) {
		if (!isset($a_users[$row['erp_id']])) {
			$a_users[$row['erp_id']] = $row;
		}
	}

	// Chargement du groupe des commerciaux
	$users_group = group::getByCode('COMMERCIAUX');

	// Comparaison des utilisateurs de CAAHMRO avec ceux de DIMS
	$rs = $db->query('SELECT * FROM `IND_DIMS_UTILISATEURS` GROUP BY `USE_INI`');
	while ($row = $db->fetchrow($rs)) {
		// User inexistant => création
		if (!isset($a_users[$row['USE_INI']])) {
			$password = passgen();
			$dims->getPasswordHash($password, $hash, $salt);

			$user = new user();
			$user->fields['login'] 				= $row['USE_INI'];
			$user->fields['lastname'] 			= $row['USE_DESCR'];
			$user->fields['email'] 				= $row['USE_EMAIL'];
			$user->fields['phone'] 				= $row['USE_PHONE'];
			$user->fields['fax'] 				= $row['USE_FAX'];
			$user->fields['initial_password'] 	= $password;
			$user->fields['password'] 			= $hash;
			$user->fields['salt'] 				= $salt;
			$user->fields['date_creation'] 		= _NOW;
			$user->fields['status'] 			= !$row['USE_BLOQUED'];
			$user->fields['erp_id'] 			= $row['USE_INI'];
			$user->fields['representative_id'] 	= $row['PAC_REPRESENTATIVE_ID'];
			$user->save();

			$user->attachtogroup($users_group->get('id'), 42);
		}
		else {
			// User modifié => mise à jour
			// ATTENTION ! Le champ status est à l'inverse de USE_BLOQUED
			if (
				$a_users[$row['USE_INI']]['email'] != $row['USE_EMAIL']
				|| $a_users[$row['USE_INI']]['phone'] != $row['USE_PHONE']
				|| $a_users[$row['USE_INI']]['fax'] != $row['USE_FAX']
				|| $a_users[$row['USE_INI']]['lastname'] != $row['USE_DESCR']
				|| $a_users[$row['USE_INI']]['status'] == $row['USE_BLOQUED']
				|| $a_users[$row['USE_INI']]['representative_id'] != $row['PAC_REPRESENTATIVE_ID']
			) {
				$user = new user();
				$user->openFromResultSet($a_users[$row['USE_INI']]);
				$user->fields['lastname'] 			= $row['USE_DESCR'];
				$user->fields['email'] 				= $row['USE_EMAIL'];
				$user->fields['phone'] 				= $row['USE_PHONE'];
				$user->fields['fax'] 				= $row['USE_FAX'];
				$user->fields['status'] 			= !$row['USE_BLOQUED'];
				$user->fields['representative_id'] 	= $row['PAC_REPRESENTATIVE_ID'];
				$user->save();

				$contact = $user->getContact();
				$contact->fields['lastname'] 		= $user->fields['lastname'];
				$contact->fields['email'] 			= $user->fields['email'];
				$contact->fields['phone'] 			= $user->fields['phone'];
				$contact->fields['fax'] 			= $user->fields['fax'];
				$contact->save();
			}
		}
	}
}

if (_IMPORT_TVA && table_exists('IND_DIMS_DICO_TVA')) {
	$db->query('DROP TABLE IF EXISTS `dims_mod_cata_tva_temp`;');
	$db->query('CREATE TABLE `dims_mod_cata_tva_temp` (
			PRIMARY KEY (`id_tva`,`id_pays`),
			KEY `id_tva` (`id_tva`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		SELECT * FROM `dims_mod_cata_tva` WHERE 1 = 0;');

	$db->query('INSERT INTO `dims_mod_cata_tva_temp` (
			`id_tva`,
			`id_pays`,
			`tx_tva`,
			`description`,
			`timestp_create`,
			`timestp_modify`,
			`id_module`,
			`id_user`,
			`id_workspace`
		) SELECT
			`DIC_TYPE_VAT_GOOD_ID`,
			'._ID_COUNTRY.',
			`RATE`,
			`DIC_DESCRIPTION`,
			'._NOW.',
			'._NOW.',
			'._ID_MODULE.',
			'._ID_USER.',
			'._ID_WORKSPACE.'
		FROM 	`IND_DIMS_DICO_TVA`
		GROUP BY `DIC_TYPE_VAT_GOOD_ID`');

	// On remplace la table que si on a des données dedans
	// Sinon on remonte une erreur
	$rs = $db->query('SELECT COUNT(*) AS nb FROM `dims_mod_cata_tva_temp`');
	$row = $db->fetchrow($rs);
	if ($row['nb'] > 0) {
		$db->query('DROP TABLE `dims_mod_cata_tva` ;');
		$db->query('ALTER TABLE `dims_mod_cata_tva_temp` RENAME `dims_mod_cata_tva` ;');
	}
	else {
		raiseError('TVA', 'La table tva est vide');
	}
}

if (_IMPORT_ARTICLES) {
	// // On vérifie que la table des stocks n'est pas vide
	// $rs = $db->query('SELECT COUNT(*) AS nb_lignes FROM `IND_DIMS_STOCK`');
	// $row = $db->fetchrow($rs);
	// if ($row['nb_lignes'] == 0) {
	// 	raiseError('stocks', 'La table IND_DIMS_STOCK est vide');
	// 	exit(E_STOCK_EMPTY);
	// }

	$db->query('
		ALTER TABLE `IND_DIMS_DESCRIPTIONS_PRODUITS`
		ADD INDEX `CODE_REFERENCE` (`CODE_REFERENCE`);');
	$db->query('
		ALTER TABLE `IND_DIMS_STOCK`
		ADD INDEX `CODE_REFERENCE` (`CODE_REFERENCE`);');
	$db->query('
		ALTER TABLE `IND_DIMS_PHYTO`
		ADD INDEX `GCO_GOOD_ID` (`GCO_GOOD_ID`);');


	// Création du filtre BIO si non existant
	$bio_filter_label = 'Utilisable en agri-bio';
	$filter = cata_champ::getObjectBylabel($bio_filter_label);
	if (is_null($filter)) {
		$filter = new cata_champ();
		$filter->init_description(true);
		$filter->setugm();
		$filter->setLabel($bio_filter_label);
		$filter->setType('liste');
		$filter->setFiche(1);
		$filter->setFiltre(1);
		$filter->setPermanent(1);
		$filter->save();

		// Catégorie
		$go = $filter->getMyGlobalObject();
		$go->addLink($goCat);

		// Affectation des langues et des valeurs
		// Les seules valeurs disponibles sont 'Oui' et 'Non'
		foreach(cata_param::getActiveLang() as $id => $lg){
			$filter->addLibelle($id, $bio_filter_label);
			$filter->addValues($id, array(dims_constant::getVal('YES'), dims_constant::getVal('NO')));
		}
	}
	$bio_field_id = $filter->get('id');
	$bio_value_no = null;
	$bio_value_yes = null;

	// Valeurs du filtre pour la langue par défaut
	$default_lang = cata_param::getDefaultLang();
	foreach ($filter->getvaleurs($default_lang) as $filter_value) {
		if ($filter_value->get('valeur') == dims_constant::getVal('YES')) {
			$bio_value_yes = $filter_value->get('id');
		}
		elseif ($filter_value->get('valeur') == dims_constant::getVal('NO')) {
			$bio_value_no = $filter_value->get('id');
		}
	}

	// Chargement de la liste des articles
	$a_articles = array();
	$rs = $db->query('SELECT `id`, `erp_id`, `reference`, `label`, `date_modify`, `poids`, `cond`, `ctva`, `certiphyto`, `taxe_certiphyto`, `status`, `fields'.$bio_field_id.'` FROM `dims_mod_cata_article`');
	while ($row = $db->fetchrow($rs)) {
		$a_articles[$row['erp_id']] = $row;
	}

	// Pour indexation des articles créés / mis à jour
	$a_to_index = array();

	// Mise à jour des existants
	$rs = $db->query('SELECT 	p.*,
								dp.`DES_LONG_DESCRIPTION`,
								dp.`DES_FREE_DESCRIPTION`,
								ph.`MONTANT_1`,
								ph.`MONTANT_2`,
								ph.`MONTANT_3`
		FROM 	`IND_DIMS_PRODUIT` p
		LEFT JOIN 	`IND_DIMS_DESCRIPTIONS_PRODUITS` dp
		ON 			dp.`CODE_REFERENCE` = p.`CODE_REFERENCE`
		LEFT JOIN 	`IND_DIMS_PHYTO` ph
		ON 			ph.`GCO_GOOD_ID` = p.`GCO_GOOD_ID` ;');

	while ($row = $db->fetchrow($rs)) {
		if (isset($a_articles[$row['GCO_GOOD_ID']])) {
			$art = $a_articles[$row['GCO_GOOD_ID']];

			$date_modify = 	substr($row['GCO_GOOD_A_DATEMOD'], 0, 4) .
							substr($row['GCO_GOOD_A_DATEMOD'], 5, 2) .
							substr($row['GCO_GOOD_A_DATEMOD'], 8, 2) .
							substr($row['GCO_GOOD_A_DATEMOD'], 11, 2) .
							substr($row['GCO_GOOD_A_DATEMOD'], 14, 2) .
							substr($row['GCO_GOOD_A_DATEMOD'], 17, 2);

			if ( $date_modify != '' && $date_modify != $art['date_modify'] ) {
				$db->query('UPDATE `dims_mod_cata_article` SET
						`date_modify` 				= '.$date_modify.',
						`label` 					= "'.addslashes($row['DES_LONG_DESCRIPTION']).'",
						`prcs` 						= '.$row['PRCS_CAAHMRO'].',
						`poids` 					= "'.$row['POID'].'",
						`fam` 						= "'.$row['DIC_GOOD_LINE_ID'].'",
						`ssfam` 					= "'.$row['DIC_GOOD_FAMILY_ID'].'",
						`cond` 						= "'.$row['CONDITIONNEMENT'].'",
						`ctva` 						= "'.$row['DIC_TYPE_VAT_GOOD_ID'].'",
						`certiphyto` 				= "'.$row['CERTIPHYTO'].'",
						`taxe_certiphyto` 			= "'.($row['MONTANT_1'] + $row['MONTANT_2'] + $row['MONTANT_3']).'",
						`status` 					= "'.(($row['STATUT'] == 'Actif') ? 'OK' : 'DELETED').'",
						`dangerousness_code` 		= "'.$row['CODE_DANGEREUSITE2'].'",
						`fields'.$bio_field_id.'` 	= "'.(($row['BIO'] == 'BIO') ? $bio_value_yes : $bio_value_no).'",
						`volume`                    = "'.$row['IND_DIMS_VOLUME'].'"
					WHERE `id` = '.$art['id']);

				$a_to_index[] = $art['id'];

			}
		}
	}

	$db->query('UPDATE `dims_mod_cata_article_famille` af
		INNER JOIN 	`dims_mod_cata_article` a
		ON 			a.`id` = af.`id_article`
		INNER JOIN 	`IND_DIMS_PRODUIT` idp
		ON 			idp.`CODE_REFERENCE` = a.`reference`
		INNER JOIN 	`dims_mod_cata_famille` f
		ON 			f.`code` = idp.`DIC_GOOD_FAMILY_ID`
		SET 	af.`id_famille` = f.`id` ;');


	// Mise à jour des stocks séparément pour le cas ou la table des stocks est vide
	$db->query('UPDATE `dims_mod_cata_article` a, `IND_DIMS_PRODUIT` p, `IND_DIMS_STOCK` s SET
			a.`qte` = s.`STOCK_CAAHMRO` + s.`STOCK_ARPIGNY` + s.`STOCK_CHARLY`
		WHERE 	a.`reference` = p.`CODE_REFERENCE`
		AND 	p.`CODE_REFERENCE` = s.`CODE_REFERENCE` ;');

	// Chargement de la liste des sociétés
	$a_companies = array();
	$rs = $db->query('SELECT `id`, `code` FROM `dims_mod_cata_companies`');
	while ($row = $db->fetchrow($rs)) {
		$a_companies[$row['code']] = $row['id'];
	}

	// Chargement des stocks existants dans un tableau
	// pour éviter de tout réinsérer à chaque fois
	$a_stocks = array();
	$rs = $db->query('SELECT s.`id`, s.`id_company`, s.`id_article`, s.`held_in_stock`, s. `end_of_life`, s.`stock`
		FROM `dims_mod_cata_stocks` s
		INNER JOIN `dims_mod_cata_article` a
		ON a.`id` = s.`id_article`
		INNER JOIN `IND_DIMS_PRODUIT` p
		ON p.`GCO_GOOD_ID` = a.`erp_id` ;');
	while ($row = $db->fetchrow($rs)) {
		$a_stocks[$row['id_article']][$row['id_company']] = array(
			'held_in_stock' 	=> $row['held_in_stock'],
			'end_of_life' 		=> $row['end_of_life'],
			'stock' 			=> $row['stock']
			);
	}

	// On conserve les stocks des articles existants avant suppression
	$rs = $db->query('SELECT a.`id`, p.`MODE_CAAHMRO`, p.`MODE_ARPIGNY`, p.`MODE_CHARLY`, s.`STOCK_CAAHMRO`, s.`STOCK_ARPIGNY`, s.`STOCK_CHARLY`
		FROM `dims_mod_cata_article` a
		INNER JOIN 	`IND_DIMS_PRODUIT` p
		ON 			p.`GCO_GOOD_ID` = a.`erp_id`
		INNER JOIN 	`IND_DIMS_STOCK` s
		ON 			s.`GCO_GOOD_ID` = a.`erp_id` ;');
	while ($row = $db->fetchrow($rs)) {
		foreach ($a_companies as $company_code => $company_id) {
			$depot_code = $a_company_stock[$company_code];
			if (isset($depot_code)) {

				// on met à jour que si ça a changé
				if (isset($a_stocks[$row['id']][$company_id])) {
					if (
						$a_stocks[$row['id']][$company_id]['held_in_stock'] != (($row['MODE_'.$depot_code] == 'STOCK') ? 1 : 0) ||
						$a_stocks[$row['id']][$company_id]['end_of_life'] != (($row['FIN_DE_VIE_'.$depot_code] == 1) ? 1 : 0) ||
						$a_stocks[$row['id']][$company_id]['stock'] != $row['STOCK_'.$depot_code]
					) {
						$a_stocks[$row['id']][$company_id] = array(
							'action' 			=> 'UPDATE',
							'held_in_stock' 	=> ($row['MODE_'.$depot_code] == 'STOCK') ? 1 : 0,
							'end_of_life' 		=> ($row['FIN_DE_VIE_'.$depot_code] == 1) ? 1 : 0,
							'stock' 			=> $row['STOCK_'.$depot_code]
							);
					}
					else {
						unset($a_stocks[$row['id']][$company_id]);
					}
				}
				else {
					$a_stocks[$row['id']][$company_id] = array(
						'action' 			=> 'INSERT',
						'held_in_stock' 	=> ($row['MODE_'.$depot_code] == 'STOCK') ? 1 : 0,
						'end_of_life' 		=> ($row['FIN_DE_VIE_'.$depot_code] == 1) ? 1 : 0,
						'stock' 			=> $row['STOCK_'.$depot_code]
						);
				}
			}
		}
	}

	// Suppression des existants
	$db->query('DELETE p.*
		FROM 	`dims_mod_cata_article` a,
				`IND_DIMS_PRODUIT` p
		WHERE 	a.`reference` = p.`CODE_REFERENCE`');

	// Suppression des stocks séparément pour le cas ou la table des stocks est vide
	$db->query('DELETE s.*
		FROM 	`dims_mod_cata_article` a,
				`IND_DIMS_STOCK` s
		WHERE 	a.`reference` = s.`CODE_REFERENCE`');

	// Suppression des descriptions séparément pour le cas ou la table des descriptions est vide
	$db->query('DELETE dp.*
		FROM 	`dims_mod_cata_article` a,
				`IND_DIMS_DESCRIPTIONS_PRODUITS` dp
		WHERE 	a.`reference` = dp.`CODE_REFERENCE`');

	// Insertion des nouveaux
	$db->query('INSERT INTO `dims_mod_cata_article` (
			`id_lang`,
			`reference`,
			`label`,
			`description`,
			`erp_id`,
			`published`,
			`certiphyto`,
			`taxe_certiphyto`,
			`dangerousness_code`,
			`status`,
			`ctva`,
			`prcs`,
			`poids`,
			`volume`,
			`fam`,
			`ssfam`,
			`uvente`,
			`cond`,
			`qte`,
			`id_user`,
			`id_module`,
			`id_workspace`,
			`date_create`,
			`user_create`,
			`date_modify`,
			`user_modify`,
			`fields'.$bio_field_id.'`
		) SELECT
			'._ID_LANG.' AS `id_lang`,
			p.`CODE_REFERENCE` AS `reference`,
			dp.`DES_LONG_DESCRIPTION` AS `label`,
			dp.`DES_FREE_DESCRIPTION` AS `description`,
			p.`GCO_GOOD_ID` AS `erp_id`,
			1 AS `published`,
			p.`CERTIPHYTO` AS `certiphyto`,
			ph.`MONTANT_1` + ph.`MONTANT_2` + ph.`MONTANT_3` AS `taxe_certiphyto`,
			p.`CODE_DANGEREUSITE2` AS `dangerousness_code`,
			IF (p.`STATUT` = \'Actif\', \'OK\', \'DELETED\') AS `status`,
			p.`DIC_TYPE_VAT_GOOD_ID` AS `ctva`,
			p.`PRCS_CAAHMRO` AS `prcs`,
			p.`POID` AS `poids`,
			p.`IND_DIMS_VOLUME` AS `volume`,
			p.`DIC_GOOD_LINE_ID` AS `fam`,
			p.`DIC_GOOD_FAMILY_ID` AS `ssfam`,
			1 AS `uvente`,
			p.`CONDITIONNEMENT` AS `cond`,
			s.`STOCK_CAAHMRO` + s.`STOCK_ARPIGNY` + s.`STOCK_CHARLY` AS `qte`,
			'._ID_USER.' AS `id_user`,
			'._ID_MODULE.' AS `id_module`,
			'._ID_WORKSPACE.' AS `id_workspace`,
			CONCAT (
				SUBSTRING(p.`GCO_GOOD_A_DATECRE`, 1, 4),
				SUBSTRING(p.`GCO_GOOD_A_DATECRE`, 6, 2),
				SUBSTRING(p.`GCO_GOOD_A_DATECRE`, 9, 2),
				SUBSTRING(p.`GCO_GOOD_A_DATECRE`, 12, 2),
				SUBSTRING(p.`GCO_GOOD_A_DATECRE`, 15, 2),
				SUBSTRING(p.`GCO_GOOD_A_DATECRE`, 18, 2)
			) AS `date_create`,
			'._ID_USER.' AS `user_create`,
			CONCAT (
				SUBSTRING(p.`GCO_GOOD_A_DATEMOD`, 1, 4),
				SUBSTRING(p.`GCO_GOOD_A_DATEMOD`, 6, 2),
				SUBSTRING(p.`GCO_GOOD_A_DATEMOD`, 9, 2),
				SUBSTRING(p.`GCO_GOOD_A_DATEMOD`, 12, 2),
				SUBSTRING(p.`GCO_GOOD_A_DATEMOD`, 15, 2),
				SUBSTRING(p.`GCO_GOOD_A_DATEMOD`, 18, 2)
			) AS `date_modify`,
			'._ID_USER.' AS `user_modify`,
			IF (p.`BIO` = "BIO", 1, 0) AS `fields'.$bio_field_id.'`
		FROM `IND_DIMS_PRODUIT` p
		INNER JOIN 	`IND_DIMS_DESCRIPTIONS_PRODUITS` dp
		ON 			dp.`GCO_GOOD_ID` = p.`GCO_GOOD_ID`
		INNER JOIN 	`IND_DIMS_STOCK` s
		ON 			s.`GCO_GOOD_ID` = p.`GCO_GOOD_ID`
		LEFT JOIN 	`IND_DIMS_PHYTO` ph
		ON 			ph.`GCO_GOOD_ID` = p.`GCO_GOOD_ID`
		GROUP BY p.`CODE_REFERENCE`;');

	// Génération des rewrites
	$rs = $db->query('SELECT `id`, `label` FROM `dims_mod_cata_article` WHERE ISNULL(`urlrewrite`) ORDER BY id_lang, id');
	while ($row = $db->fetchrow($rs)) {
		$db->query('UPDATE `dims_mod_cata_article` SET `urlrewrite` = "'.cata_genSmartArtRewrite($row['label']).'" WHERE `id` = '.$row['id']);
	}

	// Insertion des liens article / famille
	$db->query('INSERT INTO `dims_mod_cata_article_famille` (
			`id_article`,
			`id_famille`,
			`position`,
			`id_module`,
			`id_user`,
			`id_workspace`
		) SELECT
			a.`id` AS `id_article`,
			f.`id` AS `id_famille`,
			1 AS `position`,
			'._ID_MODULE.' AS `id_module`,
			'._ID_USER.' AS `id_user`,
			'._ID_WORKSPACE.' AS `id_workspace`
		FROM 	`dims_mod_cata_article` a
		INNER JOIN 	`IND_DIMS_PRODUIT` idp
		ON 			a.`reference` = idp.`CODE_REFERENCE`
		INNER JOIN 	`dims_mod_cata_famille` f
		ON 			f.`code` = idp.`DIC_GOOD_FAMILY_ID` ;');

	// On indexe les articles créés
	$rs = $db->query('SELECT a.`id`
		FROM 	`dims_mod_cata_article` a
		INNER JOIN 	`IND_DIMS_PRODUIT` p
		ON 			p.`CODE_REFERENCE` = a.`reference`');
	while ($row = $db->fetchrow($rs)) {
		$a_to_index[] = $row['id'];
	}

	// Insertion / mise à jour des stocks
	foreach ($a_stocks as $id_article => $companies) {
		foreach ($companies as $id_company => $values) {
			switch ($values['action']) {
				case 'INSERT':
					$db->query('INSERT INTO `dims_mod_cata_stocks` (
							`id_company`,
							`id_article`,
							`held_in_stock`,
							`end_of_life`,
							`stock`,
							`id_user`,
							`id_module`,
							`id_workspace`,
							`timestp_create`,
							`timestp_modify`
						) VALUES (
							'.$id_company.',
							'.$id_article.',
							'.$values['held_in_stock'].',
							'.$values['end_of_life'].',
							'.$values['stock'].',
							'._ID_USER.',
							'._ID_MODULE.',
							'._ID_WORKSPACE.',
							'._NOW.',
							'._NOW.'
						);');
					break;
				case 'UPDATE':
					$db->query('UPDATE `dims_mod_cata_stocks`
						SET 	`held_in_stock` = '.$values['held_in_stock'].',
								`end_of_life` = '.$values['end_of_life'].',
								`stock` = '.$values['stock'].',
								`timestp_modify` = '._NOW.'
						WHERE 	`id_company` = '.$id_company.'
						AND 	`id_article` = '.$id_article.' ;');
					break;
			}
		}
	}


	// Indexation
	if (sizeof($a_to_index)) {
		$rs = $db->query('SELECT * FROM `dims_mod_cata_article` WHERE id IN ('.implode(',', $a_to_index).')');
		while ($row = $db->fetchrow($rs)) {
			$article = new article();
			$article->prepareindexbeforechanges(true);
			$article->openFromResultSet($row);
			// Pour s'assurer d'avoir les meta_fields
			// et permet d'indexer que si y'a des changements détectés
			$article->prepareindex();
		}
	}
}

if (_IMPORT_TARIFS) {

	// Chargement du param SYNCHRO_FULL
	$synchro_full = 0;
	$rs = $db->query('SELECT * FROM `PARAM`');
	if ($db->numrows($rs)) {
		$row = $db->fetchrow($rs);
		if (isset($row['SYNCHRO_FULL'])) {
			$synchro_full = $row['SYNCHRO_FULL'];
		}
	}

	// Création des tables temporaires
	$db->query('DROP TABLE IF EXISTS `dims_mod_cata_prix_nets_temp`;');
	$db->query("CREATE TABLE `dims_mod_cata_prix_nets_temp` (
			KEY `code_cm` (`code_cm`),
			KEY `reference` (`reference`),
			KEY `type` (`type`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		SELECT * FROM `dims_mod_cata_prix_nets`
		WHERE $synchro_full = 0 ;");

	$db->query('DROP TABLE IF EXISTS `dims_mod_cata_tarqte_temp`;');
	$db->query("CREATE TABLE `dims_mod_cata_tarqte_temp` (
			KEY `reference` (`reference`),
			KEY `id_article` (`id_article`),
			KEY `code_cm` (`code_cm`),
			KEY `qtedeb` (`qtedeb`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		SELECT * FROM `dims_mod_cata_tarqte`
		WHERE $synchro_full = 0 ;");

	// On vérifie que la table des tarifs n'est pas vide
	$rs = $db->query('SELECT COUNT(*) AS nb_lignes FROM `IND_DIMS_PRODUITS_TARIFS`');
	$row = $db->fetchrow($rs);
	if ($row['nb_lignes'] == 0) {
		// On ne remonte plus d'erreur si la table est vide car elle l'est souvent en mode partiel
		// raiseError('Tarifs', 'La table IND_DIMS_PRODUITS_TARIFS est vide');
		exit(E_TARIFS_EMPTY);
	}

	// Chargement des id_articles pour insertion dans les tables
	$a_id_articles = array();
	$rs = $db->query('SELECT `id`, `reference` FROM `dims_mod_cata_article` WHERE `status` = "OK"');
	while ($row = $db->fetchrow($rs)) {
		$a_id_articles[$row['reference']] = $row['id'];
	}

	// Chargement des marchés pour les tarifs
	$a_markets = array();
	$rs = $db->query('SELECT * FROM `dims_mod_cata_markets` WHERE `id_module` = '._ID_MODULE.' AND `id_workspace` = '._ID_WORKSPACE);
	while ($row = $db->fetchrow($rs)) {
		$market = new cata_market();
		$market->openFromResultSet($row);
		$a_markets[$row['code']] = $market;
	}


	// On supprime les lignes qui ne sont pas de nature tarif "A facturer"
	// pour être certain de ne pas avoir de prix d'achat en ligne
	$db->query('DELETE FROM `IND_DIMS_PRODUITS_TARIFS` WHERE `NATURE_TARIF` != "A_FACTURER"');

	// Dans le cas d'une mise à jour partielle, on supprime les lignes
	// correspondantes à celles qu'on va insérer

	if (!$synchro_full) {
		$db->query('DELETE pn.*
			FROM `dims_mod_cata_prix_nets_temp` pn, `IND_DIMS_PRODUITS_TARIFS` pt
			WHERE 	pn.`reference` = pt.`CODE_REFERENCE`
			AND 	pn.`code_cm` = pt.`TYPE_TARIF`
			AND 	pn.`type` = "M" ;');
		$db->query('DELETE pn.*
			FROM `dims_mod_cata_prix_nets_temp` pn, `IND_DIMS_PRODUITS_TARIFS` pt
			WHERE 	pn.`reference` = pt.`CODE_REFERENCE`
			AND 	pn.`code_cm` = pt.`CODE_CLIENT`
			AND 	pn.`type` = "C" ;');

		$db->query('DELETE tq.*
			FROM `dims_mod_cata_tarqte_temp` tq, `IND_DIMS_PRODUITS_TARIFS` pt
			WHERE 	tq.`reference` = pt.`CODE_REFERENCE`
			AND 	tq.`code_cm` = 0
			AND 	pt.`TYPE_TARIF` = "'._DEFAULT_TARIF.'"
			AND 	tq.`qtedeb` = pt.`QUANTITE_DEPART` ;');
		$db->query('DELETE tq.*
			FROM `dims_mod_cata_tarqte_temp` tq, `IND_DIMS_PRODUITS_TARIFS` pt
			WHERE 	tq.`reference` = pt.`CODE_REFERENCE`
			AND 	tq.`code_cm` = pt.`TYPE_TARIF`
			AND 	tq.`qtedeb` = pt.`QUANTITE_DEPART` ;');
	}

	// Pour rester au plus proche du modèle actuel, le tarif par défaut sera dans la fiche article
	// Pour chaque tarif qui n'est pas celui par défaut, on crée un marché
	// Tous les clients ayant ce tarif seront attachés à ce marché
	// Si un client a un prix marché, on lui fera un prix net (type='C')

	// Ajouter un paramètre de calcul de tarif
	// - priorité au marché (ex: Papeteries d'Arvor)
	// - priorité au client (ex: Caahmro)

	$rs = $db->query('SELECT * FROM `IND_DIMS_PRODUITS_TARIFS`
		ORDER BY `GCO_GOOD_ID`, `PAC_THIRD_ID`, `TYPE_TARIF`, `QUANTITE_DEPART`');
	while ($row = $db->fetchrow($rs)) {

		// Pour les articles suspendus, il n'est pas nécessaire de mettre à jour le tarif
		if (isset($a_id_articles[$row['CODE_REFERENCE']])) {

			// Tarifs généraux
			if (is_null($row['PAC_THIRD_ID'])) {

				// Tarif par défaut (prix public)
				if ($row['TYPE_TARIF'] == _DEFAULT_TARIF) {
					// Prix à l'unité
					if ($row['QUANTITE_DEPART'] == 0) {
						$db->query('UPDATE `dims_mod_cata_article` SET `putarif_0` = '.$row['TARIF'].' WHERE `reference` = "'.$row['CODE_REFERENCE'].'"');
					}
					// Tarif dégressif
					else {
						$db->query('INSERT INTO `dims_mod_cata_tarqte_temp` (
								`type`,
								`code_cm`,
								`id_article`,
								`reference`,
								`qtedeb`,
								`qtefin`,
								`puqte`,
								`datedeb`,
								`datefin`,
								`id_user`,
								`id_module`,
								`id_workspace`,
								`timestp_create`,
								`timestp_modify`
							) VALUES (
								"",
								"0",
								"'.$a_id_articles[$row['CODE_REFERENCE']].'",
								"'.$row['CODE_REFERENCE'].'",
								"'.$row['QUANTITE_DEPART'].'",
								"'.$row['QUANTITE_FIN'].'",
								"'.$row['TARIF'].'",
								"20010101000000",
								"20991231235959",
								'._ID_USER.',
								'._ID_MODULE.',
								'._ID_WORKSPACE.',
								'._NOW.',
								'._NOW.'
							)');
					}
				}
				// Autres tarifs
				else {
					// Création du marché si non existant
					if (!isset($a_markets[$row['TYPE_TARIF']])) {
						$market = new cata_market();
						$market->fields['code'] = $row['TYPE_TARIF'];
						$market->fields['label'] = $row['TYPE_TARIF'];
						$market->fields['date_from'] = '20010101000000';
						$market->fields['date_to'] = '20991231235959';
						$market->fields['id_user'] = _ID_USER;
						$market->fields['id_module'] = _ID_MODULE;
						$market->fields['id_workspace'] = _ID_WORKSPACE;
						$market->fields['timestp_create'] = _NOW;
						$market->fields['timestp_modify'] = _NOW;
						$market->save();

						$a_markets[$row['TYPE_TARIF']] = $market;
					}
					else {
						$market = $a_markets[$row['TYPE_TARIF']];
					}

					// Prix à l'unité
					if ($row['QUANTITE_DEPART'] == 0) {
						$db->query('INSERT INTO `dims_mod_cata_prix_nets_temp` (
								`type`,
								`code_cm`,
								`reference`,
								`puht`
							) VALUES (
								"M",
								"'.$market->get('code').'",
								"'.$row['CODE_REFERENCE'].'",
								"'.$row['TARIF'].'"
							)');
					}
					// Tarif dégressif
					else {
						$db->query('INSERT INTO `dims_mod_cata_tarqte_temp` (
								`type`,
								`code_cm`,
								`id_article`,
								`reference`,
								`qtedeb`,
								`qtefin`,
								`puqte`,
								`datedeb`,
								`datefin`,
								`id_user`,
								`id_module`,
								`id_workspace`,
								`timestp_create`,
								`timestp_modify`
							) VALUES (
								"M",
								"'.$market->get('code').'",
								"'.$a_id_articles[$row['CODE_REFERENCE']].'",
								"'.$row['CODE_REFERENCE'].'",
								"'.$row['QUANTITE_DEPART'].'",
								"'.$row['QUANTITE_FIN'].'",
								"'.$row['TARIF'].'",
								"20010101000000",
								"20991231235959",
								'._ID_USER.',
								'._ID_MODULE.',
								'._ID_WORKSPACE.',
								'._NOW.',
								'._NOW.'
							)');
					}
				}
			}

			// Tarifs spéciaux client (marchés)
			else {
				// Prix à l'unité
				if ($row['QUANTITE_DEPART'] == 0) {
					$db->query('INSERT INTO `dims_mod_cata_prix_nets_temp` (
							`type`,
							`code_cm`,
							`reference`,
							`puht`
						) VALUES (
							"C",
							"'.$row['CODE_CLIENT'].'",
							"'.$row['CODE_REFERENCE'].'",
							"'.$row['TARIF'].'"
						)');
				}
				// Tarif dégressif
				else {
					$db->query('INSERT INTO `dims_mod_cata_tarqte_temp` (
							`type`,
							`code_cm`,
							`id_article`,
							`reference`,
							`qtedeb`,
							`qtefin`,
							`puqte`,
							`datedeb`,
							`datefin`,
							`id_user`,
							`id_module`,
							`id_workspace`,
							`timestp_create`,
							`timestp_modify`
						) VALUES (
							"C",
							"'.$row['CODE_CLIENT'].'",
							"'.$a_id_articles[$row['CODE_REFERENCE']].'",
							"'.$row['CODE_REFERENCE'].'",
							"'.$row['QUANTITE_DEPART'].'",
							"'.$row['QUANTITE_FIN'].'",
							"'.$row['TARIF'].'",
							"20010101000000",
							"20991231235959",
							'._ID_USER.',
							'._ID_MODULE.',
							'._ID_WORKSPACE.',
							'._NOW.',
							'._NOW.'
						)');
				}
			}

		}
	}

	// On remplace la table que si on a des données dedans
	// Sinon on remonte une erreur
	$rs = $db->query('SELECT COUNT(*) AS nb FROM `dims_mod_cata_prix_nets_temp`');
	$row = $db->fetchrow($rs);
	if ($row['nb'] > 0) {
		$db->query('DROP TABLE `dims_mod_cata_prix_nets` ;');
		$db->query('ALTER TABLE `dims_mod_cata_prix_nets_temp` RENAME `dims_mod_cata_prix_nets` ;');
	}
	else {
		raiseError('Prix nets', 'La table prix_nets est vide');
	}

	// On remplace la table que si on a des données dedans
	// Sinon on remonte une erreur
	$rs = $db->query('SELECT COUNT(*) AS nb FROM `dims_mod_cata_tarqte_temp`');
	$row = $db->fetchrow($rs);
	if ($row['nb'] > 0) {
		$db->query('DROP TABLE `dims_mod_cata_tarqte` ;');
		$db->query('ALTER TABLE `dims_mod_cata_tarqte_temp` RENAME `dims_mod_cata_tarqte` ;');
	}
	else {
		raiseError('Tarifs dégressifs', 'La table tarqte est vide');
	}

}

if (_IMPORT_CLIENTS) {
	// chargement du niveau d'utilisateur par défaut
	$lstParam = cata_param::initComptesClients();

	// echo "1 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();

	// Création des index sur les tables de synchro
	$db->query('ALTER TABLE `IND_DIMS_CLIENT` ADD INDEX (`PER_KEY1`);');
	$db->query('ALTER TABLE `IND_DIMS_ADD_FAC` ADD INDEX (`PAC_ADDRESS_ID`);');
	$db->query('ALTER TABLE `IND_DIMS_ADD_FAC` ADD INDEX (`PAC_PERSON_ID`);');
	$db->query('ALTER TABLE `IND_DIMS_ADD_LIV` ADD INDEX (`PAC_PERSON_ID`);');

	// echo "2 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();

	// Suppression de données inutiles
	$db->query('DELETE FROM `IND_DIMS_CLIENT` WHERE ISNULL(`PER_KEY1`)');


	// Chargement de la liste des sociétés
	$a_companies = array();
	$rs = $db->query('SELECT `id`, `code` FROM `dims_mod_cata_companies`');
	while ($row = $db->fetchrow($rs)) {
		$a_companies[$row['code']] = $row['id'];
	}

	// Création des sociétés non existantes
	$rs = $db->query('SELECT `CODE_SOCIETE` FROM `IND_DIMS_CLIENT` GROUP BY `CODE_SOCIETE`');
	while ($row = $db->fetchrow($rs)) {
		if (!isset($a_companies[$row['CODE_SOCIETE']])) {
			$company = cata_company::build(array(
				'code' 		=> $row['CODE_SOCIETE'],
				'label' 	=> $row['CODE_SOCIETE'],
				));
			$company->save();
			$a_companies[$row['CODE_SOCIETE']] = $company->get('id');
		}
	}

	// echo "3 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();


	// construction des contacts existants
	$allcontacts = array();
	$rs = $db->query('SELECT * FROM `dims_mod_business_contact` WHERE `erp_id` != ""');
	while ($row = $db->fetchrow($rs)) {
		$allcontacts[$row['erp_id']] = $row;
	}

	// // construction de l'ensemble des contacts de la base
	// $alldimscontacts = array();
	// $rsc = $db->query('SELECT ct.*
	// 	FROM `IND_DIMS_CONTACT` ct
	// 	INNER JOIN `IND_DIMS_CLIENT` c
	// 	ON c.`PAC_CUSTOM_PARTNER_ID` = ct.`PP_CLI_ID`');
	// while ($rowc = $db->fetchrow($rsc)) {
	// 	if ($rowc['PP_CONTACT_ID'] != '') {
	// 		if (!isset($alldimscontacts[$rowc['PP_CLI_ID']])) {
	// 			$alldimscontacts[$rowc['PP_CLI_ID']] = array();
	// 		}
	// 		$alldimscontacts[$rowc['PP_CLI_ID']][$rowc['PP_CONTACT_ID']]=$rowc;
	// 	}
	// }


	// Création de la table temporaire
	$db->query('DROP TABLE IF EXISTS `dims_mod_cata_client_temp`;');
	$db->query('CREATE TABLE `dims_mod_cata_client_temp` (
			PRIMARY KEY (`id_client`),
			KEY `code_client` (`code_client`),
			KEY `erp_id` (`erp_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		SELECT * FROM `dims_mod_cata_client`');
	$db->query('ALTER TABLE `dims_mod_cata_client_temp` CHANGE `id_client` `id_client` int(10) unsigned NOT NULL AUTO_INCREMENT');

	$db->query('CREATE TRIGGER `after_update_cata_client_temp` AFTER UPDATE ON `dims_mod_cata_client_temp` FOR EACH ROW
		IF ISNULL(OLD.mode_paiement_id) AND NOT ISNULL(NEW.mode_paiement_id) THEN
			UPDATE dims_mod_cata_cde cde, dims_mod_cata_client_temp cli SET
				cde.mode_paiement_id = cli.mode_paiement_id,
				cde.gen_file = 1
			WHERE cli.id_client = OLD.id_client
			AND cde.id_client = cli.id_client
			AND ISNULL(cde.mode_paiement_id);
		END IF;');

	// Mise à jour (et réactivation) des existants
	$db->query('
		UPDATE `dims_mod_cata_client_temp` ct, `IND_DIMS_CLIENT` c, `IND_DIMS_ADD_FAC` f, `IND_DIMS_DICO_PAYS` p, `dims_country` cy, `dims_mod_cata_companies` cp SET
			ct.`erp_id` = c.`PAC_CUSTOM_PARTNER_ID`,
			ct.`id_company` = cp.`id`,
			ct.`nom` = c.`NOM`,
			ct.`erp_id_adr` = f.`PAC_ADDRESS_ID`,
			ct.`adr1` = f.`ADD_ADDRESS1`,
			ct.`cp` = f.`ADD_ZIPCODE`,
			ct.`ville` = f.`ADD_CITY`,
			ct.`id_pays` = cy.`id`,
			ct.`code_market` = c.`CODE_TARIF`,
			ct.`representative_id` = c.`CODE_REPRESENTANT`,
			ct.`mode_paiement_id` = c.`MODE_PAIEMENT`,
			ct.`payment_conditions_id` = c.`CONDITION_PAIEMENT`,
			ct.`shipping_conditions_id` = c.`CONDITION_EXPEDITION`,
			ct.`enr_certiphyto` = c.`ENR_CERTIPHYTO`,
			ct.`commentaire` = c.`COMMENTAIRE_INTERNE`,
			ct.`bloque` = IF (c.`CODE_STATUS` = 1, 0, 1)
		WHERE 	ct.`code_client` = c.`PER_KEY1`
		AND 	f.`PAC_PERSON_ID` = c.`PAC_CUSTOM_PARTNER_ID`
		AND 	p.`PC_CNTRY_ID` = f.`PC_CNTRY_ID`
		AND 	p.`INI` = cy.`ISO`
		AND 	cp.`code` = c.`CODE_SOCIETE`');
	// echo "4 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();

	// Suppression des existants de la table d'import
	$db->query('
		DELETE `c`.*
		FROM `dims_mod_cata_client_temp` ct, `IND_DIMS_CLIENT` c
		WHERE 	ct.`code_client` = c.`PER_KEY1`');
	// echo "5 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();

	// Insertion des nouveaux
	$rs = $db->query('SELECT
			c.`PER_KEY1`,
			c.`PAC_CUSTOM_PARTNER_ID`,
			cp.`id` AS `id_company`,
			c.`NOM`,
			f.`PAC_ADDRESS_ID`,
			f.`ADD_ADDRESS1`,
			f.`ADD_ZIPCODE`,
			f.`ADD_CITY`,
			cy.`id` AS `id_country`,
			c.`CODE_TARIF`,
			c.`CODE_REPRESENTANT`,
			c.`MODE_PAIEMENT`,
			c.`CONDITION_PAIEMENT`,
			c.`CONDITION_EXPEDITION`,
			c.`ENR_CERTIPHYTO`,
			'._ID_USER.',
			'._ID_MODULE.',
			'._ID_WORKSPACE.',
			'.client::TYPE_PROFESSIONAL.',
			c.`COMMENTAIRE_INTERNE`,
			IF (c.`CODE_STATUS` = 1, 0, 1) AS `bloque`
		FROM 	`IND_DIMS_CLIENT` AS c

		INNER JOIN 	`dims_mod_cata_companies` cp
		ON 			cp.`code` = c.`CODE_SOCIETE`

		INNER JOIN 	`IND_DIMS_ADD_FAC` AS f
		ON 			f.`PAC_PERSON_ID` = c.`PAC_CUSTOM_PARTNER_ID`

		INNER JOIN 	`IND_DIMS_DICO_PAYS` AS p
		ON 			p.`PC_CNTRY_ID` = f.`PC_CNTRY_ID`

		INNER JOIN 	`dims_country` cy
		ON 			cy.`ISO` = p.`INI`

		WHERE 	c.`PER_KEY1` != ""

		GROUP BY c.`PAC_CUSTOM_PARTNER_ID`');

	while ($row = $db->fetchrow($rs)) {
		$db->query('INSERT INTO `dims_mod_cata_client_temp` (
				`code_client`,
				`erp_id`,
				`id_company`,
				`nom`,
				`erp_id_adr`,
				`adr1`,
				`cp`,
				`ville`,
				`id_pays`,
				`code_market`,
				`representative_id`,
				`mode_paiement_id`,
				`payment_conditions_id`,
				`shipping_conditions_id`,
				`enr_certiphyto`,
				`escompte`,
				`minimum_cde`,
				`franco`,
				`id_user`,
				`id_module`,
				`id_workspace`,
				`type`,
				`commentaire`,
				`bloque`
			) VALUES (
				:code_client,
				:erp_id,
				:id_company,
				:nom,
				:erp_id_adr,
				:adr1,
				:cp,
				:ville,
				:id_pays,
				:code_market,
				:representative_id,
				:mode_paiement_id,
				:payment_conditions_id,
				:shipping_conditions_id,
				:enr_certiphyto,
				0,
				0,
				0,
				'._ID_USER.',
				'._ID_MODULE.',
				'._ID_WORKSPACE.',
				'.client::TYPE_PROFESSIONAL.',
				:commentaire,
				:bloque
			)', array(
				':code_client'				=> array('type' => PDO::PARAM_STR, 'value' => $row['PER_KEY1']),
				':erp_id' 					=> array('type' => PDO::PARAM_INT, 'value' => $row['PAC_CUSTOM_PARTNER_ID']),
				':id_company' 				=> array('type' => PDO::PARAM_INT, 'value' => $row['id_company']),
				':nom' 						=> array('type' => PDO::PARAM_STR, 'value' => $row['NOM']),
				':erp_id_adr' 				=> array('type' => PDO::PARAM_INT, 'value' => $row['PAC_ADDRESS_ID']),
				':adr1' 					=> array('type' => PDO::PARAM_STR, 'value' => $row['ADD_ADDRESS1']),
				':cp' 						=> array('type' => PDO::PARAM_STR, 'value' => $row['ADD_ZIPCODE']),
				':ville' 					=> array('type' => PDO::PARAM_STR, 'value' => $row['ADD_CITY']),
				':id_pays' 					=> array('type' => PDO::PARAM_INT, 'value' => $row['id_country']),
				':code_market' 				=> array('type' => PDO::PARAM_STR, 'value' => $row['CODE_TARIF']),
				':representative_id' 		=> array('type' => PDO::PARAM_INT, 'value' => $row['CODE_REPRESENTANT']),
				':mode_paiement_id' 		=> array('type' => PDO::PARAM_INT, 'value' => $row['MODE_PAIEMENT']),
				':payment_conditions_id' 	=> array('type' => PDO::PARAM_INT, 'value' => $row['CONDITION_PAIEMENT']),
				':shipping_conditions_id' 	=> array('type' => PDO::PARAM_INT, 'value' => $row['CONDITION_EXPEDITION']),
				':enr_certiphyto' 			=> array('type' => PDO::PARAM_STR, 'value' => $row['ENR_CERTIPHYTO']),
				':commentaire' 				=> array('type' => PDO::PARAM_STR, 'value' => (is_null($row['COMMENTAIRE_INTERNE']) ? '' : $row['COMMENTAIRE_INTERNE'])),
				':bloque' 					=> array('type' => PDO::PARAM_STR, 'value' => $row['bloque'])
			));
	}

	// echo "6 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();

	// -------------------------
	// Adresses de livraison
	// -------------------------

	// Création de la table temporaire
	$db->query('DROP TABLE IF EXISTS `dims_mod_cata_depot_temp`;');
	$db->query('CREATE TABLE `dims_mod_cata_depot_temp` (
			PRIMARY KEY (`id`),
			KEY `client` (`client`),
			KEY `depot` (`depot`),
			KEY `erp_id` (`erp_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		SELECT * FROM `dims_mod_cata_depot`');
	$db->query('ALTER TABLE `dims_mod_cata_depot_temp` CHANGE `id` `id` int(10) unsigned NOT NULL AUTO_INCREMENT');

	// Mise à jour (et réactivation) des existants
	$db->query('UPDATE `dims_mod_cata_depot_temp` AS d

		INNER JOIN 	`IND_DIMS_ADD_LIV` AS l
		ON 			l.`PAC_ADDRESS_ID` = d.`erp_id`

		INNER JOIN 	`dims_mod_cata_client_temp` c
		ON 			c.`erp_id` = l.`PAC_PERSON_ID`

		INNER JOIN 	`IND_DIMS_DICO_PAYS` p
		ON 			p.`PC_CNTRY_ID` = l.`PC_CNTRY_ID`

		INNER JOIN 	`dims_country` cy
		ON 			cy.`ISO` = p.`INI`

		SET 	d.`client` 		= c.`code_client`,
				d.`adr1` 		= l.`ADD_ADDRESS1`,
				d.`cp` 			= l.`ADD_ZIPCODE`,
				d.`ville` 		= l.`ADD_CITY`,
				d.`id_country` 	= cy.`id`');

	// Suppression des existants de la table d'import
	$db->query('
		DELETE `l`.*
		FROM `dims_mod_cata_depot_temp` d, `IND_DIMS_ADD_LIV` l
		WHERE 	d.`erp_id` = l.`PAC_ADDRESS_ID`');


	// Insertion des nouveaux
	$rs = $db->query('SELECT
			c.`code_client`,
			l.`PAC_ADDRESS_ID`,
			l.`ADD_ADDRESS1`,
			l.`ADD_ZIPCODE`,
			l.`ADD_CITY`,
			cy.`id` AS id_country,
			l.`CNTNAME`
		FROM `IND_DIMS_ADD_LIV` l

		INNER JOIN 	`dims_mod_cata_client_temp` c
		ON 			c.`erp_id` = l.`PAC_PERSON_ID`

		INNER JOIN 	`IND_DIMS_DICO_PAYS` p
		ON 			p.`PC_CNTRY_ID` = l.`PC_CNTRY_ID`

		INNER JOIN 	`dims_country` cy
		ON 			cy.`ISO` = p.`INI`');

	while ($row = $db->fetchrow($rs)) {
		foreach ($row as $k => $v) { $row[$k] = trim($v); }

		$db->query('INSERT INTO `dims_mod_cata_depot_temp` (
				`client`,
				`erp_id`,
				`adr1`,
				`cp`,
				`ville`,
				`id_country`
			) VALUES (
				:client,
				:erp_id,
				:adr1,
				:cp,
				:ville,
				:id_country
			)', array(
				':client' 		=> array('type' => PDO::PARAM_STR, 'value' => $row['code_client']),
				':erp_id' 		=> array('type' => PDO::PARAM_INT, 'value' => $row['PAC_ADDRESS_ID']),
				':adr1' 		=> array('type' => PDO::PARAM_STR, 'value' => $row['ADD_ADDRESS1']),
				':cp' 			=> array('type' => PDO::PARAM_STR, 'value' => $row['ADD_ZIPCODE']),
				':ville' 		=> array('type' => PDO::PARAM_STR, 'value' => $row['ADD_CITY']),
				':id_country' 	=> array('type' => PDO::PARAM_INT, 'value' => $row['id_country'])
			));
	}

	// On vérifie qu'on a bien des données dans la table
	$rs = $db->query('SELECT COUNT(*) AS nb FROM `dims_mod_cata_depot_temp`');
	$row = $db->fetchrow($rs);
	if ($row['nb'] > 0) {
		$db->query('DROP TABLE `dims_mod_cata_depot` ;');
		$db->query('ALTER TABLE `dims_mod_cata_depot_temp` RENAME `dims_mod_cata_depot` ;');

		// On peut avoir plusieurs adresses de livraison pour un même client
		// Il faut remettre à jour la numérotation du champ 'depot'
		cata_depot::updateAllDepotsNumbers();

		// Il faut aussi mettre à jour les id_city (et créer les villes le cas échéant)
		cata_depot::updateAllDepotsCitiesIds();
	}
	else {
		raiseError('Depots', 'La table depots est vide');
	}
	// echo "7 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();


	// On poursuit (globalobject, tiers, indexation...) que si on a des données dans la table
	// Sinon on remonte une erreur
	$rs = $db->query('SELECT COUNT(*) AS nb FROM `dims_mod_cata_client_temp`');
	$row = $db->fetchrow($rs);

	if ($row['nb'] > 0) {
		$db->query('DROP TRIGGER IF EXISTS `after_update_cata_client_temp`');
		$db->query('DROP TABLE `dims_mod_cata_client` ;');
		$db->query('ALTER TABLE `dims_mod_cata_client_temp` RENAME `dims_mod_cata_client` ;');

		require_once DIMS_APP_PATH."/modules/system/class_canton.php";
		require_once DIMS_APP_PATH."/modules/system/class_departement.php";
		require_once DIMS_APP_PATH."/modules/system/class_region.php";
		require_once DIMS_APP_PATH."/modules/system/class_address.php";

		// $tiers = tiers::all();
		$listaddresses = address::all();
		$listaddresses_link = address_link::all();
		$cities = city::all();
		$countries = country::all();

		$refcities = array();
		foreach ($cities as $city) {
			if (!isset($refcities[strtoupper($city->fields['label'])])) {
				$refcities[strtoupper($city->fields['label'])] = $city->fields['id'];
			}
		}
		// on libère
		unset($cities);

		// construction des adresses connues
		$addresses = array();
		foreach ($listaddresses as $adr) {
			$addresses[$adr->fields['id_globalobject']] = $adr->fields;
		}
		// on libère
		unset($listaddresses);

		$addresseslink = array();
		foreach ($listaddresses_link as $adr) {
			$addresseslink[$adr->fields['id_goobject']][$adr->fields['id_goaddress']] = $adr->fields;
		}
		// on libère
		unset($listaddresses_link);


		// $alltiers = tiers::run();
		$alltiers = array();
		$rs = $db->query('SELECT * FROM `dims_mod_business_tiers`');
		while ($row = $db->fetchrow($rs)) {
			$alltiers[$row['id']] = $row;
		}


		// Chargement des contacts à créer / mettre à jour
		$contacts = array();
		$rs = $db->query('SELECT ct.*
			FROM `IND_DIMS_CONTACT` ct
			INNER JOIN 	`dims_mod_cata_client` c
			ON 			c.`erp_id` = ct.`PP_CLI_ID`');
		while ($row = $db->fetchrow($rs)) {
			$contacts[$row['PP_CLI_ID']][$row['PP_CONTACT_ID']] = $row;
		}


		// On parcourt les clients
		$rs = $db->query('SELECT * FROM `dims_mod_cata_client` ;');
		while ($row = $db->fetchrow($rs)) {

			$changedclient=false;

			// Ouverture du client
			$client = new client();
			$client->openFromResultSet($row);
			// $client->controlChangesBeforeIndex(true); // Pas nécessaire car on vérifie que les données ont changé avant de faire un save

			// Si pas encore de tiers, on le crée
			$tiers = isset($alltiers[$client->get('tiers_id')]) ? $alltiers[$client->get('tiers_id')] : null;
			if (is_null($tiers)) {
				$client->updateTiers();
				$tiers = $client->getTiers();
				$alltiers[$tiers->get('id')] = $tiers;
			}
			$id_go_courant = $tiers->fields['id_globalobject'];


///////////////////////

			// Adresse de la CRM
			if ($client->fields['id_go_address'] == 0) {
				$id_go_address = 0;

				// Recherche de l'adresse dans la base
				if (isset($addresseslink[$id_go_courant])) {
					// on parcourt pour verifier si cette adresse existe ou non
					foreach ($addresseslink[$id_go_courant] as $adrlink) {

						if ($id_go_address == 0 && isset($addresses[$adrlink['id_goaddress']])) {
							// on a une adresse on compare avec les valeurs courantes
							$adrtemp = $addresses[$adrlink['id_goaddress']];

							if ($adrtemp["address"] == $client->fields['adr1']
								&& $adrtemp["postalcode"] == $client->fields['cp']
								&& $adrtemp["city"] == $client->fields['ville']
							) {
								// on a la bonne
								$id_go_address = $adrtemp['id_globalobject'];
								break;
							}
						}
					}
				}

				// Si on trouve pas l'adresse, on la crée
				if ($id_go_address == 0) {
					$adr = new address();
					$adr->init_description(true);
					$adr->setugm();
					$adr->fields['id_module'] 	= _ID_MODULE;
					$adr->fields['address'] 	= is_null($client->fields['adr1']) ? '' : $client->fields['adr1'];
					$adr->fields['postalcode'] 	= is_null($client->fields['cp']) ? '' : $client->fields['cp'];
					$adr->fields['city'] 		= is_null($client->fields['ville']) ? '' : $client->fields['ville'];
					$adr->fields['id_country'] 	= $client->fields['id_pays'];
					$adr->fields['country'] 	= $countries[$client->fields['id_pays']]->fields['printable_name'];

					// Recherche de la ville
					if (isset($refcities[strtoupper($client->fields['ville'])])) {
						$adr->fields['id_city'] = $refcities[strtoupper($client->fields['ville'])];
						$tiers->setIdCity($adr->fields['id_city'], true);
					}
					// Création de la ville
					else {
						$city = new city();
						$city->setugm();
						$city->fields['id_module'] = 1;
						$city->fields['id_country'] = $adr->fields['id_country'];
						$city->fields['label'] = strtoupper($client->fields['ville']);
						$city->fields['cp'] = $client->fields['cp'];
						$city->save();

						$refcities[strtoupper($city->fields['label'])] = $city->fields['id'];
						$adr->fields['id_city'] = $city->fields['id'];
						$tiers->setIdCity($adr->fields['id_city'], true);
					}

					$adr->save();
					$id_go_address = $adr->fields['id_globalobject'];
				}

				// Rattachement de l'adresse au tiers
				if ($id_go_courant > 0 && $id_go_address > 0 && !isset($addresseslink[$id_go_courant][$id_go_address])) {
					$adrlink = new address_link();
					$adrlink->init_description(true);
					$adrlink->fields['id_goaddress'] = $id_go_address;
					$adrlink->fields['id_goobject'] = $id_go_courant;
					$adrlink->fields['id_type'] = 2; // Adresse de facturation
					$adrlink->save();

					$addresseslink[$id_go_courant][$id_go_address] = $adrlink->fields;
				}

				$client->fields['id_go_address'] = $id_go_address;
				$client->save_lite();
			}

///////////////////////

			// Adresses de livraison dans la CRM
			$a_depots = $client->getDepots();
			foreach ($a_depots as $depot) {
				if ($depot->fields['id_go_address'] == 0) {
					$id_go_address = 0;

					// Recherche de l'adresse dans la base
					if (isset($addresseslink[$id_go_courant])) {
						// on parcourt pour verifier si cette adresse existe ou non
						foreach ($addresseslink[$id_go_courant] as $adrlink) {

							if ($id_go_address == 0 && isset($addresses[$adrlink['id_goaddress']])) {
								// on a une adresse on compare avec les valeurs courantes
								$adrtemp = $addresses[$adrlink['id_goaddress']];

								if ($adrtemp["address"] == $depot->fields['adr1']
									&& $adrtemp["postalcode"] == $depot->fields['cp']
									&& $adrtemp["city"] == $depot->fields['ville']
								) {
									// on a la bonne
									$id_go_address = $adrtemp['id_globalobject'];
									break;
								}
							}
						}
					}

					// Si on trouve pas l'adresse, on la crée
					if ($id_go_address == 0) {
						$adr = new address();
						$adr->init_description(true);
						$adr->setugm();
						$adr->fields['id_module'] 	= _ID_MODULE;
						$adr->fields['address'] 	= is_null($depot->fields['adr1']) ? '' : $depot->fields['adr1'];
						$adr->fields['postalcode'] 	= is_null($depot->fields['cp']) ? '' : $depot->fields['cp'];
						$adr->fields['city'] 		= is_null($depot->fields['ville']) ? '' : $depot->fields['ville'];
						$adr->fields['id_country'] 	= $depot->fields['id_country'];
						$adr->fields['country'] 	= $countries[$depot->fields['id_country']]->fields['printable_name'];

						// Recherche de la ville
						if (isset($refcities[strtoupper($depot->fields['ville'])])) {
							$adr->fields['id_city'] = $refcities[strtoupper($depot->fields['ville'])];
						}
						// Création de la ville
						else {
							$city = new city();
							$city->setugm();
							$city->fields['id_module'] = 1;
							$city->fields['id_country'] = $adr->fields['id_country'];
							$city->fields['label'] = strtoupper($depot->fields['ville']);
							$city->fields['cp'] = $depot->fields['cp'];
							$city->save();

							$refcities[strtoupper($city->fields['label'])] = $city->fields['id'];
							$adr->fields['id_city'] = $depot->fields['id'];
						}

						$adr->save();
						$id_go_address = $adr->fields['id_globalobject'];
					}

					// Rattachement de l'adresse au tiers
					if ($id_go_courant > 0 && $id_go_address > 0 && !isset($addresseslink[$id_go_courant][$id_go_address])) {
						$adrlink = new address_link();
						$adrlink->init_description(true);
						$adrlink->fields['id_goaddress'] = $id_go_address;
						$adrlink->fields['id_goobject'] = $id_go_courant;
						$adrlink->fields['id_type'] = 1; // Adresse de livraison
						$adrlink->save();

						$addresseslink[$id_go_courant][$id_go_address] = $adrlink->fields;
					}

					$depot->fields['id_go_address'] = $id_go_address;
					$depot->save();
				}
			}

///////////////////////

			// Contacts des clients existants
			$i = 0;
			foreach ($contacts[$client->get('erp_id')] as $rowc) {
				// Reformattage des numéros de téléphone comme ceux de la BDD
				$rowc['PAC_TEL_NUMBER'] = business_format_tel($rowc['PAC_TEL_NUMBER']);
				$rowc['PAC_FAX_NUMBER'] = business_format_tel($rowc['PAC_FAX_NUMBER']);

				$contact = new contact();

				if (isset($allcontacts[$rowc['PP_CONTACT_ID']])) {
					$contact->fields = $allcontacts[$rowc['PP_CONTACT_ID']];
					$contact->new = false;
				}
				else {
					$contact->init_description(true);
				}

				if (
					$contact->fields['lastname'] 	!= $rowc['PER_NAME']
					|| $contact->fields['phone'] 	!= $rowc['PAC_TEL_NUMBER']
					|| $contact->fields['fax'] 		!= $rowc['PAC_FAX_NUMBER']
					|| $contact->fields['email'] 	!= $rowc['PAC_MAIL_INFO']
					|| $contact->fields['erp_id'] 	!= $rowc['PP_CONTACT_ID']
				) {
					$contact->fields['lastname'] 	= $rowc['PER_NAME'];
					$contact->fields['phone'] 		= $rowc['PAC_TEL_NUMBER'];
					$contact->fields['fax'] 		= $rowc['PAC_FAX_NUMBER'];
					$contact->fields['email'] 		= $rowc['PAC_MAIL_INFO'];
					$contact->fields['erp_id'] 		= $rowc['PP_CONTACT_ID'];
					$contact->save();

					$allcontacts[$rowc['PP_CONTACT_ID']] = $contact->fields;

					$user = $contact->getUser();

					// Création du user
					if ($user->isNew()) {
						$login = $client->getUniqueLogin();

						if ($login != '') {
							$password = passgen();
							$dims->getPasswordHash($password, $hash, $salt);

							$user->fields['login'] 				= $login;
							$user->fields['lastname'] 			= $contact->fields['lastname'];
							$user->fields['initial_password'] 	= $password;
							$user->fields['password'] 			= $hash;
							$user->fields['salt'] 				= $salt;
							$user->fields['date_creation'] 		= _NOW;
							$user->fields['id_contact'] 		= $contact->get('id');
							$user->fields['status'] 			= 1;
							$user->save();

							$client_group = $client->getGroup();
							$user->attachtogroup($client_group->get('id'), $lstParam['default_lvl_registration']->getValue());

							// On enregistre le 1er utilisateur dans le client
							if ($i == 0) {
								$client->fields['dims_user'] 	= $user->get('id');
								$client->fields['login'] 		= $login;
								$client->fields['librcha1'] 	= $login;
								$client->fields['librcha2'] 	= $password;
								$client->save();
							}

							// Lien entre le tiers et le contact
							include_once DIMS_APP_PATH . 'modules/system/class_tiers_contact.php';
							$lk = tiersct::find_by(array(
								'id_tiers' =>       $client->get('tiers_id'),
								'id_contact' =>     $contact->get('id'),
								'id_workspace' =>   _ID_WORKSPACE,
							), null, 1);

							if(empty($lk)) {
								$lk = new tiersct();
								$lk->init_description(true);

								$lk->set('id_tiers',    $client->get('tiers_id'));
								$lk->set('id_contact',  $contact->get('id'));
								$lk->set('link_level',  2);
								$lk->set('date_deb',    dims_createtimestamp());
								$lk->set('type_lien',   $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']);
								$lk->set('date_fin',    0);
								$lk->set('function',    '');
								$lk->save();
							}
						}

						// echo "8 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();
					}
				}

				$i++;
			}

			// Adresses de livraison
			if ($changedclient) $client->prepareindex();
		}
	}
	else {
		raiseError('Clients', 'La table client est vide');
	}
	// echo "9 : ".round($dims_timer->getexectime(), 3)."\n";ob_flush();
}


if (_UPDATE_LIVRAISONS) {

	// --------------------------
	// Création des index sur les tables de synchro
	// --------------------------
	$db->query('ALTER TABLE `IND_DIMS_CRE_ADD_LIV`
		ADD INDEX `TRAITE` (`TRAITE`),
		ADD INDEX `RETOUR_PAC_ADDRESS_ID` (`RETOUR_PAC_ADDRESS_ID`);');

	// Recherche d'adresses récemment créées
	// pour lesquelles il faut mettre à jour l'ERP ID
	$rs = $db->query('SELECT l.*
		FROM 	`IND_DIMS_CRE_ADD_LIV` l
		INNER JOIN 	`dims_mod_cata_depot` d
		ON 			d.`id` = l.`ID_DIMS_LIV`
		AND 		ISNULL(d.`erp_id`)
		WHERE 	ISNULL(l.`PAC_ADDRESS_ID`)
		AND 	l.`TRAITE` = 1
		AND 	NOT ISNULL(l.`RETOUR_PAC_ADDRESS_ID`) ;');
	while ($row = $db->fetchrow($rs)) {
		$db->query('UPDATE `dims_mod_cata_depot` d, `IND_DIMS_CRE_ADD_LIV` l SET
				d.`erp_id` = l.`RETOUR_PAC_ADDRESS_ID`
			WHERE d.`id` = '.$row['ID_DIMS_LIV']);

		// On recherche les commandes à mettre à jour et générer
		$rs2 = $db->query('SELECT cde.`id_cde`
			FROM 	`dims_mod_cata_cde` cde
			INNER JOIN 	`dims_mod_cata_client` cli
			ON 			cli.`code_client` = cde.`code_client`
			AND 		cli.`erp_id` = '.$row['PAC_PERSON_ID'].'
			INNER JOIN 	`dims_mod_cata_depot` d
			ON 			d.`id` = '.$row['ID_DIMS_LIV'].'
			AND 		d.`depot` = cde.`num_depot`
			WHERE 	ISNULL(cde.`erp_id_adr_liv`)');
		while ($row2 = $db->fetchrow($rs2)) {
			// On met à jour l'id de l'adresse de livraison dans la commande
			$db->query('UPDATE `dims_mod_cata_cde` SET
					`erp_id_adr_liv` = '.$row['RETOUR_PAC_ADDRESS_ID'].'
				WHERE `id_cde` = '.$row2['id_cde']);

			// On génère la commande
			write_cmd_file($row2['id_cde']);
		}

		// // On met également à jour les commandes
		// $db->query('UPDATE `dims_mod_cata_cde` cde, `dims_mod_cata_client` cli, `dims_mod_cata_depot` d SET
		// 		cde.`erp_id_adr_liv` = d.`erp_id`
		// 	WHERE cde.`code_client` = cli.`code_client`
		// 	AND cli.`erp_id` = '.$row['PAC_PERSON_ID'].'
		// 	AND cde.`num_depot` = d.`depot`');

	}
}


if (_IMPORT_DOCUMENTS) {
	// --------------------------
	// Création des index sur les tables de synchro
	// --------------------------

	$db->query('
		ALTER TABLE `IND_DIMS_DOC_ENTETE`
			ADD INDEX (`PAC_PAYMENT_CONDITION_ID`),
			ADD INDEX (`PAC_SENDING_CONDITION_ID`),
			ADD INDEX (`DOC_GAUGE_ID`),
			ADD INDEX (`PAC_THIRD_ID`),
			ADD INDEX (`PC_CNTRY_ID`),
			ADD INDEX (`A_IDCRE`);');
	$db->query('
		ALTER TABLE `IND_DIMS_DOC_POSITION`
			ADD INDEX (`DOC_DOCUMENT_ID`),
			ADD INDEX (`POS_NUMBER`),
			ADD INDEX (`POS_REFERENCE`),
			ADD INDEX (`DIC_TYPE_VAT_GOOD_ID`),
			ADD INDEX (`DIC_UNIT_OF_MEASURE_ID`) ;');
	$db->query('ALTER TABLE `IND_DIMS_DOC_PIED` ADD INDEX (`DOC_DOCUMENT_ID`);');
	$db->query('ALTER TABLE `IND_DIMS_DICO_CDT_PAIE` ADD INDEX (`PAC_PAYMENT_CONDITION_ID`);');
	$db->query('ALTER TABLE `IND_DIMS_DICO_MODE_EXP` ADD INDEX (`PAC_SENDING_CONDITION_ID`);');
	$db->query('ALTER TABLE `IND_DIMS_GABARIT` ADD INDEX (`DOC_GAUGE_ID`);');
	$db->query('
		ALTER TABLE `IND_DIMS_RECAP_TVA_DOC`
			ADD INDEX (`DOC_FOOT_ID`),
			ADD INDEX (`DIC_TYPE_VAT_GOOD_ID`);');
	$db->query('ALTER TABLE `IND_DIMS_DICO_PAYS` ADD INDEX (`INI`);');


	// --------------------------
	// Mise à jour des existants
	// --------------------------

	// Documents
	$db->query('UPDATE 	`dims_mod_cata_facture` f

		INNER JOIN 	`IND_DIMS_DOC_ENTETE` e
		ON 			e.`DOC_DOCUMENT_ID` = f.`erp_id`

		INNER JOIN 	`IND_DIMS_DOC_PIED` p
		ON 			p.`DOC_DOCUMENT_ID` = e.`DOC_DOCUMENT_ID`

		INNER JOIN 	`dims_mod_cata_client` c
		ON 			c.`erp_id` = e.`PAC_THIRD_ID`

		INNER JOIN 	`IND_DIMS_DICO_PAYS` dp
		ON 			dp.`PC_CNTRY_ID` = e.`PC_CNTRY_ID`

		INNER JOIN 	`dims_country` cy
		ON 			cy.`ISO` = dp.`INI`

		INNER JOIN 	`dims_user` u
		ON 			u.`erp_id` = e.`A_IDCRE`

		INNER JOIN 	`IND_DIMS_DICO_CDT_PAIE` cp
		ON 			cp.`PAC_PAYMENT_CONDITION_ID` = e.`PAC_PAYMENT_CONDITION_ID`

		LEFT JOIN 	`IND_DIMS_DICO_MODE_EXP` me
		ON 			me.`PAC_SENDING_CONDITION_ID` = e.`PAC_SENDING_CONDITION_ID`

		LEFT JOIN 	`IND_DIMS_GABARIT` g
		ON 			g.`DOC_GAUGE_ID` = e.`DOC_GAUGE_ID`

		SET
			f.`erp_footer_id` 		= p.`DOC_FOOT_ID`,
			f.`id_user` 			= u.`id`,
			f.`timestp_modify` 		= CONCAT (
				SUBSTRING(e.`A_DATEMOD`, 1, 4),
				SUBSTRING(e.`A_DATEMOD`, 6, 2),
				SUBSTRING(e.`A_DATEMOD`, 9, 2),
				SUBSTRING(e.`A_DATEMOD`, 12, 2),
				SUBSTRING(e.`A_DATEMOD`, 15, 2),
				SUBSTRING(e.`A_DATEMOD`, 18, 2)
			),
			f.`id_client` 			= c.`id_client`,
			f.`code_client`			= c.`code_client`,
			f.`cli_nom` 			= c.`nom`,
			f.`cli_adr1` 			= e.`DMT_ADDRESS1`,
			f.`cli_cp` 				= e.`DMT_POSTCODE1`,
			f.`cli_ville` 			= e.`DMT_TOWN1`,
			f.`cli_id_pays` 		= cy.`id`,
			f.`cli_pays` 			= e.`CNTNAME_ADD1`,
			f.`cli_liv_nom` 		= c.`nom`,
			f.`cli_liv_adr1` 		= e.`DMT_ADDRESS2`,
			f.`cli_liv_cp` 			= e.`DMT_POSTCODE2`,
			f.`cli_liv_ville` 		= e.`DMT_TOWN2`,
			f.`cli_liv_id_pays` 	= cy.`id`,
			f.`cli_liv_pays` 		= e.`CNTNAME_ADD2`,
			f.`num_document` 		= e.`DMT_NUMBER`,
			f.`gauge_document` 		= g.`GAU_DESCRIBE`,
			f.`date_cree` 			= CONCAT (
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 1, 4),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 6, 2),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 9, 2),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 12, 2),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 15, 2),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 18, 2)
			),
			f.`total_ht` 			= p.`FOO_GOOD_TOT_AMOUNT_EX_B`,
			f.`total_tva` 			= p.`FOO_TOT_VAT_AMOUNT_B`,
			f.`total_ttc` 			= p.`FOO_DOCUMENT_TOT_AMOUNT_B`,
			f.`payment_conditions` 	= cp.`IMF_DESCRIPTION`,
			f.`shipping_conditions` = me.`SEN_DESCR` ;');

	// Lignes de documents
	$db->query('UPDATE 	`dims_mod_cata_facture_det` l

		INNER JOIN 	`IND_DIMS_DOC_POSITION` p
		ON 			p.`DOC_DOCUMENT_ID` = l.`erp_id_facture`
		AND 		p.`POS_NUMBER` = l.`position`

		INNER JOIN 	`dims_mod_cata_tva` t
		ON 			t.`id_tva` = p.`DIC_TYPE_VAT_GOOD_ID`

		INNER JOIN 	`IND_DIMS_DICO_UNIT` du
		ON 			du.`DIC_UNIT_OF_MEASURE_ID` = p.`DIC_UNIT_OF_MEASURE_ID`

		LEFT JOIN 	`dims_mod_cata_article` a
		ON 			a.`reference` = p.`POS_REFERENCE`

		SET
			l.`id_article` 			= a.`id`,
			l.`ref` 				= p.`POS_REFERENCE`,
			l.`label` 				= p.`POS_LONG_DESCRIPTION`,
			l.`description`			= p.`POS_FREE_DESCRIPTION`,
			l.`qte` 				= p.`POS_VALUE_QUANTITY`,
			l.`qte_liv` 			= p.`POS_BASIS_QUANTITY`,
			l.`qte_rel` 			= p.`POS_VALUE_QUANTITY` - p.`POS_BASIS_QUANTITY`,
			l.`unit_of_measure` 	= du.`DIC_UNIT_OF_MEASURE_WORDING`,
			l.`poids`				= p.`POS_NET_WEIGHT`,
			l.`ctva`				= p.`DIC_TYPE_VAT_GOOD_ID`,
			l.`tx_tva` 				= t.`tx_tva`,
			l.`pu_ht` 				= p.`POS_GROSS_UNIT_VALUE`,
			l.`pu_remise`			= p.`POS_NET_VALUE_EXCL_B` / p.`POS_VALUE_QUANTITY`,
			l.`pu_ttc`				= p.`POS_NET_VALUE_INCL_B` / p.`POS_VALUE_QUANTITY` ;');

	// Récap des TVA
	$db->query('UPDATE 	`dims_mod_cata_facture_tva` ft

		INNER JOIN 	`dims_mod_cata_facture` f
		ON 			f.`erp_id` = ft.`erp_id_facture`

		INNER JOIN 	`IND_DIMS_RECAP_TVA_DOC` r
		ON 			r.`DOC_FOOT_ID` = f.`erp_footer_id`
		AND 		r.`DIC_TYPE_VAT_GOOD_ID` = ft.`id_tva`

		INNER JOIN 	`dims_mod_cata_tva` t
		ON 			t.`id_tva` = ft.`id_tva`

		SET
			ft.`tx_tva` 		= t.`tx_tva`,
			ft.`total_ht` 		= r.`VDA_LIABLE_AMOUNT`,
			ft.`total_tva` 		= r.`VDA_VAT_AMOUNT`,
			ft.`total_ttc` 		= r.`VDA_LIABLE_AMOUNT` + r.`VDA_VAT_AMOUNT` ;');


	// --------------------------
	// Suppression des existants des tables d'import
	// --------------------------

	// Documents
	$db->query('DELETE e.*, p.*
		FROM 	`IND_DIMS_DOC_ENTETE` e, `IND_DIMS_DOC_PIED` p, `dims_mod_cata_facture` f
		WHERE 	f.`erp_id` = e.`DOC_DOCUMENT_ID`
		AND 	e.`DOC_DOCUMENT_ID` = p.`DOC_DOCUMENT_ID`;');

	// Lignes de documents
	$db->query('DELETE p.*
		FROM 	`IND_DIMS_DOC_POSITION` p, `dims_mod_cata_facture_det` l
		WHERE 	l.`erp_id_facture` 	= p.`DOC_DOCUMENT_ID`
		AND 	l.`position` 		= p.`POS_NUMBER`;');

	// Récap des TVA
	$db->query('DELETE r.*
		FROM 	`IND_DIMS_RECAP_TVA_DOC` r, `dims_mod_cata_facture` f, `dims_mod_cata_facture_tva` ft
		WHERE 	r.`DOC_FOOT_ID` = f.`erp_footer_id`
		AND 	f.`erp_id` = ft.`erp_id_facture`
		AND 	r.`DIC_TYPE_VAT_GOOD_ID` = ft.`id_tva` ;');


	// --------------------------
	// Insertion des nouveaux
	// --------------------------

	// Documents
	$db->query('INSERT INTO `dims_mod_cata_facture` (
			`erp_id`,
			`erp_footer_id`,
			`id_user`,
			`id_module`,
			`id_workspace`,
			`timestp_create`,
			`timestp_modify`,
			`id_client`,
			`code_client`,
			`cli_nom`,
			`cli_adr1`,
			`cli_cp`,
			`cli_ville`,
			`cli_id_pays`,
			`cli_pays`,
			`cli_liv_nom`,
			`cli_liv_adr1`,
			`cli_liv_cp`,
			`cli_liv_ville`,
			`cli_liv_id_pays`,
			`cli_liv_pays`,
			`num_document`,
			`type`,
			`gauge_document`,
			`date_cree`,
			`total_ht`,
			`total_tva`,
			`total_ttc`,
			`payment_conditions`,
			`shipping_conditions`
		) SELECT
			e.`DOC_DOCUMENT_ID`,
			p.`DOC_FOOT_ID`,
			u.`id`,
			'._ID_MODULE.',
			'._ID_WORKSPACE.',
			CONCAT (
				SUBSTRING(e.`A_DATECRE`, 1, 4),
				SUBSTRING(e.`A_DATECRE`, 6, 2),
				SUBSTRING(e.`A_DATECRE`, 9, 2),
				SUBSTRING(e.`A_DATECRE`, 12, 2),
				SUBSTRING(e.`A_DATECRE`, 15, 2),
				SUBSTRING(e.`A_DATECRE`, 18, 2)
			),
			CONCAT (
				SUBSTRING(e.`A_DATEMOD`, 1, 4),
				SUBSTRING(e.`A_DATEMOD`, 6, 2),
				SUBSTRING(e.`A_DATEMOD`, 9, 2),
				SUBSTRING(e.`A_DATEMOD`, 12, 2),
				SUBSTRING(e.`A_DATEMOD`, 15, 2),
				SUBSTRING(e.`A_DATEMOD`, 18, 2)
			),
			c.`id_client`,
			c.`code_client`,
			c.`nom`,
			e.`DMT_ADDRESS1`,
			e.`DMT_POSTCODE1`,
			e.`DMT_TOWN1`,
			cy.`id`,
			e.`CNTNAME_ADD1`,
			c.`nom`,
			e.`DMT_ADDRESS2`,
			e.`DMT_POSTCODE2`,
			e.`DMT_TOWN2`,
			e.`CNTNAME_ADD2`,
			e.`CNTNAME`,
			e.`DMT_NUMBER`,
			IF ( LEFT(e.`DMT_NUMBER`, 2) = "BL", 4, 1 ),
			g.`GAU_DESCRIBE`,
			CONCAT (
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 1, 4),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 6, 2),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 9, 2),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 12, 2),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 15, 2),
				SUBSTRING(e.`DMT_DATE_DOCUMENT`, 18, 2)
			),
			p.`FOO_GOOD_TOT_AMOUNT_EX_B`,
			p.`FOO_TOT_VAT_AMOUNT_B`,
			p.`FOO_DOCUMENT_TOT_AMOUNT_B`,
			cp.`IMF_DESCRIPTION`,
			me.`SEN_DESCR`

		FROM 	`IND_DIMS_DOC_ENTETE` e

		INNER JOIN 	`IND_DIMS_DOC_PIED` p
		ON 			p.`DOC_DOCUMENT_ID` = e.`DOC_DOCUMENT_ID`

		INNER JOIN 	`dims_mod_cata_client` c
		ON 			c.`erp_id` = e.`PAC_THIRD_ID`

		INNER JOIN 	`IND_DIMS_DICO_PAYS` dp
		ON 			dp.`PC_CNTRY_ID` = e.`PC_CNTRY_ID`

		INNER JOIN 	`dims_country` cy
		ON 			cy.`ISO` = dp.`INI`

		INNER JOIN 	`dims_user` u
		ON 			u.`erp_id` = e.`A_IDCRE`

		INNER JOIN 	`IND_DIMS_DICO_CDT_PAIE` cp
		ON 			cp.`PAC_PAYMENT_CONDITION_ID` = e.`PAC_PAYMENT_CONDITION_ID`

		LEFT JOIN 	`IND_DIMS_DICO_MODE_EXP` me
		ON 			me.`PAC_SENDING_CONDITION_ID` = e.`PAC_SENDING_CONDITION_ID`

		LEFT JOIN 	`IND_DIMS_GABARIT` g
		ON 			g.`DOC_GAUGE_ID` = e.`DOC_GAUGE_ID` ;');

	// Lignes de documents
	$db->query('INSERT INTO `dims_mod_cata_facture_det` (
			`id_facture`,
			`erp_id_facture`,
			`id_article`,
			`ref`,
			`position`,
			`label`,
			`description`,
			`qte`,
			`qte_liv`,
			`qte_rel`,
			`unit_of_measure`,
			`poids`,
			`ctva`,
			`tx_tva`,
			`pu_ht`,
			`pu_remise`,
			`pu_ttc`,
			`id_user`,
			`id_module`,
			`id_workspace`,
			`timestp_create`,
			`timestp_modify`
		) SELECT
			f.`id`,
			p.`DOC_DOCUMENT_ID`,
			a.`id`,
			p.`POS_REFERENCE`,
			p.`POS_NUMBER`,
			p.`POS_LONG_DESCRIPTION`,
			p.`POS_FREE_DESCRIPTION`,
			p.`POS_VALUE_QUANTITY`,
			p.`POS_BASIS_QUANTITY`,
			p.`POS_VALUE_QUANTITY` - p.`POS_BASIS_QUANTITY`,
			du.`DIC_UNIT_OF_MEASURE_WORDING`,
			p.`POS_NET_WEIGHT`,
			p.`DIC_TYPE_VAT_GOOD_ID`,
			t.`tx_tva`,
			p.`POS_GROSS_VALUE_B`,
			p.`POS_NET_VALUE_EXCL_B`,
			p.`POS_NET_VALUE_INCL_B`,
			u.`id`,
			'._ID_MODULE.',
			'._ID_WORKSPACE.',
			CONCAT (
				SUBSTRING(p.`A_DATECRE`, 1, 4),
				SUBSTRING(p.`A_DATECRE`, 6, 2),
				SUBSTRING(p.`A_DATECRE`, 9, 2),
				SUBSTRING(p.`A_DATECRE`, 12, 2),
				SUBSTRING(p.`A_DATECRE`, 15, 2),
				SUBSTRING(p.`A_DATECRE`, 18, 2)
			),
			CONCAT (
				SUBSTRING(p.`A_DATEMOD`, 1, 4),
				SUBSTRING(p.`A_DATEMOD`, 6, 2),
				SUBSTRING(p.`A_DATEMOD`, 9, 2),
				SUBSTRING(p.`A_DATEMOD`, 12, 2),
				SUBSTRING(p.`A_DATEMOD`, 15, 2),
				SUBSTRING(p.`A_DATEMOD`, 18, 2)
			)
		FROM 	`IND_DIMS_DOC_POSITION` p

		INNER JOIN 	`dims_mod_cata_facture` f
		ON 			f.`erp_id` = p.`DOC_DOCUMENT_ID`

		INNER JOIN 	`dims_user` u
		ON 			u.`erp_id` = p.`A_IDCRE`

		INNER JOIN 	`dims_mod_cata_tva` t
		ON 			t.`id_tva` = p.`DIC_TYPE_VAT_GOOD_ID`

		INNER JOIN 	`IND_DIMS_DICO_UNIT` du
		ON 			du.`DIC_UNIT_OF_MEASURE_ID` = p.`DIC_UNIT_OF_MEASURE_ID`

		LEFT JOIN 	`dims_mod_cata_article` a
		ON 			a.`reference` = p.`POS_REFERENCE` ;');

	// Récap des TVA
	$db->query('INSERT INTO `dims_mod_cata_facture_tva` (
			`id_facture`,
			`erp_id_facture`,
			`id_tva`,
			`tx_tva`,
			`total_ht`,
			`total_tva`,
			`total_ttc`,
			`id_user`,
			`id_module`,
			`id_workspace`,
			`timestp_create`,
			`timestp_modify`
		) SELECT
			f.`id`,
			f.`erp_id`,
			r.`DIC_TYPE_VAT_GOOD_ID`,
			t.`tx_tva`,
			r.`VDA_LIABLE_AMOUNT`,
			r.`VDA_VAT_AMOUNT`,
			r.`VDA_LIABLE_AMOUNT` + r.`VDA_VAT_AMOUNT`,
			'._ID_USER.',
			'._ID_MODULE.',
			'._ID_WORKSPACE.',
			'._NOW.',
			'._NOW.'
		FROM `IND_DIMS_RECAP_TVA_DOC` r

		INNER JOIN 	`dims_mod_cata_facture` f
		ON 			f.`erp_footer_id` = r.`DOC_FOOT_ID`

		INNER JOIN 	`dims_mod_cata_tva` t
		ON 			t.`id_tva` = r.`DIC_TYPE_VAT_GOOD_ID` ;');
}

// --------------------------
// Génération des commandes des prospects passés en client
// --------------------------
if (_GENERATE_ORDERS) {
	$rs = $db->query('SELECT `id_cde` FROM `dims_mod_cata_cde` WHERE `gen_file` = 1');
	while ($row = $db->fetchrow($rs)) {
		write_cmd_file($row['id_cde']);
		$db->query('UPDATE `dims_mod_cata_cde` SET `gen_file` = 0 WHERE `id_cde` = '.$row['id_cde']);
	}
}

if (_DROP_TABLES) {
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_ADD_FAC` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_ADD_LIV` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_CATEGORIES` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_CLIENT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_CONTACT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_CRE_ADD_LIV` ;');
	// $db->query('DROP TABLE IF EXISTS `IND_DIMS_CRM_EVENT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DESCRIPTIONS_PRODUITS` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_CDT_PAIE` ;');
	// $db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_CRM_EVE` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_DEPOT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_FAM_CLIENT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_FAM_PROD` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_INCOTERMS` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_MODE_EXP` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_MODE_PAIE` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_REMISE_DET` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_REMISE_PIED` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_STATUT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_TAXE_DET` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_TAXE_PIED` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_TVA` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DICO_UNIT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_ENTETE` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_PIED` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_POSITION` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_DOCUMENT_MODIF` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_ENTETE` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_MAJ_ENTETE` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_MAJ_PIED` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_MAJ_POSITION` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_PIED` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_PIED_TAXE` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_DOC_POSITION` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_GABARIT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_GCO_GOOD_MODIF` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_INTERFACE_TIME` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_PAC_CUSTOM_MODIF` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_PHYTO` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_PRODUIT` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_PRODUITS_TARIFS` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_RECAP_TVA_DOC` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_STATUS` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_STOCK` ;');
	$db->query('DROP TABLE IF EXISTS `IND_DIMS_UTILISATEURS` ;');
	$db->query('DROP TABLE IF EXISTS `logs` ;');
	$db->query('DROP TABLE IF EXISTS `PARAM` ;');
}

if (file_exists(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running")) {
	unlink(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running");
}

echo "\n";
