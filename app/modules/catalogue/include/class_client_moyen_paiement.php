<?php

class client_moyen_paiement extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_client_moyen_paiement';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id_client', 'id_moyen_paiement');
	}

}
