<?
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	promotech
* @access	public
*/

class tiers_interlocuteur extends DIMS_DATA_OBJECT
{
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function  __construct() {
		parent::dims_data_object('dims_mod_business_tiers_interlocuteur','tiers_id','interlocuteur_id');
	}

	function save()
	{
		if (isset($this->fields['telephone'])) $this->fields['telephone'] = business_format_tel($this->fields['telephone']);
		if (isset($this->fields['telecopie'])) $this->fields['telecopie'] = business_format_tel($this->fields['telecopie']);
		if (isset($this->fields['telmobile'])) $this->fields['telmobile'] = business_format_tel($this->fields['telmobile']);
		return(parent::save());
	}
}
?>
