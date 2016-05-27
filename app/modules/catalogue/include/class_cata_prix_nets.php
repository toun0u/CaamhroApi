<?php
class cata_prix_nets extends pagination{
    const TABLE_NAME = 'dims_mod_cata_prix_nets';

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'code_cm', 'reference');
    }

    public function getArticle(){
        include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
        $db = dims::getInstance()->getDb();
        $sel = "SELECT  *
                FROM    ".article::TABLE_NAME."
                WHERE   reference LIKE '".$this->fields['reference']."'";
        $res = $db->query($sel);
        if($r = $db->fetchrow($res)){
            $elem = new article();
            $elem->openFromResultSet($r);
            return $elem;
        }else{
            return null;
        }
    }
}
?>
