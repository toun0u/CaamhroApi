<?php
require_once DIMS_APP_PATH.'modules/system/class_city.php';

class country extends dims_data_object {
	const TABLE_NAME = 'dims_country';
	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function getAllCity($limit = 0) {
		return city::find_by(array('id_country' => $this->get('id')), ' ORDER BY label ', $limit);
	}

	public function getcitieslist($limit = 0) {
		$citieslist = array();

		$cities = city::pick(array('id', 'label'))->conditions(array('id_country' => $this->getId()))->limit($limit)->run();

		foreach($cities as $city) {
			$citieslist[$city['id']] = $city['label'];
		}

		return $citieslist;
	}

	public function getAllCitiesByLabel($label, $limit = 0){
		return city::conditions(array(
			'id_country'=>$this->get('id'),
			'label' => array("op" => 'LIKE', 'value' => $label.'%')
		))->order('label')->limit($limit)->run();
	}

	public function getFlag(){
		if (file_exists(realpath('.')."./common/img/flag/flag_".strtolower($this->fields['iso']).".png"))
			return "./common/img/flag/flag_".strtolower($this->fields['iso']).".png";
		else
			return "";
	}

	public function getLabel($lang = dims_const::_SYSTEM_LANG_EN){
		if($lang == dims_const::_SYSTEM_LANG_FR)
			return $this->fields['fr'];//standard / en anglais
		else return isset($this->fields['printable_name']) ? $this->fields['printable_name'] : '';
	}

	/* *** Statics functions *** */
	public static function getAllCountries() {
		$db = dims::getInstance()->getDb();

		$a_countries = array();
		$rs = $db->query('SELECT * FROM dims_country ORDER BY name');
		while ($row = $db->fetchrow($rs)) {
			$country = new country();
			$country->openFromResultSet($row);
			$a_countries[$country->getId()] = $country;
		}

		return $a_countries;
	}

	// label peux être le nom complet ou le code iso
	public static function getCountryFromLabel($label, $open = false){
		$db = dims::getInstance()->getDb();
		$label = str_replace(array("L'","'",".","`",'"',"THE","LE","LES"),array(""," "," "," "," ","","",""),strtoupper($label));
		$sel = "SELECT	DISTINCT *
			FROM	dims_country
			WHERE	name LIKE :label
			OR	printable_name LIKE :label
			OR	iso3 LIKE :label
			OR	fr LIKE :label";
		$res = $db->query($sel, array(
			':label' => array('type' => PDO::PARAM_STR, 'value' => $label),
		));
		if ($r = $db->fetchrow($res))
			if ($open){
			$country = new country();
			$country->openWithFields($r);
			return $country;
			}else
			return $r['id'];
		else
			return 0;
	}

	 #retrouve un pays selon son code ISO
	public static function findByISO($iso){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT * FROM dims_country WHERE iso='".$iso."' LIMIT 0,1");
		if($db->numrows($res)){
			$fields = $db->fetchrow($res);
			$c = new country();
			$c->openFromResultSet($fields);
			return $c;
		}
		return null;
	}
}
