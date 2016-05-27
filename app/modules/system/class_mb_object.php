<?php
require_once DIMS_APP_PATH.'modules/system/class_mb_class.php';

class mb_object extends dims_data_object {
	/**
	* Class constructor
	*
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mb_object', 'id', 'id_module_type');
	}

	public static function create($id_module_type, $id, $obj, $script = '', $label = null){
		$mbo = new mb_object();
		$mbo->fields['id'] = $id;
		$mbo->fields['id_module_type'] = $id_module_type;
		$id_class = 0;
		if(isset($obj)){
			$class = new mb_class();
			$class = $class->openWithObject($obj);
			if( ! $class->isNew() ){
				$id_class = $class->getId();
			}
		}
		$mbo->fields['id_class'] = $id_class;

		$mbo->fields['script'] = $script;
		if(!isset($label)) $mbo->fields['label'] = 'Dims auto-registration / '.get_class($obj);
		else  $mbo->fields['label'] = $label;

		$mbo->save();
		return $mbo;
	}
}
?>
