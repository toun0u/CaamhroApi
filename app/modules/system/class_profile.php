<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_role_profile.php';

class profile extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_profile');
	}

	function save()
	{
		$roles = func_get_arg(0);

		$db = dims::getInstance()->getDb();

		parent::save();

		$delete = 	"
				DELETE FROM 	dims_role_profile
				WHERE		id_profile = :idprofile
				";

		$res=$db->query($delete, array(
			':idprofile' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		if (isset($roles)) {
			foreach($roles as $key => $id_roles) {
				if ($id_roles>0) {
					$role_profile = new role_profile();
					$role_profile->fields['id_role'] = $id_roles;
					$role_profile->fields['id_profile'] = $this->fields['id'];
					$role_profile->save();
				}
			}
		}
	}

	function delete()
	{
		$db = dims::getInstance()->getDb();

		$delete = "DELETE FROM dims_role_profile WHERE id_profile = :idprofile";;
		$res=$db->query($delete, array(
			':idprofile' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
		));

		parent::delete();
	}


	function getroles()
	{
		$db = dims::getInstance()->getDb();

		$roles = array();

		$select = 	"
				SELECT 		dims_role_profile.*
				FROM 		dims_role_profile
				WHERE 		dims_role_profile.id_profile = :idprofile
				";

		$result = $db->query($select, array(
			':idprofile' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($role = $db->fetchrow($result))
		{
			$roles[$role['id_role']] = $role['id_role'];
		}

		return $roles;
	}

	function getactions(&$actions)
	{
		$db = dims::getInstance()->getDb();

		$select = 	"
				SELECT		dims_role_action.id_action,
							dims_role.id_module,dims_role.id_workspace
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON			dims_role.id = dims_role_action.id_role
				INNER JOIN	dims_role_profile
				ON			dims_role_profile.id_role = dims_role.id
				AND			dims_role_profile.id_profile = :idprofile
				";

		$result = $db->query($select, array(
			':idprofile' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($fields = $db->fetchrow($result)) {
			$actions[$this->fields['id_workspace']][$fields['id_module']][$fields['id_action']] = true;
		}

		//return $actions;
	}

	public function updateDefault(){
		if ($this->fields['def'] == 1){
			$db = dims::getInstance()->getDb();
			$update = "UPDATE	dims_profile
					   SET		def = 0
					   WHERE	id_workspace = :idworkspace
					   AND		id != :idprofile";
			$db->query($update, array(
				':idprofile' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
			));
		}
	}
}
?>
