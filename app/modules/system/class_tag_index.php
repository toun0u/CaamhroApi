<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class tag_index extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_tag_index','id');
	}

}
?>
