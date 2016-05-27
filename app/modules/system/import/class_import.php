<?php

/**
 * Description of class_import
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class import extends dims_data_object{
    const TABLE_NAME = "dims_import";

    const STATUT_NO_FILE_IMPORT = _IMPORT_STATUT_NO_FILE_IMPORT;
    const STATUT_MODEL_NOT_CORRECT = _IMPORT_STATUT_MODEL_NOT_CORRECT ;
    const STATUT_FILE_NOT_CORRECT = _IMPORT_STATUT_FILE_NOT_CORRECT ;
    const STATUT_FILE_IMPORTER = _IMPORT_STATUT_FILE_IMPORTED ;
    const STATUT_FILE_IMPORT_IN_PROGRESS = _IMPORT_STATUT_IMPORT_IN_PROGRESS ;
    const STATUT_DATE_IMPORTED = _IMPORT_STATUT_DATE_IMPORTED ;

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getIdFichierModele() {
        return $this->getAttribut("id_fichier_modele", self::TYPE_ATTRIBUT_KEY);
    }

    public function getStatus() {
        return $this->getAttribut("status", self::TYPE_ATTRIBUT_NUMERIC);
    }

    public function getRefTmpTable() {
        return $this->getAttribut("ref_tmp_table", self::TYPE_ATTRIBUT_STRING);
    }

    public function getIdGlobalObjectConcerned(){
        return $this->getAttribut("id_globalobject_concerned", self::TYPE_ATTRIBUT_KEY);
    }

    public function setStatus($status, $save = false){
        $this->setAttribut("status", self::TYPE_ATTRIBUT_NUMERIC, $status, $save);
    }

    public function setComments($comments, $save = false){
        $this->setAttribut("comments", self::TYPE_ATTRIBUT_STRING, $comments, $save);
    }

    public function getComments() {
        return $this->getAttribut("comments", self::TYPE_ATTRIBUT_STRING);
    }


    public function addComments($comments){
        $old_comment = $this->getComments() ;
        if(empty($old_comment) || $old_comment == 'NULL'){
            $this->setComments($comments);
        }else{
            $this->setComments($this->getComments().",".$comments);
        }
    }

    public function setIdGlobalobjectConcerned($id_global_object_concerned, $save = false){
        $this->setAttribut("id_global_object_concerned", self::TYPE_ATTRIBUT_KEY, $id_global_object_concerned, $save);
    }

    public function setTimestpCreate($timestp_create, $save = false){
        $this->setAttribut("timestp_create", self::TYPE_ATTRIBUT_NUMERIC, $timestp_create, $save);
    }

    public function setTimestpModify($timestp_modify, $save = false){
        $this->setAttribut("timestp_modify", self::TYPE_ATTRIBUT_NUMERIC, $timestp_modify, $save);
    }

    public function setIdFichierModele($id_fichier_modele, $save = false){
        $this->setAttribut("id_fichier_modele", self::TYPE_ATTRIBUT_KEY, $id_fichier_modele, $save);
    }

    public function setNbelements($nbelements, $save = false){
        $this->setAttribut("nbelements", self::TYPE_ATTRIBUT_NUMERIC, $nbelements, $save);
    }

    public function setRefTmpTable($ref_tmp_table, $save = false){
        $this->setAttribut("ref_tmp_table", self::TYPE_ATTRIBUT_STRING, $ref_tmp_table, $save);
    }

    public function setIdUser($id_user, $save = false){
        $this->setAttribut("id_user", self::TYPE_ATTRIBUT_KEY, $id_user, $save);
    }

    public function setIdModule($id_module, $save = false){
        $this->setAttribut("id_module", self::TYPE_ATTRIBUT_KEY,$id_module, $save);
    }

    public function setIdWorkspace($id_workspace, $save = false){
        $this->setAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY, $id_workspace, $save);
    }
}

?>
