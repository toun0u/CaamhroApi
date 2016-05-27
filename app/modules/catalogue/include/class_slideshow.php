<?php

include_once DIMS_APP_PATH.'modules/catalogue/include/class_slideshow_element.php';

class slideshow extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_wce_slideshow';
    /**
    * Class constructor
    *
    * @access public
    **/
    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getElements() {
        $elemList = array();

        $sql = 'SELECT      *
                FROM        '.slideshow_element::TABLE_NAME.'
                WHERE       id_slideshow = '.$this->fields['id'].'
                ORDER BY    position ASC';

        $res = $this->db->query($sql);

        while ($fields = $this->db->fetchrow($res)) {
            $element = new slideshow_element();
            $element->openFromResultSet($fields);
            $elemList[] = $element;
        }

        return $elemList;
    }

    public function delete() {

        foreach($this->getElements() as $element) {
            $element->delete();
        }

        return parent::delete();
    }

    public static function getAll(){
        $lst = array();
        $db = dims::getInstance()->getDb();
        $sel = "SELECT  *
                FROM    ".self::TABLE_NAME."
                WHERE   id_module = ".$_SESSION['dims']['moduleid'];
        $res = $db->query($sel);
        while ($r = $db->fetchrow($res)){
            $elem = new slideshow();
            $elem->openFromResultSet($r);
            $lst[] = $elem;
        }
        return $lst;
    }
}
