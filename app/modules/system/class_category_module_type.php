<?php
/**
 *
 * @author cyrilrouyer
 */
class category_module_type extends dims_data_object{

	public function __construct() {
		parent::dims_data_object('dims_category_module_type');
	}

	public function setCategory($val){
		$this->fields['id_category'] = $val;
	}

	public function getCategory(){
		return $this->fields['id_category'];
	}

	public function setModuleType($val){
		$this->fields['id_module_type'] = $val;
	}

	public function getModuleType(){
		return $this->fields['id_module_type'];
	}

	public function create($cat, $moduletype){
		$this->init_description();
		$this->setCategory($cat);
		$this->setModuleType($moduletype);
		$this->save();
	}
}
?>
