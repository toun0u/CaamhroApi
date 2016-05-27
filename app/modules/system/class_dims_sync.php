<?php
class dims_sync extends pagination {
	const TABLE_NAME = "dims_sync";
	const MY_GLOBALOBJECT_CODE = 32;//valeur du type de globalobject pour les modèles
	const DIMS_CURRENT = 1;
	const DIMS_REMOTE = 0;
	const PERSONAL_DATA_SHARING_AVAILABLE = 1;
	const PERSONAL_DATA_SHARING_DISABLED = 0;

	public function __construct() { //ça c'est pour que ça enregistre automatiquement le mb_object_correspondant
		parent::dims_data_object(self::TABLE_NAME, 'id');
		$this->belongs_to('tiers'	, tiers::TABLE_NAME, 'id_tiers', 'id');
	}

	//redéfinition des fonctions liées aux globalobjects (FROM DDO)
	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}


	public function create($label, $id_tiers, $allow_personal_sharing = self::PERSONAL_DATA_SHARING_AVAILABLE, $current=self::DIMS_REMOTE, $frequency = 300, $key = ''){
		$this->init_description();
		$this->setugm();
		$this->setLabel($label);
		$this->setTiersID($id_tiers);
		$this->setPersonalSharing($allow_personal_sharing);
		$this->setCurrent($current);
		$this->setFrequency($frequency);
		//on précise la racine de référence
		$this->setRootReference(dims::getInstance()->getCurrentDimsID());
		if(!empty($key))
			$this->setKey($key);
		else $this->setKey($this->gen_key());
		$id_dims =  $this->save();
		return $id_dims;
	}

	public function setLabel($value){
		$this->fields['label'] = $value;
	}

	public function getLabel(){
		return $this->fields['label'];
	}

	public function setTiersID($value){
		$this->fields['id_tiers'] = $value;
	}

	public function getTiersID(){
		return $this->fields['id_tiers'];
	}

	public function setPersonalSharing($value){
		$this->fields['allow_personal_sharing'] = $value;
	}

	public function getPersonalSharing(){
		return $this->fields['allow_personal_sharing'];
	}

	public function isPersonalSharingAvailable(){
		return $this->getPersonalSharing() == self::PERSONAL_DATA_SHARING_AVAILABLE;
	}

	public function setRootReference($value){
		$this->fields['root_reference'] = $value;
	}

	public function getRootReference(){
		return $this->fields['root_reference'];
	}

	public function setCurrent($value){
		$this->fields['current'] = $value;
	}

	public function isCurrent(){
		return $this->fields['current'] == self::DIMS_CURRENT;
	}

	public function setFrequency($value){
		$this->fields['frequency'] = $value;
	}

	public function getFrequency(){
		return $this->fields['frequency'];
	}

	public function setKey($value){
		$this->fields['key'] = $value;
	}

	public function getKey(){
		return $this->fields['key'];
	}

	private function gen_key() {
		return md5(dechex(time()).dechex(mt_rand(1,65535)));
	}

	public static function getCurrentDimsID(){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT id FROM ".self::TABLE_NAME." WHERE current = :current LIMIT 0,1", array(':current' => self::DIMS_CURRENT ) );
		if($db->numrows($res)){
			$field = $db->fetchrow($res);
			return $field['id'];
		}
		else return 0;
	}

	public static function getCurrentRootDimsID(){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT root_reference, id FROM ".self::TABLE_NAME." WHERE current = :current LIMIT 0,1", array(':current' => self::DIMS_CURRENT ) );
		if($db->numrows($res)){
			$fields = $db->fetchrow($res);
			if($fields['root_reference'] == 0) return $fields['id'];//particularité pour la racine principale
			else return $fields['root_reference'];
		}
		else return 0;
	}

	public static function getIDForTiers($id_tiers){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT id FROM ".self::TABLE_NAME." WHERE id_tiers = :idtier LIMIT 0,1", array(
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $id_tiers),
		));
		if($db->numrows($res)){
			$field = $db->fetchrow($res);
			return $field['id'];
		}
		else return 0;
	}
}
