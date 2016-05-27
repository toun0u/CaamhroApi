<?php
class gescom_workflow extends dims_data_object{
	const TABLE_NAME = 'dims_gescom_workflow';

	public function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function delete(){
		// TODO: tester si ce worksflow est utilisé
		/*
		$steps = gescom_workflow_step::find_by(array('id_workflow'=>$this->get('id')));
		foreach($steps as $s){
			$s->delete();
		}
		parent::delete();
		*/
	}

	public static function selectorformat($workflowcollection) {
		$workflowslist = array(0 => '');

		foreach ($workflowcollection as $workflow) {
			$workflowslist[$workflow->get('id')] = $workflow->get('label');
		}

		return $workflowslist;
	}
}
