<?php

class etap_file_ct extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_event_etap_file_user','id');
	}

	function save() {
		$db = dims::getInstance()->getDb();
		return(parent::save());
	}

	function checkValidEtap() {
		$db = dims::getInstance()->getDb();

		$valid=true;
		$sql=	"SELECT id from dims_mod_business_event_etap_file_user
				where id_etape= :idetape
				and id_ee_contact= :idcontact and valide=0";

		// on selectionne les fichiers attaches au contact
		$res=$db->query($sql, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_contact']),
			':idetape' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_etape']),
		));

		if ($db->numrows($res)>0) {
			$valid=false;
		}

		return $valid;
	}
}
