<?php
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");

class etap_file extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_event_etap_file','id');
	}

	function save() {
		global $db;
		return(parent::save());
	}

	function delete() {
		global $db;

		if($this->fields['id_doc'] != 0) {
			$etapfile = new docfile();

			$etapfile->open($this->fields['id_doc']);

			$etapfile->delete();
		}
		return(parent::delete());
	}
}
?>
