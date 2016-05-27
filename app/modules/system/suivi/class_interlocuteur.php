<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	media
* @access  	public
*/

class interlocuteur extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_interlocuteur';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function  interlocuteur()
	{
		parent::dims_data_object(self::TABLE_NAME);
		$this->categories = array();
	}

	function open($id)
	{
		global $db;

		$res=$db->query("SELECT * FROM dims_mod_business_interlocuteur_categorie WHERE id_interlocuteur = :id",array(':id'=>$id));
		while ($fields = $db->fetchrow($res)) $this->categories[$fields['categorie']] = $fields['categorie'];

		return(parent::open($id));
	}

	function save()
	{
		if (isset($this->fields['nom'])) $this->fields['nom'] = business_format_lastname($this->fields['nom']);
		if (isset($this->fields['prenom'])) $this->fields['prenom'] = business_format_firstname($this->fields['prenom']);

		if (isset($this->fields['ville'])) $this->fields['ville'] = business_format_search($this->fields['ville']);

		if (isset($this->fields['nom'])) $this->fields['nom_search'] = business_format_search($this->fields['nom']);
		if (isset($this->fields['prenom'])) $this->fields['prenom_search'] = business_format_search($this->fields['prenom']);
		if (isset($this->fields['telephone'])) $this->fields['telephone'] = business_format_tel($this->fields['telephone']);
		if (isset($this->fields['telecopie'])) $this->fields['telecopie'] = business_format_tel($this->fields['telecopie']);
		if (isset($this->fields['telmobile'])) $this->fields['telmobile'] = business_format_tel($this->fields['telmobile']);
		$this->fields['date_maj'] = date(dims_const::DIMS_DATEFORMAT_US);
		if ($this->fields['date_creation'] == '' || $this->fields['date_creation'] == '0000-00-00') $this->fields['date_creation'] = date(dims_const::DIMS_DATEFORMAT_US);
		return(parent::save());
	}
}
