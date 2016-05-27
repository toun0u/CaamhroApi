<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class param_choice extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_param_choice','id_module_type','name');
	}

}
?>
