<?php
/**
* @author	NETLOR - LIEB Simon
* @version  1.0
* @package  system
* @access	public
*/
class newsletter_inscription extends dims_data_object {
	const TABLE_NAME = 'dims_mod_newsletter_inscription';
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
		$db = dims::getInstance()->getDb();
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		parent::delete();
	}
}
