<?php
require_once DIMS_APP_PATH.'include/class_documentsfile.php';

class documentsfolder extends dims_data_object {
	function documentsfolder() {
		parent::dims_data_object('dims_documents_folder');
		$this->fields['timestp_create'] = dims_createtimestamp();
		$this->fields['timestp_modify'] = $this->fields['timestp_create'];
		$this->fields['parents']=0;
	}

	function save() {
		if ($this->fields['id_folder'] != 0) {
			$docfolder_parent = new documentsfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$this->fields['parents'] = "{$docfolder_parent->fields['parents']},{$this->fields['id_folder']}";
			$ret = parent::save();
			$docfolder_parent->fields['nbelements'] = dims_documents_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}
		else $ret = parent::save();

		return ($ret);
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		// on recherche tous les fichiers pour les supprimer
		$rs = $db->query("SELECT id FROM dims_documents_file WHERE id_folder = :idfolder", array(
			':idfolder' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while($row = $db->fetchrow($rs)) {
			$file = new documentsfile();
			$file->open($row['id']);
			$file->delete();
		}

		// on recherche tous les dossiers fils pour les supprimer
		$rs = $db->query("SELECT id FROM dims_documents_folder WHERE id_folder = :idfolder", array(
			':idfolder' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while($row = $db->fetchrow($rs)) {
			$folder = new documentsfolder();
			$folder->open($row['id']);
			$folder->delete();
		}

		parent::delete();

		if ($this->fields['id_folder'] != 0) {
			$docfolder_parent = new documentsfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$docfolder_parent->fields['nbelements'] = dims_documents_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}

	}
}
