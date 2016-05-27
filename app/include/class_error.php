<?php
require_once(DIMS_APP_PATH.'include/class_dims_data_object.php');

class dims_error extends dims_data_object {
	/**
	* Class constructor
	*
	* @access public
	**/
	function dims_error() {
		parent::dims_data_object('dims_error','id');
	}

}
?>
