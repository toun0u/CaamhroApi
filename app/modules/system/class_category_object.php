<?php
/**
 *
 * @author cyrilrouyer
 */
class category_object extends dims_data_object{
        const TABLE_NAME = 'dims_category_object';
	public function __construct() {
		parent::dims_data_object('dims_category_object');
	}

	public function setCategory($val){
		$this->fields['id_category'] = $val;
	}

	public function getCategory(){
		return $this->fields['id_category'];
	}

	public function setObject($id, $mt){
		$this->fields['id_object'] = $id;
		$this->fields['object_id_module_type'] = $mt;
	}

	public function create($cat, $id_object, $moduletype){
		$this->init_description();
		$this->setCategory($cat);
		$this->setObject($id_object, $moduletype);
		$this->save();
	}
}
?>
