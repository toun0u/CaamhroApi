<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class ticket_status_deprecated extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_ticket_status','id_ticket','id_user','status');
	}

	function save()
	{
		if ($this->new)
		{
			$this->fields['timestp'] = dims_createtimestamp();
			parent::save();
		}
	}
}
?>
