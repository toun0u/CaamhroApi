<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class mb_field extends dims_data_object {
	/**
	* Class constructor
	*
	* @access public
	**/

	const TABLE_NAME = 'dims_mb_field';

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}
}
?>
