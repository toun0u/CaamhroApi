<?php
class opportunity_type extends dims_data_object {

	public function __construct() {
		parent::dims_data_object('dims_mod_opportunity_type');
	}

	public static function getAllTypes() {
		$db = dims::getInstance()->getDb();

		$a_types = array();
		$rs = $db->query('SELECT * FROM dims_mod_opportunity_type ORDER BY label');
		while ($row = $db->fetchrow($rs)) {
			$type = new opportunity_type();
			$type->openFromResultSet($row);
			$a_types[] = $type;
		}

		return $a_types;
	}
}
?>