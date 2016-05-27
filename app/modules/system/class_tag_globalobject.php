<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class tag_globalobject extends dims_data_object {
	const TABLE_NAME = 'dims_tag_globalobject';
	const MY_GLOBALOBJECT_CODE = '341';
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id_tag','id_globalobject');
	}

	public function delete(){
		require_once DIMS_APP_PATH.'modules/system/class_tag.php';
		$tag = tag::find_by(array('id'=>$this->get('id_tag')),null,1);
		require_once DIMS_APP_PATH.'include/class_dims_globalobject.php';
		$go = dims_globalobject::find_by(array('id'=>$this->get('id_globalobject')),null,1);
		if(!empty($tag) && !empty($go)){
			require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			require_once DIMS_APP_PATH.'modules/system/class_contact.php';
			require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
			require_once DIMS_APP_PATH.'modules/system/class_city.php';
			switch ($go->get('id_object')) {
				case contact::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_tiers'=>0,
						'id_contact'=>$this->get('id_globalobject'),
						'id_tag'=>$tag->get('id'),
						'timestp_end'=>0,
					),null,1);
					if(!empty($m)){
						$m->set('timestp_end',dims_createtimestamp());
						$m->save();
					}
					break;
				case tiers::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_tiers'=>$this->get('id_globalobject'),
						'id_contact'=>0,
						'id_tag'=>$tag->get('id'),
						'timestp_end'=>0,
					),null,1);
					if(!empty($m)){
						$m->set('timestp_end',dims_createtimestamp());
						$m->save();
					}
					break;

				case city::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_city'=>$this->get('id_globalobject'),
						'id_tag'=>$tag->get('id'),
						'timestp_end'=>0,
					),null,1);
					if(!empty($m)){
						$m->set('timestp_end',dims_createtimestamp());
						$m->save();
					}
					break;
			}
		}

		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	function save(){
		require_once DIMS_APP_PATH.'modules/system/class_tag.php';
		$tag = tag::find_by(array('id'=>$this->get('id_tag')),null,1);
		require_once DIMS_APP_PATH.'include/class_dims_globalobject.php';
		$go = dims_globalobject::find_by(array('id'=>$this->get('id_globalobject')),null,1);
		if(!empty($tag) && !empty($go)){
			require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			require_once DIMS_APP_PATH.'modules/system/class_contact.php';
			require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
			require_once DIMS_APP_PATH.'modules/system/class_city.php';
			switch ($go->get('id_object')) {
				case contact::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_tiers'=>0,
						'id_contact'=>$this->get('id_globalobject'),
						'id_tag'=>$tag->get('id'),
						'timestp_end'=>0,
					),null,1);
					if(empty($m)){
						$m = new matrix();
						$m->init_description();
						$m->set('year',date('Y'));
						$m->set('month',date('m'));
						$m->set('id_tag',$this->get('id_tag'));
						$m->set('id_contact',$this->get('id_globalobject'));
						$m->save();
					}
					break;
				case tiers::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_tiers'=>$this->get('id_globalobject'),
						'id_contact'=>0,
						'id_tag'=>$tag->get('id'),
						'timestp_end'=>0,
					),null,1);
					if(empty($m)){
						$m = new matrix();
						$m->init_description();
						$m->set('year',date('Y'));
						$m->set('month',date('m'));
						$m->set('id_tag',$this->get('id_tag'));
						$m->set('id_tiers',$this->get('id_globalobject'));
						$m->save();
					}
					break;
				case city::MY_GLOBALOBJECT_CODE:
					$m = matrix::find_by(array(
						'id_city'=>$this->get('id_globalobject'),
						'id_tag'=>$tag->get('id'),
						'timestp_end'=>0,
					),null,1);
					if(empty($m)){
						$m = new matrix();
						$m->init_description();
						$m->set('year',date('Y'));
						$m->set('month',date('m'));
						$m->set('id_tag',$this->get('id_tag'));
						$m->set('id_city',$this->get('id_globalobject'));
						$m->save();
					}
					break;
			}
		}
		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}
	function settitle(){
		$this->title = "Linking tag to object";
	}
	function openWithCouple($id_tag, $go_ref){
		$param = array(	':id_tag'=>array('value'=>$id_tag,'type'=>PDO::PARAM_INT),
						':id_globalobject'=>array('value'=>$go_ref,'type'=>PDO::PARAM_INT));
		$res = $this->db->query("	SELECT 	*
									FROM 	".self::TABLE_NAME."
									WHERE 	id_tag = :id_tag
									AND 	id_globalobject = :id_globalobject
									LIMIT 	0,1",$param);//par défaut, le couple id_tag & id_globalobject_ref est unique mais bon, sait-on jamais
		if($this->db->numrows($res)){
			$this->openFromResultSet($this->db->fetchrow($res));
		}
	}
}
?>
