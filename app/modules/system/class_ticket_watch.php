<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class ticket_watch_deprecated extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_ticket_watch','id_ticket','id_user');
	}

}
?>
