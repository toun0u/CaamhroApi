<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_param_type.php';
require_once DIMS_APP_PATH.'modules/system/class_module.php';
require_once DIMS_APP_PATH.'modules/system/class_mb_action.php';

class module_type extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_module_type');
	}

	function delete($id_object="") {
		$db = dims::getInstance()->getDb();
		// delete params

		if ($this->fields['id']!=-1) {
			$select = "SELECT * FROM dims_param_type WHERE id_module_type = :idmoduletype";
			$answer = $db->query($select, array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			while ($deletefields = $db->fetchrow($answer))
			{
				$param_type = new param_type();
				$param_type->open($this->fields['id'], $deletefields['name']);
				$param_type->delete();
			}

			// delete modules

			$select = "SELECT * FROM dims_module WHERE id_module_type = :idmoduletype";
			$answer = $db->query($select, array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			while ($deletefields = $db->fetchrow($answer))
			{
				$module = new module();
				$module->open($deletefields['id']);
				$module->delete();
			}

			// delete actions

			$select = "SELECT * FROM dims_mb_action WHERE id_module_type = :idmoduletype";
			$answer = $db->query($select, array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			while ($deletefields = $db->fetchrow($answer))
			{
				$mb_action = new mb_action();
				$mb_action->open($this->fields['id'],$deletefields['id_action']);
				$mb_action->delete();
			}

			/*$res=$db->query("DELETE FROM dims_mb_field WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));*/
			$res=$db->query("DELETE FROM dims_mb_relation WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$res=$db->query("DELETE FROM dims_mb_schema WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			/*$res=$db->query("DELETE FROM dims_mb_table WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));*/
			$res=$db->query("DELETE FROM dims_mb_wce_object WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
		}

		parent::delete();
	}


	function delete_params() {
		// used for updating module

		$db = dims::getInstance()->getDb();

		if ($this->fields['id']!=-1) {
			// delete params
			$select = "SELECT * FROM dims_param_type WHERE id_module_type = :idmoduletype";
			$answer = $db->query($select, array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			while ($deletefields = $db->fetchrow($answer)) {
				$param_type = new param_type();
				$param_type->open($this->fields['id'], $deletefields['name']);
				$param_type->delete(true);
			}

			// delete actions
			$select = "SELECT * FROM dims_mb_action WHERE id_module_type = :idmoduletype";
			$answer = $db->query($select, array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			while ($deletefields = $db->fetchrow($answer)) {
				$mb_action = new mb_action();
				$mb_action->open($this->fields['id'],$deletefields['id_action']);
				$mb_action->delete(true);
			}

			$res=$db->query("DELETE FROM dims_mb_field WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$res=$db->query("DELETE FROM dims_mb_relation WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$res=$db->query("DELETE FROM dims_mb_schema WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$res=$db->query("DELETE FROM dims_mb_table WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$res=$db->query("DELETE FROM dims_mb_wce_object WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$res=$db->query("DELETE FROM dims_mb_object WHERE id_module_type = :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
		}
	}

	function createinstance($workspaceid) {
		$position = 0;

		$module = new module();
		$module->fields['label'] = 'Nouveau_module_' . $this->fields['label'];
		$module->fields['id_module_type'] = $this->fields['id'];
		$module->fields['id_workspace'] = $workspaceid;
		$module->fields['active'] = '1';
		$module->fields['public'] = '0';
		$module->fields['shared'] = '0';

		return($module);
	}


	 function getactions() {
		$db = dims::getInstance()->getDb();

		$actions = array();

		$select =	"
				SELECT		*
				FROM		dims_mb_action
				WHERE		id_module_type = :idmoduletype
				ORDER BY	id_action
				";

		$result = $db->query($select, array(
			':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($action = $db->fetchrow($result)) {
			$actions[$action['id_action']] = $action;
		}

		return $actions;
	 }

	public function getByLabel($label='') {
		if ($label != '') {
			$rs = $this->db->query('SELECT * FROM '.$this->tablename.' WHERE label = :label LIMIT 0, 1', array(
				':label' => array('type' => PDO::PARAM_STR, 'value' => $label),
			));
			if ($this->db->numrows($rs)) {
				$this->openFromResultSet($this->db->fetchrow($rs));
			}
			return $this->db->numrows($rs);
		}
	}
}
?>
