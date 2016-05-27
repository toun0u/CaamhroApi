<?php

define('AUTHORIZED_ENTRY_POINT', true);
///////////////////////////////////////////////////////////////////////////
// START DIMS ENGINE
///////////////////////////////////////////////////////////////////////////
if (substr($_SERVER["DOCUMENT_ROOT"],strlen($_SERVER["DOCUMENT_ROOT"])-1,1)!="/") $_SERVER["DOCUMENT_ROOT"].="/";

// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

//Charge la class des gestions d'exceptions
require(DIMS_APP_PATH."include/class_exception.php");

try {
	include(DIMS_APP_PATH."include/start.php");

	// Clear cookie for Varnish if we want cache
	// Set $_SESSION['dims']['nocache'] to true to disable caching, otherwise
	// cookie will be removed (and session)
	if (isset($_SESSION) && array_key_exists("dims", $_SESSION) && (
		array_key_exists("nocache", $_SESSION['dims']) && $_SESSION['dims']['nocache'] == true
		||
		array_key_exists("formstoken", $_SESSION['dims']) && count($_SESSION['dims']['formstoken']) > 0
		)
		|| isset($_SESSION['dims']["connected"])
	) {
		setcookie("nocache", "1");
	} else {
		//session_destroy();
		//unset($_SESSION);
		//setcookie("nocache", "0", time() - 3600);
	}

	$dims_op = dims_load_securvalue('dims_op', dims_const::_DIMS_CHAR_INPUT, true, true);
	if (isset($dims_op) && $dims_op !== "") {
		require_once DIMS_APP_PATH.'include/op.php';
	}

	if( $_SESSION['dims']['mode']=='admin' &&  (!isset($_SESSION['dims']['connected']) || !$_SESSION['dims']['connected'])) {
		dims_redirect('/admin.php');
	}

	if (isset($_SESSION['dims']['mode']) && $_SESSION['dims']['mode']=='admin') {
		// controle du domaine pour verification d'activation sinon redirection vers admin
		$lstwce=$dims->getDomainToFrontEnabled();

		if (empty($lstwce)) dims_redirect('/admin.php');

	}

	require_once DIMS_APP_PATH . '/include/frontoffice.php';

if ($dims_errors_level && _DIMS_MAIL_ERRORS && _DIMS_ADMINMAIL != '') mail(_DIMS_ADMINMAIL,"[{$dims_errorlevel[$dims_errors_level]}] sur [{$_SERVER['HTTP_HOST']}]", "$dims_errors_nb erreur(s) sur $dims_errors_msg\n\nDUMP:\n$dims_errors_vars");
	if (defined('_DIMS_ACTIVELOG') && _DIMS_ACTIVELOG)	include DIMS_APP_PATH . '/modules/system/hit.php';

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

catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}

catch(PDOException $e) {
	echo 'Connection Failed : '.$e->getCode();
	if(defined('_DEBUG_MODE') && _DEBUG_MODE)
		dims_print_r($e->getTrace());
}

catch(Exception $e) {
	if(defined('_DEBUG_MODE') && _DEBUG_MODE) {
		echo '<pre>';
		echo $e->getMessage().PHP_EOL;
		print_r($e->getTrace());
		echo '</pre>';
	} else {
		ob_clean();
		die();
	}
}
