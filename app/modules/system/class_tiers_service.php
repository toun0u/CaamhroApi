<?php

class tiers_service extends dims_data_object {

	const MY_GLOBALOBJECT_CODE = dims_const::_SYSTEM_OBJECT_TIERS_SERVICE;//valeur du type de globalobject pour les tiers_services
	const TABLE_NAME = "dims_mod_business_tiers_service";

	private $tiers = null;
	private $children = array();
	private $depth = 1;

	const SERVICE_ROOT = 0;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	//redéfinition des fonctions liées aux globalobjects (FROM DDO)
	function save(){
		parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	function create($label, $description = '', $tiers_id = 0, $parent_id=0){
		$this->init_description();
		$this->setugm();
		$this->setLabel($label);
		$this->setDescription($description);
		$this->setTiersID($tiers_id);
		$this->setParentID($parent_id);
		$this->save();
	}
	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}
	function settitle(){
		$this->title = $this->getLabel();
	}

	public function setLabel($label){
		$this->fields['label'] = $label;
	}

	public function getLabel(){
		return $this->fields['label'];
	}

	public function setDescription($val){
		$this->fields['description'] = $val;
	}

	public function getDescription(){
		return $this->fields['description'];
	}

	public function setParentID($parent){
		$this->fields['id_parent'] = $parent;
	}

	public function getParentID(){
		return $this->fields['id_parent'];
	}

	public function setTiersID($tiers){
		$this->fields['id_tiers'] = $tiers;
	}

	public function getTiersID(){
		return $this->fields['id_tiers'];
	}

	public function getTiers(){
		if(!isset($this->tiers) && $this->fields['id_tiers'] > 0){
			$tiers = new tiers();
			$tiers->open($this->fields['id_tiers']);
		}
		return $tiers;
	}

	public function getRootReference(){
		$tiers = $this->getTiers();
		return $tiers->getMyRootServiceID();
	}

	public function hasChildren(){
		return count($this->children) > 0;
	}

	public function getChildren(){
		return $this->children;
	}

	public function addChild($child){
		$child->setDepth($this->getDepth() + 1);
		$this->children[] = $child;
	}

	//fonction pratique pour appliquer par exemple un modèle de services à un client
	public function createChild($label){
		$child = $this->getInstance();
		$child->create($label, $this->getTiersID(), $this->getId());
		$this->addChild($child);
		return $child;
	}

	public function setDepth($dpt){
		$this->depth = $dpt;
	}

	public function getDepth(){
		return $this->depth;
	}

	public function getInstance(){
		return new tiers_service();
	}

	public function initDescendance(){
		$tiers_id = $this->getTiersID();
		if(isset($tiers_id) && $tiers_id > 0){
			//initialisation d'un tableau des services du tiers pour ne pas avoir 50 requêtes SQL à faire
			$sql = "SELECT * FROM ".self::TABLE_NAME." WHERE id_tiers= :idtiers ";
			$res = $this->db->query($sql, array(
				':idtiers' => $this->getTiersID()
			));
			$family = array();
			while($elem = $this->db->fetchrow($res)){
				$service = $this->getInstance();
				$service->openFromResultSet($elem);
				$family[$service->getParentID()][$service->getId()] = $service;
			}

			$temp = array();
			$temp[] = $this;
			while(count($temp) > 0){
				$node = array_shift($temp);
				if(isset($family[$node->getId()])){
					foreach($family[$node->getId()] as $id => $service ){
						$node->addChild($service);
						$temp[] = $service;
					}
				}
			}
		}
	}

	public function isParentOf($id_service) {
		$isParent = false;

		// Initializing children
		if(empty($this->children)) {
			$this->initDescendance();
		}

		// Search for direct child
		foreach($this->children as $child) {
			if($child->getId() == $id_service) {
				$isParent = true;
				break; // Do not need to continue searching
			}
		}

		// Search deeper if not found
		if(!$isParent) {
			foreach($this->children as $child) {
				if($child->isParentOf($id_service)) {
					$isParent = true;
					break; // Do not need to continue searching
				}
			}
		}

		return $isParent;
	}
}
?>
