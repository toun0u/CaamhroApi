<?php
require_once DIMS_APP_PATH.'modules/system/class_workspace_group.php';
require_once DIMS_APP_PATH.'modules/system/class_group_user.php';
require_once DIMS_APP_PATH.'modules/system/class_pagination.php';

class group extends pagination {
	const TABLE_NAME = "dims_group";
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	/**
	*
	* @access public
	*
	**/

	function save() {
		$this->fields['depth'] = sizeof(explode(';',$this->fields['parents']));
		return(parent::save());
	}

	function delete() {
		if ($this->fields['id']!=-1 && !$this->fields['system'] && !$this->fields['protected']) {
			$fatherid = $this->fields['id_group'];

			// attach children to new father
			$select =	"
					SELECT	dims_group.id
					FROM	dims_group
					WHERE	dims_group.id_group = :idgroup";


			$result = $this->db->query($select, array(
				':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));

			$update =	"
						UPDATE	dims_group
						SET	dims_group.id_group = :idfather
						WHERE	dims_group.id = :idchild
						";
			while ($child =  $this->db->fetchrow($result)) {
				$res=$this->db->query($update, array(
					':idfather' => array('type' => PDO::PARAM_INT, 'value' => $fatherid),
					':idchild' => array('type' => PDO::PARAM_INT, 'value' => $child['id']),
				));
			}

			// update parents group
			system_updateparents();


			$delete = "DELETE FROM dims_group_user WHERE id_group = :idgroup";
			$res = $this->db->query($delete, array(
				':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));

			// TODO: penser à supprimer les users du groupe

			parent::delete();

		}
	}

	/**
	*
	* @param int $idgroup
	* @access private
	*
	**/

	function getfullgroup($idgroup = '') {
		if ($idgroup == '') $idgroup = $this->fields['id'];

		$res='';

		$select = "SELECT dims_group.* FROM dims_group WHERE id = :idgroup AND id_group <> :idgroup";
		$answer = $this->db->query($select, array(
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if ($fields = $this->db->fetchrow($answer)) {
			$parents = $this->getfullgroup($fields['id_group']);
			if ($parents != '') $res = $parents .' / ';
			$res .= $fields['label'];
		}
		return $res;
	}


	function getgroupchildren($depthlimit = 0, $idgroup = '') {
		if ($idgroup == '') $idgroup = $this->fields['id'];

		$ar = array();
		$groups = array();

		$select = "SELECT * FROM dims_group WHERE system = 0 ORDER BY label";
		$result = $this->db->query($select);
		while ($fields = $this->db->fetchrow($result)) {
			$groups[$fields['id_group']][$fields['id']] = $fields;
		}

		$this->depth = system_getallgroupsrec($ar, $groups, $idgroup, $depthlimit,0);

		return($ar);
	}


	// return all children ids of current group
	function getgroupchildrenlite($depthlimit = 0, $idgroup = '', $mode = '') {
		// $mode = web / public / admin
		if ($idgroup == '') $idgroup = $this->fields['id'];

		$ar = array();
		$groups = array();

		$where = '';

		if (!empty($mode) && $this->db->fieldexist('dims_group', $mode)) $where .= " AND $mode = 1 ";

		$select =	"
					SELECT	*
					FROM	dims_group
					WHERE	system = 0
					$where
					ORDER BY label
					";
		$result = $this->db->query($select);
		while ($fields = $this->db->fetchrow($result)) {
			$groups[$fields['id_group']][$fields['id']] = $fields;
		}

		$this->depth = system_getallgroupsreclite($ar, $groups, $idgroup, $depthlimit,0);

		return($ar);
	}


	// return all brothers ids of current group
	function getgroupbrotherslite($mode = '', $domain = '') {
		$where = '';
		if (!empty($mode) && $this->db->fieldexist('dims_group', $mode)) $where .= " AND $mode = 1 ";

		$select =	"
					SELECT	dims_group.*
					FROM	dims_group
					WHERE	id_group = :idgroupparent
					$where
					AND	id <> :idgroup
					";

		$result = $this->db->query($select, array(
			':idgroupparent' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']),
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		$ar = array();
		while ($fields = $this->db->fetchrow($result)) {
			if ($domain != '') {
				$dom_array = split("\r\n", $fields['domainlist']);
				foreach($dom_array as $dom) {
					if ($domain == $dom) $ar[] = $fields['id'];
				}
			}
			else $ar[] = $fields['id'];
		}

		return($ar);
	}

	function getparents($parents = '') {
		$params = array();
		if ($parents == '') $parents = $this->fields['parents'];

		$select = "SELECT * FROM dims_group WHERE id IN (".$this->db->getParamsFromArray(explode(';', $parents), 'idgroup', $params).")";
		$result = $this->db->query($select,$params);

		$groups = array();
		while ($fields = $this->db->fetchrow($result)) $groups[$fields['id']] = $fields;

		return($groups);
	}

	function getfather() {
		$father = new group();
		if ($father->open($this->fields['id_group'])) return $father;
		else return(false);
	}

	function getusers($obj=false) {
		$db = dims::getInstance()->getDb();

		$users = array();

		if($obj){
			$sel = "SELECT 		u.*
					FROM 		".user::TABLE_NAME." u
					INNER JOIN 	".group_user::TABLE_NAME." gu
					ON 			gu.id_user = u.id
					WHERE 		gu.id_group = {$this->fields['id']}";
			$result = $db->query($sel);
			while ($fields = $db->fetchrow($result)) {
				$user = new user();
				$user->openFromResultSet($fields);
				$users[$fields['id']] = $user;
			}
		}else{
			// Requ�te1
			$select = 	"
						SELECT 	dims_user.id,
								dims_user.login,
								dims_user.firstname,
								dims_user.lastname
						FROM 	dims_user,
								dims_group_user
						WHERE 	dims_group_user.id_group = {$this->fields['id']}
						AND		dims_group_user.id_user = dims_user.id
						";

			$result = $db->query($select);

			while ($fields = $db->fetchrow($result)) {
				$users[$fields['id']] = $fields;
			}
		}

		return $users;
	}

	function getNbUsers() {
		$db = dims::getInstance()->getDb();

		$select =	"
					SELECT	count(id) as cpte
					FROM	dims_user,
					dims_group_user
					WHERE	dims_group_user.id_group = :idgroup
					AND		dims_group_user.id_user = dims_user.id
					";

		$result = $db->query($select, array(
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		if ($fields = $db->fetchrow($result)) {
			return $fields['cpte'];
		}

		return false;
	}

	public function getIdUsers(){
		$db = dims::getInstance()->getDb();
		$users = array();
		$sel = "SELECT 		u.id
				FROM 		".user::TABLE_NAME." u
				INNER JOIN 	".group_user::TABLE_NAME." gu
				ON 			gu.id_user = u.id
				WHERE 		gu.id_group = {$this->fields['id']}";
		$result = $db->query($sel);
		while ($fields = $db->fetchrow($result)) {
			$users[$fields['id']] = $fields['id'];
		}
		return $users;
	}

	function createchild() {
		$child = new group();
		$child->fields = $this->fields;
		unset($child->fields['id']);
		$child->fields['id_group'] = $this->fields['id'];
		$child->fields['label'] = 'fils de '.$this->fields['label'];
		$child->fields['parents'] = $this->fields['parents'].';'.$this->fields['id'];
		$child->fields['system'] = 0;
		return($child);
	}

	function createclone()
	{
		$clone = new group();
		$clone->fields = $this->fields;
		unset($clone->fields['id']);
		$clone->fields['label'] = 'clone de '.$this->fields['label'];
		$clone->fields['system'] = 0;
		return($clone);
	}

	function attachtogroup($workspaceid, $profileid = 0)
	{
		$db = dims::getInstance()->getDb();

		$workspace_group = new workspace_group();
		$workspace_group->fields['id_group'] = $this->fields['id'];
		$workspace_group->fields['id_workspace'] = $workspaceid;
		$workspace_group->fields['id_profile'] = $profileid;
		$workspace_group->save();


		// TODO: A FINALISER => LORS DU RATTACHEMENT D'UN GROUPE, POSSIBILITE D'EXECUTER UNE COMMANDE POUR L'ENSEMBLE DES UTILISATEURS
		/*
		$select =	"
					SELECT	m.id, m.label, mt.label as moduletype
					FROM	dims_module_workspace mg,
							dims_module m,
							dims_module_type mt
					WHERE	mg.id_group = {$groupid}
					AND		mg.id_module = m.id
					AND		m.id_module_type = mt.id
					";

		$res=$db->query($select);
		while ($fields = $db->fetchrow($res))
		{
			$admin_userid = $this->fields['id'];
			$admin_groupid = $groupid;
			$admin_moduleid = $fields['id'];

			echo "<br><b>� {$fields['label']} �</b> ({$fields['moduletype']})<br>";
			if (file_exists(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_user_create.php")) include(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_user_create.php");
		}
		*/

	}

	function getactions(&$actions) {// only for org groups
		$db = dims::getInstance()->getDb();

		$select =	"
				SELECT		dims_workspace_group_role.id_workspace,
							dims_role_action.id_action,
							dims_role.id_module
				FROM		dims_role_action,
							dims_role,
							dims_workspace_group_role
				WHERE		dims_workspace_group_role.id_role = dims_role.id
				AND			dims_role.id = dims_role_action.id_role
				AND			dims_workspace_group_role.id_group = :idgroup
				";

		$result = $this->db->query($select, array(
			':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($fields = $this->db->fetchrow($result)) {
			$actions[$fields['id_workspace']][$fields['id_module']][$fields['id_action']] = true;
		}
	}

	public function getListUsers($where="",$params = array(), $pagination=false) {
		$listusers=array();

		if (!$pagination) {
			pagination::liste_page($this->getListUsers($where, $params, true));
			$limit = "LIMIT :limitstart, :limitkey";
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		}
		else $limit="";

		$select =	"
				SELECT		dims_user.id,
						dims_user.lastname,
						dims_user.firstname,
						dims_user.login,
						dims_user.service,
						dims_user.function,
						dims_group.id as idref,
						dims_group.label as label,
						dims_group_user.adminlevel
				FROM		dims_user
				INNER JOIN	dims_group_user
				ON		dims_group_user.id_user = dims_user.id
				AND		dims_group_user.id_group = :idgroup
				INNER JOIN	dims_group
				ON		dims_group.id = dims_group_user.id_group
				$where
				$limit
				";
		$params[':idgroup'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$result = $this->db->query($select, $params);

		if ($pagination) {
			return $this->db->numrows($result);
		}
		else {
			while ($fields = $this->db->fetchrow($result)) {
				$listusers[$fields['id']]=$fields;
			}
			return $listusers;
		}
	}

	function addUser($id_user, $level = null) {
		$db = dims::getInstance()->getDb();

		$group_user = new group_user();
		$group_user->fields['id_group'] = $this->fields['id'];
		$group_user->fields['id_user'] = $id_user;

		if ($level != null) {
			$group_user->fields['adminlevel'] = $level;
		}

		$group_user->save();
	}

	function delUser($id_user){
		$group_user = new group_user();
		if ($group_user->open($id_user,$this->fields['id']))
			$group_user->delete();
	}

	public function getLabel() {
		return $this->getAttribut("label", self::TYPE_ATTRIBUT_STRING);
	}

	function getRoles($workspaces = array()) {// only for org groups
		$db = dims::getInstance()->getDb();
		$lst = array();
		$params = array();
		if ($this->fields['id'] != '' && $this->fields['id'] > 0){

		$and = (count($workspaces) > 0) ? "AND dims_workspace_group_role.id_workspace IN (".$this->db->getParamsFromArray($workspaces, 'idworkspace', $params).") " : '';
		$select =	"
			SELECT		dims_role.*
			FROM		dims_role
			INNER JOIN	dims_workspace_group_role
			ON			dims_workspace_group_role.id_role = dims_role.id
			WHERE	dims_workspace_group_role.id_group = :idgroup
			$and";
		$params[':igroup'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$result = $this->db->query($select, $params);
		require_once DIMS_APP_PATH."modules/system/class_role.php";

		while ($fields = $this->db->fetchrow($result)) {
		$role = new role();
		$role->openWithFields($fields);
		$lst[$fields['id']] = $role;
		}
	}
	return $lst;
	}

	public function getChildrens(){
		$sel = "SELECT 	*
				FROM 	".self::TABLE_NAME."
				WHERE 	id_group = ".$this->getId();
		$lst = array();
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$elem = new group();
			$elem->openFromResultSet($r);
			$lst[] = $elem;
		}
		return $lst;
	}

	public static function getFirstMainGroup() {
		$db = dims::getInstance()->getDb();
		$rs = $db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE id_group = 1 LIMIT 0, 1');
		if ($db->numrows($rs)) {
			$group = new group();
			$group->openFromResultSet($db->fetchrow($rs));
			return $group;
		}
		else return null;
	}

	public static function getByCode($code = '') {
		if ($code == '') {
			return null;
		}
		else {
			$db = dims::getInstance()->getDb();
			$rs = $db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE `code` = "'.$code.'" LIMIT 0, 1');
			if ($db->numrows($rs)) {
				$group = new group();
				$group->openFromResultSet($db->fetchrow($rs));
				return $group;
			}
			else return null;
		}
	}

	public function getparentslite(){
		$lst = array();
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 	id
				FROM 	".self::TABLE_NAME."
				WHERE 	id IN (".implode(',',explode(';',$this->fields['parents'])).")";
		$res = $db->query($sel);
		while($r = $db->fetchrow($res))
			$lst[$r['id']] = $r['id'];
		return $lst;
	}
}
?>
