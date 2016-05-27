<?php

include_once DIMS_APP_PATH.'modules/catalogue/include/class_cloud_element.php';

class cloud extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_wce_cloud';

    const _MODE_ALEATOIRE = 1;
    const _MODE_IMPORTANCE = 2;
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
        $db = dims::getInstance()->getDb();

        $sql = 'SELECT  *
                FROM    '.cloud_element::TABLE_NAME.'
                WHERE   id_cloud = '.$this->fields['id'];
        switch ($this->fields['mode']) {
            case self::_MODE_ALEATOIRE:
                $sql .= " ORDER BY RAND() ";
                break;
            case self::_MODE_IMPORTANCE:
                $sql .= " ORDER BY niveau DESC ";
                break;
        }

        $res = $db->query($sql);

        while ($fields = $db->fetchrow($res)) {
            $element = new cloud_element();
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
        $sel = "SELECT      c.*, COUNT(e.id) AS nb_elem
                FROM        ".self::TABLE_NAME." c
                LEFT JOIN   ".cloud_element::TABLE_NAME." e
                ON          e.id_cloud = c.id
                WHERE       c.id_module = ".$_SESSION['dims']['moduleid']."
                GROUP BY    c.id";
        $res = $db->query($sel);
        foreach ($db->split_resultset($res) as $r){
            $elem = new cloud();
            $elem->openFromResultSet($r['c']);
            $elem->setLightAttribute('nb_elem',$r['unknown_table']['nb_elem']);
            $lst[] = $elem;
        }
        return $lst;
    }
}
