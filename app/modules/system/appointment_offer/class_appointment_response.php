<?php

class dims_appointment_response extends dims_data_object {
	const TABLE_NAME = 'dims_mod_business_action_reponse';
	public $reponses = array();

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function save(){
		$this->fields['timestp'] = dims_createtimestamp();
		parent::save();
	}

	public function saveReponses($lstDipo, $reponses = array()){
		if($this->fields['id'] > 0){
			$oldRep = $this->loadRep();
			require_once DIMS_APP_PATH."/modules/system/appointment_offer/class_appointment_response_value.php";
			$this->reponses = array();
			foreach($lstDipo as $prop){
				if(isset($oldRep[$prop->fields['id']])){
					if(in_array($prop->fields['id'],$reponses))
						$oldRep[$prop->fields['id']]->fields['presence'] = 1;
					else
						$oldRep[$prop->fields['id']]->fields['presence'] = 0;
					$oldRep[$prop->fields['id']]->save();
					$this->reponses[$oldRep[$prop->fields['id']]->fields['id_appointment']] = $oldRep[$prop->fields['id']];
				}else{
					$rep = new dims_appointment_response_val();
					$rep->init_description();
					$rep->fields['id_reponse'] = $this->fields['id'];
					$rep->fields['id_appointment'] = $prop->fields['id'];
					if(in_array($prop->fields['id'],$reponses))
						$rep->fields['presence'] = 1;
					else
						$rep->fields['presence'] = 0;
					$rep->save();
					$this->reponses[$rep->fields['id_appointment']] = $rep;
				}
			}
		}else{
			require_once DIMS_APP_PATH."/modules/system/appointment_offer/class_appointment_response_value.php";
			$this->reponses = array();
			foreach($lstDipo as $prop){
				$rep = new dims_appointment_response_val();
				$rep->init_description();
				$rep->fields['id_reponse'] = $this->fields['id'];
				$rep->fields['id_appointment'] = $prop->fields['id'];
				if(in_array($prop->fields['id'],$reponses))
					$rep->fields['presence'] = 1;
				else
					$rep->fields['presence'] = 0;
				$rep->save();
				$this->reponses[$rep->fields['id_appointment']] = $rep;
			}
		}
	}

	public function loadRep(){
		if(empty($this->reponses)){
			$this->reponses = array();
			require_once DIMS_APP_PATH."/modules/system/appointment_offer/class_appointment_response_value.php";
			$sel = "SELECT	*
					FROM	".dims_appointment_response_val::TABLE_NAME."
					WHERE	id_reponse = :idreponse ";
			$db = dims::getInstance()->db;
			$res = $db->query($sel, array(':idreponse' => $this->fields['id']) );
			while($r = $db->fetchrow($res)){
				$elem = new dims_appointment_response_val();
				$elem->openFromResultSet($r);
				$this->reponses[$r['id_appointment']] = $elem;
			}
		}
		return $this->reponses;
	}
}
?>