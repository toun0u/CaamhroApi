<?php
class action_etap_ct extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_event_etap_user','id');
	}

	function save() {
		global $db;
		return(parent::save());
	}
}
?>
