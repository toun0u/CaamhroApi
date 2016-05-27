<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class dims_action_intercom extends dims_data_object {
	public $db;						// connector to database abstraction layer : Mysql, Oracle

	function __construct($db){
		parent::dims_data_object('dims_action_intercom');
		$this->db=$db;
	}

}
?>
