<?php

/********************************************************
*********************************************************
*** @author 	Patrick Nourrissier [NETLOR CONCEPT]  ***
*** @version  	1.0			                          ***
*** @package  	system			                      ***
*** @access  	public				                  ***
*** @licence	GPL						              ***
*********************************************************
********************************************************/

/* Class de la relation tâche/tâche (dépendance) */

class event_model extends DIMS_DATA_OBJECT {

	/* Constructeur */

	function __construct() {
		parent::dims_data_object('dims_mod_business_event_model');
	}

	/* Fonction de supression de la relation tâche/tâche */

	function delete() {

		// suppression de la tâche
		parent::delete();
	}

	/* Fonction de sauvegarde de la relation tâche/tâche */

	function save()	{

		parent::save();

		return $this->fields['id'];
	}
}

?>