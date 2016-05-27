<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class mb_relation extends dims_data_object {
	/**
	* Class constructor
	*
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mb_relation','tablesrc','fieldsrc','tabledest','fielddest','id_module_type');
	}
}
?>
