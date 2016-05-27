<?php
require_once DIMS_APP_PATH.'modules/system/class_workspace_group.php';
require_once DIMS_APP_PATH.'modules/system/class_group.php';
require_once DIMS_APP_PATH.'modules/system/class_user.php';
require_once DIMS_APP_PATH.'modules/system/include/functions.php';
require_once DIMS_APP_PATH.'modules/system/class_pagination.php';
//require_once(DIMS_APP_PATH . '/modules/system/include/global.php');

class workspace extends pagination {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_workspace');
	}

	/**
	*
	* @access public
	*
	**/

	function save($id_object="",$execute_sql=true) {
		if (isset($this->fields['parents'])) $this->fields['depth'] = sizeof(explode(';',$this->fields['parents']));
		return(parent::save());
	}

	function delete($id_object="") {
		$db = dims::getInstance()->getDb();

		if ($this->fields['id']!=-1 && !$this->fields['system']) {
			$fatherid = $this->fields['id_workspace'];

			// attach children to new father
			$select =	"
					SELECT	dims_workspace.id
					FROM	dims_workspace
					WHERE	dims_workspace.id_workspace = :idworkspace";

			$result = $db->query($select, array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));

			while ($child =  $db->fetchrow($result)) {
				$update =	"
							UPDATE	dims_workspace
							SET	dims_workspace.id_workspace = $fatherid
							WHERE	dims_workspace.id = $child[id]
							";
				$res=$db->query($update);
			}

			// update parents workspace
			system_updateparents();

			$delete = "DELETE FROM dims_workspace_user WHERE id_workspace = ".$this->fields['id']."; ";
			$res=$db->query($delete);

			$delete = "DELETE FROM dims_workspace_group WHERE id_workspace = ".$this->fields['id'];
			$res=$db->query($delete);

			// delete feedback value from current workspace
			$update = "UPDATE dims_workspace SET id_workspace_feedback=0 where id_workspace_feedback = ".$this->fields['id'];
			$res=$db->query($update);

			parent::delete();
		}
	}

	/**
	*
	* @param int $idworkspace
	* @access private
	*
	**/

	function getfullworkspace($idworkspace = '') {
		$db = dims::getInstance()->getDb();

		if ($idworkspace == '') $idworkspace = $this->fields['id'];

		$res='';

		$select = "SELECT dims_workspace.* FROM dims_workspace WHERE id = :idworkspace AND id_workspace <> :idworkspace";
		$answer = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $idworkspace),
		));
		if ($fields = $db->fetchrow($answer)) {
			$parents = $this->getfullworkspace($fields['id_workspace']);
			if ($parents != '') $res = $parents .' / ';
			$res .= $fields['label'];
		}
		return $res;
	}

	function getworkspacechildren($depthlimit = 0, $idworkspace = '') {
		$db = dims::getInstance()->getDb();
		if ($idworkspace == '') $idworkspace = $this->fields['id'];

		$ar = array();
		$workspaces = array();

		$select = "SELECT * FROM dims_workspace WHERE system = 0 ORDER BY label";
		$result = $db->query($select);
		while ($fields = $db->fetchrow($result))
		{
			$workspaces[$fields['id_workspace']][$fields['id']] = $fields;
		}

		$this->depth = system_getallworkspacesrec($ar, $workspaces, $idworkspace, $depthlimit,0);

		return($ar);
	}


	// return all children ids of current workspace
	function getworkspacechildrenlite($depthlimit = 0, $idworkspace = '', $mode = '') {
		// $mode = web / public / admin

		$db = dims::getInstance()->getDb();
		if ($idworkspace == '') $idworkspace = $this->fields['id'];

		$ar = array();
		$workspaces = array();

		$where = '';
		if (!empty($mode) && in_array($mode, array_keys($this->fields))) $where .= " AND $mode = 1 ";
		$select =	"
					SELECT	*
					FROM	dims_workspace
					WHERE	system = 0
					$where
					ORDER BY label
					";
		$result = $db->query($select);
		while ($fields = $db->fetchrow($result)) {
			$workspaces[$fields['id_workspace']][$fields['id']] = $fields;
		}

		$this->depth = system_getallworkspacesreclite($ar, $workspaces, $idworkspace, $depthlimit,0);

		return($ar);
	}

// return all children ids of current workspace
	function getNbChildren($depthlimit = 0, $idworkspace = '', $mode = '') {
		// $mode = web / public / admin

		$db = dims::getInstance()->getDb();
		$nbchilds=0;
		if ($idworkspace == '') $idworkspace = $this->fields['id'];

		$ar = array();
		$workspaces = array();

		$where = '';
		if (!empty($mode) && in_array($mode, array_keys($this->fields))) $where .= " AND $mode = 1 ";
		$select =	"
					SELECT	count(id) as cpte
					FROM	dims_workspace
					WHERE	system = 0
					$where
					ORDER BY label
					";
		$result = $db->query($select);
		if ($fields = $db->fetchrow($result)) {
			$nbchilds=$fields['cpte'];
		}

		system_getNballworkspacesreclite($nbchilds, $workspaces, $idworkspace, $depthlimit,0);

		return($ar);
	}

	// return all brothers ids of current group
	function getworkspacebrotherslite($mode = '', $domain = '') {
		$db = dims::getInstance()->getDb();

		$where = '';
		if ($mode != '') $where .= " AND $mode = 1 ";

		$select =	"
					SELECT	dims_workspace.*
					FROM	dims_workspace
					WHERE	id_workspace = {$this->fields['id_workspace']}
					$where
					AND	id <> {$this->fields['id']}
					";
		$result = $db->query($select);
		$ar = array();
		while ($fields = $db->fetchrow($result))
		{
			if ($domain != '')
			{
				$web_dom_array = split("\r\n", $fields['web_domainlist']);
				$admin_dom_array = split("\r\n", $fields['admin_domainlist']);
				foreach($admin_dom_array as $dom)
				{
					if ($domain == $dom) $ar[] = $fields['id'];
				}
			}
			else $ar[] = $fields['id'];
		}

		return($ar);
	}

	function getparents($parents = '') {
		$db = dims::getInstance()->getDb();
		if ($parents == '') $parents = $this->fields['parents'];

		$parents = str_replace(';',',',$parents);

		$select = "SELECT * FROM dims_workspace WHERE id IN ({$parents})";
		$result = $db->query($select);

		$workspaces = array();
		while ($fields = $db->fetchrow($result)) $workspaces[$fields['id']] = $fields;

		return($workspaces);
	}

	function getfather() {
		$father = new workspace();
		if ($father->open($this->fields['id_workspace'])) return $father;
		else return(false);
	}


	function getContacts($lstcts=false) {
		$db = dims::getInstance()->getDb();

		$contacts=array();
		$params=array();

		// Requete
		$select =	"	SELECT		dims_mod_business_contact.id as id_contact,
									dims_mod_business_contact.firstname,
									dims_mod_business_contact.lastname
						FROM		dims_mod_business_contact
				";

		if ($lstcts!=false && is_array($lstcts) && !empty($lstcts)) {
			$select.=" where id in (".$db->getParamsFromArray($lstcts,'idcontact', $params).")";
		}

		$result = $db->query($select);

		while ($fields = $db->fetchrow($result)) {
			$contacts[$fields['id_contact']] = $fields;
		}
		return $contacts;
	}

	function getusers($contact=false,$lstusers=false) {
		$db = dims::getInstance()->getDb();

		$users = array();

		// Requete
		$select =	"	SELECT		dims_user.*,
									dims_workspace_user.id_profile,
									dims_workspace_user.adminlevel
						FROM		dims_user
						INNER JOIN	dims_workspace_user
						ON			dims_workspace_user.id_workspace = :idworkspace
						WHERE		dims_workspace_user.id_user = dims_user.id
						ORDER BY	lastname,firstname
				";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		if ($lstusers!=false && is_array($lstusers) && !empty($lstusers)) {
			$select.=" and dims_user.id in (".$db->getParamsFromArray($lstusers,'iduser', $params).")";
		}
		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			if ($contact) {
				$users[$fields['id_contact']] = $fields['id_contact'];
			}
			else {
				$users[$fields['id']] = $fields;
			}
		}

		$params = array();
		// on y ajoute les personnes qui proviennent des groupes affectes
		$select=	"	SELECT		dims_user.*,
									dims_workspace_group.id_profile,
									dims_workspace_group.adminlevel
						FROM		dims_user

						INNER JOIN	dims_group_user ON dims_group_user.id_user=dims_user.id";

		if ($lstusers!=false && is_array($lstusers) && !empty($lstusers)) {
			$select.="	AND			dims_user.id in (".$db->getParamsFromArray($lstusers,'iduser', $params).")";
		}

		$select.=	"	INNER JOIN	dims_workspace_group ON dims_workspace_group.id_group=dims_group_user.id_group
						INNER JOIN	dims_workspace	on dims_workspace.id = dims_workspace_group.id_workspace
						AND			dims_workspace_group.id_workspace = :idworkspace";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			if ($contact) {
				$users[$fields['id_contact']] = $fields['id_contact'];
			}
			else {
				// correction 26/01/2011 : perte du niveau de l'adminlevel
				$old_level=0;
				if (isset($users[$fields['id']])) {
					$old_level=$users[$fields['id']]['adminlevel'];
				}

				$users[$fields['id']] = $fields;
				if ($users[$fields['id']]['adminlevel']<$old_level) {
					$users[$fields['id']]['adminlevel']=$old_level;
				}
			}
		}
		return $users;
	}

	function getNbUsers() {
		$db = dims::getInstance()->getDb();

		$users = array();
		$nbusers=0;

		// Requete1
		$select =	"	SELECT		count(dims_user.id) as cpte
						FROM		dims_user
						INNER JOIN	dims_workspace_user
						ON			dims_workspace_user.id_workspace = :idworkspace
						WHERE		dims_workspace_user.id_user = dims_user.id
				";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		if ($fields = $db->fetchrow($result)) {
			$nbusers=$fields['cpte'];
		}

		// on y ajoute les personnes qui proviennent des groupes affectes
		$select=	"	SELECT		count(dims_user.id) as cpte
						FROM		dims_user
						INNER JOIN	dims_group_user ON dims_group_user.id_user=dims_user.id
						INNER JOIN	dims_workspace_group ON dims_workspace_group.id_group=dims_group_user.id_group
						INNER JOIN	dims_workspace	on dims_workspace.id = dims_workspace_group.id_workspace
						AND		dims_workspace_group.id_workspace = :idworkspace
					";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($fields = $db->fetchrow($result)) {
			$nbusers+=$fields['cpte'];
		}
		return $nbusers;
	}

	function getgroups($iduser = -1) {
		$db = dims::getInstance()->getDb();

		$groups = array();

		// Requ�te1
		if ($iduser!=-1) {
			$select =	"
						SELECT		dims_group.*,
									dims_workspace_group.*
						FROM		dims_group
						INNER JOIN	dims_workspace_group
						ON			dims_workspace_group.id_workspace = :idworkspace
						AND			dims_workspace_group.id_group = dims_group.id
						INNER JOIN	dims_group_user
						ON			dims_workspace_group.id_group=dims_group_user.id_group and id_user = :iduser
					";
			$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $iduser);
		}
		else {
			$select =	"
						SELECT		dims_group.*,
									dims_workspace_group.*
						FROM		dims_group
						INNER JOIN	dims_workspace_group
						ON			dims_workspace_group.id_workspace = :idworkspace
						AND			dims_workspace_group.id_group = dims_group.id
						";
		}
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) $groups[$fields['id']] = $fields;

		return $groups;
	}

	/*
	 * fonction permettant le renvoi de la liste d'un ou plusieurs avec groupe et utilisateurs et avec ou non filtre sur un nom
	 * Create by Pat le 25/01/2009
	 * renvoi un tableau structur� pour post traitement ou affichage
	 * [works]
	 *	|-> [groups] -> ref to [groups]
	 *	|-> [users]  -> list of users
	 * [groups]
	 *	|-> list of users
	 */
	function getGroupsUsers($nomsearch="",$allworkspace=false,$userid=-1,$moduleid=-1,$workspaceid=-1) {
		$db = dims::getInstance()->getDb();
		// liste tempo de users
		$lstusers = array();
		$lstgroups = array();

		// tableau de resultatho $_SESSION['dims']['moduleid'];
		$lstresult=array();
		$lstresult['works']=array(); // list of workspace concerning
		$lstresult['groups']=array(); // list of groups concerning
		$lstresult['users']=array(); // list of users concerning / name + id
		//
		// list of workspaces
		$lstwork = array();
		// default test
		if ($userid==-1) $userid=$_SESSION['dims']['userid'];
		if ($moduleid==-1) $moduleid=$_SESSION['dims']['moduleid'];
		if ($workspaceid==-1) {
			if (isset($_SESSION['dims']['sharemodule']['idrecord']) && $_SESSION['dims']['sharemodule']['idrecord']==$moduleid)
				$workspaceid=$_SESSION['dims']['current']['workspaceid'];
			else
				$workspaceid=$_SESSION['dims']['workspaceid'];
		}

		if ($allworkspace) {
			if (dims_isadmin()) {
				$querywork ="	SELECT	distinct id_workspace
				FROM		dims_module_workspace
				WHERE		id_module=:idmodule";

				$res=$db->query($querywork, array(
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']),
				));
				if ($db->numrows($res)) {
					while ($work = $db->fetchrow($res)) {
						$lstwork[$work['id_workspace']]=$work['id_workspace'];
					}
				}
			}
			else {
				$user = new user();
				$user->open($userid);
				$lstworks=$user->getworkspaces();

				foreach($lstworks as $idw=>$f) {
					$lstwork[$idw]=$idw;
				}
			}
	}
	else $lstwork[$workspaceid]=$workspaceid;

	if ($moduleid>0) {
		$lstres=dims_viewworkspaces_rec($moduleid);
		foreach(explode(",",$lstres) as $id=>$e) $lstwork[$e]=$e;
	}

		$params = array();
		// on fusionne avec ceux qui utilisent ce module pour etre sur
		// construction de la liste des workspaces
		$querywork ="	SELECT		*
				FROM		dims_module_workspace
				WHERE		id_workspace in (".$db->getParamsFromArray($lstwork, 'idworkspace', $params).")
				AND		id_module=:idmodule";
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $moduleid);

		$res=$db->query($querywork, $params);
		unset($lstwork);
		if ($db->numrows($res)) {
			while ($work = $db->fetchrow($res)) {
			$lstwork[$work['id_workspace']]=$work['id_workspace'];
			}
		}

		$params = array();
		// construction de la liste des workspaces
		$querywork ="	SELECT		*
				FROM		dims_workspace
				WHERE		id in (".$db->getParamsFromArray($lstwork, 'idworkspace', $params).")";

		$res=$db->query($querywork, $params);

		if ($db->numrows($res)) {
			while ($work = $db->fetchrow($res)) {
				if ($work['id']!=1) {
					$lstresult['works'][$work['id']] = array();
					$lstresult['works'][$work['id']]['label'] = $work['label'];
					$lstresult['works'][$work['id']]['groups'] = array(); // list of id group
					$lstresult['works'][$work['id']]['users'] = array();  // list of users
				}
			}
			unset($work);
		}

		$params = array();
		// construction de la liste des workspaces_users
		$queryworkuser =	"
					SELECT		wu.*
					FROM		dims_workspace_user wu
					INNER JOIN	dims_user as u
					ON		u.id=wu.id_user
					AND		wu.id_workspace IN (".$db->getParamsFromArray($lstwork, 'idworkspace', $params).")";

		if ($nomsearch!="") {
			$queryworkuser .=	"AND	(u.login LIKE :searchedname OR
						u.firstname LIKE :searchedname OR
						u.lastname LIKE :searchedname)";
			$params[':searchedname'] = array('type' => PDO::PARAM_STR, 'value' => $nomsearch.'%');
		}

		$res=$db->query($queryworkuser, $params);

		if ($db->numrows($res)) {
			while ($wu = $db->fetchrow($res)) {
				// affectation du user sur le workspace direct
				$lstresult['works'][$wu['id_workspace']]['users'][$wu['id_user']]=$wu['id_user'];
				// preparation de la liste complete des users
				if (!isset($lstusers[$wu['id_user']])) $lstusers[$wu['id_user']]=$wu['id_user'];
			}
			unset($wu);
		}

		$params = array();
		// construction de la liste des workspaces_groups
		$queryworkgroup =	"
							SELECT		wg.*,g.id,g.label,gu.id_user as iduser
							FROM		dims_workspace_group wg
							INNER JOIN	dims_group as g
							ON		g.id=wg.id_group
							AND		wg.id_workspace IN (".$db->getParamsFromArray($lstwork, 'idworkspace', $params).")
							";

		$queryworkgroup .=	"
							INNER JOIN	dims_group_user as gu
							ON		gu.id_group=g.id
							INNER JOIN	dims_user as u
							ON		u.id=gu.id_user
							";

		if ($nomsearch!="") {
			$queryworkgroup .=	"AND	(g.label LIKE :searchedname OR
							u.login LIKE :searchedname OR
							u.firstname LIKE :searchedname OR
							u.lastname LIKE :searchedname)";
			$params[':searchedname'] = array('type' => PDO::PARAM_STR, 'value' => $nomsearch.'%');
		}

		$res=$db->query($queryworkgroup, $params);

		if ($db->numrows($res)) {
			while ($wg = $db->fetchrow($res)) {

				// affectation du group sur le workspace direct
				$lstresult['works'][$wg['id_workspace']]['groups'][$wg['id']]=$wg['id'];
				// preparation de la liste complete des users
				if (!isset($lstgroups[$wg['id']])) $lstgroups[$wg['id']]=$wg['id'];

				// on ajoute les personnes
				if (!isset($lstresult['groups'][$wg['id']]['users'][$wg['iduser']])) $lstresult['groups'][$wg['id']]['users'][$wg['iduser']]=$wg['iduser'];

				// preparation de la liste complete des users
				if (!isset($lstusers[$wg['iduser']])) $lstusers[$wg['iduser']]=$wg['iduser'];
			}
			unset($wg);
		}

		// construction de la liste d'info des groups
		$querygroup =	"
				SELECT		g.id,g.label,g.id_group
				FROM		dims_group as g
				ORDER		BY depth";

		$res=$db->query($querygroup);
		if ($db->numrows($res)) {
			while ($g = $db->fetchrow($res)) {
				// test si concerne ou non
				if (isset($lstgroups[$g['id']]) ) { // rattachement parents
					$g['users']=array();
					if (!isset($lstresult['groups'][$g['id']])) $lstresult['groups'][$g['id']] = $g;
					else $lstresult['groups'][$g['id']] += $g;
				}
				elseif (isset($lstgroups[$g['id_group']])) {

					$lstgroups[$g['id']]=$g['id'];
					$g['herited']=true;

					if (!isset($lstresult['groups'][$g['id']])) $lstresult['groups'][$g['id']] = $g;
					else $lstresult['groups'][$g['id']] += $g;

					// on l'ajoute � la liste des espaces courant
					// on boucle sur tous les espaces, si on trouve le pere on rajout l'element courant
					foreach($lstresult['works'] as $iw=>$work) {
						if (isset($lstresult['works'][$iw]['groups'][$g['id_group']])) {
							// on ajoute cet element car le pere y est rattache aussi
							$lstresult['works'][$iw]['groups'][$g['id']]	=$g['id'];
						}
					}
				}
			}
		}

		// on parcourt maintenant la liste
		// construction de la liste des groups_users
		if (!empty($lstgroups)) {
			$params = array();
			$querygroupuser = "
				SELECT		distinct gu.*,u.id
				FROM		dims_group_user as gu
				INNER JOIN	dims_user as u
				ON		u.id=gu.id_user
				AND		gu.id_group IN (".$db->getParamsFromArray($lstgroups, 'idgroup', $params).")";

			if ($nomsearch!="") {
				$querygroupuser .=	"AND	(u.login LIKE :searchedname OR
								u.firstname LIKE :searchedname OR
								u.lastname LIKE :searchedname)";
				$params[':searchedname'] = array('type' => PDO::PARAM_STR, 'value' => $nomsearch.'%');
			}

			$res=$db->query($querygroupuser, $params);
			if ($db->numrows($res)) {
				while ($g = $db->fetchrow($res)) {
					$lstresult['groups'][$g['id_group']]['users'][$g['id']] = $g['id'];
					if (!isset($lstusers[$g['id']])) $lstusers[$g['id']]=$g['id'];
				}
			}
		}

		// construction de la liste d'info des users rapportes de : workspace_user et group_user
		if (!empty($lstusers)) {
			$params = array();
			$queryuser =	"
				SELECT		u.id,u.firstname,u.lastname,u.login,u.color,u.jabberId
				FROM		dims_user u
				where		id in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")";

			$res=$db->query($queryuser, $params);
			if ($db->numrows($res)) {
			while ($u = $db->fetchrow($res)) {
				$lstresult['users'][$u['id']] = $u;
			}
			}
		}

		return $lstresult;
	}

	function createchild() {
		$child = new workspace();
		$child->fields = $this->fields;
		unset($child->fields['id']);
		$child->fields['id_workspace'] = $this->fields['id'];
		$child->fields['label'] = 'fils de '.$this->fields['label'];
		$child->fields['parents'] = $this->fields['parents'].';'.$this->fields['id'];
		$child->fields['system'] = 0;
		return($child);
	}

	function createclone() {
		$clone = new workspace();
		$clone->fields = $this->fields;
		unset($clone->fields['id']);
		$clone->fields['label'] = 'clone de '.$this->fields['label'];
		$clone->fields['system'] = 0;
		return($clone);
	}

	function getInfoModules() {
		$db = dims::getInstance()->getDb();
		$modules=array();
		$select =	"
				SELECT		distinct dims_module.id AS instanceid,
							dims_module.active,
							dims_module.shared,
							dims_module.herited,
							dims_module.adminrestricted,
							dims_module.public,
							dims_module.viewmode,
							dims_module.autoconnect,
							dims_module.transverseview,
							dims_module.id_module_type,
							dims_module_workspace.autoconnect as autoconnect2
				FROM		dims_module
				INNER JOIN	dims_module_workspace
				ON		dims_module_workspace.id_workspace = :idworkspace
				AND		dims_module_workspace.id_module = dims_module.id
				";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($module = $db->fetchrow($result)) {
			$modules[$module['instanceid']] = $module;
			$modules[$module['instanceid']]['autoconnect']=($module['autoconnect'] || $module['autoconnect2']) ? 1 : 0;
		}

		return $modules;
	}

	function getmodules($lite = FALSE, $public = FALSE) {
		$db = dims::getInstance()->getDb();
		$id_user=0;

		$modules = array();

		if (isset($_SESSION['dims']['userid']) && $_SESSION['dims']['userid']>0) $id_user=$_SESSION['dims']['userid'];

		if ($lite) {
			$select =	"
				SELECT		dims_module.id AS instanceid,
							dims_module.id AS id
				FROM		dims_module
				INNER JOIN	dims_module_workspace
				ON			(dims_module_workspace.id_workspace = :idworkspace
				OR			dims_module.shared=1)
				AND			dims_module_workspace.id_module = dims_module.id
				ORDER BY	dims_module_workspace.position
				";
		}
		else
			$select = "
				SELECT		DISTINCT dims_module_type.*,
							dims_module.label AS instancename,
							dims_module.id AS instanceid,
							dims_module.id_workspace AS instanceworkspace,
							dims_module.active,
							dims_module.shared,
							dims_module.herited,
							dims_module.adminrestricted,
							dims_module.public,
							dims_module.viewmode,
							dims_module.autoconnect,
							dims_module.transverseview,
							dims_module.id_module_type,
							dims_module_workspace.blockposition,
							dims_module_workspace.position,
							dims_module_workspace.visible,
							dims_module_workspace.autoconnect AS autoconnect2

				FROM		dims_module_type

				INNER JOIN	dims_module
				ON		dims_module.id_module_type = dims_module_type.id
				AND		(
							dims_module.id_workspace IN (0, :idworkspace)
							OR dims_module.shared = 1
						)

				INNER JOIN	dims_module_workspace
				ON		dims_module_workspace.id_workspace IN (0, :idworkspace)
				AND		dims_module_workspace.id_module = dims_module.id

				ORDER BY	dims_module_workspace.position";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($module = $db->fetchrow($result)) {
			$modules[$module['instanceid']] = $module;
			if (!$lite) $modules[$module['instanceid']]['autoconnect']=($module['autoconnect'] || $module['autoconnect2']) ? 1 : 0;
		}

		return $modules;
	}

	function getsharedmodules($herited = FALSE) {
		$db = dims::getInstance()->getDb();
		global $_DIMS;
		$modules = array();

		$parents = str_replace(';',',',$this->fields['parents']);

		if ($parents!='') {

			if ($herited) $sql_herited = 'AND dims_module.herited = 1';
			else $sql_herited = '';

			$select = "	SELECT		dims_module.id,
							dims_module.label,
							dims_workspace.label AS workspacelabel
					FROM		dims_module, dims_workspace
					WHERE		dims_workspace.id = dims_module.id_workspace
					AND		dims_module.shared = 1
					$sql_herited
					ORDER BY	dims_module.label,workspacelabel";

			$result = $db->query($select);

			while($module = $db->fetchrow($result)) {
				$modules[$module['id']]['label'] = $module['label'];
				if (is_null($module['workspacelabel'])) $workspacelabel = $_DIMS['cste']['_DIMS_LABEL_SYSTEM'];
				else $workspacelabel = $module['workspacelabel'];
				$modules[$module['id']]['description'] = "(".$_DIMS['cste']['_DIMS_LABEL_SHARED_BY']." <i>$workspacelabel</i>)";
			}
		}
		return $modules;
	}



	 function getprofiles() {

		$db = dims::getInstance()->getDb();

		$profiles = array();

		// select profiles
		$select =	"
					SELECT		dims_profile.*,
							dims_workspace.label as labelworkspace
					FROM		dims_profile
					INNER JOIN	dims_workspace
					ON		dims_profile.id_workspace = dims_workspace.id
					AND		dims_profile.id_workspace = :idworkspace
					ORDER BY	dims_workspace.label
					";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($profile = $db->fetchrow($result)) {
			$profiles[$profile['id']] = $profile;
		}

		// recuperation des profils partages
		$select =	"
		SELECT		distinct p.*
		FROM		dims_profile as p
					INNER JOIN	dims_role_profile as rp
					ON		rp.id_profile=p.id
					INNER JOIN	dims_role as r
					ON		r.id = rp.id_role
		INNER JOIN	dims_module_workspace
		ON dims_module_workspace.id_module = r.id_module
		WHERE		(p.id_workspace = :idworkspace
		OR		p.shared=1)";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($profile = $db->fetchrow($result)) {
			$profiles[$profile['id']] = $profile;
		}

		return $profiles;
	 }

	public function getDefautprofile(){
		$lstProfiles = $this->getprofiles();
		foreach($lstProfiles as $profile)
			if($profile['def'] == 1)
				return $profile;
		return 0;
	}

	function getmodulesVisibility() {
		$db = dims::getInstance()->getDb();
		$id_user=0;

		$modules = array();

		if (isset($_SESSION['dims']['userid']) && $_SESSION['dims']['userid']>0) $id_user=$_SESSION['dims']['userid'];


		$select =	"
				SELECT		dims_module_workspace.id_module AS instanceid,
						dims_module_workspace.visible
				FROM		dims_module_workspace
				WHERE		dims_module_workspace.id_workspace = :idworkspace
				";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($module = $db->fetchrow($result)) {
			$modules[$module['instanceid']] = $module;
		}

		return $modules;
	}

	function getMailingList() {
		$db = dims::getInstance()->getDb();

		$mailignlist = array();

		$select =	"
				SELECT		distinct dims_mailinglist.*,
						count(dims_mailinglist_attach.id) as cpte
				FROM		dims_mailinglist
				LEFT JOIN	dims_mailinglist_attach
				ON		dims_mailinglist_attach.id_mailinglist= dims_mailinglist.id
				WHERE		dims_mailinglist.id_workspace = :idworkspace
				GROUP BY	dims_mailinglist.id";

		$result = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($fields = $db->fetchrow($result)) {
			$mailignlist[] = $fields;
		}

		return $mailignlist;
	}

	/*
	 *
	 * name: getMailingListAttach
	 * @param  int|array $idmailing Mailling list(s) identifier(s)
	 * @return array of string mails attach to the mailling list(s)
	 */
	function getMailingListAttach($idmailing,$idobj=0) {
		$dims = dims::getInstance();
		$db = $dims->getDb();

		$blacklist = "";
		$params = array();
		if ($idobj > 0) {
			$bl = array();
			$sel_blacklist = "	SELECT	email
								FROM	dims_mailingblacklist
								WHERE	id_newsletter = :idnewsletter";

			$res_blacklist = $db->query($sel_blacklist, array(
				':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $idobj),
			));

			while ($black = $db->fetchrow($res_blacklist)) {
				$bl[] = "'".$black['email']."'";
			}

			if(!empty($bl)) {
				$blacklist = 'AND dims_mailinglist_attach.email NOT IN ('.$db->getParamsFromArray($bl, 'blacklistedemail', $params).')';
			}
		}

		if(is_array($idmailing)) {
			$where_mailling_id = 'AND dims_mailinglist.id IN ('.$db->getParamsFromArray($idmailing, 'idmailinglist', $params).')';
		}
		else {
			$where_mailling_id = 'AND dims_mailinglist.id = :idmailinglist';
			$params[':idmailinglist'] = array('type' => PDO::PARAM_INT, 'value' => $idmailing);
		}

		$mailignlist = array();

		$select =	"
				SELECT		distinct dims_mailinglist_attach.*
				FROM		dims_mailinglist_attach
				INNER JOIN	dims_mailinglist
				ON		dims_mailinglist_attach.id_mailinglist= dims_mailinglist.id
				WHERE		dims_mailinglist.id_workspace = :idworkspace
				$blacklist".
				$where_mailling_id."
				GROUP BY	dims_mailinglist_attach.email";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$mailignlist[] = $fields;
		}

		return $mailignlist;
	}

	function getAllWorkspace() {
		$db = dims::getInstance()->getDb();

		$select =	"
					SELECT	dims_workspace.*
					FROM	dims_workspace
					";

		$result = $db->query($select);
		$ar = array();
		while ($fields = $db->fetchrow($result)) {
			$ar[$fields['id']] = $fields['label'];
		}

		return($ar);
	}

	// ajout Pat : 23/05/09
	function createBackground() {
	    require_once DIMS_APP_PATH."include/class_input_validator.php";
		$filesExt	= array('gif','jpg','jpeg','png');
		$path		= DIMS_APP_PATH."data/workspaces/";
		$image		= "background_".$this->fields['id']."_".date("YmdHis");

		dims_makedir($path);

		if($_FILES && isset($_FILES['background']['name']) && ($_FILES['background']['name']!=""))	{
			$imageExt = strtolower(substr(strrchr($_FILES['background']['name'], "."),1));

			// On supprime l'ancien fond s'il y en a un
			if(file_exists($path.$this->fields['background'])){
				unlink($path.$this->fields['background']);
			}

			// On v�rifie que le fichier est bien upload�
			if(!is_uploaded_file($_FILES['background']['tmp_name'])) {
				echo 'Le fond d\'&eacute;cran est introuvable';
			}

			// On v�rifie maintenant que c'est une image
		    $valid = new \InVal\FileValidator('background');
            $valid->rule(new \InVal\Rule\Image(true));

            if (!$valid->validate()) {
				echo 'Le fond d\'&eacute;cran n\'est pas une image';
			} else {
    			// On copie le fichier dans le dossier de destination
    			if(!move_uploaded_file($_FILES['background']['tmp_name'], $path.$image.'.'.$imageExt)) {
    				echo 'Impossible de copier le fond d\'&eacute;cran dans "'.$path.'"';
    			}

    			$this->fields['background']=$image.'.'.$imageExt;
			}

			$this->save();
		}
	}

	public function getListUsers($where, $params = array(), $pagination=false) {
		$listusers=array();

		$limit="";
		if (!$pagination) {
			pagination::liste_page($this->getListUsers($where, $params, true));
			$limit = "LIMIT :limitstart, :limitend";
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitend'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		}

		$select =	"
					SELECT		dims_user.id,
							dims_user.lastname,
							dims_user.firstname,
							dims_user.login,
							dims_user.service,
							dims_profile.label as profile,
							dims_workspace.id as idref,
							dims_workspace.label as label,
							dims_workspace_user.adminlevel
					FROM		dims_user
					INNER JOIN	dims_workspace_user
					ON		dims_workspace_user.id_user = dims_user.id
					AND		dims_workspace_user.id_workspace = :idworkspace
					INNER JOIN	dims_workspace
					ON		dims_workspace.id = dims_workspace_user.id_workspace
					LEFT JOIN	dims_profile
					ON		dims_profile.id = dims_workspace_user.id_profile

					$where
					$limit
					";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$result = $this->db->query($select, $params);

		if ($pagination) {
			return $this->db->numrows($result);
		} else {
			while ($fields = $this->db->fetchrow($result)) {
				$listusers[$fields['id']]=$fields;
			}
			return $listusers;
		}
	}

	function deleteShareObject($id_object) {
		$db = dims::getInstance()->getDb();
		if(isset($this->fields['id']) && !empty($this->fields['id']))
			$db->query("update dims_workspace_share set active=0 where id_from=:idworkspace and id_object=:idobject", array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
			));
	}

	function getWorkspaceShareObject($id_object) {
		$db = dims::getInstance()->getDb();
		global $dims;
		$lstwork=array();

		$select="select distinct id,label from dims_workspace_share as ws inner join dims_workspace as w on w.id=ws.id_to and id_from=:idworkspace and id_object=:idobject";

		$result = $this->db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
		));

		while ($fields = $this->db->fetchrow($result)) {
			$lstwork[$fields['id']]=$fields;
		}

		if (!isset($lstwork[$this->fields['id']])) {
			$lstwork[$this->fields['id']]=$this->fields;
		}
		// on filtre
		//$enabledworkspace = $dims->getAdminWorkspaces();
		//$lstwork=array_intersect($lstwork,$enabledworkspace);
		return $lstwork;
	}

	function getTiersFromWorkspace($id_object,$extand=false) {
		$db = dims::getInstance()->getDb();
		global $dims;
		$lsttiers=array();

		$lstwork=$this->getWorkspaceShareObject($id_object);
		$lstworkid=array();
		foreach ($lstwork as $id=>$w) {
			$lstworkid[$id]=$id;
		}

		$lstworkid[$this->fields['id']]=$this->fields['id'];
		$params = array();
		$select="select distinct id_tiers,id from dims_workspace where id in (".$db->getParamsFromArray($lstworkid, 'idworkspace', $param).") and id_tiers>0";

		$result = $this->db->query($select, $params);

		while ($fields = $this->db->fetchrow($result)) {
			if ($extand) $lsttiers[$fields['id_tiers']]=$fields;
			else $lsttiers[$fields['id_tiers']]=$fields['id_tiers'];
		}

		return $lsttiers;
	}

	function getMessage($field,$firstname="",$lastname="",$title="", $unsub="") {
		if (isset($this->fields[$field])) {
			$elem = array('{firstname}','{lastname}','{title}','{UNSUBSCRIBE_LINK}');
			$elemby = array(	$firstname,
								$lastname,
								$title,
						$unsub);

			return (str_replace($elem,$elemby,$this->fields[$field]));
		}
		else {
			return '';
		}
	}

	public function isUserEnabled($id_user=0) {
		$db = dims::getInstance()->getDb();

		$result =false;

		// on prend la personne connecte si =0
		if ($id_user==0 && isset($_SESSION['dims']['userid'])) {
			$id_user=$_SESSION['dims']['userid'];
		}

		// Requete
		$select =	"	SELECT		id_user
						FROM		dims_workspace_user
						WHERE		dims_workspace_user.id_workspace = :idworkspace
						AND			dims_workspace_user.id_user = :iduser";

		$res = $db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		));
		// on affecte si c bon
		$result =  $result || ($db->numrows($res)>0);

		if (!$result) {
			// on prend les groupes auxquels le user appartient on test si un des groupes est bon
			$usr = new user();
			$usr->open($id_user);
	   $grps=$usr->getgroups(true);

			if (empty($grps)) $grps[]=0;

			// on y ajoute les personnes qui proviennent des groupes affectes
			$select=	"	SELECT		dims_workspace_group.id_group
							FROM		dims_workspace_group
							WHERE		dims_workspace_group.id_workspace = :idworkspace
							AND			dims_workspace_group.id_group in (".$db->getParamsFromArray($grps, 'idgroup', $params).")";
			$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

			$res = $db->query($select, $params);
			$result = $result || ($db->numrows($res)>0);
		}

		return $result;
	}

	public function getFrontDomains() {
		$db = dims::getInstance()->getDb();
		$domains=array();

		$res=$db->query("select dims_domain.* from dims_domain inner join dims_workspace_domain on dims_workspace_domain.id_domain=dims_domain.id
				AND id_workspace=:idworkspace and (dims_workspace_domain.access=1 or dims_workspace_domain.access=2)", array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		if ($db->numrows($res)>0) {
			while ($dom=$db->fetchrow($res)) {
				if ($dom['domain']!="") {
					$domains[$dom['id']]['domain']= $dom['domain'];
			$domains[$dom['id']]['ssl']= $dom['ssl'];
					$domains[$dom['id']]['mobile']= $dom['mobile'];
				}
			}
		}


		return $domains;
	}

	public function getBackDomains() {
		$db = dims::getInstance()->getDb();
		$domains=array();

		$res=$db->query("SELECT dims_domain.* from dims_domain inner join dims_workspace_domain on dims_workspace_domain.id_domain=dims_domain.id
				AND id_workspace=:idworkspace and (dims_workspace_domain.access=0 or dims_workspace_domain.access=2)
				ORDER BY dims_workspace_domain.access", array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		if ($db->numrows($res)>0) {
			while ($dom=$db->fetchrow($res)) {
				if ($dom['domain']!="") {
					$domains[$dom['id']]= $dom['domain'];
				}
			}
		}


		return $domains;
	}

	// fonction permettant de savoir si on a un module instancié ou utilisé par le type
	public function isEnabledModuleByType($type) {
		$db = dims::getInstance()->getDb();
		$result=false;

		$select = "	SELECT		m.id
				FROM		dims_module as m
				INNER JOIN	dims_workspace as w
				ON		w.id = m.id_workspace
				INNER JOIN	dims_module_type as mt
				ON		mt.id=m.id_module_type
				AND		m.label=:label
				AND		w.id=:idworkspace";

		$res=$db->query($select, array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':label' => array('type' => PDO::PARAM_STR, 'value' => $type),
			));

		if ($db->numrows($res)>0) {
			$result=true;
		}

		return $result;
	}

	// fonction permettant de collecter de l'information sur les newsletters
	public function statsNewsletter() {
		$db = dims::getInstance()->getDb();
	}

	function getUsersOpen($recherche = '', $and = '', $lstusers=false, $order = '') {
		$db = dims::getInstance()->getDb();

		$filtre = '';
		$recherche = trim($recherche);
		if ($recherche != ''){
			$filtre = " AND (dims_user.lastname LIKE '%$recherche%' OR dims_user.firstname LIKE '%$recherche%') ";
		}
		$users = array();

		$params = array();
		// Requete
		$select =	"	SELECT		dims_user.*,
							dims_workspace_user.id_profile,
							dims_workspace_user.adminlevel
					FROM		dims_user
					INNER JOIN	dims_workspace_user
					ON		dims_workspace_user.id_workspace = :idworkspace
					WHERE		dims_workspace_user.id_user = dims_user.id
					$filtre
					$and
					$order
				";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		if ($lstusers!=false && is_array($lstusers) && !empty($lstusers)) {
			$select.=" AND		dims_user.id in (".$db->getParamsFromArray($lstusers,'iduser', $params).")";
		}
		$result = $db->query($select, $params);

		foreach($db->split_resultset($result) as $fields){
			$u = new user();
			$u->openFromResultSet($fields['dims_user']);
			$u->adminlevel = $fields['dims_workspace_user']['adminlevel'];
			$users[$u->fields['id']] = $u;
		}

		$params = array();
		// on y ajoute les personnes qui proviennent des groupes affectes
		$select=	"	SELECT		dims_user.*,
							dims_workspace_group.id_profile,
							dims_workspace_group.adminlevel
					FROM		dims_user

					INNER JOIN	dims_group_user ON dims_group_user.id_user=dims_user.id";

		if ($lstusers!=false && is_array($lstusers) && !empty($lstusers)) {
			$select.="	AND			dims_user.id in (".$db->getParamsFromArray($lstusers,'iduser', $params).")";
		}

		$select.=	"	INNER JOIN	dims_workspace_group ON dims_workspace_group.id_group=dims_group_user.id_group
						INNER JOIN	dims_workspace	on dims_workspace.id = dims_workspace_group.id_workspace
						AND			dims_workspace_group.id_workspace = :idworkspace
						WHERE		1=1
						$filtre
						$and
						$order";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$result = $db->query($select, $params);

	foreach($db->split_resultset($result) as $fields){
		$u = new user();
		$u->openFromResultSet($fields['dims_user']);

		$old_level=0;
		if (isset($users[$fields['dims_user']['id']])) {
		$old_level=$users[$fields['dims_user']['id']]->adminlevel;
		if($fields['dims_workspace_group']['adminlevel']>$old_level) {
			$users[$fields['dims_user']['id']]->adminlevel = $fields['dims_workspace_group']['adminlevel'];
		}
		}else{
		$u->adminlevel = $fields['dims_workspace_group']['adminlevel'];
		$users[$u->fields['id']] = $u;
		}
		}
		return $users;
	}

	public function getFrontFavicon($template_path = '', $rec = true){
		$fav = '';
		if (file_exists(realpath('.')."/data/favicon/front_".$this->fields['id']."/favicon.ico")){
			$fav = "data/favicon/front_".$this->fields['id']."/favicon.ico";
		}elseif(file_exists(realpath('.')."/data/favicon/front_".$this->fields['id']."/favicon.png")){
			$fav = "data/favicon/front_".$this->fields['id']."/favicon.png";

		}elseif($template_path != '' && file_exists($template_path)){
			if(file_exists($template_path."/gfx/favicon.ico"))
				$fav = $template_path."/gfx/favicon.ico";
			elseif(file_exists($template_path."./common/img/favicon.ico"))
				$fav = $template_path."./common/img/favicon.ico";
			elseif(file_exists($template_path."/images/favicon.ico"))
				$fav = $template_path."/images/favicon.ico";
			elseif(file_exists($template_path."/favicon.ico"))
				$fav = $template_path."/favicon.ico";
			elseif(file_exists($template_path."/gfx/favicon.png"))
				$fav = $template_path."/gfx/favicon.png";
			elseif(file_exists($template_path."./common/img/favicon.png"))
				$fav = $template_path."./common/img/favicon.png";
			elseif(file_exists($template_path."/images/favicon.png"))
				$fav = $template_path."/images/favicon.png";
			elseif(file_exists($template_path."/favicon.png"))
				$fav = $template_path."/favicon.png";
		}elseif($rec){
			$db = dims::getInstance()->db;
			$sel = "SELECT	template
					FROM	dims_workspace_template
					WHERE	id_workspace = :idworkspace";
			$res = $db->query($sel, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
			while($r = $db->fetchrow($res)){
				if(($fav = $this->getFrontFavicon('./common/templates/frontoffice'.$r['template'], false)) != '')
					break;
			}
		}
		return $fav;
	}

	public function addFrontFavicon($file){
		if (file_exists($file)){
			$ext = explode('.', $file);
			$ext = strtolower($ext[count($ext)-1]);
			if(($ext == 'jpg') || ($ext == 'jpeg') || ($ext == 'png') || ($ext == 'gif') || ($ext == 'ico')) {
				if (!file_exists(realpath('.')."/data/favicon/front_".$this->fields['id']))
					dims_makedir(realpath('.')."/data/favicon/front_".$this->fields['id']);
				dims_resizeimage($file, 0, 16, 16,'png',0,realpath('.')."/data/favicon/front_".$this->fields['id']."/favicon.png");
				if ($ext == 'ico')
					copy($file,realpath('.')."/data/favicon/front_".$this->fields['id']."/favicon.ico");
			}
			unlink($file);
		}
	}

	public function getFrontofficeTemplates() {
		$a_templates = array();

		$rs = $this->db->query('SELECT template FROM dims_workspace_template WHERE id_workspace = :idworkspace', array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($row = $this->db->fetchrow($rs)) {
			$a_templates[] = $row['template'];
		}

		return $a_templates;
	}

	public function getDefaultTemplate(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 	template
				FROM 	dims_workspace_template
				WHERE 	id_workspace = :idw
				AND 	is_default = 1
				LIMIT 	1";
		$res = $db->query($sel,array(':idw'=>array('value'=>$this->get('id'),'type'=>PDO::PARAM_INT)));
		if($r = $db->fetchrow($res))
			return $r['template'];
		else
			return '';
	}

	public function getTiers(){
		include_once DIMS_APP_PATH."modules/system/class_tiers.php";
		$tiers = new tiers();
		if($this->fields['id_tiers'] != '' && $this->fields['id_tiers'] > 0){
			$tiers->open($this->fields['id_tiers']);
		}else{
			$tiers->init_description();
			$tiers->setugm();
		}
		return $tiers;
	}

	public function setTiers($tiers){
		if(!($tiers->fields['id'] != '' && $tiers->fields['id'] > 0)){
			$tiers->save();
		}
		$this->fields['id_tiers'] = $tiers->fields['id'];
		$this->save();
	}
}
?>
