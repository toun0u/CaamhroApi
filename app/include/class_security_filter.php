<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class dims_security_filter extends dims_data_object {

	/**
	* Class constructor
	*
	* @access public
	**/
	function  dims_security_filter() {
		parent::dims_data_object('dims_security_filter','id');
	}

	function save() {
		parent::save();
	}
}
?>
