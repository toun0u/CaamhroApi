<?php

class cata_champ_valeur extends dims_data_object {

    const TABLE_NAME = 'dims_mod_cata_champ_valeur';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function getValeur() {
		return $this->fields['valeur'];
	}

}
