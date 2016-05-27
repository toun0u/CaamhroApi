<?
require_once DIMS_APP_PATH . '/include/class_dims_data_object.php';

class workflow extends dims_data_object {

	/**
	* Class constructor
	*
	* @access public
	**/
	function workflow()
	{
		parent::dims_data_object('dims_workflow','id');
	}

}
?>
