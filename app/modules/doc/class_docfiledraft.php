<?
require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';

class docfiledraft extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_mod_doc_file_draft');
		$this->fields['timestp_create'] = dims_createtimestamp();
	}

	function save() {
		$db = dims::getInstance()->getDb();
		$error = 0;
		if (isset($this->fields['folder'])) unset($this->fields['folder']);

		if (!isset($this->oldname)) $this->oldname = '';

		if ($this->new) {// insert
			if ($this->tmpfile == 'none') $error = _DOC_ERROR_EMPTYFILE;

			if ($this->fields['size']>_DIMS_MAXFILESIZE) $error = _DOC_ERROR_MAXFILESIZE;

			if (!$error) {
				$this->fields['extension'] = substr(strrchr($this->fields['name'], "."),1);

				$id = parent::save();

				$basepath = $this->getbasepath();
				$filepath = $this->getfilepath();

				if (file_exists($filepath) && !is_writable($filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;

				if (!$error && is_writable($basepath) && move_uploaded_file($this->tmpfile, $filepath))
				{
					chmod($filepath, 0777);
				}
				else $error = _DOC_ERROR_FILENOTWRITABLE;
			}
		}
		return($error);
	}

	function getbasepath() {
		$basepath = doc_getpath($this->fields['id_module'])._DIMS_SEP.'drafts'._DIMS_SEP.$this->fields['id'];
		dims_makedir($basepath);
		return($basepath);
	}

	function getfilepath() {
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id']}.{$this->fields['extension']}");
	}

	function publish() {
		$docfile = new docfile();

		if ($this->fields['id_docfile']) {
			if ($docfile->open($this->fields['id_docfile'])) {
				$docfile->createhistory();
				$docfile->fields['name'] = $this->fields['name'];
				$docfile->fields['size'] = $this->fields['size'];
				$docfile->fields['description'] = $this->fields['description'];
				$docfile->fields['extension'] = $this->fields['extension'];
				$docfile->fields['id_user_modify'] = $this->fields['id_user_modify'];
				$docfile->fields['timestp_modify'] = $this->fields['timestp_create'];
				$docfile->draftfile = $this->getfilepath();
				$docfile->save();
			}
		}
		else {
			$docfile->fields = $this->fields;
			unset($docfile->fields['id']);
			unset($docfile->fields['id_docfile']);
			$docfile->fields['timestp_modify'] = $docfile->fields['timestp_create'];
			$docfile->fields['version'] = 1;
			$docfile->draftfile = $this->getfilepath();
			$docfile->save();
		}
		$this->delete();
	}
}
