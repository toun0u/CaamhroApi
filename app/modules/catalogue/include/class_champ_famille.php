<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
class cata_champ_famille extends dims_data_object {
    private $oldPos = null;

    // Constantes status
    const _STATUS_OK = 0;
    const _STATUS_DEL = 1;

    // Constantes héritage
    const _INHERITED = 'H';
    const _LOCAL = 'L';

    const TABLE_NAME = 'dims_mod_cata_champ_famille';
    function __construct() {
        parent::dims_data_object(self::TABLE_NAME,'id_famille','id_champ');
    }

    public function save($del = false){
        $isNew = $this->isNew();
        // Gestion position
        if($isNew || $this->fields['position'] != $this->oldPos){
            $db = dims::getInstance()->getDb();
            if(is_null($this->oldPos) && $isNew){ // Position définie et c'est un new
                if ($this->fields['position'] == '') {
                    $this->fields['position'] = 0;
                }
                $sel = "UPDATE  ".self::TABLE_NAME."
                        SET     position =  position + 1
                        WHERE   position >= ".$this->fields['position'];
                $db->query($sel);
            }elseif($this->fields['status'] == self::_STATUS_DEL){ // Suppression du champ
                $sel = "UPDATE  ".self::TABLE_NAME."
                        SET     position =  position - 1
                        WHERE   position > ".$this->oldPos;
                $db->query($sel);
            }elseif($this->fields['position'] == '' || $this->fields['position'] <= 0){ // Position non définie : on le place à la fin
                $sel = "SELECT  position
                        FROM    ".self::TABLE_NAME."
                        WHERE   id_famille = ".$this->fields['id_famille']."
                        AND     status != ".self::_STATUS_DEL;
                $res = $db->query($sel);
                $this->fields['position'] = $db->numrows($res)+1;
            }elseif(!is_null($this->oldPos)){
                if ($this->fields['position'] > $this->oldPos) {
                    $res=$db->query("UPDATE     ".self::TABLE_NAME."
                                    SET         position = position-1
                                    WHERE       position BETWEEN ".($this->oldPos)."
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
        }
        $return = parent::save();
        if($isNew){
            if($this->fields['status'] == self::_STATUS_OK){
                include_once DIMS_APP_PATH."modules/catalogue/include/class_famille.php";
                $elem = new cata_famille();
                $elem->open($this->fields['id_famille']);
                $lstChilds = $elem->getDirectChilds(false);
                foreach($lstChilds as $idFam){
                    $lk = new cata_champ_famille();
                    $lk->open($idFam,$this->fields['id_champ']);
                    if($lk->isNew()){
                        $lk->init_description();
                        $lk->fields['id_famille'] = $idFam;
                        $lk->fields['id_champ'] = $this->fields['id_champ'];
                        $lk->fields['filtre'] = $this->fields['filtre'];
                        $lk->fields['fiche'] = $this->fields['fiche'];
                        $lk->fields['inherited'] = self::_INHERITED;
                        $lk->fields['status'] = self::_STATUS_OK;
                        $lk->save();
                    }
                }
            }
        }else{
            switch ($this->fields['status']) {
                case self::_STATUS_OK:
                    if($this->fields['inherited'] == self::_LOCAL){
                        include_once DIMS_APP_PATH."modules/catalogue/include/class_famille.php";
                        $elem = new cata_famille();
                        $elem->open($this->fields['id_famille']);
                        $lstChilds = $elem->getDirectChilds(false);
                        foreach($lstChilds as $idFam){
                            $lk = new cata_champ_famille();
                            $lk->open($idFam,$this->fields['id_champ']);
                            if($lk->isNew()){
                                $lk->init_description();
                                $lk->fields['id_famille'] = $idFam;
                                $lk->fields['id_champ'] = $this->fields['id_champ'];
                                $lk->fields['filtre'] = $this->fields['filtre'];
                                $lk->fields['fiche'] = $this->fields['fiche'];
                                $lk->fields['inherited'] = self::_INHERITED;
                                $lk->fields['status'] = self::_STATUS_OK;
                                $lk->save();
                            }elseif($lk->fields['inherited'] == self::_INHERITED){
                                $lk->fields['filtre'] = $this->fields['filtre'];
                                $lk->fields['fiche'] = $this->fields['fiche'];
                                $lk->fields['status'] = self::_STATUS_OK;
                                $lk->save();
                            }
                        }
                    }
                    break;
                case self::_STATUS_DEL:
                    if($del){
                        include_once DIMS_APP_PATH."modules/catalogue/include/class_famille.php";
                        $elem = new cata_famille();
                        $elem->open($this->fields['id_famille']);
                        $lstChilds = $elem->getDirectChilds(false);
                        if(count($lstChilds)){
                            $db =dims::getInstance()->getDb();
                            $sel = "SELECT  *
                                    FROM    ".self::TABLE_NAME."
                                    WHERE   id_champ = ".$this->fields['id_champ']."
                                    AND     id_famille IN (".implode(',',$lstChilds).")
                                    AND     inherited LIKE '".self::_INHERITED."'";
                            $res = $db->query($sel);
                            while($r = $db->fetchrow($res)){
                                $lk = new cata_champ_famille();
                                $lk->openFromResultSet($r);
                                $lk->delete();
                            }
                        }
                    }
                    break;
            }

        }
        return $return;
    }

    public function delete(){
        // Suppression des valeurs dans les articles et familles
        $this->db->query('UPDATE `dims_mod_cata_article` SET fields'.$this->get('id_champ').' = ""');
        $this->db->query('UPDATE `dims_mod_cata_famille` SET fields'.$this->get('id_champ').' = ""');

        $this->fields['status'] = self::_STATUS_DEL;
        $this->fields['position'] = 0;
        $this->save(true);
    }

    public function reactivate() {
        $this->fields['status'] = self::_STATUS_OK;
        $this->fields['position'] = -1;
        $this->save();
    }

    public function open(){
        $id_fam=0;
        $id_chp="";
        $numargs = func_num_args();
        for ($i = 0; $i < $numargs; $i++) {
            switch ($i) {
                case 0:
                    $id_fam=func_get_arg($i);
                    break;
                case 1:
                    $id_chp= func_get_arg($i);
                    break;
            }
        }
        $re = parent::open($id_fam,$id_chp);
        if(isset($this->fields['position']))
            $this->oldPos = $this->fields['position'];
        return $re;
    }

    public function openFromResultSet($fields, $unset_db = false, $go_object_value = null){
        parent::openFromResultSet($fields, $unset_db, $go_object_value);
        if(isset($this->fields['position']))
            $this->oldPos = $this->fields['position'];
    }
}
?>
