<?php
class cata_champ_lang extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_champ_lang';
    function __construct() {
        parent::dims_data_object(self::TABLE_NAME,'id_chp','id_lang','id_module');
    }

    public function save(){
        if(!isset($this->fields['id_module']) || ($this->fields['id_module'] == '' || $this->fields['id_module'] <= 0))
            $this->fields['id_module'] = $_SESSION["dims"]['moduleid'];
        return parent::save();
    }

    public function open(){
        $id_chp=0;
        $id_lang=0;
        $id_mod = $_SESSION["dims"]['moduleid'];
        $numargs = func_num_args();
        for ($i = 0; $i < $numargs; $i++) {
            switch ($i) {
                case 0:
                    $id_chp=func_get_arg($i);
                    break;
                case 1:
                    $id_lang= func_get_arg($i);
                    break;
                case 2:
                    $id_mod= func_get_arg($i);
                    break;
            }
        }
        return parent::open($id_chp,$id_lang,$id_mod);
    }
}
?>