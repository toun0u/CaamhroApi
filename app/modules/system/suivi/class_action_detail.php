<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	promotech
* @access  	public
*/

class action_detail extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_action_detail';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function action_detail()
	{
		parent::dims_data_object(self::TABLE_NAME,'action_id','tiers_id','interlocuteur_id','dossier_id');
	}
}
