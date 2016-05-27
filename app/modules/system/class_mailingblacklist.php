<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class mailingblacklist extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mailingblacklist');
	}

	function save(){
		$sel = "SELECT	id
				FROM	dims_mailingblacklist
				WHERE	id_newsletter = :idnewsletter
				AND		email LIKE :email";
		$res = $this->db->query($sel, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_newsletter']),
			':email' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['email']),
		));
		if ($this->db->numrows($res) > 0)
			return false;
		else
			parent::save();
	}
}
?>
