<?
require_once DIMS_APP_PATH.'modules/system/class_workspace_group_role.php';

class workspace_group extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_workspace_group','id_workspace','id_group');
		$this->fields['adminlevel'] = dims_const::_DIMS_ID_LEVEL_USER;
	}

	function getroles()
	{
		$db = dims::getInstance()->getDb();

		$roles = array();

		$select =	"
					SELECT		dims_role.*,
								dims_module.label as modulelabel
					FROM		dims_role,
								dims_workspace_group_role,
								dims_module
					WHERE		dims_workspace_group_role.id_group = :idgroup
					AND			dims_workspace_group_role.id_workspace = :idworkspace
					AND			dims_workspace_group_role.id_role = dims_role.id
					AND			dims_module.id = dims_role.id_module
					ORDER BY	dims_role.label
					";

		$result = $db->query($select, array(
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
		));

		while ($role = $db->fetchrow($result))
		{
			$roles[$role['id']] = $role;
		}

		return $roles;
	}

	function saveroles($roles)
	{
		$db = dims::getInstance()->getDb();

		$delete =	"
				DELETE FROM	dims_workspace_group_role
				WHERE		dims_workspace_group_role.id_group = :idgroup
				AND		dims_workspace_group_role.id_workspace = :idworkspace
				";

		$res=$db->query($delete, array(
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
		));

		foreach($roles as $key => $idrole)
		{
			$workspace_group_role = new workspace_group_role();
			$workspace_group_role->fields['id_group'] = $this->fields['id_group'];
			$workspace_group_role->fields['id_workspace'] = $this->fields['id_workspace'];
			$workspace_group_role->fields['id_role'] = $idrole;
			$workspace_group_role->save();
		}
	}

	function delete()
	{
		$db = dims::getInstance()->getDb();

		// search for modules
		$select =	"
					SELECT	m.id, m.label, mt.label as moduletype
					FROM	dims_module_workspace mw,
						dims_module m,
						dims_module_type mt
					WHERE	mw.id_workspace = :idworkspace
					AND	mw.id_module = m.id
					AND	m.id_module_type = mt.id
					";

		$res=$db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
		));
		while ($fields = $db->fetchrow($res))
		{
			$admin_groupid = $this->fields['id_group'];
			$admin_workspaceid = $this->fields['id_workspace'];
			$admin_moduleid = $fields['id'];

			echo "<br><b> {$fields['label']} </b> ({$fields['moduletype']})<br>";
			if (file_exists(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_org_delete.php")) include(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_org_delete.php");
		}
		parent::delete();
	}
}

?>
