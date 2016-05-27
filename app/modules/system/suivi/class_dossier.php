<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	media
* @access  	public
*/

class dossier extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_dossier';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function dossier()
	{
		parent::dims_data_object(self::TABLE_NAME);
		$this->fields['termine'] = 'Non';
	}

	function save()
	{
		if (isset($this->fields['objet_dossier'])) $this->fields['objet_dossier_search'] = business_format_search($this->fields['objet_dossier']);
		if (isset($this->fields['commentaire'])) $this->fields['commentaire_search'] = business_format_search($this->fields['commentaire']);
		return(parent::save());
	}

	function get_totaltime()
	{
		global $db;

		$totaltime = 0;

		$select = "SELECT sum(duree) as duree_total from dims_mod_business_action_detail where dossier_id = :id";
		$res=$db->query($select,array(':id'=>$this->fields['id']));
		if ($row = $db->fetchrow($res)) $totaltime = $row['duree_total'];

		return(round($totaltime/60,2));
	}
}
