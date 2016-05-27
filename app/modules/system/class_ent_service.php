<?php
class ent_service extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_ent_services');
	}

	function delete() {
		// suppression des enfants
		$rs = $this->db->query('SELECT id FROM dims_ent_services WHERE id_service = :idservice', array(
			':idservice' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($row = $this->db->fetchrow($rs)) {
			$es_enfant = new ent_service();
			$es_enfant->open($row['id']);
			$es_enfant->delete();
		}
		parent::delete();
	}
}
