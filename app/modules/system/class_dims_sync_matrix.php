<?php
class sync_matrix extends pagination {
	const TABLE_NAME = "dims_sync_matrix";
	const MY_GLOBALOBJECT_CODE = 35;//valeur du type de globalobject pour les modèles

	const DIMS_SYNCHRO_ACTIVE = 1;
	const DIMS_SYNCHRO_INACTIVE = 2;

	public function __construct() { //ça c'est pour que ça enregistre automatiquement le mb_object_correspondant
		parent::dims_data_object(self::TABLE_NAME, 'id');
		$this->has_one('dims_sync'	, dims_sync::TABLE_NAME, 'id_dims_from', 'id');
		$this->has_one('dims_sync'	, dims_sync::TABLE_NAME, 'id_dims_to', 'id');
	}

	//redéfinition des fonctions liées aux globalobjects (FROM DDO)
	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function openWith($id_from, $id_to){
		$res = $this->db->query("SELECT * FROM ".self::TABLE_NAME." WHERE id_dims_from = :idfrom AND id_dims_to = :idto LIMIT 0,1", array(
			':idfrom' => array('type' => PDO::PARAM_INT, 'value' => $id_from),
			':idto' => array('type' => PDO::PARAM_INT, 'value' => $id_to),
		));
		if($this->db->numrows($res)){
			$fields = $this->db->fetchrow($res);
			$this->openFromResultSet($fields, false, self::MY_GLOBALOBJECT_CODE);
		}
		return $this;
	}

	public function setDimsFROM($value){
		$this->fields['id_dims_from'] = $value;
	}
	public function setDimsTO($value){
		$this->fields['id_dims_to'] = $value;
	}
	public function setStatus($value){
		$this->fields['status'] = $value;
	}

	public function getDimsFROM(){
		return $this->fields['id_dims_from'];
	}
	public function getDimsTO(){
		return $this->fields['id_dims_to'];
	}
	public function getStatus(){
		return $this->fields['status'];
	}

	public function isSynchroActive(){
		return $this->getStatus() == self::DIMS_SYNCHRO_ACTIVE;
	}

	public static function create($id_from, $id_to, $status = self::DIMS_SYNCHRO_ACTIVE){
		$row = new sync_matrix();
		$row->init_description();
		$row->setugm();
		$row->setDimsFROM($id_from);
		$row->setDimsTO($id_to);
		$row->setStatus($status);
		return $row->save();
	}

	public static function deleteLink($id_from, $id_to){
		$db = dims::getInstance()->getDb();
		$db->query("DELETE FROM ".self::TABLE_NAME." WHERE id_dims_from = :idfrom AND id_dims_to = :idto", array(
			':idfrom' => array('type' => PDO::PARAM_INT, 'value' => $id_from),
			':idto' => array('type' => PDO::PARAM_INT, 'value' => $id_to),
		));
	}

	//retourne la liste des id
	public static function getDimsDest($id_from, $status = DIMS_SYNCHRO_ACTIVE){
		$db = dims::getInstance()->getDb();
		$lst = array();
		$res = $db->query("SELECT id_dims_to FROM ".self::TABLE_NAME." WHERE id_from = :idfrom AND status = :status", array(
			':idfrom' => array('type' => PDO::PARAM_INT, 'value' => $id_from),
			':status' => array('type' => PDO::PARAM_INT, 'value' => $status),
		));
		if($db->numrows($res)){
			$lst = $db->getarray($res);
		}
		return $lst;
	}

	public static function init_all_dest(){
		$db = dims::getInstance()->getDb();
		$db->query("TRUNCATE ".self::TABLE_NAME);
	}
}
