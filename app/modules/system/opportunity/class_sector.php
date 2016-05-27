<?php
class opportunity_sector extends dims_data_object {

	public function __construct() {
		parent::dims_data_object('dims_mod_opportunity_sector');
	}

	public static function getAllSectors() {
		$db = dims::getInstance()->getDb();

		$a_sectors = array();
		$rs = $db->query('SELECT * FROM dims_mod_opportunity_sector ORDER BY label');
		while ($row = $db->fetchrow($rs)) {
			$sector = new opportunity_sector();
			$sector->openFromResultSet($row);
			$a_sectors[] = $sector;
		}

		return $a_sectors;
	}

}
