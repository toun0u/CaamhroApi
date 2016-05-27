<?php

class exportparams extends dims_data_object {
	const TABLE_NAME = 'dims_mod_cata_export_params';

	const COLSEP_TAB = 1;
	const COLSEP_OTHER = 2;

	const ENDLINE_SEP_NO = 1;
	const ENDLINE_SEP_YES = 2;

	private static $fieldslist = array();

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function setheaderfields($headerfields) {
		$this->fields['selectedheaderfields'] = json_encode((array)$headerfields);
	}

	public function setrowfields($rowfields) {
		$this->fields['selectedrowfields'] = json_encode((array)$rowfields);
	}

	public function getheaderfields() {
		return (array)json_decode($this->fields['selectedheaderfields']);
	}

	public function getrowfields() {
		return (array)json_decode($this->fields['selectedrowfields']);
	}

	public static function openbymodule($id_module) {
		$params = new self();
		$db = dims::getInstance()->getDb();

		$sql = 'SELECT * FROM '.self::TABLE_NAME.' WHERE id_module = '.$id_module;

		$res = $db->query($sql);

		if($db->numrows($res) > 0) {
			$paramdata = $db->fetchrow($res);

			$params->openFromResultSet($paramdata);
		} else {
			$params->init_description();
			$params->setugm();
		}

		return $params;
	}

	/* Fields list management */
	public static function getfieldsnamelist($fields) {
		$fieldsnamelist = array();

		foreach($fields as $key => $field) {
			if(!empty($field)) {
				$fieldsnamelist[$key] = $field['fieldname'];
			}
		}

		return $fieldsnamelist;
	}

	public static function getfieldslabellist($fields) {
		$fieldsnamelist = array();

		foreach($fields as $key => $field) {
			if(!empty($field)) {
				$fieldsnamelist[$key] = $field['fieldlabel'];
			}
		}

		return $fieldsnamelist;
	}

	public static function filterheaderfields($fields) {
		return array_filter($fields, function ($field) {
			return (!empty($field) && $field['header'] == true);
		});
	}

	public static function filterrowfields($fields) {
		return array_filter($fields, function ($field) {
			return (empty($field) || $field['header'] == false);
		});
	}

	public static function filterfields($fields, $filter) {
		$filteredfields = array();
		foreach($filter as $key) {
			if(isset($fields[$key])) {
				$filteredfields[$key] = $fields[$key];
			}
		}
		return $filteredfields;
	}

	public static function excludefields($fields, $excludedfields) {
		return array_diff_key($fields, array_flip($excludedfields));
	}

	public static function getfieldslist() {
		if(empty(self::$fieldslist)) {
			/* Fieldlist is initialized on first access because of function call fieldlabel value. */
			self::$fieldslist = array(
				array(), // avoid zero index
				// dims_mod_cata_cde
				array('header'	=>	true,	'fieldname'	=>	'id_cde',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_client',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'code_client',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'numcde',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'date_cree',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'date_validation',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'etat',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'expediee',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'traitement',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'libelle',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'commentaire',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'hors_cata',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_regroupement',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_budget',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'adrfact',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_nom',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_adr1',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_adr2',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_adr3',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_cp',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_ville',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_id_pays',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_pays',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_tel1',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_tel2',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_fax',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_port',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_email',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_liv_nom',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_liv_adr1',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_liv_adr2',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_liv_adr3',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_liv_cp',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_liv_ville',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_liv_id_pays',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cli_liv_pays',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'port',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'port_tx_tva',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'total_ht',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'total_tva',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'total_ttc',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'mode_paiement',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'date_gen',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'mail',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'user_name',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'classe',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'validation_user_id',		'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'validation_user_name',		'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'refus_user_id',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'refus_user_name',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'refus_motif',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'mode_expedition',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'num_colis',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'sans_tva',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'teachername',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'classroom',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_code',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_error',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_transaction_id',		'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_payment_means',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_payment_date',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_payment_time',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_response_code',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_payment_certificate',	'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_authorisation_id',		'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_bank_response_code',	'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_complementary_code',	'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_complementary_info',	'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cb_customer_ip_address',	'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'retour_cb',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_globalobject',			'fieldlabel'	=>	dims_constant::getVal('')),

				// dims_mod_cata_client
				array('header'	=>	true,	'fieldname'	=>	'id_client',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'code_client',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'tiers_id',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'dims_group',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'dims_user',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'date_cree',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'newsletter',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'nom',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'login',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'password',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'use_add_client',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'adr1',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'adr2',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'adr3',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cp',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'ville',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_pays',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'tel1',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'tel2',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'fax',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'port',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'email',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'liv_nom',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'liv_adr1',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'liv_adr2',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'liv_adr3',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'liv_cp',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'liv_ville',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'liv_id_pays',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'adr_liv',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'observation',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_workspace',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_module',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_user',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'id_globalobject',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'vcd',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'civilite',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'motdir',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'incoterm',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'atcext',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'secteur',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'csp',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'codrgt',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'coltva',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'escompte',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'codmes',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'transporteur',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'librcha1',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'librcha2',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'code_tarif_1',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'code_tarif_2',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'minimum_cde',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'limite_budget',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'budget_non_bloquant',		'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'budget_reconduction',		'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'budget_date_reconduction',	'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'cata_restreint',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'afficher_prix',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'change_livraison',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'hors_catalogue',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'imprimer_selection',		'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'utiliser_selection',		'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'statistiques',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'export_catalogue',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'ttc',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'ref_cde_oblig',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'retours',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'bloque',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'demo',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'type',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	true,	'fieldname'	=>	'commentaire',				'fieldlabel'	=>	dims_constant::getVal('')),

				// dims_mod_cata_cde_ligne
				array('header'	=>	false,	'fieldname'	=>	'id_cde_ligne',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'id_article',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'ref',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'label',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'label_default',			'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'qte',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'qte_liv',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'qte_rel',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'poids',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'pu_ht',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'pu_remise',				'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'tx_tva',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'pu_ttc',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'coef',						'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'remise',					'fieldlabel'	=>	dims_constant::getVal('')),
				array('header'	=>	false,	'fieldname'	=>	'ctva',						'fieldlabel'	=>	dims_constant::getVal('')),
			);
		}

		return self::$fieldslist;
	}
}
