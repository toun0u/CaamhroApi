<?php

class cata_stock extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_stocks';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

}
