<?php

class sharefile_contact extends dims_data_object {
	const TABLE_NAME = 'dims_mod_sharefile_contact';

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}
}
