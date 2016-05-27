<?php

class produit extends dims_data_object {

	public function __construct() {
		parent::dims_data_object('dims_mod_business_produit', 'id');
	}

	public function save() {
		if ($this->new) {
			$this->setugm();
		}
		return parent::save();
	}

}
