<?php
class sync_data extends pagination {
	const TABLE_NAME = "dims_sync_data";
	const MY_GLOBALOBJECT_CODE = 33;//valeur du type de globalobject pour les modèles
	const WAITING_CONFIRMATION_STATUS = 1; //id
	const RECEIVED_BY_REMOTE_STATUS = 2; //id

	public function __construct() { //ça c'est pour que ça enregistre automatiquement le mb_object_correspondant
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	//redéfinition des fonctions liées aux globalobjects (FROM DDO)
	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function create($xml, $go_object, $classname, $id_dims_dest = 0, $status = self::WAITING_CONFIRMATION_STATUS){
		$this->init_description(true);
		$this->setugm();
		$this->setXMLData($xml);
		$this->setObjectID($go_object);
		$this->setClassname($classname);
		$this->setRemoteDimsID($id_dims_dest);
		$this->setStatus($status);
		return $this->save();
	}

	public function setXMLData($value){
		$this->fields['xml'] = $value;
	}

	public function getXMLData(){
		return $this->fields['xml'];
	}

	public function setObjectID($value){
		$this->fields['go_object'] = $value;
	}

	public function getObjectID(){
		return $this->fields['go_object'];
	}

	public function setRemoteDimsID($value){
		$this->fields['id_dims_dest'] = $value;
	}

	public function getRemoteDimsID(){
		return $this->fields['id_dims_dest'];
	}

	public function setStatus($value){
		$this->fields['status'] = $value;
	}

	public function getStatus(){
		return $this->fields['status'];
	}

	public function setClassname($value){
		$this->fields['classname'] = $value;
	}

	public function getClassname(){
		return $this->fields['classname'];
	}

	public function isReceivedByRemote(){
		return $this->getStatus() == self::RECEIVED_BY_REMOTE_STATUS;
	}
}
