<?php

include_once DIMS_APP_PATH.'modules/catalogue/include/class_slidart_element.php';

class slidart extends dims_data_object {
	const TABLE_NAME = 'dims_mod_cata_wce_slidart';
	/**
	* Class constructor
	*
	* @access public
	**/
	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function getElements() {
		$elemList = array();

		$db = dims::getInstance()->getDb();
		$sql = 'SELECT      *
				FROM        '.slidart_element::TABLE_NAME.'
				WHERE       id_slidart = '.$this->fields['id'].'
				ORDER BY    position ASC';

		$res = $db->query($sql);

		while ($fields = $db->fetchrow($res)) {
			$element = new slidart_element();
			$element->openFromResultSet($fields);

			$elemList[] = $element;
		}

		return $elemList;
	}

	public function delete() {

		foreach($this->getElements() as $element) {
			$element->delete();
		}

		return parent::delete();
	}

	public static function getAll(){
		$lst = array();
		$db = dims::getInstance()->getDb();

		$sel = "SELECT      c.*, COUNT(e.id) AS nb_elem
				FROM        ".self::TABLE_NAME." c
				LEFT JOIN   ".slidart_element::TABLE_NAME." e
				ON          e.id_slidart = c.id
				WHERE       c.id_module = ".$_SESSION['dims']['moduleid']."
				GROUP BY    c.id";
		$res = $db->query($sel);
		foreach ($db->split_resultset($res) as $r){
			$elem = new slidart();
			$elem->openFromResultSet($r['c']);
			$elem->setLightAttribute('nb_elem',$r['unknown_table']['nb_elem']);
			$lst[] = $elem;
		}
		return $lst;
	}
}
