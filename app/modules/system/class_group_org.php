<?
require_once DIMS_APP_PATH.'modules/system/class_group_org_role.php';

class group_org extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_group_org','id_group','id_org');
		$this->fields['adminlevel'] = dims_const::_DIMS_ID_LEVEL_USER;
	}

	function getroles() {
		$db = dims::getInstance()->getDb();

		$roles = array();

		$select =	"
					SELECT		dims_role.*,
								dims_module.label as modulelabel
					FROM		dims_role,
								dims_group_org_role,
								dims_module
					WHERE		dims_group_org_role.id_org = :idorg
					AND			dims_group_org_role.id_group = :idgroup
					AND			dims_group_org_role.id_role = dims_role.id
					AND			dims_module.id = dims_role.id_module
					ORDER BY	dims_role.label
					";

		$result = $db->query($select, array(
			':idorg' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_org']),
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']),
		));

		while ($role = $db->fetchrow($result))
		{
			$roles[$role['id']] = $role;
		}

		return $roles;
	}

	function saveroles($roles) {
		$db = dims::getInstance()->getDb();

		$delete =	"
				DELETE FROM	dims_group_org_role
				WHERE			dims_group_org_role.id_org = :idorg
				AND				dims_group_org_role.id_group = :idgroup
				";

		$res=$db->query($delete, array(
			':idorg' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_org']),
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']),
		));

		foreach($roles as $key => $idrole)
		{
			$group_org_role = new group_org_role();
			$group_org_role->fields['id_org'] = $this->fields['id_org'];
			$group_org_role->fields['id_group'] = $this->fields['id_group'];
			$group_org_role->fields['id_role'] = $idrole;
			$group_org_role->save();
		}
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		// search for modules
		$select =	"
					SELECT	m.id, m.label, mt.label as moduletype
					FROM	dims_module_group mg,
							dims_module m,
							dims_module_type mt
					WHERE	mg.id_group = :idgroup
					AND		mg.id_module = m.id
					AND		m.id_module_type = mt.id
					";

		$res=$db->query($select, array(
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']),
		));
		while ($fields = $db->fetchrow($res)) {
			$admin_orgid = $this->fields['id_org'];
			$admin_groupid = $this->fields['id_group'];
			$admin_moduleid = $fields['id'];

			echo "<br><b> {$fields['label']} </b> ({$fields['moduletype']})<br>";
			if (file_exists(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_org_delete.php")) include(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_org_delete.php");
		}

		parent::delete();
	}
}
?>
