<?php

include_once DIMS_APP_PATH.'modules/system/class_param_type.php';
include_once DIMS_APP_PATH.'modules/system/class_param_default.php';

class cata_param extends param_default {

	const TABLE_NAME = 'dims_param_default';

	const REGLE_CALCUL_PRIORITE_MOINS_CHER  = 1;
	const REGLE_CALCUL_PRIORITE_MARCHE      = 2;
	const REGLE_CALCUL_PRIORITE_CLIENT      = 3;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id_module', 'name');
	}

	public function getByName($name, $moduleid = 0) {
		if ($moduleid == 0 && !empty($_SESSION['dims']['moduleid'])) {
			$moduleid = $_SESSION['dims']['moduleid'];
		}

		if ($moduleid > 0) {
			$sql = '
				SELECT *
				FROM dims_param_default
				WHERE name = \''.$name.'\'
				AND id_module = '.$moduleid.'
				LIMIT 0, 1';

			$this->resultid = $this->db->query($sql);
			$this->numrows = $this->db->numrows($this->resultid);
			$this->fields = $this->db->fetchrow($this->resultid);

			if ($this->numrows>0) $this->new = false;
			$this->updateGOOnOpenedRow();

			return $this->numrows;
		}
		else {
			return 0;
		}
	}

	public function getName() {
		return $this->fields['name'];
	}

	public function setName($name) {
		$this->fields['name'] = $name;
	}

	public function getValue() {
		return $this->fields['value'];
	}

	public function setValue($value) {
		$this->fields['value'] = $value;
	}

	public static function getRootGroup(){
		$param = new cata_param();
		$param->getByName('cata_group_id');
		if(!$param->isNew()){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($this->fields['value']);
			if(!$elem->isNew())
				return $elem;
			else
				return null;
		}else{
			return null;
		}

	}


	// Gestion des paramètres de langue
	public static function isActiveParamLang(){
		$param = new cata_param();
		$param->getByName('active_lg');
		if($param->isNew()) $param->setValue(0);
		return $param->getValue();
	}
	public static function setActiveParamLangVal($bool){
		$param = new cata_param();
		$param->getByName('active_lg');
		if($param->isNew()){
			$param->init_description();
			$param->fields['name'] = 'active_lg';
			$param->fields['id_module'] = $_SESSION['dims']['moduleid'];
		}
		$param->setValue($bool);
		$param->save();
		self::reloadActiveLang();
		self::reloadDefaultLang();
	}
	public static function getActiveLang(){
		if(!isset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'])){
			$_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'] = array();
			if(self::isActiveParamLang()){
				include_once DIMS_APP_PATH."modules/system/class_lang.php";
				foreach(lang::all() as $lg){
					$paramLg = new cata_param();
					$paramLg->getByName('active_lg_'.$lg->get('id'));
					if(!$paramLg->isNew() && $paramLg->getValue())
						$_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'][$lg->get('id')] = $lg->fields['label'];
				}
			}
		}
		return $_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'];
	}

	public static function setActiveLang($id){
		self::getActiveLang();
		if(!isset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'][$id])){
			include_once DIMS_APP_PATH."modules/system/class_lang.php";
			$sel = "SELECT  *
					FROM    ".lang::TABLE_NAME."
					WHERE   id = $id";
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel);
			if($r = $db->fetchrow($res)){
				$_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'][$id] = $r['label'];
				$param = new cata_param();
				$param->getByName('active_lg_'.$id);
				if($param->isNew()){
					$param->init_description();
					$param->fields['name'] = 'active_lg_'.$id;
					$param->fields['id_module'] = $_SESSION['dims']['moduleid'];
				}
				$param->setValue(1);
				$param->save();
			}
		}
	}

	public static function isActiveLang($id){
		self::getActiveLang();
		return isset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'][$id]);
	}

	public static function setUnactiveLang($id){
		self::getActiveLang();
		if(isset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'][$id])){
			$param = new cata_param();
			$param->getByName('active_lg_'.$id);
			$param->setValue(0);
			$param->save();
			unset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'][$id]);
		}
	}

	public static function reloadActiveLang(){
		unset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg']);
		self::getActiveLang();
	}

	public static function getDefaultLang(){
		if(!isset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['default_lg'])){
			if(self::isActiveParamLang()){
				$param = new cata_param();
				$param->getByName('default_lg');
				if(!$param->isNew() && $param->getValue() > 0)
					$_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['default_lg'] = $param->getValue();
				else{
					$_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['default_lg'] = 1; //current(array_keys(self::getActiveLang()));
				}
			}else{
				$_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['default_lg']=1;
			}
		}
		return $_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['default_lg'];
	}

	public static function setDefaultLang($id){
		self::getActiveLang();
		if(isset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['active_lg'][$id])){ // on teste si la langue est dans celle dispo
			$_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['default_lg'] = $id;
			$param = new cata_param();
			$param->getByName('default_lg');
			if($param->isNew()){
				$param->init_description();
				$param->fields['name'] = 'default_lg';
				$param->fields['id_module'] = $_SESSION['dims']['moduleid'];
			}
			$param->setValue($id);
			$param->save();
		}
	}

	public static function reloadDefaultLang(){
		unset($_SESSION['dims']['cata'][$_SESSION['dims']['moduleid']]['default_lg']);
		self::getDefaultLang();
	}

	// Gestion des params compte clients
	public static function initComptesClients(){
		$lst = array();

		$codifAuto = new cata_param();
		$codifAuto->getByName('auto_codif');
		if($codifAuto->isNew()){
			$codifAuto->init_description();
			$codifAuto->setValue(0);
			$codifAuto->fields['name'] = 'auto_codif';
			$codifAuto->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$codifAuto->save();
		}
		$lst['auto_codif'] = $codifAuto;

		$baseCodif = new cata_param();
		$baseCodif->getByName('base_codif');
		if($baseCodif->isNew()){
			$baseCodif->init_description();
			$baseCodif->setValue("411******");
			$baseCodif->fields['name'] = 'base_codif';
			$baseCodif->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$baseCodif->save();
		}
		$lst['base_codif'] = $baseCodif;

		$servValid = new cata_param();
		$servValid->getByName('services_validation');
		if($servValid->isNew()){
			$servValid->init_description();
			$servValid->setValue(0);
			$servValid->fields['name'] = 'services_validation';
			$servValid->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$servValid->save();
		}
		$lst['services_validation'] = $servValid;

		$isUserWithoutVal = new cata_param();
		$isUserWithoutVal->getByName('is_user_without_valid');
		if($isUserWithoutVal->isNew()){
			$isUserWithoutVal->init_description();
			$isUserWithoutVal->setValue(0);
			$isUserWithoutVal->fields['name'] = 'is_user_without_valid';
			$isUserWithoutVal->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$isUserWithoutVal->save();
		}
		$lst['is_user_without_valid'] = $isUserWithoutVal;

		$userWithoutVal = new cata_param();
		$userWithoutVal->getByName('user_without_valid');
		if($userWithoutVal->isNew()){
			$userWithoutVal->init_description();
			$userWithoutVal->setValue(dims_constant::getVal('_PURCHASER_FREE'));
			$userWithoutVal->fields['name'] = 'user_without_valid';
			$userWithoutVal->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$userWithoutVal->save();
		}
		$lst['user_without_valid'] = $userWithoutVal;

		$isUserWithVal = new cata_param();
		$isUserWithVal->getByName('is_user_with_valid');
		if($isUserWithVal->isNew()){
			$isUserWithVal->init_description();
			$isUserWithVal->setValue(0);
			$isUserWithVal->fields['name'] = 'is_user_with_valid';
			$isUserWithVal->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$isUserWithVal->save();
		}
		$lst['is_user_with_valid'] = $isUserWithVal;

		$userWithVal = new cata_param();
		$userWithVal->getByName('user_with_valid');
		if($userWithVal->isNew()){
			$userWithVal->init_description();
			$userWithVal->setValue(dims_constant::getVal('_BUYER'));
			$userWithVal->fields['name'] = 'user_with_valid';
			$userWithVal->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$userWithVal->save();
		}
		$lst['user_with_valid'] = $userWithVal;

		$isRespServ = new cata_param();
		$isRespServ->getByName('is_service_manager');
		if($isRespServ->isNew()){
			$isRespServ->init_description();
			$isRespServ->setValue(0);
			$isRespServ->fields['name'] = 'is_service_manager';
			$isRespServ->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$isRespServ->save();
		}
		$lst['is_service_manager'] = $isRespServ;

		$respServ = new cata_param();
		$respServ->getByName('service_manager');
		if($respServ->isNew()){
			$respServ->init_description();
			$respServ->setValue(dims_constant::getVal('_SERVICE_MANAGER'));
			$respServ->fields['name'] = 'service_manager';
			$respServ->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$respServ->save();
		}
		$lst['service_manager'] = $respServ;

		$isRespAchat = new cata_param();
		$isRespAchat->getByName('is_purchasing_manager');
		if($isRespAchat->isNew()){
			$isRespAchat->init_description();
			$isRespAchat->setValue(0);
			$isRespAchat->fields['name'] = 'is_purchasing_manager';
			$isRespAchat->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$isRespAchat->save();
		}
		$lst['is_purchasing_manager'] = $isRespAchat;

		$respAchat = new cata_param();
		$respAchat->getByName('purchasing_manager');
		if($respAchat->isNew()){
			$respAchat->init_description();
			$respAchat->setValue(dims_constant::getVal('_PURCHASING_MANAGER'));
			$respAchat->fields['name'] = 'purchasing_manager';
			$respAchat->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$respAchat->save();
		}
		$lst['purchasing_manager'] = $respAchat;

		$isAdminCompte = new cata_param();
		$isAdminCompte->getByName('is_account_admin');
		if($isAdminCompte->isNew()){
			$isAdminCompte->init_description();
			$isAdminCompte->setValue(0);
			$isAdminCompte->fields['name'] = 'is_account_admin';
			$isAdminCompte->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$isAdminCompte->save();
		}
		$lst['is_account_admin'] = $isAdminCompte;

		$adminCompte = new cata_param();
		$adminCompte->getByName('account_admin');
		if($adminCompte->isNew()){
			$adminCompte->init_description();
			$adminCompte->setValue(dims_constant::getVal('_DIRECTOR'));
			$adminCompte->fields['name'] = 'account_admin';
			$adminCompte->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$adminCompte->save();
		}
		$lst['account_admin'] = $adminCompte;

		$default_lvl_registration = new cata_param();
		$default_lvl_registration->getByName('default_lvl_registration');
		if($default_lvl_registration->isNew()){
			$default_lvl_registration->init_description();
			$default_lvl_registration->setValue($lst['user_without_valid']->getName());
			$default_lvl_registration->fields['name'] = 'default_lvl_registration';
			$default_lvl_registration->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$default_lvl_registration->save();
		}
		$lst['default_lvl_registration'] = $default_lvl_registration;

		return $lst;
	}

	public static function getSelectServicesDispo(){
		$lst = array();
		$servValid = new cata_param();
		$servValid->getByName('services_validation');
		if(!$servValid->isNew() && $servValid->getValue()){

			$isUserWithoutVal = new cata_param();
			$isUserWithoutVal->getByName('is_user_without_valid');
			if(!$isUserWithoutVal->isNew() && $isUserWithoutVal->getValue()){
				$userWithoutVal = new cata_param();
				$userWithoutVal->getByName('user_without_valid');
				if($userWithoutVal->isNew()){
					$userWithoutVal->init_description();
					$userWithoutVal->setValue(dims_constant::getVal('_PURCHASER_FREE'));
					$userWithoutVal->fields['name'] = 'user_without_valid';
					$userWithoutVal->fields['id_module'] = $_SESSION['dims']['moduleid'];
					$userWithoutVal->save();
				}
				$lst['user_without_valid'] = $userWithoutVal->getValue();
			}

			$isUserWithVal = new cata_param();
			$isUserWithVal->getByName('is_user_with_valid');
			if(!$isUserWithVal->isNew() && $isUserWithVal->getValue()){
				$userWithVal = new cata_param();
				$userWithVal->getByName('user_with_valid');
				if($userWithVal->isNew()){
					$userWithVal->init_description();
					$userWithVal->setValue(dims_constant::getVal('_BUYER'));
					$userWithVal->fields['name'] = 'user_with_valid';
					$userWithVal->fields['id_module'] = $_SESSION['dims']['moduleid'];
					$userWithVal->save();
				}
				$lst['user_with_valid'] = $userWithVal->getValue();
			}

			$isRespServ = new cata_param();
			$isRespServ->getByName('is_service_manager');
			if(!$isRespServ->isNew() && $isRespServ->getValue()){
				$respServ = new cata_param();
				$respServ->getByName('service_manager');
				if($respServ->isNew()){
					$respServ->init_description();
					$respServ->setValue(dims_constant::getVal('_SERVICE_MANAGER'));
					$respServ->fields['name'] = 'service_manager';
					$respServ->fields['id_module'] = $_SESSION['dims']['moduleid'];
					$respServ->save();
				}
				$lst['service_manager'] = $respServ->getValue();
			}

			$isRespAchat = new cata_param();
			$isRespAchat->getByName('is_purchasing_manager');
			if(!$isRespAchat->isNew() && $isRespAchat->getValue()){
				$respAchat = new cata_param();
				$respAchat->getByName('purchasing_manager');
				if($respAchat->isNew()){
					$respAchat->init_description();
					$respAchat->setValue(dims_constant::getVal('_PURCHASING_MANAGER'));
					$respAchat->fields['name'] = 'purchasing_manager';
					$respAchat->fields['id_module'] = $_SESSION['dims']['moduleid'];
					$respAchat->save();
				}
				$lst['purchasing_manager'] = $respAchat->getValue();
			}

			$isAdminCompte = new cata_param();
			$isAdminCompte->getByName('is_account_admin');
			if(!$isAdminCompte->isNew() && $isAdminCompte->getValue()){
				$adminCompte = new cata_param();
				$adminCompte->getByName('account_admin');
				if($adminCompte->isNew()){
					$adminCompte->init_description();
					$adminCompte->setValue(dims_constant::getVal('_DIRECTOR'));
					$adminCompte->fields['name'] = 'account_admin';
					$adminCompte->fields['id_module'] = $_SESSION['dims']['moduleid'];
					$adminCompte->save();
				}
				$lst['account_admin'] = $adminCompte->getValue();
			}

			return $lst;
		}
	}

	public static function GetLabelCorresp($lvlId){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_const.php";
		switch ($lvlId) {
			case dims_const::_DIMS_ID_LEVEL_USER: // 10
				return 'user_with_valid';
				break;
			case cata_const::_DIMS_ID_LEVEL_USERSUP: // 11
				return 'user_without_valid';
				break;
			case cata_const::_DIMS_ID_LEVEL_SERVICERESP: // 12
				return 'service_manager';
				break;
			case cata_const::_DIMS_ID_LEVEL_PURCHASERESP: // 13
				return 'purchasing_manager';
				break;
			case dims_const::_DIMS_ID_LEVEL_GROUPMANAGER: // 15
				return 'account_admin';
				break;
			default:
				$default_lvl_registration = new cata_param();
				$default_lvl_registration->getByName('default_lvl_registration');
				if($default_lvl_registration->isNew()){
					$default_lvl_registration->init_description();
					$default_lvl_registration->setValue($lst['user_without_valid']->getName());
					$default_lvl_registration->fields['name'] = 'default_lvl_registration';
					$default_lvl_registration->fields['id_module'] = $_SESSION['dims']['moduleid'];
					$default_lvl_registration->save();
				}
				return $default_lvl_registration->getValue();
				break;
		}
	}
	public static function GetIdCorresp($lvlLabel){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_const.php";
		switch ($lvlLabel) {
			case 'user_with_valid': // 10
				return dims_const::_DIMS_ID_LEVEL_USER;
				break;
			case 'user_without_valid': // 11
				return cata_const::_DIMS_ID_LEVEL_USERSUP;
				break;
			case 'service_manager': // 12
				return cata_const::_DIMS_ID_LEVEL_SERVICERESP;
				break;
			case 'purchasing_manager': // 13
				return cata_const::_DIMS_ID_LEVEL_PURCHASERESP;
				break;
			case 'account_admin': // 15
				return dims_const::_DIMS_ID_LEVEL_GROUPMANAGER;
				break;
			default:
				$default_lvl_registration = new cata_param();
				$default_lvl_registration->getByName('default_lvl_registration');
				if($default_lvl_registration->isNew()){
					$default_lvl_registration->init_description();
					$default_lvl_registration->setValue($lst['user_without_valid']->getName());
					$default_lvl_registration->fields['name'] = 'default_lvl_registration';
					$default_lvl_registration->fields['id_module'] = $_SESSION['dims']['moduleid'];
					$default_lvl_registration->save();
				}
				return self::GetIdCorresp($default_lvl_registration->getValue());
				break;
		}
	}

	// Gestion des param Espaces Clients
	public static function initEspacesClients(){
		$lst = array();

		$activeCart = new cata_param();
		$activeCart->getByName('active_cart');
		if($activeCart->isNew()){
			$activeCart->init_description();
			$activeCart->setValue(0);
			$activeCart->fields['name'] = 'active_cart';
			$activeCart->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$activeCart->save();
		}
		$lst['active_cart'] = $activeCart;

		$personalInformations = new cata_param();
		$personalInformations->getByName('personal_informations');
		if($personalInformations->isNew()){
			$personalInformations->init_description();
			$personalInformations->setValue(0);
			$personalInformations->fields['name'] = 'personal_informations';
			$personalInformations->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$personalInformations->save();
		}
		$lst['personal_informations'] = $personalInformations;

		$waitCommandes = new cata_param();
		$waitCommandes->getByName('wait_commandes');
		if($waitCommandes->isNew()){
			$waitCommandes->init_description();
			$waitCommandes->setValue(0);
			$waitCommandes->fields['name'] = 'wait_commandes';
			$waitCommandes->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$waitCommandes->save();
		}
		$lst['wait_commandes'] = $waitCommandes;

		$histoCommandes = new cata_param();
		$histoCommandes->getByName('history_cmd');
		if($histoCommandes->isNew()){
			$histoCommandes->init_description();
			$histoCommandes->setValue(0);
			$histoCommandes->fields['name'] = 'history_cmd';
			$histoCommandes->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$histoCommandes->save();
		}
		$lst['history_cmd'] = $histoCommandes;

		$exceptionalOrders = new cata_param();
		$exceptionalOrders->getByName('exceptional_orders');
		if($exceptionalOrders->isNew()){
			$exceptionalOrders->init_description();
			$exceptionalOrders->setValue(0);
			$exceptionalOrders->fields['name'] = 'exceptional_orders';
			$exceptionalOrders->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$exceptionalOrders->save();
		}
		$lst['exceptional_orders'] = $exceptionalOrders;

		$bonLivraison = new cata_param();
		$bonLivraison->getByName('bon_livraison');
		if($bonLivraison->isNew()){
			$bonLivraison->init_description();
			$bonLivraison->setValue(0);
			$bonLivraison->fields['name'] = 'bon_livraison';
			$bonLivraison->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$bonLivraison->save();
		}
		$lst['bon_livraison'] = $bonLivraison;

		$remainings = new cata_param();
		$remainings->getByName('remainings');
		if($remainings->isNew()){
			$remainings->init_description();
			$remainings->setValue(0);
			$remainings->fields['name'] = 'remainings';
			$remainings->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$remainings->save();
		}
		$lst['remainings'] = $remainings;

		$invoices = new cata_param();
		$invoices->getByName('invoices');
		if($invoices->isNew()){
			$invoices->init_description();
			$invoices->setValue(0);
			$invoices->fields['name'] = 'invoices';
			$invoices->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$invoices->save();
		}
		$lst['invoices'] = $invoices;

		$accountStatements = new cata_param();
		$accountStatements->getByName('account_statements');
		if($accountStatements->isNew()){
			$accountStatements->init_description();
			$accountStatements->setValue(0);
			$accountStatements->fields['name'] = 'account_statements';
			$accountStatements->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$accountStatements->save();
		}
		$lst['account_statements'] = $accountStatements;

		$saisieRapide = new cata_param();
		$saisieRapide->getByName('saisie_rapide');
		if($saisieRapide->isNew()){
			$saisieRapide->init_description();
			$saisieRapide->setValue(0);
			$saisieRapide->fields['name'] = 'saisie_rapide';
			$saisieRapide->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$saisieRapide->save();
		}
		$lst['saisie_rapide'] = $saisieRapide;

		$panierType = new cata_param();
		$panierType->getByName('panier_type');
		if($panierType->isNew()){
			$panierType->init_description();
			$panierType->setValue(0);
			$panierType->fields['name'] = 'panier_type';
			$panierType->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$panierType->save();
		}
		$lst['panier_type'] = $panierType;

		$schoolLists = new cata_param();
		$schoolLists->getByName('school_lists');
		if($schoolLists->isNew()){
			$schoolLists->init_description();
			$schoolLists->setValue(0);
			$schoolLists->fields['name'] = 'school_lists';
			$schoolLists->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$schoolLists->save();
		}
		$lst['school_lists'] = $schoolLists;

		$statistics = new cata_param();
		$statistics->getByName('statistics');
		if($statistics->isNew()){
			$statistics->init_description();
			$statistics->setValue(0);
			$statistics->fields['name'] = 'statistics';
			$statistics->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$statistics->save();
		}
		$lst['statistics'] = $statistics;

		$hierarchyValidation = new cata_param();
		$hierarchyValidation->getByName('hierarchy_validation');
		if($hierarchyValidation->isNew()){
			$hierarchyValidation->init_description();
			$hierarchyValidation->setValue(0);
			$hierarchyValidation->fields['name'] = 'hierarchy_validation';
			$hierarchyValidation->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$hierarchyValidation->save();
		}
		$lst['hierarchy_validation'] = $hierarchyValidation;

		return $lst;
	}

	// Gestion des param Notifications Email
	public static function initNotifMail(){
		$lst = array();

		$active_notif_mail = new cata_param();
		$active_notif_mail->getByName('active_notif_mail');
		if($active_notif_mail->isNew()){
			$active_notif_mail->init_description();
			$active_notif_mail->setValue(0);
			$active_notif_mail->fields['name'] = 'active_notif_mail';
			$active_notif_mail->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$active_notif_mail->save();
		}
		$lst['active_notif_mail'] = $active_notif_mail;

		$notif_send = new cata_param();
		$notif_send->getByName('notif_send_mail');
		if($notif_send->isNew()){
			$notif_send->init_description();
			$notif_send->setValue("");
			$notif_send->fields['name'] = 'notif_send_mail';
			$notif_send->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$notif_send->save();
		}
		$lst['notif_send_mail'] = $notif_send;

		$reception_cmd_mail = new cata_param();
		$reception_cmd_mail->getByName('reception_cmd_mail');
		if($reception_cmd_mail->isNew()){
			$reception_cmd_mail->init_description();
			$reception_cmd_mail->setValue("");
			$reception_cmd_mail->fields['name'] = 'reception_cmd_mail';
			$reception_cmd_mail->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$reception_cmd_mail->save();
		}
		$lst['reception_cmd_mail'] = $reception_cmd_mail;

		$reception_retour_mail = new cata_param();
		$reception_retour_mail->getByName('reception_retour_mail');
		if($reception_retour_mail->isNew()){
			$reception_retour_mail->init_description();
			$reception_retour_mail->setValue("");
			$reception_retour_mail->fields['name'] = 'reception_retour_mail';
			$reception_retour_mail->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$reception_retour_mail->save();
		}
		$lst['reception_retour_mail'] = $reception_retour_mail;

		$alert_notif_mail = new cata_param();
		$alert_notif_mail->getByName('alert_notif_mail');
		if($alert_notif_mail->isNew()){
			$alert_notif_mail->init_description();
			$alert_notif_mail->setValue("");
			$alert_notif_mail->fields['name'] = 'alert_notif_mail';
			$alert_notif_mail->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$alert_notif_mail->save();
		}
		$lst['alert_notif_mail'] = $alert_notif_mail;

		$logisticDeptEmail = new cata_param();
		$logisticDeptEmail->getByName('logistic_dept_email');
		if($logisticDeptEmail->isNew()){
			$logisticDeptEmail->init_description();
			$logisticDeptEmail->setValue("");
			$logisticDeptEmail->fields['name'] = 'logistic_dept_email';
			$logisticDeptEmail->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$logisticDeptEmail->save();
		}
		$lst['logistic_dept_email'] = $logisticDeptEmail;

		$logisticDeptEmailCopy = new cata_param();
		$logisticDeptEmailCopy->getByName('logistic_dept_email_copy');
		if($logisticDeptEmailCopy->isNew()){
			$logisticDeptEmailCopy->init_description();
			$logisticDeptEmailCopy->setValue("");
			$logisticDeptEmailCopy->fields['name'] = 'logistic_dept_email_copy';
			$logisticDeptEmailCopy->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$logisticDeptEmailCopy->save();
		}
		$lst['logistic_dept_email_copy'] = $logisticDeptEmailCopy;

		$logisticDeptEmailCopyCopy = new cata_param();
		$logisticDeptEmailCopyCopy->getByName('logistic_dept_email_copy_copy');
		if($logisticDeptEmailCopyCopy->isNew()){
			$logisticDeptEmailCopyCopy->init_description();
			$logisticDeptEmailCopyCopy->setValue("");
			$logisticDeptEmailCopyCopy->fields['name'] = 'logistic_dept_email_copy_copy';
			$logisticDeptEmailCopyCopy->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$logisticDeptEmailCopyCopy->save();
		}
		$lst['logistic_dept_email_copy_copy'] = $logisticDeptEmailCopyCopy;

		return $lst;
	}

	// Gestion des param Tarifs / Gestion des vente
	public static function initTarifGestVente(){
		$lst = array();

		$default_tva = new cata_param();
		$default_tva->getByName('default_tva');
		if($default_tva->isNew()){
			$default_tva->init_description();
			$default_tva->setValue(19.6);
			$default_tva->fields['name'] = 'default_tva';
			$default_tva->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$default_tva->save();
		}
		$lst['default_tva'] = $default_tva;

		$remise_web = new cata_param();
		$remise_web->getByName('remise_web');
		if($remise_web->isNew()){
			$remise_web->init_description();
			$remise_web->setValue(0);
			$remise_web->fields['name'] = 'remise_web';
			$remise_web->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$remise_web->save();
		}
		$lst['remise_web'] = $remise_web;

		$devise = new cata_param();
		$devise->getByName('devise');
		if($devise->isNew()){
			$devise->init_description();
			$devise->setValue("€");
			$devise->fields['name'] = 'devise';
			$devise->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$devise->save();
		}
		$lst['devise'] = $devise;

		$command_mini = new cata_param();
		$command_mini->getByName('command_mini');
		if($command_mini->isNew()){
			$command_mini->init_description();
			$command_mini->setValue(0);
			$command_mini->fields['name'] = 'command_mini';
			$command_mini->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$command_mini->save();
		}
		$lst['command_mini'] = $command_mini;

		$franco_port = new cata_param();
		$franco_port->getByName('franco_port');
		if($franco_port->isNew()){
			$franco_port->init_description();
			$franco_port->setValue(0);
			$franco_port->fields['name'] = 'franco_port';
			$franco_port->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$franco_port->save();
		}
		$lst['franco_port'] = $franco_port;

		$supplement_hayon = new cata_param();
		$supplement_hayon->getByName('supplement_hayon');
		if($supplement_hayon->isNew()){
			$supplement_hayon->init_description();
			$supplement_hayon->setValue(0);
			$supplement_hayon->fields['name'] = 'supplement_hayon';
			$supplement_hayon->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$supplement_hayon->save();
		}
		$lst['supplement_hayon'] = $supplement_hayon;

		$gestion_prix_net = new cata_param();
		$gestion_prix_net->getByName('gestion_prix_net');
		if($gestion_prix_net->isNew()){
			$gestion_prix_net->init_description();
			$gestion_prix_net->setValue(0);
			$gestion_prix_net->fields['name'] = 'gestion_prix_net';
			$gestion_prix_net->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$gestion_prix_net->save();
		}
		$lst['gestion_prix_net'] = $gestion_prix_net;

		$gestion_escompte = new cata_param();
		$gestion_escompte->getByName('gestion_escompte');
		if($gestion_escompte->isNew()){
			$gestion_escompte->init_description();
			$gestion_escompte->setValue(0);
			$gestion_escompte->fields['name'] = 'gestion_escompte';
			$gestion_escompte->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$gestion_escompte->save();
		}
		$lst['gestion_escompte'] = $gestion_escompte;

		$regles_remises = new cata_param();
		$regles_remises->getByName('regles_remises');
		if($regles_remises->isNew()){
			$regles_remises->init_description();
			$regles_remises->setValue("");
			$regles_remises->fields['name'] = 'regles_remises';
			$regles_remises->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$regles_remises->save();
		}
		$lst['regles_remises'] = $regles_remises;

		return $lst;
	}

	public static function getSelReglesRemises(){

		return array(
			self::REGLE_CALCUL_PRIORITE_MOINS_CHER => "Toujours le moins cher",
			self::REGLE_CALCUL_PRIORITE_MARCHE     => "Priorité au marché",
			self::REGLE_CALCUL_PRIORITE_CLIENT     => "Priorité au client"
			);
	}
}
?>
