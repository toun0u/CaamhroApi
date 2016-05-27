<?php

class cata_reseller extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_resellers';

	private $logo_file = null;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public static function allFields() {
		$db = dims::getInstance()->getDb();
		$a_resellers = array();
		$rs = $db->query('SELECT * FROM '.self::TABLE_NAME);
		while($row = $db->fetchrow($rs)) {
			$a_resellers[] = $row;
		}
		return $a_resellers;
	}

	public function delete() {
		// Suppression du logo attaché
		$logo = $this->getLogo();
		if (!is_null($logo)) {
			$logo->delete();
		}

		parent::delete();
	}


	/**
	* Getters
	*/
	public function getName() {
		return $this->fields['name'];
	}

	public function getAddress1() {
		return $this->fields['address1'];
	}

	public function getAddress2() {
		return $this->fields['address2'];
	}

	public function getAddress3() {
		return $this->fields['address3'];
	}

	public function getPostalCode() {
		return $this->fields['postal_code'];
	}

	public function getCity() {
		return $this->fields['city'];
	}

	public function getCountryId() {
		return $this->fields['id_country'];
	}

	public function getCountryLabel() {
		$country = new country();
		$country->open($this->fields['id_country']);
		if ($country->isNew()) {
			return '';
		}
		else {
			return $country->get('fr');
		}
	}

	public function getWebSite() {
		return $this->fields['website'];
	}

	public function getEmail() {
		return $this->fields['email'];
	}

	public function getPhone() {
		return $this->fields['tel'];
	}

	public function getFax() {
		return $this->fields['fax'];
	}

	public function getLogo() {
		if (is_null($this->logo_file)) {
			if ($this->fields['id_logo'] > 0) {
				require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
				$logo_file = new docfile();
				$logo_file->open($this->fields['id_logo']);
				$this->logo_file = $logo_file;
				return $this->logo_file;
			}
			else {
				return null;
			}
		}
		else {
			return $this->logo_file;
		}
	}

	public function getLogoWebPath() {
		if (is_null($this->logo_file)) {
			$this->getLogo();
		}
		if (!is_null($this->logo_file)) {
			return $this->logo_file->getwebpath();
		}
		else {
			return '';
		}
	}


	/**
	* Setters
	*/
	public function setName($name) {
		$this->fields['name'] = $name;
	}

	public function setAddress1($address) {
		$this->fields['address1'] = $address;
	}

	public function setAddress2($address) {
		$this->fields['address2'] = $address;
	}

	public function setAddress3($address) {
		$this->fields['address3'] = $address;
	}

	public function setPostalCode($postal_code) {
		$this->fields['postal_code'] = $postal_code;
	}

	public function setCity($city) {
		$this->fields['city'] = $city;
	}

	public function setCountry($id_country) {
		$this->fields['id_country'] = $id_country;
	}

	public function setLogo($logo) {
		$this->fields['id_logo'] = $logo->getId();
	}

	public function setWebSite($website) {
		$this->fields['website'] = $website;
	}

	public function setEmail($email) {
		$this->fields['email'] = $email;
	}

	public function setPhone($tel) {
		$this->fields['tel'] = $tel;
	}

	public function setFax($fax) {
		$this->fields['fax'] = $fax;
	}

}
