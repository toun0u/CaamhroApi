<?php

class cata_config {

	// valeurs par défaut
	const DEFAULT_SYNCHRONIZED				= 0;
	const DEFAULT_MODE_B2C					= 0;
	const DEFAULT_VISIBLE_NOT_CONNCTED		= 1;
	const DEFAULT_ACTIVE_MARQUES			= 1;
	const DEFAULT_BASE_TTC					= 0;
	const DEFAULT_PERMIT_HORS_CATA			= 0;
	const DEFAULT_NEGATIVE_STOCKS			= 0;
	const DEFAULT_SHOW_STOCKS				= 0;
	const DEFAULT_DETAIL_RELIQUATS			= 0;
	const DEFAULT_ALERT_STOCK_MINI			= 0;
	const DEFAULT_NAV_STYLE					= 'finder';
	const DEFAULT_CART_MANAGEMENT			= 'bdd';
	const DEFAULT_FILTERS_VIEW				= 'global';

	private $params;

	public static function getDomainId() {
		$db = dims::getInstance()->getDb();

		$rs = $db->query('SELECT id FROM dims_domain WHERE domain = \''.$_SERVER['HTTP_HOST'].'\' LIMIT 0,1');

		if($db->numrows($rs)) {
			$row = $db->fetchrow($rs);
			return $row['id'];
		}

		return 0;
	}

	public static function get($module_id = -1) {
		$config = new cata_config();

		if ($module_id > 0) {
			$db = dims::getInstance()->getDb();

			// on charge les parametres generaux, et ceux lie au domaine actuel
			$rs = $db->query('
				SELECT	*
				FROM	dims_param_default
				WHERE	id_module = '.$module_id.'
				AND	(id_domain = 0
						OR id_domain = '.self::getDomainId().' )');
			while ($row = $db->fetchrow($rs)) {
				$param = new cata_param();
				$param->openFromResultSet($row);
				$config->addParam($param);
			}
		}

		return $config;
	}

	private function addParam($param) {
		$this->params[$param->getName()] = $param;
	}

	private function getParam($param_name) {
		if (isset($this->params[$param_name])) {
			return $this->params[$param_name];
		}
		else {
			return null;
		}
	}

	private function createParam($name, $label, $default_value) {
		$param_type = new param_type();
		if (!$param_type->open($_SESSION['dims']['currentmodule']['id_module_type'], $name)) {
			$param_type->fields['id_module_type']   = $_SESSION['dims']['currentmodule']['id_module_type'];
			$param_type->fields['name']             = $name;
			$param_type->fields['default_value']    = $default_value;
			$param_type->fields['public']           = 0;
			$param_type->fields['description']      = null;
			$param_type->fields['label']            = $label;
			$param_type->save();
		}

		$param = new cata_param();
		$param->fields['id_module'] = $_SESSION['dims']['moduleid'];
		$param->fields['name'] = $name;
		$param->fields['value'] = $default_value;
		$param->fields['id_module_type'] = $_SESSION['dims']['currentmodule']['id_module_type'];
		$param->fields['id_domain'] = 0;
		$param->save();

		$this->addParam($param);
	}


	public function isSynchronized() {
		if ($this->getParam('cata_synchronized') == null) {
			$this->createParam('cata_synchronized', dims_constant::getVal('CATA_SYNCHRONIZED_CATALOG'), self::DEFAULT_SYNCHRONIZED);
		}
		return $this->getParam('cata_synchronized')->getValue() == 1;
	}

	public function modeB2C() {
		if ($this->getParam('cata_mode_B2C') == null) {
			$this->createParam('cata_mode_B2C', dims_constant::getVal('CATA_B2C_CATALOG'), self::DEFAULT_MODE_B2C);
		}
		return $this->getParam('cata_mode_B2C')->getValue() == 1;
	}

	public function visibleNotConnected() {
		if ($this->getParam('cata_visible_not_connected') == null) {
			$this->createParam('cata_visible_not_connected', dims_constant::getVal('CATA_VISIBLE_NOT_CONNECTED'), self::DEFAULT_VISIBLE_NOT_CONNCTED);
		}
		return $this->getParam('cata_visible_not_connected')->getValue() == 1;
	}

	public function activeMarques() {
		if ($this->getParam('cata_active_marques') == null) {
			$this->createParam('cata_active_marques', dims_constant::getVal('CATA_BRAND_MANAGEMENT'), self::DEFAULT_ACTIVE_MARQUES);
		}
		return $this->getParam('cata_active_marques')->getValue() == 1;
	}

	public function baseHT() {
		if ($this->getParam('cata_base_ttc') == null) {
			$this->createParam('cata_base_ttc', dims_constant::getVal('CATA_PRICES_DISPLAY_MODE'), self::DEFAULT_BASE_TTC);
		}
		return $this->getParam('cata_base_ttc')->getValue() == 0;
	}

	public function baseTTC() {
		if ($this->getParam('cata_base_ttc') == null) {
			$this->createParam('cata_base_ttc', dims_constant::getVal('CATA_PRICES_DISPLAY_MODE'), self::DEFAULT_BASE_TTC);
		}
		return $this->getParam('cata_base_ttc')->getValue() == 1;
	}

	public function permitHorscata() {
		if ($this->getParam('cata_permit_horscata') == null) {
			$this->createParam('cata_permit_horscata', dims_constant::getVal('CATA_ALLOW_ORDERS_OUTSIDE_CATALOG'), self::DEFAULT_PERMIT_HORS_CATA);
		}
		return $this->getParam('cata_permit_horscata')->getValue() == 1;
	}

	public function negativeStocks() {
		if ($this->getParam('cata_negative_stocks') == null) {
			$this->createParam('cata_negative_stocks', dims_constant::getVal('CATA_ACTIVATE_NEGATIVE_STOCK'), self::DEFAULT_NEGATIVE_STOCKS);
		}
		return $this->getParam('cata_negative_stocks')->getValue() == 1;
	}

	public function showStocks() {
		if ($this->getParam('cata_show_stocks') == null) {
			$this->createParam('cata_show_stocks', dims_constant::getVal('CATA_ACTIVATE_STOCK_QTY'), self::DEFAULT_SHOW_STOCKS);
		}
		return $this->getParam('cata_show_stocks')->getValue() == 1;
	}

	public function detailReliquats() {
		if ($this->getParam('cata_detail_reliquats') == null) {
			$this->createParam('cata_detail_reliquats', dims_constant::getVal('CATA_DETAIL_REMAININGS'), self::DEFAULT_DETAIL_RELIQUATS);
		}
		return $this->getParam('cata_detail_reliquats')->getValue() == 1;
	}

	public function alertStockMini() {
		if ($this->getParam('cata_alert_stock_mini') == null) {
			$this->createParam('cata_alert_stock_mini', dims_constant::getVal('CATA_ALERT_IF_MIN_STOCK_REACHED'), self::DEFAULT_ALERT_STOCK_MINI);
		}
		return $this->getParam('cata_alert_stock_mini')->getValue() == 1;
	}

	public function navStyleHorizontal() {
		if ($this->getParam('cata_nav_style') == null) {
			$this->createParam('cata_nav_style', dims_constant::getVal('CATA_FAMILYS_DISPLAY'), self::DEFAULT_NAV_STYLE);
		}
		return $this->getParam('cata_nav_style')->getValue() == 'finder';
	}

	public function navStyleVertical() {
		if ($this->getParam('cata_nav_style') == null) {
			$this->createParam('cata_nav_style', dims_constant::getVal('CATA_FAMILYS_DISPLAY'), self::DEFAULT_NAV_STYLE);
		}
		return $this->getParam('cata_nav_style')->getValue() == 'arbo';
	}

	public function getCartManagement() {
		if ($this->getParam('cart_management') == null) {
			$this->createParam('cart_management', dims_constant::getVal('_CATA_CART_MANAGEMENT'), self::DEFAULT_CART_MANAGEMENT);
		}
		return $this->getParam('cart_management')->getValue();
	}

	public function getDefaultShowFamilies() {
		if ($this->getParam('cata_default_show_families') == null) {
			$this->createParam('cata_default_show_families', dims_constant::getVal('_CATA_DEFAULT_SHOW_FAMILIES'), self::DEFAULT_CART_MANAGEMENT);
		}
		return $this->getParam('cata_default_show_families')->getValue();
	}

	public function getInfosPersosEditable() {
		if ($this->getParam('cata_infos_persos_editable') == null) {
			$this->createParam('cata_infos_persos_editable', dims_constant::getVal('_CATA_ALLOW_EDIT_PERSONAL_INFOS'), self::DEFAULT_CART_MANAGEMENT);
		}
		return $this->getParam('cata_infos_persos_editable')->getValue();
	}

	public function getFiltersView() {
		if ($this->getParam('cata_filters_view') == null) {
			$this->createParam('cata_filters_view', dims_constant::getVal('CATA_FILTERS_OPERATING_MODE'), self::DEFAULT_FILTERS_VIEW);
		}
		return $this->getParam('cata_filters_view')->getValue();
	}

	public function getCompanyName() {
		if ($this->getParam('cata_company_name') == null) {
			$this->createParam('cata_company_name', dims_constant::getVal('_DIMS_LABEL_ENT_NAME'), '');
		}
		return $this->getParam('cata_company_name')->getValue();
	}

	public function getCompanySiren() {
		if ($this->getParam('cata_company_siren') == null) {
			$this->createParam('cata_company_siren', dims_constant::getVal('CATA_COMPANY_SIREN'), '');
		}
		return $this->getParam('cata_company_siren')->getValue();
	}

	public function getCompanyNic() {
		if ($this->getParam('cata_company_nic') == null) {
			$this->createParam('cata_company_nic', dims_constant::getVal('CATA_COMPANY_NIC'), '');
		}
		return $this->getParam('cata_company_nic')->getValue();
	}

	public function getCompanyApe() {
		if ($this->getParam('cata_company_ape') == null) {
			$this->createParam('cata_company_ape', dims_constant::getVal('CATA_COMPANY_APE'), '');
		}
		return $this->getParam('cata_company_ape')->getValue();
	}

	public function getCompanyAddress1() {
		if ($this->getParam('cata_company_address1') == null) {
			$this->createParam('cata_company_address1', dims_constant::getVal('_DIMS_LABEL_ADDRESS'), '');
		}
		return $this->getParam('cata_company_address1')->getValue();
	}

	public function getCompanyAddress2() {
		if ($this->getParam('cata_company_address2') == null) {
			$this->createParam('cata_company_address2', dims_constant::getVal('_DIMS_LABEL_ADDRESS_2'), '');
		}
		return $this->getParam('cata_company_address2')->getValue();
	}

	public function getCompanyAddress3() {
		if ($this->getParam('cata_company_address3') == null) {
			$this->createParam('cata_company_address3', dims_constant::getVal('_DIMS_LABEL_ADDRESS_3'), '');
		}
		return $this->getParam('cata_company_address3')->getValue();
	}

	public function getCompanyCountry() {
		if ($this->getParam('cata_company_country') == null) {
			$this->createParam('cata_company_country', dims_constant::getVal('_DIMS_LABEL_COUNTRY'), '');
		}
		return $this->getParam('cata_company_country')->getValue();
	}

	public function getCompanyCity() {
		if ($this->getParam('cata_company_city') == null) {
			$this->createParam('cata_company_city', dims_constant::getVal('_DIMS_LABEL_CITY'), '');
		}
		return $this->getParam('cata_company_city')->getValue();
	}

	public function getCompanyPostalCode() {
		if ($this->getParam('cata_company_postal_code') == null) {
			$this->createParam('cata_company_postal_code', dims_constant::getVal('_DIMS_LABEL_CP'), '');
		}
		return $this->getParam('cata_company_postal_code')->getValue();
	}

	public function getCompanyLogo() {
		if ($this->getParam('cata_company_logo') == null) {
			$this->createParam('cata_company_logo', dims_constant::getVal('CATA_COMPANY_LOGO'), '');
		}
		return $this->getParam('cata_company_logo')->getValue();
	}

	public function geteditquotelines() {
		if ($this->getParam('cata_edit_quotelines') == null) {
			$this->createParam('cata_edit_quotelines', dims_constant::getVal('ALLOW_EDITING_OF_QUOTE_LINES'), '');
		}
		return $this->getParam('cata_edit_quotelines')->getValue();
	}

	public function getlabelpattern() {
		if ($this->getParam('cata_label_pattern') == null) {
			$this->createParam('cata_label_pattern', dims_constant::getVal('LABEL_PATTERN'), '');
		}
		return $this->getParam('cata_label_pattern')->getValue();
	}

	public function getfiscalyear() {
		if ($this->getParam('cata_fiscal_year') == null) {
			$this->createParam('cata_fiscal_year', dims_constant::getVal('FISCAL_YEAR'), '');
		}
		return $this->getParam('cata_fiscal_year')->getValue();
	}

	public function getDocumentsTemplate() {
		if ($this->getParam('cata_documents_template') == null) {
			$this->createParam('cata_documents_template', dims_constant::getVal('CATA_DOCUMENTS_TEMPLATE_FOR_PRINT'), '');
		}
		return $this->getParam('cata_documents_template')->getValue();
	}

}
