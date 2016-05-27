<?php
/**
* @author	NETLOR
* @version	2.0
* @package	system
* @access	public
*/
require_once DIMS_APP_PATH.'modules/system/class_action_detail.php';
require_once DIMS_APP_PATH.'modules/system/class_action_user.php';
require_once DIMS_APP_PATH.'modules/system/class_action_resp.php';

// N�cessaire pour la gestion des fichers li� aux �v�nements
require_once DIMS_APP_PATH.'modules/doc/include/global.php';
require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

class action extends DIMS_DATA_OBJECT {
	const TABLE_NAME = "dims_mod_business_action";
	const FUTURE_ACTIVITY = "future_activity";
	const PAST_CLOSED_ACTIVITY = "past_closed_activity";
	const PAST_OPEN_ACTIVITY = "past_open_activity";

	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
		$this->fields['temps_duplique'] = 'non';
		$this->dossiers = array();
		$this->tiers = array();
	}

	function open() {
		$db = dims::getInstance()->getDb();
		$id=0;
		$numargs = func_num_args();

		for ($i = 0; $i < $numargs; $i++) {
			if ($i==0) $id = func_get_arg($i);
		}

		$return = parent::open($id);

		$res=$db->query("SELECT * FROM dims_mod_business_action_detail WHERE action_id = :idaction", array(
			':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id),
		));
		if ($this->fields['dossier_id'] != 0) {// action Dossier
			$this->tiers = array();
			while ($row = $db->fetchrow($res)) {
				$this->tiers[] = $row['tiers_id'];
			}
		}
		if ($this->fields['tiers_id'] != 0) {// action Tiers
			$this->dossiers = array();
			while ($row = $db->fetchrow($res)) {
				$this->dossiers[] = $row['dossier_id'];
			}
		}

		if($this->fields['type'] == dims_const::_PLANNING_ACTION_RDV)
		{
			$res=$db->query("SELECT distinct user_id FROM dims_mod_business_action_utilisateur WHERE action_id = :idaction", array(
				':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id),
			));
			$this->utilisateurs = array();
			while ($row = $db->fetchrow($res)) {
				$this->utilisateurs[$row['user_id']] = $row['user_id'];
			}
		}
		elseif($this->fields['type'] == dims_const::_PLANNING_ACTION_EVT || $this->fields['type'] == dims_const::_PLANNING_ACTION_RCT)
		{
			$res=$db->query("SELECT distinct contact_id, tiers_id, participate FROM dims_mod_business_action_detail WHERE action_id = :idaction", array(
				':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id),
			));
			$this->utilisateurs = array();
			$this->partenaires	= array();
			while ($row = $db->fetchrow($res))
			{
				if(!empty($row['contact_id']))
				{
					$this->utilisateurs[$row['contact_id']] = $row['contact_id'];
					$this->ctParticipate[$row['contact_id']] = $row['participate'];
				}
				if(!empty($row['tiers_id']))
					$this->partenaires[$row['tiers_id']] = $row['tiers_id'];
			}
		}
	return $return;
	}

	function save($arrayParticipate = array()) {
		$db = dims::getInstance()->getDb();
		$isnew=$this->new;

		if (!$this->new) {
			$res=$db->query("DELETE FROM dims_mod_business_action_detail WHERE action_id = :idaction", array(
				':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$res=$db->query("DELETE FROM dims_mod_business_action_utilisateur WHERE action_id = :idaction", array(
				':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
		}

		if (!isset($this->fields['temps_passe']) || !$this->fields['temps_passe']) {
			$hdeb = explode(':',$this->fields['heuredeb']);
			$hfin = explode(':',$this->fields['heurefin']);
			$this->fields['temps_passe'] = ($hfin[0]-$hdeb[0])*60+$hfin[1]-$hdeb[1];
			$this->fields['temps_prevu'] = $this->fields['temps_passe'];
		}

		parent::save(dims_const::_SYSTEM_OBJECT_EVENT);

		// on enregistre que si on a l'element pere => unique
		if ($this->fields['id_parent']==0) {
			// creation de l'action lie a l'action planning
			require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
			$action = new dims_action(/*$this->db*/);
			$action->setWorkspace($_SESSION['dims']['workspaceid']);
			$action->fields['id_parent']=0;
			$action->fields['timestp_modify']= dims_createtimestamp();
			$action->setUser($_SESSION['dims']['userid']);
			$action->setModule($_SESSION['dims']['moduleid']);


			if ($this->fields['type'] == dims_const::_PLANNING_ACTION_EVT) {
				$action->addObject(0, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_EVENT, $this->fields['id'],$this->fields['libelle']);
			}
			else {
				$action->addObject(0, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_ACTION, $this->fields['id'],$this->fields['libelle']);
			}
			//$action->fields['id_record']= $this->fields['id'];
			//$action->fields['id_user']= $_SESSION['dims']['userid']; // personne user liée

			if ($isnew) {
				if ($this->fields['type'] == dims_const::_PLANNING_ACTION_EVT) {
					$action->fields['comment']= '_DIMS_LABEL_CREATE_EVENT';
					$action->fields['type'] = dims_const::_ACTION_CREATE_EVENT; // link
				}
				//$action->fields['link_title'] = $this->fields['libelle']." - ".$this->fields['datejour'];
			}
			else {
				// on a deux contacts sans id_user
				if ($this->fields['type'] == dims_const::_PLANNING_ACTION_EVT) {
					$action->fields['comment']= '_DIMS_LABEL_UPDATE_EVENT';
					$action->fields['type'] = dims_const::_ACTION_MODIFY_EVENT; // link
				}
				//$action->fields['link_title'] = $this->fields['libelle']." - ".$this->fields['datejour'];
				//$action->fields['id_user']=$_SESSION['dims']['userid']; // peut etre different du userid en session
			}

			// save object action
			if(!empty($action->fields['type'])) $action->save();
		}

		if (isset($this->fields['tiers_id']) && !empty($this->fields['tiers_id'])) {// Action Tiers
			if (!isset($this->dossiers)) $this->dossiers[0] = 0;

			foreach($this->dossiers as $dossier_id) {
				$action_detail = new action_detail();
				$action_detail->fields['action_id'] = $this->fields['id'];
				$action_detail->fields['tiers_id'] = $this->fields['tiers_id'];
				if(empty($action_detail->fields['contact_id'])) $action_detail->fields['contact_id'] = 0;
				$action_detail->fields['dossier_id'] = $dossier_id;
				if ($this->fields['temps_duplique'] == 'oui') $action_detail->fields['duree'] = $this->fields['temps_passe'];
				else $action_detail->fields['duree'] = ($this->fields['temps_passe']/sizeof($this->dossiers));
				$action_detail->fields['id_module'] = $this->fields['id_module'];
				$action_detail->fields['id_user'] = $this->fields['id_user'];
				$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
				$action_detail->save();
			}
		}
		elseif (isset($this->fields['dossier_id']) && !empty($this->fields['dossier_id'])) {// Action Dossier

			if (!isset($this->tiers)) $this->tiers[0] = 0;

			foreach($this->tiers as $tiers_id) {
				$action_detail = new action_detail();
				$action_detail->fields['action_id'] = $this->fields['id'];
				$action_detail->fields['dossier_id'] = $this->fields['dossier_id'];
				if(empty($action_detail->fields['contact_id'])) $action_detail->fields['contact_id'] = 0;
				$action_detail->fields['tiers_id'] = $tiers_id;
				if ($this->fields['temps_duplique'] == 'oui') $action_detail->fields['duree'] = $this->fields['temps_passe'];
				else $action_detail->fields['duree'] = ($this->fields['temps_passe']/sizeof($this->tiers));
				$action_detail->fields['id_module'] = $this->fields['id_module'];
				$action_detail->fields['id_user'] = $this->fields['id_user'];
				$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
				$action_detail->save();
			}
		}
		elseif($this->fields['type'] == dims_const::_PLANNING_ACTION_EVT || $this->fields['type'] == dims_const::_PLANNING_ACTION_RCT) {
			foreach($arrayParticipate as $id_contact => $niveau) {
				$action_detail = new action_detail();
				$action_detail->fields['action_id'] = $this->fields['id'];
				$action_detail->fields['dossier_id'] = 0;
				$action_detail->fields['contact_id'] = $id_contact;
				$action_detail->fields['participate'] = $niveau;
				$action_detail->fields['tiers_id'] = 0;
				$action_detail->fields['duree'] = $this->fields['temps_passe'];
				$action_detail->fields['id_module'] = $this->fields['id_module'];
				$action_detail->fields['id_user'] = $this->fields['id_user'];
				$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
				$action_detail->save();
			}

			if(isset($this->partenaires) && !empty($this->partenaires)) {
				foreach($this->partenaires as $id_partenaire => $id) {
				$action_detail = new action_detail();
				$action_detail->fields['action_id'] = $this->fields['id'];
				$action_detail->fields['dossier_id'] = 0;
				$action_detail->fields['contact_id'] = 0;
				$action_detail->fields['participate'] = 1;
				$action_detail->fields['tiers_id'] = $id_partenaire;
				$action_detail->fields['duree'] = $this->fields['temps_passe'];
				$action_detail->fields['id_module'] = $this->fields['id_module'];
				$action_detail->fields['id_user'] = $this->fields['id_user'];
				$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
				$action_detail->save();
				}
			}
		}
		else {// ni dossier ni tiers
			$action_detail = new action_detail();
			$action_detail->fields['action_id'] = $this->fields['id'];
			$action_detail->fields['dossier_id'] = 0;
			if(empty($action_detail->fields['contact_id'])) $action_detail->fields['contact_id'] = 0;
			$action_detail->fields['tiers_id'] = 0;
			$action_detail->fields['duree'] = $this->fields['temps_passe'];
			$action_detail->fields['id_module'] = $this->fields['id_module'];
			$action_detail->fields['id_user'] = $this->fields['id_user'];
			$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
			$action_detail->save();
		}

		if (isset($this->utilisateurs) && $this->fields['type'] == dims_const::_PLANNING_ACTION_RDV) {
			// suppression des liens avec recreation
			foreach($this->utilisateurs as $user_id) {
				$action_user = new action_user();
				$action_user->fields['action_id'] = $this->fields['id'];
				$action_user->fields['user_id'] = $user_id;

				// test si participe ou pour info
				if (isset($arrayParticipate[$user_id]) && $arrayParticipate[$user_id]) $action_user->fields['participate'] = 1;
				else $action_user->fields['participate'] = 0;
				$action_user->save();
			}
		}

		return($this->fields['id']);
	}

	function getIdDocBooklet() {
		$db = dims::getInstance()->getDb();
		$return_value = false;

		if($this->fields['type'] ==dims_const::_PLANNING_ACTION_EVT) {

			$id_module = 1;
			$id_object = dims_const::_SYSTEM_OBJECT_EVENT;
			$id_record = $this->fields['id'];

			$sql = 'SELECT		id
					FROM		dims_mod_doc_file
					WHERE		id_module= :idmodule
					AND			id_object= :idobject
					AND			id_record= :idrecord
					AND		extension LIKE CONVERT(_utf8 \'pdf\' USING latin1)';

			$res=$db->query($sql, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
				':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
				':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			));

			if ($db->numrows($res)>0)
			{
				$doc = new docfile();
				while($f=$db->fetchrow($res)) {
					$doc->open($f['id']);
					if (file_exists($doc->getfilepath())) {
						$return_value = $f['id'];
						break;
					}
				}
			}
		}

		return $return_value;
	}

	function getFilesGallery() {
		$db = dims::getInstance()->getDb();
		$return_value = array();

		if($this->fields['type'] ==dims_const::_PLANNING_ACTION_EVT) {

			$picture_extension = "'jpeg', 'jpg', 'gif', 'png', 'bmp' ";
			$id_module = 1;
			$id_object = dims_const::_SYSTEM_OBJECT_EVENT;
			$id_record = $this->fields['id'];

			$sql = 'SELECT		id
					FROM		dims_mod_doc_file
					WHERE		id_module= :idmodule
					AND			id_object= :idobject
					AND			id_record= :idrecord
					AND		extension IN ('.$picture_extension.')
					LIMIT		0,4';

			$res=$db->query($sql, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
				':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
				':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			));

			if ($db->numrows($res)>0)
			{
				while($f=$db->fetchrow($res)) {
					$return_value[] = $f['id'];
				}
			}
		}

		return $return_value;
	}

	/*
		Fait par nico donc � v�rifier
		Modifs Simon :
			Gestion de multiple enregistrement action_detail/action_user
			Gestion des fichiers rattach�s (EVT)
	*/
	function delete() {
		$db = dims::getInstance()->getDb();

		$db->query('DELETE FROM '.action_detail::TABLE_NAME.' WHERE action_id = :idaction', array(
			':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		$db->query('DELETE FROM '.action_user::TABLE_NAME.' WHERE action_id = :idaction', array(
			':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		if($this->fields['type'] == dims_const::_PLANNING_ACTION_EVT) {
			$docfile = new docfile();

			$id_module = $_SESSION['dims']['moduleid'];
			$id_object = dims_const::_SYSTEM_OBJECT_EVENT;
			$id_record = $this->fields['id'];

			$sql = "SELECT		id
					FROM		dims_mod_doc_file
					WHERE		id_module= :idmodule
					AND			id_object= :idobject
					AND			id_record= :idrecord";

			$res=$db->query($sql, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
				':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
				':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			));
			if ($db->numrows($res)>0) {
				while ($f=$db->fetchrow($res)) {
					$docfile->open($f['id']);
					$docfile->delete();
				}
			}
		}

		return(parent::delete());
	}

	public function getRespsByUsers($idonly=true) {
		$arrayusers=array();

		$st=$this->getResps();
		$arrayusers=$st['users'];

		if (!isset($st['groups']) || empty($st['groups'])) {
		$st['groups'][0]=0;
		}

		// on traite les groups
		$params = array();
		$select = "SELECT distinct id_user FROM dims_group_user WHERE id_group in (".$this->db->getParamsFromArray($st['groups'], 'idgroup', $params).") ";
		$res=$this->db->query($select, $params);
		while($f=$this->db->fetchrow($res)) {
		 $arrayusers[$f['id_user']]=$f['id_user'];
		}

		if (empty($arrayusers)) {
		$arrayusers[0]=0;
		}

		if (!$idonly) {
			$params = array();
			// on fait la recherche sur la table dims_users
			$select = "SELECT distinct u.id,u.email,u.firstname,u.lastname,c.email as cemail FROM dims_user as u
					INNER JOIN dims_mod_business_contact as c
					ON c.id = u.id_contact
					WHERE u.id in (". $this->db->getParamsFromArray($arrayusers, 'iduser', $params).") ";
			unset($arrayusers);

			$res=$this->db->query($select, $params);
			while($f=$this->db->fetchrow($res)) {
				if ($f['email'] == '' && $f['cemail']!='') {
				$f['email'] = $f['cemail'];
				unset($f['cemail']);
				}
				$arrayusers[$f['id']]=$f;
			}
		}

		// on traite maintenant les groupes
		return $arrayusers;
	}


	// fonction permettant de recuperer la liste des personnes inscrites
	public function getResps() {
		$result=array();

		//on recharge les users et contacts
		$sql = "SELECT * FROM dims_mod_business_action_resp WHERE id_action = :idaction";
		$res = $this->db->query($sql, array(
			':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		$result['users']=array();
		$result['groups']=array();

		while($value = $this->db->fetchrow($res)) {
		if ($value['id_object']==1) {
					$result['users'][$value['id_record']]=$value['id_record'];
		}
				else {
			// on a un group
			$result['groups'][$value['id_record']]=$value['id_record'];
		}
		}
		return $result;
	}

	// fonction permettant l'actualisation des personnes responsables
	public function updateResps($element) {
		// on supprime les responsables courants
		$this->db->query('DELETE from dims_mod_business_action_resp WHERE id_action = :idaction', array(
			':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if (isset($_SESSION['obj'][$element])) {
			foreach ($_SESSION['obj'][$element]['users'] as $id_usr) {
				$action_resp = new action_resp();
				$action_resp->fields['id_action']=$this->fields['id'];
				$action_resp->fields['id_object']=1;
				$action_resp->fields['id_record']=$id_usr;
				$action_resp->save();
			}

			// boucle sur les groupes
			foreach ($_SESSION['obj'][$element]['groups'] as $id_grp) {
				$action_resp = new action_resp();
				$action_resp->fields['id_action']=$this->fields['id'];
				$action_resp->fields['id_object']=2;
				$action_resp->fields['id_record']=$id_grp;
				$action_resp->save();
			}
		}
	}

	/**
	* Function getUsers
	*
	* @param
	* @access public
	**/
	function getUsers($id_module=0,$id_action=0,$id_workspace=array()) {

		// initialisation
		$users = array();

		// tests si valeurs non passees
		if ($id_module==0 && isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid']>0) $id_module=$_SESSION['dims']['moduleid'];
		if ($id_action==0 && isset($this->fields['id'])) $id_action=$this->fields['id'];
		if (empty($id_workspace)) $id_workspace[] = $_SESSION['dims']['workspaceid'];

		$params = array();
		$select =	"
				SELECT		distinct dims_workspace_user_role.id_user,
							dims_workspace_group.id_group
				FROM		dims_role_action,
							dims_role,
							dims_workspace_user_role,
							dims_workspace_group,
							dims_user
				WHERE		dims_workspace_user_role.id_role = dims_role.id
				AND			dims_user.id = dims_workspace_user_role.id_user
				AND			dims_role.id = dims_role_action.id_role
				AND			dims_role.id_module= :idmodule
				AND			dims_workspace_user_role.id_workspace IN (".$this->db->getParamsFromArray($id_workspace, 'idworkspace', $params).")
				AND			dims_role_action.id_action = :idaction";
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $id_module);
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);

		$result = $this->db->query($select, $params);

		while ($fields = $this->db->fetchrow($result)) {
			$users['users'][$fields['id_user']] = $fields['id_user'];
		}

		$params = array();
		// remontée des actions concernant le profil de l'utilisateur rattache
		// traitement du user avec profil
		$select =	"
				SELECT		distinct dims_workspace_user.id_user
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON			dims_role.id = dims_role_action.id_role
				INNER JOIN	dims_role_profile
				ON			dims_role_profile.id_role = dims_role.id
				INNER JOIN	dims_workspace_user
				ON			dims_workspace_user.id_profile = dims_role_profile.id_profile
				INNER JOIN	dims_user
				ON			dims_user.id = dims_workspace_user.id_user
				WHERE		dims_role.id_module= :idmodule
				AND			dims_role.id_workspace IN (".$this->db->getParamsFromArray($id_workspace, 'idworkspace', $params).")
				AND			dims_role_action.id_action = :idaction";
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $id_module);
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);

		$result = $this->db->query($select, $params);

		while ($fields = $this->db->fetchrow($result)) {
			if (!isset($actions['users'][$fields['id_user']])) {
				$users['users'][$fields['id_user']] = $fields['id_user'];
			}
		}

		$params = array();
		// traitement des rattachements du user à l'aide de groupes  : 2 pos. soit action avec role ou profil
		//traitement du group avec role
		$select =	"
				SELECT		distinct dims_group_user.id_user,dims_group_user.id_group
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON			dims_role.id = dims_role_action.id_role
				INNER JOIN	dims_workspace_group_role
				ON			dims_workspace_group_role.id_role = dims_role.id
				INNER JOIN	dims_group_user
				ON			dims_group_user.id_group = dims_workspace_group_role.id_group
				INNER JOIN	dims_user
				ON			dims_user.id = dims_group_user.id_user
				WHERE		dims_role.id_module= :idmodule
				AND			dims_workspace_group_role.id_workspace IN (".$this->db->getParamsFromArray($id_workspace, 'idworkspace', $params).")
				AND			dims_role_action.id_action = :idaction";
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $id_module);
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);

		$result = $this->db->query($select, $params);

		while ($fields = $this->db->fetchrow($result)) {
			if (!isset($users['users'][$fields['id_user']])) {
				$users['users'][$fields['id_user']] = $fields['id_user'];
			}

			// test sur le groupe
			if (!isset($users['groups'][$fields['id_group']])) {
				$users['groups'][$fields['id_group']] = $fields['id_group'];
			}
		}

		$params = array();
		// traitement du group avec profil
		$select =	"
				SELECT		distinct dims_group_user.id_user,
							dims_workspace_group.id_group
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON			dims_role.id = dims_role_action.id_role
				INNER JOIN	dims_role_profile
				ON			dims_role_profile.id_role = dims_role.id
				INNER JOIN	dims_workspace_group
				ON			dims_workspace_group.id_profile = dims_role_profile.id_profile
				INNER JOIN	dims_group_user
				ON			dims_group_user.id_group = dims_workspace_group.id_group
				INNER JOIN	dims_user
				ON			dims_user.id = dims_group_user.id_user
				WHERE		dims_workspace_group.id_workspace IN (".$this->db->getParamsFromArray($id_workspace, 'idworkspace', $params).")
				AND			dims_role.id_module= :idmodule
				AND			dims_role_action.id_action = :idaction";
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $id_module);
		$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);

		$result = $this->db->query($select, $params);

		while ($fields = $this->db->fetchrow($result)) {
			if (!isset($users['users'][$fields['id_user']])) {
				$users['users'][$fields['id_user']] = $fields['id_user'];
			}
			// test sur le groupe
			if (!isset($users['groups'][$fields['id_group']])) {
				$users['groups'][$fields['id_group']] = $fields['id_group'];
			}
		}

		//retour des users
		return $users;
	}

	// fonction permettant de récupérer l'ensemble des sous événements
	public function getExtendedActions() {
		$list=array();
		$params = array();
		if (array_key_exists('is', $this->fields) && $this->fields['is'] != '' && $this->fields['is'] > 0) {
			$select =	"
				SELECT		distinct *
				FROM		dims_mod_business_action
				WHERE		id_parent= :idaction";
			$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		} else {
			$select =	"
				SELECT		distinct *
				FROM		dims_mod_business_action
				WHERE		id_parent=0";
		}

		$result = $this->db->query($select, $params);

		while ($fields = $this->db->fetchrow($result)) {
			$list[$fields['id']]=$fields;
		}

		return $list;
	}

	public function getSearchableType(){
		require_once DIMS_APP_PATH . '/modules/system/class_search.php';
		switch($this->fields['type']){
			case dims_const::_PLANNING_ACTION_EVT: //fairs and missions
				if($this->fields['typeaction'] == '_DIMS_PLANNING_FAIR'){
					return search::RESULT_TYPE_FAIR;
				}
				else// if($this->fields['typeaction'] == '_DIMS_MISSIONS'){
			return search::RESULT_TYPE_MISSION;
				//}
				break;
			case dims_const::_PLANNING_ACTION_ACTIVITY ://activities
				return search::RESULT_TYPE_ACTIVITY;
				break;
		case dims_const::_PLANNING_ACTION_OPPORTUNITY:
		return search::RESULT_TYPE_OPPORTUNITY;
		break;
		}
		return null;
	}

	public function updateIdCountry($countryArray = array()) {
		if(empty($countryArray)) {
			// conversion des tags vers country
			$resu=$this->db->query('SELECT * FROM dims_country');
			$c=0;
			if ($this->db->numrows($resu)>0) {
				while($a = $this->db->fetchrow($resu)) {
					$countryArray[strtoupper($a['printable_name'])]=$a['id'];
					$countryArray[strtoupper($a['fr'])]=$a['id'];
				}
			}
		}

		$id_country = 0;
		// on split le lieu pour trouver qq chose
		$lieux=$this->fields['lieu'];
		$lieux=str_replace(array("-",";"),",",$lieux);
		$alieux=explode(',',$lieux);

		foreach ($alieux as $lieu) {
			$lieu=trim($lieu);
			$wordlength=strlen($lieu);

			if ($lieu!='') {
			if (isset($countryArray[strtoupper($lieu)])) {
				$id_country=$countryArray[strtoupper($lieu)];
			}
			else {
				// recherche du pays pour ct / entreprise
				foreach ($countryArray as $country=>$idc) {
					$res = similar_text(trim(strtoupper($lieu)) ,trim(substr($country,0,$wordlength)),$percent);

					if ($percent>=80) {
						$id_country=$idc; // on a trouve le pays
						break;
					}
				}
			}

			if ($id_country>0)
				$this->db->query('UPDATE '.self::TABLE_NAME.' SET id_country = :idcountry WHERE '.self::TABLE_NAME.'.'.$this->idfields[0].' = :idaction', array(
					':idcountry' => array('type' => PDO::PARAM_INT, 'value' => $id_country),
					':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				));
			}
		}

		return $id_country;
	}

	/*
	 *
	 */
	public function getLightContactsAndCompanies() {

	}

}
