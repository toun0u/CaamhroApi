<?php
class article_kit extends dims_data_object {
	const TABLE_NAME = 'dims_mod_cata_article_kit';

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	function create($id_kit, $id_component, $qty){
		$this->init_description(true);
		$this->setugm();
		$this->setKitId($id_kit);
		$this->setComponentId($id_component);
		$this->setQuantity($qty);

		return $this->save();
	}

	public function setKitId($val){
		$this->fields['id_article_kit'] = $val;
	}
	public function setComponentId($val){
		$this->fields['id_article_attach'] = $val;
	}
	public function setQuantity($val){
		$this->fields['quantity'] = $val;
	}

	public function getKitId(){
		return $this->fields['id_article_kit'];
	}
	public function getComponentId(){
		return $this->fields['id_article_attach'];
	}
	public function getQuantity(){
		return $this->fields['quantity'];
	}
}
