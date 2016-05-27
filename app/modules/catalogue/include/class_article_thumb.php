<?php
include_once DIMS_APP_PATH."modules/doc/class_docfile.php";
class cata_art_thumb extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_article_thumbnail';
    private $docfile = null;
    private $oldPos = null;
    function __construct() {
        parent::dims_data_object(self::TABLE_NAME,'id_article','id_doc');
    }

    public function save(){
        $db = dims::getInstance()->getDb();
        if($this->isNew() && ($this->fields['position'] == "" || $this->fields['position'] <= 0)){
            $sel = "SELECT  *
                    FROM    ".self::TABLE_NAME."
                    WHERE   id_article = ".$this->fields['id_article'];
            $res = $db->query($sel);
            $this->fields['position'] = $db->numrows($res)+1;
        }elseif(is_null($this->oldPos) && $this->isNew()){ // Position définie et c'est un new
            $sel = "UPDATE  ".self::TABLE_NAME."
                    SET     position =  position + 1
                    WHERE   position >= ".$this->fields['position']."
                    AND     id_article = ".$this->fields['id_article'];
            $db->query($sel);
        }elseif(!is_null($this->oldPos) && $this->oldPos != $this->fields['position']){
            if ($this->fields['position'] > $this->oldPos) {
                $res=$db->query("UPDATE     ".self::TABLE_NAME."
                                SET         position = position-1
                                WHERE       position BETWEEN ".($this->oldPos-1)."
                                AND         {$this->fields['position']}
                                AND         position>0
                                AND         id_article = ".$this->fields['id_article']."");
            }
            else {
                $res=$db->query("UPDATE     ".self::TABLE_NAME."
                                SET         position = position+1
                                WHERE       position BETWEEN {$this->fields['position']}
                                AND         ".($this->oldPos-1)."
                                AND         id_article = ".$this->fields['id_article']."");
            }
        }
        return parent::save();
    }

    public function open(){
        $args = func_get_args();
        if(count($args) >= 2)
            parent::open($args[0],$args[1]);
        else
            parent::open();
        if(isset($this->fields['position']))
            $this->oldPos = $this->fields['position'];
    }

    public function openFromResultSet($fields, $unset_db = false, $go_object_value = null) {
        parent::openFromResultSet($fields, $unset_db, $go_object_value);
        $this->oldPos = $this->fields['position'];
    }
    public function delete(){
        $db = dims::getInstance()->getDb();
        include_once DIMS_APP_PATH."modules/catalogue/include/class_famille_thumb.php";
        $sel = "SELECT  *
                FROM    ".cata_fam_thumb::TABLE_NAME."
                WHERE   id_doc = ".$this->fields['id_doc'];
        $res = $db->query($sel);
        while($r = $db->fetchrow($res)){
            $lk = new cata_fam_thumb();
            $lk->openFromResultSet($r);
            $lk->delete();
        }

        $doc = $this->getDocFile();
        $doc->delete();
        $sel = "UPDATE  ".self::TABLE_NAME."
                SET     position =  position - 1
                WHERE   position >= ".$this->fields['position']."
                AND     id_article = ".$this->fields['id_article'];
        $db->query($sel);
        parent::delete();
    }

    public function getDocfile(){
        if(is_null($this->docfile)){
            $this->docfile = new docfile();
            $this->docfile->open($this->fields['id_doc']);
        }
        return $this->docfile;
    }

    public function setDocFile(docfile $doc){
        $this->docfile = $doc;
        $this->fields['id_doc'] = $doc->fields['id'];
    }
}
?>