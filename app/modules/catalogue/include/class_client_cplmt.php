<?php
class client_cplmt extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_client_cplmt';
    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id_client');
    }

}
?>
