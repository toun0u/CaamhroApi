<?php

/********************************************************
*********************************************************
*** @author	Patrick Nourrissier [NETLOR CONCEPT]  ***
*** @version	1.0				      ***
*** @package	system				      ***
*** @access	public				      ***
*** @licence	GPL				      ***
*********************************************************
********************************************************/

/* Class de la relation t�che/t�che (d�pendance) */

class event_model extends DIMS_DATA_OBJECT {

	/* Constructeur */

	function __construct() {
		parent::dims_data_object('dims_mod_business_event_model');
	}

	/* Fonction de supression de la relation t�che/t�che */

	function delete() {

		// suppression de la t�che
		parent::delete();
	}

	/* Fonction de sauvegarde de la relation t�che/t�che */

	function save()	{

		parent::save();

		return $this->fields['id'];
	}
}

?>
