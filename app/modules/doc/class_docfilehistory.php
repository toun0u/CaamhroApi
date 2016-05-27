<?

class docfilehistory extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_mod_doc_file_history', 'id_docfile', 'version');
	}

	function getbasepath() {
		$basepath = doc_getpath($this->fields['id_module'],true)._DIMS_SEP.substr($this->fields['timestp_create'],0,8);

		$basephyspath = realpath(doc_getpath($this->fields['id_module'],true))._DIMS_SEP.substr($this->fields['timestp_create'],0,8);
		dims_makedir($basephyspath);
		dims_makedir($basepath);

		return($basepath);
	}

	function getfilepath() {
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id_docfile']}_{$this->fields['version']}.{$this->fields['extension']}");
	}

	public function delete() {
		$filepath = $this->getfilepath();
		if (file_exists($filepath)) unlink($filepath);

		$this->deletePreview();

		parent::delete();
	}

	public function deletePreview() {
		// suppression du preview
		$encoded=md5($this->getfilepath());
		$tmpdir=_DIMS_PATHDATA."/preview/".$encoded."/";

		if (file_exists($tmpdir)) dims_deletedir($tmpdir);
	}
}
