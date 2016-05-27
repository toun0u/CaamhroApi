<?php

/**
 * Description of dims_intervention
 *
 * @author Netlor
 * @copyright Wave Software / Netlor 2011
 */
class dims_intervention extends dims_data_object{
    const TABLE_NAME = "dims_intervention";
	const _INTERVENTION_IN = 1;
	const _INTERVENTION_OUT = 2;

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getIdTypeIntervention() {
        return $this->getAttribut("id_type_intervention", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdUser() {
        return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
    }

    public function getComment() {
        return $this->getAttribut("comment", self::TYPE_ATTRIBUT_STRING);
    }

    public function getTmstpRealized() {
        return $this->getAttribut("tmstp_realized", self::TYPE_ATTRIBUT_NUMERIC);
    }

    public function getInterventionGround() {
        return $this->getAttribut("intervention_ground", self::TYPE_ATTRIBUT_STRING);
    }

    public function getStatus() {
        return $this->getAttribut("status", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdContact() {
        return $this->getAttribut("id_contact", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdCase() {
        return $this->getAttribut("id_case", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdTodo() {
        return $this->getAttribut("id_todo", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdGlobalobject() {
        return $this->getAttribut("id_globalobject", self::TYPE_ATTRIBUT_KEY);
    }

    public function setIdTypeIntervention($id_type_intervention, $save = false){
        $this->setAttribut("id_type_intervention", self::TYPE_ATTRIBUT_KEY, $id_type_intervention, $save);
    }

    public function setIdUser($id_user, $save = false){
        $this->setAttribut("id_user", self::TYPE_ATTRIBUT_KEY, $id_user, $save);
    }

    public function setComment($comment, $save = false){
        $this->setAttribut("comment", self::TYPE_ATTRIBUT_STRING, $comment, $save);
    }

    public function setTmstpRealized($tmstp_realized, $save = false){
        $this->setAttribut("tmstp_realized", self::TYPE_ATTRIBUT_NUMERIC, $tmstp_realized, $save);
    }

    public function setInterventionGround($intervention_ground, $save = false){
        $this->setAttribut("intervention_ground", self::TYPE_ATTRIBUT_STRING, $intervention_ground, $save);
    }

    public function setStatus($status, $save = false){
        $this->setAttribut("status", self::TYPE_ATTRIBUT_KEY, $status, $save);
    }

    public function setIdContact($id_contact, $save = false){
        $this->setAttribut("id_contact", self::TYPE_ATTRIBUT_KEY, $id_contact, $save);
    }

    public function setIdCase($id_case, $save = false){
        $this->setAttribut("id_case", self::TYPE_ATTRIBUT_KEY, $id_case, $save);
    }

    public function setIdTodo($id_todo, $save = false){
        $this->setAttribut("id_todo", self::TYPE_ATTRIBUT_KEY, $id_todo, $save);
    }


	public function getLabel() {
		$interv_type = new dims_intervention_type();
		$interv_type->open($this->getIdTypeIntervention());

		return $interv_type->getText();
	}

	public static function getAllFromCase($id_case) {
		$db = dims::getInstance()->getDb();
		$sql = 'SELECT 	*
				FROM 	'.self::TABLE_NAME.'
				WHERE 	'.self::TABLE_NAME.'.id_case = :idcase ';

		$res = $db->query($sql, array(
            ':idcase' => $id_case
        ));

		$interv_list = array();
		while($info = $db->fetchrow($res)) {
			$interv = new dims_intervention();
			$interv->openWithFields($info);

			$interv_list[] = $interv;
		}
		return $interv_list;
	}

	public static function getAllFromContact($id_globalobject_contact) {
		$db = dims::getInstance()->getDb();
		$sql = 'SELECT 	*
				FROM 	'.self::TABLE_NAME.'
				WHERE 	'.self::TABLE_NAME.'.id_globalobject_ref = :idglobalobjectref ';

		$res = $db->query($sql, array(
            ':idglobalobjectref' => $id_globalobject_contact
        ));

		$interv_list = array();
		while($info = $db->fetchrow($res)) {
			$interv = new dims_intervention();
			$interv->openWithFields($info);

			$interv_list[] = $interv;
		}
		return $interv_list;
	}
}
?>
