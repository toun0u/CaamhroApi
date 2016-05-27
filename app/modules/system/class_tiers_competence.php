<?
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	media
* @access	public
*/
class tiers_competence extends DIMS_DATA_OBJECT {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_mod_business_tiers_competence', 'tiers_id', 'competence_code');
	}
}
?>
