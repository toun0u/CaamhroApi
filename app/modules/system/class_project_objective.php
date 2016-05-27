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

class project_objective extends DIMS_DATA_OBJECT {

	/* Constructeur */

	function __construct() {

		parent::dims_data_object('dims_mod_prjt_project_objective');
	}

	/* Fonction de supression de la relation projet/objectif */

	function delete() {

		parent::delete();
	}

	/* Fonction de sauvegarde de la relation projet/objectif */

	function save()	{

		parent::save();

		return $this->fields['id'];
	}

}
?>
