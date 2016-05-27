<?php
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	lfb
* @access  	public
*/

class inscription extends DIMS_DATA_OBJECT {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_event_inscription','id');
	}
}
?>
