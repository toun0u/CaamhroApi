<?php
/**
 *
 * @author cyrilrouyer
 */
class category_module extends dims_data_object{

	public function __construct() {
		parent::dims_data_object('dims_category_module');
	}

	public function setCategory($val){
		$this->fields['id_category'] = $val;
	}

	public function getCategory(){
		return $this->fields['id_category'];
	}

	public function setModule($val){
		$this->fields['id_module'] = $val;
	}

	public function getModule(){
		return $this->fields['id_module'];
	}

	public function create($cat, $module){
		$this->init_description();
		$this->setCategory($cat);
		$this->setModule($module);
		$this->save();
	}
}
?>
