<?php
class enum extends dims_data_object {
	const TABLE_NAME = "dims_mod_business_enum";

	function __construct() {
	parent::dims_data_object(self::TABLE_NAME);
	}

	public function getLibelle() {
	return $this->getAttribut("libelle", self::TYPE_ATTRIBUT_STRING);
	}

	/**
	 *
	 * @param type $type
	 * @param type $lang
	 * @return enum (array)
	 */
	public static function getEnumByType($type, $lang = -1) {
	$liste_enum = array();

	if(!empty($type)){
		$db = dims::getInstance()->getDb();

		if($lang == -1){
		$lang = $_SESSION['dims']['currentlang'];
		}
		$sql = "SELECT * FROM ".self::TABLE_NAME."
		WHERE type = :type
		AND lang = :lang ";

		$res = $db->query($sql, array(
			':type' => array('type' => PDO::PARAM_STR, 'value' => $type),
			':lang' => array('type' => PDO::PARAM_INT, 'value' => $lang),
		));
		while ($row = $db->fetchrow($res)) {
		$enum = new enum();
		$enum->openWithFields($row, true);

		$liste_enum[] = $enum;
		}
	}
	return $liste_enum ;
	}

}
