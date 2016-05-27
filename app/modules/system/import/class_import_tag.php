<?php

/**
 * Description of class_import
 *
 * @author Patrick Nourrissier
 * @copyright Netlor 2013
 */
class import_tag extends dims_data_object{
    const TABLE_NAME = "dims_import_tag";

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function checkTag($tag) {

    }

}

?>
