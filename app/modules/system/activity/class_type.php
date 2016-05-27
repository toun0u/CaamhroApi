<?php
class activity_type extends dims_data_object {
	const TABLE_NAME = 'dims_mod_activity_type';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public static function getAllTypes() {
		$db = dims::getInstance()->getDb();

		$a_types = array();
		$rs = $db->query('SELECT * FROM '.self::TABLE_NAME.' ORDER BY label');
		while ($row = $db->fetchrow($rs)) {
			$type = new activity_type();
			$type->openFromResultSet($row);
			$a_types[] = $type;
		}

		return $a_types;
	}

}
