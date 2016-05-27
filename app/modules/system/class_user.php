<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_group_user.php';
require_once DIMS_APP_PATH.'modules/system/class_workspace_user.php';
require_once DIMS_APP_PATH.'modules/system/class_contact.php';
require_once DIMS_APP_PATH.'modules/system/class_contact_layer.php';
require_once DIMS_APP_PATH.'modules/system/class_newsletter.php';
require_once DIMS_APP_PATH.'modules/system/class_newsletter_subscribed_options.php';

class user extends dims_data_object {
	const TABLE_NAME = "dims_user";

	private $myCt = null;

	const USER_ACTIF = 1;
	const USER_INACTIF = 0;
	const ID_TYPE_DELETED = 99;

	function __construct() {
		parent::dims_data_object('dims_user');
		$this->fields['date_creation'] = dims_createtimestamp();
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		$this->detachUser();
		parent::delete();
	}

	function detachUser($work=false,$group=false) {
		$db = dims::getInstance()->getDb();

		if ($group) {
			$select = "SELECT * FROM dims_group_user WHERE id_user = :iduser";
			$res=$db->query($select, array(
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			while($fields=$db->fetchrow($res)) {
				$group_user = new group_user();
				$group_user->open($fields['id_group'], $fields['id_user']);
				$group_user->delete();
			}
		}

		if ($work) {
			$select = "SELECT * FROM dims_workspace_user WHERE id_user = :iduser";
			$res=$db->query($select, array(
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			while($fields=$db->fetchrow($res)) {
				$workspace_user = new workspace_user();
				$workspace_user->open($fields['id_workspace'], $fields['id_user']);
				$workspace_user->delete();
			}
		}
	}

	/*
	* Cyril, 03/09/2012, modification des paramètres :
	* contact_instance : dans l'hypothèse où l'instance du contact qui sera créé par la fonction ne soit pas business_contact mais un instance métier
	* num_object : le global_object_code à envoyer pour dims_data_object
	*/
	function save($id_contact = -1, $contact_instance = null, $num_object = null) {
		$db = dims::getInstance()->getDb();

		if ($id_contact>-1) {
			$this->fields['id_contact']=$id_contact;
		}

		$_SESSION['dims']['contact_fields_mode'] = array();
		//on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
		$sql = "	SELECT			mf.*,mc.label as categlabel, mc.id as id_cat,
							mb.protected,mb.name as namefield,mb.label as titlefield
				FROM			dims_mod_business_meta_field as mf
				INNER JOIN		dims_mb_field as mb
				ON			mb.id=mf.id_mbfield
				RIGHT JOIN		dims_mod_business_meta_categ as mc
				ON			mf.id_metacateg=mc.id
				WHERE			mf.id_object = ".dims_const::_SYSTEM_OBJECT_CONTACT."
				AND			mc.admin=1
				AND			mf.used=1
				ORDER BY		mc.position, mf.position ";
		$rs_fields=$db->query($sql);

		$rubgen=array();
		$convmeta = array();

		while ($fields = $db->fetchrow($rs_fields)) {
			if (!isset($rubgen[$fields['id_cat']]))  {
				$rubgen[$fields['id_cat']]=array();
				$rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
				$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
				if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
			}

			// on ajoute maintenant les champs dans la liste
			$fields['use']=0;// par defaut non utilise
			$fields['enabled']=array();
			if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

			$_SESSION['dims']['contact_fields_mode'][$fields['id']]=$fields['mode'];

			// enregistrement de la conversion
			$convmeta[$fields['namefield']]=$fields['id'];
		}

		$ct_layer = new contact_layer();
		$maj_ly = 0;

		//if($this->new) {
		if( is_null($contact_instance) || empty($contact_instance) ) //Cyril : par défaut on garde business_contact
			$contact = new contact();
		else $contact = new $contact_instance; //sinon on prend le nom de la classe passé en paramètre

		$contact->init_description();
		if(!isset($this->fields['id_contact']) || $this->fields['id_contact']==0){
			$contact->fields['lastname']	= $this->fields['lastname'];
			$contact->fields['firstname']	= $this->fields['firstname'];

			$ct_layer->init_description();
			$ct_layer->fields['type_layer'] = 1;
			$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

			//cyril :sinon ça bug quand on crée un user puis un contact (projet lorient --> class_elisath_compte |> function syncFromXML)
			if(!isset($ct_layer->fields['id_user_create'])) $ct_layer->fields['id_user_create']= $_SESSION['dims']['userid'];

			if(isset($convmeta['address']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['address'])) {
						$contact->fields['address'] = $this->fields['address'];
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['address'])) {
						$ct_layer->fields['address'] = $this->fields['address'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['postalcode']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['postalcode'])) {
						$contact->fields['postalcode'] = $this->fields['postalcode'];
						$maj_ct = 1;
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['postalcode'])) {
						$ct_layer->fields['postalcode'] = $this->fields['postalcode'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['city']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['city'])) {
						$contact->fields['city'] = $this->fields['city'];
						$maj_ct = 1;
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['city'])) {
						$ct_layer->fields['city'] = $this->fields['city'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['country']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['postalcode'])) {
						$contact->fields['country'] = $this->fields['country'];
						$maj_ct = 1;
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['postalcode'])) {
						$ct_layer->fields['country'] = $this->fields['country'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['phone']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['phone'])) {
						$contact->fields['phone'] = $this->fields['phone'];
						$maj_ct = 1;
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['phone'])) {
						$ct_layer->fields['phone'] = $this->fields['phone'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['email']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['email'])) {
						$contact->fields['email'] = $this->fields['email'];
						$maj_ct = 1;
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['email'])) {
						$ct_layer->fields['email'] = $this->fields['email'];
						$maj_ly = 1;
					}
				}
			}

			$contact->fields['comments']	= (isset($this->fields['comments'])) ? $this->fields['comments'] : "";

			if(!(isset($contact->fields['id_module']) && $contact->fields['id_module'] > 0))
					$contact->fields['id_module'] = dims_const::_DIMS_MODULE_SYSTEM;

			$id_ct = $contact->save();
			$this->fields['id_contact'] = $id_ct;
			$ct_layer->fields['id'] = $id_ct;

			if($maj_ly == 1) {
				$ct_layer->save();
			}

		}
		else {
			// recherche si layer pour workspace
			$res=$db->query("SELECT id,type_layer,id_layer from dims_mod_business_contact_layer where id= :idcontact and type_layer=1 and id_layer= :idlayer", array(
				':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_contact']),
				':idlayer' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));

			if($db->numrows($res) > 0 ) {
				$sel_layer = $db->fetchrow($res);
				//on charge le layer
				$ct_layer->open($sel_layer['id'],$sel_layer['type_layer'],$sel_layer['id_layer']);
			}
			else {
				//on cree un layer
				$ct_layer->init_description();
				$ct_layer->fields['id'] = $this->fields['id_contact'];
				$ct_layer->fields['type_layer'] = 1;
				$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
			}

			// on met a jour
			$contact->open($this->fields['id_contact']);
			$contact->fields['lastname']	= $this->fields['lastname'];
			$contact->fields['firstname']	= $this->fields['firstname'];

			if(isset($convmeta['address']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['address'])) {
						$contact->fields['address'] = $this->fields['address'];
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['address'])) {
						$ct_layer->fields['address'] = $this->fields['address'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['postalcode']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['postalcode'])) {
						$contact->fields['postalcode'] = $this->fields['postalcode'];
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['postalcode'])) {
						$ct_layer->fields['postalcode'] = $this->fields['postalcode'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['city']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['city'])) {
						$contact->fields['city'] = $this->fields['city'];
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['city'])) {
						$ct_layer->fields['city'] = $this->fields['city'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['country']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['postalcode'])) {
						$contact->fields['country'] = $this->fields['country'];
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['postalcode'])) {
						$ct_layer->fields['country'] = $this->fields['country'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['phone']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['phone'])) {
						$contact->fields['phone'] = $this->fields['phone'];
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['phone'])) {
						$ct_layer->fields['phone'] = $this->fields['phone'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['mobile']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['mobile'])) {
						$contact->fields['mobile'] = $this->fields['mobile'];
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['mobile'])) {
						$ct_layer->fields['mobile'] = $this->fields['mobile'];
						$maj_ly = 1;
					}
				}
			}
			if(isset($convmeta['email']) && isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
					//c'est un champ generique -> on enregistre dans contact
					if(!empty($this->fields['email'])) {
						$contact->fields['email'] = $this->fields['email'];
					}
				}
				else {
					//c'est un champ metier -> on enregistre dans un layer
					if(!empty($this->fields['email'])) {
						$ct_layer->fields['email'] = $this->fields['email'];
						$maj_ly = 1;
					}
				}
			}

			if (empty($contact->fields['comments']))	$contact->fields['comments']	= $this->fields['comments'];

			$id_ct = $contact->save();

			if($maj_ly == 1) {
				$ct_layer->save();
			}
		}
		if( is_null($num_object) || empty($num_object) )
			$id_user =	parent::save();
		else $id_user = parent::save($num_object);

		if( !$contact->isNew() ){
			$contact->fields['account_id'] = $id_user;
			$contact->save();
		}
		return $id_user;
	}

	function getworkspaces() {
		$db = dims::getInstance()->getDb();

		$workspaces = array();
		$tabresnewsletter=array();

		// get organisation groups
		// on r�cup�re l'ensemble des groupes d'utilisateurs et leurs parents
		$groups = $this->getgroupsadmin();

		if (sizeof($groups)>0) {
			$parents = array();
			foreach($groups as $org) {
				if ($org['id']!="" && $org['id']>0) {
					$parents = array_merge($parents,explode(';',$org['parents']));
					$parents[] = $org['id'];
				}
			}

			$groups = array_keys(array_flip($parents));
			if (empty($groups)) $groups[]=0;
			$params = array();
			$select = "	SELECT		dims_workspace.*,
							dims_workspace_group.adminlevel,
							dims_workspace_group.activenewsletter,
							dims_workspace_group.id_profile,
							dims_group_user.adminlevel as guadminlevel
					FROM		dims_workspace
					LEFT JOIN	dims_workspace_group ON dims_workspace_group.id_workspace = dims_workspace.id
					INNER JOIN	dims_group_user
					ON		dims_group_user.id_group=dims_workspace_group.id_group
					AND		dims_group_user.id_user= :iduser
					WHERE		dims_workspace_group.id_group IN (".$db->getParamsFromArray($groups, 'idgroup', $params).") ";
			$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

			$result = $db->query($select, $params);

			while ($fields = $db->fetchrow($result)) {
				if (!isset($tabresnewsletter[$fields['id']])) {
					$tabresnewsletter[$fields['id']]=$fields['activenewsletter'];
				} else {
					$tabresnewsletter[$fields['id']]=$tabresnewsletter[$fields['id']] || $fields['activenewsletter'];
				}

				if ($fields['guadminlevel']>$fields['adminlevel']) {
					$fields['adminlevel']=$fields['guadminlevel'];
				}
				$workspaces[$fields['id']] = $fields;
			}
		}

		// get workspaces
		// rattachement classique entre 1 utilisateur et 1 groupe de travail
		$select = "	SELECT			dims_workspace.*,
							dims_workspace_user.adminlevel,
							dims_workspace_user.activenewsletter,
							dims_workspace_user.id_profile
				FROM			dims_workspace
				INNER JOIN		dims_workspace_user ON dims_workspace_user.id_workspace = dims_workspace.id
				WHERE			dims_workspace_user.id_user = :iduser
				ORDER BY		dims_workspace.depth, id";

		$result = $db->query($select, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($fields = $db->fetchrow($result)) {
			if (!isset($tabresnewsletter[$fields['id']])) {
				$tabresnewsletter[$fields['id']]=$fields['activenewsletter'];
			}
			else {
				$tabresnewsletter[$fields['id']]=$tabresnewsletter[$fields['id']] || $fields['activenewsletter'];
			}

			if (isset($fields['guadminlevel']) && $fields['guadminlevel']>$fields['adminlevel']) {
				$fields['adminlevel']=$fields['guadminlevel'];
			}
			$workspaces[$fields['id']] = $fields;
		}

		// vérification des newsletters actives
		foreach ($tabresnewsletter as $idw => $value) {
			$workspaces[$idw]['activenewsletter']= $workspaces[$idw]['activenewsletter'] && $value;
		}
		return $workspaces;
	}

	function getgroups($expand=false) {
		$db = dims::getInstance()->getDb();

		$groups = array();

		// if(dims_group_user.id_group=0,0,dims_group.depth) as globaldepth
		$select = "	SELECT		distinct dims_group_user.id_group,dims_group.parents
				FROM		dims_group_user
				LEFT JOIN	dims_group
				ON		dims_group_user.id_group = dims_group.id
				WHERE		dims_group_user.id_user = :iduser
				";

		$result = $db->query($select, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($fields = $db->fetchrow($result)) {
			$groups[$fields['id_group']] = $fields['id_group'];
			if ($expand) {
				foreach(explode(";",$fields['parents']) as $idgrp) {
					if ($idgrp>0) $groups[$idgrp]=$idgrp;
				}
			}
		}

		return $groups;
	}

	function getGroupsLabeled($inGroups = array()) {
		$db = dims::getInstance()->getDb();

		$groups = array();
		$params = array();

		$select =	"
					SELECT		distinct dims_group_user.id_group,
								dims_group.parents,
								dims_group.label
					FROM		dims_group_user
					LEFT JOIN	dims_group
					ON			dims_group_user.id_group = dims_group.id
					WHERE		dims_group_user.id_user = :iduser
					";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		if (!empty($inGroups) && is_array($inGroups)) {
			$select .= ' AND dims_group_user.id_group IN ('.$db->getParamsFromArray(array_keys($inGroups), 'idgroup', $params).')';
		}

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$groups[$fields['id_group']] = $fields;
		}

		return $groups;
	}

	public function getgroupadminlevel($groups = array()){
		$lvl = 0;
		if(!empty($groups)){
			$db = dims::getInstance()->getDb();
			$sel = "SELECT	MAX(adminlevel) as lvl
					FROM	dims_group_user
					WHERE	id_user = ".$this->fields['id']."
					AND		id_group IN (".implode(',',$groups).")";
			$res = $db->query($sel);
			if($r = $db->fetchrow($res))
				$lvl = $r['lvl'];
		}
		return $lvl;
	}

	function getgroupsadmin() {
		$db = dims::getInstance()->getDb();

		$groups = array();

		// if(dims_group_user.id_group=0,0,dims_group.depth) as globaldepth
		$select =	"
					SELECT		dims_group_user.id_group as idgroup,
							dims_group.*,
							dims_group.depth as globaldepth
					FROM		dims_group_user
					LEFT JOIN	dims_group
					ON		dims_group_user.id_group = dims_group.id
					WHERE		dims_group_user.id_user = :iduser
					ORDER BY	globaldepth ASC
					";

		$result = $db->query($select, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($fields = $db->fetchrow($result)) {
			// group 0 = virtual group SYSTEM
			if ($fields['idgroup'] == dims_const::_SYSTEM_SYSTEMADMIN) $fields['label'] = $_DIMS['cste']['_DIMS_LABEL_SYSTEM'];
			if ($fields['id_group']==0) $fields['globaldepth']=0;
			$groups[$fields['idgroup']] = $fields;
		}

		return $groups;
	}

	function getFirstGroups($lstusers="") {
		$db = dims::getInstance()->getDb();

		$groups = array();
		$params = array();

		if (empty($lstusers)) $lstusers[0]=0;
		if (is_array($lstusers)) {
			$select =	"
					SELECT		g.id as idgroup,g.label,g.id_group,g.parents, min(g.depth) as depth
					FROM		dims_user as u
					inner join	dims_group_user as gu on gu.id_user=u.id and u.id in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")
					inner join	dims_group as g on g.id=gu.id_group
					group by	u.id";
		}
		else {
			$select ="SELECT	g.id as idgroup,g.label,g.id_group,g.parents
					FROM		dims_user as u
					inner join	dims_group_user as gu on gu.id_user=u.id and u.id= :iduser
					inner join	dims_group as g on g.id=gu.id_group
					group by	u.id";
			$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		}

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			// group 0 = virtual group SYSTEM
			if ($fields['idgroup'] == dims_const::_SYSTEM_SYSTEMADMIN) $fields['label'] = $_DIMS['cste']['_DIMS_LABEL_SYSTEM'];
			if ($fields['id_group']==0) $fields['globaldepth']=0;
			$groups[$fields['idgroup']] = $fields;
		}

		return $groups;
	}

	function attachtogroup($groupid, $adminlevel = 0) {
		$db = dims::getInstance()->getDb();

		$group_user = new group_user();
		$group_user->fields['id_user'] = $this->fields['id'];
		$group_user->fields['id_group'] = $groupid;
		$group_user->fields['adminlevel'] = $adminlevel;
		$group_user->save();
	}

	function attachtoworkspace($workspaceid, $profileid = 0) {
		$db = dims::getInstance()->getDb();

		$params = array();
		$select = "	SELECT	*
				FROM	dims_workspace_user
				WHERE	id_user=:iduser
				AND	id_workspace = :idworkspace";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $workspaceid);
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$res=$db->query($select,$params);
		if ($db->numrows($res)==0) {
			$workspace_user = new workspace_user();
			$workspace_user->fields['id_user'] = $this->fields['id'];
			$workspace_user->fields['id_workspace'] = $workspaceid;
			$workspace_user->fields['id_profile'] = $profileid;
			$workspace_user->save();


			// search for modules
			$params = array();
			$select = "	SELECT	m.id, m.label, mt.label as moduletype
					FROM	dims_module_workspace mg,
						dims_module m,
						dims_module_type mt
					WHERE	mg.id_workspace = :idworkspace
					AND	mg.id_module = m.id
					AND	m.id_module_type = mt.id ";
			$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $workspaceid);

			$res=$db->query($select, $params);
			while ($fields = $db->fetchrow($res)) {
				$admin_userid = $this->fields['id'];
				$admin_workspaceid = $workspaceid;
				$admin_moduleid = $fields['id'];

				//echo "<br><b>� {$fields['label']} �</b> ({$fields['moduletype']})<br>";
				if (file_exists(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_user_create.php")) include(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_user_create.php");
			}
		}
	}

	public function detachtoworkspace($workspaceid){
		$db = dims::getInstance()->getDb();
		$params = array();
		$del = "DELETE FROM	dims_workspace_user
			WHERE		id_user = :iduser
			AND		id_workspace = :idworkspace";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $workspaceid);
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$db->query($del, $params);
	}

	function movetogroup($groupid, $profileid = 0) {
		$db = dims::getInstance()->getDb();

		// delete all existing group associations
		$res=$db->query("DELETE FROM dims_group_user WHERE id_user = :iduser", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		// create new group association
		$group_user = new group_user();
		$group_user->fields['id_user'] = $this->fields['id'];
		$group_user->fields['id_group'] = $groupid;
		$group_user->fields['id_profile'] = $profileid;
		$group_user->save();

		$params = array();
		// search for modules
		$select = "	SELECT	m.id, m.label, mt.label as moduletype
				FROM	dims_module_workspace mg,
					dims_module m,
					dims_module_type mt
				WHERE	mg.id_group = :idgroup
				AND	mg.id_module = m.id
				AND	m.id_module_type = mt.id ";
		$params[':idgroup'] = array('type' => PDO::PARAM_INT, 'value' => $groupid);

		$res=$db->query($select, $params);
		while ($fields = $db->fetchrow($res)) {
			$admin_userid = $this->fields['id'];
			$admin_groupid = $groupid;
			$admin_moduleid = $fields['id'];

			echo "<br><b>� {$fields['label']} �</b> ({$fields['moduletype']})<br>";
			if (file_exists(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_user_create.php")) include(DIMS_APP_PATH . "/modules/{$fields['moduletype']}/include/admin_user_create.php");
		}
	}

	function getprofile($workspaceid) {
		$db = dims::getInstance()->getDb();

		$profile=-1;

		$params = array();
		$select = "	SELECT		dims_workspace_user.id_profile
				FROM		dims_workspace_user
				WHERE		dims_workspace_user.id_user= :iduser
				AND		dims_workspace_user.id_workspace = :idworkspace
				";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $workspaceid);
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$profile = $fields['id_profile'];
		}

		return $profile;
	}

	function detachfromgroup($groupid) {
		$group_user = new group_user();
		$group_user->open($groupid,$this->fields['id']);
		$group_user->delete();
	}

	function getactions(&$actions) {
		$db = dims::getInstance()->getDb();

		$sql = "
			SELECT		DISTINCT dims_workspace_user_role.id_workspace,
					dims_workspace_user.id_workspace as id_workspace2,
					dims_role_action.id_action,
					dims_role.id_module

			FROM		dims_role_action

			INNER JOIN	dims_role
			ON		dims_role.id = dims_role_action.id_role

			LEFT JOIN	dims_workspace_user_role
			ON		dims_workspace_user_role.id_role = dims_role.id
			AND		dims_workspace_user_role.id_user = :iduser

			LEFT JOIN	dims_role_profile
			ON		dims_role_profile.id_role = dims_role.id

			LEFT JOIN	dims_workspace_user
			ON		dims_workspace_user.id_profile = dims_role_profile.id_profile
			AND		dims_workspace_user.id_user = :iduser";
		$allRows = $db->fetchAllCached("_DIMS_MODULES_SYSTEM_CLASS_USER_GETACTIONS_1", 60, $sql, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		foreach ($allRows as $fields) {
			if ($fields['id_workspace'] != null) {
				$actions[$fields['id_workspace']][$fields['id_module']][$fields['id_action']] = true;
			}
			elseif ($fields['id_workspace2'] != null) {
				$actions[$fields['id_workspace2']][$fields['id_module']][$fields['id_action']] = true;
			}
		}

		// traitement des groupes
		$grp=$this->getgroups(true);
		if (empty($grp)) $grp[0]=0;

		// traitement des rattachements du user ? l'aide de groupes  : 2 pos. soit action avec role ou profil
		//traitement du group avec role
		$params = array();
		$select =	"
				SELECT		distinct dims_workspace_group_role.id_workspace,
						dims_role_action.id_action,
						dims_role.id_module
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON		dims_role.id = dims_role_action.id_role
				INNER JOIN	dims_workspace_group_role
				ON		dims_workspace_group_role.id_role = dims_role.id
				INNER JOIN	dims_group_user
				ON		dims_group_user.id_group = dims_workspace_group_role.id_group
				AND		(dims_group_user.id_user = :iduser
				OR		dims_group_user.id_group in (".$db->getParamsFromArray($grp, 'idgroup', $params)."))";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$allRows = $db->fetchAllCached("_DIMS_APP_MODULES_SYSTEM_CLASS_USER_GETACTIONS_2", 60, $select, $params);

		foreach ($allRows as $fields) {
			$actions[$fields['id_workspace']][$fields['id_module']][$fields['id_action']] = true;
		}

		// traitement du group avec profil
		$select = "	SELECT		distinct dims_workspace_group.id_workspace,
						dims_role_action.id_action,
						dims_role.id_module
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON		dims_role.id = dims_role_action.id_role
				INNER JOIN	dims_role_profile
				ON		dims_role_profile.id_role = dims_role.id
				INNER JOIN	dims_workspace_group
				ON		dims_workspace_group.id_profile = dims_role_profile.id_profile
				INNER JOIN	dims_group_user
				ON		dims_group_user.id_group = dims_workspace_group.id_group
				AND		(dims_group_user.id_user = :iduser
				OR		dims_group_user.id_group in (".$db->getParamsFromArray($grp, 'idgroup', $params)."))";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$allRows = $db->fetchAllCached("_DIMS_APP_MODULES_SYSTEM_CLASS_GETACTIONS_3", 60, $select, $params);

		foreach ($allRows as $fields) {
			$actions[$fields['id_workspace']][$fields['id_module']][$fields['id_action']] = true;
		}
	}

	function getusersgroup($nomsearch ="",$workspaceid=0,$projectid=0,&$returngroups=null,$optionuser="",$optiongroup="") {
		$db = dims::getInstance()->getDb();
		global $dims;
		$params = array();

		$usrlist=array();
		// r�cup�ration de ts les espaces de travail
		if ($workspaceid==0) {
			$workspaces = $dims->getListWorkspaces();//array_keys($this->getworkspaces());
		} else {
			$workspaces = $workspaceid;
		}

		// r�cup�ration de ceux qui sont attach�s directement � ceux ci
		$select = "	SELECT		DISTINCT dims_workspace_user.id_user,
						dims_user.lastname,
						dims_user.firstname
				FROM		dims_workspace_user
				INNER JOIN	dims_user
				ON		dims_user.id= dims_workspace_user.id_user
				AND		dims_workspace_user.id_workspace in (".$workspaces.")";

		if ($optionuser!='') {
			$select.=" AND ".$optionuser;
		}
		if ($nomsearch!="") {
			$select .= " AND (dims_user.login like :searchedname or firstname like :searchedname or lastname like :searchedname)";
			$params[':searchedname'] = array('type' => PDO::PARAM_STR, 'value' => $nomsearch.'%');
		}

		if ($projectid>0) {
			$select .=" inner join dims_project_user on dims_project_user.id_ref=dims_user.id and dims_project_user.type=0 and dims_project_user.id_project= :idproject ";
			$params[':idproject'] = array('type' => PDO::PARAM_INT, 'value' => $projectid);
		}

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$usrlist[$fields['id_user']]=$fields['id_user'];
		}

		$lstgroup = array();
		$lstfinalgroup = array();
		// on recup�re la liste des groupes directes rattaches
		$select = "	SELECT		distinct g.id,g.id_group,g.parents
				FROM		dims_group as g
				INNER JOIN	dims_workspace_group
				ON		g.id = dims_workspace_group.id_group
				AND		dims_workspace_group.id_workspace in (".$workspaces.")";

		if ($optiongroup!='') {
			$select.=" AND ".$optiongroup;
		}

		$result = $db->query($select);

		while ($fields = $db->fetchrow($result)) {
			$lstgroup[$fields['id']]=$fields['id'];
		}

		$allgroup = array();
		$select = "	SELECT		id, id_group, parents
				FROM		dims_group
				WHERE		id>1
				ORDER BY	depth";
		$result = $db->query($select);

		while ($fields = $db->fetchrow($result)) {
			$elem=array();
			$elem['id']=$fields['id'];
			$elem['childs']=array();
			$allgroup[$elem['id']]=$elem;

			if ($fields['id_group']>0) {
				foreach(explode(";",$fields['parents']) as $idgrp) {
					$allgroup[$idgrp]['childs'][$fields['id']]=$fields['id'];
				}
			}
		}

		// on a maintenant a faire l'addition des childs
		foreach ($lstgroup as $idgrp) {
			 if ($optiongroup!='') {
				$lstfinalgroup+=$allgroup[$idgrp]['childs'];
			 }
			$lstfinalgroup[]=$idgrp; // update pat du 23/08/09
		}

		if (!empty($lstfinalgroup)) {
			// on compare si
			$lstgrpuser=array();
			$params = array();
			// récupération de ceux qui sont attachés par un groupe
			$select = "	SELECT		distinct dims_user.id, dims_user.lastname, dims_user.firstname
					FROM		dims_group_user
					INNER JOIN	dims_user
					ON		dims_user.id= dims_group_user.id_user
					AND		dims_group_user.id_group in (".$db->getParamsFromArray($lstfinalgroup, 'idgroup', $params).")";

			if ($nomsearch!="") {
				$select .= " AND (dims_user.login like :searchedname or firstname like :searchedname or lastname like :searchedname)";
				$params[':searchedname'] = array('type' => PDO::PARAM_STR, 'value' => $nomsearch.'%');
			}

			if ($projectid>0) {
				$select .=" inner join dims_project_user on dims_project_user.id_ref=dims_group_user.id_user and dims_project_user.type=0 and dims_project_user.id_project= :idproject ";
				$params[':idproject'] = array('type' => PDO::PARAM_INT, 'value' => $projectid);
			}

			if (is_array($returngroups)) {
				$returngroups=$lstfinalgroup;
			}

			$result = $db->query($select, $params);

			while ($fields = $db->fetchrow($result)) {
				$lstgrpuser[$fields['id']]=$fields['id'];
			}
			$usrlist=array_merge($usrlist,$lstgrpuser);
		}
		ksort($usrlist);

		return($usrlist);
	}

	function getFavorites($id_module = -1,$id_workspace=null,$id_object = -1, $id_record = -1) {
		$db = dims::getInstance()->getDb();

		$favorites = array();

		if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];
		if ($id_workspace == null) $id_workspace = $_SESSION['dims']['workspaceid'];

		$sql =	"SELECT * FROM dims_favorite WHERE id_user=:iduser";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);

		if ($id_module != -1) {
			$sql .= " AND id_module = :idmodule";
			$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $id_module);
		}

		if ($id_object != -1) {
			$sql .= " AND id_object = :idobject";
			$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $id_object);
		}

		if ($id_record != -1) {
			$sql .= " AND id_record = :idrecord";
			$params[':idrecord'] = array('type' => PDO::PARAM_INT, 'value' => $id_record);
		}

		if(is_numeric($id_workspace)) {
			$sql .= " AND id_workspace = :idworkspace";
			$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);
		} else {
			if(is_string($id_workspace)) {
				$id_workspace = explode(',', $id_workspace);
			}

			if(is_array($id_workspace)) {
				$sql .= ' AND id_workspace IN ('.$db->getParamsFromArray($id_workspace, 'idworkspace', $params).')';
			}
		}

		$sql.=" order by timestp";
		$res=$db->query($sql, $params);

		while ($row = $db->fetchrow($res)) {
			$favorites['list'][]=$row;
			$favorites['access'][$row['id_workspace']][$row['id_module']][$row['id_object']][$row['id_record']] = $row;
		}

		return($favorites);
	}

	function createFavorite($favorites,$id_module,$id_workspace,$id_object, $id_record,$id_user_from) {
		if (isset($favorites['access'][$id_workspace][$id_module][$id_object][$id_record]) && $favorites['access'][$id_workspace][$id_module][$id_object][$id_record]['type']>0) {
			$idfav=$favorites['access'][$id_workspace][$id_module][$id_object][$id_record]['id'];
			$value=$favorites['access'][$id_workspace][$id_module][$id_object][$id_record]['type'];
		}
		else {
			$idfav=0;
			$value=0;
		}
		$divname="fav_".$id_module."_".$id_object."_".$id_record;
		$res="<div style=\"width:18px;display:inline;\" id=\"".$divname."\">";
		$res.= $this->refreshFavorites($idfav,$value,$id_module,$id_workspace,$id_object, $id_record,$id_user_from);
		$res.="</div>";
		return $res;
	}

	function refreshFavorites($idfav,$value,$id_module,$id_workspace,$id_object, $id_record,$id_user_from) {
		$divname="fav_".$id_module."_".$id_object."_".$id_record;
		$res="";
		switch ($value) {
			//case 1: $valsuiv=2;break;
			case 1: $valsuiv=0;break;
			default: $valsuiv=1;
		}

		if ($idfav>0) {
			$res.= "<a href=\"#\" onclick=\"refreshFavorites(".$idfav.",".$this->fields['id'].",".$id_module.",".$id_workspace.",".$id_object.",".$id_record.",$valsuiv,$id_user_from,'".$divname."');\">";
			switch($value) {
				case 1:
					$res.="<img src=\"./common/img/fav1.png\"	alt=\"\"></a>";
				break;
				/*case 2:
					$res.="<img src=\"./common/img/fav2.png\"	alt=\"\"></a>";
				break;*/
				default :
					$res.="<img src=\"./common/img/fav0.png\"	alt=\"\"></a>";
				break;
			}
		}
		else {
			$res.= "<a href=\"#\" onclick=\"refreshFavorites(0,".$this->fields['id'].",".$id_module.",".$id_workspace.",".$id_object.",".$id_record.",1,$id_user_from,'".$divname."');\">";
			$res.="<img src=\"./common/img/fav0.png\"	alt=\"\"></a>";
		}
		return $res;
	}

	function getInterests() {
		$a_interests = array();
		$this->db->query("SELECT id_int FROM dims_user_interest WHERE id_user = :iduser", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($row = $this->db->fetchrow($res)) {
			$a_interests[] = $row['id_int'];
		}
		return $a_interests;
	}

	function setInterests($a_int) {
		$this->db->query("DELETE FROM dims_user_interest WHERE id_user = :iduser", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		foreach ($a_int as $id_int) {
			if (is_numeric($id_int)) {
				$this->db->query("INSERT INTO dims_user_interest SET id_user = :iduser, id_int = :idintereset", array(
					':idintereset' => array('type' => PDO::PARAM_INT, 'value' => $id_int),
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				));
			}
		}
	}

	function getWorks() {
		$a_works = array();
		$this->db->query("SELECT id_works FROM dims_user_works WHERE id_user = :iduser", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($row = $this->db->fetchrow($res)) {
			$a_works[] = $row['id_works'];
		}
		return $a_works;
	}

	function setWorks($a_works) {
		$this->db->query("DELETE FROM dims_user_works WHERE id_user = :iduser", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		foreach ($a_works as $id_work) {
			if (is_numeric($id_work)) {
				$this->db->query("INSERT INTO dims_user_works SET id_user = :iduser , id_works = :idworks ", array(
					':iduser' 	=> array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
					':idworks' 	=> array('type' => PDO::PARAM_INT, 'value' => $id_work)
				));
			}
		}
	}

	function getTasks() {
		$a_tasks = array();

		$sql = "select		p.label as labelproject,
					t.*
			FROM		dims_project as p
			inner join	dims_task as t
			ON		t.id_project=p.id
			inner join	dims_task_user as tu
			ON		tu.id_ref= :iduser
			AND		tu.type=0
			AND		tu.id_task=t.id
			AND		t.state<2
			";

		$res=$this->db->query($sql, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($row = $this->db->fetchrow($res)) {
			$a_tasks[] = $row;
		}
		return $a_tasks;
	}

	function getProjects() {
		$a_projects = array();

		$sql = "SELECT		distinct p.id,p.label
			FROM		dims_project as p
			inner join	dims_task as t
			ON		t.id_project=p.id
			inner join	dims_task_user as tu
			ON		tu.id_ref= :idref
			AND		tu.type=0
			AND		tu.id_task=t.id
			AND		t.state<2
			";

		$res=$this->db->query($sql, array(
			':idref' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id'] ),
		));
		while ($row = $this->db->fetchrow($res)) {
			$a_projects[] = $row;
		}
		return $a_projects;
	}

	// ajout Sylvain Durand & Pat : 07/01/09
	function createPicto() {
		require_once(DIMS_APP_PATH."include/functions/image.php");
		$path=_DIMS_PATHDATA."users/";

		dims_makedir($path);

		$path=$path."icon_".str_replace("#","",$this->fields['color']).".png";

		if (!file_exists($path)) {
			$im	= imagecreatefromgif(DIMS_APP_PATH."www/common/img/icon_model.gif");

			//Lecture de la couleur (x=4 et y=10)
			$bg = imagecolorat($im, 4, 10);

			// Red�finit la couleur d'arri�re-plan
			$rgb=dims_hexaToRvb($this->fields['color']);

			imagecolorset($im, $bg, $rgb[0],$rgb[1],$rgb[2]);

			// On sauvegarde l'image
			imagepng($im, $path);
			imagedestroy($im);
		}
	}

	// ajout Arthur LAURENT & Pat : 23/04/09
	function createBackground() {
		$filesExt	= array('gif','jpg','jpeg','png');
		$path		= _DIMS_PATHDATA."users/";
		$image		= "background_".$this->fields['id'];

		dims_makedir($path);

		if($_FILES && isset($_FILES['background']['name']) && ($_FILES['background']['name']!=""))	{
			$imageExt = strtolower(substr(strrchr($_FILES['background']['name'], "."),1));

			// On supprime l'ancien fond s'il y en a un
			if(file_exists($path.$image.'.'.$imageExt)){
				unlink($path.$image.'.'.$imageExt);
			}

			// On v�rifie que le fichier est bien upload�
			if(!is_uploaded_file($_FILES['background']['tmp_name'])) {
				echo 'Le fond d\'&eacute;cran est introuvable';
			}

			require_once DIMS_APP_PATH."include/class_input_validator.php";
			$valid = new \InVal\FileValidator('background');
			$valid->rule(new \InVal\Rule\Image(true));

			if (!$valid->validate()) {
				echo 'Le fond d\'&eacute;cran n\'est pas une image';
			} else {
				// On copie le fichier dans le dossier de destination
				if(!move_uploaded_file($_FILES['background']['tmp_name'], $path.$image.'.'.$imageExt)) {
					echo 'Impossible de copier le fond d\'&eacute;cran dans "'.$path.'"';
				}
			}

			$this->fields['background']=$image.'.'.$imageExt;
			$this->save();
		}
	}

	function updateState() {
		if (isset($_SESSION['dims']['user'])) {
			$upcolor=strtoupper($_SESSION['dims']['user']['color']);
			if ($upcolor!=$this->fields['color']) {
				$this->fields['color']=$upcolor;
				$this->save();
			}
		}
		// generation du logo
		$this->createPicto();

		$usericon=_DIMS_PATHDATA."users/icon".$this->fields['id'].".png";
		if (file_exists($usericon)) $this->fields['icon']=_DIMS_PATHDATA . "users/icon".$this->fields['id'].".png";
	}

	public function updateSystemTask() {
		global $_DIMS;
		require_once(DIMS_APP_PATH . '/include/class_todo.php');

		// Partie contacts existants
		// fonction permettant de créer ou valider des tasks deja faites
		$sql_vsim = "SELECT id
			FROM	dims_mod_business_contact_import
			WHERE	id_user_create = :iduser
			AND	id_workspace = :idworkspace";

		$res_vsim = $this->db->query($sql_vsim, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		));

		$nb_sim = $this->db->numrows($res_vsim);

		// on a des similitudes à traiter
		$id_todo=0;
		$nb=0;
		$resu=$this->db->query('SELECT id from dims_todo where user_from=0 and id_object='.dims_const::_SYSTEM_OBJECT_IMPORT." and	user_to= :iduser and type=2", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if ($this->db->numrows($resu)>0) {

			while ($td=$this->db->fetchrow($resu)) {
				$nb++;
				$id_todo=$td['id'];
				$todo = new todo($this->db, $this->fields['id']);
				$todo->open($id_todo);

				if ($nb_sim>0) {
					if ($nb==1) {
						$todo->fields['content'] = $nb_sim.' '.$_DIMS['cste']['_DIMS_LABEL_IMPORT_WITH_SIM'];
						$todo->fields['date'] = dims_createtimestamp();
						$todo->save();
					}
					else {
						$todo->delete();// on delete les autres si y a
					}
				}
				else {
					$todo->delete();
				}
			} // fin du while
		}
		else {
			// on a pas encore de todo à faire
			$todo = new todo($this->db, $this->fields['id']);
			$todo->fields['user_from'] = 0;
			$todo->fields['user_to'] = $this->fields['id'];
			$todo->fields['type'] = 2;
			$todo->fields['priority'] = 2;
			$todo->fields['id_object'] = dims_const::_SYSTEM_OBJECT_IMPORT;
			$todo->fields['content'] = $nb_sim.' '.$_DIMS['cste']['_DIMS_LABEL_IMPORT_WITH_SIM'];
			$todo->fields['date'] = dims_createtimestamp();
			$todo->save();
		}
	}

	// collecte des activites de l'utilisateur
	public function getActivities($workspace='',$userid=0,$filter_type=0,&$lsttags,&$lstUsers,&$countByType) {
		global $_DIMS;
		$a = array();
		$sqlid='';
		$elems=array();
		$lsttagsstart=$lsttags;
		$tabworkspaces=array();
		$tabusers=array();
		$tabtags=array();
		$tabactions=array();

		if (!isset($_SESSION['dims']['desktop_view_date'])) $_SESSION['dims']['desktop_view_date']=1;

		switch($_SESSION['dims']['desktop_view_date']) {
			case 0;
				$jcount=2;
				break;
			case 1:
				$jcount=8;
				break;
			case 2:
				$jcount=15;
				break;
			case 3:
				$jcount=31;
				break;
			case 4:
				$jcount=90;
				break;
			default:
				$jcount=7;
				break;
		}

		if ($jcount==2) {
			$datedeb_timestp = mktime(0,0,0,date('n'),date('j')-2,date('Y'));
		}
		else {
			$datedeb_timestp = mktime(date('H'),date('i'),date('s'),date('n'),date('j')-$jcount,date('Y'));
		}

		$datedeb_jour = intval($datedeb_timestp/(60*60*24));
		$datedeb_timestp = date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL,$datedeb_timestp);

		$paramsbase = array();
		$sqlbase ="
			SELECT		am.*,
					go.id_module,
					go.id_record,
					go.id_object,
					go.title,
					go.link_title
			FROM		dims_action_matrix as am
			INNER JOIN	dims_globalobject as go
			ON		go.id=am.id_globalobject
			";

		if ($workspace==0 && $userid==0) {
			$tabusers[$this->fields['id']]=$this->fields['id'];
		}
		elseif ($workspace!='') {
			if(is_numeric($workspace)) {
				$sqlbase.= "	and	id_workspace=:idworkspace";
				$paramsbase[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $workspace);
			}
			else {
				$sqlbase.= "	and	id_workspace in (".$workspace.")";
			}
			if ($userid>0){
				$sqlbase.= "	and	id_user=:iduser";
				$paramsbase[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $userid);
			}
		}
		else {
			$sqlbase.= "	and	id_user=:iduser";
			$paramsbase[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $userid);
		}

		// filtre sur la date
		$sqlbase.= " and id_date>".$datedeb_jour;

		// filtre sur les tags
		if (!empty($lsttags)) {
			// construction de la liste des objets qui sont rattachés aux tags utilisés
			$lstglobalobjects=array();

			foreach ($lsttags as $k=>$idtag) {
				$lstobjects=array();
				// on construit la requete qui regarde les tags
				$sql="SELECT distinct g.id as id_globalobject FROM dims_globalobject as g inner join dims_tag_index as ti on g.id_module=ti.id_module and g.id_object=ti.id_object and g.id_record=ti.id_record and ti.id_tag= :idtag ";

				$res=$this->db->query($sql, array(
					':idtag' => array('type' => PDO::PARAM_INT, 'value' => $idtag),
				));
				while ($row = $this->db->fetchrow($res)) {
					$lstobjects[$row['id_globalobject']]=$row['id_globalobject'];
				}

				// on regroupe la liste de resultats
				if (empty($lstglobalobjects)) {
					$lstglobalobjects=$lstobjects;
				}
				else {
					// on fusionne les tags
					$lstglobalobjects=array_intersect($lstglobalobjects,$lstobjects);
				}
			}

			if (empty($lstglobalobjects)) {
				$lstglobalobjects[0]=0;
			}

			$sqlbase.= " and id_globalobject in (".implode(",",$lstglobalobjects).")";
		}

		$tab_object=array();
		$liste_object='0';
		$res=$this->db->query($sqlbase, $paramsbase);
		while ($row = $this->db->fetchrow($res)) {
			if (!isset($tab_object[$row['id_globalobject']])) {
				$tab_object[$row['id_globalobject']]=$row['id_globalobject'];
				$liste_object.=",".$row['id_globalobject'];
			}
		}

		// liberation des objets
		unset($tab_object);
		$tab_object=array();
		// on recherche les tags rattachés
		$sql="SELECT distinct g.id as id_globalobject,ti.id_tag FROM dims_globalobject as g inner join dims_tag_index as ti on g.id_module=ti.id_module and g.id_object=ti.id_object and g.id_record=ti.id_record and
				g.id in (".$liste_object.")";
		$res=$this->db->query($sql);
		$object_tag=array();
		while ($row = $this->db->fetchrow($res)) {
			// construction de la liste complète des tags
			if (!isset($lsttags[$row['id_tag']])) {
				$lsttags[$row['id_tag']]=$row['id_tag'];
			}

			// on doit enregistrer la correspondance entre l'objet et le tag
			if (!isset($tab_object[$row['id_globalobject']])) {
				$tab_object[$row['id_globalobject']]=array();
			}

			if (!isset($tab_object[$row['id_globalobject']][$row['id_tag']])) {
				// on ajoute le tag
				$tab_object[$row['id_globalobject']][$row['id_tag']]=$row['id_tag'];
			}
		}

		$tabaction=array();
		$sql=$sqlbase;
		if (!empty($lsttagsstart)) {
			$sql.= " and id in (".$liste_object.") ";
		}

		$res=$this->db->query($sqlbase);
		while ($row = $this->db->fetchrow($res)) {
			// test si action existe deja ou non
			if (!isset($tabaction[$row['id_action']])) {
				$elem=array();
				$elem['id']=$row['id_action'];
				$elem['id_user']=$row['id_user'];

				$elem['tags']=array();
				$elem['objects']=array();
				$tabaction[$row['id_action']]=$elem;

				$sqlidref[$row['id_action']]=$row['id_action'];
			}

			if (isset($tab_object[$row['id_globalobject']])) {
				foreach ($tab_object[$row['id_globalobject']] as $id_tag) {
					$tabaction[$row['id_action']]['tags'][$id_tag]=$id_tag;
				}
			}

			// on traite les objets
			if ($row['id_globalobject']>0 && !isset($tabaction[$row['id_action']]['tags'][$row['id_globalobject']])) {
				$elem=array();
				$elem['id']=$row['id_globalobject'];
				$elem['title']=$row['title'];
				$elem['link_title']=$row['link_title'];
				$elem['id_module']=$row['id_module'];
				$elem['id_object']=$row['id_object'];
				$elem['id_record']=$row['id_record'];
				$tabaction[$row['id_action']]['objects'][$row['id_globalobject']]=$elem;
			}

			// on traite le user
			if (!isset($lstUsers[$row['id_user']])) {
				$lstUsers[$row['id_user']]=$row['id_user'];
			}
		}

		if (empty($sqlidref)) $sqlidref[]=0;

		$sqlbase ="
			SELECT		id,id_parent,comment,timestp_modify,id_user
			FROM		dims_action";

		if ($userid>0){
			$sqlbase.= "	where	id_user=:iduser";
			$paramsbase[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $userid);
		}
		else {
			$sqlbase.= "	where 1=1";
		}
		// commencons par remonter les commentaires
		$sql=$sqlbase." and type=".dims_const::_ACTION_COMMENT." and id_parent in (".$this->db->getParamsFromArray($sqlidref, 'idparent', $paramsbase).") and timestp_modify>".$datedeb_timestp;
		if (!empty($lsttagsstart)) {
			$sql.= " and id in (".$liste_object.") ";
		}
		$sql.=" order by timestp_modify desc";

		$res=$this->db->query($sql, $paramsbase);

		while ($row = $this->db->fetchrow($res)) {
			$parent_ok=false;
			// on a des commentaires et ou autres elements rattaches
			if (!empty($lsttagsstart)) {
				if (isset($tab_object[$row['id_globalobject']])) {
					$parent_ok=true;
				}
			}
			else {
				// cas normal on ne filtre pas
				$elems[$row['id_parent']][]=$row;
				$parent_ok=true;
			}

			// test si insert ok
			if ($parent_ok) {
				if($sqlid!='') $sqlid.=',';
				$sqlid.=$row['id'];
				$sqlidref[$row['id_parent']]=$row['id_parent'];

				// on traite le user
				if (!isset($lstUsers[$row['id_user']])) {
						$lstUsers[$row['id_user']]=$row['id_user'];
				}
			}
		}

		if ($sqlid=='') $sqlid=0;

		$paramsbase = array();
		$sqlbase ="
			SELECT		*
			FROM		dims_action
			WHERE		id in (".$this->db->getParamsFromArray($sqlidref, 'idaction', $paramsbase).") and comment != '' ";

		// on traite le nombre sans filtre
		$sql=$sqlbase;

		$sql.=" order by id desc";

		// initialisation des sous catégories
		$_SESSION['dims']['sub_activities']=array();

		$res=$this->db->query($sql, $paramsbase);
		$nb_res = $this->db->numrows($res);
		while ($row = $this->db->fetchrow($res)) {
			// increment du compteur
			$type=0;
			switch ($row['type']) {
				case dims_const::_ACTION_CREATE_CONTACT:
				case dims_const::_ACTION_UPDATE_CONTACT:
				case dims_const::_ACTION_CREATE_TIERS:
				case dims_const::_ACTION_UPDATE_TIERS:
				case dims_const::_ACTION_LINK:
				case dims_const::_ACTION_UPDATE_LINK:
					$type=1;
					break;
				case dims_const::_ACTION_CREATE_EVENT:
				case dims_const::_ACTION_MODIFY_EVENT:
				case dims_const::_ACTION_DELETE_EVENT:
					$type=2;
					break;
				case dims_const::_ACTION_CREATE_DOC:
				case dims_const::_ACTION_UPDATE_DOC:
					$type=3;
					break;
				default:
					$type=4;
					break;
			}

			if (!isset($countByType[$type])) {
				$countByType[$type]=1;
			}
			else {
				$countByType[$type]++;
			}
		}

		// construction des sous elements
		if (!isset($_SESSION['dims']['desktop_filter_type'])) {
			$_SESSION['dims']['desktop_filter_type']=0;
		}

		$elem=array();
		// switch filter_type;

		switch ($_SESSION['dims']['desktop_filter_type']) {
			 case 0:

				break ;
			 case 1:
				$elem['title']=$_DIMS['cste']['_ADD']." ".$_DIMS['cste']['_DIMS_LABEL_CONTACTS'];
				$elem['link']='/admin.php?desktop_sublink=1';
				$elem['cpte']=0;
				$_SESSION['dims']['sub_activities'][1][1]=$elem;

				$elem['title']=$_DIMS['cste']['_DIMS_DATE_MODIFY']." ".$_DIMS['cste']['_DIMS_LABEL_CONTACTS'];
				$elem['link']='/admin.php?desktop_sublink=2';
				$elem['cpte']=0;
				$_SESSION['dims']['sub_activities'][1][2]=$elem;

				$elem['title']=$_DIMS['cste']['_ADD']." ".$_DIMS['cste']['_DIMS_LABEL_COMPANY'];
				$elem['link']='/admin.php?desktop_sublink=3';
				$elem['cpte']=0;
				$_SESSION['dims']['sub_activities'][1][3]=$elem;

				$elem['title']=$_DIMS['cste']['_DIMS_DATE_MODIFY']." ".$_DIMS['cste']['_DIMS_LABEL_COMPANY'];
				$elem['link']='/admin.php?desktop_sublink=4';
				$elem['cpte']=0;
				$_SESSION['dims']['sub_activities'][1][4]=$elem;

				$elem['title']=$_DIMS['cste']['_DIMS_LABEL_LINKS'];
				$elem['link']='/admin.php?desktop_sublink=5';
				$elem['cpte']=0;
				$_SESSION['dims']['sub_activities'][1][5]=$elem;
				break;
			case 2:

				break;
			case 3:

				break;
			default:
				break;
		}

		// filtre sur le type d'action que l'on veut remonter
		if ($filter_type!=0) {
			switch ($filter_type) {
				case 1 : // contacts
					$sqlbase.= " AND (type in (".dims_const::_ACTION_CREATE_CONTACT.",".dims_const::_ACTION_UPDATE_CONTACT.",".dims_const::_ACTION_CREATE_TIERS.",".dims_const::_ACTION_UPDATE_TIERS.",".dims_const::_ACTION_LINK.",".dims_const::_ACTION_UPDATE_LINK.")";
					$sqlbase.= " OR id in (".$sqlid.")) ";
					break;
				case 2: // events
					$sqlbase.= " AND (type in (".dims_const::_ACTION_CREATE_EVENT.",".dims_const::_ACTION_MODIFY_EVENT.",".dims_const::_ACTION_DELETE_EVENT.")";
					$sqlbase.= " OR id in (".$sqlid.")) ";
					break;
				case 3: // documents
					$sqlbase.= " AND (type in (".dims_const::_ACTION_CREATE_DOC.",".dims_const::_ACTION_UPDATE_DOC.")";
					$sqlbase.= " OR id in (".$sqlid.")) ";
					break;
				case 4:
					$sqlbase.= " AND (type not in (".dims_const::_ACTION_CREATE_EVENT.",".dims_const::_ACTION_MODIFY_EVENT.",".dims_const::_ACTION_DELETE_EVENT.",".dims_const::_ACTION_CREATE_CONTACT.",".dims_const::_ACTION_UPDATE_CONTACT.",".dims_const::_ACTION_CREATE_TIERS.",".dims_const::_ACTION_UPDATE_TIERS.",".dims_const::_ACTION_LINK.",".dims_const::_ACTION_UPDATE_LINK.",".dims_const::_ACTION_CREATE_DOC.",".dims_const::_ACTION_UPDATE_DOC.")";
					$sqlbase.= " OR id in (".$sqlid.")) ";
					break;
				default:
					// no filter
					if ($sqlid!='0') $sqlbase.= " OR id in (".$sqlid.") ";
					break;
			}
		}
		else {
			if ($sqlid!='0') {
				$sqlbase.= " OR id in (".$sqlid.") ";
			}
		}

		// on supprimer les type action LINK
		if (!empty($lsttagsstart)) {
			$sqlbase.= " and type not in (".dims_const::_ACTION_LINK.",".dims_const::_ACTION_UPDATE_LINK.") ";
		}


		$sql=$sqlbase." order by id_parent asc, timestp_modify desc";
		$c=0;
		$sameid=0;
		$arraycorresp=array();

		$res=$this->db->query($sql);
		while ($row = $this->db->fetchrow($res)) {
			$row['id_object']=0; // objet par defaut

			if (isset($elems[$row['id']])) {
					$row['elems']=$elems[$row['id']];
			}
			if ($row['id_parent']==0) {
				if (isset($tabaction[$row['id']])) {
					$row+=$tabaction[$row['id']]; // addition des éléments tags
					if (isset($tabaction[$row['id']]['tags']) && !empty($tabaction[$row['id']]['tags'])) {
						$row['tags']=$tabaction[$row['id']]['tags'];
					}

					if (sizeof($row['objects'])==1) {
						$el=current($row['objects']);
						$row['id_object']=$el['id'];
					}
				}
				$key=$row['type']."_".$row['id_user']."_".$row['id_workspace'];
				$row['key']=$key;
				$row['more']=0;
				$row['ref']=-1;
				$insert=true;

				// traitement du regroupement des actions faites dans dims
				if (!isset($arraycorresp[$key]) || ($_SESSION['dims']['desktop_filter_type']>0 && $_SESSION['dims']['desktop_sublink']==0)) {
					$arraycorresp[$key]=array();
					$arraycorresp[$key]['ref']=$c;
					$arraycorresp[$key]['count']=1;
					$arraycorresp[$key]['objects']=array();

					if ($row['id_object']>0) {
						$arraycorresp[$key]['objects'][$row['type']][$row['id_object']]=1;
					}
				}
				else {
					if ($row['id_object']>0 && isset($arraycorresp[$key]['objects'][$row['type']][$row['id_object']]) && $nb_res > $this->db->numrows($res)) {
						$insert=false;
						// on decrémente le nombre en fonction du type
						switch ($row['type']) {
							case dims_const::_ACTION_CREATE_CONTACT:
							case dims_const::_ACTION_UPDATE_CONTACT:
							case dims_const::_ACTION_CREATE_TIERS:
							case dims_const::_ACTION_UPDATE_TIERS:
							case dims_const::_ACTION_LINK:
							case dims_const::_ACTION_UPDATE_LINK:
								$countByType[1]--;
								break;
							case dims_const::_ACTION_CREATE_EVENT:
							case dims_const::_ACTION_MODIFY_EVENT:
							case dims_const::_ACTION_DELETE_EVENT:
								$countByType[2]--;
								break;
							case dims_const::_ACTION_CREATE_DOC:
							case dims_const::_ACTION_UPDATE_DOC:
								$countByType[3]--;
								break;
							default:
								$countByType[4]--;
								break;
						}
					}
					else {
						if ($_SESSION['dims']['desktop_sublink']==0 && empty($lsttagsstart)) {
							$ref=$arraycorresp[$key]['ref'];
							$a[$ref]['more']++;


							// permet d'afficher ou non le detail
							//if (!isset($_SESSION['dims']['desktop_more_actions'][$key])) {
								$row['ref']=$ref;
							//}
							$a[$ref]['moreobjects'][]=$row;
						}
					}
				}

				$insertfinal=true;
				// on doit insérer l'element
				if ($insert) {
					// on calcule maintenant la répartition des actions
					 switch ($_SESSION['dims']['desktop_filter_type']) {
						case 0:

							break ;
						case 1:
							 switch ($row['type']) {
								case dims_const::_ACTION_CREATE_CONTACT:
									if (!($_SESSION['dims']['desktop_sublink']==0 || $_SESSION['dims']['desktop_sublink']==1))
										$insertfinal=false;
									$_SESSION['dims']['sub_activities'][1][1]['cpte']++;
									break;

								case dims_const::_ACTION_UPDATE_CONTACT:
									if (!($_SESSION['dims']['desktop_sublink']==0 || $_SESSION['dims']['desktop_sublink']==2))
										$insertfinal=false;
									$_SESSION['dims']['sub_activities'][1][2]['cpte']++;
									break;

								case dims_const::_ACTION_CREATE_TIERS:
									if (!($_SESSION['dims']['desktop_sublink']==0 || $_SESSION['dims']['desktop_sublink']==3))
										$insertfinal=false;
									$_SESSION['dims']['sub_activities'][1][3]['cpte']++;
									break;

								case dims_const::_ACTION_UPDATE_TIERS:
									if (!($_SESSION['dims']['desktop_sublink']==0 || $_SESSION['dims']['desktop_sublink']==4))
										$insertfinal=false;
									$_SESSION['dims']['sub_activities'][1][4]['cpte']++;
									break;
								case dims_const::_ACTION_LINK:
								case dims_const::_ACTION_UPDATE_LINK:
									if (!($_SESSION['dims']['desktop_sublink']==0 || $_SESSION['dims']['desktop_sublink']==5))
										$insertfinal=false;
									$_SESSION['dims']['sub_activities'][1][5]['cpte']++;
									break;
							 }
							break;

						case dims_const::_ACTION_CREATE_EVENT:
						case dims_const::_ACTION_MODIFY_EVENT:
						case dims_const::_ACTION_DELETE_EVENT:
							$type=2;
							break;
						case dims_const::_ACTION_CREATE_DOC:
						case dims_const::_ACTION_UPDATE_DOC:
							$type=3;
							break;
						default:
							$type=4;
							break;
					}

				}
				else {
					$insertfinal=false;
				}

				// on insère quand meme la ligne
				if ($insertfinal) {
					$a[$c] = $row;
					$c++; // increment
				}

			}
		}
		unset($arraycorresp);
		// on recherche les autres tags si on a un tag sélectionné

		// traitement des tags
		// on recherche maintenant les noms des tags
		if (empty($lsttags)) $lsttags[0]=0;

		$params = array();
		$res=$this->db->query("select id,tag from dims_tag where id in (".$db->getParamsFromArray($lsttags, 'idtag', $params).")", $params);
		$lsttags=array();

		if ($this->db->numrows($res)>0) {
			while ($t=$this->db->fetchrow($res)) {
				if (isset($_DIMS['cste'][$t['tag']])) {
					$t['tag'] = $_DIMS['cste'][$t['tag']];
				}
				$lsttags[$t['id']]=$t['tag'];
			}
		}

		// traitement des users
		if (empty($lstUsers)) $lstUsers[0]=0;

		$params = array();
		$res=$this->db->query("select u.id,u.firstname,u.lastname,u.id_contact,c.photo from dims_user as u left join dims_mod_business_contact as c on c.id=u.id_contact where u.id in (".$db->getParamsFromArray($lstUsers, 'iduser', $params).")", $params);
		$lstUsers=array();

		if ($this->db->numrows($res)>0) {
			while ($t=$this->db->fetchrow($res)) {
				if ($t['photo']!='') {
					$filephoto=DIMS_WEB_PATH . 'data/photo_cts/contact_'.$t['id_contact'].'/photo60'.$t['photo'].'.png';
					if (file_exists($filephoto)) {
						$photo= $filephoto;
					}
					else $photo='';

					$t['photo']=$photo;
				}
				$lstUsers[$t['id']]=$t;
			}
		}

		return $a;
	}

	public function getDirectContacts() {
		$lstdirectusers=array();
		$params = array();

		$works=new workspace();
		$works->open($_SESSION['dims']['workspaceid']);
		$lstctrelais=$works->getusers(true);

		if (empty($lstctrelais)) {
			$lstctrelais[]=0;
		}
		$sql_pp = "	SELECT	id,
						id_contact1,
						id_contact2,
						link_level
				FROM		dims_mod_business_ct_link
				WHERE		(id_contact1  in (".$this->db->getParamsFromArray($lstctrelais, 'idcontact1', $params).") AND id_contact2= :idcontact )
				OR		(id_contact2  in (".$this->db->getParamsFromArray($lstctrelais, 'idcontact2', $params).") AND id_contact1= :idcontact )
				AND		id_object = ".dims_const::_SYSTEM_OBJECT_CONTACT."
				AND		link_level <=2
				ORDER BY time_create DESC";
		$params[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_contact']);

		//echo $sql_pp;
		$res_pp = $this->db->query($sql_pp);
		if ($this->db->numrows($res_pp)) {
			while ($f = $this->db->fetchrow($res_pp)) {

				if ($this->fields['id_contact']==$f['id_contact1']) {
					$lstdirectusers[]=$f['id_contact2'];
				}

				if ($this->fields['id_contact']==$f['id_contact2']) {
					$lstdirectusers[]=$f['id_contact1'];
				}
			}
		}
		return ($lstdirectusers);
	}

	// fonction permettant la mise des actions ou la création d'une en fonction de la personne
	public function updateActionbyTag($id_object,$id_record,$id_module) {
		$desktop_view_type=$_SESSION['dims']['desktop_view_type'];
		$desktop_filter_type=$_SESSION['dims']['desktop_filter_type'];
		$view_user_id=$_SESSION['dims']['view_user_id'];
		$chtag='';

		$res=$this->db->query("SELECT id_tag from dims_tag_index where id_record= :idrecord and id_module= :idmodule and id_object= :idobject ", array(
			':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
		));

		while($tag=$this->db->fetchrow($res)) {
			if ($chtag=='') $chtag=$tag['id_tag'];
			else $chtag.=';'.$tag['id_tag'];
		}

		if ($desktop_view_type==0) {
			$listact=$this->getActivities($_SESSION['dims']['workspaceid'],$view_user_id,$desktop_filter_type);
		}
		elseif ($desktop_view_type==1) {
			$listact=$this->getActivities($dims->getListWorkspaces(),$view_user_id,$desktop_filter_type);
		}

		$found=false;
		$idact=0;
		$link_title='';

		// boucle sur les actions du mur
		foreach ($listact as $k=>$act) {
			if ($act['id_module']==$id_module && $act['id_record']==$id_record && $act['id_object']==$id_object) {
				// soit
				if ($act['type']==dims_const::_ACTION_TAG) {
					$found=true;
					$idact=$act['id'];

				}
				if ($act['link_title']!='') {
					$link_title=$act['link_title'];
				}
			}
		}

		require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
		$action = new dims_action(/*$this->db*/);

		// on a deja une action de tag sur cette personne ou update simplement
		if ($found) {

			$action->open($idact);
			$action->fields['timestp_modify']= dims_createtimestamp();
		}
		else {
			// on crée l'action
			$action->fields['id_parent']=0;
			$action->fields['id_workspace']= $_SESSION['dims']['workspaceid'];
			$action->fields['id_module']=  $id_module;
			$action->fields['timestp_modify']= dims_createtimestamp();
			$action->fields['id_parent']=0;
			$action->fields['id_object']=$id_object;
			$action->fields['id_record']= $id_record;
			$action->fields['id_user']= $_SESSION['dims']['userid']; // personne user liée

			$action->fields['comment']= '_DIMS_LABEL_TAG_CREATED';
			$action->fields['type'] = dims_const::_ACTION_TAG; // link

			// on place le nom dans la nouvelle action
			if ($link_title=='' && isset($_SESSION['dims']['current_object']['label']) && $_SESSION['dims']['current_object']['label']!='') {
				$link_title=$_SESSION['dims']['current_object']['label'];
			}

			$action->fields['link_title'] = $link_title;
		}

		$action->fields['tags']=$chtag;
		// save object action
		$action->save();

	}

	public function getUserFromContact($id_contact=0) {
		$id_user=0;
		$sql = "SELECT	id
				FROM	dims_user
				WHERE	id_contact = :idcontact";

		$res = $this->db->query($sql, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
		));

		if ($this->db->numrows($res)>0) {
			if ($u=$this->db->fetchrow($res)) {
				$id_user=$u['id'];
			}
		}
		return $id_user;
	}

	/*
	 * Cyril - 05/07/2011 > Fonction de récupération des todo de l'utilisateur
	 * @param status : status des todos à récupérer
	*/
	public function getMyTodos($status = null, $orderby ='', $where = ''){
		$sql = "SELECT		DISTINCT dt.id
				FROM		dims_todo dt
				INNER JOIN	dims_todo_dest dtd
				ON		dtd.id_todo = dt.id
				AND		dtd.id_user= :iduser
				AND		dtd.type = ".dims_const::_SYSTEM_OBJECT_USER;
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $iduser);

		if(!is_null($status)){
			$sql .= " AND dt.state= :status ";
			$params[':status'] = array('type' => PDO::PARAM_INT, 'value' => $status);
		}

		$sql .= " ".$where;
		$params = array_merge($params, $pdo_params);

		$sql.= " ".$orderby;

		$res = $this->db->query($sql, $params);
		$todos = array();
		 while($todo = $this->db->fetchrow($res)){
			$t = new todo();
			$t->open($todo['id']);
			$todos[$todo['id']] = $t;
		}

		$sql = "SELECT	DISTINCT dt.id
				FROM		dims_todo dt
				INNER JOIN dims_todo_dest dtd
				ON		dtd.id_todo = dt.id
				AND	   dtd.type = ".dims_const::_SYSTEM_OBJECT_GROUP."
				INNER JOIN dims_group_user gu
				ON	   gu.id_group = dtd.id_user
				AND		gu.id_user= :iduser";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $iduser);
		if(!is_null($status)){
			$sql .= " AND dt.state= :status ";
			$params[':status'] = array('type' => PDO::PARAM_INT, 'value' => $status);
		}

		$sql .= " ".$where;
		$params = array_merge($params, $pdo_params);
		$sql.= " ".$orderby;

		$res = $this->db->query($sql, $params);
		while($todo = $this->db->fetchrow($res)){

			$t = new todo();
			$t->open($todo['id']);
			$todos[$todo['id']] = $t;
		 }
		ksort($todos);
		return $todos;
	}

	public function getLastname() {
		return $this->getAttribut("lastname", self::TYPE_ATTRIBUT_STRING);
	}

	public function getFirstname() {
		return $this->getAttribut("firstname", self::TYPE_ATTRIBUT_STRING);
	}

	function activate(){
		$this->fields['status'] = self::USER_ACTIF;
	}

	function deactivate(){
		$this->fields['status'] = self::USER_INACTIF;
	}

	function isActivated(){
		return ($this->fields['status'] == self::USER_ACTIF);
	}

	public function setIdType($id_type, $save = false) {
		$this->setAttribut("id_type", self::TYPE_ATTRIBUT_NUMERIC, $id_type, $save);
	}

	public function getIdType() {
		return $this->getAttribut("id_type", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getLogin() {
		return $this->getAttribut("login", self::TYPE_ATTRIBUT_STRING);
	}

	public function getContact() {
		if(is_null($this->myCt)) {
			$this->myCt = new contact();
			if(empty($this->fields['id_contact'])) {
				$this->myCt->init_description();
				$this->myCt->setugm();
			}
			else {
				$this->myCt->open($this->fields['id_contact']);
			}
		}

		return $this->myCt;
	}

	public function getProfileType() {
		return $this->fields['profile_type'];
	}

	/**
	 * Static functions
	**/
	/* Cyril --> permet de récupérer un user par son login, la colonne login étant unique */
	static public function getUserByLogin($login)
	{
		$db = dims::getInstance()->getDb();
		$sel = "SELECT id FROM dims_user WHERE login= :login ";
		$res = $db->query($sel, array(
			':login' => array('type' => PDO::PARAM_STR, 'value' => $login),
		));
		if($db->numrows($res) == 1)
		{
			$tab = $db->fetchrow($res);
			$u = new user();
			$u->open($tab['id']);
			return $u;
		}
		else return null;
	}

	/* Cyril --> 26/05/2011 permet de générer un login unique sur la base du nom /prénom */
	static public function getLoginUnique($fname, $lname)
	{
		if(!empty($fname) && !empty($lname)){
			$base = substr(strtolower($fname),0,1).user::filterStringForLogin(strtolower($lname));
			$base_loop = $base;
			$i = 1;
			while(!is_null(user::getUserByLogin($base_loop))){
				$base_loop = $base.'_'.$i;
				$i++;
			}
			return $base_loop;
		}
		else return null;
	}

	static private function filterStringForLogin($in) {
		$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ ]@i','@[^a-zA-Z0-9_]@');
		$replace = array ('e','a','i','u','o','c','','');
		return preg_replace($search, $replace, $in);
	}

	/**
	*
	* @param type $liste_id_user
	* @return user
	*/
	public static function geListeUserByIdUser($liste_id_user){
		$list_user = array();

		if(!empty($liste_id_user)){
			$db = dims::getInstance()->getDb();

			$sql = "SELECT * FROM ".self::TABLE_NAME."
			WHERE id IN (".$db->getParamsFromArray($liste_id_user, 'iduser', $params).")
			";

			$res = $db->query($sql, $params);
			while ($row = $db->fetchrow($res)) {
				$user = new user();
				$user->openWithFields($row, true);

				$list_user[$user->getId()] = $user ;
			}
		}

		return $list_user;
	}

	/**
	 *
	 * @param int $id_contact
	 * @return user
	 */
	public static function getUserByIdContact($id_contact){
		$user = null ;

		if($id_contact>0){
			$db = dims::getInstance()->getDb();

			$sql = "SELECT * FROM ".self::TABLE_NAME."
				WHERE id_contact = :idcontact";

			$res = $db->query($sql, array(
				':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
			));
			$row = $db->fetchrow($res);
			if($row) {
				$user = new user();
				$user->openWithFields($row, true);
			}

		}

		return $user;
	}

	function getNotifications($where = '') {
		$actions = $this->getIdsActions();

		$notif = array();
		if (count($actions) > 0){
			$db = dims::getInstance()->db;
			global $dims;
			$params = array();
			require_once DIMS_APP_PATH."include/class_dims_action.php";
			$sel = "SELECT		dims_action.*
				FROM		dims_action
				INNER JOIN	dims_mb_action
				ON		dims_mb_action.id_action IN (".$db->getParamsFromArray($actions, 'idaction', $params).")
				AND		dims_mb_action.notif_view = 1
				AND		dims_mb_action.id_module_type = :moduletype
				AND		dims_mb_action.id_action = dims_action.type
				WHERE		dims_action.type IN (".$db->getParamsFromArray($actions, 'idaction', $params).")
				AND		dims_action.id_module = :idmodule
				AND		dims_action.id_workspace IN (".$db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
				AND		dims_action.id_parent = 0
				$where
				ORDER BY	dims_action.timestp_modify";
			$params[':moduletype'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduletypeid']);
			$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']);
			$res = $db->query($sel, $params);
			while ($r = $db->fetchrow($res)){
				$ac = new dims_action();
				$ac->openWithFields($r);
				$notif[] = $ac;
			}
		}
		return $notif;
	}

	public function getIdsActions(){
	$actions = array();
		$actions[0] = 0;
		$db = dims::getInstance()->db;

		$params = array();
		$select =	"
				SELECT		DISTINCT dims_workspace_user_role.id_workspace,
						dims_role_action.id_action,
						dims_role.id_module
				FROM		dims_role_action,
						dims_role,
						dims_workspace_user_role
				WHERE		dims_workspace_user_role.id_role = dims_role.id
				AND		dims_role.id = dims_role_action.id_role
				AND		dims_workspace_user_role.id_user = :iduser
				AND		dims_workspace_user_role.id_workspace = :idworkspace
				AND		dims_role.id_module = :idmodule
				";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']);

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$actions[$fields['id_action']] = $fields['id_action'];
		}

		$params = array();
		// remont?e des actions concernant le profil de l'utilisateur rattache
		// traitement du user avec profil
		$select =	"
				SELECT		distinct dims_workspace_user.id_workspace,
						dims_role_action.id_action,
						dims_role.id_module
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON		dims_role.id = dims_role_action.id_role
				AND		dims_role.id_module = :idmodule
				INNER JOIN	dims_role_profile
				ON		dims_role_profile.id_role = dims_role.id
				INNER JOIN	dims_workspace_user
				ON		dims_workspace_user.id_profile = dims_role_profile.id_profile
				AND		dims_workspace_user.id_user = :iduser
				AND		dims_workspace_user.id_workspace = :idworkspace";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']);

		$result = $db->query($select);

		while ($fields = $db->fetchrow($result)) {
			$actions[$fields['id_action']] = $fields['id_action'];
		}

		// traitement des groupes
		$grp=$this->getgroups(true);
		if (empty($grp)) $grp[0]=0;

		$params = array();
		// traitement des rattachements du user ? l'aide de groupes  : 2 pos. soit action avec role ou profil
		//traitement du group avec role
		$select =	"
				SELECT		distinct dims_workspace_group_role.id_workspace,
						dims_role_action.id_action,
						dims_role.id_module
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON		dims_role.id = dims_role_action.id_role
				AND		dims_role.id_module = :idmodule
				INNER JOIN	dims_workspace_group_role
				ON		dims_workspace_group_role.id_role = dims_role.id
				AND		dims_workspace_group_role.id_workspace = :idworkspace
				INNER JOIN	dims_group_user
				ON		dims_group_user.id_group = dims_workspace_group_role.id_group
				AND		(dims_group_user.id_user = :iduser
				OR		dims_group_user.id_group in (".$db->getParamsFromArray($grp, 'idgroup', $params)."))";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']);

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$actions[$fields['id_action']] = $fields['id_action'];
		}

		$params = array();
		// traitement du group avec profil
		$select =	"
				SELECT		distinct dims_workspace_group.id_workspace,
						dims_role_action.id_action,
						dims_role.id_module
				FROM		dims_role_action
				INNER JOIN	dims_role
				ON		dims_role.id = dims_role_action.id_role
				AND		dims_role.id_module = :idmodule
				INNER JOIN	dims_role_profile
				ON		dims_role_profile.id_role = dims_role.id
				INNER JOIN	dims_workspace_group
				ON		dims_workspace_group.id_profile = dims_role_profile.id_profile
				AND		dims_workspace_group.id_workspace = :idworkspace
				INNER JOIN	dims_group_user
				ON		dims_group_user.id_group = dims_workspace_group.id_group
				AND		(dims_group_user.id_user = :iduser
				OR		dims_group_user.id_group in (".$db->getParamsFromArray($grp, 'idgroup', $params)."))";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']);

		$result = $db->query($select, $params);

		while ($fields = $db->fetchrow($result)) {
			$actions[$fields['id_action']] = $fields['id_action'];
		}

	return $actions;
	}

	public function isAllowedTo($actionid, $workspaceid, $moduleid){
		//3 possibilités soit attaché directement à l'utilisateur, soit directement au groupe de l'utilisateur, soit au profil de l'utilisateur, soit au profil du groupe
		$rs = $this->db->query('
			 SELECT wur.id_user
			 FROM dims_workspace_user_role wur
			 INNER JOIN dims_role r ON wur.id_role = r.id
			 INNER JOIN dims_role_action ra ON ra.id_role = r.id AND ra.id_action = :id_action
			 INNER JOIN dims_module m ON m.id_module_type = ra.id_module_type AND m.id = :id_module
			 WHERE wur.id_user = :id_user AND wur.id_workspace = :id_workspace
			' , array(
					':id_workspace' => array( 'value' => $workspaceid, 'type' => PDO::PARAM_INT ),
					':id_action' => array( 'value' => $actionid, 'type' => PDO::PARAM_INT ),
					':id_module' => array( 'value' => $moduleid, 'type' => PDO::PARAM_INT ),
					':id_user' => array( 'value' => $this->get('id'), 'type' => PDO::PARAM_INT )
				));

		if($this->db->numrows($rs)) return true;
		else{
			//on s'attaque au profile de l'utilisateur
			$rs = $this->db->query('
				 SELECT wu.id_user
				 FROM dims_workspace_user wu
				 INNER JOIN dims_role_profile rp ON rp.id_profile = wu.id_profile
				 INNER JOIN dims_role r ON rp.id_role = r.id
				 INNER JOIN dims_role_action ra ON ra.id_role = r.id AND ra.id_action = :id_action
				 INNER JOIN dims_module m ON m.id_module_type = ra.id_module_type AND m.id = :id_module
				 WHERE wu.id_user = :id_user AND wu.id_workspace = :id_workspace
				' , array(
						':id_workspace' => array( 'value' => $workspaceid, 'type' => PDO::PARAM_INT ),
						':id_action' => array( 'value' => $actionid, 'type' => PDO::PARAM_INT ),
						':id_module' => array( 'value' => $moduleid, 'type' => PDO::PARAM_INT ),
						':id_user' => array( 'value' => $this->get('id'), 'type' => PDO::PARAM_INT )
					));
			if($this->db->numrows($rs)) return true;
			else{
				//les profiles sur les groupes
				$rs = $this->db->query('
					 SELECT gu.id_user
					 FROM dims_group_user gu
					 INNER JOIN dims_workspace_group wg ON gu.id_group = wg.id_group AND wg.id_workspace = :id_workspace
					 INNER JOIN dims_role_profile rp ON rp.id_profile = wg.id_profile
					 INNER JOIN dims_role r ON rp.id_role = r.id
					 INNER JOIN dims_role_action ra ON ra.id_role = r.id AND ra.id_action = :id_action
					 INNER JOIN dims_module m ON m.id_module_type = ra.id_module_type AND m.id = :id_module
					 WHERE gu.id_user = :id_user
					' , array(
							':id_workspace' => array( 'value' => $workspaceid, 'type' => PDO::PARAM_INT ),
							':id_action' => array( 'value' => $actionid, 'type' => PDO::PARAM_INT ),
							':id_module' => array( 'value' => $moduleid, 'type' => PDO::PARAM_INT ),
							':id_user' => array( 'value' => $this->get('id'), 'type' => PDO::PARAM_INT )
						));
				if($this->db->numrows($rs)) return true;
				else{
					//les rôles sur les groupes
					$rs = $this->db->query('
						 SELECT gu.id_user
						 FROM dims_group_user gu
						 INNER JOIN dims_workspace_group_role wgr ON wgr.id_group = gu.id_group AND wgr.id_workspace = :id_workspace
						 INNER JOIN dims_role r ON wgr.id_role = r.id
						 INNER JOIN dims_role_action ra ON ra.id_role = r.id AND ra.id_action = :id_action
						 INNER JOIN dims_module m ON m.id_module_type = ra.id_module_type AND m.id = :id_module
						 WHERE gu.id_user = :id_user
						' , array(
								':id_workspace' => array( 'value' => $workspaceid, 'type' => PDO::PARAM_INT ),
								':id_action' => array( 'value' => $actionid, 'type' => PDO::PARAM_INT ),
								':id_module' => array( 'value' => $moduleid, 'type' => PDO::PARAM_INT ),
								':id_user' => array( 'value' => $this->get('id'), 'type' => PDO::PARAM_INT )
							));
					if($this->db->numrows($rs)) return true;
					else return false;
				}
			}
		}
	}

	public function getPhotoPath($size=60){
		if($this->get('id_contact') != '' && $this->get('id_contact') > 0){
			$ct = contact::find_by(array('id'=>$this->get('id_contact')),null,1);
			if(!empty($ct)){
				return $ct->getPhotoPath($size);
			}
		}
		return null;
	}
	public function getPhotoWebPath($size=60){
		if($this->get('id_contact') != '' && $this->get('id_contact') > 0){
			$ct = contact::find_by(array('id'=>$this->get('id_contact')),null,1);
			if(!empty($ct)){
				return $ct->getPhotoWebPath($size);
			}
		}
		return null;
	}

	public function getMyGroups(){
		$db = dims::getInstance()->getDb();
		$groups = array();
		$sel = "SELECT		g.*
				FROM		".group::TABLE_NAME." g
				INNER JOIN	".group_user::TABLE_NAME." gu
				ON			gu.id_group = g.id
				WHERE		gu.id_user = {$this->getId()}
				ORDER BY	g.depth, g.label";
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$gr = new group();
			$gr->openFromResultSet($r);
			$groups[] = $gr;
		}
		return $groups;
	}

	public function getsubscribedmailinglists() {
		$mailinglists = array();

		$sql = 'SELECT      mailinglist.*
				FROM        '.newsletter::TABLE_NAME.' mailinglist
				INNER JOIN  dims_mod_newsletter_subscribed sub
				ON          sub.id_newsletter = mailinglist.id
				WHERE       (sub.date_desinscription = ""
				OR          sub.date_desinscription IS NULL)
				AND         sub.id_contact = :idcontact';

		$res = $this->db->query($sql, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_contact']),
		));

		while($data = $this->db->fetchrow($res)) {
			if(!isset($mailinglists[$data['id']])) {
				$mailinglists[$data['id']] = new newsletter();
				$mailinglists[$data['id']]->openFromResultSet($data);
			}
		}

		return $mailinglists;
	}
}

