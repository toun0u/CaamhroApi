<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_module_workspace.php';
require_once DIMS_APP_PATH.'modules/system/class_module_type.php';

class module extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_module','id');
	}

	function save($id_object="",$execute_sql=true) {
		$db = dims::getInstance()->getDb();

		$res = -1;

		if ($this->new) {
			$res = parent::save();

			// insert default parameters
			$insert = "INSERT INTO dims_param_default SELECT ".$this->fields['id'].", name, default_value, id_module_type,0 FROM dims_param_type WHERE id_module_type = :idmoduletype";
			$res=$db->query($insert, array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));

			// todo when new module
			$select = "SELECT * FROM dims_module_type WHERE dims_module_type.id = :idmoduletype";
			$answer = $db->query($select, array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
			));
			$fields = $db->fetchrow($answer);

			$admin_moduleid = $this->fields['id'];
			// script to execute to create specific module data
			if (file_exists(DIMS_APP_PATH . "/modules/".$fields['label']."/include/create.php")) include(DIMS_APP_PATH . "/modules/".$fields['label']."/include/create.php");
			elseif (file_exists(DIMS_APP_PATH . "/modules/".$fields['label']."/include/admin_instance_create.php")) include(DIMS_APP_PATH . "/modules/".$fields['label']."/include/admin_instance_create.php");
		}
		else $res = parent::save();

		return($res);
	}

	function delete($id_object="") {
		$db = dims::getInstance()->getDb();

		if ($this->fields['id']!=-1) {
			// delete specific data of the module (call to delete function of the module)
			$select = "SELECT dims_module_type.label FROM dims_module_type, dims_module WHERE dims_module_type.id = dims_module.id_module_type AND dims_module.id = :idmodule";
			$answer = $db->query($select, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			if ($fields = $db->fetchrow($answer)) {
				$admin_moduleid = $this->fields['id'];
				// script to execute to create specific module data
				if (file_exists(DIMS_APP_PATH . "/modules/".$fields['label']."/include/delete.php")) include(DIMS_APP_PATH . "/modules/".$fields['label']."/include/delete.php");
				elseif (file_exists(DIMS_APP_PATH . "/modules/".$fields['label']."/include/admin_instance_delete.php")) include(DIMS_APP_PATH . "/modules/".$fields['label']."/include/admin_instance_delete.php");
			}

			// delete all module_workspace
			$groups = $this->getallgroups();

			foreach($groups as $idgroup => $group) {
				$module_workspace  = new module_workspace();
				$module_workspace->open($idgroup,$this->fields['id']);
				$module_workspace->delete();
			}

			// delete params (default & user)

			$delete = "DELETE FROM dims_param_default WHERE id_module = :idmodule";
			$res=$db->query($delete, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));

			$delete = "DELETE FROM dims_param_user WHERE id_module = :idmodule";
			$res=$db->query($delete, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
		}

		parent::delete();

	}

	function getallgroups() {
		$db = dims::getInstance()->getDb();

		$groups = array();

		$select =	"
				SELECT	*
				FROM	dims_module_workspace
				WHERE	id_module = :idmodule
				";

		$result = $db->query($select, array(
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($fields = $db->fetchrow($result)) {
			$groups[$fields['id_workspace']] = $fields;
		}

		return($groups);
	}

	function getroles() {
		$db = dims::getInstance()->getDb();

		$roles = array();

		$select =	"
				SELECT		dims_role.*
				FROM		dims_role
				WHERE		dims_role.id_module = :idmodule
				ORDER BY	dims_role.label
				";

		$result = $db->query($select, array(
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($role = $db->fetchrow($result)) {
			$roles[$role['id']] = $role;
		}

		return $roles;
	}

	function getrolesshared() {
		$db = dims::getInstance()->getDb();

		$roles = array();

		$select =	"
				SELECT		dims_role.*
				FROM		dims_role
				WHERE		dims_role.id_module = :idmodule
				AND		dims_role.shared=1
				ORDER BY	dims_role.label
				";

		$result = $db->query($select, array(
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($role = $db->fetchrow($result)) {
			$roles[$role['id']] = $role;
		}

		return $roles;
	}


	function unlink($idgroup) {
		$db = dims::getInstance()->getDb();

		$delete =	"
				DELETE FROM	dims_module_workspace
				WHERE		dims_module_workspace.id_workspace = :idworkspace
				AND		dims_module_workspace.id_module = :idmodule
				";

		$res=$db->query($delete, array(
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $idgroup),
		));
	}


	function verifyShares() {
		require_once DIMS_APP_PATH . '/include/functions/shares.php';
		$lstrules=dims_shares_get(-1,dims_const::_SYSTEM_OBJECT_GROUP,$this->fields['id'],dims_const::_DIMS_MODULE_SYSTEM);

		if ($this->fields['shared'] &&	sizeof($lstrules)>0) {
			// on switch sur un partage global
			$this->fields['viewmode']=dims_const::_DIMS_VIEWMODE_GLOBAL;
			$this->save();
		}
	}

	function getInformation() {
		$db = dims::getInstance()->getDb();
		$tabinfo=array();

		$tabinfo[$_DIMS['cste']['_DIMS_LABEL_NAME']]=$this->fields['label'];


		// recherche du nom de l'espace qui le partage
		$select =	"
				SELECT		dims_workspace.label
				FROM		dims_workspace
				WHERE		id = :idworkspace
				";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
		));

		if ($fmod = $db->fetchrow($result)) {
			if($this->fields['id_workspace']==$_SESSION['dims']['workspaceid'])
				$tabinfo[$_DIMS['cste']['_WORKSPACE']]=$fmod['label'];
			else $tabinfo[$_DIMS['cste']['_WORKSPACE']]="partage par (<i>".$fmod['label']."</i>)";
		}


		$dims_viewmodes = array(	dims_const::_DIMS_VIEWMODE_UNDEFINED	=> $_DIMS['cste']['_DIMS_LABEL_UNDEFINED'],
							dims_const::_DIMS_VIEWMODE_PRIVATE		=> $_DIMS['cste']['_DIMS_LABEL_VIEWMODE_PRIVATE'],
							dims_const::_DIMS_VIEWMODE_DESC			=> $_DIMS['cste']['_LABEL_VIEWMODE_DESC'],
							dims_const::_DIMS_VIEWMODE_ASC			=> $_DIMS['cste']['_LABEL_VIEWMODE_ASC'],
							dims_const::_DIMS_VIEWMODE_GLOBAL		=> $_DIMS['cste']['_LABEL_VIEWMODE_GLOBAL']
						);

		// on recalcule la vue sur l'information
		$currentview=$dims_viewmodes[$this->fields['viewmode']];
		$tabinfo[$_DIMS['cste']['_DIMS_LABEL_VIEWMODE']]=$currentview;

		$tabinfo[$_DIMS['cste']['_DIMS_LABEL_ACTIVE']]=$this->fields['active'];

		$roles = array();

		$select =	"
				SELECT	dims_module_workspace.visible
				FROM	dims_module_workspace
				WHERE	id_module = :idmodule
				AND		id_workspace = :idworkspace
				";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));

		if ($fmod = $db->fetchrow($result)) {
			$tabinfo[$_DIMS['cste']['_DIMS_LABEL_VISIBLE']]=($fmod['visible']) ? $_DIMS['cste']['_DIMS_YES'] : $_DIMS['cste']['_DIMS_NO'];
		}


		$tabinfo[$_DIMS['cste']['_WORKSPACE']]=($this->fields['shared']) ? $_DIMS['cste']['_DIMS_YES'] : $_DIMS['cste']['_DIMS_NO'];
		$tabinfo[$_DIMS['cste']['_DIMS_LABEL_HERITED']]=($this->fields['herited']) ? $_DIMS['cste']['_DIMS_YES'] : $_DIMS['cste']['_DIMS_NO'];
		return $tabinfo;
	}

	function getLabels($idmodule=-1) {
		$db = dims::getInstance()->getDb();

		$label=array();
		$label['label']="";
		$label['moduletype']="";

		$sql ="	select		dims_module.label,dims_module_type.label as moduletype
				from		dims_module_type
				inner join	dims_module
				on			dims_module.id_module_type=dims_module_type.id
				and			dims_module.id=";

		if ($idmodule!=-1) $sql.=$idmodule;
		else $sql.=$this->fields['id'];

		$res=$db->query($sql);

		if ($db->numrows($res)>0) {
			if ($row=$db->fetchrow($res)) {
				$label['label']=$row['label'];
				$label['moduletype']=$row['moduletype'];
			}
		}

		return $label;
	}

	public function getUsersFromActions($actionid,$workspaceid=0) {
		$db = dims::getInstance()->getDb();
	global $dims;

	$usrlist=array();
		// recuperation de ts les espaces de travail
	if ($workspaceid==0) {
			$workspaces = $dims->getListWorkspaces();
		}
	else {
			$workspaces = $workspaceid;
		}

		$params = array();
		$select =	"
				SELECT		distinct dims_workspace_user_role.id_workspace,
							dims_workspace_user_role.id_user
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON			dims_role.id = dims_role_action.id_role
				AND			dims_role_action.id_action=:idaction
				INNER JOIN	dims_workspace_user_role
				ON			dims_workspace_user_role.id_role = dims_role.id
				AND			dims_workspace_user_role.id_workspace in (".$db->getParamsFromArray(explode(',', $workspaces), 'idworkspace', $params).")
				INNER JOIN	dims_user
				ON			dims_user.id=dims_workspace_user_role.id_user";
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $actionid);

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$usrlist[$fields['id_workspace']][$fields['id_user']] = $fields['id_user'];
		}

		$params = array();
		// remontee des users concernant le profil de l'utilisateur rattache
		// traitement du user avec profil
		$select =	"
				SELECT		distinct dims_workspace_user.id_workspace,
							dims_workspace_user.id_user
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON			dims_role.id = dims_role_action.id_role
				AND			dims_role_action.id_action=:idaction
				INNER JOIN	dims_role_profile
				ON			dims_role_profile.id_role = dims_role.id
				INNER JOIN	dims_workspace_user
				ON			dims_workspace_user.id_profile = dims_role_profile.id_profile
				AND			dims_workspace_user.id_workspace in (".$db->getParamsFromArray(explode(',', $workspaces), 'idworkspace', $params).")
				INNER JOIN	dims_user
				ON			dims_user.id=dims_workspace_user.id_user";
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $actionid);

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$usrlist[$fields['id_workspace']][$fields['id_user']] = $fields['id_user'];
		}

		$params = array();
		// traitement des rattachements du user ? l'aide de groupes  : 2 pos. soit action avec role ou profil
		//traitement du group avec role
		$select =	"
				SELECT		distinct dims_workspace_group_role.id_workspace,
							dims_group_user.id_user
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON			dims_role.id = dims_role_action.id_role
				AND			dims_role_action.id_action=:idaction
				INNER JOIN	dims_workspace_group_role
				ON			dims_workspace_group_role.id_role = dims_role.id
				AND			dims_workspace_group_role.id_workspace in (".$db->getParamsFromArray(explode(',', $workspaces), 'idworkspace', $params).")
				INNER JOIN	dims_group_user
				ON			dims_group_user.id_group = dims_workspace_group_role.id_group
				INNER JOIN	dims_user
				ON			dims_user.id=dims_group_user.id_user";
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $actionid);

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$usrlist[$fields['id_workspace']][$fields['id_user']] = $fields['id_user'];
		}

		$params = array();
		// traitement du group avec profil
		$select =	"
				SELECT		distinct dims_workspace_group.id_workspace,
							dims_group_user.id_user
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON			dims_role.id = dims_role_action.id_role
				AND			dims_role_action.id_action=:idaction
				INNER JOIN	dims_role_profile
				ON			dims_role_profile.id_role = dims_role.id
				INNER JOIN	dims_workspace_group
				ON			dims_workspace_group.id_profile = dims_role_profile.id_profile
				AND			dims_workspace_group.id_workspace in (".$db->getParamsFromArray(explode(',', $workspaces), 'idworkspace', $params).")
				INNER JOIN	dims_group_user
				ON			dims_group_user.id_group = dims_workspace_group.id_group
				INNER JOIN	dims_user
				ON			dims_user.id=dims_group_user.id_user";
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $actionid);

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$usrlist[$fields['id_workspace']][$fields['id_user']] = $fields['id_user'];
		}

		return $usrlist;
	}

	/*
	 * Cyril <-| 04/08/2011 |-> fonction permettant de récupérer toutes les catégories utilisables dans le module courant
	 * @return : talbeau les systèmes de catégories sans leur descendance
	 * @param : $user -> l'utilisateur courant
	 * @param : $o -> objet éventuel sur lequel devrait porter la recherche
	 */
	function getCategorySystemsAllowed($user, $o=null){

		require_once DIMS_APP_PATH . '/modules/system/class_category.php';
		$categories = array();
		//peut-être un peu lourd. Après est-ce qu'il y en aura 500 ...
		$sql = "SELECT * FROM dims_category WHERE id_parent IS NULL";//récupération de tous les systèmes de catégories
		$res = $this->db->query($sql);
		while($tab = $this->db->fetchrow($res)){
			$c = new category();
			$c->openFromResultSet($tab);//simple open des fields
			$c->initRules();//récupération des droits de la catégories
			if($c->isUsable($user, $this->fields['id'], $o)) $categories[] = $c;
		}
		return $categories;
	}

	public function getActionsDispo(){
		$sel = "SELECT	*
			FROM	dims_mb_action
			WHERE	id_module_type = :idmoduletype ";
		$res = $this->db->query($sel, array(
					':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
				));
		$lst = array();
		while($r = $this->db->fetchrow($res)){
			$lst[] = $r;
		}
		return $lst;
	}

	//retourne potentiellement un tableau si le wid n'est pas précisé
	public static function  getmodulesid($moduletype_name, $wid = null){
		$mt = module_type::find_by(array('label' => $moduletype_name), null, 1);
		$modules = null;
		if(!empty($mt)){
			$conditions = array('id_module_type' => $mt->get('id'));
			if( ! is_null($wid)) $conditions['id_workspace'] = $wid;
			$modules = self::conditions($conditions)->run();
			if(empty($modules)) $modules = null;
		}
		return $modules;
	}
}
?>
