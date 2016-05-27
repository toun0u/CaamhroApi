<?php

class appointment_notification extends dims_data_object {

	public function __construct() {
		parent::dims_data_object('dims_mod_business_action_notification', 'id_action', 'id_contact');
	}

}
