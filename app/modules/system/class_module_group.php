<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class module_group extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_module_group','id_group','id_module');
	}

	function save()	{
		$db = dims::getInstance()->getDb();

		if ($this->new) {
			$select =	"
					SELECT MAX(dims_module_group.position) AS position
					FROM dims_module_group
					WHERE dims_module_group.id_group = :idgroup ";

			$result = $db->query($select, array(
				':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']),
			));
			$fields = $db->fetchrow($result);
			$this->fields['position'] = $fields['position'] + 1;
		}

		parent::save();
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		$groupid = $this->fields['id_group'];
		$position = $this->fields['position'];

		$update = "UPDATE dims_module_group set position=position-1 where id_group = :idgroup and position> :position";
		$res=$db->query($update, array(
				':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
				':position' => array('type' => PDO::PARAM_INT, 'value' => $position),
			));

		parent::delete();
	}

	function changeposition($direction) {

		$db = dims::getInstance()->getDb();

		$groupid = $this->fields['id_group'];

		$select =	"
				SELECT	min(position) as minpos,
					max(position) as maxpos
				FROM	dims_module_group
				WHERE	id_group = :idgroup
				";

		$result = $db->query($select, array(
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $groupid),
		));
		$fields = $db->fetchrow($result);
		$minpos = $fields['minpos'];
		$maxpos = $fields['maxpos'];
		$position = $this->fields['position'];
		$move = 0;

		if ($direction=='down' && $position != $maxpos) {
			$move = 1;
		}

		if ($direction=='up' && $position != $minpos) {
			$move = -1;
		}

		if ($move!=0) {
			$update = "UPDATE dims_module_group set position=0 where id_group = :idgroup and position= :position";
			$res=$db->query($update, array(
				':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $groupid),
				':position' => array('type' => PDO::PARAM_INT, 'value' => $position+$move),
			));
			$update = "UPDATE dims_module_group set position= :newposition where id_group = :idgroup and position= :oldposition";
			$res=$db->query($update, array(
				':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $groupid),
				':newposition' => array('type' => PDO::PARAM_INT, 'value' => $position+$move),
				':oldposition' => array('type' => PDO::PARAM_INT, 'value' => $position),
			));
			$update = "UPDATE dims_module_group set position= :position where id_group = :idgroup and position=0";
			$res=$db->query($update, array(
				':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $groupid),
				':position' => array('type' => PDO::PARAM_INT, 'value' => $position+$move),
			));
		}
	}


	 function getroles() {
		$db = dims::getInstance()->getDb();

		$group = new group();
		$group->open($this->fields['id_group']);
		$parents = str_replace(';',',',$group->fields['parents']);

		$roles = array();
		$params = array();

		// select own roles and shared herited roles
		$select =	"
				SELECT		dims_role.*,
						dims_group.label as labelgroup
				FROM		dims_role,
						dims_group
				WHERE		dims_role.id_module = :idmodule
				AND		(dims_role.id_group = :idgroup
				OR		(dims_role.id_group IN (".$db->getParamsFromArray(explode(',', $parents), 'idgroup', $params)." AND dims_role.shared = 1))
				AND		dims_role.id_group = dims_group.id
				ORDER BY	dims_role.label
				";
		$params[':idgroup'] = array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']);
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module']);

		$result = $db->query($select, $params);

		while ($role = $db->fetchrow($result)) {
			$roles[$role['id']] = $role;
		}

		return $roles;
	 }
}
?>
