<?php
class desktopv2 {
	private $db;
	public function __construct(){
		$db = dims::getInstance()->getDb();
		$this->db = $db;
	}

	public function getFirstRecentConnexions($desktop_view_type = 0, $desktop_view_connexion = 1){
		$params = array();
		$sql = "SELECT		DISTINCT c.*, u.*, MAX(cu.timestp) as timestp
				FROM		dims_user as u
				INNER JOIN	dims_connecteduser as cu
				ON		cu.user_id = u.id
				INNER JOIN	dims_mod_business_contact c ON c.id = u.id_contact AND c.inactif = 0";
		if ($desktop_view_type==0) {
			$sql.= " AND	workspace_id = :idworkspace ";
			$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		}
		elseif ($desktop_view_connexion==1) {
			global $dims;
			$sql.= " AND	workspace_id in (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).") ";
		}
		else {
			global $dims;
			$sql.= " AND	workspace_id in (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).") ";
		}
		$sql .= " GROUP BY	u.id
			  ORDER BY	timestp DESC
			  LIMIT		6";

		$res = $this->db->query($sql, $params);
		$LastConn = array();
		foreach($this->db->split_resultset($res) as $r){
			$us = new user();
			$us->openFromResultSet($r['u']);
			$us->fields['diff'] = dims_diffdate(date("YmdHis"),$r['unknown_table']['timestp']);
			$us->fields['date'] = dims_nicetime($r['unknown_table']['timestp']);

			$ct = new contact();
			$ct->openFromResultSet($r['c']);
			$us->setLightAttribute('photo_path', $ct->getPhotoPath(36));
			$us->setLightAttribute('photo_web_path', $ct->getPhotoWebPath(36));

			$LastConn[$r['u']['id']] = $us;

		}
		return $LastConn;
	}

	public function getRecentActivities($workspaces = array(), $limite = 5){
		global $dims;
		$lstOpp = array();
		$params = array();
		$sel = "SELECT		*
			FROM		dims_mod_business_action
			WHERE		type = :type ";
			$params[':type'] = dims_const::_PLANNING_ACTION_ACTIVITY;
		if(!empty($workspaces)) {
			$sel .= ' AND id_workspace IN ('.$this->db->getParamsFromArray($workspaces, 'idworkspace', $params).')';
		}

		$sel.=" ORDER BY	datejour DESC
				LIMIT		:limit";
		$params[':limit'] = array('type' => PDO::PARAM_INT, 'value' => $limite);

		$res = $this->db->query($sel, $params);
		while ($r = $this->db->fetchrow($res)){
			$event = new action();
			$event->openWithFields($r);
			$lstOpp[] = $event;
		}
		return $lstOpp;
	}

	public function getRecentOpportunities($workspaces = array(), $limite = 5){
		global $dims;
		$lstOpp = array();
		$params = array();
		$sel = "SELECT		*
			FROM		dims_mod_business_action
			WHERE		type = :type ";
			$params[':type'] = dims_const::_PLANNING_ACTION_OPPORTUNITY;
		if(!empty($workspaces)) {
			$sel .= ' AND id_workspace IN ('.$this->db->getParamsFromArray($workspaces, 'idworkspace', $params).')';
		}

		$sel.=" ORDER BY	datejour DESC
				LIMIT		:limit";
		$params[':limit'] = array('type' => PDO::PARAM_INT, 'value' => $limite);

		$res = $this->db->query($sel, $params);
		while ($r = $this->db->fetchrow($res)){
			$event = new action();
			$event->openWithFields($r);
			$lstOpp[] = $event;
		}
		return $lstOpp;
	}

	public function getNextOpportunities($workspaces = array(), $limite = 5){
		global $dims;
		$lstOpp = array();
		$params = array();
		$sel = "SELECT		*
				FROM		dims_mod_business_action
				WHERE		datejour > '".date('Y')."-".date('m')."-".date('d')."'
				AND		id_parent=0";

		if(!empty($workspaces)) {
			$sel .= ' AND id_workspace IN ('.$this->db->getParamsFromArray($workspaces, 'idworkspace', $params).')';
		}

		$sel .=" ORDER BY	datejour ASC
				LIMIT		:limit";
		$params[':limit'] = array('type' => PDO::PARAM_INT, 'value' => $limite);

		$res = $this->db->query($sel, $params);
		while ($r = $this->db->fetchrow($res)){
			$event = new action();
			$event->openWithFields($r);
			$lstOpp[] = $event;
		}
		return $lstOpp;
	}

	public function getRecentTodos($limite = 5) {
		global $dims;
		$listOpp = array();
		$sel = "SELECT *
				FROM dims_todo
				WHERE user_to = :iduser
				ORDER BY date DESC
				LIMIT :limit ";
		$res = $this->db->query($sel, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
			':limit' => array('type' => PDO::PARAM_INT, 'value' => $limite),
		));
		while ($r = $this->db->fetchrow($res)) {
			$event = new todo();
			$event->openWithFields($r);
			$listOpp[] = $event;
		}
		return $listOpp;
	}

	public function getRecentCompanies($workspaces = array(), $limite = 5){
		global $dims;
		$lstComp = array();
		$params = array();
		$sel = "SELECT		*
				FROM		dims_mod_business_tiers
				WHERE		inactif = 0";

		if(!empty($workspaces)) {
			$sel .= ' AND id_workspace IN ('.$this->db->getParamsFromArray($workspaces, 'idworkspace', $params).')';
		}

		$sel .=" ORDER BY	timestp_modify DESC
				LIMIT		:limit";
		$params[':limit'] = array('type' => PDO::PARAM_INT, 'value' => $limite);

		$res = $this->db->query($sel, $params);
		while ($r = $this->db->fetchrow($res)){
			$tier = new tiers();
			$tier->openWithFields($r);
			$lstComp[] = $tier;
		}
		return $lstComp;
	}

	public function getRecentContacts($workspaces = array(), $limite = 5){
		global $dims;
		$lstComp = array();
		$params = array();
		$sel = "SELECT		*
			FROM		dims_mod_business_contact
			WHERE		inactif = 0";

		if(!empty($workspaces)) {
			$sel .= ' AND id_workspace IN ('.$this->db->getParamsFromArray($workspaces, 'idworkspace', $params).')';
		}

		$sel .=" ORDER BY	timestp_modify DESC
				LIMIT		:limit";
		$params[':limit'] = array('type' => PDO::PARAM_INT, 'value' => $limite);

		$res = $this->db->query($sel, $params);
		while ($r = $this->db->fetchrow($res)){
			$ct = new contact();
			$ct->openWithFields($r);
			$lstComp[] = $ct;
		}
		return $lstComp;
	}

	public function getShortcuts(){
		global $dims;
		$lstShortcuts = array();
		$currentworkspace = $dims->getWorkspaces($_SESSION['dims']['workspaceid']);

		// address book
		/*$elem = array();
		$elem['title'] = $_SESSION['cste']['ADDRESS_BOOK'];
		$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/address_book.png';
		$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=address_book';
		$elem['sep'] = true;
		$lstShortcuts[] = $elem;*/

		$elem = array();
		$elem['title'] = $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT'];
		$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/add_contact32.png';
		$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=contact&action=new';
		$elem['sep'] = false;
		$lstShortcuts[] = $elem;

		// $elem = array();
		// $elem['title'] = $_SESSION['cste']['_NEW_STRUCTURE'];
		// $elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/add_company32.png';
		// $elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=company&action=new';
		// $elem['sep'] = false;
		// $lstShortcuts[] = $elem;

        $elem = array();
        $elem['title'] = "ERP";
        $elem['img'] = '/common/modules/catalogue/admin/views/gfx//clients50x30.png';
        $elem['link'] = '/admin.php?dims_mainmenu=catalogue&dims_desktop=block&dims_action=public';
        $elem['sep'] = false;
        $lstShortcuts[] = $elem;

		$i = 0;
		if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
			$elem = array();
			$elem['title'] = $_SESSION['cste']['_BUSINESS_PAGE_TITLE'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/business.png';
			$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=business';
			$elem['sep'] = true;
			$lstShortcuts[] = $elem;
			$i++;
		}

		if (!defined('_ACTIVE_ACTIVITY') || _ACTIVE_ACTIVITY) {
			$elem = array();
			if ($dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_CREATE)) {
				$elem['title'] = $_SESSION['cste']['ENTER_NEW_BUSINESS_EVENT'];
				$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/add32.png';
				$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=activity&action=edit';
				$elem['sep'] = ($i==1);
				$lstShortcuts[] = $elem;
			}

			$elem = array();
			$elem['title'] = $_SESSION['cste']['_SYSTEM_MANAGE_EVENTS'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/manage_activities.png';
			$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=activity&action=manage';
			$elem['sep'] = true;
			$lstShortcuts[] = $elem;
			$i++; $i++;
			if($i >= 3) $i = 0;
		}

		if (!defined('_ACTIVE_OPPORTUNITY') || _ACTIVE_OPPORTUNITY) {
			$elem = array();
			$elem['title'] = $_SESSION['cste']['_SYSTEM_MANAGE_OPPORTUNITIES'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/manage_opportunities.png';
			$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=leads&action=manage';
			$elem['sep'] = false;
			if($i >= 3){
				$elem['sep'] = true;
				$i = 0;
			}
			$i++;
			$lstShortcuts[] = $elem;
		}

		// planning
		if ($currentworkspace['activeplanning'] && defined('_ACTIVE_DESKTOP_V2') && _ACTIVE_DESKTOP_V2 ) {
			$elem = array();
			$elem['title'] = $_SESSION['cste']['_PLANNING'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/calendar32.png';
			$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=refreshplanning';
			$elem['sep'] = false;
			$lstShortcuts[] = $elem;

			// $elem = array();
			// $elem['title'] = $_SESSION['cste']['_DIMS_OFFER_APPOINTMENT'];
			// $elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/new_event.png';
			// $elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=appointment_offer&action=edit';
			// $elem['sep'] = false;
			// $lstShortcuts[] = $elem;

			$elem = array();
			$elem['title'] = $_SESSION['cste']['_ORGANISE_MEETINGS'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/calendar32.png';
			$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=appointment_offer&action=manage';
			$elem['sep'] = false;
			$lstShortcuts[] = $elem;

		}

		/*

		$lstcata=$dims->getModuleByType('catalogue');
		if (!empty($lstcata)) {
			$first=true;
			foreach ($lstcata as $cata) {

				$elem = array();
				$elem['title'] = $cata['instancename'];
				if (file_exists(realpath('.').'/modules/'.$cata['contenttype'].'./common/img/mod32.png'))
					$imgmodule='/modules/'.$cata['contenttype'].'./common/img/mod32.png';
				else
					$imgmodule=_DESKTOP_TPL_PATH.'/gfx/common/administration.png';
				$elem['img'] = $imgmodule;
				$elem['link'] = dims_urlencode('/admin.php?dims_moduleid='.$cata['instanceid'].'&dims_desktop=block&dims_action=public');

				if ($first) {
					$elem['sep'] = true;
					$first=false;
				}
				else
					$elem['sep'] = false;
				$lstShortcuts[] = $elem;
			}
		}

		if ($dims->isModuleTypeEnabled('events') && ($currentworkspace['activeevent'] || $currentworkspace['activeeventstep'])){
			$mod = current($dims->getModuleByType('events'));

			$elem = array();
			$elem['title'] = $_SESSION['cste']['MANAGE_EXISTING_EVENTS'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/manage_existing_events.png';
			$elem['link'] = '/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block&admin.php?dims_mainmenu=events&dims_action=public&action=view_admin_events&ssubmenu=0&dims_desktop=block';
			$elem['sep'] = true;
			$lstShortcuts[] = $elem;

			$elem = array();
			$elem['title'] = $_SESSION['cste']['NEW_EVENT'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/new_event.png';
			$elem['link'] = '/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block&admin.php?dims_mainmenu=events&dims_action=public&action=add_evt&ssubmenu=11&type='.dims_const::_PLANNING_ACTION_EVT.'&id=0&dims_desktop=block&type_action=_DIMS_EVENT_OPPORTUNITIES';
			$elem['sep'] = false;
			$lstShortcuts[] = $elem;

			/*$elem = array();
			$elem['title'] = $_SESSION['cste']['NEW_FAIR'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/new_fair.png';
			$elem['link'] = '/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block&admin.php?dims_mainmenu=events&dims_action=public&action=add_evt&ssubmenu=11&type='.dims_const::_PLANNING_ACTION_EVT.'&id=0&dims_desktop=block&type_action=_DIMS_PLANNING_FAIR';
			$elem['sep'] = false;
			$lstShortcuts[] = $elem;
			*/

		/*}

		if ($currentworkspace['activenewsletter'] ){
			$elem = array();
			$elem['title'] = $_SESSION['cste']['_DIMS_LABEL_NEWSLETTER'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/newsletter.png';
			$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=newsletters&news_op='.dims_const_desktopv2::_NEWSLETTERS_DESKTOP;
			$elem['sep'] = false;
			$lstShortcuts[] = $elem;
		}


		$elem = array();
		$elem['title'] = $_SESSION['cste']['_EXCEL_FILE'];
		$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/import.png';
		$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=import_data';
		$elem['sep'] = false;
		$lstShortcuts[] = $elem;*/

		if ($currentworkspace['activenewsletter'] ){
			$elem = array();
			$elem['title'] = $_SESSION['cste']['_DIMS_LABEL_NEWSLETTER'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/newsletter.png';
			$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=newsletters&news_op='.dims_const_desktopv2::_NEWSLETTERS_DESKTOP;
			$elem['sep'] = false;
			$lstShortcuts[] = $elem;
		}

		if($dims->isAdmin() || $dims->isManager()){
			$elem = array();
			$elem['title'] = $_SESSION['cste']['_DIMS_LABEL_ADMIN'];
			$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/administration.png';
			$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=admin&o=tag';
			$elem['sep'] = false;
			$lstShortcuts[] = $elem;
		}

		return $lstShortcuts;
	}

	public function getContextualFunctions(){
		global $dims;
		$lstFunctions = array();
		$currentworkspace = $dims->getWorkspaces($_SESSION['dims']['workspaceid']);
		$mode = $_SESSION['desktopv2']['mode'];

		switch ($mode) {
			case 'appointment_offer':
				$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
				switch ($action) {
					case 'manage':
						$elem = array();
						$elem['title'] = $_SESSION['cste']['_DIMS_OFFER_APPOINTMENT'];
						$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/add24.png';
						$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=appointment_offer&action=edit';
						$lstFunctions[] = $elem;
						break;
					case 'edit':
						$app_offer_id = dims_load_securvalue('app_offer_id', dims_const::_DIMS_NUM_INPUT, true, true);
						if ($app_offer_id) {
							$elem = array();
							$elem['title'] = $_SESSION['cste']['_DIMS_APPOINTMENT_OFFER_SEND_REMINDER'];
							$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/enveloppe24.png';
							$elem['link'] = 'javascript:showReminderPopup('.$app_offer_id.');';
							$lstFunctions[] = $elem;
						}
						break;
				}
				break;
			case 'planning':
				$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
				if(isset($_SESSION['desktopv2']['appointment_offer']) && $action != 'manage'){
					$elem = array();
					$elem['title'] = $_SESSION['cste']['_DIMS_BACK'];
					$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/icon_back.png';
					$elem['link'] = '/admin.php?action=edit&mode=appointment_offer&app_offer_id='.$_SESSION['desktopv2']['appointment_offer'];
					$lstFunctions[] = $elem;
				}
				break;
			case 'activity':
				$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
				$activity_id = dims_load_securvalue('activity_id', dims_const::_DIMS_CHAR_INPUT, true, true);

				// verif permissions
				$bModify = false;

				if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_MODIFY_OTHERS) ) {
					$bModify = true;
				}
				elseif ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_MODIFY_OWNS) ) {
					if ($activity_id) {
						$activity = new dims_activity();
						$activity->open($activity_id);
						if ($activity->fields['id_user'] == $_SESSION['dims']['userid']) {
							$bModify = true;
						}
					}
				}

				switch ($action) {
					case 'manage':
						if ($dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_CREATE)) {
							$elem = array();
							$elem['title'] = $_SESSION['cste']['ENTER_NEW_BUSINESS_EVENT'];
							$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/add24.png';
							$elem['link'] = './admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=activity&action=edit';
							$lstFunctions[] = $elem;
						}
						break;
					case 'view':
						if ($bModify) {
							$elem = array();
							$elem['title'] = 'Editer les informations générales';
							$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/editer20.png';
							$elem['link'] = './admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=activity&action=edit&activity_id='.$activity_id;
							$lstFunctions[] = $elem;
						}
					// pas de break ici
					case 'edit':
						if ( $bModify ) {
							$elem = array();
							$elem['title'] = 'Fermer l\'activité';
							$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/fermer20.png';
							if (!empty($_SERVER['HTTP_REFERER'])) {
								$elem['link'] = $_SERVER['HTTP_REFERER'];
							} else {
								$elem['link'] = _DESKTOP_TPL_PATH.'/gfx/common/fermer20.png';
							}
							$lstFunctions[] = $elem;
						}
						break;
				}
				break;
			case 'leads':
				$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
				$lead_id = dims_load_securvalue('lead_id', dims_const::_DIMS_CHAR_INPUT, true, true);

				// verif permissions
				$bModify = false;
				$bDelete = false;

				if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_MODIFY_OTHERS) ) {
					$bModify = true;
				}
				elseif ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_MODIFY_OWNS) ) {
					if ($lead_id) {
						$lead = new dims_lead();
						$lead->open($lead_id);
						if ($lead->fields['id_user'] == $_SESSION['dims']['userid']) {
							$bModify = true;
						}
					}
				}

				if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_DELETE_OTHERS) ) {
					$bDelete = true;
				}
				elseif ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_DELETE_OWNS) ) {
					if ($lead_id) {
						$lead = new dims_lead();
						$lead->open($lead_id);
						if ($lead->fields['id_user'] == $_SESSION['dims']['userid']) {
							$bDelete = true;
						}
					}
				}

				switch ($action) {
					case 'manage':
						if ($dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_CREATE)) {
							$elem = array();
							$elem['title'] = $_SESSION['cste']['ENTER_NEW_BUSINESS_OPPORTUNITY'];
							$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/add24.png';
							$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=leads&action=edit';
							$lstFunctions[] = $elem;
						}
						break;
					case 'view':
						if ($bModify) {
							$elem = array();
							$elem['title'] = 'Editer les informations générales';
							$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/editer20.png';
							$elem['link'] = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=leads&action=edit&lead_id='.$lead_id;
							$lstFunctions[] = $elem;
						}
					// pas de break ici
					case 'edit':
						if ($bDelete) {
							$elem = array();
							$elem['title'] = 'Supprimer l\'opportunité';
							$elem['img'] = _DESKTOP_TPL_PATH.'/gfx/common/fermer20.png';
							$elem['link'] = 'javascript:dims_confirmlink(\'/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=leads&action=delete&lead_id='.$lead_id.'\', \''.$_SESSION['cste']['_DIMS_LABEL_CONFIRM_DELETE'].'\')';
							$lstFunctions[] = $elem;
						}
						break;
				}
				break;
		}

		return $lstFunctions;
	}

	public function getRecentFolders() {
		global $dims;
		$lstFunctions = array();
		$currentworkspace = $dims->getWorkspaces($_SESSION['dims']['workspaceid']);
		return $lstFunctions;
	}

	public function getGenericTags($deb = 0, $fin = 0){
		global $dims;
		if ($deb == 0 && $fin == 0) {
			$sql = "SELECT		DISTINCT *
				FROM		dims_tag
				WHERE		type <4 and ((id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).") AND private=0)
				OR		(id_user = :iduser AND private=1))
				GROUP BY	tag
				ORDER BY	tag ASC";
		} else {
			$sql = "SELECT		DISTINCT *
				FROM		dims_tag
				WHERE		((id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).") AND private=0)
				OR		(id_user = :iduser AND private=1))
				GROUP BY	tag
				ORDER BY	tag ASC
				LIMIT		:limitstart,:limitend";
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $deb);
			$params[':limitend'] = array('type' => PDO::PARAM_INT, 'value' => $fin);
		}
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);

		$lstTags = array();
		$res = $this->db->query($sql, $params);
		while ($r = $this->db->fetchrow($res)){
			$tag = new tag();
			$tag->openWithFields($r);
			$lstTags[] = $tag;
		}
		return $lstTags;
	}

	public function getRecentlyTags($deb = 0, $fin = 0,$type=-1){
		global $dims;
		$typestring='';
		$params = array();

		if ($type!=-1) {
			$typestring=" and type=:type";
			$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
		}

		if (isset($_SESSION['dims']['desktopfilters']['expand_to_all_workspace']) && $_SESSION['dims']['desktopfilters']['expand_to_all_workspace'])
			$listworkspace=$dims->getListWorkspaces();
		else {
			$listworkspace=$_SESSION['dims']['workspaceid'];
		}

		if ($deb == 0 && $fin == 0) {
			$sql = "SELECT		DISTINCT t.*,
						COUNT(l.id) as cpte
				FROM		dims_tag as t
				INNER JOIN	dims_tag_index as l
				ON		l.id_tag = t.id
				WHERE		type <4 ".$typestring."
				AND		((t.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $listworkspace), 'idworkspace', $params).") AND private=0)
				OR		(t.id_user = :iduser AND private=1))
				GROUP BY	t.tag
				ORDER BY	l.id DESC";

		} else {
			$sql = "SELECT		DISTINCT t.*,
						COUNT(l.id) as cpte
				FROM		dims_tag as t
				INNER JOIN	dims_tag_index as l
				ON		l.id_tag = t.id
				WHERE		type <4 ".$typestring."
				AND		((t.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $listworkspace), 'idworkspace', $params).") AND private=0)
				OR		(t.id_user = :iduser AND private=1))
				GROUP BY	t.tag
				ORDER BY	t.tag
				LIMIT		:limitstart, :limitend";
				$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $deb);
				$params[':limitend'] = array('type' => PDO::PARAM_INT, 'value' => $fin);
		}
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);

		$lstTags = array();
		$res = $this->db->query($sql, $params);
		$splitted = $this->db->split_resultset($res);
		foreach ($splitted as $r){
			$tag = new tag();
			$tag->openWithFields($r['t']);
			$lstTags[] = $tag;
		}
		return $lstTags;
	}

	public function getSearchNewsletters($excludedNewsletter=array('0')) {
		global $dims;
		require_once(DIMS_APP_PATH . '/modules/system/class_newsletter.php');
		$newsletters=array();

		if (empty($excludedNewsletter)) $excludedNewsletter[0]=0;

		$sql = "SELECT n.*
			FROM dims_mod_newsletter as n

			WHERE n.id_workspace= :idworkspace
			AND n.id not in (".$this->db->getParamsFromArray($excludedNewsletter, 'idnewsletter', $params).")
			ORDER BY n.label ASC";
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		$res = $this->db->query($sql, $params);

		while($table = $this->db->fetchrow($res)) {
			$t = new newsletter();
			$t->openFromResultSet($table);
			$newsletters[] = $t;
		}
		return $newsletters;
	}

	public function getSearchTags($label, $deb = 0, $fin = 0, $excludedTags = array()){
		global $dims;

		$exclusionSql = '';
		$params = array();
		if(!empty($excludedTags)) {
			$exclusionSql = ' AND dims_tag.id NOT IN ('.$this->db->getParamsFromArray($excludedTags, 'idtag', $params).') ';
		}


		if ($deb == 0 && $fin == 0) {
			$sql = "SELECT		DISTINCT *
				FROM		dims_tag
				WHERE		type <4 and tag LIKE :taglabel
				AND		((id_workspace IN (".$this->db->getParamsFromArray($dims->getArrayWorkspaces(), 'idworkspace', $params).") AND private=0)
				OR		(id_user = :iduser AND private=1)
				OR		shared=1)
				$exclusionSql
				AND 		type = ".tag::TYPE_DEFAULT."
				GROUP BY	tag
				ORDER BY	tag ASC";
		} else {
			$sql = "SELECT		DISTINCT *
				FROM		dims_tag
				WHERE		type <4 and tag LIKE :taglabel
				AND		((id_workspace IN (".$this->db->getParamsFromArray($dims->getArrayWorkspaces(), 'idworkspace', $params).") AND private=0)
				OR		(id_user = :iduser AND private=1)
				OR		shared=1)
				$exclusionSql
				AND 		type = ".tag::TYPE_DEFAULT."
				GROUP BY	tag
				ORDER BY	tag ASC
				LIMIT		:limitstart, :limitend";
				$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $deb);
				$params[':limitend'] = array('type' => PDO::PARAM_INT, 'value' => $fin);
		}
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
		$params[':taglabel'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$label.'%');
		$lstTags = array();

		$res = $this->db->query($sql, $params);
		while ($r = $this->db->fetchrow($res)){
			$tag = new tag();
			$tag->openWithFields($r);
			$lstTags[] = $tag;
		}
		return $lstTags;
	}

	public function getAllContacts($type = 0, $id_contact = 0){
		if ($id_contact <= 0) $id_contact = $_SESSION['dims']['user']['id_contact'];
		global $dims;
		$res = array();
		switch($type){
			case dims_const::_SYSTEM_OBJECT_CONTACT :
				$notIn = array();
				$notIn[] = 0;
				$params = array();
				$sel = "SELECT		DISTINCT c.*
					FROM		dims_mod_business_contact c
					INNER JOIN	dims_mod_business_ct_link ct
					ON		c.id = ct.id_contact2
					WHERE		ct.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
					AND		ct.id_contact1 = :idcontact
					AND		ct.id_object = :idobject
					AND		c.inactif = 0
					ORDER BY	c.lastname, c.firstname";
				$params[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
				$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT);
				$rSel = $this->db->query($sel, $params);
				while($r = $this->db->fetchrow($rSel)){
					$ct = new contact();
					$ct->openWithFields($r);
					$res[] = $ct;
					$notIn[] = $r['id'];
				}
				$params = array();
				$sel = "SELECT		DISTINCT c.*
					FROM		dims_mod_business_contact c
					INNER JOIN	dims_mod_business_ct_link ct
					ON		c.id = ct.id_contact1
					WHERE		ct.id_contact1 NOT IN (".$this->db->getParamsFromArray($notIn, 'idcontact1', $params).")
					AND		ct.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
					AND		ct.id_contact2 = :idcontact2
					AND		ct.id_object = :idobject2
					AND		c.inactif = 0
					ORDER BY	c.lastname, c.firstname";
				$params[':idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
				$params[':idobject2'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT);
				$rSel = $this->db->query($sel, $params);
				while($r = $this->db->fetchrow($rSel)){
					$ct = new contact();
					$ct->openWithFields($r);
					$res[] = $ct;
				}
				break;
			case dims_const::_SYSTEM_OBJECT_TIERS :
				$params = array();
				$sel = "SELECT		DISTINCT e.*
					FROM		dims_mod_business_tiers e
					INNER JOIN	dims_mod_business_tiers_contact le
					ON		le.id_tiers = e.id
					AND		le.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
					AND		le.id_contact = :idcontact
					ORDER BY	intitule";
				$params[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
				$rSel = $this->db->query($sel, $params);
				while($r = $this->db->fetchrow($rSel)){
					$ct = new tiers();
					$ct->openWithFields($r);
					$res[] = $ct;
				}
				break;
			default:
				$res = array_merge($this->getAllContacts(dims_const::_SYSTEM_OBJECT_CONTACT,$id_contact),$this->getAllContacts(dims_const::_SYSTEM_OBJECT_TIERS,$id_contact));
				usort($res,'sortCtTiers');
				break;
		}
		return $res;
	}

	public function getLinkToTiers($id_tiers, $type = 0){
		global $dims;
		$res = array();
		switch($type){
			case dims_const::_SYSTEM_OBJECT_TIERS :
				$notIn = array();
				$params = array();
				$sel = "SELECT		DISTINCT c.*
					FROM		dims_mod_business_tiers c
					INNER JOIN	dims_mod_business_ct_link ct
					ON		c.id = ct.id_contact2
					WHERE		ct.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
					AND		ct.id_contact1 = :idtiers
					AND		ct.id_object = :idobject
					ORDER BY	c.intitule";
				$params[':idtiers'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
				$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_TIERS);
				$rSel = $this->db->query($sel);
				while($r = $this->db->fetchrow($rSel)){
					$ct = new tiers();
					$ct->openWithFields($r);
					$res[] = $ct;
					$notIn[] = $r['id'];
				}
				if (count($notIn) > 0){
					$params = array();
					$sel = "SELECT		DISTINCT c.*
						FROM		dims_mod_business_tiers c
						INNER JOIN	dims_mod_business_ct_link ct
						ON		c.id = ct.id_contact1
						WHERE		ct.id_contact1 NOT IN (".$this->db->getParamsFromArray($notIn, 'idcontact', $params).")
						AND		ct.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
						AND		ct.id_contact2 = :idtiers
						AND		ct.id_object = :idobject2
						ORDER BY	c.intitule";
					$params[':idtiers'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
					$params[':idobject2'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_TIERS);
					$rSel = $this->db->query($sel);
					while($r = $this->db->fetchrow($rSel)){
						$ct = new tiers();
						$ct->openWithFields($r);
						$res[] = $ct;
					}
				}
				break;
			case dims_const::_SYSTEM_OBJECT_CONTACT :
				$params = array();
				$sel = "SELECT		e.*
					FROM		dims_mod_business_contact e
					INNER JOIN	dims_mod_business_tiers_contact le
					ON		le.id_tiers = :idtiers
					AND		le.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
					AND		le.id_contact = e.id
					WHERE		e.inactif = 0
					ORDER BY	e.lastname, e.firstname";
				$params[':idtiers'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
				$rSel = $this->db->query($sel);
				while($r = $this->db->fetchrow($rSel)){
					$ct = new contact();
					$ct->openWithFields($r);
					$res[] = $ct;
				}
				break;
			default:
				$res = array_merge($this->getLinkToTiers($id_tiers,dims_const::_SYSTEM_OBJECT_CONTACT),$this->getLinkToTiers($id_tiers,dims_const::_SYSTEM_OBJECT_TIERS));
				usort($res,'sortCtTiers');
				break;
		}
		return $res;
	}

	public function loadLinksFromtiers(&$lttiers,$tlistid,$contactid=0,$tiersid=0) {
		// on charge les entreprises
		$idcts='0';

		$i=0;
		$params = array();
		$sql=	"SELECT 	tc.function, t.intitule, t.id,id_contact,tc.id as idlink,type_lien,date_fin
				FROM 		dims_mod_business_tiers_contact tc
				INNER JOIN dims_mod_business_tiers t ON t.id = tc.id_tiers

				WHERE 		tc.id_tiers IN (".$this->db->getParamsFromArray(explode(',', $tlistid), 'tlistid', $params).")
				AND 		tc.id_contact= :idcontact

				ORDER BY	tc.date_create DESC";
		$params[':idcontact'] = $contactid;
		$res = $this->db->query($sql, $params);

		while($tab = $this->db->fetchrow($res)){
			//if ($tab['id']==$tiersid) {
			$lttiers[$tab['id']]['links'][$tab['id_contact']]=$tab;
			//}
		}

   }
	 public function loadCompaniesFromContacts(&$ltcontacts, $type_link,$ctlistid,$tiersid=0) {
		// on charge les entreprises
		$idcts='0';

		if (!isset($ctlistid)) $ctlistid='0';
		$employers = array();
		$c = new dims_constant();
		$types = $c->getAllValues($type_link);
		$types = str_replace("'","''",$types);
		$i=0;
		$in = '';
		$in = "'".implode("','",$types)."'";

		$params = array();
		$params[':tiersid']=$tiersid;

		$sql = "SELECT		tc.function, t.intitule, t.id,id_contact
			FROM		dims_mod_business_tiers_contact tc
			INNER JOIN	dims_mod_business_tiers t ON t.id = tc.id_tiers

			WHERE		id_contact in (".$this->db->getParamsFromArray(explode(',', $ctlistid), 'idcontact', $params).")
			AND			(type_lien IN (".$this->db->getParamsFromArray($types, 'typelien', $params).")
			OR			tc.id_tiers= :tiersid)
			AND			(date_fin = 0 OR date_fin >= ".dims_createtimestamp().")
			ORDER BY	tc.date_create DESC";

		$res = $this->db->query($sql, $params);

		while($tab = $this->db->fetchrow($res)){
			//$employers[] = $tab;
			if ($tab['id_contact']>0 && isset($ltcontacts[$tab['id_contact']])) {
				if ($tab['id']==$tiersid) {
					$ltcontacts[$tab['id_contact']]['links'][$tab['id']]=$tab;
				}

				if ($tab['type_lien']=='employer')
					$ltcontacts[$tab['id_contact']]['employers'][$tab['id']]=$tab;
			}
		}

		//return $employers;
	}

	// filtre les id_globalobjects de la sortie de la matrice
	// en fonction des tags sélectionnés puis renvoie les objets
	public function getLinkedObjects($linkedObjectsIds = array(), $tags = array()) {
		// tri en fonction des tags
		if (sizeof($tags) && sizeof($linkedObjectsIds['goids'])) {
			$i = -1;
			$old_id_tag = 0;
			$a_goids = array();
			$params = array();
			$rs = $this->db->query('
				SELECT	id_tag, id_globalobject
				FROM	dims_tag_globalobject
				WHERE	id_tag IN ('.$this->db->getParamsFromArray($tags, 'idtag', $params).')
				AND	id_globalobject IN ('.$this->db->getParamsFromArray($linkedObjectsIds['goids'], 'idglobalobject', $params).')', $params);
			while ($row = $this->db->fetchrow($rs)) {
				if ($row['id_tag'] != $old_id_tag) {
					$i++;
					$old_id_tag = $row['id_tag'];
				}
				$a_goids[$i][] = $row['id_globalobject'];
			}

			// intersection entre les tags si il y en a plusieurs
			$final_goids = $a_goids[0];
			for ($t = 1; $t < sizeof($a_goids); $t++) {
				$final_goids = array_intersect($final_goids, $a_goids[$t]);
			}

			// filtrage
			$linkedObjectsIds['goids'] = array_intersect($linkedObjectsIds['goids'], $final_goids);
			foreach ($linkedObjectsIds['distribution'] as $objType => $a_objIds) {
				foreach ($a_objIds as $objId => $timestamp) {
					if (!isset($linkedObjectsIds['goids'][$objId])) {
						unset($linkedObjectsIds['distribution'][$objType][$objId]);
					}
				}
				if (!sizeof($linkedObjectsIds['distribution'][$objType])) {
					unset($linkedObjectsIds['distribution'][$objType]);
				}
			}
		}

		// recherche des objets
		$res = array();

		if (isset($linkedObjectsIds['distribution'])) {
			$listidtiers = "";
			if (isset($linkedObjectsIds['distribution']['tiers'])) {
				$params = array();
				$rs = $this->db->query('SELECT intitule,id_globalobject,id,photo
										FROM dims_mod_business_tiers
										WHERE id_globalobject
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['tiers']), 'idglobalobject', $params).')', $params);
				$listidtiers='0';
				while ($row = $this->db->fetchrow($rs)) {
					//$ct = new tiers();
					//$ct->openFromResultSet($row);
					//$res['ct'][] = $ct;
					$res['tiers'][$row['id']]=$row;
					$listidtiers.=','.$row['id'];
				}

			}

			if (strlen($listidtiers)>1) $listidtiers=substr($listidtiers,2);
			$res['tierslistid']=$listidtiers;

			$listidct='';
			if (isset($linkedObjectsIds['distribution']['contacts'])) {
				$params = array();
				$rs = $this->db->query('SELECT firstname,lastname,email,photo,id_globalobject,id
										FROM dims_mod_business_contact
										WHERE id_globalobject
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
										AND inactif = 0', $params);
				$listidct='0';
				while ($row = $this->db->fetchrow($rs)) {
					//$ct = new contact();
					//$ct->openFromResultSet($row);
					//$res['ct'][] = $ct;
					$res['ct'][$row['id']]=$row;
					$listidct.=','.$row['id'];
				}

			}

			if (strlen($listidct)>1) $listidct=substr($listidct,2);

			$res['ctlistid']=$listidct;

			if (isset($linkedObjectsIds['distribution']['events'])) {
				$params = array();
				$rs = $this->db->query('SELECT *
										FROM dims_mod_business_action
										WHERE id_globalobject
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['events']), 'idglobalobject', $params).')', $params);
				while ($row = $this->db->fetchrow($rs)) {
					$event = new action();
					$event->openFromResultSet($row);
					$res['event'][] = $event;
				}
			}

			if (isset($linkedObjectsIds['distribution']['activities'])) {
				$params = array();
				$rs = $this->db->query('SELECT *
										FROM dims_mod_business_action
										WHERE id_globalobject
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['activities']), 'idglobalobject', $params).')', $params);
				while ($row = $this->db->fetchrow($rs)) {
					$event = new action();
					$event->openFromResultSet($row);
					$res['activities'][] = $event;
				}
			}

			if (isset($linkedObjectsIds['distribution']['opportunities'])) {
				$params = array();
				$rs = $this->db->query('SELECT *
										FROM dims_mod_business_action
										WHERE id_globalobject
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['opportunities']), 'idglobalobject', $params).')', $params);
				while ($row = $this->db->fetchrow($rs)) {
					$event = new action();
					$event->openFromResultSet($row);
					$res['opportunities'][] = $event;
				}
			}

			if (isset($linkedObjectsIds['distribution']['docs'])) {
				$params = array();
				$rs = $this->db->query('SELECT *
										FROM dims_mod_doc_file
										WHERE id_globalobject
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['docs']), 'idglobalobject', $params).')', $params);
				while ($row = $this->db->fetchrow($rs)) {
					$doc = new docfile();
					$doc->openFromResultSet($row);
					$res['doc'][] = $doc;
					if (isset($linkedObjectsIds['distribution']['docs'][$doc->fields['id_globalobject']]['ref'])) {
						$res['doc'][sizeof($res['doc'])-1]->setLightAttribute('ref', $linkedObjectsIds['distribution']['docs'][$doc->fields['id_globalobject']]['ref']);
					}
				}
			}

			if (isset($linkedObjectsIds['distribution']['dossiers'])) {
				$params = array();
				$rs = $this->db->query('SELECT *
										FROM dims_case
										WHERE id_globalobject
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['dossiers']), 'idglobalobject', $params).')', $params);
				while ($row = $this->db->fetchrow($rs)) {
					$dossier = new dims_case();
					$dossier->openFromResultSet($row);
					$res['dossiers'][] = $dossier;
				}
			}

			if (isset($linkedObjectsIds['distribution']['suivis'])) {
				$params = array();
				$rs = $this->db->query('SELECT *
										FROM dims_mod_business_suivi
										WHERE id_globalobject
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['suivis']), 'idglobalobject', $params).')
										ORDER BY timestp_modify DESC', $params);
				while ($row = $this->db->fetchrow($rs)) {
					$suivi = new suivi();
					$suivi->openFromResultSet($row);
					$res['suivis'][] = $suivi;
				}
			}

			if (isset($linkedObjectsIds['distribution']['years'])) {
				$res['years'] = array_keys($linkedObjectsIds['distribution']['years']);
			}

			if (isset($linkedObjectsIds['distribution']['countries'])) {
				$params = array();
				require_once DIMS_APP_PATH.'modules/system/class_country.php';
				$rs = $this->db->query('SELECT *
										FROM dims_country
										WHERE id
										IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['countries']), 'idcountry', $params).')', $params);
				while ($row = $this->db->fetchrow($rs)) {
					$country = new country();
					$country->openFromResultSet($row);
					$res['countries'][] = $country;
				}
			}
		}

		return $res;
	}

	public function getLastLinkedContacts($type = 0){
		global $dims;
		$res = array();
		switch($type){
			case dims_const::_SYSTEM_OBJECT_CONTACT :
				$notIn = array();
				$sel = "SELECT		DISTINCT c.*
					FROM		dims_mod_business_contact c
					INNER JOIN	dims_mod_business_ct_link ct
					ON		c.id = ct.id_contact2
					WHERE		ct.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
					AND		ct.time_create >= ".date("Ymd000000",mktime(0,0,0,date('m'),date('d')-7))."
					AND		ct.id_contact1 = :idcontact
					AND		ct.id_object = :idobject
					AND		c.inactif = 0
					ORDER BY	c.lastname, c.firstname";
				$params[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']);
				$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT);
				$rSel = $this->db->query($sel, $params);
				while($r = $this->db->fetchrow($rSel)){
					$ct = new contact();
					$ct->openWithFields($r);
					$res[] = $ct;
					$notIn[] = $r['id'];
				}
				if (count($notIn) > 0){
					$params = array();
					$sel = "SELECT		DISTINCT c.*
						FROM		dims_mod_business_contact c
						INNER JOIN	dims_mod_business_ct_link ct
						ON		c.id = ct.id_contact1
						WHERE		ct.id_contact1 NOT IN (".$this->db->getParamsFromArray($notIn, 'idcontact1', $params).")
						AND		ct.time_create >= ".date("Ymd000000",mktime(0,0,0,date('m'),date('d')-7))."
						AND		ct.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
						AND		ct.id_contact2 = :idcontact2
						AND		ct.id_object = :idobject2
						AND		c.inactif = 0
						ORDER BY	c.lastname, c.firstname";
					$params[':idcontact2']	= array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']);
					$params[':idobject2']	= array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT);
					$rSel = $this->db->query($sel, $params);
					while($r = $this->db->fetchrow($rSel)){
						$ct = new contact();
						$ct->openWithFields($r);
						$res[] = $ct;
					}
				}
				break;
			case dims_const::_SYSTEM_OBJECT_TIERS :
				$params = array();
				$sel = "SELECT		e.*
					FROM		dims_mod_business_tiers e
					INNER JOIN	dims_mod_business_tiers_contact le
					ON		le.id_tiers = e.id
					AND		le.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
					AND		le.id_contact = :idcontact
					AND		le.date_create >= ".date("Ymd000000",mktime(0,0,0,date('m'),date('d')-7))."
					ORDER BY	e.intitule";
				$params[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']);
				$rSel = $this->db->query($sel, $params);
				while($r = $this->db->fetchrow($rSel)){
					$ct = new tiers();
					$ct->openWithFields($r);
					$res[] = $ct;
				}
				break;
			default:
				$res = array_merge($this->getLastLinkedContacts(dims_const::_SYSTEM_OBJECT_CONTACT),$this->getLastLinkedContacts(dims_const::_SYSTEM_OBJECT_TIERS));
				usort($res,'sortCtTiers');
				break;
		}
		return $res;
	}

	public function getFavoritesContacts($type = 0){
		global $dims;
		$res = array();
		switch($type){
			case dims_const::_SYSTEM_OBJECT_CONTACT :
				$notIn = array();
				$sel = "SELECT		DISTINCT c.*
					FROM		dims_mod_business_contact c
					INNER JOIN	dims_favorite df
					ON		c.id_globalobject = df.id_globalobject
					WHERE		df.id_user = :iduser
					AND		df.status = :status
					AND		c.inactif = 0
					ORDER BY	c.lastname, c.firstname";
				$rSel = $this->db->query($sel, array(
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':status' => favorite::Favorite
				));
				while($r = $this->db->fetchrow($rSel)){
					$ct = new contact();
					$ct->openWithFields($r);
					$res[] = $ct;
					$notIn[] = $r['id'];
				}
				break;
			case dims_const::_SYSTEM_OBJECT_TIERS :
				$sel = "SELECT		e.*
					FROM		dims_mod_business_tiers e
					INNER JOIN	dims_favorite df
					ON		e.id_globalobject = df.id_globalobject
					WHERE		df.id_user = :iduser
					AND		df.status = :status
					ORDER BY	e.intitule";
				$rSel = $this->db->query($sel, array(
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':status' => favorite::Favorite
				));
				while($r = $this->db->fetchrow($rSel)){
					$ct = new tiers();
					$ct->openWithFields($r);
					$res[] = $ct;
				}
				break;
			default:
				$res = array_merge($this->getFavoritesContacts(dims_const::_SYSTEM_OBJECT_CONTACT),$this->getFavoritesContacts(dims_const::_SYSTEM_OBJECT_TIERS));
				usort($res,'sortCtTiers');
				break;
		}
		return $res;
	}

	public function getMonitoredContacts(){
		global $dims;
		$res = array();
		return $res;
	}

	public function getGroupsUser($id_user = 0){
		$lstGr = array();
		if ($id_user == 0) $id_user = $_SESSION['dims']['userid'];
		$sel = "SELECT		*
			FROM		dims_mod_business_contact_group
			WHERE		id_user_create = :iduser
			ORDER BY	label";
		$res = $this->db->query($sel, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		));
		while($r = $this->db->fetchrow($res)){
			$gr = new ct_group();
			$gr->openWithFields($r);
			$lstGr[$r['id']] = $gr;
		}
		return $lstGr;
	}

	public function getTagForObject($type_tag,$id_object,$type_object){
		global $dims;
		$lstTag = array();
		$params = array();
		$sql = "SELECT			DISTINCT t.*
			FROM			dims_tag as t
			INNER JOIN		dims_tag_index as l
			ON			l.id_tag = t.id
			WHERE			((t.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).",0) AND private=0)
						OR (t.id_user = :iduser AND private=1))
			AND			t.type = :typetag
			AND			l.id_record = :idrecord
			AND			l.id_object = :idobject
			GROUP BY		t.tag
			ORDER BY		t.tag ASC";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
		$params[':typetag'] = array('type' => PDO::PARAM_STR, 'value' => $type_tag);
		$params[':idrecord'] = array('type' => PDO::PARAM_INT, 'value' => $id_object);
		$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $type_object);
		$res = $this->db->query($sql, $params);
		while ($r = $this->db->fetchrow($res)){
			$tag = new tag();
			$tag->openWithFields($r);
			$lstTag[$r['id']] = $tag;
		}
		return $lstTag;
	}

// --------- Fonctions pour la recherche avancée
	public function getAvailableYears(&$years){
		if(empty($years) || (isset($_SESSION['dims']['refresh_years']) && mktime(date('H'),date('i')-5) > $_SESSION['dims']['refresh_years'])){
			$res = $this->db->query("SELECT DISTINCT(year) FROM dims_matrix WHERE year > 0 ORDER BY year DESC");
			while($y = $this->db->fetchrow($res)){
                if ($y['year']>1900)
				$years[$y['year']] = $y['year'];
			}
			$_SESSION['dims']['refresh_years'] = time();
		}
		return $years;
	}

	public function getCountries(&$countries, $lang='en'){
		if(empty($countries)){
			if($lang=='fr') $col = 'fr';
			else $col = 'printable_name';
			$res = $this->db->query("SELECT id, ".$col." as label FROM dims_country ORDER BY ".$col." ASC");
			while($c = $this->db->fetchrow($res)){
				$countries[$c['id']] = $c['label'];
			}
		}
		return $countries;
	}

	public function constructLstTiersFromCt($lstCt = array(), $lstCtId = array(), $myListTiers = array()){
		if (count($lstCt) > count($lstCtId)){
			$lstCtId = array();
			foreach($lstCt as $ct)
				$lstCtId[$ct->fields['id']]['id'] = $ct->fields['id'];
		}elseif(count($lstCt) < count($lstCtId)){
			$params = array();
			$sel = "SELECT	*
					FROM	dims_mod_business_contact
					WHERE	id IN (".$this->db->getParamsFromArray(array_keys($lstCtId), 'idcontact', $params).")
					AND	inactif = 0";
			$res = $this->db->query($sel, $params);
			while($r = $this->db->fetchrow($res)){
				$ct = new contact();
				$ct->openWithFields($r);
				$lstCt[$r['id']] = $ct;
			}
		}

		$lstTiers = array();
		$lstBisTiers = array();
		if (count($lstCt) > 0){
			$c = new dims_constant();
			$types = $c->getAllValues('_DIMS_LABEL_EMPLOYEUR');
			$params = array();
			$sql = "SELECT		t.*, tc.id_contact
				FROM		dims_mod_business_tiers t
				INNER JOIN	dims_mod_business_tiers_contact tc
				ON		t.id = tc.id_tiers
				WHERE		tc.id_contact IN (".$this->db->getParamsFromArray(array_keys($lstCtId), 'idcontact', $params).")
				AND		tc.type_lien IN (".$this->db->getParamsFromArray($types, 'typelien', $params).")
				AND		(tc.date_fin = 0 OR tc.date_fin >= ".dims_createtimestamp().")
				ORDER BY	tc.date_create DESC";

			$res = $this->db->query($sql, $params);
			$splitted = $this->db->split_resultset($res);
			foreach($splitted as $r){
				if (isset($lstCt[$r['tc']['id_contact']])){
					if (!isset($lstTiers[$r['t']['id']]) && (!isset($lstCtId[$r['tc']['id_contact']]['src']) || (isset($lstCtId[$r['tc']['id_contact']]['src']) && $lstCtId[$r['tc']['id_contact']]['src']==$r['t']['id'] ))   ){
						$t = new tiers();
						$t->openWithFields($r['t']);
						$t->contacts = array();
						$lstTiers[$r['t']['id']] = $t;
						$lstTiers[$r['t']['id']]->contacts[] = $lstCt[$r['tc']['id_contact']];
						unset($lstCt[$r['tc']['id_contact']]);
						$lstBisTiers[] = $r['t']['id'];
					}
					else if(isset($lstTiers[$r['t']['id']]) && (!isset($lstCtId[$r['tc']['id_contact']]['src']) || (isset($lstCtId[$r['tc']['id_contact']]['src']) && $lstCtId[$r['tc']['id_contact']]['src']==$r['t']['id'] ))){
						$lstTiers[$r['t']['id']]->contacts[] = $lstCt[$r['tc']['id_contact']];
						unset($lstCt[$r['tc']['id_contact']]);
					}


				}
			}
		}
		foreach($myListTiers as $id){
			if (!in_array($id,$lstBisTiers)){
				$lstBisTiers[] = $id;
				$tiers = new tiers();
				$tiers->open($id);
				$tiers->contacts = array();
				$lstTiers[$id] = $tiers;
			}
		}
		usort($lstTiers,'sortCtTiers');
		if (count($lstCt) > 0){
			$t = new tiers();
			$t->init_description();
			$t->fields['id'] = 0;
			$t->fields['intitule'] = $_SESSION['cste']['_IMPORT_UNKNOWN_TIER'];
			$t->fields['id_globalobject'] = 0;
			$t->contacts = $lstCt;
			$lstTiers[] = $t;
		}
		return $lstTiers;
	}

	public function getVcard($search = ""){
		require_once DIMS_APP_PATH."modules/system/class_dims_vcard.php";
		// update des vcards dispo
		$sel = "SELECT		".docfile::TABLE_NAME.".*
			FROM		".docfile::TABLE_NAME."
			LEFT JOIN	".dims_vcard::TABLE_NAME."
			ON		".dims_vcard::TABLE_NAME.".id_docfile = ".docfile::TABLE_NAME.".id
			WHERE		".dims_vcard::TABLE_NAME.".id_docfile IS NULL
			AND		".docfile::TABLE_NAME.".id_user = :iduser
			AND		".docfile::TABLE_NAME.".extension = 'vcf'";
		$res = $this->db->query($sel, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
		));
		if ($this->db->numrows($res) > 0){
			while($r = $this->db->fetchrow($res)){
				$doc = new docfile();
				$doc->openWithFields($r);
				foreach($doc->getParseVcf() as $key => $datas){
					$vcard = new dims_vcard();
					$vcard->fields['id_docfile'] = $doc->fields['id'];
					$vcard->fields['name_vcard'] = $datas['prenom']." ".$datas['nom'];
					$vcard->fields['id_contact'] = 0;
					$vcard->fields['num'] = $key+1;
					$vcard->fields['date_modify'] = 0;
					$vcard->save();
				}
			}
		}

		$params = array();
		// selections des vcards
		$sel = "SELECT		".dims_vcard::TABLE_NAME.".*
			FROM		".dims_vcard::TABLE_NAME."
			INNER JOIN	".docfile::TABLE_NAME."
			ON		".dims_vcard::TABLE_NAME.".id_docfile = ".docfile::TABLE_NAME.".id
			WHERE		".docfile::TABLE_NAME.".id_user = :iduser
			AND		".docfile::TABLE_NAME.".extension = 'vcf'";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
		$tmp = $_SESSION['desktopv2']['opportunity']['ct_added'];
		unset($tmp[0]);
		if (count($tmp) > 0) {
			$sel .= " AND	".dims_vcard::TABLE_NAME.".id_contact NOT IN (".$this->db->getParamsFromArray($tmp, 'idcontact', $params).") ";
		}
		if ($search != '') {
			$sel .= " AND	".dims_vcard::TABLE_NAME.".name_vcard LIKE :search ";
			$params[':search'] = array('type' => PDO::PARAM_INT, 'value' => '%'.$search.'%');
		}
		$lstVcard = array();
		$res = $this->db->query($sel, $params);
		while($r = $this->db->fetchrow($res)){
			$vcard = new dims_vcard();
			$vcard->openWithFields($r);
			$lstVcard[] = $vcard;
		}
		return $lstVcard;
	}

	public function getEvents($lsteventid = ""){
		require_once DIMS_APP_PATH."modules/system/class_action.php";
		global $dims;
		$lstEvent = array();

				 if (empty($lsteventid)) $lsteventid[]=0;

		$params = array();
		$sel = "SELECT		*
			FROM		dims_mod_business_action
			WHERE		id IN (".$this->db->getParamsFromArray($lsteventid, 'idaction', $params).")
			ORDER BY	libelle,datejour DESC ";
		$res = $this->db->query($sel, $params);
		while ($r = $this->db->fetchrow($res)){
			$event = new action();
			$event->openWithFields($r);
			$lstEvent[] = $event;
		}

		return $lstEvent;
		}
}
