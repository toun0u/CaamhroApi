<?php
require_once DIMS_APP_PATH.'include/class_error.php';

define('_DIMS_ERROR_LOGINEXPIRE', 1);

//if (defined('_DIMS_ERROR_REPORTING')) {error_reporting(_DIMS_ERROR_REPORTING);}
if (!defined('_DIMS_DISPLAY_ERRORS')) define('_DIMS_DISPLAY_ERRORS', false);
if (!defined('_DIMS_MAIL_ERRORS')) define('_DIMS_MAIL_ERRORS', false);
if (!defined('_DIMS_ADMINMAIL')) define('_DIMS_ADMINMAIL', '');

global $dims_errors_msg;
global $dims_errors_vars;
global $dims_errors_nb;
global $dims_errors_level;
global $dims_errortype;
global $dims_errorlevel;

$dims_errors_msg = '';
$dims_errors_vars = '';
$dims_errors_nb = 0;
$dims_errors_level = 0;

if (!isset($_SESSION['dims']['errors']['dims_errortype'])) $_SESSION['dims']['errors']['dims_errortype'] = array (
							E_ERROR			 => 'Error',
							E_WARNING		 => 'Warning',
							E_PARSE			 => 'Parse Error',
							E_NOTICE		 => 'Notice',
							E_CORE_ERROR	 => 'Core Error',
							E_CORE_WARNING	 => 'Core Warning',
							E_COMPILE_ERROR  => 'Compile Error',
							E_COMPILE_WARNING => 'Compile Warning',
							E_USER_ERROR	 => 'User Error',
							E_USER_WARNING	 => 'User Warning',
							E_USER_NOTICE	 => 'User Notice'
							);

if (!isset($_SESSION['dims']['errors']['dims_errorlevel'])) $_SESSION['dims']['errors']['dims_errorlevel'] = array (
							0 => 'OK',
							1 => 'WARNING',
							2 => 'CRITICAL ERROR'
							);


function dims_errorhandler($errno, $errstr, $errfile, $errline, $vars) {
	global $dims;
	/*
	global $dims_errors_msg;
	global $dims_errors_nb;
	global $dims_errors_vars;
	global $dims_errors_level;

	global $dims_errortype;
	global $dims_errorlevel;

	// translate error_level into "readable" array
	$bit = _DIMS_ERROR_REPORTING;
	$res = array();

	while ($bit > 0)
	{
	   for($i = 0, $n = 0; $i <= $bit; $i = 1 * pow(2, $n), $n++) {
		   $end = $i;
	   }
	   $res[] = $end;
	   $bit = $bit - $end;
	}

	// if error in error reporting levels
	if (in_array($errno,$res))
	{
		$dims_errors_nb++;

		if ($errno == E_ERROR || $errno == E_PARSE || $errno == E_USER_ERROR) $dims_errors_level = 2;
		else if (($errno == E_WARNING || $errno == E_NOTICE || $errno == E_USER_NOTICE) && $dims_errors_level < 2) $dims_errors_level = 1;

		if ($dims_errors_msg == '') $dims_errors_msg  = "[{$_SERVER['HTTP_HOST']}] le ".date("d-m-Y H:i:s (T)")."\n\nVersion PHP : ".PHP_VERSION."\nOS : ".PHP_OS."\n\n";

		$dims_errors_msg .= "\nType d'erreur : {$dims_errortype[$errno]}\nMessage : $errstr\nFichier : $errfile\nLigne : $errline\n";

		ob_start();
		print_r($vars);
		$dims_errors_vars = ob_get_contents();
		ob_end_clean();

		if (_DIMS_DISPLAY_ERRORS)
		{
			// display message
			echo	"
					<div class=\"dims_error\">
					<b>{$dims_errortype[$errno]}</b> <span>$errstr</span> dans <b>$errfile</b> � la ligne <b>$errline</b>
					</div>
					";

			if ($errno == E_ERROR || $errno == E_PARSE || $errno == E_USER_ERROR)
			{
				if (_DIMS_MAIL_ERRORS && _DIMS_ADMINMAIL != '') mail(_DIMS_ADMINMAIL,"[{$dims_errorlevel[$dims_errors_level]}] sur [{$_SERVER['HTTP_HOST']}]", "$dims_errors_nb erreur(s) sur $dims_errors_msg\n\nDUMP:\n$dims_errors_vars");
				die();
			}

		}
		else
		{
			// critical error
			if ($errno == E_ERROR || $errno == E_PARSE || $errno == E_USER_ERROR)
			{
				while (@ob_end_clean());
				echo '<html><body><div align="center">Une erreur est survenue sur le site.<br />Contactez l\'administrateur.</div></body></html>';
				if (_DIMS_MAIL_ERRORS && _DIMS_ADMINMAIL != '') mail(_DIMS_ADMINMAIL,"[{$dims_errorlevel[$dims_errors_level]}] sur [{$_SERVER['HTTP_HOST']}]", "$dims_errors_nb erreur(s) sur $dims_errors_msg\n\nDUMP:\n$dims_errors_vars");
				die();
			}
		}
	}
	*/
	if (!file_exists('./install.php') && _DIMS_DISPLAY_ERRORS && $dims->debugmode) {
		require_once(DIMS_APP_PATH."include/class_error.php");
		$error= new dims_error();
		$error->fields['id_user']			= (isset($_SESSION['dims']['userid'])) ? $_SESSION['dims']['userid']:0;
		$error->fields['id_module']			= (isset($_SESSION['dims']['moduleid'])) ? $_SESSION['dims']['moduleid'] : 0;
		$error->fields['id_workspace']		= (isset($_SESSION['dims']['workspaceid'])) ? $_SESSION['dims']['workspaceid'] : 0;
		$error->fields['date_create']		= dims_getdatetime();
		$error->fields['method']			= $_SERVER['REQUEST_METHOD'];
		$error->fields['query']				= $_SERVER['REQUEST_URI'];
		$error->fields['message']="";
		if (error_reporting() & $errno) {
			if (isset($_SESSION['dims']['errors']['dims_errortype'][$errno])) $error->fields['message'] = "Type : {$_SESSION['dims']['errors']['dims_errortype'][$errno]}";
			$error->fields['message']			.= "\nMessage : $errstr\nFile : $errfile\nLine : $errline\n";

			if (_DIMS_DEBUGMODE) {
				echo "<font style=\"color:#FF0000;\">".$error->fields['message']."</font>";
			}
			$error->save();
		}
	}


	//echo $error->fields['message'];
}

set_error_handler("dims_errorhandler");

?>
