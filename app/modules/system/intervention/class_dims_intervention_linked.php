<?php

/**
 * Description of dims_intervention_linked
 *
 * @author Netlor
 * @copyright Wave Software / Netlor 2011
 */
class dims_intervention_linked extends dims_data_object{
    const TABLE_NAME = "dims_intervention_linked";

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getIdGlobalobjectLinked() {
        return $this->getAttribut("id_globalobject_linked", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdIntervention() {
        return $this->getAttribut("id_intervention", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdTypeLink() {
        return $this->getAttribut("id_type_link", self::TYPE_ATTRIBUT_KEY);
    }

    public function setIdGlobalobjectLinked($id_globalobject_linked, $save = false){
        $this->setAttribut("id_globalobject_linked", self::TYPE_ATTRIBUT_KEY, $id_globalobject_linked, $save);
    }

    public function setIdIntervention($id_intervention, $save = false){
        $this->setAttribut("id_intervention", self::TYPE_ATTRIBUT_KEY, $id_intervention, $save);
    }

    public function setIdTypeLink($id_type_link, $save = false){
        $this->setAttribut("id_type_link", self::TYPE_ATTRIBUT_KEY, $id_type_link, $save);
    }
}

?>
