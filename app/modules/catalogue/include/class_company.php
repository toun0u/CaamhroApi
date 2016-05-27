<?php

class cata_company extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_companies';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

}
