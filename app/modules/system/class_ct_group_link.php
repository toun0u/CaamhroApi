<?php
/**
* @author	NETLOR - Flo
* @version	1.0
* @package	system
* @access	public
*/
class ct_group_link extends dims_data_object {
	const TABLE_NAME = "dims_mod_business_contact_group_link";
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function save() {
		return(parent::save());
	}

	function delete() {
	$db = dims::getInstance()->getDb();
		parent::delete();
	}
}
?>
