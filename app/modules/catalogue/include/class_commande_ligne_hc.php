<?php
class commande_ligne_hc extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_cde_lignes_hc';

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id_cde_ligne');
    }

}
?>
