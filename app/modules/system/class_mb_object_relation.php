<?php
require_once DIMS_APP_PATH."modules/system/class_mb_class.php";
class mb_object_relation extends dims_data_object {
	const TABLE_NAME = 'dims_mb_object_relation';

	const MB_RELATION_ID_TYPE		= 1;//pour les jointure qui porte sur un id_classique
	const MB_RELATION_GO_TYPE		= 2;

	const MB_RELATION_HAS_MANY				= 'has_many';
	const MB_RELATION_HAS_ONE				= 'has_one';
	const MB_RELATION_BELONGS_TO			= 'belongs_to';
	const MB_RELATION_HAS_MANY_THROUGH		= 'has_many_through';

	const MB_RELATION_NO_INDEX			= 0;
	const MB_RELATION_ON_ME_INDEX		= 1;
	const MB_RELATION_ON_REMOTE_INDEX	= 2;

	const MB_RELATION_INTO_SYNC_DATA 	= 1;//indique si la relation doit figurer dans les body XML de synchronisation des objets
	const MB_RELATION_NOT_IN_SYNC_DATA 	= 0;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}
	public function create($class_on, $tbln_on, $on, $class_to, $tbln_to, $to, $type_relation, $data_type = self::MB_RELATION_ID_TYPE, $extended_indexation = self::MB_RELATION_NO_INDEX, $sync_behavior=self::MB_RELATION_INTO_SYNC_DATA, $alias = null, $through = null){
		//récupération de l'id de la classe fournie en param
		$on_class = mb_class::getIdOf($class_on, $tbln_on);
		$to_class = mb_class::getIdOf($class_to, $tbln_to);

		if( $on_class > 0 && $to_class > 0){

			if( is_null($alias) ) $alias = $class_on;

			$this->init_description();
			$this->fields['id_class_on'] = $on_class;
			$this->fields['on'] = $on;

			$this->fields['id_class_to'] = $to_class;
			$this->fields['to'] = $to;

			$this->fields['type'] = $type_relation;
			$this->fields['data_type'] = $data_type;

			$this->fields['extended_indexation'] = $extended_indexation;
			$this->fields['sync_behavior'] = $sync_behavior;
			$this->fields['alias'] = $alias;
			$this->fields['through'] = $through;

			return $this->save();
		}
		else return null;
	}

	public static function init_all_relations(){
		$db = dims::getInstance()->getDb();
		$db->query('TRUNCATE '.self::TABLE_NAME);
	}

	public static function getTableRelations($classname_from, $tablename_to, $type=null){
		//on a besoin de connaitre toutes les classes qui ont une jointure sur la tablename on
		$dims = dims::getInstance();
		$fields = $dims->getMbClasse($classname_from);
		$lst = array();
		if( ! empty($fields) ){
			$class_ids = mb_class::getAllClassesForTable($tablename_to);
			if (count($class_ids) > 0){
				$db = dims::getInstance()->getDb();
				$where = '';
				if( !is_null($type)){
					$where = " AND type=:type";
					$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
				}
				$params[':idclass'] = array('type' => PDO::PARAM_INT, 'value' => $fields['id']);
				$res = $db->query("SELECT * FROM ".self::TABLE_NAME." WHERE id_class_on = :idclass AND id_class_to IN (".$db->getParamsFromArray($class_ids, 'idclassto', $params).") ".$where, $params);
				while($fields = $db->fetchrow($res)){
					$rel = new mb_object_relation();
					$rel->openFromResultSet($fields);
					$lst[] = $rel;
				}
			}
		}
		return $lst;
	}

	public function isExtendedRelation(){
		return $this->fields['extended_indexation'];
	}

	public function getColumnOn(){
		return isset($this->fields['on']) ? $this->fields['on'] : '';
	}

	public function getColumnTo(){
		return isset($this->fields['to']) ? $this->fields['to'] : '';
	}
}
?>
