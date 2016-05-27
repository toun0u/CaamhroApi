<?php
class gescom_form extends dims_data_object{
	const TABLE_NAME = 'dims_gescom_form';

	public function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
	}
}
