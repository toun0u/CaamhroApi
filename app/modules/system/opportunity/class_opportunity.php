<?php

require_once DIMS_APP_PATH."modules/system/class_matrix.php";
require_once DIMS_APP_PATH."modules/system/class_action.php";


class dims_opportunity extends action {

	const DATE_FORMAT_JJMMAAAA = 0;
	const DATE_FORMAT_MMAAAA = 1;
	const DATE_FORMAT_AAAA = 2;
	const TYPE_ACTION = "_DIMS_OPPORTUNITY";

	public function __construct() {
		parent::dims_data_object('dims_mod_business_action');
	}

	public function delete() {
		// Delete matrix elem
		$sql = 'DELETE FROM dims_matrix WHERE id_opportunity = :idopportunity ';

		$this->db->query($sql, array(
			':idopportunity' => $this->getId()
		));

		parent::delete();
	}

	public function initFolder(){
		if ($this->fields['id_folder'] == '' || $this->fields['id_folder'] <= 0){
			require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
			$tmstp = dims_createtimestamp();
			$fold = new docfolder();
			$fold->init_description();
			$fold->fields['name'] = 'root_'.$this->fields['id_globalobject'];
			$fold->fields['parents'] = 0;
			$fold->setugm();
			$fold->fields['timestp_create'] = $tmstp;
			$fold->save();
			$this->fields['id_folder'] = $fold->fields['id'];
			$fold->save(); // pr la synchro

			$sql="update dims_mod_business_action set id_folder=:id_folder where id=:id";

			$params=array();
			$params[':id_folder']= array('type' => PDO::PARAM_INT, 'value' => $fold->fields['id']);
			$params[':id']= array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']);
			//dims_print_r($params);
			$this->db->query($sql,$params);
			$this->save();
		}
	}
}
