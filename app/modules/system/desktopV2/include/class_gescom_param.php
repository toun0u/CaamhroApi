<?php
class class_gescom_param extends dims_data_object {

	const TABLE_NAME = 'dims_mod_business_params';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'param', 'id_workspace');
	}

	public static function getAllParams() {
		$db = dims::getInstance()->getDb();

		$params = array();
		$rs = $db->query('SELECT * FROM '.self::TABLE_NAME.' WHERE id_workspace = :idworkspace ', array(
			':idworkspace' => $_SESSION['dims']['workspaceid']
		));
		while ($row = $db->fetchrow($rs)) {
			$params[$row['param']] = $row['value'];
		}
		return $params;
	}

}
