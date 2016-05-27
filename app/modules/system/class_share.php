<?

class share extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_share','id');
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
?>