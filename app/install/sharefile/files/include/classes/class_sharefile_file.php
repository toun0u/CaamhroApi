<?php

include_once("./common/modules/doc/class_docfile.php");

class sharefile_file extends dims_data_object {
	const TABLE_NAME = 'dims_mod_sharefile_file';

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id_share','id_doc');
	}

	function delete() {
		global $db;
		$doc = new docfile();
		$doc->open($this->fields['id_doc']);
		$doc->delete();
		parent::delete();
	}

	public static function openByLink($id_share, $id_doc) {
		$db = dims::getInstance()->getDb();

		$sql = 'SELECT * FROM '.self::TABLE_NAME.' WHERE id_share = :idshare AND id_doc = :iddoc ';

		$res = $db->query($sql,array(':idshare' => $id_share, ':iddoc' => $id_doc));

		$share_file = new self();
		if($db->numrows($res)) {
			$data = $db->fetchrow($res);
			$share_file->openFromResultSet($data);
		}
		else {
			$share_file->init_description();
			$share_file->fields['id_share'] = $id_share;
			$share_file->fields['id_doc'] 	= $id_doc;
			$share_file->fields['download'] = 0;
		}

		return $share_file;
	}
}
