<?

class business_metacateg extends dims_data_object {
	const TABLE_NAME = "dims_mod_business_meta_categ";
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


}
?>
