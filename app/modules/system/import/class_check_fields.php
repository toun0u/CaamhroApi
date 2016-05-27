<?php
class import_check_fields extends dims_data_object{
	const TABLE_NAME = 'dims_import_check_fields';

	public function __construct(){
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public static function create($name_fields,$id_mt, $type_obj){
		$db = dims::getInstance()->db;
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	name_fields LIKE :namefields
				AND		id_mt_field = :idmt
				AND		type_obj = :typeobj ";
		$res = $db->query($sel, array(
			':namefields'	=> $name_fields,
			':idmt'			=> $id_mt,
			':typeobj'		=> $type_obj
		));
		$check = new import_check_fields();
		if ($r = $db->fetchrow($res)){
			$check->openFromResultSet($r);
		}else{
			$check->init_description();
			$check->fields['name_fields'] = strtolower($name_fields);
			$check->fields['id_mt_field'] = $id_mt;
			$check->fields['type_obj'] = $type_obj;
			$check->fields['nb_used'] = 0;
			$check->setugm();
		}
		return $check;
	}

	public static function getListForType($type){
		$db = dims::getInstance()->db;
		$sel = "SELECT		*
				FROM		".self::TABLE_NAME."
				WHERE		type_obj = :type
				ORDER BY	nb_used";
		$res = $db->query($sel, array(
			':type'	=> $type
		));
		$lst = array();
		while ($r = $db->fetchrow($res)){
			$lst[$r['name_fields']] = $r['id_mt_field'];
		}
		return $lst;
	}
}