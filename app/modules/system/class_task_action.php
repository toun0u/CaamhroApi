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

class task_action extends DIMS_DATA_OBJECT {

	/* Constructeur */

	function __construct() {

		parent::dims_data_object('dims_task_action');
	}

	/* Fonction de supression de la relation tache/user/action */

	function delete() {

		parent::delete();
	}

	/* Fonction de sauvegarde de la relation tache/user/action */

	function save()	{

		parent::save();

		return $this->fields['id'];
	}

}
?>
