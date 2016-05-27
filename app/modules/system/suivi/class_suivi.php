<?php
class suivi extends pagination {

	const DATE_FORMAT_DISPLAY_FR	= 'd/m/Y';
	const DATE_FORMAT_DISPLAY_US	= 'Y-m-d';
	const DATE_FORMAT_BIGINT		= 'Ymd';

	const TYPE_TOUS		= "All";
	const TYPE_DEVIS	= 'Devis';
	const TYPE_FACTURE	= 'Facture';
	const TYPE_AVOIR	= 'Avoir';

	const SUIVI_TOUS		= -1;
	const SUIVI_OUI			= 1;
	const SUIVI_NON			= 0;

	const SUIVI_TRI_ASC		= true;
	const SUIVI_TRI_DESC	= false;

	const TABLE_NAME = "dims_mod_business_suivi";

	private $tiers;
	private $orders;

	public static $cols = array(
		"s.valide",
		"s.type",
		"CONCAT(s.type, s.exercice, s.id)",
		"s.libelle",
		"s.exercice",
		"s.timestp_modify",
		"s.montantht * 100 / (100 - remise)",
		"s.remise",
		"s.montantht",
		"s.montantttc",
		"s.solde"
	);

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id_suivi');
	}

	public function getAll($search_text='', $pagination=false, $filters = "", $orders = array(), $linkedObjectsIds = array()) {
		$params = array();
		if($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getAll($search_text, true, $filters, $linkedObjectsIds));
			$limit = " LIMIT :limitstart, :limitkey";
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		}

		else $limit="";

		if(empty($orders))
			$order_by = "s.timestp_modify DESC";

		else {
			$keys = array_keys($orders);
			$key = array_shift($keys);
			$order_by = $key." ".(array_shift($orders) == self::SUIVI_TRI_ASC ? "ASC" : "DESC");

			foreach($orders as $key => $value) {
				$order_by .= ", ".$key." ".($value == suivi::SUIVI_TRI_ASC ? "ASC"  : "DESC");
			}
		}

		$sel = "SELECT s.*, t.share_suivi, t.id_workspace AS tiers_workspace
			FROM ".self::TABLE_NAME." s
			LEFT JOIN `dims_mod_business_tiers` t
			ON t.id = s.tiers_id
			WHERE 1=1 ";

		if(!empty($search_text)) {
			$sel .= " AND (s.type LIKE :searchtext OR s.exercice LIKE :searchtext OR s.id LIKE :searchtext OR s.libelle LIKE :searchtext OR s.description LIKE :searchtext) ";
			$params[':searchtext'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$search_text.'%');
		}

		if(!empty($filters)) {
			// FIXME : Should not concatenate sql var without a check
			$sel .= " AND ".$filters;
		}

		if(!empty($linkedObjectsIds)) {
			$sel .= " AND s.id_globalobject IN (".$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['suivis']), 'idglobalobject', $params).")";
		}

		$sel .= " ORDER BY "
			.$order_by
			.$limit;

		$res = array();
		$rs = $this->db->query($sel, $params);

		if ($this->isPageLimited && $pagination) {
			return $this->db->numrows($rs);
		}

		$separation = $this->db->split_resultset($rs);

		foreach ($separation as $elements) {
			$suivi = new suivi();
			$suivi->openFromResultSet($elements['s']);

			// Si c'est un tiers, on vérifie qu'on est dans le bon workspace
			// ou que les suivis du tiers sont partagés
			if ( $suivi->fields['tiers_id'] == 0 || $elements['t']['share_suivi'] == 1 || $elements['t']['tiers_workspace'] == $_SESSION['dims']['workspaceid'] ) {
				if(isset($orders)) {
					foreach($orders as $key => $value) {
						$suivi->orders[array_search($key, self::$cols)] = true;
					}
				}

				$res[] = $suivi;
			}

		}

		return $res;
	}

	public function save() {
		// numéro en fonction du type et de l'exercice
		if ( $this->getId() == null || $this->getId() == '' ) {
			$this->setNextId();
		}

		// calcul du solde
		$detail_commande = $this->get_detail();

		$this->fields['montantht'] = $detail_commande['montant_ht'];
		$this->fields['montanttva'] = $detail_commande['montant_tva'];
		$this->fields['montantttc'] = $detail_commande['montant_ttc'];

		//if ($this->fields['type'] == 'Facture') {
			$select = "
				SELECT	sum(montant) as total_vers
				FROM	dims_mod_business_versement
				WHERE	suivi_id = :idsuivi
				AND	suivi_type = :typesuivi
				AND	suivi_exercice = :exercicesuivi
				AND	id_workspace = :idworkspace";
			$rs = $this->db->query($select, array(
				':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
				':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));

			if ($fields = $this->db->fetchrow($rs)) $this->fields['solde'] =  $this->fields['montantttc'] - $fields['total_vers'];
			else $this->fields['solde'] = $this->fields['montantttc'];

			if ($this->fields['solde'] < 0.01) $this->fields['solde'] = 0;
		//}

		$this->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

		parent::save(dims_const::_SYSTEM_OBJECT_SUIVI);
	}

	public function delete() {
		$this->db->query('DELETE FROM dims_mod_business_suivi_detail
				WHERE suivi_id = :idsuivi
				AND suivi_type = :typesuivi
				AND suivi_exercice = :exercicesuivi
				AND id_workspace = :idworkspace', array(
			':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
			':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		));
		parent::delete();
	}

	public function getIdSuivi() {
		return $this->fields['id_suivi'];
	}

	public function getId() {
		return $this->fields['id'];
	}

	public function setId($id) {
		$this->fields['id'] = $id;
	}

	public function setNextId() {
		// recherche du premier numéro libre
		$precedent = 0;
		$rs = $this->db->query('SELECT id
					FROM '.self::TABLE_NAME.'
					WHERE type = :typesuivi
					AND exercice = :exercicesuivi
					AND id_workspace = :idworkspace
					ORDER BY id ASC', array(
			':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
			':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		));
		while ($row = $this->db->fetchrow($rs)) {
			if ($row['id'] > $precedent + 1) {
				$this->setId($precedent + 1);
				break;
			}
			else {
				$precedent++;
			}
		}
		//  si pas de trou, on met a la suite
		$this->setId($precedent + 1);
	}

	public function getType() {
		require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
		$s = suivi_type::find_by(array('id'=>$this->get('id_type')),null,1);
		if(!empty($s)){
			return $s->get('label');
		}else{
			return $this->fields['type'];
		}
	}

	public function getExercice() {
		return (!empty($this->fields['exercice']) ? $this->fields['exercice'] : "");
	}

	public function setExercice($exercice) {
		$this->fields['exercice'] = $exercice;
	}

	public function getNumero() {
		$t = $this->getType();
		return sprintf("%s%s%06d", $t[0], $this->fields['exercice'], $this->fields['id']);
	}

	public function getDateJour($format = '') {
		if ($format == '') {
			$format = self::DATE_FORMAT_DISPLAY_FR;
		}

		if (preg_match("#^([0-9]{4})-([0-9]{2})-([0-9]{2})$#", $this->fields['datejour'], $matches)) {
			return date($format, mktime(0, 0, 0, $matches[2], $matches[3], $matches[1]));
		}
		else {
			return '';
		}

	}

	public function getLibelle() {
		return $this->fields['libelle'];
	}

	public function getMontantHT() {
		return $this->fields['montantht'];
	}

	public function getMontantTTC() {
		return $this->fields['montantttc'];
	}

	public function getSoldeTTC() {
		return $this->fields['solde'];
	}

	public function getRemise() {
		return $this->fields['remise'];
	}

	public function getPeriode() {
		return $this->fields['periode'];
	}

	public function getDescription() {
		return $this->fields['description'];
	}

	public function getGlobalObjectId() {
		return $this->fields['id_globalobject'];
	}

	public function getValide() {
		return $this->fields['valide'];
	}

	public function getDateValide($format = '') {
		if ($format == '') {
			$format = self::DATE_FORMAT_DISPLAY_FR;
		}

		if (preg_match("#^([0-9]{4})-([0-9]{2})-([0-9]{2})$#", $this->fields['datevalide'], $matches)) {
			return date($format, mktime(0, 0, 0, $matches[2], $matches[3], $matches[1]));
		}
		else {
			return '';
		}
	}

	public function get_detail() {
		$db = dims::getInstance()->getDb();
		$params = array();

		$select = "
			SELECT	*
			FROM	dims_mod_business_suivi_detail
			WHERE	suivi_id = :idsuivi ";

		if(!empty($this->fields['type'])) {
			$select .= " AND suivi_type = :typesuivi ";
			$params[':typesuivi'] = array('type' => PDO::PARAM_STR, 'value' => $this->fields['type']);
		}

		if(!empty($this->fields['exercice'])) {
			$select .= " AND suivi_exercice = :exercicesuivi ";
			$params[':exercicesuivi'] = array('type' => PDO::PARAM_STR, 'value' => $this->fields['exercice']);
		}

		if(!empty($_SESSION['dims']['workspaceid'])) {
			$select .= " AND id_workspace = :idworkspace ";
			$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		}

		$select .= " ORDER BY position ";
		$params[':idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$res = $db->query($select, $params);

		$detail_commande = array(
			'remise_ht' 				=> 0,
			'remise_tva'				=> 0,
			'remise_ttc'				=> 0,
			'montant_ht'				=> 0,
			'montant_ht_sans_remise'	=> 0,
			'montant_ttc'				=> 0,
			'montant_tva'				=> 0,
			'taux'						=> array(),
		);

		while ($fields = $db->fetchrow($res)) {
			$detail_commande['taux'][$fields['tauxtva']]['articles'][] = $fields;
		}

		foreach ($detail_commande['taux'] as $tauxtva => $detail) {
			$detail_commande['taux'][$tauxtva]['total_ht'] = $detail_commande['taux'][$tauxtva]['total_tva'] = $detail_commande['taux'][$tauxtva]['total_ttc'] = 0;

			foreach ($detail['articles'] as $article) {
				$montant_ht = round($article['pu']*$article['qte'], 2);
				$montant_tva = round(($montant_ht*$tauxtva)/100, 2);
				$detail_commande['taux'][$tauxtva]['total_ht'] += $montant_ht;
				$detail_commande['taux'][$tauxtva]['total_tva'] += $montant_tva;
				$detail_commande['taux'][$tauxtva]['total_ttc'] += ($montant_ht+$montant_tva);
			}

			if ($this->fields['remise'] > 0) {
				$detail_commande['taux'][$tauxtva]['remise_ht'] = round(($detail_commande['taux'][$tauxtva]['total_ht'] * $this->fields['remise'])/100, 2);
				$detail_commande['taux'][$tauxtva]['remise_tva'] = round(($detail_commande['taux'][$tauxtva]['total_tva'] * $this->fields['remise'])/100, 2);
				$detail_commande['taux'][$tauxtva]['remise_ttc'] = round(($detail_commande['taux'][$tauxtva]['total_ttc'] * $this->fields['remise'])/100, 2);
			}
			else {
				$detail_commande['taux'][$tauxtva]['remise_ht'] = 0;
				$detail_commande['taux'][$tauxtva]['remise_tva'] = 0;
				$detail_commande['taux'][$tauxtva]['remise_ttc'] = 0;
			}

			$detail_commande['remise_ht'] += $detail_commande['taux'][$tauxtva]['remise_ht'];
			$detail_commande['remise_tva'] += $detail_commande['taux'][$tauxtva]['remise_tva'];
			$detail_commande['remise_ttc'] += $detail_commande['taux'][$tauxtva]['remise_ttc'];

			$detail_commande['montant_ht_sans_remise'] += $detail_commande['taux'][$tauxtva]['total_ht'];
			$detail_commande['montant_ht'] += $detail_commande['taux'][$tauxtva]['total_ht'] - $detail_commande['taux'][$tauxtva]['remise_ht'];
			$detail_commande['montant_tva'] += $detail_commande['taux'][$tauxtva]['total_tva'] - $detail_commande['taux'][$tauxtva]['remise_tva'];
			$detail_commande['montant_ttc'] += $detail_commande['taux'][$tauxtva]['total_ttc'] - $detail_commande['taux'][$tauxtva]['remise_ttc'];
		}

		return($detail_commande);
	}

	public function getTiers() {
		if (is_null($this->tiers)) {
			if($this->fields['tiers_id'] > 0){
				$this->tiers = new tiers();
				$this->tiers->open($this->fields['tiers_id']);
			}elseif($this->fields['contact_id'] > 0){
				$this->tiers = new contact();
				$this->tiers->open($this->fields['contact_id']);
			}
		}
		return $this->tiers;
	}

	public function dupliquer($type = '') {
		require_once DIMS_APP_PATH.'modules/system/suivi/class_versement.php';
		// chargement des paramètres
		$res=$this->db->query("SELECT * FROM dims_mod_business_params WHERE id_workspace = :idworkspace ", array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		));
		while( $fields = $this->db->fetchrow($res) ) $params[$fields['param']] = $fields['value'];

		// on génère le clone
		$clone = new suivi();
		$clone->fields = $this->fields;
		$clone->fields['id_suivi'] = '';
		$clone->fields['id'] = '';
		$clone->fields['datejour'] = date(self::DATE_FORMAT_DISPLAY_US);
		$clone->fields['exercice'] = $params['exercice'];
		if ($type != '') $clone->fields['type'] = $type;
		$clone->save();

		// on récupère les détails
		$select = "
			SELECT	*
			FROM	dims_mod_business_suivi_detail
			WHERE	suivi_id = :idsuivi
			AND	suivi_type = :typesuivi
			AND	suivi_exercice = :exercicesuivie
			AND	id_workspace = :idworkspace
			ORDER BY position";
		$rs = $this->db->query($select, array(
			':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
			':exercicesuivie' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
			':idworkspace' => array('type' => PDO::PARAM_STR, 'value' => $_SESSION['dims']['workspaceid']),
		));
		while ($fields = $this->db->fetchrow($rs)) {
			$clone_detail = new suividetail();
			$clone_detail->fields = $fields;
			$clone_detail->fields['id'] = '';
			$clone_detail->fields['suivi_id'] = $clone->fields['id'];
			$clone_detail->fields['suivi_type'] = $clone->fields['type'];
			$clone_detail->fields['id_type'] = $clone->get('id_type');
			$clone_detail->fields['suivi_exercice'] = $clone->fields['exercice'];
			$clone_detail->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$clone_detail->save();
		}

		// on doit reprendre les versements
		$select = "
				SELECT	montant
				FROM	dims_mod_business_versement
				WHERE	suivi_id = :idsuivi
				AND	suivi_type = :typesuivi
				AND	suivi_exercice = :exercicesuivie
				AND	id_workspace = :idworkspace";
		$rs = $this->db->query($select, array(
			':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
			':exercicesuivie' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
			':idworkspace' => array('type' => PDO::PARAM_STR, 'value' => $_SESSION['dims']['workspaceid']),
		));

		while ($fields = $this->db->fetchrow($rs)) {
			$versement = new versement();
			$versement->fields['date_paiement'] = dims_createtimestamp();
			$versement->fields['montant'] = $fields['montant'];
			$versement->fields['suivi_id'] = $clone->fields['id'];
			$versement->fields['suivi_type'] = $clone->fields['type'];
			$versement->fields['id_type'] = $clone->fields['id_type'];
			$versement->fields['suivi_exercice'] = $clone->fields['exercice'];
			$versement->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$versement->save();
		}

		$clone->save();
		return($clone);
	}

	public function getCaseAttach() {
		return $this->searchGbLink(_SYSTEM_OBJECT_CASE);
	}

	public function getObjectCaseAttach() {
		$lst = $this->getCaseAttach();
		if(count($lst) > 0){
//			require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
			$case = new dims_case();
			return $case->openWithGB(current($lst));
		}
		return false;
	}

	public function display($template) {
		if (file_exists($template)) {
			include $template;
		}
		else {
			echo 'Unable to find '.$template;
		}
	}

	public function updateLinesId($oldId, $oldType, $oldExercice) {
		$this->db->query("
			UPDATE dims_mod_business_suivi_detail SET
				suivi_id = :idsuivito,
				suivi_type = :typesuivito,
				suivi_exercice = :exercicesuivieto
			WHERE suivi_id = :idsuivifrom
			AND suivi_type = :typesuivifrom
			AND suivi_exercice = :exercicesuiviefrom", array(
			':idsuivito' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':typesuivito' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
			':exercicesuivieto' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
			':idsuivifrom' => array('type' => PDO::PARAM_INT, 'value' => $oldId),
			':typesuivifrom' => array('type' => PDO::PARAM_STR, 'value' => $oldType),
			':exercicesuiviefrom' => array('type' => PDO::PARAM_STR, 'value' => $oldExercice),
		));
	}

	public function updateVersementsId($oldId, $oldType, $oldExercice) {
		$this->db->query("
			UPDATE dims_mod_business_versement SET
				suivi_id = :idsuivito,
				suivi_type = :typesuivito,
				suivi_exercice = :exercicesuivieto
			WHERE suivi_id = :idsuivifrom
			AND suivi_type = :typesuivifrom
			AND suivi_exercice = :exercicesuiviefrom", array(
			':idsuivito' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':typesuivito' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
			':exercicesuivieto' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
			':idsuivifrom' => array('type' => PDO::PARAM_INT, 'value' => $oldId),
			':typesuivifrom' => array('type' => PDO::PARAM_STR, 'value' => $oldType),
			':exercicesuiviefrom' => array('type' => PDO::PARAM_STR, 'value' => $oldExercice),
		));
	}

	public function generePdf($suivi_modele,$format='PDF'){
		if (!$this->isNew()) {
			require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/class_gescom_param.php';
			require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/class_xmlmodel.php';
			require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/xmlparser_content.php';

			// chargement des params
			$params = class_gescom_param::getAllParams();

			$tiers = new tiers();
			$contact = new contact();
			if ($this->fields['tiers_id']>0) {
				$tiers->open($this->fields['tiers_id']);
				$intitule=isset($tiers->fields['intitule']) ? $tiers->fields['intitule'] : '';
				$adresse=isset($tiers->fields['adresse']) ? $tiers->fields['adresse'] : '';
				$codepostal=isset($tiers->fields['codepostal']) ? $tiers->fields['codepostal'] : '';
				$ville=isset($tiers->fields['ville']) ? $tiers->fields['ville'] : '';
				$pays=isset($tiers->fields['pays']) ? $tiers->fields['pays'] : '';
				$telclient=isset($tiers->fields['telephone']) ? $tiers->fields['telephone'] : '';
				$mobileclient = isset($contact->fields['telmobile']) ? $contact->fields['telmobile'] : '';
			}
			elseif ($this->fields['contact_id']>0) {
				$contact->open($this->fields['contact_id']);
				$intitule=$contact->fields['lastname']." ".$contact->fields['firstname'];
				$adresse=isset($contact->fields['address']) ? $contact->fields['address'] : '';
				$codepostal=isset($contact->fields['postalcode']) ? $contact->fields['postalcode'] : '';
				$ville=isset($contact->fields['city']) ? $contact->fields['city'] : '';
				$pays=isset($contact->fields['country']) ? $contact->fields['country'] : '';
				$telclient=isset($contact->fields['phone']) ? $contact->fields['phone'] : '';
				$mobileclient=isset($contact->fields['mobile']) ? $contact->fields['mobile'] : '';
			}

			if (empty($format)) $format = 'ODT';

			$model_filename = 'suivi_graphique.odt';
			$folder_src = DIMS_APP_PATH . '/modules/system/desktopV2/templates/suivis/documents/';
			if ( ! empty($suivi_modele)){
				require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
				require_once DIMS_APP_PATH."modules/system/suivi/class_print_model.php";
				$model = new print_model();
				$model->open($suivi_modele);
				if( ! $model->isNew() ){
					$doc = new docfile();
					$doc->open($model->getDocId());
					if( ! $doc->isNew() ){
						$model_filename = "{$doc->fields['id']}_{$doc->fields['version']}.{$doc->fields['extension']}";
						$folder_src = $doc->getbasepath();
					}
				}
			}

			$modele_content = DIMS_APP_PATH . "/modules/system/desktopV2/templates/suivis/documents/tmp/content.xml" ;
			$modele_styles = DIMS_APP_PATH . "/modules/system/desktopV2/templates/suivis/documents/tmp/styles.xml" ;
			dims_deletedir(realpath('.')."/modules/system/desktopV2/templates/suivis/documents/tmp/");
			dims_makedir(realpath('.')."/modules/system/desktopV2/templates/suivis/documents/tmp/");
			$tmp_path = DIMS_APP_PATH . "/modules/system/desktopV2/templates/suivis/documents/tmp/" ;

			$output_path = DIMS_APP_PATH . "/modules/system/desktopV2/templates/suivis/documents/" ;

			if ($format != 'ODT') {
				switch($format) {
					case 'PDF':
						$output_file = $this->getType().'_'.$this->getExercice().'_'.$this->getId().'.pdf';
						break;
					case 'DOC':
						$output_file = $this->getType().'_'.$this->getExercice().'_'.$this->getId().'.doc';
						break;
					case 'SXW':
						$output_file = $this->getType().'_'.$suivi->getExercice().'_'.$suivi->getId().'.sxw';
						break;
					case 'RTF':
						$output_file = $this->getType().'_'.$this->getExercice().'_'.$this->getId().'.rtf';
						break;
					case 'XML':
						$output_file = "{$suivi_type}_{$suivi_exercice}_{$suivi_id}.xml" ;
						$output_odt = $this->getType().'_'.$this->getExercice().'_'.$this->getId().'.odt';
						break;
				}
			}

			$output_odt = $this->getType().'_'.$this->getExercice().'_'.$this->getId().'.odt';
			//die($model_filename . ' --> in '.$folder_src);
			dims_unzip($model_filename, $folder_src, DIMS_APP_PATH . '/modules/system/desktopV2/templates/suivis/documents/tmp/') ;

			$xml_content = '';
			$xml_styles = '';

			if ($f = fopen( $modele_content, "r" )) {
				while (!feof($f)) $xml_content .= fgets($f, 4096);
				fclose($f);
			}
			else die("erreur avec le fichier $modele_content");

			if ($f = fopen( $modele_styles, "r" )) {
				while (!feof($f)) $xml_styles .= fgets($f, 4096);
				fclose($f);
			}
			else die("erreur avec le fichier $modele_styles");


			global $xmlmodel;
			global $output;
			global $modeleligne;
			$output = '';

			// construction des tags à remplacer
			$xmlmodel = new xmlmodel('');
			$xmlmodel->addtag('(NOMSUIVI)', $this->getLibelle());
			$xmlmodel->addtag('(NUMEROSUIVI)', $this->getNumero());
			$xmlmodel->addtag('(TYPESUIVI)', $this->getType());
			$xmlmodel->addtag('(DATESUIVI)', $this->getDateJour());
			$xmlmodel->addtag('(ADRESSE1)', $intitule);
			$xmlmodel->addtag('(ADRESSE2)', $adresse);
			$xmlmodel->addtag('(ADRESSE3)', '');
			$xmlmodel->addtag('(CODEPOSTAL)', $codepostal);
			$xmlmodel->addtag('(VILLE)', $ville);
			$xmlmodel->addtag('(TELEPHONE)', $telclient);
			$xmlmodel->addtag('(MOBILE)', $mobileclient);
			if (isset($params['pays']) && $pays != $params['pays']) $xmlmodel->addtag('(PAYS)', $pays);
			else $xmlmodel->addtag('(PAYS)', '');

			$detail_commande = $this->get_detail();

			$c = 0;
			foreach($detail_commande['taux'] as $taux => $detail) {
				$c++;
				$xmlmodel->addtag("(TAUX{$c})", number_format(round($taux, 2), 2, ',', ' ').' %');
				$xmlmodel->addtag("(TAUX{$c}_MONTANTHT)", number_format(round($detail['total_ht'] - $detail['remise_ht'], 2), 2, ',', ' '));
				$xmlmodel->addtag("(TAUX{$c}_MONTANTTVA)", number_format(round($detail['total_tva'] - $detail['remise_tva'], 2), 2, ',', ' '));
			}

			for ($c=$c+1;$c<=5;$c++) {
				$xmlmodel->addtag("(TAUX{$c})", '');
				$xmlmodel->addtag("(TAUX{$c}_MONTANTHT)", '');
				$xmlmodel->addtag("(TAUX{$c}_MONTANTTVA)", '');
			}

			$xmlmodel->addtag('(REMISEP100)', number_format(round($this->fields['remise'], 2), 2, ',', ' '));
			$xmlmodel->addtag('(REMISEHT)', number_format(round($detail_commande['remise_ht'], 2), 2, ',', ' '));
			$xmlmodel->addtag('(MONTANTHT_SANS_R)', number_format(round($detail_commande['montant_ht_sans_remise'], 2), 2, ',', ' '));
			$xmlmodel->addtag('(MONTANTHT)', number_format(round($detail_commande['montant_ht'], 2), 2, ',', ' '));
			$xmlmodel->addtag('(MONTANTTVA)', number_format(round($detail_commande['montant_tva'], 2), 2, ',', ' '));
			$xmlmodel->addtag('(MONTANTTTC)', number_format(round($detail_commande['montant_ttc'], 2), 2, ',', ' '));

			// on calcule les montants 5, 10, 15, etc
			for ($jj = 5; $jj <= 95; $jj += 5) {
				$xmlmodel->addtag('(MONTANT_'.$jj.'HT)', number_format(round($detail_commande['montant_ht'] * $jj / 100, 2), 2, ',', ' '));
				$xmlmodel->addtag('(MONTANT_'.$jj.'TTC)', number_format(round($detail_commande['montant_ttc'] * $jj / 100, 2), 2, ',', ' '));
			}

			$xmlmodel->addtag('(COMMENTAIRE)', $this->fields['description']);
			if(isset($params['conditionpaiement'])) {
				$xmlmodel->addtag('(CONDITIONPAIEMENT)', $params['conditionpaiement']);
			}

			$select = "
				SELECT	*
				FROM	dims_mod_business_versement
				WHERE	suivi_id = :idsuivi
				AND	suivi_type = :typesuivi
				AND	suivi_exercice = :exercicesuivi
				AND	id_workspace = :idworkspace
				ORDER BY date_paiement";

			$db = dims::getInstance()->getDb();
			$res=$db->query($select, array(
				':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['type']),
				':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['exercice']),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));

			$lignes = '';
			$xml_debug = 1;
			$montant_verse=0;
			$restant_du=0;
			$ivers=1;
			$texte_versement='';
			$montant_versement='';
			$format_date = 'd/m/Y';

			if ($db->numrows($res)>0) {
				while ($fields = $db->fetchrow($res)) {
				if ($ivers>1) {
					$texte_versement.="\r";
					$montant_versement.="\r";
				}

				$date_fr = dims_timestamp2local($fields['date_paiement']);
				$texteverse='';
				switch ($ivers) {
					case 1:
					$texteverse="1 er acompte";
					break;
					default:
					$texteverse=$ivers." ème acompte";
					break;
				}
				$texte_versement.=$texteverse." versé le ".$date_fr['date'];
				$montant_versement.=number_format(round($fields['montant'], 2), 2, ',', ' ')." €";
				$montant_verse+=$fields['montant'];
				$ivers++;
				}
			}
			else {
				$texte_versement="Acompte déjà versé";
				$restant_du=0;
				$montant_versement=number_format(round($restant_du, 2), 2, ',', ' ');
			}

			$restant_du=$detail_commande['montant_ttc']-$montant_verse;

			$xmlmodel->addtag('(TEXTE_VERSEMENT)', $texte_versement);
			$xmlmodel->addtag('(MONTANT_VERSEMENT)', $montant_versement);
			//$xmlmodel->addtag('(MONTANT_VERSEMENT)', number_format(round($montant_verse, 2), 2, ',', ' '));
			$xmlmodel->addtag('(RESTANT_DU)', number_format(round($restant_du, 2), 2, ',', ' '));

			$xml_parser = xmlparser_content();
			if (!xml_parse($xml_parser, $xml_content, TRUE)) {
				printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
			}

			$content = '<?xml version="1.0" encoding="UTF-8"?>'.$output;

			$xml_modeleligne = $modeleligne;

			$xml_modeleligne_versement = $modeleligneversement;

			$output = '';
			$etape = '';
			$modeleligne = '';

			$xml_parser = xmlparser_content();
			if (!xml_parse($xml_parser, $xml_styles, TRUE)) {
				printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
			}

			$styles = '<?xml version="1.0" encoding="UTF-8"?>'.$output;

			$select = "
				SELECT	*
				FROM	dims_mod_business_suivi_detail
				WHERE	suivi_id = :idsuivi
				AND		suivi_type = :typesuivi
				AND		suivi_exercice = :exercicesuivi
				AND	id_workspace = :idworkspace
				ORDER BY position";

			$db->query($select, array(
				':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['type']),
				':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['exercice']),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));

			$lignes = '';
			$xml_debug = 1;

			while ($fields = $db->fetchrow()) {
				$xmlmodel = new xmlmodel('');
				$xmlmodel->addtag('(CODE)', $fields['code']);
				$xmlmodel->addtag('(LIBELLE)', $fields['libelle']);
				$xmlmodel->addtag('(DESCRIPTION)', $fields['description']);
				$xmlmodel->addtag('(QTE)', $fields['qte']);
				$xmlmodel->addtag('(TVA)', number_format(round($fields['tauxtva'], 2), 2, ',', ' '));
				$xmlmodel->addtag('(PU)', number_format(round($fields['pu'], 2), 2, ',', ' '));
				$xmlmodel->addtag('(MONTANT)', number_format(round($fields['pu'] * $fields['qte'], 2), 2, ',', ' '));

				$output = '';
				$etape = '';
				$modeleligne = '';
				$ligne = '';
				$params = '';


				$xml_parser = xmlparser_content();
				if (!xml_parse($xml_parser, $xml_modeleligne, TRUE)) {
					printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
				}

				$lignes .= $output;
			}

			$content = str_replace($xml_modeleligne,$lignes,$content);

			// Assurons nous que le fichier est accessible en écriture
			if (is_writable($output_path)) {
				if (!$handle = fopen($modele_styles, 'w')) {
					 echo "Impossible d'ouvrir le fichier $modele_styles";
					 exit;
				}

				if (fwrite($handle, $styles) === FALSE) {
					echo "Impossible d'écrire dans le fichier $modele_styles";
					exit;
				}

				if (!$handle = fopen($modele_content, 'w')) {
					 echo "Impossible d'ouvrir le fichier $modele_content";
					 exit;
				}

				if (fwrite($handle, $content) === FALSE) {
					echo "Impossible d'écrire dans le fichier $modele_content";
					exit;
				}

				fclose($handle);

				$res = array();
				$cwd = getcwd();
				chdir($tmp_path);
				shell_exec(escapeshellcmd("zip -r ".escapeshellarg("../$output_odt")." . -i *"));
				shell_exec(escapeshellcmd("rm -rf *"));
				chdir($cwd);

				if ($format != 'ODT') {
					$converter_path = realpath(DIMS_APP_PATH . '/lib/jooconverter/').'/jooconverter-2.0rc2.jar';
					$cmd = "`which java` -jar ".escapeshellarg($converter_path)." ".escapeshellarg(realpath("{$output_path}/{$output_odt}")).' '.escapeshellarg(realpath("{$output_path}")."/{$output_file}");

					// $cmd_error = shell_exec(escapeshellcmd($cmd));
					$cmd_error = shell_exec(escapeshellcmd($cmd));
					if (!$cmd_error) unlink(realpath($output_path.$output_odt));
					dims_downloadfile(realpath($output_path.$output_file),$output_file, true, true);
				}
				else {
					dims_downloadfile(realpath($output_path.$output_odt), $output_odt, true, true);
				}
			}
			else {
				echo "Le dossier $output_path n'est pas accessible en écriture.";
			}
		}
	}
}
