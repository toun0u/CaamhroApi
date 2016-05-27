<?php
require_once(DIMS_APP_PATH.'include/class_dims_data_object.php');
require_once(DIMS_APP_PATH.'modules/system/class_annotation_tag.php');
require_once(DIMS_APP_PATH.'modules/system/class_tag.php');

class annotation extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function annotation() {
		parent::dims_data_object('dims_annotation','id');
	}

	function save() {
		$db = dims::getInstance()->getDb();

		$id_tag = 0;

		$tags = preg_split('/(,)|( )/',$this->tags,-1,PREG_SPLIT_NO_EMPTY);

		$id_annotation = parent::save();

		foreach($tags as $tag) {
			$tag = trim($tag);

			$select = "SELECT id FROM dims_tag WHERE tag = :tag";
			$rs = $db->query($select, array(':tag' => array('type' => PDO::PARAM_STR, 'value' => $tag)));
			if (!($row = $db->fetchrow($rs))) {
				$objtag = new tag();
				$objtag->fields['tag'] = $tag;
				$id_tag = $objtag->save();
			} else {
				$id_tag = $row['id'];
			}

			$annotation_tag = new annotation_tag();
			$annotation_tag->fields['id_tag'] = $id_tag;
			$annotation_tag->fields['id_annotation'] = $id_annotation;
			$annotation_tag->save();
		}

		return($id_annotation);
	}


	function delete() {
		$db = dims::getInstance()->getDb();

		$sql = "SELECT	*
			FROM	dims_annotation_tag
			WHERE	id_annotation = :idannotation ";

		$rs = $db->query($sql, array(':idannotation' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id'])));
		while ($row = $db->fetchrow($rs)) {
			$annotation_tag = new annotation_tag();
			$annotation_tag->open($this->fields['id'], $row['id_tag']);
			$annotation_tag->delete();
		}

		parent::delete();
	}
}
