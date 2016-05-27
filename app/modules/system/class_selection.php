<?php
require_once DIMS_APP_PATH."modules/system/class_selection_categ.php";
class selection extends DIMS_DATA_OBJECT{
	const TABLE_NAME = "dims_selection";

	function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public static function openByLink($id_categ, $idgo_elem) {
		$sql = 'SELECT * FROM '.self::TABLE_NAME.' WHERE id_categ = :idcateg AND id_globalobject = :idglobalobject';

		$db = dims::getInstance()->getDb();

		$res = $db->query($sql, array(
			':idcateg' => array('type' => PDO::PARAM_INT, 'value' => $id_categ),
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $idgo_elem),
		));

		$selection = new self();
		if($db->numrows($res) > 0) {
			$data = $db->fetchrow($res);
			$selection->openFromResultSet($data);
		}
		else {
			$go = new dims_globalobject();
			$go->open($idgo_elem);

			$selection->setugm();
			$selection->fields['timestp'] = dims_createtimestamp();
			$selection->fields['id_categ'] = $id_categ;
			$selection->fields['id_globalobject'] = $idgo_elem;
			$selection->fields['type_object'] = $go->fields['id_object'];
		}

		return $selection;
	}
}
?>
