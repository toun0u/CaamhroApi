<?
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	business
* @access	public
*/

class tiers_dossier extends DIMS_DATA_OBJECT {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function  __construct() {
		parent::dims_data_object('dims_mod_business_tiers_dossier','tiers_id','dossier_id');
	}

}
?>
