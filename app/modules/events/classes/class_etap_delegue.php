<?php
/**
* @author 	NETLOR - Flo
* @version  	1.0
* @package  	system
* @access  	public
*/
class etap_delegue extends dims_data_object {

	function __construct() {
		parent::dims_data_object('dims_mod_business_event_etap_delegue','id');
	}

	function save() {
		return(parent::save());
	}

	function delete() {
        global $db;
		parent::delete();
	}
}


?>
