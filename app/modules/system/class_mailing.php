<?php
/**
* @author	NETLOR - Flo
* @version	1.0
* @package	system
* @access	public
*/
class mailing extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_newsletter_mailing_list');
	}

	function save() {
	if($this->new) {
		$this->fields['id_user_create'] = $_SESSION['dims']['userid'];
		$this->fields['date_create'] = date("YmdHis");
		$this->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
	}

		return(parent::save());
	}



	function getNbmail() {
		$db = dims::getInstance()->getDb();

		$sql_sch = 'SELECT id FROM dims_mod_newsletter_mailing_ct WHERE id_mailing = :idmailing';
		$res_sch = $db->query($sql_sch, array(
			':idmailing' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		return $db->numrows($res_sch);
	}
}
?>
