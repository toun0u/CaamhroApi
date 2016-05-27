<?php

/**
 * Description of dims_history_user_object
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class dims_history_user_object extends dims_data_object{
    const TABLE_NAME = "dims_history_user_object";

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getIdGlobalObjectConcerned() {
        return $this->getAttribut("id_globalobject_concerned", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdModule() {
        return $this->getAttribut("id_module", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdWorkspace() {
        return $this->getAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdUser() {
        return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
    }

    public function getTmstp() {
        return $this->getAttribut("tmstp", self::TYPE_ATTRIBUT_NUMERIC);
    }

    public function getType() {
        return $this->getAttribut("type", self::TYPE_ATTRIBUT_NUMERIC);
    }

    public function setIdGlobalObjectConcerned($id_globalobject_concerned, $save = false){
        $this->setAttribut("id_globalobject_concerned", self::TYPE_ATTRIBUT_KEY, $id_globalobject_concerned, $save);
    }

    public function setIdModule($id_module, $save = false){
        $this->setAttribut("id_module", self::TYPE_ATTRIBUT_KEY, $id_module, $save);
    }

    public function setIdWorkspace($id_workspace, $save = false){
        $this->setAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY, $id_workspace, $save);
    }

    public function setIdUser($id_user, $save = false){
        $this->setAttribut("id_user", self::TYPE_ATTRIBUT_KEY, $id_user, $save);
    }

    public function setTmstp($tmstp, $save = false){
        $this->setAttribut("tmstp", self::TYPE_ATTRIBUT_NUMERIC, $tmstp, $save);
    }

    public function setType($type, $save = false){
        $this->setAttribut("type", self::TYPE_ATTRIBUT_NUMERIC, $type, $save);
    }

}

?>
