<?php

namespace FormToken {
	$tokenID=0; // Globale pour éviter la génération de token identique lors d'un génération trop rapide ( sur la même milliseconde)

	// Durée de vie d'un token (en secondes)
	define('_DIMS_TOKEN_EXPIRE', 3600);

	/**
	 * Cette classe génère un champ à ajouter dans les formulaires. Les tokens
	 * générés sont stockés dans une variable de $_SESSION et supprimés au fur
	 * et à mesure qu'ils sont consommés et/ou qu'ils expirent
	 */
	class TokenField {
		private $GeneratedToken;

		/**
		 * Initialise le champ a générer
		 */
		public function __construct() {
			global $tokenID;
			$token = hash("sha256", "_DIMSdims_" . time() . "-" . microtime(true) . "//" . $tokenID);
			$tokenID++;
			$debugBacktrace = debug_backtrace();
			$this->GeneratedToken = array(
				'token' => $token,
				'fields' => array(),
				'trace' => "Fichier du formulaire : ".$debugBacktrace[0]['file']."<br>Ligne : ".$debugBacktrace[0]['line']."<br>",
				'genTimestamp' => time());
			// Si le nombre de tokens en session dépasse 100, on lance le garbage collector
			if(isset($_SESSION['dims']['formtokens']) && count($_SESSION['dims']['formtokens'])>100){
				$this->garbageCollector();
			}
		}

		/**
		 * Ajoute un nom de champ a valider
		 * @param $name     Nom du champs à ajouter au token
		 * @param $const    Si non vide, la valeur du champs doit rester constante pour valider le formulaire
		 */
		public function field($name, $const = "") {
			$name = preg_replace("/\[[^\]]*\]/","",$name);
			/*str_replace(
				array('[', ']'),
				'',
				$name
			);*/
			$this->GeneratedToken['fields'][] = $name;
			if (!empty($const)){
				$this->GeneratedToken['const'][$name] = $const;
			}
		}

		/**
		 * Affiche les champs enregistres
		 */
		public function dump() {
			var_dump($this->GeneratedToken['fields']);
		}

		/**
		 * Génère le champ et stocke le token généré en session
		 * Renvoie l'HTML à intégrer au formulaire
		 */
		public function generate() {
			$html = '<input type="hidden" name="'. _DIMS_TOKEN_FIELD . '" value="' . $this->GeneratedToken['token'] . '" />';
			$this->field(_DIMS_TOKEN_FIELD,$this->GeneratedToken['token']); // for empty forms
			$_SESSION['dims']['formtokens'][$this->GeneratedToken['token']] = $this->GeneratedToken;
			return $html;
		}

		/**
		 * Fonction de ramasse miettes qui garde que les 15 tokens les + récents
		 */
		public function garbageCollector(){
			if (!isset($_SESSION['dims']['formtokens'])) return;
			uasort(
					$_SESSION['dims']['formtokens'],
					function($a, $b) {
						if ($a['genTimestamp'] == $b['genTimestamp']) {
							return 0;
						}
						return ($a['genTimestamp'] > $b['genTimestamp']) ? -1 : 1;
					}
				);
			$_SESSION['dims']['formtokens'] = array_slice($_SESSION['dims']['formtokens'], 0, 15, true);
		}
	};


	/**
	 * Cette classe vérifie le token récupéré du formulaire, et/ou supprime le token expiré
	 */
	class TokenValidator {
		public function __construct() {
		}

		public function consume($token,$validate = false) {
			if(_DIMS_DEBUGMODE && $validate)
				$_SESSION['dims']['formtokens'][$token]['genTimestamp'] = time();
			else
				unset($_SESSION['dims']['formtokens'][$token]);
		}

		/**
		* Vérifie l'authenticité du token pour valider le formulaire
		* @param $token     Token à vérifier
		*
		* @return   [errorCode]
		*                   0 : Token OK
		*                   1 : Le token n'existe pas en session
		*                   2 : Le token est expiré
		*                   3 : Un champs constant a été édité
		*           [infos]
		*                   Contient des informations + détaillées sur l'erreur
		*
		*/
		public function validate($token) {
			// vérifie si le token existe // a modifier car on ne peut plus se connecter
			if (isset($_SESSION['dims']['formtokens']) && defined('_DISABLE_TOKENIZER') && !_DISABLE_TOKENIZER) {
				if (!array_key_exists($token, $_SESSION['dims']['formtokens']))
					return array('errorCode' => 1, 'infos' => "");

				$tokenData = $_SESSION['dims']['formtokens'][$token];

				// vérifie si le token n'est pas expiré
				if (time() - $tokenData['genTimestamp'] > _DIMS_TOKEN_EXPIRE) {
					$this->consume($token);
					return array('errorCode' => 2, 'infos' => "Fichier du formulaire : ".$tokenData['trace']);
				}

				// supprime les champs de formulaires non autorisés par le token
				foreach ($_POST as $key => $value) {
					if (!in_array($key, $tokenData['fields'])) {
					unset($_POST[$key]);
					} else {
					if (isset($tokenData['const'][$key])){
						if ($tokenData['const'][$key] != $_POST[$key]){
						unset($_POST[$key]);
						return array('errorCode' => 3, 'infos' => "Fichier du formulaire : ".$tokenData['trace']."<br>Le champs constant : ".htmlspecialchars($key)." a été modifié");
						}
					}
					}
				}

				$this->consume($token,true);
			}

			// le token est valide et les champs non autorisés ont été supprimés
			return array('errorCode' => 0, 'infos' => "");
		}
	}
}
