<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'include/class_ent_contact.php';

class ent extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_ent');
	}

	function delete() {
		$select = "SELECT * FROM dims_ent_contact WHERE id_ent = :ident";
		$res=$this->db->query($select, array(
			':ident' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while($fields=$this->db->fetchrow($res)) {
			$ent_contact = new ent_contact();
			$ent_contact->open($fields['id_ent'], $fields['id']);
			$ent_contact->delete();
		}
		parent::delete();
	}

	function getContacts() {
		$contacts = array();

		$select =	"
					SELECT		*
					FROM		dims_contact as c
					INNER JOIN	dims_ent_contact as ec
					ON			ec.id_contact = c.id
					WHERE		ec.id_ent= :ident
					ORDER BY	c.lastname,c.firstname ASC
					";

		$result = $this->db->query($select, array(
			':ident' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($fields = $this->db->fetchrow($result)) {
			$contacts[] = $fields;
		}

		return $contacts;
	}

}
