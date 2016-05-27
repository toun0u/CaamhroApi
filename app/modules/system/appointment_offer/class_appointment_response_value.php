<?php

class dims_appointment_response_val extends dims_data_object {
	const TABLE_NAME = 'dims_mod_business_action_reponse_value';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}
}
?>