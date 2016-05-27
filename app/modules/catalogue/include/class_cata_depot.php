<?php
class cata_depot extends pagination {

	const TABLE_NAME = 'dims_mod_cata_depot';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function save() {
		if (empty($this->fields['depot'])) $this->setNumDepot();
		parent::save();
	}

	public function getCity() {
		include_once DIMS_APP_PATH."modules/system/class_city.php";
		$elem = new city();

		if ($this->fields['id_city'] != '' && $this->fields['id_city'] > 0) {
			$elem->open($this->fields['id_city']);
		} else {
			$elem->init_description();
		}

		return $elem;
	}

	public function getCountry() {
		include_once DIMS_APP_PATH."modules/system/class_country.php";
		$elem = new country();

		if ($this->fields['id_country'] != '' && $this->fields['id_country'] > 0) {
			$elem->open($this->fields['id_country']);
		} else {
			$elem->init_description();
		}

		return $elem;
	}

	public function getCountryLabel() {
		$country = $this->getCountry();
		return $country->get('printable_name');
	}

	public function setClient($client) {
		$this->fields['client'] = $client;
	}

	public function setErpId($erp_id) {
		$this->fields['erp_id'] = $erp_id;
	}

	public function setNumDepot() {
		if ($this->fields['client'] != '') {
			$rs = $this->db->query('SELECT MAX(depot) AS m FROM `'.self::TABLE_NAME.'` WHERE client = \''.$this->fields['client'].'\'');
			$row = $this->db->fetchrow($rs);
			if ($row['m'] === null) {
				$this->fields['depot'] = 0;
			} else {
				$this->fields['depot'] = $row['m'] + 1;
			}
		} else {
			$this->fields['depot'] = 0;
		}
	}

	public function setNomLivr($nomlivr) {
		$this->fields['nomlivr'] = $nomlivr;
	}

	public function setAddress1($adr1) {
		$this->fields['adr1'] = $adr1;
	}

	public function setAddress2($adr2) {
		$this->fields['adr2'] = $adr2;
	}

	public function setPostalCode($cp) {
		$this->fields['cp'] = $cp;
	}

	public function setCity($ville) {
		$this->fields['ville'] = $ville;

		// Mise à jour de l'id_city
		if (!empty($this->fields['id_country'])) {
			$city = city::getByLabel($ville, $this->fields['id_country']);
			if (is_null($city)) {
				$city = new city();
				$city->init_description(true);
				$city->setIdCountry($this->fields['id_country']);
				$city->setLabel($this->fields['ville']);
				$city->setCp($this->fields['cp']);
				$city->setugm();
				$city->save();
			}
			$this->fields['id_city'] = $city->get('id');
		}
	}

	public function setCountry($id_country) {
		$this->fields['id_country'] = $id_country;
	}

	public static function updateAllDepotsNumbers() {
		$db = dims::getInstance()->getDb();

		$old_client = '';
		$rs = $db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE `depot` = 0 ORDER BY `client`, `depot`, `erp_id`');
		while ($row = $db->fetchrow($rs)) {
			if ($row['client'] != $old_client) {
				$old_client = $row['client'];
				$numdepot = 1;
			}

			$db->query('UPDATE `'.self::TABLE_NAME.'` SET `depot` = '.$numdepot.' WHERE `id` = '.$row['id'].' LIMIT 1');
			$numdepot++;
		}
	}

	public static function updateAllDepotsCitiesIds() {
		$db = dims::getInstance()->getDb();

		$rs = $db->query('SELECT DISTINCT(`ville`), `id_country` FROM `'.self::TABLE_NAME.'` WHERE `id_city` = 0');
		while ($row = $db->fetchrow($rs)) {

			if (trim($row['ville']) != '') {
				$ville = city::getByLabel($row['ville'], $row['id_country']);

				// On crée la ville
				if (is_null($ville)) {
					$ville = new city();
					$ville->init_description();
					$ville->set('id_country', 	$row['id_country']);
					$ville->set('label', 		$row['ville']);
					$ville->set('id_user', 		1);
					$ville->set('id_module', 	1);
					$ville->set('id_workspace', 1);
					$ville->save();
				}

				// On met à jour l'id_city des villes existantes
				$db->query('UPDATE `'.self::TABLE_NAME.'` SET `id_city` = :id_city WHERE `ville` = :ville AND `id_city` = 0',
					array(
						':id_city' 	=> array('type' => PDO::PARAM_INT, 'value' => $ville->get('id')),
						':ville' 	=> array('type' => PDO::PARAM_STR, 'value' => $ville->get('label'))
					)
				);
			}
		}
	}
}
