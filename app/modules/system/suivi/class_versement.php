<?php
class versement extends dims_data_object {

	const TABLE_NAME = 'dims_mod_business_versement';
	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function save() {
		$this->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		parent::save();
	}

}
