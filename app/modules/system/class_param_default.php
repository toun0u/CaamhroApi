<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class param_default extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_param_default','id_module','name');
	}
}
?>
