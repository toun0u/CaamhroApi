<?php
include_once DIMS_APP_PATH."modules/doc/class_docfile.php";
class cata_fam_thumb extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_famille_thumbnail';
    private $docfile = null;
    private $oldPos = null;
    function __construct() {
        parent::dims_data_object(self::TABLE_NAME,'id_famille','id_doc');
    }

    public function save(){
        $db = dims::getInstance()->getDb();
        if($this->isNew() && ($this->fields['position'] == "" || $this->fields['position'] <= 0)){
            $sel = "SELECT  *
                    FROM    ".self::TABLE_NAME."
                    WHERE   id_famille = ".$this->fields['id_famille'];
            $res = $db->query($sel);
            $this->fields['position'] = $db->numrows($res)+1;
        }elseif(is_null($this->oldPos) && $this->isNew()){ // Position définie et c'est un new
            $sel = "UPDATE  ".self::TABLE_NAME."
                    SET     position =  position + 1
                    WHERE   position >= ".$this->fields['position']."
                    AND     id_famille = ".$this->fields['id_famille'];
            $db->query($sel);
        }elseif(!is_null($this->oldPos) && $this->oldPos != $this->fields['position']){
            if ($this->fields['position'] > $this->oldPos) {
                $res=$db->query("UPDATE     ".self::TABLE_NAME."
                                SET         position = position-1
                                WHERE       position BETWEEN ".($this->oldPos-1)."
                                AND         {$this->fields['position']}
                                AND         position>0
                                AND         id_famille = ".$this->fields['id_famille']."");
            }
            else {
                $res=$db->query("UPDATE     ".self::TABLE_NAME."
                                SET         position = position+1
                                WHERE       position BETWEEN {$this->fields['position']}
                                AND         ".($this->oldPos-1)."
                                AND         id_famille = ".$this->fields['id_famille']."");
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
        if($this->fields['id_article'] <= 0 || $this->fields['id_article'] == ''){
            $doc = $this->getDocFile();
            $doc->delete();
        }

        $sel = "UPDATE  ".self::TABLE_NAME."
                SET     position =  position - 1
                WHERE   position >= ".$this->fields['position']."
                AND     id_famille = ".$this->fields['id_famille'];
        $db->query($sel);
        parent::delete();
    }

    public function getDocfile(){
        if(is_null($this->docfile)){
            $this->docfile = new docfile();
            $this->docfile->open($this->fields['id_docfile']);
        }
        return $this->docfile;
    }

    public function setDocFile(docfile $doc){
        $this->docfile = $doc;
        $this->fields['id_doc'] = $doc->fields['id'];
    }

    public function getArticle(){
        if($this->fields['id_article'] != '' && $this->fields['id_article'] > 0){
            include_once DIMS_APP_PATH."modules/catalogue/include/class_famille_thumb.php";
            $art = new article();
            $art->open($this->fields['id_article']);
            return $art;
        }else
            return null;
    }
}
?>
