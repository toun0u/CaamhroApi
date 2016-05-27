<?php
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	forms
* @access  	public
*/

class reply_field extends dims_data_object {
	const TABLE_NAME = 'dims_mod_forms_reply_field';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function save() {
		return parent::save(_FORMS_OBJECT_REPLY);
	}

	function delete() {
		// delete attached file
		if($this->get('type') == 'file'){
			$path = _DIMS_PATHDATA.'forms-'.$this->fields['id_module']._DIMS_SEP.$this->fields['id_forms']._DIMS_SEP.$this->fields['id_reply']._DIMS_SEP;
			unlink($path.$this->fields['value']);
		}
		parent::delete(_FORMS_OBJECT_REPLY);
	}
}
