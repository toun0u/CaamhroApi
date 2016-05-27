<?php
class canton extends dims_data_object {
	const TABLE_NAME = 'dims_canton';
	const MY_GLOBALOBJECT_CODE = 453;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function delete(){
		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	public function getLabel(){
		return $this->get('name');
	}

	public function save(){
		$this->set('name',strtoupper(dims_convertaccents($this->get('name'))));
		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = $this->get('name');
	}
}
