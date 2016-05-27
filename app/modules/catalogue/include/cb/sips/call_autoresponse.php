<?php

chdir(DIMS_APP_PATH);

include_once './modules/system/class_dims.php';
include_once './modules/system/class_workspace.php';
include_once './modules/system/class_mb_object.php';
include_once './modules/system/class_mb_object_relation.php';
include_once './modules/system/class_mb_class.php';

//Charge la class des gestions d'exceptions
require DIMS_APP_PATH.'include/class_exception.php';

try {
	// INITIALIZE DIMS OBJECT
	$dims = new dims();

	if (!file_exists('./include/config.php')) {
		require './install.php';
		die();
	}
	require './include/config.php'; // load config (mysql, path, etc.)
	require './include/errors.php';

	// load DIMS global classes
	require_once './include/class_dims_data_object.php';

	// initialize DIMS
	require './include/global.php'; 		// load dims global functions & constants
	require './modules/system/class_module.php';
	/**
	* Database connection
	*
	* Don't forget to param db connection in ./include/config.php
	*/
	if (file_exists('./db/class_db_'._DIMS_SQL_LAYER.'.php')) require './db/class_db_'._DIMS_SQL_LAYER.'.php';
	global $db;

	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	if(!$db->connection_id) trigger_error(_DIMS_MSG_DBERROR, E_USER_ERROR);

	$dims->setDb($db);
	dims::setInstance($dims);


	//initialisation de la matrice de synchronisation
	if( !isset( $_SESSION['dims']['permanent_data']['table_descriptions'] ) ){
		$_SESSION['dims']['permanent_data']['table_descriptions'] = array();
		$dims->initTableDescriptions($_SESSION['dims']['permanent_data']['table_descriptions']);
	}
	else $dims->setTableDescriptions($_SESSION['dims']['permanent_data']['table_descriptions']);

	//--- MB_OBJECT
	if( !isset( $_SESSION['dims']['permanent_data']['mb_objects'] ) ){
		$_SESSION['dims']['permanent_data']['mb_objects'] = array();
		$dims->initPermanentMBObjects($_SESSION['dims']['permanent_data']['mb_objects']);
	}
	else $dims->setMBObjects($_SESSION['dims']['permanent_data']['mb_objects']);

	//-- mise en initialisation du module type
	if( !empty($_SESSION['dims']['moduletypeid']))
		$dims->setCurrentModuleTypeID($_SESSION['dims']['moduletypeid']);

	//--- MB_CLASSES
	if( !isset( $_SESSION['dims']['permanent_data']['mb_tables'] ) ){
		$_SESSION['dims']['permanent_data']['mb_tables'] = array();
		$dims->initPermanentMBTables($_SESSION['dims']['permanent_data']['mb_tables']);
	}
	else $dims->setMBTables($_SESSION['dims']['permanent_data']['mb_tables']);

	//--- MB_CLASSES
	if( !isset( $_SESSION['dims']['permanent_data']['mb_classes'] ) ){
		$_SESSION['dims']['permanent_data']['mb_classes'] = array();
		$dims->initPermanentMBClasses($_SESSION['dims']['permanent_data']['mb_classes']);
	}
	else $dims->setMBClasses($_SESSION['dims']['permanent_data']['mb_classes']);

	//--- MB_RELATIONS
	if( !isset( $_SESSION['dims']['permanent_data']['mb_object_relations'] ) ){
		$_SESSION['dims']['permanent_data']['mb_object_relations'] = array();
		if( _DIMS_DEBUGMODE ) mb_object_relation::init_all_relations();  //c'est pour que si le développeur s'est viandé dans une relation, en la modifiant ça la restaure proprement
		$dims->initPermanentMBObjectRelations($_SESSION['dims']['permanent_data']['mb_object_relations']);
	}
	else $dims->setMBObjectRelations($_SESSION['dims']['permanent_data']['mb_object_relations']);


	$dims->prepareMetaData($_SESSION['dims']['index']);


	ob_start();

	date_default_timezone_set('Europe/Paris');


	// ecriture dans le log
	$handle = fopen(realpath('.').'/logs/sips.log', 'a');
	flock($handle, LOCK_EX);

	echo "------------------------------------------\n";
	echo 'Le '.date('d/m/Y').' a '.date('H:i').' : '."\n";
	echo "------------------------------------------\n";


$debug = false;

if (!$debug) {
	print_r($_POST);

	// Récupération de la variable cryptée DATA
	$message = "message=$_POST[DATA]";

	// Initialisation du chemin du fichier pathfile (à modifier)
	//   ex :
	//    -> Windows : $pathfile="pathfile=c:\\repertoire\\pathfile"
	//    -> Unix    : $pathfile="pathfile=/home/repertoire/pathfile"

	$pathfile = "pathfile=".realpath('.').'/modules/catalogue/include/cb/sips/param/pathfile';

	//Initialisation du chemin de l'executable response (à modifier)
	//ex :
	//-> Windows : $path_bin = "c:\\repertoire\\bin\\response"
	//-> Unix    : $path_bin = "/home/repertoire/bin/response"
	//

	$path_bin = realpath('.').'/modules/catalogue/include/cb/sips/bin/response';

	// Appel du binaire response

	$result = exec("$path_bin $pathfile $message");

	//	Sortie de la fonction : !code!error!v1!v2!v3!...!v29
	//		- code=0	: la fonction retourne les données de la transaction dans les variables v1, v2, ...
	//				: Ces variables sont décrites dans le GUIDE DU PROGRAMMEUR
	//		- code=-1 	: La fonction retourne un message d'erreur dans la variable error


	//	on separe les differents champs et on les met dans une variable tableau

	$tableau = explode ("!", $result);
	print_r($tableau);
}
else {
	$tableau = array(
		0 => '',
		1 => '0',
		2 => '',
		3 => '011223344551111',
		4 => 'fr',
		5 => '1774',
		6 => '165807',
		7 => 'VISA',
		8 => '20130628145807',
		9 => '165817',
		10 => '20130628',
		11 => '00',
		12 => '1372431497',
		13 => '431497',
		14 => '978',
		15 => '4974.00',
		16 => '1',
		17 => '4D',
		18 => '00',
		19 => '',
		20 => '',
		21 => '',
		22 => '',
		23 => '',
		24 => 'fr',
		25 => 'fr',
		26 => '',
		27 => '21',
		28 => '',
		29 => '',
		30 => '0',
		31 => 'AUTHOR_CAPTURE',
		32 => '',
		33 => '02',
		34 => 'SSL',
		35 => '',
		36 => '201309',
		37 => '',
		38 => '',
		39 => '',
		40 => '',
		41 => '',
		42 => '',
		43 => '9',
		44 => '0');
}



	$code = $tableau[1];
	$error = $tableau[2];
	$merchant_id = $tableau[3];
	$merchant_country = $tableau[4];
	$amount = $tableau[5];
	$transaction_id = $tableau[6];
	$payment_means = $tableau[7];
	$transmission_date= $tableau[8];
	$payment_time = $tableau[9];
	$payment_date = $tableau[10];
	$response_code = $tableau[11];
	$payment_certificate = $tableau[12];
	$authorisation_id = $tableau[13];
	$currency_code = $tableau[14];
	$card_number = $tableau[15];
	$cvv_flag = $tableau[16];
	$cvv_response_code = $tableau[17];
	$bank_response_code = $tableau[18];
	$complementary_code = $tableau[19];
	$complementary_info= $tableau[20];
	$return_context = $tableau[21];
	$caddie = $tableau[22];
	$receipt_complement = $tableau[23];
	$merchant_language = $tableau[24];
	$language = $tableau[25];
	$customer_id = $tableau[26];
	$order_id = $tableau[27];
	$customer_email = $tableau[28];
	$customer_ip_address = $tableau[29];
	$capture_day = $tableau[30];
	$capture_mode = $tableau[31];
	$data = $tableau[32];
	$order_validity = $tableau[33];
	$transaction_condition = $tableau[34];
	$statement_reference = $tableau[35];
	$card_validity = $tableau[36];
	$score_value = $tableau[37];
	$score_color = $tableau[38];
	$score_info = $tableau[39];
	$score_threshold = $tableau[40];
	$score_profile = $tableau[41];
	$threed_ls_code = $tableau[43];
	$threed_relegation_code = $tableau[44];

	echo "Données reçues :\n";
	print_r($tableau);

	$id_cde = $order_id;

	$error = false;

	if ($id_cde == '') {
		echo "Id de commande vide\n";
		$error = true;
	}
	else {
		require DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';

		$obj_cde = new commande();
		if (!$obj_cde->getById($id_cde)) {
			echo "Impossible d'ouvrir la commande $id_cde\n";
			$error = true;
		}
		else {
			echo "Contenu de la commande :\n";
			print_r($obj_cde->fields);
			if (intval(strval($obj_cde->fields['total_ttc'] * 100)) != $amount) {
				echo "Montant incorrect :\n";
				$error = true;
			}
			else {
				$client = new client();
				if (!$client->getById($obj_cde->fields['id_client'])) {
					echo "Impossible d'ouvrir le client {$obj_cde->fields['id_client']}\n";
					$error = true;
				}
				else {
					echo "Contenu de la client :\n";
					print_r($client->fields);
					if ($response_code != '00' || $bank_response_code != '00') {
						echo "Autorisation refusée\n";
						$error = true;
					}
				}
			}
		}
	}

	if (!$error) {
		echo "Autorisation acceptée => validation de la commande\n";

		require DIMS_APP_PATH.'modules/catalogue/include/global.php';
		require DIMS_APP_PATH.'modules/catalogue/include/functions.php';
		if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/functions.php')) {
			require DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/functions.php';
		}
		require DIMS_APP_PATH.'modules/catalogue/include/class_catalogue.php';
		require DIMS_APP_PATH.'modules/wce/include/global.php';

		require DIMS_APP_PATH.'include/start.php';
		// vidage du buffer créé dans le start
		ob_end_clean();

		$obj_cde->fields['retour_cb'] = print_r($tableau, true);

		$obj_cde->fields['cb_transaction_id']		= $transaction_id;
		$obj_cde->fields['cb_payment_means']		= 'CB';
		$obj_cde->fields['cb_payment_date']			= date('Ymd', $payment_date);
		$obj_cde->fields['cb_payment_time']			= date('His', $payment_time);

		$obj_cde->fields['etat'] = commande::_STATUS_VALIDATED;
		$obj_cde->save();

		// Ecriture du fichier
		echo "Ecriture du fichier\n";
		write_cmd_file($obj_cde->get('id'));

		// chargement des paramètres du catalogue
		$dims->setHttpHost($_SERVER['HTTP_HOST']);
		$dims->setModeOffice('web');
		$dims->initWorkspaces();

		$webWorkspaces = array_keys($dims->getWebWorkspaces());
		$workspaceId = $webWorkspaces[0];

		// recherche du template pour envoi de mail
		$lstwcemods = $dims->getWceModules();
		$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

		$headings = wce_getheadings($wce_module_id);
		$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

		$template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
		// FIN - recherche du template


		//chargement des modules concernes par les workspaces disponibles
		$dims->initUserModules();
		$mods = $dims->getModuleByType('catalogue', $workspaceId);

		$oCatalogue = new catalogue();
		$oCatalogue->open($mods[0]['instanceid']);
		$oCatalogue->loadParams();

		// envoi des mails
		echo "Envoi des mails\n";
		require DIMS_APP_PATH.'modules/catalogue/mail_cde.php';
	}

	// fin log
	fwrite($handle, "#########################################\n".ob_get_contents()."\n");
	flock($handle, LOCK_UN);
	fclose($handle);
	ob_end_clean();
}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
