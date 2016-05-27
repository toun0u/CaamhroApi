<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class address_link extends dims_data_object {
	const TABLE_NAME = 'dims_mod_business_address_link';

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function delete($o = true){
		require_once DIMS_APP_PATH.'modules/system/class_address.php';
		require_once DIMS_APP_PATH.'include/class_dims_globalobject.php';
		$a = address::find_by(array('id_globalobject'=>$this->get('id_goaddress')),null,1);
		$go = dims_globalobject::find_by(array('id'=>$this->get('id_goobject')),null,1);
		if(!empty($a)){
			require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			require_once DIMS_APP_PATH.'modules/system/class_contact.php';
			require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
			switch ($go->get('id_object')) {
				case contact::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_action'=>0,
						'id_opportunity'=>0,
						'id_tiers'=>0,
						'id_contact'=>$this->get('id_goobject'),
						'id_doc'=>0,
						'id_tiers2'=>0,
						'id_contact2'=>0,
						'id_case'=>0,
						'id_suivi'=>0,
						'id_address'=>$this->get('id_goaddress'),
						'timestp_end'=>0,
					),null,1);
					break;
				case tiers::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_action'=>0,
						'id_opportunity'=>0,
						'id_tiers'=>$this->get('id_goobject'),
						'id_contact'=>0,
						'id_doc'=>0,
						'id_tiers2'=>0,
						'id_contact2'=>0,
						'id_case'=>0,
						'id_suivi'=>0,
						'id_address'=>$this->get('id_goaddress'),
						'timestp_end'=>0,
					),null,1);
					break;
			}
			if(isset($m) && !empty($m)){
				$m->set('timestp_end',dims_createtimestamp());
				$m->save();
			}
		}
		if($o){
			$other = self::find_by(array('id_goaddress'=>$this->get('id_goaddress')));
			if(count($other) == 1){
				$adr = address::find_by(array('id_globalobject'=>$this->get('id_goaddress')),null,1);
				if(!empty($adr)){
					$adr->delete(false);
				}
			}
		}
		parent::delete();
	}

	public function save(){
		require_once DIMS_APP_PATH.'modules/system/class_address.php';
		require_once DIMS_APP_PATH.'include/class_dims_globalobject.php';
		$a = address::find_by(array('id_globalobject'=>$this->get('id_goaddress')),null,1);
		$go = dims_globalobject::find_by(array('id'=>$this->get('id_goobject')),null,1);
		if(!empty($a) && !empty($go)){
			require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			require_once DIMS_APP_PATH.'modules/system/class_contact.php';
			require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
			switch ($go->get('id_object')) {
				case contact::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_action'=>0,
						'id_opportunity'=>0,
						'id_tiers'=>0,
						'id_contact'=>$this->get('id_goobject'),
						'id_doc'=>0,
						'id_tiers2'=>0,
						'id_contact2'=>0,
						'id_case'=>0,
						'id_suivi'=>0,
						'id_address'=>$this->get('id_goaddress'),
						'timestp_end'=>0,
					),null,1);
					break;
				case tiers::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_action'=>0,
						'id_opportunity'=>0,
						'id_tiers'=>$this->get('id_goobject'),
						'id_contact'=>0,
						'id_doc'=>0,
						'id_tiers2'=>0,
						'id_contact2'=>0,
						'id_case'=>0,
						'id_suivi'=>0,
						'id_address'=>$this->get('id_goaddress'),
						'timestp_end'=>0,
					),null,1);
					break;
			}
			if(isset($m)){
				if(!empty($m)){
					if($m->get('id_city') != $a->get('id_city')){
						$m->set('timestp_end',dims_createtimestamp());
						$m->save();
						$m = new matrix();
						$m->init_description();
						$m->set('id_address',$this->get('id_goaddress'));
						$m->set('id_city',$a->get('id_city'));
						$m->set('id_country',$a->get('id_country'));
						$m->set('year',date('Y'));
						$m->set('month',date('m'));
						switch ($go->get('id_object')) {
							case contact::MY_GLOBALOBJECT_CODE:
								$m->set('id_contact',$this->get('id_goobject'));
								break;
							case tiers::MY_GLOBALOBJECT_CODE:
								$m->set('id_tiers',$this->get('id_goobject'));
								break;
						}
						$m->save();
					}
				}else{
					$m = new matrix();
					$m->init_description();
					$m->set('id_address',$this->get('id_goaddress'));
					$m->set('id_city',$a->get('id_city'));
					$m->set('id_country',$a->get('id_country'));
					$m->set('year',date('Y'));
					$m->set('month',date('m'));
					switch ($go->get('id_object')) {
						case contact::MY_GLOBALOBJECT_CODE:
							$m->set('id_contact',$this->get('id_goobject'));
							break;
						case tiers::MY_GLOBALOBJECT_CODE:
							$m->set('id_tiers',$this->get('id_goobject'));
							break;
					}
					$m->save();
				}
			}
		}
		return parent::save();
	}
}
