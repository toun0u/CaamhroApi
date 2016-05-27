<?php
require_once '../config.php'; // load config (mysql, path, etc.)
// define('AUTHORIZED_ENTRY_POINT', true);
// session_start();
//vérification de l'ip source 
$options=array(
      CURLOPT_URL            => "http://ip.netlor.fr/",    
      CURLOPT_HEADER         => false,      
      CURLOPT_FAILONERROR    => false,        
      CURLOPT_RETURNTRANSFER => true,
);

// curl pour api
$CURL=curl_init();
if(empty($CURL)){die("ERREUR curl_init : Il semble que cURL ne soit pas disponible.");}
curl_setopt_array($CURL,$options);
$res=curl_exec($CURL);        
if(curl_errno($CURL)){
    echo "ERREUR curl_exec : ".curl_error($CURL);
}
curl_close($CURL);

// On vérifie que l'ip du visiteur correspond à celle du fichier de config
if($res == _DIMS_IP_AUTHORIZED){
	//adresse relative au serveur Node.js à changer si opérateur différent
	define("_TELEPHONY_API", "http://telephony:1337/");


	if ($_POST['action'] == 'poll') {
		// On poll depuis le proxy NodeJS de Telephony
		$data = @file_get_contents(_TELEPHONY_API."poll?token=".$_POST['token']."&lastevent=".$_POST['lastEvent']) or die('{"ongoing":[],"log":[],"taken":[]}');
		echo $data;
		//echo $_SESSION['dims']['connected'];
	}
	//prise de notes d'un appel
	else if($_POST['action'] == 'takeCall'){

		mysql_connect(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD);
		mysql_select_db(_DIMS_DB_DATABASE);
		
		$query = mysql_query("SELECT account FROM dims_mod_telephony_tokens WHERE token='" . mysql_real_escape_string($_POST['token']) . "' LIMIT 1");
		
		if (mysql_num_rows($query) > 0) {
			$row = mysql_fetch_array($query);
			$SIPAccount = $row['account'];
			//test takecall
			//interface génerique telephony
			require_once ("../app/include/interface_telephony.php");    
		//////////////////////////////////////////////////////////////    BLOC A MODIFIER SI OPERATEUR != KEYYO
			require_once ("../app/include/class_keyyo.php");        //    
			$keyyo = new Keyyo($SIPAccount);                        //  
			//ligne keyyo, reference de l'appel, numero de l'applant, direction de l'appel (incoming,outgoing), session de l'appel (ref standard)
			$keyyo->takeCall($SIPAccount,$_POST['ref'],$_POST['number'],$_POST['direction'],$_POST['sessionid']);// 	 
		//////////////////////////////////////////////////////////////    
		}

	}//appel
	else if ($_POST['action'] == 'call') {

		mysql_connect(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD);
		mysql_select_db(_DIMS_DB_DATABASE);
		
		$query = mysql_query("SELECT account FROM dims_mod_telephony_tokens WHERE token='" . mysql_real_escape_string($_POST['token']) . "' LIMIT 1");
		
		if (mysql_num_rows($query) > 0) {
			$row = mysql_fetch_array($query);
			$SIPAccount = $row['account'];
			// Lancement d'un appel vers le numéro indiqué
			//interface génerique telephony
			require_once ("../app/include/interface_telephony.php");    
		//////////////////////////////////////////////////////////////    BLOC A MODIFIER SI OPERATEUR != KEYYO
			require_once ("../app/include/class_keyyo.php");        //    require_once("...../class_operateur");
			$keyyo = new Keyyo($SIPAccount);                        //    $op=new Operateur($numerolignetelehponique);
			$keyyo->makeCall($_POST['number'],$_POST['nom_ref']);   // 	  $op->makeCall(num_apellé,nomapellé);
		//////////////////////////////////////////////////////////////    makeCall méthode générique cf interface_telephony.php
		}
	}
	else if ($_POST['action'] == 'send_sms') {

		//Mode sms non configuré si forfait non souscris
		mysql_connect(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD);
		mysql_select_db(_DIMS_DB_DATABASE);

		$query = mysql_query("SELECT account FROM dims_mod_telephony_tokens WHERE token='" . mysql_real_escape_string($_POST['token']) . "' LIMIT 1");

		if (mysql_num_rows($query) > 0) {
			$row = mysql_fetch_array($query);
			$SIPAccount = $row['account'];

			// Envoi d'un SMS au numéro indiqué avec le texte indiqué

			//interface génerique telephony
			require_once ("../app/include/interface_telephony.php");

		//////////////////////////////////////////////////////////////    BLOC A MODIFIER SI OPERATEUR != KEYYO
			require_once ("../app/include/class_keyyo.php");        //    require_once("...../class_operateur");
			$keyyo = new Keyyo($SIPAccount);                        //    $op=new Operateur($numerolignetelehponique);
			$keyyo->sendSMS($_POST['number'], $_POST['text']);      // 	  $op->sendSMS(num_apellé,message);
		//////////////////////////////////////////////////////////////    sendSMS méthode générique cf interface_telephony.php
			
		}
	}

}


?>