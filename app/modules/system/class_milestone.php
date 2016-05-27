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

/* Class d'un projet */

class milestone extends DIMS_DATA_OBJECT {

	/* Constructeur	*/

	function __construct() {
		parent::dims_data_object('dims_milestone');
		$this->fields['state'] = 1;
	}

	/* Fonction pour changer l'état de l'milestone */

	function change_state($idmilestone) {

		$db = dims::getInstance()->getDb();

		$etat = "";
		$select = "select state as state from dims_milestone where id = :idmilestone";
		$res=$db->query($select, array(
			':idmilestone' => array('type' => PDO::PARAM_INT, 'value' => $idmilestone),
		));

		/* On récupère l'état */
		if ($row = $db->fetchrow($res))
			$etat = $row['state'];

		/* On change d'état suivant l'état courant */
		if ($etat == 1) {
			$select = "update `dims_milestone` set `state` = 0 where id = :idmilestone";
		} else {
			$select = "update `dims_milestone` set `state` = 1 where id = :idmilestone";
		}

		$res=$db->query($select, array(
			':idmilestone' => array('type' => PDO::PARAM_INT, 'value' => $idmilestone),
		));
	}

	/* Fonction de suppression d'un objectif */

	function delete() {

		parent::delete();
	}


	/* Fonction de sauvegarde d'un objectif */

	function save()	{

		parent::save();

		return $this->fields['id'];
	}
}

?>
