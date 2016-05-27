<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class workspace_user_role extends dims_data_object
{

	/**
	* Class constructor
	*
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_workspace_user_role','id_user','id_workspace','id_role');
	}

}
?>
