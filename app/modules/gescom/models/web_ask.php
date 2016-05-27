<?php
class web_ask extends dims_data_object{
	const TABLE_NAME = 'dims_web_ask';
	const MY_GLOBALOBJECT_CODE = 600;

	const _TYPE_CATA_DEVIS = 1;

	const _STATE_WAITING = 0;
	const _STATE_VALIDATED = 1;
	const _STATE_DELETED = 2;

	public function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = 'Demande Web #'.$this->get('id');
	}
}
