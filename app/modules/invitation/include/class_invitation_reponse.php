<?php
class invitation_reponse extends dims_data_object {
	const TABLE_NAME = 'dims_mod_business_action_reponse';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function save(){
		$this->set('timestp',dims_createtimestamp());
		parent::save();
	}

	public function delete(){
		require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_accompany.php';
		$accs = invitation_accompany::find_by(array('id_reponse'=>$this->get('id')));
		foreach($accs as $a){
			$a->delete();
		}
		require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse_value.php';
		$repv = invitation_reponse_val::find_by(array('id_reponse'=>$this->get('id')));
		foreach($repv as $r){
			$r->delete();
		}
		return parent::delete();
	}
}
