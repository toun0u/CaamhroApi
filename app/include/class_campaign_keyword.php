<?
require_once(DIMS_APP_PATH.'include/class_dims_data_object.php');

class campaign_keyword extends dims_data_object
{

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function campaign_keyword()
	{
		parent::dims_data_object('dims_campaign_keyword','id');
	}

	function save()
	{
		parent::save();
	}

}
?>
