<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	System
* @access  	public
*/

class user_action_log extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function user_action_log()
	{
		parent::dims_data_object('dims_user_action_log','id_user','id_action','id_module_type');
	}


}
?>
