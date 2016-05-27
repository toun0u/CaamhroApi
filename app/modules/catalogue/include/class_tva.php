<?php
include_once DIMS_APP_PATH."modules/system/class_country.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";

class tva extends dims_data_object {
	const TABLE_NAME = 'dims_mod_cata_tva';

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id_tva', 'id_pays');
	}

	public function create($code, $country, $taux){
		$this->init_description(true);
		$this->setugm();
		$this->setCode($code);
		$this->setCountry($country);
		$this->setTaux($taux);
	}

	public function delete(){
		$codes = tva::findByCode($this->getCode());
		if(count($codes) == 1){#c'est que c'est le dernier de ce code, il faut mettre à jour les articles
			$this->db->query("UPDATE ".article::TABLE_NAME." SET ctva = '' WHERE ctva=".$this->getCode());
		}
		parent::delete();
	}

	public function setCode($val){
		$this->fields['id_tva'] = $val;
	}
	public function setCountry($val){
		$this->fields['id_pays'] = $val;
	}
	public function setTaux($val){
		$this->fields['tx_tva'] = $val;
	}

	public function getCode(){
		return $this->fields['id_tva'];
	}
	public function getCountry(){
		return $this->fields['id_pays'];
	}
	public function getTaux() {
		if (!empty($this->fields['tx_tva'])) {
			return $this->fields['tx_tva'];
		}
		return 0;
	}

	public static function all($conditions = '',$params=array()){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT t.*, c.printable_name, c.fr
						   FROM ".self::TABLE_NAME." t
						   INNER JOIN dims_country c ON t.id_pays = c.id
						   ORDER BY t.id_tva ASC, c.printable_name ASC, t.tx_tva ASC");

		$split = $db->split_resultset($res);
		$taux = array();
		foreach($split as $fields){
			$tva = new tva();
			$tva->openFromResultSet($fields['t']);
			$tva->setLightAttribute('en', $fields['c']['printable_name']);
			$tva->setLightAttribute('fr', $fields['c']['fr']);
			$taux[] = $tva;
		}

		return $taux;
	}

	public static function getDistinctCodes(){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT DISTINCT id_tva FROM ".self::TABLE_NAME." ORDER BY id_tva ASC");

		$taux = array();
		while($fields = $db->fetchrow($res)){
			$taux[$fields['id_tva']] = $fields['id_tva'];
		}

		return $taux;
	}

	public static function findByCode($code){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT * FROM ".self::TABLE_NAME." WHERE id_tva = ".$code);
		$codes = array();
		while($fields = $db->fetchrow($res)){
			$t = new tva();
			$t->openFromResultSet($fields);
			$codes[] = $t;
		}
		return $codes;
	}
}
