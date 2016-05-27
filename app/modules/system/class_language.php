<?php


/**
 * Description of language
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class language extends dims_data_object{
    const TABLE_NAME = "dims_languages";

    public function __construct() {
	parent::dims_data_object(self::TABLE_NAME, 'id');
    }


}

?>
