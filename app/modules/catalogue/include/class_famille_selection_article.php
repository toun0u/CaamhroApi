<?php

class cata_famille_selection_article extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_familles_selections_articles';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'famille_id', 'selection_id', 'article_id');
	}

}
