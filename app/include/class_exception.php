<?php
//Gestion des exceptions dans les classes.
class Error_class extends Exception {
	function __construct($error, $full_debug=false){
		if (!is_array($error)) {
			throw new Error_class(array("message" => "error is not array"));

			return false;
		}

		//Si la plateforme est en mode debug mon incr�mente la variable d'erreur.
		if (_DIMS_DEBUGMODE == true) {
			//Chercher les points d'arr�t des fichiers.
			$trace	= array_reverse(debug_backtrace());
			$count	= count($trace)-2;

			//Cherche le nom de la class.
			$error["class"]		= $trace[$count]["class"];

			//Cherche le nom de la fonction.
			$error["function"]	= $trace[$count]["function"];

			//affiche les fichiers sauf ceus qui sont avant cms.php
			$print_last_error	= false;
			$error["file_exit"]	= "";

			foreach ($trace as $value){
				if (isset($value["file"]) and isset($value["line"]) and ($print_last_error or (isset($full_debug) && $full_debug))) {
					$error["file_exit"]	.= "Fichier : ".$value["file"].", line : ".$value["line"]."<br />";
				}

				if (isset($value["file"]) and preg_match("#modules/wce/include/global.php$#", $value["file"])) {
					$print_last_error	= true;
				}
			}

			$_SESSION["error_class"][]	= $error;

			parent::__construct("");
		}
	}

	function getError(){
		$liste_error	= false;

		if (is_array($_SESSION["error_class"])) {
			foreach ($_SESSION["error_class"] as $error) {
				foreach ($error as $key => $value){
					$liste_error	.= "<b>".$key."</b> => ".$value."<br />\n";
				}

				$liste_error	.= "<br />\n";
			}
		}

		if ($liste_error) {
			print $liste_error;
			unset($_SESSION["error_class"]);
			exit;
		}
	}
}

//Gestion des exceptions dans les requet MySQL.
class Error_MySQL extends Exception {
	function __construct($query){
		if (!$query) {
			throw new Error_class(array("message" => "query is null"));

			return false;
		}

		//Si la plateforme est en mode debug mon incr�mente la variable d'erreur.
		if (_DIMS_DEBUGMODE == true) {
			//Chercher les points d'arr�t des fichiers.
			$trace	= array_reverse(debug_backtrace());

			//affiche les fichiers sauf ceus qui sont avant cms.php
			$print_last_error	= false;
			foreach ($trace as $value){
				if (isset($value["file"]) && $value["file"] and $value["line"] and ($print_last_error or (isset($full_debug) && $full_debug))) {
					$error["file_exit"]	.= "Fichier : ".$value["file"].", line : ".$value["line"]."<br />";
				}
			}

			$error["query"]			= $query;
			$error["code_error"]	= mysql_errno();
			$error["text_error"]	= mysql_error();
			$error["id_error"]		= "error".rand();


			$_SESSION["error_mysql"][]	= $error;

			parent::__construct("");
		}
	}

	function getError(){
		$liste_error	= false;

		if (is_array($_SESSION["error_mysql"])) {
			foreach ($_SESSION["error_mysql"] as $error) {
				if(_DIMS_SQL_LAYER == "oracle"){
					if (!isset($error["file_exit"])) $error["file_exit"]="";
					$liste_error	= '	<div style="border:0.1em solid #FF0000; margin: 5px; padding: 10px 10px 10px 10px; background-color: #FFFFCC; color: red;">
										<span style="font-style: bold;">Requet MySQL</span> :
										<br />'.
										$error["query"]["sqltext"].
										'<br /><br />
										<span style="font-style: bold;">Error MySQL</span> : <br />'.
										$error["query"]["code"].
										" - ".
										$error["query"]["message"].
										'<br /><br />
										Context :
										<br/>'.
										$error["file_exit"].
										"</div>\n";

					trigger_error($error["text_error"]."<BR><B>query:</B>".$error["query"], E_USER_WARNING);
				}else{
					if (!isset($error["file_exit"])) $error["file_exit"]="";
					$liste_error	= '	<div style="border:0.1em solid #FF0000; margin: 5px; padding: 10px 10px 10px 10px; background-color: #FFFFCC; color: red;">
										<span style="font-style: bold;">Requet MySQL</span> :
										<br />'.
										$error["query"].
										'<br /><br />
										<span style="font-style: bold;">Error MySQL</span> : <br />'.
										$error["code_error"].
										" - ".
										$error["text_error"].
										'<br /><br />
										Context :
										<br/>'.
										$error["file_exit"].
										"</div>\n";

					trigger_error($error["text_error"]."<BR><B>query:</B>".$error["query"], E_USER_WARNING);
				}
			}
		}

		if ($liste_error) {
			print $liste_error;
			unset($_SESSION["error_mysql"]);
			//exit;
		}
	}
	//function getError(){
	//	$liste_error	= false;
	//
	//	if (is_array($_SESSION["error_mysql"])) {
	//		foreach ($_SESSION["error_mysql"] as $error) {
	//			if (!isset($error["file_exit"])) $error["file_exit"]="";
	//			$liste_error	= '	<div style="border:0.1em solid #FF0000; margin: 5px; padding: 10px 10px 10px 10px; background-color: #FFFFCC; color: red;">
	//								<span style="font-style: bold;">Requet MySQL</span> :
	//								<br />'.
	//								$error["query"]["sqltext"].
	//								'<br /><br />
	//								<span style="font-style: bold;">Error MySQL</span> : <br />'.
	//								$error["query"]["code"].
	//								" - ".
	//								$error["query"]["message"].
	//								'<br /><br />
	//								Context :
	//								<br/>'.
	//								$error["file_exit"].
	//								"</div>\n";
	//
	//			trigger_error($error["text_error"]."<BR><B>query:</B>".$error["query"], E_USER_WARNING);
	//		}
	//	}
	//
	//	if ($liste_error) {
	//		print $liste_error;
	//		unset($_SESSION["error_mysql"]);
	//		//exit;
	//	}
	//}
}

//Gestion des exceptions Fatal.
class Error_Fatal extends Exception {

	function __construct($code_error=-1){
		$_SESSION["erreur_fatal"]	= $code_error;
		parent::__construct("");
	}

	function getError(){
		global $dims;
		//Affichage de la page d'erreur.
		if (defined('_DIMS_URL_ERREUR_FATAL') && is_file(_DIMS_URL_ERREUR_FATAL)) {
			require(_DIMS_URL_ERREUR_FATAL);
		}
		else{
			// test sur l'acces
			if ($dims->getScriptEnv() =='index.php') {
				if (isset($_SESSION['dims']['front_template_path']) && file_exists($_SESSION['dims']['front_template_path']."/error.php")) {

				}
				elseif (file_exists(DIMS_APP_PATH."templates/frontoffice/default/error.php"))
					require(DIMS_APP_PATH."templates/frontoffice/default/error.php");
				else {
					if (file_exists(DIMS_APP_PATH."templates/backoffice/dims/error.php"))
					require(DIMS_APP_PATH."templates/backoffice/dims//error.php");
				}
			}
			else {
				if (file_exists(DIMS_APP_PATH."templates/backoffice/dims/error.php"))
					require(DIMS_APP_PATH."templates/backoffice/dims//error.php");
			}
		}

		//Envoi d'un mail � l'administrateur.
		$this->mail();

		unset($_SESSION["erreur_fatal"]);
		exit;
	}

	private function mail(){
		require_once(DIMS_APP_PATH."include/phpmailer/class.phpmailer.php");
		$mail = new PHPMailer;

		$mail->AddAddress(_DIMS_ADMINMAIL);
		$mail->IsHTML(false);

		$mail->From		= _DIMS_ADMINMAIL;
		$mail->FromName = $_SERVER["HTTP_HOST"];

		$mail->Subject = "Erreur fatal sur ".$_SERVER["HTTP_HOST"];
		$mail->Body    = "Erreur fatal le ".date("d/m/Y � H:i:s")." sur le site ".$_SERVER["HTTP_HOST"]."\n";

		$mail->Body    .= "Erreur interne : ".$_SESSION["erreur_fatal"]."\n";
		if (isset($_SESSION["dims_error_fatal"][$_SESSION["erreur_fatal"]]["type"]))
			$mail->Body    .= "Erreur type : ".$_SESSION["dims_error_fatal"][$_SESSION["erreur_fatal"]]["type"]."\n";
		if (isset($_SESSION["dims_error_fatal"][$_SESSION["erreur_fatal"]]["synification"]))
		$mail->Body    .= "Erreur synification : ".$_SESSION["dims_error_fatal"][$_SESSION["erreur_fatal"]]["synification"]."\n";

		if(!$mail->Send()){
		   return false;
		}
	}
}
?>
