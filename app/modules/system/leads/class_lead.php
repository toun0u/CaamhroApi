<?php

require_once DIMS_APP_PATH."include/ajax_datatable.php";

class dims_lead extends dims_data_object implements ajax_datatable {
	const TABLE_NAME = 'dims_mod_business_action';

	// type d'action
	const TYPE_ACTION = "_DIMS_EVENT_OPPORTUNITIES";

	const STATUS_IN_PROGRESS	= 1;
	const STATUS_LOST 			= 2;
	const STATUS_ABANDONED 		= 3;
	const STATUS_WON			= 4;


	public function __construct() {
		parent::dims_data_object('dims_mod_business_action');
	}

	public function save() {
		parent::save(dims_const::_SYSTEM_OBJECT_OPPORTUNITY);
	}

	public function abandon($save = true) {
		$this->fields['status'] = self::STATUS_ABANDONED;
		if ($save) {
			parent::save();
		}
	}

	public function setid_object() {
		$this->id_globalobject = dims_const::_SYSTEM_OBJECT_OPPORTUNITY;
	}

	public function settitle() {
		$this->title = $this->fields['libelle'];
	}

	public function get_sTable() {
		return " ".$this->tablename." a INNER JOIN dims_mod_business_tiers t ON t.id = a.tiers_id INNER JOIN dims_mod_business_produit p ON p.id = a.opportunity_product_id";
	}

    public function get_aColumns() {
		return array('a.datefin', 'a.opportunity_budget', 'a.libelle', 't.intitule', 'p.libelle');
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
		$where = " AND a.typeaction= :typeaction ";
		$params[':typeaction'] = self::TYPE_ACTION;

		// filtres
		if ($_SESSION['desktopv2']['lead']['filters']['status'] > 0) {
			$where .= ' AND status = :status ';
			$params[':status'] = $_SESSION['desktopv2']['lead']['filters']['status'];
		}
		if ($_SESSION['desktopv2']['lead']['filters']['responsible'] > 0) {
			$where .= ' AND a.id_responsible = :idresponsible ';
			$params[':idresponsible'] = $_SESSION['desktopv2']['lead']['filters']['responsible'];
		}
		if ($_SESSION['desktopv2']['lead']['filters']['tiers'] > 0) {
			$where .= ' AND a.tiers_id = :tiersid ';
			$params[':tiersid'] = $_SESSION['desktopv2']['lead']['filters']['tiers'];
		}
		if ($_SESSION['desktopv2']['lead']['filters']['product'] > 0) {
			$where .= ' AND a.opportunity_product_id = :opportunityproductid ';
			$params[':opportunityproductid'] = $_SESSION['desktopv2']['lead']['filters']['product'];
		}
		if ($_SESSION['desktopv2']['lead']['filters']['echeance_deb'] != '') {
			$a_ed = explode('/', $_SESSION['desktopv2']['lead']['filters']['echeance_deb']);
			$where .= ' AND datefin >= :datefin1 ';
			$params[':datefin1'] = $a_ed[2].'-'.$a_ed[1].'-'.$a_ed[0];
		}
		if ($_SESSION['desktopv2']['lead']['filters']['echeance_fin'] != '') {
			$a_ef = explode('/', $_SESSION['desktopv2']['lead']['filters']['echeance_fin']);
			$where .= ' AND datefin <= :datefin2 ';
			$params[':datefin2'] = $a_ef[2].'-'.$a_ef[1].'-'.$a_ef[0];
		}

		// budget
		$where .= '
			AND a.opportunity_budget BETWEEN :between1 AND :between2
			AND a.id_workspace = :workspaceid ';
		$params[':between1']	= $_SESSION['desktopv2']['lead']['filters']['budget_min'];
		$params[':between2']	= $_SESSION['desktopv2']['lead']['filters']['budget_max'];
		$params[':workspaceid']	= $_SESSION['dims']['workspaceid'];

		return array($where, $params);
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
				($tab['a']['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OWNS))
				|| ($tab['a']['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OTHERS))
			) {
				$bView = true;
			}
			$res = array();

			// ouverture des participants
			require_once DIMS_APP_PATH.'modules/system/class_search.php';
			$matrix = new search();
			$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, null, array($tab['a']['id_globalobject']));

			if (!empty($linkedObjectsIds['distribution']['contacts'])) {
				$params = array();
				$rs = $this->db->query(
					'SELECT u.id
					FROM dims_mod_business_contact c
					INNER JOIN dims_user u
					ON u.id_contact = c.id
					WHERE c.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')', $params);
				while ($row = $this->db->fetchrow($rs)) {
					// si on est participant
					if ($row['id'] == $_SESSION['dims']['userid']) {
						$bView = true;
					}
				}
			}

			// statut
			switch ($tab['a']['status']) {
				case self::STATUS_IN_PROGRESS:
				default:
					$status_img = 'puce_blanche16.png';
					$status_title = 'En cours';
					break;
				case self::STATUS_LOST:
					$status_img = 'inactif16.png';
					$status_title = 'Perdu';
					break;
				case self::STATUS_ABANDONED:
					$status_img = 'avalider16.png';
					$status_title = 'Abandonné';
					break;
				case self::STATUS_WON:
					$status_img = 'actif16.png';
					$status_title = 'Gagné';
					break;
			}
			$res[] = "<img src='"._DESKTOP_TPL_PATH."/gfx/common/".$status_img."' alt='".$status_title."' title='".$status_title."'/>";

			// dates
			$df = explode('-', $tab['a']['datefin']);
			$res[] = $df[2].'/'.$df[1].'/'.$df[0];

			// budget
			$res[] = $tab['a']['opportunity_budget'];

			// libelle
			$res[] = stripslashes($tab['a']['libelle']);

			// compte
			$res[] = $tab['t']['intitule'];

			// produit
			$res[] = $tab['p']['libelle'];

			// actions
			$detail = '<a href="javascript:void(0);" onclick="javascript:document.location.href=\''.$dims->getScriptEnv().'?action=view&lead_id='.$tab['a']['id'].'\';" title="Voir le détail"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/open_record16.png" alt="Voir le détail" /></a>';

			// verif permission
			$suppr = '';
			$bSuppr = false;
			if (
				($tab['a']['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_DELETE_OWNS))
				|| ($tab['a']['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_DELETE_OTHERS))
			) {
				$bSuppr = true;
			}
			if ($bSuppr) {
				$suppr = ' <a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$dims->getScriptEnv().'?action=delete_lead&id='.$tab['a']['id'].'\',\'Êtes-vous sûr de vouloir supprimer cette activité ?\');" title="Supprimer"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/close.png" alt="Supprimer" /></a>';
			}
			$res[] = $detail.$suppr;

			if ($bView) {
				$aaData[] = $res;
			}
		}

		return $aaData;
	}

	public function getOldest() {
		$query = 	"SELECT * from ".$this->tablename."
					WHERE typeaction= :typeaction
					AND datejour = (	SELECT min(datejour)
										FROM ".$this->tablename."
										WHERE typeaction= :typeaction
										AND datejour<>'0000-00-00')";
		$res = $this->db->query($query, array(
			':typeaction'	=> self::TYPE_ACTION
		));
		return $this->db->fetchrow($res);
	}

	public function getNewest() {
		$query = 	"SELECT * from ".$this->tablename."
					WHERE typeaction='".self::TYPE_ACTION."'
					AND datejour = (	SELECT max(datejour)
										FROM ".$this->tablename."
										WHERE typeaction= :typeaction )";
		$res = $this->db->query($query, array(
			':typeaction'	=> self::TYPE_ACTION
		));
		return $this->db->fetchrow($res);
	}

	public static function getMaxBudget() {
		$db = dims::getInstance()->getDb();
		$rs = $db->query('SELECT MAX(opportunity_budget) AS m FROM '.self::TABLE_NAME.' WHERE typeaction = :typeaction ', array(
			':typeaction'	=> self::TYPE_ACTION
		));
		$row = $db->fetchrow($rs);
		return intval($row['m']);
	}

	public function getStatus() {
		return $this->fields['status'];
	}

	public function getDescriptionHTML() {
		return str_replace('\r\n', '<br/>', $this->fields['description']);
	}

	public function getDescriptionRaw() {
		return stripslashes(str_replace('\r\n', "\r\n", $this->fields['description']));
	}

}
