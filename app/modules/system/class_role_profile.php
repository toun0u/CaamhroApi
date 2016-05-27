<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class role_profile extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_role_profile','id_role','id_profile');
	}

}
?>
