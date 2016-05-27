<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	promotech
* @access  	public
*/

class action_utilisateur extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_action_utilisateur';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function action_utilisateur()
	{
		parent::dims_data_object(self::TABLE_NAME,'action_id','user_id');
	}
}
