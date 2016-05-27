<?php

// Remontée de la commande dans les tables du catalogue
// depuis les SQL à destination de proconcept

define('AUTHORIZED_ENTRY_POINT', true);

chdir(dirname($argv[0]));
chdir('../../../www/');


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


$_SESSION['dims']['userid'] 		= 65;
$_SESSION['dims']['moduleid'] 		= 355;
$_SESSION['dims']['workspaceid'] 	= 64;


if (!empty($argv[1])) {
	$id_cde = $argv[1];
	$filename = DIMS_APP_PATH.'data/synchro/output/backup/'.$id_cde.'.sql';

	if (file_exists($filename)) {

		// Charegement de la commande dans la structure à destination de proconcept
		$db->query("DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_ENTETE`;");
		$db->query("
			CREATE TABLE `IND_DIMS_DOC_CREA_ENTETE` (
				`PAC_PERSON_ID` decimal(12,0) DEFAULT NULL,
				`DATE_DOCUMENT` datetime DEFAULT NULL,
				`DIMS_ORDER_ID` varchar(32) NOT NULL,
				`FAC_PAC_ADDRESS_ID` decimal(12,0) DEFAULT NULL,
				`LIV_PAC_ADDRESS_ID` decimal(12,0) DEFAULT NULL,
				`PAC_PAYMENT_CONDITION_ID` decimal(12,0) DEFAULT NULL,
				`ACS_PAYMENT_METHOD_ID` decimal(12,0) DEFAULT NULL,
				`PAC_SENDING_CONDITION_ID` decimal(12,0) DEFAULT NULL,
				`COMMENTAIRE_CLIENT` varchar(255) DEFAULT NULL,
				`DOC_GAUGE_ID` decimal(12,0) DEFAULT NULL,
				`PRIX_TOTAL_CTRL_INTERFACE` decimal(15,4) DEFAULT NULL,
				`C_INCOTERMS` varchar(10) DEFAULT NULL,
				`date_enlevement` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
				`heure_enlevement` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
				`date_livraison` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
				`semi_remorque_possible` tinyint(1) unsigned DEFAULT NULL,
				`hauteur_maxi` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
				`hayon_necessaire` tinyint(1) unsigned DEFAULT NULL,
				`tire_pal_necessaire` tinyint(1) unsigned DEFAULT NULL,
				`camion_autre` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
				`impossibilites_lirvaison` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
				`contact_nom` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
				`contact_prenom` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
				`contact_tel` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
				`contact_autre` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
				PRIMARY KEY (`DIMS_ORDER_ID`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$db->query("DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_PIED`;");
		$db->query("
			CREATE TABLE `IND_DIMS_DOC_CREA_PIED` (
				`DIMS_ORDER_ID` varchar(255) DEFAULT NULL,
				`PTC_CHARGE_ID` decimal(12,0) DEFAULT NULL,
				`MONTANT_HT` decimal(15,4) DEFAULT NULL,
				`DIMS_CHARGE_ID` varchar(255) DEFAULT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$db->query("DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_POSITION`;");
		$db->query("
			CREATE TABLE `IND_DIMS_DOC_CREA_POSITION` (
				`DIMS_POSITION_ID` varchar(32) NOT NULL DEFAULT '',
				`DIMS_ORDER_ID` varchar(32) DEFAULT NULL,
				`GCO_GOOD_ID` decimal(12,0) DEFAULT NULL,
				`QUANTITY` decimal(15,4) DEFAULT NULL,
				`PRIX_NET_HT` decimal(15,4) DEFAULT NULL,
				PRIMARY KEY (`DIMS_POSITION_ID`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$handle = fopen($filename, 'r');
		if ($handle) {
			while (($request = fgets($handle, 4096)) !== false) {
				$db->query($request);
			}
		}

		// On renvoie les infos qu'on a dans les commandes de DIMS
		$db->query('DELETE FROM `dims_mod_cata_cde` WHERE `id_cde` = '.$id_cde);
		$db->query('DELETE FROM `dims_mod_cata_cde_lignes` WHERE `id_cde` = '.$id_cde);

		require_once DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
		$cde = new commande();

		// id_cde
		// id_module
		// id_workspace
		// id_user
		// id_client
		// code_client
		// numcde
		// date_cree
		// date_validation
		// representative_creator
		// representative_validator
		// etat
		// expediee
		// traitement
		// libelle
		// commentaire
		// hors_cata
		// id_service
		// id_regroupement
		// id_budget
		// adrfact
		// erp_id_adr_fac
		// erp_id_adr_liv
		// num_depot
		// date_enlevement
		// heure_enlevement
		// semi_remorque_possible
		// hauteur_maxi
		// hayon_necessaire
		// tire_pal_necessaire
		// camion_autre
		// impossibilites_lirvaison
		// contact_nom
		// contact_prenom
		// contact_tel
		// contact_autre
		// date_livraison
		// poids_total
		// cli_nom
		// cli_adr1
		// cli_adr2
		// cli_adr3
		// cli_cp
		// cli_ville
		// cli_id_pays
		// cli_pays
		// cli_tel1
		// cli_tel2
		// cli_fax
		// cli_port
		// cli_email
		// cli_liv_nom
		// cli_liv_adr1
		// cli_liv_adr2
		// cli_liv_adr3
		// cli_liv_cp
		// cli_liv_ville
		// cli_liv_id_pays
		// cli_liv_pays
		// port
		// port_tx_tva
		// total_ht
		// total_tva
		// total_ttc
		// mode_paiement
		// mode_paiement_id
		// payment_conditions_id
		// shipping_conditions_id
		// require_costing
		// date_gen
		// mail
		// user_name
		// classe
		// validation_user_id
		// validation_user_name
		// refus_user_id
		// refus_user_name
		// refus_motif
		// mode_expedition
		// num_colis
		// sans_tva
		// teachername
		// classroom
		// cb_code
		// cb_error
		// cb_transaction_id
		// cb_payment_means
		// cb_payment_date
		// cb_payment_time
		// cb_response_code
		// cb_payment_certificate
		// cb_authorisation_id
		// cb_bank_response_code
		// cb_complementary_code
		// cb_complementary_info
		// cb_customer_ip_address
		// retour_cb
		// id_globalobject


		$db->query("DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_ENTETE`;");
		$db->query("DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_PIED`;");
		$db->query("DROP TABLE IF EXISTS `IND_DIMS_DOC_CREA_POSITION`;");
	}
	else {
		echo "Fichier inexistant : $filename\n";
	}

}
