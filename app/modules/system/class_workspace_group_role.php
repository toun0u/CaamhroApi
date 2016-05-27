<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class workspace_group_role extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_workspace_group_role','id_workspace','id_group','id_role');
	}

}
?>
