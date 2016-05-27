<?php
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	media
* @access	public
*/

class dossier extends DIMS_DATA_OBJECT {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_planning_dossier');
		$this->fields['termine'] = 'Non';
	}

	function save() {
		if (isset($this->fields['objet_dossier'])) $this->fields['objet_dossier_search'] = planning_format_search($this->fields['objet_dossier']);
		if (isset($this->fields['commentaire'])) $this->fields['commentaire_search'] = planning_format_search($this->fields['commentaire']);
		return(parent::save());
	}

	function get_totaltime() {
		$totaltime = 0;

		$select = "SELECT sum(duree) as duree_total from dims_mod_planning_action_detail where dossier_id = :iddossier";
		$res=$this->db->query($select, array(
			':iddossier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if ($row = $this->db->fetchrow($res)) $totaltime = $row['duree_total'];

		return(round($totaltime/60,2));
	}
}
