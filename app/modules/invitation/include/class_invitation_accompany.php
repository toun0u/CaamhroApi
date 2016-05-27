<?php
class invitation_accompany extends dims_data_object {
	const TABLE_NAME = 'dims_mod_business_action_accompany';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function save(){
		if($this->get('age') == 'dims_nan') $this->set('age','');
		if($this->get('name') != '')
			parent::save();
	}
}
