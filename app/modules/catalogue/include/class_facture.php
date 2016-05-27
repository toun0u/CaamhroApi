<?php
include_once DIMS_APP_PATH . 'modules/catalogue/include/class_facture_detail.php';
include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';

include_once DIMS_APP_PATH . 'modules/catalogue/include/class_facture_tva.php';

include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';
include_once DIMS_APP_PATH . 'modules/catalogue/include/class_moyen_paiement.php';
include_once DIMS_APP_PATH . 'modules/catalogue/include/class_config.php';


require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/class_gescom_param.php';
// require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/class_xmlmodel.php';
// require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/xmlparser_content.php';

require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
require_once DIMS_APP_PATH."modules/system/suivi/class_print_model.php";

class cata_facture extends pagination {
	const TABLE_NAME = 'dims_mod_cata_facture';

	const TYPE_FACTURE          = 1;
	const TYPE_QUOTATION        = 2;
	const TYPE_PURCHASEORDER    = 3;
	const TYPE_DELIVERYORDER    = 4;
	const TYPE_ASSET            = 5;

	const STATE_WAITING     = 0;
	const STATE_VALIDATED   = 1;
	const STATE_REFUSED     = 2;

	// Responsable du dossier
	private $responsible = null;

	private $client = null;

	private $activePag = true;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function __clone() {
		$this->fields['libelle']    = self::generatelabel($this->fields['type']);
		$this->fields['date_cree']  = dims_local2timestamp(date('d/m/Y'));

		$newlines = array();
		foreach($this->getlines() as $line) {
			$newlines[] = clone $line;
		}

		parent::__clone();

		$this->save();

		foreach($newlines as $newline) {
			$newline->fields['id_facture'] = $this->getId();
			$newline->save();
		}
	}

	public function delete($forcedelete = false) {
		$returnvalue = true;

		foreach($this->getlines() as $line) {
			$line->delete($forcedelete);
		}

		if($forcedelete) {
			$returnvalue = parent::delete();
		} else {
			$this->fields['deleted'] = 1;

			$this->save();
		}

		return $returnvalue;
	}

	// public function getArticles() {
	// 	if ($this->fields['id'] == '') {
	// 		return (array());
	// 	}
	// 	else {
	// 		$articles = array();
	// 		$rs = $this->db->query('SELECT * FROM dims_mod_vpc_facture_detail WHERE id_facture = '.$this->fields['id']);
	// 		while ($row = $this->db->fetchrow($rs)) {
	// 			$articles[$row['ref_article']] = $row;
	// 		}
	// 		return($articles);
	// 	}
	// }

	public function getclient() {
		if($this->client == null && !empty($this->fields['id_client'])) {
			$this->client = new client();
			$this->client->open($this->get('id_client'));
		}

		return $this->client;
	}

	public function setclient(client $client) {
		if($client != null) {
			$this->client = $client;
			$this->set('id_client', $client->getId());
		}
	}

	public function getCodeRgt() {
		return $this->fields['codrgt'];
	}

	public function getDateEch() {
		return $this->fields['datech'];
	}

	public function getlines() {
		return cata_facture_detail::find_by(array('id_facture' => $this->getId(), 'deleted' => 0), ' ORDER BY position ASC ');
	}

	public function getVATLines() {
		return cata_facture_tva::find_by(array('id_facture' => $this->getId()));
	}

	public function computetotals() {
		$this->fields['total_ht']   = 0;
		$this->fields['total_tva']  = 0;
		$this->fields['total_ttc']  = 0;

		foreach($this->getlines() as $line) {
			$this->fields['total_ht']   += $line->gettotalht();
			$this->fields['total_tva']  += ($line->gettotalht()) * ($line->fields['tx_tva'] / 100);
		}

		$this->fields['total_ttc'] = ($this->fields['total_ht'] + $this->fields['total_tva']) * (1 - (floatval($this->fields['discount']) / 100));
		$this->save();
	}

	public function validate() {
		if(!$this->isNew()) {
			$this->fields['state'] = self::STATE_VALIDATED;
			$this->save();
		}
	}

	public function refuse() {
		if(!$this->isNew()) {
			$this->fields['state'] = self::STATE_REFUSED;
			$this->save();
		}
	}

	public function printout($modele, $format = 'PDF') {
		require_once DIMS_APP_PATH.'include/class_opendocument.php';

		$od = new dims_opendocument($modele);

		$od->setFormat($format);

		$data = array();
		$tables = array();

		// En-tête du document
		$data['(NUM_DOCUMENT)']				= $this->getDocumentNumber();
		$data['(DATE_DOCUMENT)']			= $this->getDocumentDate();
		$data['(GABARIT_DOCUMENT)']			= $this->getDocumentGauge();

		$data['(NOM_FACTURATION)'] 			= $this->getBillingName();
		$data['(CODE_CLIENT)'] 				= $this->getClientCode();
		$data['(NOM_RESPONSABLE)'] 			= $this->getResponsible()->getLastname();
		$data['(TEL_RESPONSABLE)'] 			= $this->getResponsible()->fields['phone'];
		$data['(FAX_RESPONSABLE)'] 			= $this->getResponsible()->fields['fax'];

		$data['(CONDITIONS_PORT)'] 			= $this->getShippingConditions();
		// $data['(MODE_REGLEMENT)'] 			= $this->getPaymentMethod();
		$data['(CONDITIONS_PAIEMENT)'] 		= $this->getPaymentConditions();

		$data['(ADRESSE_FACTURATION)'] 		= $this->getRawBillingAddress();

		$data['(TOTAL_HT)']					= catalogue_formateprix($this->getTotalHT());
		$data['(TOTAL_TVA)']				= catalogue_formateprix($this->getTotalTVA());
		$data['(TOTAL_TTC)']				= catalogue_formateprix($this->getTotalTTC());

		// Lignes du document
		foreach ($this->getlines() as $line) {
			$detail = array();
			$detail['(POS)'] 					= $line->get('position');
			$detail['(REF)'] 					= $line->get('ref');
			$detail['(DESIGNATION)'] 			= htmlspecialchars($line->get('label'));
			$detail['(QTE)'] 					= $line->get('qte');
			$detail['(UNITE)'] 					= $line->get('unit_of_measure');
			$detail['(PU_BRUT)'] 				= catalogue_formateprix($line->get('pu_ht')).' EUR';
			$detail['(PU_NET)'] 				= catalogue_formateprix($line->get('pu_remise')).' EUR';
			$detail['(TOTAL_LIGNE_HT)'] 		= catalogue_formateprix($line->get('pu_remise') * $line->get('qte')).' EUR';
			$detail['(TVA)'] 					= $line->get('tx_tva').' %';

			$tables['lines'][] = $detail;
		}

		// Lignes des TVA
		foreach ($this->getVATLines() as $vat_line) {
			$tva = array();
			$tva['(TVA_DESCRIPTION)'] 			= $vat_line->getVATDescription();
			$tva['(TX_TVA)'] 					= $vat_line->get('tx_tva');
			$tva['(MT_HT)'] 					= catalogue_formateprix($vat_line->get('total_ht'));
			$tva['(MT_TVA)'] 					= catalogue_formateprix($vat_line->get('total_tva'));
			$tva['(MT_TTC)'] 					= catalogue_formateprix($vat_line->get('total_ttc'));

			$tables['tva_lines'][] = $tva;
		}

		// on save les datas
		$od->setData($data);
		$od->setTables($tables);

		// on s'occupe des images maintenant
		$images = array();

		$od->createOpenDocument($this->getDocumentNumber().'.pdf', DIMS_APP_PATH . 'modules/catalogue/templates/factures/', $images, true);
	}

	public function build_index($status = -1, $client = 0, $date_deb = 0, $date_fin = 0, $payment = 'all', $keywords = '', $sort_by = 'date_validation', $sort_way='ASC', $pagination=false){
		$db = dims::getInstance()->getDb();

		if ($this->activePag && !$pagination) {
			$this->total_index = $this->build_index($status, $client, $date_deb, $date_fin, $payment, $keywords, $sort_by, $sort_way, true);
			pagination::liste_page($this->total_index);
			$limit = ' LIMIT '.$this->sql_debut.', '.$this->limite_key;
		} else {
			$limit = '';
		}
		$where = 'WHERE f.id_module = '.$_SESSION['dims']['moduleid'].' ';

		$lstStatus = self::getstateslist();
		if($status >= 0 && isset($lstStatus[$status])) {
			$where .= " AND f.state = '$status' ";
		}

		if ($client > 0 && $client != '') {
			$where .= " AND f.id_client = $client ";
		}

		if($date_deb > 0) {
			$dds = explode('/',$date_deb);
			if (count($dds) == 3) {
				$dd = $dds[2].$dds[1].$dds[0]."000000";
			} else {
				$dd =$date_deb;
			}
			$where .= " AND f.date_cree >= $dd ";
		}

		if($date_fin > 0){
			$dfs = explode('/',$date_fin);
			if (count($dfs) == 3){
				$df = $dfs[2].$dfs[1].$dfs[0]."000000";
			}else
				$df =$date_deb;
			$where .= " AND f.date_cree <= $df ";
		}

		if(in_array($payment, moyen_paiement::getAllType())) {
			$where .= " AND f.mode_paiement = $payment ";
		}

		if( ! empty($keywords)){
			#Récupération des ids d'article correspondant aux keywords
			$go_ids = article::simple_search($keywords);
			if(!empty($go_ids)){
				$where .= " AND f.id_globalobject IN (".implode(',', $go_ids).")";
			}
		}

		$inner_plus = "";
		$order_by = 'f.date_cree';
		switch ($sort_by) {
			case 'client':
				$order_by = "cl.nom";
				break;
			case 'numcde':
				$order_by = "f.$sort_by";
				break;
			case 'date_cree':
			default:
				break;
		}

		if(!($sort_way == 'ASC' || $sort_way == 'DESC')) {
			$sort_way = 'ASC';
		}

		$sql = 'SELECT      f.*, cl.*
				FROM        '.self::TABLE_NAME.' f
				INNER JOIN  '.client::TABLE_NAME.' cl
				ON          cl.id_client = f.id_client
				'.$inner_plus.'
				'.$where.'
				ORDER BY    '.$order_by.' '.$sort_way.'
				'.$limit;

		$res = $this->db->query($sql);

		if ($pagination) {
			return $this->db->numrows($res);
		} else {
			$split = $this->db->split_resultset($res);
			$lst = array();
			foreach($split as $tab) {
				$quotation = new self();
				$quotation->openFromResultSet($tab['f']);

				$cli = new client();
				$cli->openFromResultSet($tab['cl']);

				$quotation->setClient($cli);

				$lst[$quotation->get('id')] = $quotation;
			}
			return $lst;
		}
	}

	public static function getByNumFac($numFac) {
		$db = dims::getInstance()->getDb();

		$rs = $db->query(
			'SELECT * FROM `'.self::TABLE_NAME.'` WHERE numfac = :numfac LIMIT 0, 1',
			array(
				':numfac' => array('TYPE' => PDO::PARAM_STR, 'VALUE' => $numFac),
			)
		);
		if ($db->numrows($rs)) {
			$facture = new cata_facture();
			$facture->openFromResultSet($db->fetchrow($rs));
			return $facture;
		}
		else {
			return null;
		}
	}

	public static function generatelabel($type = null) {
		$newlabel = '';

		$config = cata_config::get($_SESSION['dims']['moduleid']);
		$labelpattern = $config->getlabelpattern();

		if(!empty($labelpattern)) {
			$labelpattern = str_replace(
				array(
					'T',
					'Y',
					'X',
				),
				array(
					'%1$s',
					'%2$d',
					'%3$s',
				),
				$labelpattern
			);

			$labelpattern = preg_replace_callback(
				'/(\*+)/',
				function($matches) {
					return '%4$0'.strlen($matches[1]).'d';
				},
				$labelpattern
			);

			$typechar = '';
			switch($type) {
				case self::TYPE_QUOTATION:
					$typechar = 'D';
					break;
				case self::TYPE_FACTURE:
					$typechar = 'F';
					break;
				//case self::TYPE_PURCHASEORDER:
				//	$typechar = 'B';
				//	break;
			}

			$currentyear    = date('Y');
			$fiscalyear     = $config->getfiscalyear();
			$newid          = cata_facture::pick('MAX(id)')->conditions(array('type' => $type))->run(false, 'id', true) + 1;

			$newlabel = sprintf(
				$labelpattern,
				$typechar,      // Type character   : %1$s
				$currentyear,   // current year     : %2$d
				$fiscalyear,    // fiscal year      : %3$s
				$newid          // padded ID        : %4$0Xd - X computed from star number
			);
		}

		return $newlabel;
	}

	public static function getstatepicture($state) {
		$picturepath = '';

		$view = view::getInstance();
		switch($state) {
			case self::STATE_WAITING:
				$picturepath = $view->getTemplateWebPath('gfx/pastille_orange12.png');;
				break;
			case self::STATE_VALIDATED:
				$picturepath = $view->getTemplateWebPath('gfx/pastille_verte12.png');;
				break;
			case self::STATE_REFUSED:
				$picturepath = $view->getTemplateWebPath('gfx/pastille_rouge12.png');;
				break;
		}

		return $picturepath;
	}

	public static function getstatelabel($state) {
		$statelabel = '';

		switch($state) {
			case self::STATE_WAITING:
				$statelabel = dims_constant::getVal('WAITING');
				break;
			case self::STATE_VALIDATED:
				$statelabel = dims_constant::getVal('VALIDATED');
				break;
			case self::STATE_REFUSED:
				$statelabel = dims_constant::getVal('_REFUSED');
				break;
		}

		return $statelabel;
	}

	public static function getstateslist() {
		return array(
			self::STATE_WAITING   => self::getstatelabel(self::STATE_WAITING),
			self::STATE_VALIDATED => self::getstatelabel(self::STATE_VALIDATED),
			self::STATE_REFUSED   => self::getstatelabel(self::STATE_REFUSED),
		);
	}

	public static function gettypelabel($type) {
		$typelabel = '';

		switch($type) {
			case self::TYPE_FACTURE:
				$typelabel = dims_constant::getVal('INVOICE');
				break;
			case self::TYPE_QUOTATION:
				$typelabel = dims_constant::getVal('QUOTATION');
				break;
			case self::TYPE_PURCHASEORDER:
				$typelabel = dims_constant::getVal('PURCHASE_ORDER');
				break;
			case self::TYPE_DELIVERYORDER:
				$typelabel = dims_constant::getVal('_DELIVERY_NOTES');
				break;
			case self::TYPE_ASSET:
				$typelabel = dims_constant::getVal('ASSET');
				break;
		}

		return $typelabel;
	}

	public function getRawBillingAddress() {
		$address = $this->getBillingName();

		if ($this->getBillingAdr1() != '') {
			$address .= "\n".$this->getBillingAdr1();
		}

		if ($this->getBillingAdr2() != '') {
			if ($address != '') $address .= "\n";
			$address .= $this->getBillingAdr2();
		}

		if ($this->getBillingAdr3() != '') {
			if ($address != '') $address .= "\n";
			$address .= $this->getBillingAdr3();
		}

		$address .= "\n".$this->getBillingPostalCode().' '.$this->getBillingCity();

		return $address;
	}

	public function getDocumentNumber() {
		return $this->fields['num_document'];
	}

	public function getDocumentDate() {
		return substr($this->fields['date_cree'], 6, 2)
			.'/'. substr($this->fields['date_cree'], 4, 2)
			.'/'. substr($this->fields['date_cree'], 0, 4);
	}

	public function getDocumentGauge() {
		return $this->fields['gauge_document'];
	}

	public function getBillingName() {
		return $this->fields['cli_nom'];
	}

	public function getBillingAdr1() {
		return $this->fields['cli_adr1'];
	}

	public function getBillingAdr2() {
		return $this->fields['cli_adr2'];
	}

	public function getBillingAdr3() {
		return $this->fields['cli_adr3'];
	}

	public function getBillingPostalCode() {
		return $this->fields['cli_cp'];
	}

	public function getBillingCity() {
		return $this->fields['cli_ville'];
	}

	public function getClientCode() {
		return $this->fields['code_client'];
	}

	public function getResponsible() {
		if (is_null($this->responsible)) {
			$this->responsible = user::find_by(array('id' => $this->fields['id_user']), null, 1);
		}
		return $this->responsible;
	}

	public function getShippingConditions() {
		return $this->fields['shipping_conditions'];
	}

	public function getPaymentMethod() {
		return $this->fields['mode_paiement'];
	}

	public function getPaymentConditions() {
		return $this->fields['payment_conditions'];
	}

	public function getTotalHT() {
		return $this->fields['total_ht'];
	}

	public function getTotalTVA() {
		return $this->fields['total_tva'];
	}

	public function getTotalTTC() {
		return $this->fields['total_ttc'];
	}

}
