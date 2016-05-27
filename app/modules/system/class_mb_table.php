<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class mb_table extends dims_data_object {
	/**
	* Class constructor
	*
	* @access public
	**/

	const TABLE_NAME = 'dims_mb_table';
	function __construct() {
		parent::dims_data_object('dims_mb_table','id');
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		$res=$db->query("DELETE FROM dims_mb_field WHERE tablename = :tablename AND id_module_type = :idmoduletype", array(
			':tablename' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['name']),
			':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['id_module_type']),
		));
		$res=$db->query("DELETE FROM dims_mb_relation WHERE (tablesrc = :tablename OR tabledest = :tablename) AND id_module_type = :idmoduletype", array(
			':tablename' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['name']),
			':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['id_module_type']),
		));
		$res=$db->query("DELETE FROM dims_mb_schema WHERE (tablesrc = :tablename OR tabledest = :tablename) AND id_module_type = :idmoduletype", array(
			':tablename' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['name']),
			':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['id_module_type']),
		));
		parent::delete();
	}

	public static function getTableID($tablename, $allow_creation = true){
		$db = dims::getInstance()->getDb();
		$id = 0;
		if( !empty($tablename)){
			$dims = dims::getInstance();
			$fields = $dims->getMbTable($tablename);
			if(! empty($fields) ){
				$id = $fields['id'];
			}
			else{
				$res = $db->query("SELECT id FROM ".self::TABLE_NAME." WHERE name = :tablename", array(
					':tablename' => array('type' => PDO::PARAM_STR, 'value' => $tablename),
				));
				if($db->numrows($res)){
					$fields = $db->fetchrow($res);
					$dims->addMBTable($fields); //au passage on la balance en session pour pas à avoir à refaire la requête la fois d'après
					$id = $fields['id'];
				}
				else if($allow_creation){
					$mbt = new mb_table();
					$mbt->init_description();
					$mbt->fields['name'] = $tablename;
					$mbt->fields['label'] = $tablename;
					$mbt->fields['visible'] = 1;
					$mbt->fields['sql'] = '';
					$mbt->save();
					$dims->addMBTable($mbt->fields); //au passage on la balance en session pour pas à avoir à refaire la requête la fois d'après
					$id = $mbt->getId();
				}
			}
		}
		return $id;
	}

	public static function create($tablename){
		$dims = dims::getInstance();
		$mbt = mb_table::build(array(
			'name' 		=> $tablename,
			'label' 	=> $tablename,
			'visible' 	=> 1,
			'sql' 		=> ''
			));
		$mbt->save();
		$dims->addMBTable($mbt->fields); //au passage on la balance en session pour pas à avoir à refaire la requête la fois d'après
		return $mbt->fields;
	}
}
?>
