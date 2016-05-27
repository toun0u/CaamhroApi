<?php
class moyen_paiement extends dims_data_object{
	// Type de moyen de paiement
	const _TYPE_CB          = 1;
	const _TYPE_VIREMENT    = 2;
	const _TYPE_DIFFERE     = 3;
	const _TYPE_PAYPAL      = 4;
	const _TYPE_CHEQUE      = 5;

	const TABLE_NAME = 'dims_mod_cata_moyen_paiement';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}


	// ---------------------------------
	// GETTERS
	// ---------------------------------

	public static function getAll($module_id = -1){
		if ($module_id == -1) {
			$module_id = $_SESSION['dims']['moduleid'];
		}

		$db = dims::getInstance()->getDb();
		$sel = "SELECT  *
				FROM    ".self::TABLE_NAME."
				WHERE   id_module = ".$module_id;
		$res = $db->query($sel);
		$lst = array();
		if($db->numrows($res) > 0){
			while($r = $db->fetchrow($res)){
				$mp = new moyen_paiement();
				$mp->openFromResultSet($r);
				$lst[] = $mp;
			}
		}else{
			$lst = self::initPaiements();
		}
		return $lst;
	}

	public static function getByType($type, $module_id = -1) {
		if ($module_id == -1) {
			$module_id = $_SESSION['dims']['moduleid'];
		}

		$db = dims::getInstance()->getDb();

		$rs = $db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE type = '.$type.' AND id_module = '.$module_id.' LIMIT 0, 1');
		if ($db->numrows($rs)) {
			$mp = new moyen_paiement();
			$mp->openFromResultSet($db->fetchrow($rs));
			return $mp;
		}
		else {
			return null;
		}
	}

	public static function initPaiements(){
		$lst = array();
		foreach(self::getAllType() as $t){
			$mp = new moyen_paiement();
			$mp->init_description();
			$mp->setugm();
			$mp->fields['type'] = $t;
			$mp->fields['active'] = true;
			$mp->save();
			$lst[] = $mp;
		}
		return $lst;
	}

	public static function getActivePaiement($module_id = -1){
		if ($module_id == -1) {
			$module_id = $_SESSION['dims']['moduleid'];
		}

		$db = dims::getInstance()->getDb();
		$sel = "SELECT  *
				FROM    ".self::TABLE_NAME."
				WHERE   id_module = ".$module_id."
				AND     active = 1";
		$res = $db->query($sel);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$mp = new moyen_paiement();
			$mp->openFromResultSet($r);
			$lst[$mp->getType()] = $mp;
		}
		return $lst;
	}

	public static function getAllType(){
		return array(   self::_TYPE_CB          => self::_TYPE_CB,
						self::_TYPE_VIREMENT    => self::_TYPE_VIREMENT,
						self::_TYPE_DIFFERE     => self::_TYPE_DIFFERE,
						self::_TYPE_PAYPAL      => self::_TYPE_PAYPAL,
						self::_TYPE_CHEQUE      => self::_TYPE_CHEQUE);
	}

	public static function getPaiementsSelect(){
		$lst = array(0 => dims_constant::getVal('_DIMS_ALLS'));
		foreach(self::getActivePaiement() as $mp){
			$lst[$mp->get('id')] = moyen_paiement::getTypeLabel($mp->fields['type']);
		}
		return $lst;
	}

	public static function getTypeLabel($type){
		switch ($type) {
			case self::_TYPE_CB:
				return dims_constant::getVal('_CREDIT_CARD');
				break;
			case self::_TYPE_VIREMENT:
				return dims_constant::getVal('_TRANSFER');
				break;
			case self::_TYPE_DIFFERE:
				return dims_constant::getVal('_DELAYED');
				break;
			case self::_TYPE_PAYPAL:
				return dims_constant::getVal('_PAYPAL');
				break;
			case self::_TYPE_CHEQUE:
				return dims_constant::getVal('_CHEQUE');
				break;
			default:
				return "";
				break;
		}
	}

	public function getType() {
		return $this->fields['type'];
	}

	public function getLabel() {
		return moyen_paiement::getTypeLabel($this->getType());
	}

	public function getDescription() {
		return $this->fields['description'];
	}

	public function getDescriptionRaw() {
		return stripslashes(str_replace('\r\n', "\r\n", $this->fields['description']));
	}

	public function getDescriptionHTML() {
		return stripslashes(str_replace('\r\n', "<br/>", $this->fields['description']));
	}

	public function isActive() {
		return $this->fields['active'];
	}


	// ---------------------------------
	// Spécifique Paypal
	// ---------------------------------

	public function getPayPalUrl() {
		if ($this->fields['paypal_production']) {
			return 'https://www.paypal.com/cgi-bin/webscr';
		}
		else {
			return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
	}

	public function getPaypalEmail() {
		return $this->fields['paypal_email'];
	}

	public function getPaypalSuccessURL() {
		return $this->fields['paypal_success_url'];
	}

	public function getPaypalCancelURL() {
		return $this->fields['paypal_cancel_url'];
	}

	public function getLogoUrl() {
		if (empty($this->fields['paypal_logo_doc_id'])) {
			return '';
		}

		$dims = dims::getInstance();

		// ouverture du doc
		$doc = new docfile();
		$doc->open($this->fields['paypal_logo_doc_id']);
		return $dims->getProtocol().$dims->getHttpHost().'/'.$doc->getwebpath();
	}


	// ---------------------------------
	// SETTERS
	// ---------------------------------

	public function setDescription($description) {
		$this->fields['description'] = str_replace('\r\n', '', $description);
	}

	public function switchActive() {
		$this->fields['active'] = !$this->fields['active'];
	}

}
