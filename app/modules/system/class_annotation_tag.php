<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class annotation_tag extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_annotation_tag','id_annotation','id_tag');
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		$select =	"
					SELECT	count(*) as c
					FROM	dims_annotation_tag
					WHERE	id_tag = :idtag
					AND		id_annotation <> :idannotation
					";

		$rs = $db->query($select, array(
			':idtag' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_tag']),
			':idannotation' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_annotation']),
		));
		if (!($row = $db->fetchrow($rs)) || $row['c'] == 0) {
			$tag = new tag();
			$tag->open($this->fields['id_tag']);
			$tag->delete();
		}

		parent::delete();
	}
}
