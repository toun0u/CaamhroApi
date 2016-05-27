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

/* Class de la relation projet/tâche  */

class task_user extends DIMS_DATA_OBJECT {
	/* Constructeur */
	function __construct() {
		parent::dims_data_object('dims_task_user','id_task','id_ref','type');
	}

	/* Fonction de supression de la relation tâche/user */

	function delete() {

		parent::delete();
	}

	/* Fonction de sauvegarde de la relation tâche/user */

	function save()	{

		parent::save();

		return $this->fields['id'];
	}

}
?>
