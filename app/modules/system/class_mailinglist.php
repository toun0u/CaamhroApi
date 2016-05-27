<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class mailinglist extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mailinglist');
	}

	/*
	 * Fonction permettant de désincrire toutes les personnes ayant cet email
	 * Recherche par les emails directemnt attachés ou par mailinglist
	 */
	public function unsubscribeByMail($email) {
		if ($this->fields['query_delete']=='') {
			$this->db->query("UPDATE dims_mailinglist_attach set deleted=1 where id_mailinglist= :idmailinglist and email like :email ", array(
				':idmailinglist' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':email' => array('type' => PDO::PARAM_STR, 'value' => $email),
			));
		}
		else {
			// on doit traiter le cas sql_update
			$cmd= str_replace("[EMAIL]", $email, $this->fields['query_delete']);
			$this->db->query($cmd);
		}
	}
}
?>
