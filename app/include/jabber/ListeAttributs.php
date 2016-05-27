<?php
require_once("../../config.php");
require_once DIMS_APP_PATH . '/include/default_config.php'; // load config (mysql, path, etc.)
require_once(DIMS_APP_PATH . "/include/db/class_db_mysql.php");
require_once(DIMS_APP_PATH . "/include/jabber/functions.php");
require_once(DIMS_APP_PATH . "/include/jabber/requetes.php");

/**
 * Cette classe permet de créer les listes d'attributs du fichier messageStructure.xml
 *
 * Creator : JC
 * 5 Juil 2010
 *
 */

class ListeAttributs {
	public $intitule;
	public $attributs = array();
	public $type = array();

	public function __construct($corps){
		$this->intitule = $corps->childNodes->item(0)->nodeValue;
		$tableau = $this->creationListe($corps->childNodes->item(1));
		$this->attributs = $tableau["liste"];
		$this->type = $tableau["type"];
	}

	/**
	 * Créer la liste d'attributs d'un objet passé en argument
 	 *
	 */
	public function creationListe($objet) {
		$j = 0;
		$liste = array();
		// Tant que le message n'est pas terminé on cherche à créer des listes d'attributs
		while ($objet) {
			// Si l'attribut que l'on traite est en fait un parent d'une autre liste d'attribut
			// On créer une nouvelle liste d'attribut
			if($objet->childNodes->item(0)->nodeName != "#text")  {
				$liste[$j] = new ListeAttributs($objet);
			}
			else  {
				$liste[$j] = $objet->nodeValue;
				$type[$j] = $objet->getAttribute("type");
			}
			$objet = $objet->nextSibling;
			$j++;
		}
		$tableau = array("liste" => $liste, "type" => $type);
		return $tableau;
	}

	/**
	 * Methode permettant d'analyser la bonne construction de n'importe quel message reçu
	 * Fais suite à la methode analyseMessage (dans la classe structureMessage)
	 */
	public function analyseAttributs($xml, $resultat) {
		if($this->intitule == $xml->nodeName) {
			$xml = $xml->firstChild;
			foreach($this->attributs as $attribut) {
				if(is_object($attribut)) {
					if(!$attribut->analyseAttributs($xml->firstChild)) {
						$resultat["valide"] = false;
						return $resultat;
						// return false;
					}
				}
				else {
					if($attribut != $xml->nodeName) {
						$resultat["valide"] = false;
						return $resultat;
						// return false;
					}
					else if($attribut == "keyword") {
					 	$resultat["keyword"] = $xml->nodeValue;
					}
					else if($attribut == "jabberID") {
						$resultat["jabberID"] = $xml->nodeValue;
					}
					else if($attribut == "ID_fichier") {
						$resultat["ID_fichier"] = $xml->nodeValue;
					}
				}
				$xml = $xml->nextSibling;
			}
		}
		else {
			$resultat["valide"] = false;
			return $resultat;
			// return false;
		}
		$resultat["valide"] = true;
		return $resultat;
		// return true;
	}

	/**
	 * Methode permettant de creer la reponse du message recu
	 * Cette methode est appelée depuis creationEnTete dans la classe StructureMessage
	 */
	// refaire la fonction creationCorps

	public function creationCorps($reponse, $racine, $tableau) {

		$database = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
		$compteur = 0;
		$nbTuple = -1;
		$requete = 0;

		if($this->intitule == "news") {
			$parent = $reponse->createElement($this->intitule);
			foreach ($this->attributs as $attribut) {
				if(is_object($attribut)) {
					$element = $attribut->creationCorps($reponse, $parent, $tableau);
				}
			}
			$racine->appendChild($parent);
		}
		else {
			echo "\n\tintitule - ".$this->intitule."\n";
			$requete = quelleRequete($this->intitule, $tableau, $database);
			$nbTuple = mysql_num_rows($requete);
			while($nbTuple != $compteur) {
				$parent = $reponse->createElement($this->intitule);
				$donnees = mysql_fetch_array($requete);
				foreach ($this->attributs as $attribut) {
					if(is_object($attribut)) {
						$element = $attribut->creationCorps($reponse, $parent, $tableau);
						$compteur--;
					}
					else {
						//Cas particulier du Download
						if($attribut == "URL"){
							// On va chercher le path util
							$path = requeteURL($donnees["id_fichier"]);
							$element = $reponse->createElement($attribut, $path);
						}
						else {
							$element = $reponse->createElement($attribut, utf8_encode($donnees[$attribut]));
						}
					}
					$parent->appendChild($element);
				}
				$compteur++;
				$racine->appendChild($parent);
			}
		}
		return $racine;
	}

	/**
	 * Methode permettant de creer le message de connexion
	 * Ce message est a destination du relais
 	 * Cette methode est appelée dans la classe StructureMessage (methode messageConnexion())
	 */
	public function attributsConnexion ($tableau, $racine, $connexion) {
		$parent = $connexion->createElement($this->intitule);

		foreach($this->attributs as $attribut) {
			$element = $connexion->createElement($attribut, $tableau[$attribut]);
			$parent->appendChild($element);
		}

		$racine->appendChild($parent);
		return $racine;
	}

	/**
	 * Methode permettant d'afficher tout un objet ListeAttributs de façon ordonné
	 *
	 */
	public function toString() {
		echo "\t\t".$this->intitule."\n";
		foreach($this->attributs as $attribut) {
			if(is_object($attribut)) {
				$attribut->toString();
			}
			else {
				echo "\t\t\t".$attribut."\n";
			}
		}
	}

                public function getIntitule () {
		return $this->intitule;
	}
}

?>
