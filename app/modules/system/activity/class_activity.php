<?php

require_once DIMS_APP_PATH."include/ajax_datatable.php";
require_once DIMS_APP_PATH."modules/system/activity/class_type.php";

class dims_activity extends dims_data_object implements ajax_datatable {

	const TABLE_NAME = 'dims_mod_business_action';

	// type d'action
	const TYPE_ACTION = "_DIMS_EVENT_ACTIVITIES";

	const STATUS_ALL 			= 0;
	const STATUS_TO_COME 		= 1;
	const STATUS_PASSED 		= 2;
	const STATUS_CLOSED 		= 3;


	public function __construct() {
		parent::dims_data_object('dims_mod_business_action');
	}

	public function get_sTable() {
		return " ".$this->tablename." as a ";
	}

    public function get_aColumns() {
		return array('CONCAT(a.datejour, a.close)', 'activity_type_id', 'datejour', 'libelle', '', '');
	}

	public function get_sIndexColumn() {
		return "a.id";
	}


	/**
	 * @return (array) 	[0] => (string)	Clauses AND
	 *                  [1] => (array) Tableau de paramètres des clauses AND
	 */
	public function get_sWhere() {
		$params = array();
		$where = " AND typeaction= :typeaction AND id_workspace = :workspaceid ";
		$params[':typeaction']	= self::TYPE_ACTION;
		$params[':workspaceid']	= $_SESSION['dims']['workspaceid'];


		// filtres
		if ($_SESSION['desktopv2']['activity']['filters']['responsible'] > 0) {
			$where .= ' AND id_responsible = :idresponsible ';
			$params[':idresponsible'] = $_SESSION['desktopv2']['activity']['filters']['responsible'];
		}
		if ($_SESSION['desktopv2']['activity']['filters']['status'] > -1) {
			switch ($_SESSION['desktopv2']['activity']['filters']['status']) {
				case self::STATUS_TO_COME:
					$where .= ' AND datejour > \''.date('Y-m-d').'\'';
					break;
				case self::STATUS_PASSED:
					$where .= ' AND close = 0 AND datejour <= \''.date('Y-m-d').'\'';
					break;
				case self::STATUS_CLOSED:
					$where .= ' AND close = 1';
					break;
			}
		}
		if ($_SESSION['desktopv2']['activity']['filters']['type'] > 0) {
			$where .= ' AND activity_type_id = :activitytypeid ';
			$params[':activitytypeid'] = $_SESSION['desktopv2']['activity']['filters']['type'];
		}

		if (!$_SESSION['desktopv2']['activity']['filters']['whole_period']) {
			// date de départ
			$ts_start = $_SESSION['desktopv2']['activity']['filters']['slider_date'];

			// nb de jours avant / apres
			$where .= '
				AND datejour >= \''.date('Y-m-d', $ts_start - $_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] * 86400).'\'
				AND datejour <= \''.date('Y-m-d', $ts_start + $_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] * 86400).'\'';
			// $where .= ' AND ( datefin <= \''.date('Y-m-d', $ts_start + $_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] * 86400).'\' OR datefin = \'0000-00-00\' )';
		}

		return array($where,$params);
	}

	public function get_aaData($res_query) {
		$aaData = array();
		$dims = dims::getInstance();
		$db = $dims->getDb();
		$separation = $db->split_resultset($res_query);
		$current_date = current(explode(" ", dims_getdatetime()));

		foreach ($separation as $tab) {
			// gestion des permissions
			$bView = false;
			if (
				($tab['a']['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_VIEW_OWNS))
				|| ($tab['a']['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_VIEW_OTHERS))
			) {
				$bView = true;
			}

			// ouverture des participants
			require_once DIMS_APP_PATH.'modules/system/class_search.php';
			$matrix = new search();
			$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, array($tab['a']['id_globalobject']));

			$a_contacts = array();
			if (!empty($linkedObjectsIds['distribution']['contacts'])) {
				$rs = $this->db->query('
					SELECT c.*, u.id
					FROM dims_mod_business_contact c
					LEFT JOIN dims_user u
					ON u.id_contact = c.id
					WHERE c.id_globalobject IN ('.implode(',', array_fill(0, count(array_keys($linkedObjectsIds['distribution']['contacts'])), '?')).')',
					array_keys($linkedObjectsIds['distribution']['contacts']));
				$sepContacts = $this->db->split_resultset($rs);
				foreach ($sepContacts as $tabContacts) {
					$contact = new contact();
					$contact->openFromResultSet($tabContacts['c']);
					$a_contacts[] = $contact->fields['firstname'].' '.$contact->fields['lastname'];

					// si on est participant
					if ($tabContacts['u']['id'] == $_SESSION['dims']['userid']) {
						$bView = true;
					}
				}
			}

			$res = array();

			// statut
			if(strcmp($tab['a']['datejour'], $current_date) >= 0) {
				$status_img = "future_activity16.png";
				$status_title = 'A venir';
			}
			else if($tab['a']['close']) {
				$status_img = "actif16.png";
				$status_title = 'Fermée';
			}
			else {
				$status_img = "avalider16.png";
				$status_title = 'Passée';
			}
			$res[] = "<img src='"._DESKTOP_TPL_PATH."/gfx/common/".$status_img."' alt='".$status_title."' title='".$status_title."'/>";

			// type
			if(!empty($tab['a']['activity_type_id'])) {
				$activity_type = new activity_type();
				$activity_type->open($tab['a']['activity_type_id']);
                if (isset($activity_type->fields['label']))
                    $res[] = $activity_type->fields['label'];
                else
                    $res[] = '';
			}
			else {
				$res[] = '';
			}

			// dates
			$a_df = explode('-', $tab['a']['datejour']);
			$date_from = $a_df[2].'/'.$a_df[1].'/'.$a_df[0];

			$a_dt = explode('-', $tab['a']['datefin']);
			$date_to = $a_dt[2].'/'.$a_dt[1].'/'.$a_dt[0];

			$horaires = '';
			$heuredeb = ($tab['a']['heuredeb'] != '00:00:00') ? substr($tab['a']['heuredeb'], 0, -3) : '';
			$heurefin = ($tab['a']['heurefin'] != '00:00:00') ? substr($tab['a']['heurefin'], 0, -3) : '';

			if ($tab['a']['datefin'] != '0000-00-00' && $tab['a']['datefin'] != $tab['a']['datejour']) {
				if ($heuredeb != '') {
					$heuredeb = ' à '.$heuredeb;
				}
				if ($heurefin != '') {
					$heurefin = ' à '.$heurefin;
				}
				$res[] = 'du '.$date_from.$heuredeb.' au '.$date_to.$heurefin;
			}
			else {
				if ($heuredeb != '') {
					$horaires = ' ('.$heuredeb;
					if ($heurefin != '') {
						$horaires .= ' - '.$heurefin;
					}
					$horaires .= ')';
				}
				$res[] = $date_from.$horaires;
			}

			// description
			$res[] = stripslashes($tab['a']['libelle']);

			// contacts
			$res[] = implode('<br/>', $a_contacts);

			if (!defined('_ACTIVE_OPPORTUNITY') || _ACTIVE_OPPORTUNITY) {
				// opportunité
				$res[] = '';
			}

			// actions
			$detail = '<a href="javascript:void(0);" onclick="javascript:document.location.href=\''.$dims->getScriptEnv().'?action=view&activity_id='.$tab['a']['id'].'\';" title="Voir le détail"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/open_record16.png" alt="Voir le détail" /></a>';

			// verif permission
			$suppr = '';
			$bSuppr = false;
			if (
				($tab['a']['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_DELETE_OWNS))
				|| ($tab['a']['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_DELETE_OTHERS))
			) {
				$bSuppr = true;
			}
			if ($bSuppr) {
				$suppr = ' <a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$dims->getScriptEnv().'?action=delete_activity&id='.$tab['a']['id'].'\',\'Êtes-vous sûr de vouloir supprimer cette activité ?\');" title="Supprimer"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/close.png" alt="Supprimer" /></a>';
			}

			$res[] = $detail.$suppr;

			if ($bView) {
				$aaData[] = $res;
			}
		}

		return $aaData;
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
		$params = array( ':type_action' => self::TYPE_ACTION );
		$query = "
			SELECT *
			FROM ".self::TABLE_NAME."
			WHERE typeaction= :type_action
			AND datejour = (
				SELECT MIN(datejour)
				FROM ".self::TABLE_NAME."
				WHERE typeaction= :type_action
				AND datejour<>'0000-00-00'
			)";
		$res = $this->db->query($query, $params);
		return $this->db->fetchrow($res);
	}

	public function getNewest() {
		$params = array( ':type_action' => self::TYPE_ACTION );
		$query = "
			SELECT *
			FROM ".self::TABLE_NAME."
			WHERE typeaction= :type_action
			AND datejour = (
				SELECT MAX(datejour)
				FROM ".self::TABLE_NAME."
				WHERE typeaction= :type_action
				)";
		$res = $this->db->query($query, $params);
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
		$type = new activity_type();
		if(!empty($this->fields['activity_type_id']) && $this->fields['activity_type_id'] != '' && $this->fields['activity_type_id'] > 0)
			$type->open($this->fields['activity_type_id']);
		else
			$type->init_description();

		return $type;
	}

	public function getDescriptionHTML() {
		return nl2br($this->fields['description']);
	}

	public function getDescriptionRaw() {
		return $this->fields['description'];
	}

}
