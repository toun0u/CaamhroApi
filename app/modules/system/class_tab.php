<?php
class Tab {

	/**
	 * int - id pour différencier les onglets ayant potentiellement le même link et label
	 */
	public $id;

	/**
	 * String - Lien qui sera situé dans le href
	 */
	public $link;

	/**
	 * String - Label écrit sur l'onglet
	 */
	public $label;

	/**
	 * Int - Etat de l'onglet : 1 = actif | 0 = inactif
	 */
	public $state;

	public function __construct($link, $label, $state = 0) {
		$this->update($link, $label, $state);
	}

	/**
	 * Permet de faire un update de tous les attributs en une fois
	 */
	public function update($link, $label, $state = 0) {
		$this->link = $link;
		$this->label = $label;
		$this->state = $state;
	}

	/**
	 * Permet de changer la valeur d'un attribut
	 */
	public function set($attr, $val) {
		if(property_exists($this, $attr)) {
			$this->$attr = $val;
		}
		else {
			throw new Exception('Attribut "<b>'.$attr.'</b>" inconnu dans la class "<b>'.get_class($this).'</b>"');
		}
	}

}