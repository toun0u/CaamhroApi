<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class group_org_role extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_group_org_role','id_group','id_org','id_role');
	}

}
?>
