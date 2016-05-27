<?php
/**
* @author	NETLOR - Flo
* @version	1.0
* @package	system
* @access	public
*/
class news_article extends dims_data_object {
	const TABLE_NAME = 'dims_mod_newsletter_content';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function save() {
		if($this->new) {
			$this->fields['id_user_create'] = $_SESSION['dims']['userid'];
			$this->fields['date_create'] = date("YmdHis");
		}
		//$this->fields['id_user_modif'] = $_SESSION['dims']['userid'];
		$this->fields['date_modif'] = date("YmdHis");
		return(parent::save());
	}

	function delete() {
		global $dims;
		$db = dims::getInstance()->getDb();

		//on supprime tous les docs rattaches
		require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
		require_once DIMS_APP_PATH.'include/functions/files.php';

		$id_module = $_SESSION['dims']['moduleid'];
		$id_object = dims_const::_SYSTEM_OBJECT_NEWSLETTER;
		$id_record = $this->fields['id'];

		$listfiles = dims_getFiles($dims,$id_module,$id_object,$id_record);

		if(isset($listfiles) && $listfiles != '') {
			foreach($listfiles as $key => $files) {
				$docfile = new docfile();
				$docfile->open($files['id']);
				$docfile->delete();
			}
		}
		parent::delete();
	}
}
