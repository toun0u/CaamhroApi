<?
/**
* @author	NETLOR - Pat
* @version	1.0
* @package	business
* @access	public
*/
class metafielduse extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_meta_use');
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
