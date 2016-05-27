<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class user_planning extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_mod_business_user_planning','id_user','id_user_sel');
	}

}
?>
