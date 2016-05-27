<?php

class link_type extends dims_data_object {
	const TABLE_NAME 			= 'dims_mod_cata_article_link_type';

	const TYPE_COMPLEMENTAIRE 	= 1;
	const TYPE_SUBSTITUTION 	= 2;

	public function link_type() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function create($label, $code = ''){
		$homonyme = link_type::findByCode($code);
		if(is_null($homonyme)){
			$this->init_description(true);
			$this->setugm();
			$this->setLabel($label);
			$this->setCode($code);
			return $this->save();
		}
		else return false;
	}

	public function setLabel($label){
		$this->fields['label'] = $label;
	}

	public function getLabel(){
		return $this->fields['label'];
	}

	public function setCode($code){
		$this->fields['code'] = $code;
	}

	public function getCode(){
		return $this->fields['code'];
	}

	public static function findByCode($code){
		$db = dims::getInstance()->getDb();
		$res = $db->query('SELECT * FROM '.self::TABLE_NAME.' WHERE code = \''.$code.'\'');
		$type = null;
		if($db->numrows($res)){
			$type = new link_type();
			$type->openFromResultSet($db->fetchrow($res));
		}
		return $type;
	}

	public static function getLabels(){
		$db = dims::getInstance()->getDb();
		$res = $db->query('SELECT id, label FROM '.self::TABLE_NAME);
		$lst = array();
		while($fields = $db->fetchrow($res)){
			$lst[$fields['id']] = $fields['label'];
		}
		return $lst;
	}
}
