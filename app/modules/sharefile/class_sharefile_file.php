<?
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");

class sharefile_file extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_mod_sharefile_file','id_share','id_doc');
	}

	function delete() {
		global $db;
		$doc = new docfile();
		$doc->open($this->fields['id_doc']);
		$doc->delete();
		parent::delete();
	}
}
?>
