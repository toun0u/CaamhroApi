<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class share extends dims_data_object
{

	/**
	* Class constructor
	*
	* @access public
	**/
	function share()
	{
		parent::dims_data_object('dims_share','id');
	}

}
?>
