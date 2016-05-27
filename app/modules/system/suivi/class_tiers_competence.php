<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	media
* @access  	public
*/

class tiers_competence extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_tiers_competence';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function tiers_competence()
	{
		parent::dims_data_object(self::TABLE_NAME, 'tiers_id', 'competence_code');
	}
}
