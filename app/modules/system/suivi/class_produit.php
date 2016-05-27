<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	media
* @access  	public
*/

class produit extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_print_model';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function produit()
	{
		parent::dims_data_object(self::TABLE_NAME, 'reference');
	}

}
