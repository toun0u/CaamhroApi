<?php
class action_etap extends dims_data_object {
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
		global $db;
		return(parent::save());
	}

	function getEtapeFromContact($id_contact) {
		global $db;
		$id_etapuser=0;

		$sql="select id from dims_mod_business_event_etap_user
				where id_etape= :idetape
				and id_ee_contact= :idcontact ";

		// on selectionne les fichiers attaches au contact
		$res=$db->query($sql, array(':idetape' => $this->fields['id'], ':idcontact' => $id_contact) );

		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				$id_etapuser=$f['id'];
			}
		}

		return $id_etapuser;
	}
}
?>
