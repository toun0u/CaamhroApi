<?php
/**
* @author	patrick[at]netlor.fr - 27/10/2009
* @version	1.0
* @package	system
* @access	public
*/
//require_once(DIMS_APP_PATH . "/modules/system/include/business.php");

class tiers_layer extends DIMS_DATA_OBJECT {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_tiers_layer','id','type_layer','id_layer');
	}

	function save() {
		parent::save(dims_const::_SYSTEM_OBJECT_TIERS_WORKSPACE);
	}
}
?>
