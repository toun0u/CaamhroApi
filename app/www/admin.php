<?php
//die('test');
define('AUTHORIZED_ENTRY_POINT', true);
///////////////////////////////////////////////////////////////////////////
// START DIMS ENGINE
///////////////////////////////////////////////////////////////////////////
if (substr($_SERVER["DOCUMENT_ROOT"],strlen($_SERVER["DOCUMENT_ROOT"])-1,1)!="/") $_SERVER["DOCUMENT_ROOT"].="/";

// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

//Charge la classe des gestions d'exceptions
require(DIMS_APP_PATH."include/class_exception.php");

// Ne pas mettre en cache les pages du back-office
setcookie('nocache', '1');

try {
	include(DIMS_APP_PATH.'include/start.php');

	// verify admin part
	if ($dims->getEnabledBackoffice()) {

		//Cyril - 29/06/2012 - Couche de contrôle d'accès FRONT / BACK sur l'utilisateur connecté
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {//bien sûr on teste d'abord si y'a une connexion active
			$details = $dims->getWorkspaces($_SESSION['dims']['workspaceid']);
			if( ! $details['activeback'] ){
				//on teste s'il a accès au front
				if( $details['activefront']) dims_redirect('index.php');
				else dims_redirect('index.php?dims_logout=1');//sinon on le déconnecte tout court
			}
		}

		// choix du backoffice correspondant
		if(isset($_SESSION['dims']['MOBILE_VERSION']) && $_SESSION['dims']['MOBILE_VERSION']==1 && (file_exists(realpath(DIMS_APP_PATH)."/modules/mobile/index.php"))) {
			require_once DIMS_APP_PATH.'include/backoffice_mobile.php';
		}else {
			require_once DIMS_APP_PATH.'include/backoffice.php';
		}

		if ($dims_errors_level && _DIMS_MAIL_ERRORS && _DIMS_ADMINMAIL != '')
			mail(_DIMS_ADMINMAIL,"[{$dims_errorlevel[$dims_errors_level]}] sur [{$_SERVER['HTTP_HOST']}]", "$dims_errors_nb erreur(s) sur $dims_errors_msg\n\nDUMP:\n$dims_errors_vars");

		if (defined('_DIMS_ACTIVELOG') && _DIMS_ACTIVELOG)	include DIMS_APP_PATH . 'modules/system/hit.php';
	}
	else {
		$dims->setError(2);
		securityCheck(dims_const::_DIMS_SECURITY_LEVEL_CRITICAL);
	}

/*
						   *
						  * *
						 * * *
						* * * *
					   * * * * *
					  * * * * * *
					 * * * * * * *
					* ATTENTION * *   Cyril - 04/03/2013 - Remise à false de la 1ère connexion - Ce bloc doit nécessairement être à la fin du try
				   * * * * * * * * *
				  * * * * * * * * * *
*/
	if(isset($_SESSION['dims']['from_connexion_user']) && $_SESSION['dims']['from_connexion_user']) $_SESSION['dims']['from_connexion_user'] = false;
}

catch(Error_class $e) {
	//Gestion des erreurs dans les class.

	$e->getError();
}

catch(Exception $e) {
	//Cyril - Ajout d'un filtre sur _DIMS_DEBUGMODE parce que sinon on peut voir en clair le mot de passe db
	if (defined('_DIMS_DEBUGMODE') && _DIMS_DEBUGMODE) echo $e->getMessage().'<pre>'.htmlentities($e->getTraceAsString()).'</pre>';
	else echo 'Error. Please contact the administrator of this website';
}

catch(PDOException $e) {
	echo 'Connection Failed : '.$e->getCode();
}

?>
