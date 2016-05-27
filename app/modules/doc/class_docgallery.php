<?php
class docgallery extends dims_data_object {

	/**
	* Class constructor
	*
	* @access public
	**/
	function docgallery() {
		parent::dims_data_object('dims_mod_doc_gallery');
		$this->fields['timestp_create'] = dims_createtimestamp();
		$this->fields['timestp_modify'] = $this->fields['timestp_create'];
	}

	function save() {
		$db = dims::getInstance()->getDb();
		$id = parent::save();
		return ($id);
	}

	function delete() {
		parent::delete();
	}
}
