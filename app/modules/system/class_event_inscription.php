<?
/**
* @author	Flo @netlor
* @version	1.0
* @package	LFB
* @access	public
*/

class event_insc extends DIMS_DATA_OBJECT {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_event_inscription','id');
	}
}
?>
