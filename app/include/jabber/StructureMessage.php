<?php
require_once(DIMS_APP_PATH . "/include/jabber/ListeAttributs.php");

/**
 * Cette classe permet de créer la structure de chaque message contenu dans messageStructure.xml
 *
 * Creator : JC
 * 5 Juil 2010
 *
 */

class StructureMessage {
	public $designation;
	public $from;
	public $to;
	public $corps;

	public function __construct($designation, $from, $to, $corps){
		$this->designation = $designation->nodeValue;
		$this->from = $from->nodeValue;
		$this->to = $to->nodeValue;
		$this->destinationReponse = "";
		// Le corps est compose d'une suite de bloc
		// Un bloc est compose d'un intitule suivis de différents attributs
		$this->corps = new ListeAttributs($corps);
	}

	/**
	 * Methode permettant d'analyser la bonne construction de n'importe quel message reçu
	 * Fais suite à la methode analyseStructure (dans la classe ListeStructure)
	 */
	public function analyseMessage($xml, $resultat) {
		if($this->from == $xml->nodeName && $this->to == $xml->nextSibling->nodeName) {
			//echo "\n\tfrom - ".$xml->nodeValue;
			$resultat["from"] = $xml->nodeValue;
			$resultat = $this->corps->analyseAttributs($xml->nextSibling->nextSibling, $resultat);
			return $resultat;
			// return $this->corps->analyseAttributs($xml->nextSibling->nextSibling);
		}
		else {
			$resultat["valide"] = false;
			return $resultat;
			// return false;
		}
	}

	/**
	 * Methode permettant de creer la reponse du message recu
	 * Cette methode est appelée depuis creationReponse dans la classe ListeStructures
	 */
	public function creationEnTete($reponse, $racine, $tableau) {
		$from = $reponse->createElement($this->from, params::socket_login);
		$to = $reponse->createElement($this->to, $tableau["from"]);

		// Création de l'architecture
		$racine->appendChild($from);
		$racine->appendChild($to);

				// On continue la création
				$racine = $this->corps->creationCorps($reponse, $racine, $tableau);

		return $racine;
	}

	/**
	 * Methode permettant de creer le message de connexion
	 * Ce message est a destination du relais
	 * Cette methode est appelée dans la classe ListeStructures (methode structureConnexion())
	 */
	public function messageConnexion($tableau, $racine, $connexion) {
		$from = $connexion->createElement($this->from, $tableau[$this->from]);
		$to = $connexion->createElement($this->to, $tableau[$this->to]);

		// Création de l'architecture
		$racine->appendChild($from);
		$racine->appendChild($to);

		return $this->corps->attributsConnexion($tableau, $racine, $connexion);
	}

	/**
	 * Methode permettant d'afficher tout un objet StructureMessage de façon ordonné
	 *
	 */
	public function toString() {
		echo $this->designation."\n";
		echo "\t".$this->from."\n";
		echo "\t".$this->to."\n";
		$this->corps->toString();
	}

	public function getDesignation() {
		return $this->designation;
	}

	public function getFrom() {
		return $this->from;
	}

	public function getTo() {
		return $this->to;
	}

		public function getCorps() {
		return $this->corps;
	}
}

?>

