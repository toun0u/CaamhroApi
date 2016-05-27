<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_address_link.php';
require_once DIMS_APP_PATH.'modules/system/class_address_type.php';
require_once DIMS_APP_PATH.'modules/system/class_city.php';
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'/modules/system/class_region.php';
require_once DIMS_APP_PATH.'/modules/system/class_departement.php';
require_once DIMS_APP_PATH.'/modules/system/class_canton.php';
require_once DIMS_APP_PATH.'/modules/system/class_arrondissement.php';

class address extends dims_data_object {
	const TABLE_NAME = 'dims_mod_business_address';
	const MY_GLOBALOBJECT_CODE = 410;

	private $default = false;
	private $oldFields = array();

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function delete($o = true){
		if($o){
			$links = address_link::find_by(array('id_goaddress'=>$this->get('id_globalobject')));
			foreach($links as $link){
				$link->delete(false);
			}
		}

		require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
		$matrices = matrix::find_by(array(
			'id_action'=>0,
			'id_opportunity'=>0,
			'id_doc'=>0,
			'id_tiers2'=>0,
			'id_contact2'=>0,
			'id_case'=>0,
			'id_suivi'=>0,
			'id_address'=>$this->get('id_globalobject'),
			'timestp_end'=>0,
		));
		foreach($matrices as $m){
			$m->set('timestp_end',dims_createtimestamp());
			$m->save();
		}

		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	public function save() {
		// on calcule le code pays si non selectionne
		if ($this->fields['id_country']==0 || $this->fields['id_country']=='') $this->updateIdCountry();
		if(!($this->get('user_create') != '' && $this->get('user_create') > 0))
			$this->set('user_create',$_SESSION['dims']['userid']);
		if(!($this->get('timestp_create') != '' && $this->get('timestp_create') > 0))
			$this->set('timestp_create',dims_createtimestamp());

		// liens matrice
		if(!$this->isNew() && $this->get('id_city') != $this->oldFields['id_city']){
			require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			$matrices = matrix::find_by(array(
				'id_action'=>0,
				'id_opportunity'=>0,
				'id_doc'=>0,
				'id_tiers2'=>0,
				'id_contact2'=>0,
				'id_case'=>0,
				'id_suivi'=>0,
				'id_address'=>$this->get('id_globalobject'),
				'id_city'=>$this->oldFields['id_city'],
				'timestp_end'=>0,
			));
			foreach($matrices as $m){
				$m->set('timestp_end',dims_createtimestamp());
				$m->save();

				$m2 = new matrix();
				$m2->init_description();
				$m2->set('id',0);
				$m2->set('year',date('Y'));
				$m2->set('month',date('m'));
				$m2->set('id_contact',$m->get('id_contact'));
				$m2->set('id_tiers',$m->get('id_tiers'));
				$m2->set('id_country',$this->get('id_country'));
				$m2->set('id_address',$this->get('id_globalobject'));
				$m2->set('id_city',$this->get('id_city'));
				$m2->save();
			}
		}

		// on sauve l'adresse
		$return = parent::save(self::MY_GLOBALOBJECT_CODE);

		$this->oldFields = $this->fields;
		return $return;
	}

	/* fct open */
	private function setOldFields(){
		$this->oldFields = $this->fields;
	}
	public function open(){
		$id = func_get_arg(0);
		parent::open($id);
		$this->setOldFields();
	}
	public function openWithFields($fields, $unset_db = false) {
		parent::openWithFields($fields,$unset_db);
		$this->setOldFields();
	}
	public function openFromResultSet($fields, $unset_db = false, $go_object_value = null) {
		parent::openWithFields($fields,$unset_db,$go_object_value);
		$this->setOldFields();
	}
	public function openWithGB($id) {
		$return = parent::openWithGB($id);
		$this->setOldFields();
		return $return;
	}
	public static function find_by($search, $order_by = null, $limit_start = null, $limit_qte = null, $force_database = null){
		$res = parent::find_by($search, $order_by, $limit_start, $limit_qte, $force_database);
		if(!empty($res) && !is_array($res)){
			$res->setOldFields();
		}
		return $res;
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = $this->get('address');
	}

	public function updateIdCountry($countryArray = array()) {
		if(empty($countryArray)) {
			// conversion des tags vers country
			$resu=$this->db->query('SELECT * FROM dims_country');
			$c=0;
			if ($this->db->numrows($resu)>0) {
				while($a = $this->db->fetchrow($resu)) {
					$countryArray[strtoupper($a['printable_name'])]=$a['id'];
					$countryArray[strtoupper($a['fr'])]=$a['id'];
				}
			}
		}

		$id_country = 0;
		if ($this->fields['country']!='') {
			// traitement des pays type France - Europe

			$lieux=str_replace(array("-",";"),",",$this->fields['country']);

			$alieux=explode(',',$lieux);

			foreach ($alieux as $lieu) {
				//echo $lieu." ";
				$lieu=trim($lieu);
				$wordlength=strlen($lieu);

				if ($wordlength>0) {
				if (isset($countryArray[strtoupper($lieu)])) {
					$id_country=$countryArray[strtoupper($lieu)];
				}
				else {
					// recherche du pays pour ct / entreprise
					foreach ($countryArray as $country=>$idc) {
						$res = similar_text(trim(strtoupper($lieu)) ,substr($country,0,$wordlength),$percent);

						if ($percent>=80) {
							$id_country=$idc; // on a trouve le pays
							break;
						}
					}
				}
				}
			}

			$this->fields['id_country']=$id_country;
		}
		return $id_country;
	}

	public function addLink($go, $type=0, $default=false){
		if(!$this->isNew()){
			$lk = new address_link();
			$lk->init_description();
			$lk->set('id_goaddress',$this->get('id_globalobject'));
			$lk->set('id_goobject',$go);
			$lk->set('id_type',$type);
			$lk->set('default',$default);
			$lk->save();
			return $lk;
		}
		return false;
	}

	public function delLink($go){
		$lk = address_link::find_by(array('id_goaddress'=>$this->get('id_globalobject'),'id_goobject'=>$go),null,1);
		if(!empty($lk)){
			$lk->delete();
		}
	}

	public function getLinkCt($go){
		if(!$this->isNew()){
			$lk = address_link::find_by(array('id_goaddress'=>$this->get('id_globalobject'), 'id_goobject'=>$go),null,1);
			if(!empty($lk))
				return $lk;
		}
		return null;
	}

	public function setIsDefault($is){
		$this->default = $is;
	}
	public function getIsDefault(){
		return $this->default;
	}

	public function getCity(){
		require_once DIMS_APP_PATH.'modules/system/class_city.php';
		$city = new city();
		if(!$this->isNew() && $this->get('id_city') != '' && $this->get('id_city') > 0){
			$city->open($this->get('id_city'));
		}else{
			$city->init_description();
		}
		return $city;
	}
	public function getCountry(){
		require_once DIMS_APP_PATH.'modules/system/class_country.php';
		$country = new country();
		if(!$this->isNew() && $this->get('id_country') != '' && $this->get('id_country') > 0){
			$country->open($this->get('id_country'));
		}else{
			$country->init_description();
		}
		return $country;
	}

	public static function getAddressesFromGo($go){
		$lst = array();
		if(is_array($go) && !empty($go)){
			$db = dims::getInstance()->getDb();
			$parms = array();
			$sel = "SELECT 		a.*, lk.id_type, lk.id_goobject, lk.phone, lk.email, lk.fax
					FROM 		".self::TABLE_NAME." a
					INNER JOIN 	".address_link::TABLE_NAME." lk
					ON 			lk.id_goaddress = a.id_globalobject
					WHERE 		lk.id_goobject IN (".$db->getParamsFromArray($go, 'goo', $params).")";
			$res = $db->query($sel,$params);
			$res2 = $db->split_resultset($res);
			foreach($res2 as $r){
				if(isset($lst[$r['a']['id']])){
					$attr = $lst[$r['a']['id']]->getLightAttribute('attached');
					$attr[$r['lk']['id_type']][] = $r['lk']['id_goobject'];
					$lst[$r['a']['id']]->setLightAttribute('attached',$attr);
				}else{
					$address = new address();
					$address->openFromResultSet($r['a']);
					$address->setLightAttribute('attached',array($r['lk']['id_type']=>array($r['lk']['id_goobject'])));
					$address->setLightAttribute('phone',$r['lk']['phone']);
					$address->setLightAttribute('email',$r['lk']['email']);
					$address->setLightAttribute('fax',$r['lk']['fax']);
					$lst[$r['a']['id']] = $address;
				}
			}
		}elseif($go != '' && $go > 0){
			$db = dims::getInstance()->getDb();
			$sel = "SELECT 		a.*, lk.id_type, lk.id_goobject, lk.phone, lk.email, lk.fax
					FROM 		".self::TABLE_NAME." a
					INNER JOIN 	".address_link::TABLE_NAME." lk
					ON 			lk.id_goaddress = a.id_globalobject
					WHERE 		lk.id_goobject = :go";
			$params = array(
				':go'=>array('value'=>$go,'type'=>PDO::PARAM_INT),
			);
			$res = $db->query($sel,$params);
			$res2 = $db->split_resultset($res);
			foreach($res2 as $r){
				$address = new address();
				$address->openFromResultSet($r['a']);
				$address->setLightAttribute('attached',array($r['lk']['id_type']=>array($r['lk']['id_goobject'])));
				$address->setLightAttribute('phone',$r['lk']['phone']);
				$address->setLightAttribute('email',$r['lk']['email']);
				$address->setLightAttribute('fax',$r['lk']['fax']);
				$lst[$r['a']['id']] = $address;
			}
		}
		return $lst;
	}

	public function getLinkedObject($not = null){
		$lst = array();
		if(!$this->isNew()){
			$db = dims::getInstance()->getDb();
			$params = array(
				':go' => array('value'=>$this->get('id_globalobject'),'type'=>PDO::PARAM_INT),
			);
			$sel = "SELECT 		DISTINCT g.*
					FROM 		".dims_globalobject::TABLE_NAME." g
					INNER JOIN 	".address_link::TABLE_NAME." lk
					ON 			lk.id_goobject = g.id
					WHERE 		lk.id_goaddress = :go";
			if(!is_null($not)){
				if(is_array($not)){
					$sel .= " AND 	lk.id_goobject NOT IN (".$db->getParamsFromArray($not, 'goo', $params).") ";
				}else{
					$sel .= " AND 	lk.id_goobject != :goo ";
					$params[':goo'] = array('value'=>$not,'type'=>PDO::PARAM_INT);
				}
			}
			$res = $db->query($sel,$params);
			while($r = $db->fetchrow($res)){
				$go = new dims_globalobject();
				$go->openFromResultSet($r);
				$lst[$go->get('id_object')][] = $go;
			}
		}
		return $lst;
	}

	public function getCreatedBy(){
		$user = new user();
		if($this->get('user_create') != '' && $this->get('user_create') > 0){
			$user->open($this->get('user_create'));
		}else{
			$user->init_description();
		}
		return $user;
	}

	public function getPrintableCreatedDate($full = false){
		$dd = dims_timestp2local($this->get('timestp_create'));
		if($full)
			return $dd['date']." ".$dd['time'];
		else
			return $dd['date'];
	}

	public function searchSimilar(){
		$lst = array();
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 	*
				FROM 	".self::TABLE_NAME."
				WHERE 	id_workspace = :idw
				AND 	id_city = :id_city
				AND 	id_country = :id_country";
				//AND 	address LIKE :add";
		$params = array(
			':idw' => array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
			':id_city' => array('value'=>$this->get('id_city'),'type'=>PDO::PARAM_INT),
			':id_country' => array('value'=>$this->get('id_country'),'type'=>PDO::PARAM_INT),
			//':add' => array('value'=>"%".$this->get('address')."%",'type'=>PDO::PARAM_INT),
		);
		$res = $db->query($sel,$params);
		while($r = $db->fetchrow($res)){
			$addr = strtoupper($this->get('address'));
			$lev = levenshtein($addr, strtoupper($r['address']));
			$coef = $lev - (ceil(strlen($addr)/6));
			if($coef < 4){
				$ad = new address();
				$ad->openFromResultSet($r);
				$lst[$ad->get('id')] = $ad;
			}
		}
		return $lst;
	}
}
