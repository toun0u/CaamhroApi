<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class dims_object_change extends dims_data_object
{
	public function __construct() {
		parent::dims_data_object('dims_object_historic_changes', 'id');
	}

	public function initValues($id, $action_id, $field_name, $field_before, $field_after ){
		$this->fields['id'] = $id;
		$this->fields['action_id'] = $action_id;
		$this->fields['field_name'] = $field_name;
		$this->fields['field_before'] = $field_before;
		$this->fields['field_after'] = $field_after;
	}

	public function setActionID($id)
	{
		$this->fields['action_id'] = $id;
	}

	public function setFieldName($field)
	{
		$this->fields['field_name'] = $field;
	}

	public function getFieldName(){
		return $this->fields['field_name'];
	}

	public function setPreviousValue($value)
	{
		$this->fields['field_before'] = $value;
	}

	public function getPreviousValue(){
		return $this->fields['field_before'];
	}

	public function setNextValue($value)
	{
		$this->fields['field_after'] = $value;
	}

	public function getNextValue(){
		return $this->fields['field_after'];
	}
}

?>
