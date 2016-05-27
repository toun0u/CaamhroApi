<?php
require_once DIMS_APP_PATH."include/ajax_datatable.php";

class dims_appointment_offer extends dims_data_object implements ajax_datatable {
	// type d'action
	const TYPE_ACTION = "_DIMS_EVENT_APPOINTMENT_OFFER";
	const TABLE_NAME = 'dims_mod_business_action';

	const STATUS_NOT_VALIDATED		= 0;
	const STATUS_VALIDATED			= 1;

	private $lstReponses = array();

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function save(){
		if($this->fields['typeaction'] == self::TYPE_ACTION && trim($this->fields['ref']) == '' && $this->fields['id_parent'] == 0){
			$this->fields['ref'] = md5(uniqid(true).$this->fields['id'].time());
		}

		if (func_num_args() > 0 && func_get_arg(0) != '') {
			parent::save(func_get_arg(0));
		}
		else {
			parent::save();
		}
	}

	public static function openByRef($ref){
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	ref LIKE :ref
				AND		typeaction LIKE :typeaction ";
		$db = dims::getInstance()->db;
		$res = $db->query($sel, array(':ref' => $ref, ':typeaction' => self::TYPE_ACTION) );
		$elem = false;
		if($r = $db->fetchrow($res)){
			$elem = new dims_appointment_offer();
			$elem->openFromResultSet($r);
		}
		return $elem;
	}

	public function getFrontUrl($param = ""){
		if (!empty($param))
			return "/appointment/".$this->fields['ref']."/$param";
		else
			return "/appointment/".$this->fields['ref']."/";
	}

	public function getLibelle(){
		// type
		$type = $this->getType()->fields['label'];

		// horaires
		$a_df = explode('-', $this->fields['datejour']);
		$date_from = $a_df[2].'/'.$a_df[1].'/'.$a_df[0];

		$a_dt = explode('-', $this->fields['datefin']);
		$date_to = $a_dt[2].'/'.$a_dt[1].'/'.$a_dt[0];

		if ($this->fields['datefin'] != '0000-00-00' && $this->fields['datefin'] != $this->fields['datejour']) {
			$horaires = 'du '.$date_from.' à '.substr($this->fields['heuredeb'], 0, -3).' au '.$date_to.' à '.substr($this->fields['heurefin'], 0, -3);
		}
		else {
			$horaires = $date_from.' ('.substr($this->fields['heuredeb'], 0, -3).' - '.substr($this->fields['heurefin'], 0, -3).')';
		}

		return $type. ' ' .$horaires;
	}

	public function getOldest() {
		$query = "SELECT * FROM ".$this->tablename." WHERE typeaction= :typeaction AND datejour = (SELECT MIN(datejour) FROM ".$this->tablename." WHERE typeaction= :typeaction AND datejour<>'0000-00-00')";
		$res = $this->db->query($query, array(':typeaction' => self::TYPE_ACTION) );
		return $this->db->fetchrow($res);
	}

	public function getNewest() {
		$query = "SELECT * FROM ".$this->tablename." WHERE typeaction= :typeaction AND datejour = (SELECT MAX(datejour) FROM ".$this->tablename." WHERE typeaction= :typeaction )";
		$res = $this->db->query($query, array(':typeaction' => self::TYPE_ACTION) );
		return $this->db->fetchrow($res);
	}

	public function getLink() {
		// lien relatif car le lien est utilisé en cron par la gestion des alertes
		// c'est elle qui donne le protocole et le domaine à utiliser
		return '/admin.php?dims_mainmenu=0&submenu=1&mode=activity&action=view&activity_id='.$this->getId();
	}

	public function getTitle(){
		// type
		$type = $this->getType()->fields['label'];

		// horaires
		$a_df = explode('-', $this->fields['datejour']);
		$date_from = $a_df[2].'/'.$a_df[1].'/'.$a_df[0];

		$a_dt = explode('-', $this->fields['datefin']);
		$date_to = $a_dt[2].'/'.$a_dt[1].'/'.$a_dt[0];

		$horaires = '';
		$heuredeb = ($this->fields['heuredeb'] != '00:00:00') ? substr($this->fields['heuredeb'], 0, -3) : '';
		$heurefin = ($this->fields['heurefin'] != '00:00:00') ? substr($this->fields['heurefin'], 0, -3) : '';

		if ($this->fields['datefin'] != '0000-00-00' && $this->fields['datefin'] != $this->fields['datejour']) {
			if ($heuredeb != '') {
				$heuredeb = ' à '.$heuredeb;
			}
			if ($heurefin != '') {
				$heurefin = ' à '.$heurefin;
			}
			$horaires = 'du '.$date_from.$heuredeb.' au '.$date_to.$heurefin;
		}
		else {
			if ($heuredeb != '') {
				$horaires = ' ('.$heuredeb;
				if ($heurefin != '') {
					$horaires .= ' - '.$heurefin;
				}
				$horaires .= ')';
			}
			$horaires = $date_from.$horaires;
		}

		return $type .' '. $horaires;
	}

	public function getType() {
		require_once DIMS_APP_PATH."/modules/system/activity/class_type.php";
		$type = new activity_type();
		if(!empty($this->fields['activity_type_id']) && $this->fields['activity_type_id'] != '' && $this->fields['activity_type_id'] > 0)
			$type->open($this->fields['activity_type_id']);
		else
			$type->init_description();

		return $type;
	}

	public static function getAllByParent($parent_id) {
		$db = dims::getInstance()->getDb();

		$a_dates = array();
		$rs = $db->query(	'SELECT datejour, heuredeb, heurefin
							FROM '.self::TABLE_NAME.'
							WHERE typeaction LIKE :typeaction
							AND id_parent = :idparent 
							ORDER BY datejour, heuredeb',
							array(':typeaction' => self::TYPE_ACTION, ':idparent' => $parent_id) );
		while ($row = $db->fetchrow($rs)) {
			$a_dates[] = array(
				'datefrom' => $row['datejour'],
				'heuredeb' => $row['heuredeb'],
				'heurefin' => $row['heurefin']
				);
		}
		return $a_dates;
	}

	public function getChildren() {
		$children = array();
		$rs = $this->db->query('SELECT * FROM '.$this->tablename.' WHERE id_parent = :idparent AND typeaction LIKE :typeaction ',
							array(':idparent' => $this->getId(), ':typeaction' => self::TYPE_ACTION) );
		while ($row = $this->db->fetchrow($rs)) {
			$app_offer = new dims_appointment_offer();
			$app_offer->openFromResultSet($row);
			$children[$row['datejour']][$row['heuredeb']][$row['heurefin']] = $app_offer;
		}
		return $children;
	}

	public function getLinkedDoc(){
		$lst = array();
		if ($this->fields['id_parent'] == '' || $this->fields['id_parent'] <= 0){
			$sel = "SELECT		DISTINCT d.*
					FROM		dims_mod_doc_file d
					INNER JOIN	dims_matrix m
					ON			m.id_doc = d.id_globalobject
					WHERE		m.id_appointment_offer = :idglobalobject
					AND			m.id_doc > 0";
			$db = dims::getInstance()->db;
			$res = $db->query($sel, array(':idglobalobject' => $this->fields['id_globalobject']) );
			while($r = $db->fetchrow($res)){
				$elem = new docfile();
				$elem->openFromResultSet($r);
				$lst[] = $elem;
			}
		}
		return $lst;
	}

	public function getListProp(){
		$lst = array();
		if (isset($this->fields['id_parent']) && ($this->fields['id_parent'] == '' || $this->fields['id_parent'] <= 0)){
			$sel = "SELECT		*
					FROM		".self::TABLE_NAME."
					WHERE		id_parent = :idparent
					AND			typeaction LIKE :typeaction
					ORDER BY	datejour, heuredeb";
			$db = dims::getInstance()->db;
			$res = $db->query($sel, array(':idparent' => $this->fields['id'], ':typeaction' => self::TYPE_ACTION) );
			while($r = $db->fetchrow($res)){
				$elem = new dims_appointment_offer();
				$elem->openFromResultSet($r);
				$lst[] = $elem;
			}
		}
		return $lst;
	}

	public function getVerifContact($id){
		$elem = false;
		if(trim($id) != ''){
			$param = array();
			if($this->fields['private']){
				$sel = "SELECT		c.*
						FROM		dims_mod_business_contact c
						INNER JOIN	dims_matrix m
						ON			c.id_globalobject = m.id_contact
						WHERE		m.id_appointment_offer = :idglobalobject
						AND			c.ref LIKE :id "; // on se base sur les invitations
				$param[':idglobalobject'] = $this->fields['id_globalobject'];
				$param[':id'] = dims_sql_filter($id);
			} else {
				$sel = "SELECT		c.*
						FROM		dims_mod_business_contact c
						WHERE		c.ref LIKE :id "; // on prend n'importe quel contact de la base
				$param[':id'] = dims_sql_filter($id);
			}

			$db = dims::getInstance()->db;
			$res = $db->query($sel, $param );
			if ($r = $db->fetchrow($res)){
				$elem = new contact();
				$elem->openFromResultSet($r);
			}
		}
		return $elem;
	}

	public function saveReponses($name, $reponses = array(), $user = 0){
		require_once DIMS_APP_PATH."/modules/system/appointment_offer/class_appointment_response.php";
		$rep = new dims_appointment_response();
		$rep->init_description();
		$id_ct = 0;
		if(trim($user) != '' && ($ct = $this->getVerifContact($user)) !== false){
			$id_ct = $ct->fields['id'];
			if(trim($name) == '')
				$name = $ct->fields['firstname']." ".$ct->fields['lastname'];
			foreach($this->getListRep($id_ct) as $re2){
				if($re2->getLightAttribute('currentCt')){
					$rep = $re2;
					break;
				}
			}
		}
		$rep->fields['name'] = $name;
		$rep->fields['id_contact'] = $id_ct;
		$rep->fields['go_appointment'] = $this->fields['id_globalobject'];
		$rep->save();
		$rep->saveReponses($this->getListProp(),$reponses);

		// Expéditeur
		$workspace = new workspace();
		$workspace->open($_SESSION['dims']['workspaceid']);
		$email = $workspace->fields['email_appointment'];
		if ($email==""){
			$email = $workspace->fields['email_noreply'];
			if ($email=="") $email=_DIMS_ADMINMAIL;
		}
		$pos=strpos($email,"@");
		if ($pos>0) $name2=substr($email,$pos+1);
		else $name2=$email;
		$from[0]['name']   = $name2;
		$from[0]['address']= $email;

		$domain = current($workspace->getFrontDomains());
		if ($domain['ssl'])
			$lk = "https://".$domain['domain'];
		else
			$lk = "http://".$domain['domain'];
		$url = $lk.$this->getFrontUrl();

		// Destinataire
		$user = new user();
		$user->open($this->fields['id_user']);
		$lstEmail = array();
		$lstEmail[0]['name']   = $user->fields['email'];
		$lstEmail[0]['address']= $user->fields['email'];
		// Message
		if(count($reponses) > 0){
			$lstDates = array();
			foreach($rep->loadRep() as $re){
				if($re->fields['presence']){
					$act = new dims_appointment_offer();
					$act->open($re->fields['id_appointment']);
					$dd = explode('-',$act->fields['datejour']);
					if (count($dd) == 3)
						$lstDates[] = $dd[2]."/".$dd[1]."/".$dd[0];
					else
						$lstDates[] = $act->fields['datejour'];
				}
			}
			$dates = implode('<br />', $lstDates);
		}else{
			$dates = $_SESSION['cste']['_NO_DATE_SHOULD'];
		}
		$title = str_replace(array('{NAME}','{URL}','{RDV}','{DATES}'),
								 array($name,'<a href="'.$url.'">'.$this->fields['libelle'].'</a>',$this->fields['libelle'],$dates),
								 $workspace->fields['title_appointment_rep']);
		$body = str_replace(array('{NAME}','{URL}','{RDV}','{DATES}'),
								 array($name,'<a href="'.$url.'">'.$this->fields['libelle'].'</a>',$this->fields['libelle'],$dates),
								 $workspace->fields['content_appointment_rep']);

		dims_send_mail_with_pear($from, $lstEmail, $title, self::getContentMail($title,$body));
	}

	public static function getContentMail($title, $content){
		// recherche d'un bandeau personnalisé dans le template
		$workspace = new workspace();
		$workspace->open($_SESSION['dims']['workspaceid']);

		// il peut y avoir plusieurs templates frontoffice sur un workspace
		// alors on ne teste que le premier
		$a_templates = $workspace->getFrontofficeTemplates();
		if (sizeof($a_templates) && $a_templates[0] != '' && file_exists(DIMS_APP_PATH.'templates/frontoffice/'.$a_templates[0].'./common/img/dims_banner_rdv.png')) {
			$mail_banner = dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'./common/templates/frontoffice'.$a_templates[0].'./common/img/dims_banner_rdv.png';
		}
		else {
			$mail_banner = dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/common/modules/system/appointment_offer/dims_banner_rdv.png';
		}


		$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
					<html>
					<head>
						<title>'.$title.'</title>
						<meta http-equiv="content-type" content="text/html; charset=utf-8">
						<meta name="title" content="'.$title.'">
						<meta name="description" content="'.$title.'">
						<style>
							a{
								color: #95DE2C;
							}
						</style>
					</head>
					<body>
						<table width="100%">
							<tr>
								<td>
									<!-- HEADER -->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="770px">
										<tr>
											<td height="80px" width="770px"><img name="IC_Mail_Banner.png" src="'.$mail_banner.'" width="770" height="80" alt="Bannière Dims" style="display: block;" border="0"></td>
										</tr>
									</table><!-- FIN HEADER -->
									<!-- CONTENT -->
									<table cellspacing="0" align="center" width="770" bgcolor="#F1F1F1">
										<tr>
											<td valign="top">
												<table cellpadding="40" cellspacing="0" align="center" width="600">
													<tr>
														<td valign="top" align="justigy"><font face="Arial" size="4" color="#434343">
															'.$content.'
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table><!-- FIN CONTENT -->
									<!-- FOOTER -->
									<table cellspacing="0" align="center" width="770">
										<tr bgcolor="#D9DADB">
											<td valign="top">
												<table cellpadding="20" cellspacing="0" align="center" width="500">
													<tr>
														<td valign="top" align="center"><font face="Arial" size="1" color="#5F5F5F">Dims Portal v5</font></td>
													</tr>
												</table>
											</td>
										</tr>
									</table><!-- FIN FOOTER -->
								</td>
							</tr>
						</table>
					</body>
					</html>';
		return $message;
	}

	public function getListRep($id_ct = null){
		if(count($this->lstReponses) <= 0){
			require_once DIMS_APP_PATH."/modules/system/appointment_offer/class_appointment_response.php";
			$this->lstReponses = array();
			$sel = "SELECT		*
					FROM		".dims_appointment_response::TABLE_NAME."
					WHERE		go_appointment = :idglobalobject
					ORDER BY	timestp";
			$db = dims::getInstance()->db;
			$res = $db->query($sel, array(':idglobalobject' => $this->fields['id_globalobject']) );
			while($r = $db->fetchrow($res)){
				$elem = new dims_appointment_response();
				$elem->openFromResultSet($r);
				$elem->loadRep();
				$elem->setLightAttribute('currentCt',(!is_null($id_ct) && $id_ct > 0 && $id_ct == $elem->fields['id_contact']));
				$this->lstReponses[] = $elem;
			}
		}
		return $this->lstReponses;
	}

	public function getListRepCt(){
		require_once DIMS_APP_PATH."/modules/system/appointment_offer/class_appointment_response.php";
		$lst = array();
		$sel = "SELECT		DISTINCT ct.*
				FROM		dims_mod_business_contact ct
				INNER JOIN	".dims_appointment_response::TABLE_NAME." r
				ON			r.id_contact = ct.id
				WHERE		r.go_appointment = :idglobalobject ";
		$db = dims::getInstance()->db;
		$res = $db->query($sel, array(':idglobalobject' => $this->fields['id_globalobject']) );
		while($r = $db->fetchrow($res)){
			$elem = new contact();
			$elem->openFromResultSet($r);
			$lst[] = $elem;
		}
		return $lst;
	}

	public static function getAppointmentsTable($status = null){
		$sel = 'SELECT 		a.*, CONCAT(a.address," <br />",a.cp," ",a.lieu) address, COUNT(a2.id)
				FROM 		'.self::TABLE_NAME." a
				INNER JOIN 	".self::TABLE_NAME." a2
				ON			a2.id_parent = a.id
				AND 		a.typeaction LIKE :typeaction
				AND			a.id_parent = 0
				AND			a.id_workspace = :workspaceid ";
		$params = array(
			':typeaction' => array('value'=>self::TYPE_ACTION,'type'=>PDO::PARAM_STR),
			':workspaceid' => array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
		);
		if(!is_null($status) && $status > -1){
			$sel .= ' AND a.status = :status ';
			$params[':status'] = array('value'=>$status,'type'=>PDO::PARAM_INT);
		}
		$sel .= " GROUP BY a.id ";
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		$separation = $db->split_resultset($res);

		$current_date = current(explode(" ", dims_getdatetime()));

		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$domain = current($work->getFrontDomains());
		if ($domain['ssl'])
			$lk = "https://".$domain['domain']."/appointment/";
		else
			$lk = "http://".$domain['domain']."/appointment/";
		$ct = new contact();
		$ct->open($_SESSION['dims']['user']['id_contact']);
		$ref = $ct->fields['ref'];

		$lstReturn = array();
		foreach ($separation as $tab) {
			$elem = array();
			$elem[] = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/'.(($tab['a']['status'] == self::STATUS_VALIDATED)?'actif16.png':'inactif16.png').'" />';
			$elem[] = '<a href="'.dims::getInstance()->getScriptEnv().'?action=edit&mode=appointment_offer&app_offer_id='.$tab['a']['id'].'" title="Voir le détail">'.stripslashes($tab['a']['libelle']).'</a>';
			$app = new dims_appointment_offer();
			$app->openFromResultSet($tab['a']);
			$elem2 = array();
			if ($tab['a']['status'] == self::STATUS_VALIDATED){
				$dd = explode('-',$tab['a']['datejour']);
				if (count($dd) == 3)
					$elem2[] = $dd[2]."/".$dd[1]."/".$dd[0];
				else
					$elem2[] = $tab['a']['datejour'];
			}else{
				foreach($app->getListProp() as $prop){
					$dd = explode('-',$prop->fields['datejour']);
					if (count($dd) == 3)
						$elem2[] = $dd[2]."/".$dd[1]."/".$dd[0];
					else
						$elem2[] = $prop->fields['datejour'];
				}
			}
			$elem[] = implode('<br />',$elem2);
			$elem[] = $tab['a']['address']."<br />".$tab['a']['cp']." ".$tab['a']['lieu'];
			$elem[] = count($app->getListRep());
			$elem[] = '<a target="_blank" href="'.$lk.$tab['a']['ref'].'/"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/visu_picto.png" alt="Accéder au lien public" /></a>';
			$action = "";
			if ($tab['a']['id_user'] == $_SESSION['dims']['userid']){
				$action = '<a href="'.dims::getInstance()->getScriptEnv().'?action=edit&mode=appointment_offer&app_offer_id='.$tab['a']['id'].'" title="Voir le détail"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/open_record16.png" alt="Voir le détail" /></a>';
				$action = '<a href="javascript:void(0);" onclick="javascript:showReminderPopup('.$tab['a']['id'].');"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/enveloppe16.png" alt="Envoyer un rappel" /></a>';
			}
			$action .= '<a target="_blank" href="'.$lk.$tab['a']['ref'].'/&ct='.$ref.'" title="Répondre"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/icon_attach.png" alt="Répondre" /></a>';
			$action .= '<a href="javascript:void(0);" onclick="javascript:validateAppointment('.$tab['a']['id'].');" title="Valider"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/event_mini.png" alt="Valider" /></a>';
			//$action .= '<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$dims->getScriptEnv().'?action=delete&id='.$tab['a']['id'].'\',\'Êtes-vous sûr de vouloir supprimer cette proposition ?\');" title="Supprimer"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/close.png" alt="Supprimer" /></a>';
			$elem[] = $action;
			$lstReturn[] = $elem;
		}
		return $lstReturn;
	}

	// recherche dataTable
	public function get_sTable() {
		return " ".self::TABLE_NAME." a
				INNER JOIN ".self::TABLE_NAME." a2
				ON			a2.id_parent = a.id ";
	}

	public function get_aColumns() {
		return array('a.status', 'a.libelle', 'a.datejour', 'CONCAT(a.address," <br />",a.cp," ",a.lieu)', 'COUNT(a2.id)', '');
	}

	public function get_sIndexColumn() {
		return "a.id";
	}


	/**
	 * @return (array) 	[0] => (string)	Clauses WHERE & AND
	 *                  [1] => (array) Tableau de paramètres des clauses WHERE & AND
	 */
	public function get_sWhere() {
		$params = array();
		$where = "	AND a.typeaction LIKE :typeaction
					AND	a.id_parent = 0
					AND	a.id_workspace = :workspaceid ";
		$params[':typeaction'] = self::TYPE_ACTION;
		$params[':workspaceid'] = $_SESSION['dims']['workspaceid'];

		// filtres
		if ($_SESSION['desktopv2']['appointment']['filters']['status'] > -1)
			$where .= ' AND a.status = :status ';
		$params[':status'] = $_SESSION['desktopv2']['appointment']['filters']['status'];

		return array($where." GROUP BY a.id", $params);
	}

	public function get_aaData($res_query) {
		$aaData = array();
		$dims = dims::getInstance();
		$db = $dims->getDb();
		$separation = $db->split_resultset($res_query);
		$current_date = current(explode(" ", dims_getdatetime()));

		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$domain = current($work->getFrontDomains());
		if ($domain['ssl'])
			$lk = "https://".$domain['domain']."/appointment/";
		else
			$lk = "http://".$domain['domain']."/appointment/";
		$ct = new contact();
		$ct->open($_SESSION['dims']['user']['id_contact']);
		$ref = $ct->fields['ref'];

		foreach ($separation as $tab) {
			$elem = array();
			$elem[] = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/'.(($tab['a']['status'] == self::STATUS_VALIDATED)?'actif16.png':'inactif16.png').'" />';
			$elem[] = '<a href="'.$dims->getScriptEnv().'?action=edit&mode=appointment_offer&app_offer_id='.$tab['a']['id'].'" title="Voir le détail">'.stripslashes($tab['a']['libelle']).'</a>';
			$app = new dims_appointment_offer();
			$app->openFromResultSet($tab['a']);
			$elem2 = array();
			if ($tab['a']['status'] == self::STATUS_VALIDATED){
				$dd = explode('-',$tab['a']['datejour']);
				if (count($dd) == 3)
					$elem2[] = $dd[2]."/".$dd[1]."/".$dd[0];
				else
					$elem2[] = $tab['a']['datejour'];
			}else{
				foreach($app->getListProp() as $prop){
					$dd = explode('-',$prop->fields['datejour']);
					if (count($dd) == 3)
						$elem2[] = $dd[2]."/".$dd[1]."/".$dd[0];
					else
						$elem2[] = $prop->fields['datejour'];
				}
			}
			$elem[] = implode('<br />',$elem2);
			$elem[] = $tab['a']['address']."<br />".$tab['a']['cp']." ".$tab['a']['lieu'];
			$elem[] = count($app->getListRep());
			$elem[] = '<a target="_blank" href="'.$lk.$tab['a']['ref'].'">Accéder au lien public</a>';
			$action = "";
			if ($tab['a']['id_user'] == $_SESSION['dims']['userid']){
				$action = '<a href="'.$dims->getScriptEnv().'?action=edit&mode=appointment_offer&app_offer_id='.$tab['a']['id'].'" title="Voir le détail"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/open_record16.png" alt="Voir le détail" /></a>';
				$action = '<a href="javascript:void(0);" onclick="javascript:showReminderPopup('.$tab['a']['id'].');"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/enveloppe16.png" alt="Envoyer un rappel" /></a>';
			}
			$action .= '<a target="_blank" href="'.$lk.$tab['a']['ref'].'&'.$ref.'" title="Répondre"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/icon_attach.png" alt="Répondre" /></a>';
			$action .= '<a href="javascript:void(0);" onclick="javascript:validateAppointment('.$tab['a']['id'].');" title="Valider"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/event_mini.png" alt="Valider" /></a>';
			//$action .= '<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$dims->getScriptEnv().'?action=delete&id='.$tab['a']['id'].'\',\'Êtes-vous sûr de vouloir supprimer cette proposition ?\');" title="Supprimer"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/close.png" alt="Supprimer" /></a>';
			$elem[] = $action;
			$aaData[] = $elem;
		}

		return $aaData;
	}

	public function generateCalDav(){
		$dir = DIMS_TMP_PATH . "/appointment/";
		if (!file_exists($dir))
			mkdir($dir);
		$sid = session_id();
		if (!file_exists($dir.$sid))
			mkdir($dir.$sid);
		$url = $dir.$sid.'/'.str_replace(' ','_',$this->fields['libelle']).".ics";
		$cal = fopen($url,'w+');

		$uid = $this->fields['ref'];
		$summary = $description = str_replace(array(',',':'),array('\,','\:'),$this->fields['libelle']);
		$dd = explode('-',$this->fields['datejour']);
		$tstart = $tend = $tstamp = gmdate("Ymd\THis\Z");
		if (count($dd) == 3){
			if($this->fields['heuredeb'] != '08:00:00' && $this->fields['heurefin'] != '08:00:00'){
				$tstart = $dd[0].$dd[1].$dd[2]."T".substr(str_replace(':', '', $this->fields['heuredeb']),0,4)."Z";
				$tend = $dd[0].$dd[1].$dd[2]."T".substr(str_replace(':', '', $this->fields['heurefin']),0,4)."Z";
			}else{
				$tstart = $dd[0].$dd[1].$dd[2]."T000000Z";
				$tend = $dd[0].$dd[1].$dd[2]."T235959Z";
			}
		}
		$location = str_replace(array(',',':'),array('\,','\:'),$this->fields['address']." ".$this->fields['cp']." ".$this->fields['lieu']);
		$body = "BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
DTSTAMP:$tstamp
DTSTART:$tstart
DTEND:$tend
UID:$uid
LOCATION:$location
SUMMARY:$summary
DESCRIPTION:$description
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR";
/*
 // TODO : à ajouter entre END:VEVENT & END:VCALENDAR
BEGIN:VALARM
TRIGGER;RELATED=START:-PT30H  ||| TRIGGER;VALUE=DATE-TIME:gmdate("Ymd\THis\Z")
ACTION:DISPLAY
SUMMARY:$summary
DESCRIPTION:$description
END:VALARM
*/
		fwrite($cal,$body);
		fclose($cal);
		return $url;
	}

	public function sendMailValidation($lstEmail = array()){
		if (count($lstEmail) > 0){
			// Expéditeur
			$workspace = new workspace();
			$workspace->open($_SESSION['dims']['workspaceid']);
			$email = $workspace->fields['email_appointment'];
			if ($email==""){
				$email = $workspace->fields['email_noreply'];
				if ($email=="") $email=_DIMS_ADMINMAIL;
			}
			$pos=strpos($email,"@");
			if ($pos>0) $name=substr($email,$pos+1);
			else $name=$email;
			$from[0]['name']   = $name;
			$from[0]['address']= $email;

			$domain = current($workspace->getFrontDomains());
			if ($domain['ssl'])
				$lk = "https://".$domain['domain'];
			else
				$lk = "http://".$domain['domain'];
			$url = $lk.$this->getFrontUrl();

			// CalDav en pièce jointe
			$calDav = $this->generateCalDav();
			$file = array();
			$file[0]['name'] = stripslashes($this->fields['libelle']).'.ics';
			$file[0]['filename'] = $calDav;
			$file[0]['mime-type'] = mime_content_type($calDav);
			//$file[0]['mime-type'] = "text/calendar";

			$dd = explode('-',$this->fields['datejour']);
			$date = $this->fields['datejour'];
			if (count($dd) == 3)
				$date = $dd[2]."/".$dd[1]."/".$dd[0];
			if ($this->fields['heuredeb'] == $this->fields['heurefin'] && $this->fields['heuredeb'] == '08:00:00') $hours = $_SESSION['cste']['_ALL_DAY'];
			else $hours = $_SESSION['cste']['FROM'].' '.substr($this->fields['heuredeb'], 0, -3).' '.$_SESSION['cste']['_DIMS_LABEL_A'].' '.substr($this->fields['heurefin'], 0, -3);
			$date .= ' - '.$hours;


			// Message
			$title = stripslashes(str_replace(array('{FIRSTNAME}','{LASTNAME}','{EMAIL}','{URL}','{RDV}','{DATE}'),
								 array($_SESSION['dims']['user']['firstname'],$_SESSION['dims']['user']['lastname'],$_SESSION['dims']['user']['email'],'<a href="'.$url.'">'.$this->fields['libelle'].'</a>',$this->fields['libelle'],$date),
								 $workspace->fields['title_appointment_val']));
			$content = stripslashes(str_replace(array('{FIRSTNAME}','{LASTNAME}','{EMAIL}','{URL}','{RDV}','{DATE}'),
								 array($_SESSION['dims']['user']['firstname'],$_SESSION['dims']['user']['lastname'],$_SESSION['dims']['user']['email'],'<a href="'.$url.'">'.$this->fields['libelle'].'</a>',$this->fields['libelle'],$date),
								 $workspace->fields['validation_appointment']));

			dims_send_mail_with_pear($from, $lstEmail, $title,	self::getContentMail($title,$content), $file);
		}
	}

	public function sendMailInvitation($lstGoCt = array()){
		require_once DIMS_APP_PATH.'modules/system/appointment_offer/class_appointment_notification.php';

		if (count($lstGoCt) > 0){
			// Expéditeur
			$workspace = new workspace();
			$workspace->open($_SESSION['dims']['workspaceid']);
			$email = $workspace->fields['email_appointment'];
			if ($email==""){
				$email = $workspace->fields['email_noreply'];
				if ($email=="") $email=_DIMS_ADMINMAIL;
			}
			$pos=strpos($email,"@");
			if ($pos>0) $name=substr($email,$pos+1);
			else $name=$email;
			$from[0]['name']   = $name;
			$from[0]['address']= $email;

			$domain = current($workspace->getFrontDomains());
			if ($domain['ssl'])
				$lk = "https://".$domain['domain'];
			else
				$lk = "http://".$domain['domain'];

			foreach($lstGoCt as $go){
				$ct = new contact();
				$ct->open($go['id']);
				if (isset($ct->fields['email']) && trim($ct->fields['email']) != ''){

					// on enregistre la notification pour ne pas envoyer plusieurs mails à un même contact
					// voir #4920
					$notif = new appointment_notification();
					if (!$notif->open($this->getId(), $ct->getId())) {
						$notif->save();

						$url = $lk.$this->getFrontUrl("&ct=".$ct->fields['ref']);
						$title = stripslashes(str_replace(array('{FIRSTNAME}','{LASTNAME}','{EMAIL}','{URL}','{RDV}'),
											 array($_SESSION['dims']['user']['firstname'],$_SESSION['dims']['user']['lastname'],$_SESSION['dims']['user']['email'],'<a href="'.$url.'">'.$this->fields['libelle'].'</a>',$this->fields['libelle']),
											 $workspace->fields['title_appointment']));
						$content = stripslashes(str_replace(array('{FIRSTNAME}','{LASTNAME}','{EMAIL}','{URL}','{RDV}'),
											 array($_SESSION['dims']['user']['firstname'],$_SESSION['dims']['user']['lastname'],$_SESSION['dims']['user']['email'],'<a href="'.$url.'">'.$this->fields['libelle'].'</a>',$this->fields['libelle']),
											 $workspace->fields['content_appointment']));

						dims_send_mail_with_pear($from, $ct->fields['email'], $title,  self::getContentMail($title,$content));
					}
				}
			}
		}
	}

	public function sendMailRappel($lstCt = array()){
		if (count($lstCt) > 0){
			// Expéditeur
			$workspace = new workspace();
			$workspace->open($_SESSION['dims']['workspaceid']);
			$email = $workspace->fields['email_appointment'];
			if ($email==""){
				$email = $workspace->fields['email_noreply'];
				if ($email=="") $email=_DIMS_ADMINMAIL;
			}
			$pos=strpos($email,"@");
			if ($pos>0) $name=substr($email,$pos+1);
			else $name=$email;
			$from[0]['name']   = $name;
			$from[0]['address']= $email;

			$domain = current($workspace->getFrontDomains());
			if ($domain['ssl'])
				$lk = "https://".$domain['domain'];
			else
				$lk = "http://".$domain['domain'];

			$a_dates = self::getAllByParent($this->getId());
			foreach ($a_dates as $date) {
				if ($date['heuredeb'] == $date['heurefin'] && $date['heuredeb'] == '08:00:00') $hours = $_SESSION['cste']['_ALL_DAY'];
				else $hours = $_SESSION['cste']['FROM'].' '.substr($date['heuredeb'], 0, -3).' '.$_SESSION['cste']['_DIMS_LABEL_A'].' '.substr($date['heurefin'], 0, -3);

				$dd = explode('-',$date['datefrom']);
				if (count($dd) == 3)
					$lstDates[] = $dd[2]."/".$dd[1]."/".$dd[0].' - '.$hours;
				else
					$lstDates[] = $date['dateform'].' - '.$hours;
			}
			$dates = implode('<br/>', $lstDates);

			foreach($lstCt as $ct){
				if (isset($ct->fields['email']) && trim($ct->fields['email']) != ''){
					$url = $lk.$this->getFrontUrl("&ct=".$ct->fields['ref']);

					// Message
					$title = stripslashes(str_replace(array('{FIRSTNAME}','{LASTNAME}','{EMAIL}','{URL}','{RDV}'),
										 array($_SESSION['dims']['user']['firstname'],$_SESSION['dims']['user']['lastname'],$_SESSION['dims']['user']['email'],'<a href="'.$url.'">'.$this->fields['libelle'].'</a>',$this->fields['libelle']),
										 $workspace->fields['title_appointment_remind']));
					$content = stripslashes(str_replace(array('{FIRSTNAME}','{LASTNAME}','{EMAIL}','{URL}','{RDV}','{DATES}'),
										 array($_SESSION['dims']['user']['firstname'],$_SESSION['dims']['user']['lastname'],$_SESSION['dims']['user']['email'],'<a href="'.$url.'">'.$this->fields['libelle'].'</a>',$this->fields['libelle'],$dates),
										 $workspace->fields['content_appointment_remind']));

					dims_send_mail_with_pear($from, $ct->fields['email'], $title,  self::getContentMail($title,$content));
				}
			}
		}
	}

	public function getLinkedContacts($no_rep = false) {
		$a_contacts = array();
		$param = array();
		$sql = '
			SELECT	c.*
			FROM	dims_mod_business_contact c
			INNER JOIN	dims_matrix m
			ON			m.id_contact = c.id_globalobject
			AND			m.id_appointment_offer = :idglobalobject ';
		$param[':idglobalobject'] = $this->fields['id_globalobject'];
		// uniquement ceux qui n'ont pas répondu
		if ($no_rep) {
			$sql .= ' WHERE c.id NOT IN ( SELECT id_contact FROM '.dims_appointment_response::TABLE_NAME.' WHERE go_appointment = :goappointment )';
			$param[':goappointment'] = $this->fields['id_globalobject'];
		}
		$rs = $this->db->query($sql, $param);
		while ($row = $this->db->fetchrow($rs)) {
			$contact = new contact();
			$contact->openFromResultSet($row);
			$a_contacts[] = $contact;
		}

		return $a_contacts;
	}

}
