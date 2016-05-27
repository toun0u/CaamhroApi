<?php
class tarif_qte extends dims_data_object {

    const TABLE_NAME = 'dims_mod_cata_tarqte';

    function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'ccat', 'numart');
    }
    public function setCategoryClient($val){
    	$this->fields['ccat'] = $val;
    }

    public function setArticleID($val){
    	$this->fields['numart'] = $val;
    }

    public function addStep($index, $qty, $price){
    	$this->fields['seuil_'.$index] = $qty;
    	$this->fields['pv_'.$index] = $price;
    }

}
?>