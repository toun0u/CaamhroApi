<?php

class cata_famille_selection extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_familles_selections';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'famille_id', 'selection_id');
	}

}
