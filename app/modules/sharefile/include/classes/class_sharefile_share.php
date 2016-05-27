<?php
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");
require_once(DIMS_APP_PATH . "/modules/sharefile/include/classes/class_sharefile_history.php");
require_once(DIMS_APP_PATH . '/modules/sharefile/include/classes/class_sharefile_param.php');

class sharefile_share extends dims_data_object {
	const TABLE_NAME = 'dims_mod_sharefile_share';
	const MY_GLOBALOBJECT_CODE = 336;//valeur du type de globalobject pour les mod�les

	private $users = null;
	private $contacts = null;
	private $files = null;

	function __construct() {
		$this->users	= array();
		$this->contacts = array();
		$this->files = array();

		parent::dims_data_object(self::TABLE_NAME);
	}

	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	function settitle(){
		$this->title = $this->fields['label'];
	}


	function delete() {
		// suppression des fichiers rattaches
		// FIXME : Check if doc not referenced in other share before deleting !
		/*
		$sql = "SELECT id_doc FROM dims_mod_sharefile_file WHERE id_share = ".$this->fields['id'];
		$res = $this->db->query($sql);
		while($value = $this->db->fetchrow($res)) {
				$doc = new docfile();
				$doc->open($value['id_doc']);
				$doc->delete();
		}
		*/

		// il faut supprimer aussi le fichier zip

		$zip_path = realpath(DIMS_APP_PATH . '/').'/data/sharefiles';
		$zip_file=$zip_path."/".$this->fields['id']."_".$this->fields['timestp_create'].".zip";
			$zip_file_web=DIMS_APP_PATH . "/data/sharefiles/".$this->fields['id']."_".$this->fields['timestp_create'].".zip";
			if (file_exists($zip_file)) {
			unlink($zip_file);
		}
		// supp. des user
		//$db->query("delete from dims_mod_sharefile_user where id_share=".$this->fields['id']);

		// supp. des historiques
		//$db->query("delete from dims_mod_sharefile_history where id_share=".$this->fields['id']);

		$this->fields['deleted']=1;
		$this->save();
		//parent::delete();
	}

	public function isOwner($id_user) {
		return ($this->fields['id_user']==$id_user);
	}

	public function isEnabled($id_user,$id_contact,&$code) {
		$code="";
		if ($id_contact>0) {
			$sql = 'SELECT id,code FROM dims_mod_sharefile_user WHERE id_share = :idshare and id_contact= :idcontact';
			$res = $this->db->query($sql, array(':idshare' => $this->fields['id'], ':idcontact' => $id_contact) );
			if ($this->db->numrows($res)) {
				$f=$this->db->fetchrow($res);
				$code=$f['code'];
				return true;
			}
		}
		else {
			if ($id_user>0) {
				// on a certainement un id_user
				$sql = 'SELECT id,code FROM dims_mod_sharefile_user WHERE id_share = :idshare and id_user= :iduser';

				$res = $this->db->query($sql, array(':idshare' => $this->fields['id'], ':iduser' => $id_user) );
				if ($this->db->numrows($res)) {
					$f=$this->db->fetchrow($res);
					$code=$f['code'];
					return true;
				}
				else return false;
			}
			else return false;
		}
	}

	/*
	 * @deprecated
	 * See __clone()
	 */
	public function retrieveSession($moduleid) {
		$sharefile_param = new sharefile_param();
		$sharefile_param->verifParam($moduleid);
		global $db;
		unset($_SESSION['share']); //reset de la session courante
		$_SESSION['share']=array();
		$_SESSION['share']['etape']=1;
		$_SESSION['share']['users']=array();
		$_SESSION['share']['contacts']=array();
		$_SESSION['share']['code']=$this->fields['code'];
		$_SESSION['share']['title']=$this->fields['label'];
		$_SESSION['share']['descriptif']=$this->fields['description'];

		if ($this->fields['timestp_finished']==0) { // on prend le calcul par defaut
			$maxtoday = mktime(0,0,0,date('n'),date('j')+$sharefile_param->fields['nbdays'],date('Y'));
			$dateday=date('d/m/Y',$maxtoday);
			$_SESSION['share']['timestp_finished']=$dateday;
		}
		else {
			// on calcul la diff entre les deux dates
			$fin=dims_timestamp2local($this->fields['timestp_finished']);
			$creation=dims_timestamp2local($this->fields['timestp_create']);

			$date_fin=split('/',$fin['date']);
			$date_debut=split('/',$creation['date']);;

			$date_debut = mktime( 0, 0, 0, $date_debut[1], $date_debut[0], $date_debut[2] );
			$date_fin = mktime( 0, 0, 0, $date_fin[1], $date_fin[0], $date_fin[2] );

			$nbjour= (($date_fin - $date_debut) / (3600*24));

			$maxtoday = mktime(0,0,0,date('n'),date('j')+$nbjour,date('Y'));
			$dateday=date('d/m/Y',$maxtoday);

			$_SESSION['share']['timestp_finished']=$dateday;
		}
		$_SESSION['share']['currentsearch']="";

		//on recharge les users et contactst en session
		$sql = "SELECT id_user,id_group FROM dims_mod_sharefile_user WHERE id_share = :idshare and id_user>0 or id_group>0";
		$res = $this->db->query($sql, array(':idshare' => $this->fields['id']) );
		while($value = $this->db->fetchrow($res)) {
			if ($value['id_user']>0) {
				$_SESSION['share']['users'][$value['id_user']]=$value['id_user'];
			}
			else {
				// on a un group
				$_SESSION['share']['groups'][$value['id_group']]=$value['id_group'];
			}
		}

		// traitement des contacts
		$sql = "SELECT id_contact FROM dims_mod_sharefile_user WHERE id_share = :idshare and id_contact>0";
		$res = $this->db->query($sql, array(':idshare' => $this->fields['id']) );
		while($value = $this->db->fetchrow($res)) {
		   $_SESSION['share']['contacts'][$value['id_contact']]=$value['id_contact'];
		}

		// chargement des documents
		$sql = "SELECT f.* FROM dims_mod_sharefile_file as sff inner join dims_mod_doc_file as f on f.id=sff.id_doc AND sff.id_share = :idshare";

		$res = $this->db->query($sql, array(':idshare' => $this->fields['id']) );
		while($value = $this->db->fetchrow($res)) {
			//$value['downloadlink']=dims_urlencode("index.php?dims_op=doc_file_download&docfile_id=".$value['id']);
		   $value['downloadlink']=dims_urlencode("index.php?dims_op=share_file_download&id_share=".$this->fields['id']."&docfile_md5id=".$value['md5id']);
				$_SESSION['share']['files'][]=$value;
		}
		$_SESSION['share']['duplicate']=1;
	}

	public function getSize() {
		global $db;
		$lstfiles=array();
		$sql = "SELECT f.* FROM dims_mod_sharefile_file as sff inner join dims_mod_doc_file as f on f.id=sff.id_doc AND sff.id_share = :idshare ";
		$total=0;
		$nbdoc=0;
		$res = $this->db->query($sql, array(':idshare' => $this->fields['id']) );

		while($value = $this->db->fetchrow($res)) {
			$total+=$value['size'];
			$nbdoc++;
		}

		if ($this->fields['docsize']==0) {
			$this->fields['docsize']=$total;
			$this->fields['nbdoc']=$nbdoc;
			$this->save();
		}
	}

	public function createHistory($id_user,$id_contact) {
		$ch = new sharefile_history();

		$ch->fields['id_share']			= $this->fields['id'];
		if ($id_user>0) $ch->fields['id_user']=$id_user;
		if ($id_contact>0) $ch->fields['id_contact']=$id_contact;
		$ch->fields['timestp_create']	= dims_createtimestamp();
		$ch->fields['id_module']		= $this->fields['id_module'];
		$ch->fields['id_workspace']		= $this->fields['id_workspace'];

		$ch->save();

	}

	public function getFilesProperties() {
		$properties=array();
		$properties['size']=0;
		$properties['nbfiles']=0;

		$sql = "SELECT f.* FROM dims_mod_sharefile_file as sff inner join dims_mod_doc_file as f on f.id=sff.id_doc AND sff.id_share = :idshare ";

		$res = $this->db->query($sql, array(':idshare' => $this->fields['id']) );
		while($value = $this->db->fetchrow($res)) {
			$properties['nbfiles']++;
			$properties['size']+=$value['size'];
		}
		return ($properties);
	}

	public function verifAccessFile($md5_iddoc) {
		$sql = "SELECT f.id FROM dims_mod_sharefile_file as sff inner join dims_mod_doc_file as f on f.id=sff.id_doc AND sff.id_share = :idshare and f.md5id= :md5 ";
		$return=false;
		$res = $this->db->query($sql, array(':idshare' => $this->fields['id'], ':md5' => addslashes($md5_iddoc)) );
		while($value = $this->db->fetchrow($res)) {
			$return=true;
		}

		return $return;
	}

	public function addUser($id_user) {
		$this->users[$id_user] = $id_user;
	}

	public function removeUser($id_user) {
		unset($this->users[$id_user]);
	}

	public function isUserLinked($id_user) {
		return isset($this->users[$id_user]);
	}

	public function addContact($id_contact) {
		$this->contacts[$id_contact] = $id_contact;
	}

	public function removeContact($id_contact) {
		unset($this->contacts[$id_contact]);
	}

	public function isContactLinked($id_contact) {
		return isset($this->contacts[$id_contact]);
	}

	public function getUsers() {
		$users = array();

		if(!empty($this->users)) {
			$param = array();
			$sqlUsers =
				'SELECT		user.id			AS user_id,
							user.firstname	AS user_firstname,
							user.lastname	AS user_lastname,
							user.email		AS user_email,

							groupe.id		AS group_id,
							groupe.label	AS group_label,

							share_user.view AS shareuser_view,
							share_user.code AS shareuser_code,
							share_user.id	AS shareuser_id

				FROM		dims_user AS user

				LEFT JOIN	dims_group_user AS group_user
				ON			user.id = group_user.id_user

				LEFT JOIN	dims_group		AS groupe
				ON			group_user.id_group = groupe.id

				LEFT JOIN	'.sharefile_user::TABLE_NAME.' share_user
				ON			user.id = share_user.id_user';

			if(!$this->isNew()) {
				$sqlUsers.= '
					AND			share_user.id_share = :idshare ';
				$param[':idshare'] = $this->getId();
			}

			$sqlUsers.= '
				WHERE		user.id IN ('.implode(',',$this->users).')';

			$res = $this->db->query($sqlUsers, $param);

			if ($this->db->numrows($res)) {
				while ($data = $this->db->fetchrow($res)) {
					$users[$data['user_id']]['id']				= $data['user_id'];
					$users[$data['user_id']]['firstname']		= $data['user_firstname'];
					$users[$data['user_id']]['lastname']		= $data['user_lastname'];
					$users[$data['user_id']]['email']			= $data['user_email'];
					$users[$data['user_id']]['code']			= $data['shareuser_code'];
					$users[$data['user_id']]['view']			= $data['shareuser_view'];
					$users[$data['user_id']]['shareuser_id']	= $data['shareuser_id'];

					if(!empty($data['group_id'])) {
						$users[$data['user_id']]['group'][$data['group_id']]	= $data['group_label'];
					}
				}
			}
		}

		return $users;
	}

	public function getContacts() {
		$contacts = array();

		if(!empty($this->contacts)) {
			$param = array();
			$sqlContact =
				'SELECT		contact.id,
							contact.lastname,
							contact.firstname,
							contact.email,

							user.id AS id_user,

							share_user.id	AS shareuser_id,
							share_user.view AS shareuser_view,
							share_user.code AS shareuser_code

				FROM		dims_mod_business_contact contact

				LEFT JOIN	dims_user user
				ON			contact.id = user.id_contact

				LEFT JOIN	'.sharefile_user::TABLE_NAME.' share_user
				ON			contact.id = share_user.id_contact';

			if(!$this->isNew()) {
				$sqlContact.= '
					AND			share_user.id_share = :idshare ';
				$param[':idshare'] = $this->getId();
			}

			$sqlContact.= '
				WHERE		contact.id IN ('.implode(',',$this->contacts).')';

			$res = $this->db->query($sqlContact, $param);

			if ($this->db->numrows($res)) {
				while ($data = $this->db->fetchrow($res)) {
					$contacts[$data['id']]['id']			= $data['id'];
					$contacts[$data['id']]['firstname']		= $data['firstname'];
					$contacts[$data['id']]['lastname']		= $data['lastname'];
					$contacts[$data['id']]['email']			= $data['email'];
					$contacts[$data['id']]['code']			= $data['shareuser_code'];
					$contacts[$data['id']]['view']			= $data['shareuser_view'];
					$contacts[$data['id']]['shareuser_id']	= $data['shareuser_id'];
				}
			}
		}

		return $contacts;
	}

	public function addFile($id_doc) {
		$this->files[$id_doc] = $id_doc;
	}

	public function getFiles() {
		$files = array();

		if(!empty($this->files)) {
			$sqlFile = 'SELECT *
						FROM dims_mod_doc_file file
						INNER JOIN '.sharefile_file::TABLE_NAME.' share_file
						ON file.id = share_file.id_doc
						WHERE file.id IN  ('.implode(',', $this->files).')';

			$res = $this->db->query($sqlFile);

			if($this->db->query($sqlFile)) {
				$downloadId = (!empty($this->fields['id'])) ? $this->getId() : $this->clonedId;
				while($data = $this->db->fetchrow($res)) {
					$files[$data['id']] = $data;
					$files[$data['id']]['downloadlink'] = 'index.php?op=share&action=file_download&id_share='.$downloadId.'&docfile_md5id='.$data['md5id'];
				}
			}
		}

		return $files;
	}

	public function save() {
		if($this->isNew()) {
			$this->fields['timestp_create']		= dims_createtimestamp();
			$this->fields['timestp_modify']		= dims_createtimestamp();
			// $share->fields['id_module']			= $moduleid; /!\ Do *not* set - Had been set in cms.php:~195 op=share&action=add
			$this->fields['id_workspace']		= $_SESSION['dims']['workspaceid'];
			$this->fields['id_user']			= $_SESSION['dims']['userid'];
		}

		$parent_return = parent::save(self::MY_GLOBALOBJECT_CODE);

		$files_path = array();
		foreach($this->files as $id_doc) {
			$share_file = sharefile_file::openByLink($this->getId(), $id_doc);
			$share_file->save();

			$docfile = new docfile();
			$docfile->open($id_doc);
			$files_path[] = $docfile->getfilepath();
		}

		$zip_path = realpath(DIMS_APP_PATH . '/').'/data/sharefiles/';
		if (!is_dir($zip_path)) dims_makedir ($zip_path);

		$zip_file=$zip_path."/".$this->fields['id']."_".$this->fields['timestp_create'].".zip";

		if(!file_exists($zip_file) && !empty($files_path)) {
			// on va cr�er notre fichier zip
			$exec='zip -1 -r -D -j '.$zip_file.' "'.implode('" "', $files_path).'"';
			$tabres = array();
			$return=0;
			exec(escapeshellcmd($exec),$tabres,$return);
		}

		foreach($this->contacts as $id_contact) {
			$share_users = sharefile_user::openByLinkContact($this->getId(), $id_contact);
			$share_users->save();
		}

		foreach($this->users as $id_user) {
			$share_users = sharefile_user::openByLinkUser($this->getId(), $id_user);
			$share_users->save();
		}


		if($this->hasEntity()) {
			// This share has been made from gescom - Need to register matrix stuff
			require_once DIMS_APP_PATH . '/modules/system/class_tiers.php';
			$entity = new tiers();
			$entity->open($this->getEntityId());

			require_once DIMS_APP_PATH . '/modules/system/class_search.php';

			// We need the contact's entity list - See below why
			$search = new search();
			$result = $search-> exploreMatrice($_SESSION['dims']['workspaceid'],null,null,null,array($entity->fields['id_globalobject']));

			require_once DIMS_APP_PATH . '/modules/system/class_matrix.php';

			$sql = 'SELECT id_globalobject FROM '.docfile::TABLE_NAME.' WHERE id IN ('.implode(',', $this->files).')';

			$resFile = $this->db->query($sql);

			$sql = 'SELECT id_globalobject FROM '.contact::TABLE_NAME.' WHERE id IN ('.implode(',', $this->contacts).')';

			$resContact = $this->db->query($sql);

			// Init matrix object to register row
			$matrix = new matrix();
			while($file = $this->db->fetchrow($resFile)) {
				while($contact = $this->db->fetchrow($resContact)) {
					$fields = array();

					$fields['id_share'] = $this->fields['id_globalobject'];
					$fields['id_doc'] = $file['id_globalobject'];
					$fields['id_contact'] = $contact['id_globalobject'];

					// If it's a contact's entity, no matter to link with the entity in matrix
					if(!empty($result['distribution']['contacts'][$contact['id_globalobject']])) {
						$fields['id_tiers'] = $entity->fields['id_globalobject'];
					}

					$matrix->addLink($fields);
				}
			}
		}

		// calcul de la taille des fichiers
		$this->getSize();

		return $parent_return;
	}

	public function open() {
		$id_share = func_get_arg(0);
		parent::open($id_share);

		if(!$this->isNew()) {
			// Files
			$sqlFile = 'SELECT id_doc FROM '.sharefile_file::TABLE_NAME.' WHERE id_share = :idshare ';
			$res = $this->db->query($sqlFile, array(':idshare' => $this->getId()));

			while($data = $this->db->fetchrow($res)) {
				$this->files[$data['id_doc']] = $data['id_doc'];
			}

			// Contacts & user (if any)
			$sqlParticipants = 'SELECT id_contact, id_user FROM '.sharefile_user::TABLE_NAME.' WHERE id_share = :idshare ';

			$res = $this->db->query($sqlParticipants, array(':idshare' => $this->getId()));

			while($data = $this->db->fetchrow($res)) {
				if(!empty($data['id_user'])) {
					$this->users[$data['id_user']] = $data['id_user'];
				}

				if(!empty($data['id_contact'])) {
					$this->contacts[$data['id_contact']] = $data['id_contact'];
				}
			}
		}
	}

	public function __clone() {
		// This is the new object
		$this->clonedId = $this->fields['id'];
		$this->fields['id'] = 0;
		$this->setNew(true);

		// on calcul la diff entre les deux dates
		// If it's the same day, do not need to calculate the new date end
		if($this->fields['timestp_create'] != date('Ymd').'000000') {
			$fin=dims_timestamp2local($this->fields['timestp_finished']);
			$creation=dims_timestamp2local($this->fields['timestp_create']);

			$date_fin=split('/',$fin['date']);
			$date_debut=split('/',$creation['date']);;

			$date_debut = mktime( 0, 0, 0, $date_debut[1], $date_debut[0], $date_debut[2] );
			$date_fin = mktime( 0, 0, 0, $date_fin[1], $date_fin[0], $date_fin[2] );

			$nbjour= (($date_fin - $date_debut) / (3600*24));

			$maxtoday = mktime(0,0,0,date('n'),date('j')+$nbjour,date('Y'));

			$this->fields['timestp_finished'] = date('YmdHis',$maxtoday);
			$this->fields['timestp_create'] = date('Ymd').'000000';
		}
	}

	public function setEntityId($id_entity) {
		$this->fields['id_entity'] = $id_entity;
	}

	public function getEntityId() {
		return $this->fields['id_entity'];
	}

	public function hasEntity() {
		return !empty($this->fields['id_entity']);
	}
}
