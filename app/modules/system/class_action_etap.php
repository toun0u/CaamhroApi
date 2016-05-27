<?php
class sys_action_etap extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_event_etap','id');
	}

	function save() {
		$db = dims::getInstance()->getDb();
		return(parent::save());
	}

	function getEtapeFromContact($id_contact) {
		$db = dims::getInstance()->getDb();
		$id_etapuser=0;

		$sql=	"SELECT id from dims_mod_business_event_etap_user
				where id_etape= :idetape
				and id_ee_contact= :idcontact";

		// on selectionne les fichiers attaches au contact
		$res=$db->query($sql, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
			':idetape' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_etape']),
		));

		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				$id_etapuser=$f['id'];
			}
		}

		return $id_etapuser;
	}
}
