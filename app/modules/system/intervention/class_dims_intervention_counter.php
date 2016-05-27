<?php

/**
 * Description of dims_intervention_counter
 *
 * @author Netlor
 * @copyright Wave Software / Netlor 2011
 */
class dims_intervention_counter extends dims_data_object{
    const TABLE_NAME = "dims_intervention_counter";

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getIdTypeIntervention() {
        return $this->getAttribut("id_type_intervention", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdGlobalObject() {
        return $this->getAttribut("id_globalobject", self::TYPE_ATTRIBUT_KEY);
    }

    public function getCmpt() {
        return $this->getAttribut("cmpt", self::TYPE_ATTRIBUT_NUMERIC);
    }


    public function setIdTypeIntervention($id_type_intervention, $save = false){
        $this->setAttribut("id_type_intervention", self::TYPE_ATTRIBUT_KEY, $id_type_intervention, $save);
    }

    public function setIdGlobalobject($id_globalobject, $save = false){
        $this->setAttribut("id_globalobject", self::TYPE_ATTRIBUT_KEY, $id_globalobject, $save);
    }

    public function setCmpt($cmpt, $save = false){
        $this->setAttribut("cmpt", self::TYPE_ATTRIBUT_NUMERIC, $cmpt, $save);
    }

    public static function addIntervention($type_intervention, $id_globalobject){

        if($id_globalobject > 0 && $type_intervention > 0){
            $db = dims::getInstance()->getDb();

            $sql = "SELECT 	id
					FROM 	".self::TABLE_NAME."
					WHERE 	id_globalobject = :idglobalobject
					AND 	id_type_intervention = :idtypeintervention
					";

            $res = $db->query($sql, array(
                ':idglobalobject'      => $id_globalobject,
                ':idtypeintervention'   => $type_intervention
            ));
            $row = $db->fetchrow($res) ;
            if ($row) {
                $sql = "UPDATE 	".self::TABLE_NAME."
						SET 	cmpt=cmpt+1
						WHERE 	id= :id
						";
                $db->query($sql, array(
                    ':id' => $row['id']
                ));
            }else{
                $intervention_counter = new dims_intervention_counter();
                $intervention_counter->setCmpt(1);
                $intervention_counter->setIdGlobalobject($id_globalobject);
                $intervention_counter->setIdTypeIntervention($type_intervention);
                $intervention_counter->save();
            }
        }
    }
}

?>
