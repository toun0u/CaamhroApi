<?php
function dims_checkpasswordvalidity($password, $min_length = 8, $max_length = 20) {
	return (preg_match('/^.*(?=.{'.$min_length.',})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/', $password));
}

function dims_verif_data($element,$dims) {
	global $_DIMS;
	$error=false;

	if (isset($element->fields)) {
		// test existing object
		if ($element->new) $error=true;
		// test workspace accessible
		$lstwork=$dims->getWorkspaces();
		if (isset($element->fields['id_workspace'])) {
			if ($element->fields['id_workspace']>0 && !isset($lstwork[$element->fields['id_workspace']])) $error=true;
			if (isset($element->fields['id_module']) && $element->fields['id_workspace']>0 && !$dims->isModuleEnabled($element->fields['id_module'],$element->fields['id_workspace'])) $error=true;
		}
	}

	if ($error) {
		echo $_DIMS['cste']['_DIMS_SECURITY_ERROR'];
		die();
	}
}

function dims_sql_filter($data) {
	if(get_magic_quotes_gpc()) {
		$data = stripslashes($data);
	}

	/*if(_DIMS_SQL_LAYER == "mysql") {
		//check if this function exists
		if(function_exists( "mysql_real_escape_string" ))
			$data = mysql_real_escape_string($data);
	}else {
		//for PHP version < 4.3.0 use addslashes
		$data = addslashes($data);
	}*/

	return $data;
}

function dims_load_securvalue($var,$type=dims_const::_DIMS_NUM_INPUT,$get=false,$post=false,$sqlfilter=true,&$currentvar=null,$defaultvalue=null,$canBeEmpty = false) {
	if( is_array($var) || ( $get && isset($_GET[$var]) && is_array($_GET[$var]) ) ||  ( $post && isset($_POST[$var]) && is_array($_POST[$var]) ) ) {
		$result = array();
		$ok = true;
		if(is_array($var)) $tab = $var;
		else $tab = ( $get && isset($_GET[$var]) && is_array($_GET[$var]) ) ? $_GET[$var] : $_POST[$var];
		foreach($tab as $key => $value){
			if(is_array($value)) {
				$val = dims_load_securvalue($value, $type,$get,$post,$sqlfilter);#Récursif si on a un tableau de tableaux
			} else {
				$val = test_value($value, true, $type,$get,$post,$sqlfilter);#on ne prend pas les values par défaut car c'est géré sur l'objet principal en fait
			}
			if( empty($val) ) $ok = false;
			$result[$key] = $val;
		}

		if($ok && ! is_null($currentvar)){
			$currentvar = $result;
		}
		else {
			// affect current var by default value
			if (!isset($currentvar) || (!is_null($currentvar) && !$canBeEmpty && empty($currentvar) && !is_null($defaultvalue))) {
				$currentvar=$defaultvalue;
			}

			// default value par $defaultvalue or $currentvar
			if ( !is_null($currentvar) && ( !empty($currentvar) || $canBeEmpty ) ) $result=$currentvar;
			else if (!is_null($defaultvalue)) $result=$defaultvalue;
		}
	}
	else $result = test_value($var, false, $type,$get,$post,$sqlfilter,$currentvar,$defaultvalue,$canBeEmpty);
	return $result;
}

function test_value($var, $extracted_value = false, $type=dims_const::_DIMS_NUM_INPUT,$get=false,$post=false,$sqlfilter=true,&$currentvar=null,$defaultvalue=null,$canBeEmpty = false){
	$result="";
	$newvalue="";
	$isset = false;
	if ($type==dims_const::_DIMS_NUM_INPUT) {
		$result=0;

		if( ! $extracted_value ){
			// check get method
			if($get && isset($_GET[$var]) && is_numeric($_GET[$var])) {
				$isset = true;
				$newvalue=intval($_GET[$var]);
			}

			// check post method
			if($post && isset($_POST[$var]) && is_numeric($_POST[$var])) {
				$isset = true;

				$newvalue=intval($_POST[$var]);
			}
		}
		else if(isset($var) && is_numeric($var) ){
			$isset = true;
			$newvalue = intval($var);
		}

	}elseif ($type == dims_const::_DIMS_MAIL_INPUT) {
		require_once DIMS_APP_PATH . '/include/functions/mail.php';

		if( ! $extracted_value ){
			// check get method
			if($get && isset($_GET[$var]) && dims_verifyemail($_GET[$var])) {
				$isset = true;
				$newvalue=$_GET[$var];
			}

			// check post method
			if($post && isset($_POST[$var]) && dims_verifyemail($_POST[$var])) {
				$isset = true;
				$newvalue=$_POST[$var];
			}
		}
		else if(isset($var) && dims_verifyemail($var)){
			$isset = true;
			$newvalue=$var;
		}
	}
	else {
		// check get method
		if( ! $extracted_value ){
			if($get && isset($_GET[$var])) {
				$isset = true;
				if ($sqlfilter) $newvalue=dims_sql_filter($_GET[$var]);
				else $newvalue=$_GET[$var];
			}

			// check post method
			if($post && isset($_POST[$var])) {
				$isset = true;
				if ($sqlfilter) $newvalue=dims_sql_filter($_POST[$var]);
				else $newvalue=$_POST[$var];
			}
		}
		else{
			$isset = true;
			if ($sqlfilter) $newvalue=dims_sql_filter($var);
			else $newvalue=$var;
		}
	}
	if ($newvalue!="" || ($isset && $canBeEmpty)) {
		// affectation de la nouvelle valeur
		$result=$newvalue;

		if (!is_null($currentvar)) $currentvar=$result;
	}
	else {
		// affect current var by default value
		if (!isset($currentvar) || (!is_null($currentvar) && !$canBeEmpty && empty($currentvar) && !is_null($defaultvalue))) {
			$currentvar=$defaultvalue;
		}

		// default value par $defaultvalue or $currentvar
		if ( !is_null($currentvar) && ( !empty($currentvar) || $canBeEmpty ) ) $result=$currentvar;
		else if (!is_null($defaultvalue)) $result=$defaultvalue;
	}
	return $result;
}

function isModuleEnabled($moduleid,$workspaceid=-1) {
	if ($workspaceid==-1) $workspaceid=$_SESSION['dims']['workspaceid'];
	return (isset($moduleid) && ((($moduleid==dims_const::_DIMS_MODULE_SYSTEM && dims_ismanager()) || isset($_SESSION['dims']['workspaces'][$workspaceid]['modules'][$moduleid]))));
}

function dims_getPasswordSalt() {
	return substr( str_pad( dechex( mt_rand() ), 8, '0',STR_PAD_LEFT ), -8 );
}

// calculate the hash from a salt and a password
function dims_getPasswordHash($password,$salt="" ) {
	if ($salt=="") $salt = dims_getPasswordSalt();
	return $salt . ( hash( 'sha1', $salt . $password ) );
}

// compare a password to a hash
function dims_comparePassword($textpassword,$hash) {
	$salt = substr($hash,0,8);
	return "$hash" === dims_getPasswordHash($textpassword,$salt);
}

function securityCheck($level,$url="",$logids='') {
	// save for current session
	$_SESSION['dims']['security']['level']=dims_const::_DIMS_SECURITY_LEVEL_CRITICAL;
	$_SESSION['dims']['security']['time']=dims_createtimestamp();

	$secfilter= new dims_security_filter();
	$secfilter->fields['ip']=$_SERVER['REMOTE_ADDR'];
	$secfilter->fields['datefilter']=dims_createtimestamp();
	$secfilter->fields['method']=$_SERVER['REQUEST_METHOD'];
	$secfilter->fields['query']=$_SERVER['REQUEST_URI']." => url :".$url;
	if ($logids!='') {
		$secfilter->fields['query'].="\n".$logids;
	}
	$secfilter->fields['level']=$level;
	$secfilter->save();
	if ($url=="") $url=".";

	if (!isset($_SESSION['dims']['security']['redirect'])) {
		$_SESSION['dims']['security']['redirect']=$url;
		dims_redirect($url);
	}
	else {
		if (isset($_SESSION['cste'])) {
			echo "<html><body><div style=\"text-align:center;\"><br /><br /><h1>Dims Error</h1><br /><br /><b>".$_SESSION['cste']['_WCE_NO_DOMAIN']."</b><br /><br /></div></body></html>";
		}
	}
}

//fonction qui retourne un mot de passe aléatoire
function passgen($size=8) {
	$key="";
	$letter = "abcdefghijkmnpqrstuvwxyz123456789";
	for($i=0;$i< $size;$i++) {
		$key .= $letter[rand(0, strlen($letter) -1)];
	}
	return $key;
}

function dims_getiprules($rules) {
	$intervals = array();
	$iprules = array();
	$ip1 = 0;
	$ip2 = 0;

	if ($rules == '') return FALSE;
	$intervals = explode(';',$rules);

	foreach ($intervals as $interval) {
		$ips = explode('-',trim($interval));

		if (count($ips) == 1) {
			$ips[0] = trim($ips[0]);
			if (strpos($ips[0],"*") !== false) {
				$ip1 = str_replace('*','0',$ips[0]);
				$ip2 = str_replace('*','255',$ips[0]);
			}
			else {
				$ip1 = $ip2 = $ips[0];
			}
		}
		elseif (count($ips) == 2) {
			$ip1 = trim($ips[0]);
			$ip2 = trim($ips[1]);
		}

		$ip1 = ip2long($ip1);
		$ip2 = ip2long($ip2);
		$iprules[$ip1] = $ip2;
	}

	return $iprules;
}

function courante_page() {
	$page				= false;
	$num_arg			= func_num_args();
	$args				= func_get_args();
	$explode			= explode('?', $_SERVER['REQUEST_URI']);
	if (isset($explode[1])) $url_explode		= explode('&', $explode[1]);
	else $url_explode		=array();

	$url_pagination		= '';

	foreach ($url_explode as $value) {
		$value		= explode('=', $value);

		if (!in_array($value[0], $args)) {
			if (isset($value[1])) $url_pagination	.= $value[0]."=".$value[1].'&';
		}
	}

	if ($url_pagination) {
		return $explode[0]."?".substr($url_pagination, 0, -1);
	}else {
		return $explode[0];
	}
}

function dims_isEmpty($var) {
	return empty($var);
}



function dims_load_securarray($var) {
	$resultat=array();
	if(!isset($_POST[$var])){
		return $res;
	}
	$tabValue=$_POST[$var];
	for($i=0;$i< count($tabValue) ;$i++){
		if(is_numeric($tabValue[$i])) {
			$resultat[]=$tabValue[$i];
		}else {
			$resultat[]=0;
		}
	}
	return $resultat;
}

?>
