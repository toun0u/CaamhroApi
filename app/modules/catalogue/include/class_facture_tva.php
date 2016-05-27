<?php

class cata_facture_tva extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_facture_tva';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function getVATDescription() {
		require_once DIMS_APP_PATH.'modules/catalogue/include/class_tva.php';
		$tva = new tva();
		return $tva->find_by(array('id_tva' => $this->get('id_tva')), null, 1)->get('description');
	}

}
