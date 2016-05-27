<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_role_action.php';

class rule extends dims_data_object {
	/**
	* Class constructor
	*
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_rule');
	}

	function save() {
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		$delete = "DELETE FROM dims_rule WHERE id = :idrule";
		$res=$db->query($delete, array(
			':idrule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		parent::delete();
	}

}
?>
