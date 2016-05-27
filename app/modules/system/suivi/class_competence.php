<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	media
* @access  	public
*/

class competence extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_competence';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function competence()
	{
		parent::dims_data_object(self::TABLE_NAME);
	}
}
