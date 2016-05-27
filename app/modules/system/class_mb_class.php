<?php
class mb_class extends dims_data_object {
	/**
	* Class constructor
	*
	* @access public
	**/

	const TABLE_NAME = 'dims_mb_classes';
	const MY_CLASSNAME = 'mb_class'; //particularité utilsé dans getIdOf pour éviter de boucler

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public static function create($classname, $id_table){
		$class = new mb_class();
		$class->fields['classname'] = $classname;
		$class->fields['id_table'] = $id_table;
		$class->save();
		return $class;
	}

	public function openWithObject($obj, $allow_creation = true){
		$dims = dims::getInstance();
		$fields = $dims->getMbClasse(get_class($obj));
		if(! empty($fields) ){
			$this->openFromResultSet($fields);
		}
		else{
			$res = $this->db->query("SELECT * FROM ".self::TABLE_NAME." WHERE classname= :classname ", array(
				':classname' => array('type' => PDO::PARAM_STR, 'value' => get_class($obj)),
			));
			if($this->db->numrows() > 0){
				$fields = $this->db->fetchrow($res);
				$this->openFromResultSet($fields);
				$dims->addMBClasse($this->fields);//au passage on peut bien l'envoyer en session pour la fois d'après
			}
			else if($allow_creation){ //il faut le créer
				//il faut trouver l'id tablename
				$id_tablename = mb_table::getTableID($obj->getTablename());
				$new_classe = self::create(get_class($obj), $id_tablename);
				$dims->addMBClasse($new_classe->fields);
				return $new_classe;
			}
		}
		return $this;
	}

	public static function getIdOf($classname, $tablename, $allow_creation = true){
		$id = 0;
		$dims = dims::getInstance();
		$db = dims::getInstance()->getDb();
		$fields = $dims->getMbClasse($classname);
		if(! empty($fields) ){
			$id = $fields['id'];
		}
		else{
			$res = $db->query("SELECT * FROM ".self::TABLE_NAME." WHERE classname= :classname", array(
				':classname' => array('type' => PDO::PARAM_INT, 'value' => $classname),
			));

			if($db->numrows() > 0){
				$fields = $db->fetchrow($res);
				$dims->addMBClasse($fields);//au passage on envoie les champs dans la session pour la fois d'après
				$id = $fields['id'];
			}
			else if($allow_creation){ //il faut le créer
				//il faut trouver l'id tablename
				include_once DIMS_APP_PATH.'modules/system/class_mb_table.php';
				$id_tablename = mb_table::getTableID($tablename);
				$new_classe =  self::create($classname, $id_tablename);
				$dims->addMBClasse($new_classe->fields);
				$id = $new_classe->getId();
			}
		}
		return $id;
	}

	public static function getAllClassesForTable($tablename){
		$db = dims::getInstance()->getDb();
		$dims = dims::getInstance();
		$fields = $dims->getMbTable($tablename);
		$lst = array();
		if( ! empty($fields) ){
			$res = $db->query("SELECT id FROM ".self::TABLE_NAME." WHERE id_table = :idnewsletter ", array(
				':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $fields['id']),
			));
			if($db->numrows($res)){
				$lst = $db->getarray($res);
			}
		}
		return $lst;
	}
}
?>
