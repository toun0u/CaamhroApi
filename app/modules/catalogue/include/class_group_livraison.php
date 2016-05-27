<?php
class cata_gr_liv extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_group_livraison';
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id_group');
	}
}
