<?php

// Récupération de la variable cryptée DATA
$message="message=$_POST[DATA]";

// Initialisation du chemin du fichier pathfile (à modifier)
//   ex :
//    -> Windows : $pathfile="pathfile=c:/repertoire/pathfile";
//    -> Unix    : $pathfile="pathfile=/home/repertoire/pathfile";

$pathfile="pathfile=".realpath('.').'/modules/catalogue/include/cb/sips/param/pathfile';

// Initialisation du chemin de l'executable response (à modifier)
// ex :
// -> Windows : $path_bin = "c:/repertoire/bin/response";
// -> Unix    : $path_bin = "/home/repertoire/bin/response";
//

$path_bin = realpath('.').'/modules/catalogue/include/cb/sips/bin/response';

// Appel du binaire response
$message = escapeshellcmd($message);
$result = exec("$path_bin $pathfile $message");


//	Sortie de la fonction : !code!error!v1!v2!v3!...!v29
//		- code=0	: la fonction retourne les données de la transaction dans les variables v1, v2, ...
//				: Ces variables sont décrites dans le GUIDE DU PROGRAMMEUR
//		- code=-1 	: La fonction retourne un message d'erreur dans la variable error


//	on separe les differents champs et on les met dans une variable tableau

$tableau = explode ("!", $result);

//	Récupération des données de la réponse

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
$complementary_info = $tableau[20];
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


$errorCode = false;

if (intval(strval($cde->fields['total_ttc'] * 100)) != $amount) {
	$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_3');
	$errorCode = true;
}
else {
	$client = new client();
	if (!$client->getById($cde->fields['id_client'])) {
		$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_4');
		$errorCode = true;
	}
	else {
		if ($response_code != '00' || $bank_response_code != '00') {
			$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_5');
			$errorCode = true;
		}
	}
}

if (!$errorCode) {
	//  analyse du code retour
	if (( $code == "" ) && ( $error == "" )) {
		$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_6');
	}

	//	Erreur, affiche le message d'erreur
	else if ( $code != 0 ) {
		$_SESSION['catalogue']['msg_confirm'] = $error;
	}

	// OK, affichage des champs de la réponse
	else {
		$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_2');

		// suppression de la sauvegarde du panier
		if ($op == 'valider_panier') {
			if ($oCatalogue->getParams('cart_management') == 'cookie') {
				$_SESSION['catalogue']['panier']['articles'] = array();
				$_SESSION['catalogue']['panier']['montant'] = 0;
				panier2cookie();
			}
			if ($oCatalogue->getParams('cart_management') == 'bdd') {
				include_once DIMS_APP_PATH.'/modules/catalogue/include/class_panier.php';
				$panier = new cata_panier();
				$panier->open($_SESSION['dims']['userid']);
				$panier->delete();
				unset($_SESSION['catalogue']['panier']);
			}
		}
	}
}
