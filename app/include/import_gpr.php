<?php
###############################################
# RECUPERATION DES VARIABLES GET/POST/REQUEST #
###############################################
$purifier='';
define('_DIMS_TOKEN_FIELD', '_dims_formtoken');

//define cache temporary folder
if (!file_exists(DIMS_TMP_PATH.'purifier/')) dims_makedir (DIMS_TMP_PATH.'purifier/');

function strip_rec($val) {
	if(is_array($val)) {
		foreach($val as &$v)
			$v = strip_rec($v);
		return $val;
	}

	return strip_tags(force_decode_entities(html_entity_decode($val)));
}

function purify_rec($purifier, $val) {
	if(is_array($val)) {
		foreach($val as &$v)
			$v = purify_rec($purifier, $v);
		return $val;
	}

	return $purifier->purify(force_decode_entities(html_entity_decode($val, ENT_COMPAT | ENT_HTML5, 'UTF-8')));
}

function force_decode_entities($text){
	return preg_replace_callback("/(&#[0-9]+;)/", function($m) {
				return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
			}, $text);
}


//TODO --> Il faut que ce soit récursif
if (isset($_GET) && !empty($_GET)) {
	foreach ($_GET as $clef => $value) {
		strip_rec($value);
		/*if(!is_array($value)) $_GET[$clef] = strip_tags($value);
		else{//il faut le faire de manière récursive
			foreach($value as $idx => $v){
				$_GET[$clef][$idx] = strip_tags($v);
			}
		}*/
	}
}

if (isset($_POST) && !empty($_POST)) {
	// filter using Token (if exists)
	$tokenValid = true;
	if (array_key_exists(_DIMS_TOKEN_FIELD, $_POST)) {
		require_once DIMS_APP_PATH.'/include/class_form_tokenizer.php';
		$validator = new FormToken\TokenValidator;
		$postPreValidator = $_POST;
		if (isset($_POST[_DIMS_TOKEN_FIELD])){
			$tokenString = $_POST[_DIMS_TOKEN_FIELD];
		}
		// Affichage des entrées POST qui ne sont pas vérifiées par le TokenValidator
		if (_DIMS_DEBUGMODE && isset($tokenString) && isset($_SESSION['dims']['formtokens'][$tokenString]) ) {
			$allPostAreTokenized = true;
			$missingToken = array();
			foreach ($_POST as $keyPOST => $valuePOST) {
				$tokenExisting = false;
				foreach ($_SESSION['dims']['formtokens'][$tokenString]['fields'] as $keyFields => $valueFields) {
					if ($keyPOST==_DIMS_TOKEN_FIELD || $keyPOST == $valueFields){
						$tokenExisting = true;
					}
				}
				if (!$tokenExisting){
					$allPostAreTokenized = false;
					$missingToken[] = $keyPOST;
				}
			}
			if (!$allPostAreTokenized){
				$messageTokenFail = "<h1>ERREUR : Certaines valeurs de POST ne sont pas vérifiées par le TokenValidator</h1>";
				$messageTokenFail .= "<h3>".$_SESSION['dims']['formtokens'][$tokenString]['trace']."</h3>";
				$messageTokenFail .= "<h3>Champs manquants : </h3>";
				foreach ($missingToken as $key => $value) {
					$messageTokenFail .= $value."<br>";
				}
				die($messageTokenFail);
			}
		}
		// Validation du token
		$tokenErrorCode = $validator->validate($_POST[_DIMS_TOKEN_FIELD]);
		if ($tokenErrorCode['errorCode'] != 0) {
			// Affichage en détail du contenu de POST, du token soumis (s'il existe) ainsi que de tous les tokens en session
			if (defined('_DIMS_DEBUGMODE') && _DIMS_DEBUGMODE) {
				switch ($tokenErrorCode['errorCode']) {
					case 1: // Le token n'existe pas en session
						$messageTokenFail = "<h1>ERREUR : Token de formulaire invalide (code : 1 Le token n'existe pas en session)</h1>";
						$messageTokenFail .= "<h3>".$tokenErrorCode['infos']."</h3>";
						break;
					case 2: // Le token est expiré
						$messageTokenFail = "<h1>ERREUR : Token de formulaire invalide (code : 2 Le token est expiré)</h1>";
						$messageTokenFail .= "<h3>".$tokenErrorCode['infos']."</h3>";
						break;
					case 3: // Un champs constant a été édité
						$messageTokenFail = "<h1>ERREUR : Token de formulaire invalide (code : 3 Un champs constant a été modifié)</h1>";
						$messageTokenFail .= "<h3>".$tokenErrorCode['infos']."</h3>";
						break;

					default: // Code d'erreur inconnu
						$messageTokenFail = "<h1>ERREUR : Token de formulaire invalide (code : $tokenErrorCode CODE INCONNU)</h1>";

						break;
				}
				// Dump de la variable POST
				$messageTokenFail .= '<h3>Vardump $_POST: </h3><pre>'.print_r($postPreValidator,true).'</pre>';
				if ( isset($tokenString) && isset($_SESSION['dims']['formtokens'][$tokenString]) ){
					// Si le token existe on le dump
					$messageTokenFail .= '<h3>Contenu du token du formulaire soumi: </h3><pre>'.print_r($_SESSION['dims']['formtokens'][$tokenString],true).'</pre>';
				} else {
					if ( isset($tokenString) )
						$messageTokenFail .= '<h3>Le token du formulaire soumi n\'a pas été retrouvé en session, id du token : '.$tokenString.' </pre>';
				}
				$messageTokenFail .= '<h3>Liste des tokens en session: </h3><pre>'.print_r($_SESSION['dims']['formtokens'],true).'</pre>';

				$validator->consume($_POST[_DIMS_TOKEN_FIELD]);
				die ($messageTokenFail);
			}

			unset($_POST);
			$tokenValid = false;
		}
	}


	if ($tokenValid) {
		foreach ($_POST as $clef => $value) {
			//specific replace to preserve html fckeditor content
			if (substr($clef,0,4)=="fck_") {
				// initialisation de purifier
				if ($purifier=='') {
					require_once DIMS_APP_PATH.'scripts/htmlpurifier/library/HTMLPurifier.auto.php';
					$config = HTMLPurifier_Config::createDefault();
					$config->set('Core.Encoding', _DIMS_ENCODING);
					$config->set('Attr.AllowedFrameTargets', array('_blank','_top','_self','_parent'));
					$config->set('HTML.SafeIframe', true);
					// $config->set('URI.SafeIframeRegexp','%^((http|https):)?//(www.youtube.com/embed/|player.vimeo.com/video/)%');
					$config->set('URI.SafeIframeRegexp', '%^(http:)?(https:)?(//)?([\w\d-_]+).([\w\d-_]+)%');
					$config->set('HTML.SafeObject', true);
					$config->set('HTML.SafeEmbed', true);
					//$config->set('Output.FlashCompat', true);
					$config->set('HTML.Trusted', true);
					$config->set('Filter.YouTube', true);
					//$config->set('HTML.Allowed', 'object[width|height|data],param[name|value],embed[src|type|allowscriptaccess|allowfullscreen|width|height]');
					$config->set('Cache.SerializerPath', realpath(DIMS_TMP_PATH).'/purifier/');
					//Permettre les ancres sur les balises <a>
   					$config->set('Attr', 'EnableID', true);
					$config->set('Attr', 'EnableAttrID', true);

					// Permettre les map
					$def = $config->getHTMLDefinition(true);

					// Add usemap attribute to img tag
					$def->addAttribute('img', 'usemap', 'CDATA');

					// Add map tag
					$map = $def->addElement(
						'map',   // name
						'Block',  // content set
						'Flow', // allowed children
						'Common', // attribute collection
						array( // attributes
							'name' => 'CDATA',
							'id' => 'ID',
							'title' => 'CDATA',
						)
					);
					$map->excludes = array('map' => true);

					// Add area tag
					$area = $def->addElement(
						'area',   // name
						'Block',  // content set
						'Empty', // don't allow children
						'Common', // attribute collection
						array( // attributes
							'name' => 'CDATA',
							'id' => 'ID',
							'alt' => 'Text',
							'coords' => 'CDATA',
							'accesskey' => 'Character',
							'nohref' => new HTMLPurifier_AttrDef_Enum(array('nohref')),
							'href' => 'URI',
							'shape' => new HTMLPurifier_AttrDef_Enum(array('rect','circle','poly','default')),
							'tabindex' => 'Number',
							'target' => new HTMLPurifier_AttrDef_Enum(array('_blank','_self','_target','_top'))
						)
					);
					$area->excludes = array('area' => true);

					//init
					$purifier = new HTMLPurifier($config);
				}
				unset($_POST[$clef]);
				$clef = substr($clef,4);
				$_POST[$clef] = $value;
				// Purify fck editor
				if (is_array($value)) {
					$_POST[$clef] = purify_rec($purifier, $value);
				}
				else {
					$_POST[$clef] = $purifier->purify($value);
				}
			}
			else {
				if (!isset($_POST['dims_op']) || $_POST['dims_op']!='syncemails') {
					$_POST[$clef] = strip_rec($value);
				}
			}
		}
	}
}

if (isset($_GET['dims_url'])) {
	foreach(explode('&',base64_decode($_GET['dims_url'])) as $param) {
		if (strstr($param, '=')) list($key, $value) = explode('=',$param);
		else {$key = $param; $value = '';}

		$_GET[$key] = strip_tags($value);
	}
}

/* filter $_FILES with basename */
if (isset($_FILES) && !empty($_FILES)) {
	foreach($_FILES as $k => $file) {
		if (isset($file['name']) && !is_array($file['name'])) {
			$_FILES[$k]['name'] =  basename($_FILES[$k]['name']);

			// filtrage d'upload de .php
			$ext = pathinfo($_FILES[$k]['name'], PATHINFO_EXTENSION);
			if ($ext == "php") unset($_FILES[$k]);
		}
	}
}
?>
