<?php
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	promotech
* @access	public
*/

class action_detail extends DIMS_DATA_OBJECT {
	const TABLE_NAME = 'dims_mod_business_action_detail';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'action_id','tiers_id','contact_id','dossier_id');
	}
}
?>
