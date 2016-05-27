<?php
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	system
* @access	public
*/

class action_resp extends DIMS_DATA_OBJECT {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_action_resp','id_action','id_object','id_record');
	}
}
?>
