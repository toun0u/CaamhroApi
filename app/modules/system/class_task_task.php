<?php

/****************************************************
*****************************************************
*** @author	Arnaud KNOBLOCH [NETLOR CONCEPT]  ***
*** @version	1.0				  ***
*** @package	projects			  ***
*** @access	public				  ***
*** @licence	GPL				  ***
*****************************************************
*****************************************************/

/* Class de la relation tâche/tâche (dépendance) */

class task_task extends DIMS_DATA_OBJECT {

	/* Constructeur */

	function __construct() {

		parent::dims_data_object('dims_mod_prjt_task_task');
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
