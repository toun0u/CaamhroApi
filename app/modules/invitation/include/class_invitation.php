<?php
require_once DIMS_APP_PATH."modules/system/class_action.php";
class invitation extends action{
	const TYPE_ACTION = "_DIMS_EVENT_INVITATIONS";

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
		$this->fields['type'] = dims_const::_PLANNING_ACTION_INVITATION;
		$this->fields['typeaction'] = self::TYPE_ACTION;
	}

	public function save($arrayParticipate = Array()){
		$this->fields['type'] = dims_const::_PLANNING_ACTION_INVITATION;
		$this->fields['typeaction'] = self::TYPE_ACTION;
		if($this->get('ref') == '' && $this->get('id_parent') == 0){
			$this->set('ref',md5($this->get('libelle').dims_createtimestamp().uniqid()));
		}
		return parent::save();	
	}

	public function delete(){
		if($this->get("id_parent") == 0){
			// on supprime les fils
			$dates = $this->getDatesLink();
			foreach($dates as $d){
				$d->delete();
			}

			// on nettoye la matrice
			if($this->get("id_globalobject") > 0){
				require_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
				$matrices = matrix::find_by(array('id_action'=>$this->get('id_globalobject'),'id_workspace'=>$this->get('id_workspace')));
				foreach($matrices as $m){
					$m->delete();
				}
			}
			require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse.php';
			$reps = invitation_reponse::find_by(array('go_appointment'=>$this->get('id_globalobject')));
			foreach($reps as $r){
				$r->delete();
			}
		}
		return parent::delete();
	}

	function setid_object(){
		$this->id_globalobject = dims_const::_SYSTEM_OBJECT_EVENT;
	}

	function settitle(){
		$this->title = $this->fields['libelle'];
	}

	public static function getInvitation(){
		return self::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>dims_const::_PLANNING_ACTION_INVITATION, 'id_parent'=>0),' ORDER BY timestp_modify ');
	}

	public static function openByRef($ref){
		return self::find_by(array('type'=>dims_const::_PLANNING_ACTION_INVITATION, 'id_parent'=>0, 'ref'=>$ref),null,1);
	}

	public function getSimpleDatesLink(){
		$list = array();
		if($this->get('type') == dims_const::_PLANNING_ACTION_INVITATION && $this->get('id_parent') == 0){
			$child = self::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>dims_const::_PLANNING_ACTION_INVITATION, 'id_parent'=>$this->get('id')),' ORDER BY datejour, heuredeb ');
			foreach($child as $c){
				$d1 = implode('/',array_reverse(explode('-',$c->get('datejour'))));
				$d2 = implode('/',array_reverse(explode('-',$c->get('datefin'))));
				$d = $d1." ".substr($c->get('heuredeb'), 0, 5);
				if($d1 == $d2){
					$d .= " - ".substr($c->get('heurefin'), 0, 5);
				}else{
					$d .= " - ".$d2." ".substr($c->get('heurefin'), 0, 5);
				}
				$list[] = $d;
			}
		}
		return $list;
	}

	public function getDatesLink(){
		$list = array();
		if(!$this->isNew() && $this->get('type') == dims_const::_PLANNING_ACTION_INVITATION && $this->get('id_parent') == 0){
			return self::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>dims_const::_PLANNING_ACTION_INVITATION, 'id_parent'=>$this->get('id')),' ORDER BY datejour, heuredeb ');

		}
		return array();
	}

	public function getCtLinks(){
		$lst = array();
		if(!$this->isNew()){
			require_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
			require_once(DIMS_APP_PATH.'modules/system/class_contact.php');
			$sel = "SELECT 		DISTINCT ct.*
					FROM 		".contact::TABLE_NAME." ct
					INNER JOIN 	".matrix::TABLE_NAME." m
					ON 			m.id_contact = ct.id_globalobject
					WHERE 		m.id_action = :go
					AND 		m.id_workspace = :idw
					ORDER BY 	ct.firstname, ct.lastname";
			$params = array(
				':go' => array('value' => $this->get('id_globalobject'), 'type' => PDO::PARAM_INT),
				':idw' => array('value' => $_SESSION['dims']['workspaceid'], 'type' => PDO::PARAM_INT),
			);
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,$params);
			while($r = $db->fetchrow($res)){
				$ct = new contact();
				$ct->openFromResultSet($r);
				$lst[$ct->get('id')] = $ct;
			}
		}
		return $lst;
	}

	public function getFrontUrl($param = ""){
		if (!empty($param))
			return "/invitation/".$this->fields['ref']."/$param";
		else
			return "/invitation/".$this->fields['ref']."/";
	}

	public function getGoReponseVal($idct){
		require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse.php';
		require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse_value.php';
		$sel = "SELECT 		i.id_globalobject
				FROM 		".self::TABLE_NAME." i
				INNER JOIN 	".invitation_reponse_val::TABLE_NAME." rv
				ON 			rv.id_appointment = i.id
				INNER JOIN 	".invitation_reponse::TABLE_NAME." r
				ON 			r.id = rv.id_reponse
				WHERE 		r.go_appointment = :go
				AND 		r.id_contact = :idct
				AND 		i.id_parent = :id";
		$params = array(
			':go' => array('value'=>$this->get('id_globalobject'), 'type'=>PDO::PARAM_INT),
			':idct' => array('value'=>$idct, 'type'=>PDO::PARAM_INT),
			':id' => array('value'=>$this->get('id'), 'type'=>PDO::PARAM_INT),
		);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		if($r = $db->fetchrow($res))
			return $r['id_globalobject'];
		else
			return 0;
	}

	public function getCtReponse(){
		require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse.php';
		require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse_value.php';
		$sel = "SELECT 		ct.*, i.*
				FROM 		".contact::TABLE_NAME." ct
				INNER JOIN 	".invitation_reponse::TABLE_NAME." r
				ON 			r.id_contact = ct.id
				INNER JOIN 	".invitation_reponse_val::TABLE_NAME." rv
				ON 			rv.id_reponse = r.id
				INNER JOIN 	".self::TABLE_NAME." i
				ON 			i.id = rv.id_appointment
				WHERE 		r.go_appointment = :go
				AND 		i.id_parent = :id";
		$params = array(
			':go' => array('value'=>$this->get('id_globalobject'), 'type'=>PDO::PARAM_INT),
			':id' => array('value'=>$this->get('id'), 'type'=>PDO::PARAM_INT),
		);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		$lst = array();
		$tab = $db->split_resultset($res);
		foreach($tab as $r){
			$contact = new contact();
			$contact->openFromResultSet($r['ct']);
			$invi = new invitation();
			$invi->openFromResultSet($r['i']);
			$contact->setLightAttribute('reponse',$invi);
			$lst[] = $contact;
		}
		return $lst;
	}

	public function getAccompanyValues($idCt){
		require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse.php';
		require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_accompany.php';
		$sel = "SELECT 		DISTINCT ac.*
				FROM 		".invitation_accompany::TABLE_NAME." ac
				INNER JOIN 	".invitation_reponse::TABLE_NAME." r
				ON 			r.id = ac.id_reponse
				WHERE 		r.id_contact = :idct
				AND 		ac.id_action = :id
				AND 		r.go_appointment = :go
				ORDER BY 	ac.name";
		$params = array(
			':go' => array('value'=>$this->get('id_globalobject'), 'type'=>PDO::PARAM_INT),
			':idct' => array('value'=>$idCt, 'type'=>PDO::PARAM_INT),
			':id' => array('value'=>$this->get('id'), 'type'=>PDO::PARAM_INT),
		);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$a = new invitation_accompany();
			$a->openFromResultSet($r);
			$lst[] = $a;
		}
		reset($lst);
		return $lst;
	}

	public function sendMailInvitation($id = 0){
		$lstCts = $this->getCtLinks();
		$content = str_replace(array('{title}','{description}'),array($this->get('libelle'),nl2br($this->get('description'))),self::getContentMail());
		if (substr($_SERVER['SERVER_PROTOCOL'], 0, 5) == "HTTP/")
			$rootpath = "http://";
		else
			$rootpath="https://";
		$rootpath.=$_SERVER['HTTP_HOST'];
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$email = _DEBUG_EMAIL_ADDRESS;
		if($work->get('email') != '')
			$email = $work->get('email');

		if($id != '' && $id > 0){
			if(isset($lstCts[$id])){
				// on envoit le mail : $lstCts[$id]->get('email');
				dims_send_mail(
					$email,
					$lstCts[$id]->get('firstname').' '.$lstCts[$id]->get('lastname').'<'.$lstCts[$id]->get('email').'>',
					$_SESSION['cste']['_INVITATION']." : ".$this->get('libelle'),
					str_replace(array('{name}','{url}'), array($lstCts[$id]->get('firstname').' '.$lstCts[$id]->get('lastname'),$rootpath.$this->getFrontUrl("&id=".$lstCts[$id]->get('ref'))), $content)
				);
			}
		}else{
			foreach($lstCts as $ct){
				// on envoit le mail : $ct->get('email');
				dims_send_mail(
					$email,
					$ct->get('firstname').' '.$ct->get('lastname').'<'.$ct->get('email').'>',
					$_SESSION['cste']['_INVITATION']." : ".$this->get('libelle'),
					str_replace(array('{name}','{url}'), array($ct->get('firstname').' '.$ct->get('lastname'),$rootpath.$this->getFrontUrl("&id=".$ct->get('ref'))), $content)
				);
			}
		}
	}

	public static function getContentMail(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 	template
				FROM 	dims_workspace_template
				WHERE 	id_workspace = :idw";
		$params = array(
			':idw' => array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
		);
		$res = $db->query($sel,$params);
		$path = '';
		while(($r = $db->fetchrow($res)) && $path == ''){
			if(file_exists(DIMS_APP_PATH."templates/frontoffice/".$r['template']."/mail_invitation.tpl")){
				$path = DIMS_APP_PATH."templates/frontoffice/".$r['template']."/mail_invitation.tpl";
			}
		}
		if($path == ''){
			$path = DIMS_APP_PATH."modules/invitation/mail_invitation.tpl";
		}
		return file_get_contents($path);
	}
}
