<?
// PZ + Patrick le 14/04/2011
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class spool_mail extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_spool_mail');
	}

}
?>
