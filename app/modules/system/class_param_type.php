<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_param_choice.php';

class param_type extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_param_type', 'id_module_type', 'name');
	}

	function delete($preserve_data = false)
	{
		$db = dims::getInstance()->getDb();

		$delete = "DELETE FROM dims_param_choice WHERE id_module_type = :idmoduletype AND name = :name";
		$res=$db->query($delete, array(
			':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['id_module_type']),
			':name' 		=> array('type' => PDO::PARAM_STR, 'value' => $this->fields['name']),
		));

		if (!$preserve_data)
		{
			$delete = "DELETE FROM dims_param_default WHERE id_module_type = :idmoduletype AND name = :name";
			$res=$db->query($delete, array(
			':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['id_module_type']),
			':name' 		=> array('type' => PDO::PARAM_STR, 'value' => $this->fields['name']),
		));

			$delete = "DELETE FROM dims_param_group WHERE id_module_type = :idmoduletype AND name = :name";
			$res=$db->query($delete, array(
			':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['id_module_type']),
			':name' 		=> array('type' => PDO::PARAM_STR, 'value' => $this->fields['name']),
		));

			$delete = "DELETE FROM dims_param_user WHERE id_module_type = :idmoduletype AND name = :name";
			$res=$db->query($delete, array(
			':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['id_module_type']),
			':name' 		=> array('type' => PDO::PARAM_STR, 'value' => $this->fields['name']),
		));
		}

		parent::delete();
	}

	function getallchoices($id = 0)
	{
		$db = dims::getInstance()->getDb();
		$param_choice = array();

		if ($id != 0) $id_param_type = $id;
		else $id_param_type = $this->fields['id'];

		$select = "SELECT * FROM dims_param_choice WHERE id_param_type = :idparamtype";
		$res=$db->query($select, array(
			':idparamtype' => array('type' => PDO::PARAM_INT, 'value' => $id_param_type),
		));
		while ($fields = $db->fetchrow($res))
		{
			$param_choice[$fields['value']] = $fields['displayed_value'];
		}

		return($param_choice);
	}
}
?>
