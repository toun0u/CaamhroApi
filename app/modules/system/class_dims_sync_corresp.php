<?php
class sync_corresp extends pagination {
	const TABLE_NAME = "dims_sync_corresp";
	const MY_GLOBALOBJECT_CODE = 37;//valeur du type de globalobject pour les modèles
	const WAITING_CONFIRMATION_STATUS = 1; //id
	const RECEIVED_BY_REMOTE_STATUS = 2; //id

	public function __construct(){
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	//redéfinition des fonctions liées aux globalobjects (FROM DDO)
	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function create($id_local_object, $id_remote_dims, $id_remote_object, $id_remote_record){
		$this->init_description(true);
		$this->setLocalObject($id_local_object);
		$this->setRemoteDims($id_remote_dims);
		$this->setRemoteObject($id_remote_object);
		$this->setRemoteRecord($id_remote_record);
		return $this->save();
	}

	public function setLocalObject($value){
		$this->fields['id_local_object'] = $value;
	}
	public function setRemoteDims($value){
		$this->fields['id_remote_dims'] = $value;
	}
	public function setRemoteObject($value){
		$this->fields['id_remote_object'] = $value;
	}
	public function setRemoteRecord($value){
		$this->fields['id_remote_record'] = $value;
	}

	public function getLocalObject(){
		return $this->fields['id_local_object'];
	}
	public function getRemoteDims(){
		return $this->fields['id_remote_dims'];
	}
	public function getRemoteObject(){
		return $this->fields['id_remote_object'];
	}
	public function getRemoteRecord(){
		return $this->fields['id_remote_record'];
	}

	public static function getCorrespFor($id_local_object, $id_remote_dims){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT * FROM ".self::TABLE_NAME." WHERE id_remote_dims= :idremotedims AND id_local_object= :idlocalobject", array(
			':idremotedims' => array('type' => PDO::PARAM_INT, 'value' => $id_remote_dims),
			':idlocalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_local_object),
		));
		$corresp = new sync_corresp();
		if($db->numrows($res)){
			$fields = $db->fetchrow($res);
			$corresp->openFromResultSet($fields);
		}
		return $corresp;
	}
}
