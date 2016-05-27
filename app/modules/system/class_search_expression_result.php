<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_search_expression_result
 *
 * @author cyrilrouyer
 */
class search_expression_result extends DIMS_DATA_OBJECT{

	function __construct() {
		parent::dims_data_object('dims_search_expression_result');
	}

	function initUserResults($id_user){
		$this->db->query("DELETE FROM ".$this->tablename." WHERE id_user = :iduser", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		));
		$this->db->query("DELETE FROM dims_search_expression_tag WHERE id_user = :iduser", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		));
		$this->initBulkRows();
	}

	function getUserId(){
		return $this->fields['id_user'];
	}

	function setUserId($val){
		$this->fields['id_user'] = $val;
	}

	function getObjectRef(){
		return $this->fields['id_globalobject_ref'];
	}

	function setObjectRef($val){
		$this->fields['id_globalobject_ref'] = $val;
	}

	function getType(){
		return $this->fields['type'];
	}

	function setType($val){
		$this->fields['type'] = $val;
	}

	function getFreshness(){
		return $this->fields['timestp_freshness'];
	}

	function setFreshness($val){
		$this->fields['timestp_freshness'] = $val;
	}

	function getSearchId(){
		return $this->fields['id_search'];
	}

	function setSearchId($val){
		$this->fields['id_user'] = $val;
	}
}
