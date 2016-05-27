<?php

class cata_promotion_article extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_promotion_article';

	public function __construct() {
		parent::dims_data_object('dims_mod_cata_promotion_article', 'id_promo', 'ref_article');
	}

}
