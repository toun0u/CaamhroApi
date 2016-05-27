<?
require_once DIMS_APP_PATH.'modules/system/class_workspace_user_role.php';

class workspace_user extends dims_data_object {
	const TABLE_NAME = 'dims_workspace_user';
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_workspace_user','id_workspace','id_user');
		$this->fields['adminlevel'] = dims_const::_DIMS_ID_LEVEL_USER;
	}

	function getroles() {
		$db = dims::getInstance()->getDb();

		$roles = array();

		$select =	"
				SELECT		dims_role.*,
						dims_module.label as modulelabel
				FROM		dims_role,
						dims_workspace_user_role,
						dims_module
				WHERE		dims_workspace_user_role.id_user = :iduser
				AND		dims_workspace_user_role.id_workspace = :idworkspace
				AND		dims_workspace_user_role.id_role = dims_role.id
				AND		dims_module.id = dims_role.id_module
				ORDER BY	dims_role.label
				";

		$result = $db->query($select, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
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
				DELETE FROM	dims_workspace_user_role
				WHERE		dims_workspace_user_role.id_user = :iduser
				AND		dims_workspace_user_role.id_workspace = :idworkspace
				";

		$res=$db->query($delete, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
		));

		foreach($roles as $key => $idrole)
		{
			$workspace_user_role = new workspace_user_role();
			$workspace_user_role->fields['id_user'] = $this->fields['id_user'];
			$workspace_user_role->fields['id_workspace'] = $this->fields['id_workspace'];
			$workspace_user_role->fields['id_role'] = $idrole;
			$workspace_user_role->save();
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
			$admin_userid = $this->fields['id_user'];
			$admin_workspaceid = $this->fields['id_workspace'];
			$admin_moduleid = $fields['id'];

			echo "<br><b>« {$fields['label']} »</b> ({$fields['moduletype']})<br>";
			if (file_exists(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_user_delete.php")) include(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_user_delete.php");
		}

		parent::delete();
	}

	function getConfigBlocks(&$lstmods) {
		$db = dims::getInstance()->getDb();

		$nbcolumns=$_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SITE]['site_blockcolumn_number'];

		$tabcolumns=array();

		$select =	"
					SELECT	bu.*,dims_module_type.label as labeltype
					FROM	dims_param_block_user bu
					inner	join dims_module on dims_module.id = bu.id_module
					inner	join dims_module_type on dims_module_type.id = dims_module.id_module_type
					WHERE	bu.id_workspace = :idworkspace
					AND	bu.id_user = :iduser
					order by bu.id_column, bu.position
					";

		$res=$db->query($select, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
		));
		$arraymodused=array();
		$datenow=dims_createtimestamp();

		if ($db->numrows($res)>0) {
			while ($fields = $db->fetchrow($res)) {

				if (isset($lstmods[$fields['labeltype']][$fields['id_module']])) {
					//if ($fields['id_module']==dims_const::_DIMS_MODULE_SYSTEM) $fields['id_module']="admin";
					$tabcolumns[$fields['id_column']][$fields['id_module']]=$lstmods[$fields['labeltype']][$fields['id_module']];
					$tabcolumns[$fields['id_column']][$fields['id_module']]['type']=$fields['labeltype'];
					$lstmods[$fields['labeltype']][$fields['id_module']]['blockused']=true;
					$tabcolumns[$fields['id_column']][$fields['id_module']]['state']=$fields['state'];
					$tabcolumns[$fields['id_column']][$fields['id_module']]['date_lastvalidate']=$fields['date_lastvalidate'];

					// update date_lastvalidate to auto validate
					if ($fields['date_lastvalidate']==0)
						$res=$db->query("UPDATE dims_param_block_user set date_lastvalidate= :datelast where id_workspace= :idworkspace and id_user= :iduser", array(
							':datelast' 	=> $datenow,
							':idworkspace' 	=> array('type' => PDO::PARAM_INT, 'value' => $fields['id_workspace']),
							':iduser' 		=> array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
						));

					array_push($arraymodused,$fields['id_module']);
				}
			}

		}
		//else
		//{
			require_once(DIMS_APP_PATH . '/modules/system/class_block_user.php');

			// define default configuration for current user
			$colcour=0;

			foreach($lstmods as $blocktype => $blockmod) {
				foreach($blockmod as $idmod => $mod) {
					if (!in_array($idmod,$arraymodused) && $idmod>=1) {
						if ($colcour==$nbcolumns) $colcour=0;

						$tabcolumns[$colcour][$idmod]=$mod;
						$tabcolumns[$colcour][$idmod]['state']=1;
						$lstmods[$blocktype][$idmod]['blockused']=true;

						$blockuser = new block_user();
						$blockuser->fields["id_user"]=$this->fields['id_user'];
						$blockuser->fields["id_module"]=$idmod;
						$blockuser->fields["id_workspace"]=$this->fields['id_workspace'];
						$blockuser->fields["id_column"]=$colcour;
						$blockuser->fields["position"]=sizeof($tabcolumns[$colcour]);
						$blockuser->fields["state"]=1;
						$blockuser->fields["date_lastvalidate"]=$datenow;  // valeur par defaut de validation des donnees km

						$blockuser->save();

						$colcour++;
					}
				}
			}

		//}
		return $tabcolumns;
	}
}
?>
