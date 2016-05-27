<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class group_user_role extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_group_user_role','id_group','id_user','id_role');
	}
}
?>
