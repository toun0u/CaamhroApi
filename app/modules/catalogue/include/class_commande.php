<?php
include_once DIMS_APP_PATH.'modules/catalogue/include/class_commande_ligne.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_commande_ligne_detail.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_commande_ligne_hc.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_client_cplmt.php';

class commande extends pagination {
	const TABLE_NAME = 'dims_mod_cata_cde';
	const MY_GLOBALOBJECT_CODE = 235;
	const _STANDARD = 1;
	const _UNITAIRE = 2;

	// États de commande
	const _STATUS_PROGRESS 					= 1;
	const _STATUS_VALIDATED					= 2;
	const _STATUS_REFUSED 					= 3;
	const _STATUS_WAIT_PAYMENT 				= 4;
	const _STATUS_AWAITING_VALIDATION1 		= 5;
	const _STATUS_AWAITING_VALIDATION2 		= 6;
	const _STATUS_AWAITING_VALIDATION3 		= 7;
	const _STATUS_AWAITING_COSTING          = 8;

	/* États non utilisés actuellement
	const _STATUS_CANCELED 					= 8;
	const _STATUS_PREPARATION 				= 9;
	const _STATUS_AWAITING_SHIPMENT 		= 10;
	*/

	private $user;
	private $client;
	private $articles;
	private $mode;
	private $activePag = true;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id_cde');
		$this->articles = array();
		$this->mode=commande::_STANDARD;
	}

	public function setMode($mode) {
		$this->mode=$mode;
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = 'COMMANDE n° '.$this->fields['code_client'];
	}

	public function getById($id) {
		$rs = $this->db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE id_cde = '.$id.' LIMIT 0, 1');
		if ($this->db->numrows($rs)) {
			$this->openFromResultSet($this->db->fetchrow($rs));
			return $this;
		}
		else {
			return null;
		}
	}

	public function save() {
		if (!empty($this->fields['port'])) {
			$this->fields['port'] = str_replace(',', '.', $this->fields['port']);
		}

		if (!empty($_SESSION['dims']['previous_user'])) {
			$previous_user = user::getUserByLogin($_SESSION['dims']['previous_user']['l']);
		}

		if ($this->new) {
			$this->fields['id_user']	= $_SESSION['dims']['userid'];
			$this->fields['id_workspace']	= $_SESSION['dims']['currentworkspace']['id'];
			if (isset($_SESSION['catalogue']['moduleid'])) {
				$this->fields['id_module'] = $_SESSION['catalogue']['moduleid'];
			}
			else {
				$this->fields['id_module'] = $_SESSION['dims']['moduleid'];
			}

			$this->fields['representative_creator'] = 0;
			if (isset($previous_user)) {
				$this->fields['representative_creator'] = $previous_user->get('id');
			}
		}

		$this->fields['representative_validator'] = 0;
		if (isset($previous_user)) {
			$this->fields['representative_validator'] = $previous_user->get('id');
		}

		if (!isset($this->fields['cli_id_pays'])) {
			$this->fields['cli_id_pays'] = 73;
		}

		parent::save();

		// if(!empty($this->articles)) {
		// 	// chargement des paramètres du catalogue
		// 	$mods = dims::getInstance()->getModuleByType('catalogue');
		// 	$oCatalogue = new catalogue();
		// 	$oCatalogue->open($mods[0]['instanceid']);
		// 	$oCatalogue->loadParams();

		// 	$total_panier_ht=0;
		// 	// commande standard
		// 	if (!$this->fields['hors_cata']) {
		// 		global $a_tva;

		// 		$i = 0;
		// 		$total_panier = 0;
		// 		$a_total_tva = array();

		// 		$q = 'DELETE FROM '.commande_ligne::TABLE_NAME.' WHERE id_cde = '.$this->getId();
		// 		$rs = $this->db->query($q);

		// 		foreach($this->articles as $ref => $qte) {
		// 			$i++;
		// 			$article = new article();
		// 			$article->open($ref);

		// 			// remise sur les commandes web
		// 			$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

		// 			// base de calcul (HT / TTC ?)
		// 			// TTC
		// 			if ($oCatalogue->getParams('cata_base_ttc')) {
		// 				$pu_brut			= catalogue_getprixarticle($article, $qte, true);
		// 				$pu_ttc				= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
		// 				$pu_ht				= $pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100);
		// 				$total_ttc			= $pu_ttc * $qte;
		// 				$total_panier		+= $total_ttc;

		// 				if (!isset($a_total_tva[$article->fields['ctva']])) {
		// 					$a_total_tva[$article->fields['ctva']] = 0;
		// 				}
		// 				$a_total_tva[$article->fields['ctva']] += $total_ttc - ($total_ttc / (1 + $a_tva[$article->fields['ctva']] / 100));

		// 			}
		// 			// HT
		// 			else {
		// 				$pu_brut			= catalogue_getprixarticle($article, $qte, true);
		// 				$pu_ht				= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
		// 				$pu_ttc				= $pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100);
		// 				$total_ht			= $pu_ht * $qte;
		// 				$total_panier		+= $total_ht;

		// 				if (!isset($a_total_tva[$article->fields['ctva']])) {
		// 					$a_total_tva[$article->fields['ctva']] = 0;
		// 				}
		// 				$a_total_tva[$article->fields['ctva']] += $total_ht * $a_tva[$article->fields['ctva']] / 100;
		// 			}

		// 			// qtes
		// 			if ($oCatalogue->getParams('cata_aff_livrable')) {
		// 				$qte_liv = ($article->fields['qte'] >= $qte) ? $qte : $article->fields['qte'];
		// 				$qte_rel = ($article->fields['qte'] >= $qte) ? 0 : $qte - $article->fields['qte'];
		// 			}

		// 			//Enregistrement de la ligne de commande
		// 			$obj_cde_li = new commande_ligne();
		// 			$obj_cde_li->fields['id_cde']			= $this->getId();
		// 			$obj_cde_li->fields['id_article']		= $article->fields['id'];
		// 			$obj_cde_li->fields['ref']				= $article->fields['reference'];
		// 			$obj_cde_li->fields['label']			= $article->fields['label'];
		// 			$obj_cde_li->fields['label_default']	= $article->fields['label'];
		// 			$obj_cde_li->fields['qte']				= $qte;
		// 			$obj_cde_li->fields['pu_ht']			= $pu_ht;
		// 			$obj_cde_li->fields['pu_ttc']			= $pu_ttc;
		// 			$obj_cde_li->fields['tx_tva']			= $a_tva[$article->fields['ctva']];
		// 			$obj_cde_li->fields['ctva']				= $article->fields['ctva'];
		// 			$obj_cde_li->save();

		// 			// on test si on a une generation unitaire à faire
		// 			if ($this->mode==commande::_UNITAIRE) {
		// 				$cld = new commande_ligne_detail();
		// 				$cld->fields['id_cde_ligne']=$obj_cde_li->fields['id_cde_ligne'];
		// 				$cld->fields['status']=0;
		// 				$cld->fields['timestp_create']=dims_createtimestamp();
		// 				$dims=dims::getInstance();
		// 				$dims->getPasswordHash($obj_cde_li->fields['id_cde_ligne']."-".$cld->fields['timestp_create'] ,$hash,$saltligne);
		// 				$cld->fields['hash_code']=$hash;
		// 				$cld->fields['salt']=$saltligne;
		// 				$cld->save();
		// 			}

		// 			$total_panier_ht+=$pu_ht*$qte;


		// 		}
		// 	}
		// 	// commande hors catalogue
		// 	else {
		// 		$total_panier = 0;
		// 		$a_total_tva = array();

		// 		$q = '	DELETE FROM 	'.commande_ligne_hc::TABLE_NAME.'
		// 				WHERE 			id_cde = '.$this->getId();
		// 		$rs = $this->db->query($q);

		// 		foreach($this->articles as $ligne) {
		// 			//Enregistrement de la ligne de commande
		// 			$obj_cde_li = new commande_ligne_hc();
		// 			$obj_cde_li->fields['id_cde']			= $this->getId();
		// 			$obj_cde_li->fields['reference']		= $ligne['reference'];
		// 			$obj_cde_li->fields['designation']		= $ligne['designation'];
		// 			$obj_cde_li->fields['qte']			= $ligne['qte'];
		// 			$obj_cde_li->fields['pu']			= $ligne['pu'];
		// 			$obj_cde_li->save();

		// 			$total_ligne = $ligne['pu'] * $ligne['qte'];
		// 			$total_panier += $total_ligne;

		// 			$a_total_tva['hc'] += $total_ligne - ($total_ligne / (1 + _DEFAULT_TVA));
		// 		}
		// 	}

		// 	// calcul de la remise
		// 	$rem = 0;

		// 	if($_SESSION['catalogue']['remgen']>0 && $_SESSION['catalogue']['remgen'] != ""){
		// 		$obj_cde_li = new commande_ligne();
		// 		$obj_cde_li->fields['id_cde'] = $this->fields['id_cde'];
		// 		$obj_cde_li->fields['id_article'] = 0;
		// 		$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem0'];
		// 		$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem0'];
		// 		$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem0'];
		// 		$obj_cde_li->fields['qte'] = -1;
		// 		$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
		// 		$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['remgen'];
		// 		$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
		// 		$obj_cde_li->fields['ctva'] = 1;
		// 		if($obj_cde_li->fields['remise'] != 100)
		// 		$obj_cde_li->save();
		// 		$rem += $_SESSION['catalogue']['remgen'];
		// 	}

		// 	$total_panier_ht = $total_panier_ht - ($total_panier_ht*($_SESSION['catalogue']['remgen']/100));

		// 	if(isset($_SESSION['catalogue']['seuilrem1']) && $_SESSION['catalogue']['seuilrem1']>0 && $total_panier_ht > $_SESSION['catalogue']['seuilrem1'] && $total_panier_ht < $_SESSION['catalogue']['seuilrem2']){
		// 		$obj_cde_li = new commande_ligne();
		// 		$obj_cde_li->fields['id_cde'] = $this->fields['id_cde'];
		// 		$obj_cde_li->fields['id_article'] = 0;
		// 		$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem1'];
		// 		$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem1'];
		// 		$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem1'];
		// 		$obj_cde_li->fields['qte'] = -1;
		// 		$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
		// 		$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['pourcrem1'];
		// 		$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
		// 		$obj_cde_li->fields['ctva'] = 1;
		// 		$total_panier_ht = $total_panier_ht - $obj_cde_li->fields['pu_remise'];
		// 		$obj_cde_li->save();
		// 		$rem += $_SESSION['catalogue']['pourcrem1'];
		// 	}
		// 	if(isset($_SESSION['catalogue']['seuilrem1']) && $_SESSION['catalogue']['seuilrem2']>0 && $total_panier_ht > $_SESSION['catalogue']['seuilrem2']){
		// 		$obj_cde_li = new commande_ligne();
		// 		$obj_cde_li->fields['id_cde'] = $this->fields['id_cde'];
		// 		$obj_cde_li->fields['id_article'] = 0;
		// 		$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem2'];
		// 		$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem2'];
		// 		$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem2'];
		// 		$obj_cde_li->fields['qte'] = -1;
		// 		$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
		// 		$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['pourcrem2'];
		// 		$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
		// 		$obj_cde_li->fields['ctva'] = 1;
		// 		$total_panier_ht = $total_panier_ht - $obj_cde_li->fields['pu_remise'];
		// 		$obj_cde_li->save();
		// 		$rem += $_SESSION['catalogue']['pourcrem2'];
		// 	}

		// 	$obj_client = new client();
		// 	$obj_client->open($this->fields['id_client']);

		// 	if (!$this->fields['hors_cata']) {
		// 		if (isset($this->fields['cli_liv_cp']))
		// 			$frais_port = get_fraisport(73, $this->fields['cli_liv_cp'], $total_panier);
		// 		else {
		// 			$frais_port = 0;
		// 		}
		// 	}
		// 	else {
		// 		$frais_port['fp_montant'] = 0;
		// 	}
		// 	if ($oCatalogue->getParams('cata_base_ttc')) {
		// 		$a_total_tva['fp'] = $frais_port['fp_montant'] - ( $frais_port['fp_montant'] / (1 + _DEFAULT_TVA));
		// 	}
		// 	else {
		// 		$a_total_tva['fp'] = $frais_port['fp_montant'] * _DEFAULT_TVA;
		// 	}


		// 	// calcul des totaux
		// 	$total_tva = 0;
		// 	foreach ($a_total_tva as $key => $totaltva) {
		// 		if($key != 'fp')
		// 			$total_tva += $totaltva * (1 - $rem / 100);
		// 		else
		// 			$total_tva += $totaltva;
		// 	}
		// 	$total_tva = round($total_tva, 2);

		// 	if ($oCatalogue->getParams('cata_base_ttc')) {
		// 		$total_panier_ttc = $total_panier + $frais_port['fp_montant'];
		// 		$total_panier_ht = $total_panier_ttc - $total_tva;
		// 	}
		// 	else {
		// 		$total_panier_ht = $total_panier;
		// 		$total_panier_ttc = $total_panier_ht + $frais_port['fp_montant'] + $total_tva;
		// 	}

		// 	$this->fields['total_ht']	= $total_panier_ht;
		// 	$this->fields['total_tva']	= $total_tva;
		// 	$this->fields['port']		= $frais_port['fp_montant'];
		// 	$this->fields['port_tx_tva']= _DEFAULT_TVA * 100;
		// 	$this->fields['total_ttc']	= $total_panier_ttc;

		// 	parent::save();
		// }
	}

	public function delete() {
		if (!$this->fields['hors_cata']) {
			$q = '	DELETE FROM 	'.commande_ligne::TABLE_NAME.'
					WHERE 			id_cde = '.$this->fields['id_cde'];
			$this->db->query($q);
		}
		else {
			$q = '	DELETE FROM 	'.commande_ligne_hc::TABLE_NAME.'
					WHERE 			id_cde = '.$this->fields['id_cde'];
			$this->db->query($q);
		}

		if (file_exists('./commandes/'.$this->fields['id_cde'].'.txt'))
			unlink('./commandes/'.$this->fields['id_cde'].'.txt');

		parent::delete();
	}

	public function getlignes($key = 'id_cde_ligne', $displayRemiseRow = false) {
		$a_lignes = array();

		if ($this->mode==commande::_STANDARD) {

			if (!$this->fields['hors_cata']) {
				$sql = 'SELECT 	*
						FROM 	'.commande_ligne::TABLE_NAME.'
						WHERE 	id_cde = '.$this->fields['id_cde'];
				$class = "commande_ligne";
			}
			else {
				$sql = 'SELECT 	*
						FROM 	'.commande_ligne_hc::TABLE_NAME.'
						WHERE 	id_cde = '.$this->fields['id_cde'];
				$class = "commande_ligne_hc";
			}
		}
		else {
			$sql = 'SELECT 	c.*
						FROM 	'.commande_ligne::TABLE_NAME.' as c
						LEFT JOIN dims_mod_cata_cde_lignes_Detail as cl
						ON	cl.id_cde_ligne=c.id_cde_ligne
						WHERE 	c.id_cde = '.$this->fields['id_cde'];
				$class = "commande_ligne";
		}

		if(!$displayRemiseRow) {
			$sql .= ' AND qte > 0';
		}

		$sql .= ' ORDER BY id_cde_ligne ASC';

		$rs_lignes = $this->db->query($sql);
		while ($row = $this->db->fetchrow($rs_lignes)) {
			$elem = new $class();
			$elem->openFromResultSet($row);
			$a_lignes[$row[$key]] = $elem;
		}

		return $a_lignes;
	}

	public function getArticles($key = 'id_cde_ligne'){
		$a_lignes = array();
		if (!$this->fields['hors_cata']) {
			$sql = 'SELECT		a.*
					FROM		'.article::TABLE_NAME.' a
					INNER JOIN	'.commande_ligne::TABLE_NAME.' l
					ON			a.id = l.id_article
					AND			l.id_cde = '.$this->fields['id_cde'].'
					ORDER BY	l.id_cde_ligne ASC';
			$rs_lignes = $this->db->query($sql);
			while ($row = $this->db->fetchrow($rs_lignes)) {
				$elem = new article();
				$elem->openFromResultSet($row);
				$a_lignes[$row[$key]] = $elem;
			}
		}
		return $a_lignes;
	}


	public function getFileName() {
		return $this->getId().'.txt';
	}

	public function getId() {
		return $this->fields['id_cde'];
	}

	public function getClient() {
		if (empty($this->fields['id_client'])) {
			return null;
		}
		if (is_null($this->client)) {
			$this->client = new client();
			$this->client->open($this->fields['id_client']);
		}
		return $this->client;
	}

	public function setClient(client $cli) {
		$this->client = $cli;
	}

	public function getUser() {
		if (is_null($this->user)) {
			$this->user = new user();
			$this->user->open($this->fields['id_user']);
		}
		return $this->user;
	}

	public function getLibelle() {
		return $this->fields['libelle'];
	}

	public function sendConfirmationMail($to, $template_name) {
		if (cata_checkEmail($to) && $template_name != '') {
			if (file_exists(DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/mails/cde_confirmation.tpl.php')) {
				include DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/mails/cde_confirmation.tpl.php';
			} else {
				include DIMS_APP_PATH.'modules/catalogue/templates/mails/cde_confirmation.tpl.php';
			}
			$subject = 'Confirmation de votre commande '.$website_name.' n° '.$this->fields['id_cde'];
			dims_send_mail($expeditor, $to, $subject, $message);
		}
	}

	public function sendValidationMail($to, $template_name) {
		if (cata_checkEmail($to) && $template_name != '') {
			require DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/mails/cde_validation.tpl.php';
			$subject = 'Demande de validation de commande';
			dims_send_mail($expeditor, $to, $subject, $message);
		}
	}

	public function sendRefusMail($to, $template_name) {
		if (cata_checkEmail($to) && $template_name != '') {
			require DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/mails/cde_refus.tpl.php';
			$subject = 'Refus de votre commande';
			dims_send_mail($expeditor, $to, $subject, $message);
		}
	}

	public function sendRequireCostingEmail($to, $template_name) {
		if (!empty($to) && !empty($template_name)) {
			require DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/mails/cde_require_costing.tpl.php';
			$subject = 'Demande de chiffrage';
			dims_send_mail($expeditor, $to, $subject, $message);
		}
	}

	public function sendCostingDoneMail($to, $template_name) {
		if (!empty($to) && !empty($template_name)) {
			require DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/mails/cde_costing_done.tpl.php';
			$subject = 'CAAHMRO : Le port de votre panier est chiffré';
			dims_send_mail($expeditor, $to, $subject, $message);
		}
	}

	public static function sendNotEnoughStockMail($representative, $client, $articles, $template_name) {
		$to = $representative->get('lastname').' <'.$representative->get('email').'>';

		if (!empty($to) && !empty($template_name)) {
			require DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/mails/not_enough_stock.tpl.php';
			$subject = 'CAAHMRO : Stock insuffisant sur certains produits';
			dims_send_mail($expeditor, $to, $subject, $message);
		}
	}

	public function isModifiable() {
		return $this->fields['id_user'] == $_SESSION['dims']['userid'] && ( $this->fields['etat'] == self::_STATUS_PROGRESS || $this->fields['etat'] == self::_STATUS_REFUSED );
	}

	public function isValideable() {
		/*switch ($_SESSION['session_adminlevel']) {
			case dims_const::_DIMS_ID_LEVEL_USER:
			case cata_const::_DIMS_ID_LEVEL_USERSUP:
				return $this->isModifiable();
				break;
			case cata_const::_DIMS_ID_LEVEL_PURCHASERESP:*/
				return $this->isModifiable() ||
					( $this->fields['id_user'] != $_SESSION['dims']['userid'] && ( $this->fields['etat'] == self::_STATUS_AWAITING_VALIDATION1 || $this->fields['etat'] == self::_STATUS_AWAITING_VALIDATION2 || $this->fields['etat'] == self::_STATUS_AWAITING_VALIDATION3 ) ) ||
					( $this->fields['id_user'] == $_SESSION['dims']['userid'] && $this->fields['etat'] == self::_STATUS_WAIT_PAYMENT );
				/*break;
		}*/
	}

	public function getStateLabel() {
		return self::getState($this->fields['etat']);
	}

	public function isRefusable() {
		return $_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_SERVICERESP && ( $this->fields['etat'] == self::_STATUS_AWAITING_VALIDATION1 || $this->fields['etat'] == self::_STATUS_AWAITING_VALIDATION2 || $this->fields['etat'] == self::_STATUS_AWAITING_VALIDATION3 ) && $this->fields['id_user'] != $_SESSION['dims']['userid'];
	}

	public function addArticle($ref, $qte = 1) {
		if(!isset($this->articles[$ref])) $this->articles[$ref] = 0;
		$this->articles[$ref] += $qte;
	}

	public function setArticles($articles) {
		$this->articles = $articles;
	}

	public function activePagination($val = true){
		$this->activePag = $val;
	}

	public function build_index($status = 'all', $client = 0, $date_deb = 0, $date_fin = 0, $payment = 'all', $keywords = '', $sort_by = 'date_validation', $sort_way='ASC', $pagination=false){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_moyen_paiement.php";
		$db = dims::getInstance()->getDb();

		if ($this->activePag && !$pagination) {
			$this->total_index = $this->build_index($status, $client, $date_deb, $date_fin, $payment, $keywords, $sort_by, $sort_way, true);
			pagination::liste_page($this->total_index);
			$limit = " LIMIT ".$this->sql_debut.", ".$this->limite_key;
		}else
			$limit="";
		$where = "WHERE c.id_module = ".$_SESSION['dims']['moduleid']."
			AND (c.etat != ".commande::_STATUS_PROGRESS." || c.mode_paiement != 0) ";

		$lstStatus = self::getStatesSelect();
		if($status > 0 && isset($lstStatus[$status]))
			$where .= " AND c.etat = '$status' ";

		if ($client > 0 && $client != ''){
			$where .= " AND c.id_client = $client ";
		}

		if($date_deb > 0){
			$dds = explode('/',$date_deb);
			if (count($dds) == 3){
				$dd = $dds[2].$dds[1].$dds[0]."000000";
			}else
				$dd =$date_deb;
			$where .= " AND c.date_validation >= $dd ";
		}

		if($date_fin > 0){
			$dfs = explode('/',$date_fin);
			if (count($dfs) == 3){
				$df = $dfs[2].$dfs[1].$dfs[0]."235959";
			}else
				$df =$date_deb;
			$where .= " AND c.date_validation <= $df ";
		}

		if(in_array($payment,moyen_paiement::getAllType())){
			$where .= " AND c.mode_paiement = $payment ";
		}

		if( ! empty($keywords)){
			#Récupération des ids d'article correspondant aux keywords
			$go_ids = article::simple_search($keywords);
			if(!empty($go_ids)){
				$where .= " AND c.id_globalobject IN (".implode(',', $go_ids).")";
			}
		}

		$inner_plus = "";
		$order_by = 'c.date_validation';
		switch ($sort_by) {
			case 'client':
				$order_by = "cl.nom";
				break;
			case 'numcde':
				$order_by = "c.id_cde";
				break;
			case 'date_validation':
			default:
				break;
		}
		if(!($sort_way == 'ASC' || $sort_way == 'DESC'))
			$sort_way = 'ASC';

		$sql = 'SELECT 		c.*, cl.*
				FROM 		'.self::TABLE_NAME.' c
				INNER JOIN 	'.client::TABLE_NAME.' cl
				ON 			cl.id_client = c.id_client
				'.$inner_plus.'
				'.$where.'
				ORDER BY '.$order_by.' '.$sort_way.'
				'.$limit;

		$res = $this->db->query($sql);

		if ($pagination) {
			return $this->db->numrows($res);
		}
		else{
			$split = $this->db->split_resultset($res);
			$lst = array();
			foreach($split as $tab){
				$cde = new commande();
				$cde->openFromResultSet($tab['c']);
				$cli = new client();
				$cli->openFromResultSet($tab['cl']);
				$cde->setClient($cli);
				$lst[$cde->fields['id_cde']] = $cde;
			}
			return $lst;
		}
	}

	public static function simple_search($keywords){
		include_once DIMS_APP_PATH.'/modules/system/class_search.php';
		$dims = dims::getInstance();
		$dimsearch = new search($dims);
		$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], self::MY_GLOBALOBJECT_CODE, '');
		$dimsearch->initSearchObject();
		$dimsearch->executeSearch2($keywords, '', $_SESSION['dims']['moduleid'], self::MY_GLOBALOBJECT_CODE, 0, '');
		return array_keys($dimsearch->tabresultat[$_SESSION['dims']['moduleid']][self::MY_GLOBALOBJECT_CODE]);
	}

	public static function getStatesSelect(){
		return array(
			0 => dims_constant::getVal('_DIMS_ALLS'),
		) + self::getStates();
	}

	public static function getStates(){
		return array(
			self::_STATUS_PROGRESS 				=> dims_constant::getVal('_IN_PROGRESS'),
			self::_STATUS_VALIDATED 			=> dims_constant::getVal('_VALIDATED_F'),
			self::_STATUS_REFUSED 				=> dims_constant::getVal('_REFUSED'),
			self::_STATUS_WAIT_PAYMENT 			=> dims_constant::getVal('_AWAITING_SETTLEMENT'),
			self::_STATUS_AWAITING_VALIDATION1 	=> dims_constant::getVal('_DIMS_CONFIRM_WAIT')." 1",
			self::_STATUS_AWAITING_VALIDATION2 	=> dims_constant::getVal('_DIMS_CONFIRM_WAIT')." 2",
			self::_STATUS_AWAITING_VALIDATION3 	=> dims_constant::getVal('_DIMS_CONFIRM_WAIT')." 3",
			self::_STATUS_AWAITING_COSTING      => dims_constant::getVal('AWAITING_COSTING'),
		);
	}

	public static function getState($state){
		$stateLabel = '';

		$statesList = self::getStates();

		if (isset($statesList[$state])) {
			$stateLabel = $statesList[$state];
		}

		return $stateLabel;
	}

	public function getBases(){
		$db = dims::getInstance()->getDb();
		$lst = array();
		if (!$this->fields['hors_cata']) {
			$sql = 'SELECT 		tx_tva, remise, SUM(pu_remise*qte) as ht, SUM(pu_ttc*qte) as ttc
					FROM 		'.commande_ligne::TABLE_NAME.'
					WHERE 		id_cde = '.$this->fields['id_cde']."
					GROUP BY 	tx_tva
					ORDER BY 	tx_tva";
			$res = $db->query($sql);
			while($r = $db->fetchrow($res)){
				$r['remise'] = str_replace('%', '', $r['remise']);
				$lst[] = $r;
			}
		}
		else {
			$sql = 'SELECT 	qte, pu
					FROM 	'.commande_ligne_hc::TABLE_NAME.'
					WHERE 	id_cde = '.$this->fields['id_cde'];
		}
		return $lst;
	}

	public function validate(){
		if($this->isValideable()){
			$this->fields['etat'] = self::_STATUS_VALIDATED;
			$this->save();
			//TODO : envoyer le mail
			// $this->sendConfirmationMail($mail);
		}
	}

	public function exportCommande($type){
		switch($type){
			default:
			case 'pdf':
				$this->exportPdf();
				break;
			case 'csv':
				$this->exportCsv();
				break;
			case 'xls':

				break;
		}

	}

	public function exportPdf(){
		$class = (!$this->fields['hors_cata'])?'pdf_commande':'pdf_commande_hc';
		include_once DIMS_APP_PATH."modules/catalogue/include/class_$class.php";

		$client = $this->getClient();

		$user = new user();
		$user->open($this->fields['id_user']);

		$pdf_commande = new $class();
		$pdf_commande->commande = $this;
		$pdf_commande->client = $client;
		$pdf_commande->user = $user;

		$pdf_commande->afficher_prix = (($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_PURCHASERESP || $client->fields['afficher_prix']) && _SHOW_PRICES) ? true : false;

		// Recherche du service
		$service = new group();
		$service->open($this->fields['id_group']);
		$pdf_commande->service = $service->fields['label'];

		ob_end_clean();
		$pdf_commande->Open();
		$pdf_commande->AliasNbPages();
		$pdf_commande->AddPage();
		$pdf_commande->Content();
		$pdf_commande->Output('commande.pdf', 'D');
		die();
	}

	public function exportCsv(){
		header("Cache-control: private");
		header("Content-type: application/csv");
		header("Content-Disposition: inline; filename=commande.csv");
		header("Pragma: public");

		ob_clean();
		ob_start();

		echo '"'.dims_constant::getVal('_ORDER_NO')."  CD".$this->fields['id_cde'].'","","","","","",""'."\n";
		echo '"","","","","","",""'."\n";
		echo '"","'.dims_constant::getVal('_DIMS_DATE').'","'.dims_constant::getVal('_DUTY_FREE_AMOUNT').'","'.dims_constant::getVal('_PORT_HT').'","'.dims_constant::getVal('_TOTAL_AMOUNT_WITH_DUTY').'","",""'."\n";

		$total_ht = $total_tva = 0;
		$cont = "";
		foreach($this->getlignes() as $ligne){
			$ht = $ligne->fields['pu_ht']*$ligne->fields['qte'];
			$esc = ($ligne->fields['remise']*$ht)/100;
			$tva = (($ht+$esc)*$ligne->fields['tx_tva'])/100;
			$total_tva += $tva;
			$total_ht += $ht+$esc;
			$cont .= '"'.$ligne->fields['ref'].'","'.$ligne->fields['label'].'","'.$ligne->fields['qte'].'","'.money_format('%n',$ligne->fields['pu_ht']).'","'.(($ligne->fields['remise']=='0%' || $ligne->fields['remise'] == 0)?"0":number_format((float)$ligne->fields['remise'],2,',', ' ')).'","'.number_format($ligne->fields['tx_tva'],2,',', ' ').'","'.money_format('%n',$ligne->fields['pu_ht']*$ligne->fields['qte']).'"'."\n";
		}
		// PORT
		$pourcEscPort = 0;
		$esc = ($pourcEscPort*$this->fields['port'])/100;
		$tva = (($this->fields['port']+$esc)*$this->fields['port_tx_tva'])/100;

		$ttTVA = $total_ht+$total_tva+$tva+$this->fields['port']+$esc;
		$dc = dims_timestamp2local($this->fields['date_cree']);
		echo '"","'.$dc['date'].'","'.money_format('%n',$total_ht).'","'.money_format('%n',$this->fields['port']).'","'.money_format('%n',$ttTVA).'","",""'."\n";
		echo '"","","","","","",""'."\n";
		echo '"'.dims_constant::getVal('_DESIGNATION').'","'.dims_constant::getVal('REF').'","'.dims_constant::getVal('_QTY').'","'.dims_constant::getVal('PU_HT').'","'.dims_constant::getVal('_DISCOUNT')." (%)".'","TVA (%)","'.dims_constant::getVal('_TOTAL_DUTY_FREE_AMOUNT').'"'."\n";
		echo $cont;
		ob_end_flush();
		die();
	}

	public function confirmCosting() {
		if ($this->fields['etat'] == self::_STATUS_AWAITING_COSTING) {
			include_once DIMS_APP_PATH."modules/catalogue/include/class_moyen_paiement.php";

			$this->fields['etat'] = self::_STATUS_PROGRESS;
			$this->fields['mode_paiement'] = moyen_paiement::_TYPE_DIFFERE;
			$this->fields['require_costing'] = 0;
			$this->fields['forced_port'] = 1;
			$this->save();

			return true;
		}

		return false;
	}

	public static function getStateIcon($state, $horsCata = false) {
		switch ($state) {
			case commande::_STATUS_VALIDATED:
				return '<span class="" style="color: #90C000; font-weight: bold;">VA</span>';
				break;
			case commande::_STATUS_REFUSED:
				return '<span class="" style="color: #F53825; font-weight: bold;">AN</span>';
				break;
			case commande::_STATUS_PROGRESS:
			case commande::_STATUS_WAIT_PAYMENT:
			case commande::_STATUS_AWAITING_VALIDATION1:
			case commande::_STATUS_AWAITING_VALIDATION2:
			case commande::_STATUS_AWAITING_VALIDATION3:
				if ($horsCata) {
					return '<span class="" style="color: #C0A600; font-weight: bold;">AC</span>';
				} else {
					return '<span class="" style="color: #FFA21B; font-weight: bold;">AV</span>';
				}
				break;
			case commande::_STATUS_AWAITING_COSTING:
				return '<span class="" style="color: #C0A600; font-weight: bold;">AC</span>';
				break;
		}
	}
}
