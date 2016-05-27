<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	media
* @access  	public
*/

class equipement extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_equipement';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function equipement()
	{
		parent::dims_data_object(self::TABLE_NAME);
	}
}
