<?php
class cloud_element extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_wce_cloud_element';
    /**
    * Class constructor
    *
    * @access public
    **/
    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }
}
