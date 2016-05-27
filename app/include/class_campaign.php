<?php
require_once(DIMS_APP_PATH.'include/class_dims_data_object.php');

class campaign extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function campaign() {
		parent::dims_data_object('dims_campaign','id');
	}

	function save() {
		parent::save();
	}

	function delete() {
		$this->deleteCorresp();
		parent::delete();
	}

	function deleteCorresp() {
		$db = dims::getInstance()->getDb();
		$res=$db->query("DELETE FROM dims_campaign_keyword WHERE id_campaign= :idcampaign", array(':idcampaign' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id'])));
	}

	function update() {
		$this->fields['state']=0;
	}
}
