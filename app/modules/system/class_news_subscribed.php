<?php
/**
* @author	NETLOR - Flo
* @version	1.0
* @package	system
* @access	public
*/
class news_subscribed extends dims_data_object {
	const TABLE_NAME = 'dims_mod_newsletter_subscribed';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id_newsletter', 'id_contact');
	}

	function save() {
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		parent::delete();
	}
}
