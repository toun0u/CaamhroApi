<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class address_type extends dims_data_object {
	const TABLE_NAME = 'dims_mod_business_address_type';
	const MY_GLOBALOBJECT_CODE = 411;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = $this->getLabel();
	}

	public function getLabel(){
		if(isset($_SESSION['cste'][$this->get('label')])){
			return $_SESSION['cste'][$this->get('label')];
		}else{
			return $this->get('label');
		}
	}

	public function save() {
		parent::save(self::MY_GLOBALOBJECT_CODE);
	}
}
