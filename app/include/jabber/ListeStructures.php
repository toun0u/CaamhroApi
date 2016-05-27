<?php
require_once(DIMS_APP_PATH . "/include/jabber/paramsJabber.php");
require_once(DIMS_APP_PATH . "/include/jabber/StructureMessage.php");

/**
 * Cette classe permet de créer la liste des différents messages de messageStructure.xml
 *
 * Creator : JC
 * 5 Juil 2010
 *
 */

class ListeStructures {
	public $liste = array();

	public function __construct() {
		// appel à la methode de création de la liste
		$this->liste = $this->creation();
	}

	public function creation() {
		$document = new DomDocument();
		// Permet de retirer tous les espaces et retour à la ligne dans messageStructure.xml
		$document->preserveWhiteSpace = false;
		// Le document modele est messageStructure.xml

		$document->load(params::MessageXML);

		$messages = $document->getElementsByTagName("message");

		$i = 0;
		// Pour tous les types de messages, on créer un objet StructureMessage
		foreach($messages as $message) {
			$liste[$i] = new StructureMessage($message->childNodes->item(0), $message->childNodes->item(1), $message->childNodes->item(2), $message->childNodes->item(3));
			$i++;
		}
		return $liste;
	}

	/**
	 * Methode permettant d'afficher tout un objet ListeStructures de façon ordonné
	 *
	 */
	public function toString() {
		foreach($this->liste as $mess) {
			$mess->toString();
		}
	}

	/**
	 * Methode permettant d'analyser la construction d'un message envoyé en paramètre
	 *
	 */
	public function analyseStructure($xml) {
		$racine = $xml->documentElement;
		$resultat = array();

		$valide = false;

		foreach($this->liste as $mess) {
					//echo $mess->getDesignation()."<br>";
			if($mess->getDesignation() == $racine->nodeName) {
				//echo "\nVérif Constr - ".$racine->nodeName;
				$resultat = $mess->analyseMessage($racine->firstChild, $resultat);
				break;
			}
		}

				if ($mess!=null) {
					$resultat["designation"] = $mess->getDesignation();
					$resultat["corps"] = $mess->getCorps();
				}

		return $resultat;
	}

	/**
	 * Methode permettant de creer la reponse du message recu
	 * Cette reponse est a destination du relais
	 */
	public function creationReponse($tableau) {
		// on retire le "request" du nom du message pour créer celui de la reponse
		$designation = substr($tableau["designation"], 7, strlen($tableau["designation"]));

		$reponse = new DomDocument("1.0");
		$racine = $reponse->createElement($designation);

		foreach($this->liste as $mess) {
			if($mess->getDesignation() == $designation) {
				$racine = $mess->creationEnTete($reponse, $racine, $tableau);
				break;
			}
		}

		// Création de l'architecture du nouveau message
		$reponse->appendChild($racine);
		return $reponse;
	}

	/**
	 * Methode permettant de creer le message de connexion
	 * Ce message est a destination du relais
	 */
	public function structureConnexion($tableau) {

		$connexion = new DomDocument("1.0");
		$racine = $connexion->createElement($tableau["designation"]);

		foreach($this->liste as $mess) {
			if($mess->getDesignation() == $tableau["designation"]) {
				$valide = $mess->messageConnexion($tableau, $racine, $connexion);
				break;
			}
		}


		// Création de l'architecture du nouveau message
		$connexion->appendChild($racine);
		return $connexion;
	}


}

?>
