<?php

class cata_market_restriction extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_market_restrictions';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id_market', 'id_article');
	}

}
