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
		global $db;
		return(parent::save());
	}

	function checkValidEtap() {
		global $db;

		$valid=false;
		$sql = "select		ee.id,
							count(distinct eef.id_etape) as cpte,
							count(distinct efu.id_etape) as cpte2
				from		dims_mod_business_event_etap as ee
				left join	dims_mod_business_event_etap_file as eef
				ON			eef.id_etape=ee.id and eef.id_doc=0
				left join	dims_mod_business_event_etap_file_user as efu
				ON			efu.id_etape=ee.id
				AND			efu.id_action=eef.id_action and efu.valide=1
				AND			efu.id_contact= :idcontact
				WHERE		ee.id_action= :idaction
				AND			ee.id= :idetape
				GROUP by	ee.id";


		// on selectionne les fichiers attaches au contact
		$res=$db->query($sql, array(':idcontact' => $this->fields['id_contact'], ':idaction' => $this->fields['id_action'], ':idetape' => $this->fields['id_etape']) );

		if ($db->numrows($res)>0) {
			if ($f=$db->fetchrow($res)) {
				if ($f['cpte']==$f['cpte2']) {
					$valid=true;
				}
			}
		}


		return $valid;
	}
}
?>
