<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class module_workspace extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_module_workspace','id_workspace','id_module');
	}


	function save($id_object="",$execute_sql=true) {
		$db = dims::getInstance()->getDb();

		if ($this->new) {
			$select =	"
					SELECT MAX(dims_module_workspace.position) AS position
					FROM dims_module_workspace
					WHERE dims_module_workspace.id_workspace = :idworkspace";;

			$result = $db->query($select, array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
			));
			$fields = $db->fetchrow($result);
			$this->fields['position'] = $fields['position'] + 1;
		}

		parent::save();
	}

	function delete($id_object="") {
		$db = dims::getInstance()->getDb();

		$workspaceid = $this->fields['id_workspace'];
		$position = $this->fields['position'];
		if ($position>0) {
			$update = "UPDATE dims_module_workspace set position=position-1 where id_workspace = :idworkspace and position> :position";
		}
		$res=$db->query($update, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $workspaceid),
			':position' => array('type' => PDO::PARAM_INT, 'value' => $position),
		));;

		parent::delete();
	}

	function changeposition($direction) {

		$db = dims::getInstance()->getDb();

		$workspaceid = $this->fields['id_workspace'];

		$select =	"
				SELECT	min(position) as minpos,
					max(position) as maxpos
				FROM	dims_module_workspace
				WHERE	id_workspace = :idworkspace
				";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $workspaceid),
		));
		$fields = $db->fetchrow($result);
		$minpos = $fields['minpos'];
		$maxpos = $fields['maxpos'];
		$position = $this->fields['position'];
		$move = 0;

		if ($direction=='down' && $position != $maxpos)
		{
			$move = 1;
		}

		if ($direction=='up' && $position != $minpos)
		{
			$move = -1;
		}

		if ($move!=0)
		{
			$update = "UPDATE dims_module_workspace set position=0 where id_workspace = :idworkspace and position= :position";
			$res=$db->query($update, array(
				':position' => array('type' => PDO::PARAM_INT, 'value' => $position+$move),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $workspaceid),
			));
			$update = "UPDATE dims_module_workspace set position= :newposition where id_workspace = :idworkspace and position= :oldposition";
			$res=$db->query($update, array(
				':newposition' => array('type' => PDO::PARAM_INT, 'value' => $position+$move),
				':oldposition' => array('type' => PDO::PARAM_INT, 'value' => $position),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $workspaceid),
			));
			$update = "UPDATE dims_module_workspace set position= :position where id_workspace = :idworkspace and position=0";
			$res=$db->query($update, array(
				':position' => array('type' => PDO::PARAM_INT, 'value' => $position),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $workspaceid),
			));
		}
	}

	 function getroles()
	 {
		$db = dims::getInstance()->getDb();
		$workspace = new workspace();
		$workspace->open($this->fields['id_workspace']);
		$parents = str_replace(';',',',$workspace->fields['parents']);

		$roles = array();


		// select own roles and shared herited roles
		$select =	"
				SELECT		dims_role.*,
						dims_workspace.label as labelworkspace
				FROM		dims_role,
						dims_workspace
				WHERE		dims_role.id_module = :idmodule
				AND		(dims_role.id_workspace = :idworkspace
				OR		(dims_role.shared = 1))
				AND		dims_role.id_workspace = dims_workspace.id
				ORDER BY	dims_role.label
				";

		$result = $db->query($select, array(
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module']),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
		));

		while ($role = $db->fetchrow($result)) {
			$roles[$role['id']] = $role;
		}

		return $roles;
	 }

}
?>
