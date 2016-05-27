<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_role_action.php';

class role extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_role');
	}

	function save() { // $actions, $id_module_type=0
		$db = dims::getInstance()->getDb();
		$actions=array();
		$numargs = func_num_args();
		for ($i = 0; $i < $numargs; $i++) {
			if ($i==0) $actions=func_get_arg($i);
			else $id_module_type=func_get_arg($i);
		}
		parent::save();

		$delete =	"
				DELETE FROM	dims_role_action
				WHERE		id_role = :idrole
				";

		$res=$db->query($delete, array(
			':idrole' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		foreach($actions as $key => $id_action) {
			$role_action = new role_action();
			$role_action->fields['id_role'] = $this->fields['id'];
			$role_action->fields['id_action'] = $id_action;
			$role_action->fields['id_module_type'] = $id_module_type;
			$role_action->save();
		}
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		$delete = "DELETE FROM dims_role_action WHERE id_role = :idrole";
		$res=$db->query($delete, array(
			':idrole' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		parent::delete();
	}

	function getactions() {
		$db = dims::getInstance()->getDb();

		$actions = array();

		if (isset($this->fields['id']) && $this->fields['id']>0) {
			$select =	"
					SELECT		dims_mb_action.*,
								dims_role_action.id_action

					FROM		dims_role_action

					LEFT JOIN		dims_mb_action
					ON			dims_role_action.id_action = dims_mb_action.id_action
					AND			dims_role_action.id_module_type = dims_mb_action.id_module_type

					WHERE		dims_role_action.id_role = :idrole

					ORDER BY	dims_mb_action.label
					";

			$result = $db->query($select, array(
				':idrole' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));

			while ($action = $db->fetchrow($result)) {
				$actions[$action['id_action']] = $action;
			}
		}
		return $actions;
	}

}
?>
