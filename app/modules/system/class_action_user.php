<?php
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	system
* @access	public
*/

class action_user extends DIMS_DATA_OBJECT {
	const TABLE_NAME = 'dims_mod_business_action_utilisateur';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'action_id','user_id');
	}
}
?>
