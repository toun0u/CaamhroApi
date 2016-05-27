<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	promotech
* @access  	public
*/

class tiers_dossier extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_tiers_dossier';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function  tiers_dossier()
	{
		parent::dims_data_object(self::TABLE_NAME,'tiers_id','dossier_id');
	}

}
