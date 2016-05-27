<?php
class city extends dims_data_object {
	const TABLE_NAME = 'dims_city';
	const MY_GLOBALOBJECT_CODE = 450;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function delete(){
		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	public function getLabel(){
		return (isset($this->fields['label'])) ? $this->fields['label'] : '';
	}
	public function setLabel($label) {
		$this->fields['label'] = $label;
	}

	public function setIdCountry($id_country) {
		$this->fields['id_country'] = $id_country;
	}

	public function setCp($cp) {
		$this->fields['cp'] = $cp;
	}


	public function save(){
		$this->set('label',strtoupper(dims_convertaccents($this->get('label'))));
		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = $this->get('label');
	}

	public static function getByLabel($label, $id_country = 0) {
		$db = dims::getInstance()->getDb();

		$sql = 'SELECT * FROM `'.self::TABLE_NAME.'` WHERE label LIKE \''.addslashes($label).'\'';
		if ($id_country > 0) {
			$sql .= ' AND id_country = '.$id_country;
		}
		$sql .= ' LIMIT 0, 1';

		$rs = $db->query($sql);

		if ($db->numrows($rs)) {
			$row = $db->fetchrow($rs);
			$city = new city();
			$city->openFromResultSet($row);
			return $city;
		}
		else {
			return null;
		}
	}

	public static function searchStart($label,$idCountry){
		$db = dims::getInstance()->getDb();
		$params = array(
			':l'=>array('value'=>$label."%",'type'=>PDO::PARAM_STR),
			':i'=>array('value'=>$label."%",'type'=>PDO::PARAM_STR),
			':c'=>array('value'=>$label."%",'type'=>PDO::PARAM_STR),
			':ic'=>array('value'=>$idCountry,'type'=>PDO::PARAM_INT),
		);
		$sel = "SELECT 		*
				FROM 		".self::TABLE_NAME."
				WHERE 		(label LIKE :l
				OR 			insee LIKE :i
				OR 			cp LIKE :c)
				AND 		id_country = :ic
				ORDER BY 	label";
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$c = new city();
			$c->openFromResultSet($r);
			$lst[$c->get('id')] = $c;
		}
		return $lst;
	}
}
