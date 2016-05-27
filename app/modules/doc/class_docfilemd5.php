<?php
require_once DIMS_APP_PATH . '/modules/doc/class_docfilehistory.php';

class docfile extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	var $oldname;
	var $tmpfile;
	var $draftfile;

	function __construct() {
		parent::dims_data_object('dims_mod_doc_file');
		$this->fields['id_user'] = 0;
		$this->fields['timestp_create'] = dims_createtimestamp();
		$this->fields['timestp_modify'] = $this->fields['timestp_create'];
		$this->fields['description']='';
		$this->fields['size'] = 0;
		$this->fields['version'] = 1;
		$this->fields['nbclick'] = 0;

		$this->oldname = '';
		$this->tmpfile = 'none';
		$this->draftfile = 'none';
	}

	function open($id) {
		$res = parent::open($id);
		$this->oldname = $this->fields['name'];
		return($res);
	}

	function save() {
		$db = dims::getInstance()->getDb();
		$error = 0;
		if (isset($this->fields['folder'])) unset($this->fields['folder']);

		if (!isset($this->oldname)) $this->oldname = '';

		if ($this->new) {// insert
			if ($this->tmpfile == 'none' && $this->draftfile == 'none') $error = _DOC_ERROR_EMPTYFILE;

			if ($this->fields['size']>_DIMS_MAXFILESIZE) $error = _DOC_ERROR_MAXFILESIZE;

			if (!$error) {
				$this->fields['extension'] = substr(strrchr($this->fields['name'], "."),1);
				$id = parent::save();
				$this->fields['md5'] = md5(sprintf("%s_%d_%d",$this->fields['timestp_create'],$id,$this->version));
				parent::save();

				$basepath = $this->getbasepath();
				$filepath = $this->getfilepath();

				if (file_exists($filepath) && !is_writable($filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;

				if (!$error && is_writable($basepath)) {
					if ($this->draftfile != 'none')
					{
						if (!rename($this->draftfile, $filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;
					}
					elseif ($this->tmpfile != 'none') {
						if (!move_uploaded_file($this->tmpfile, $filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;
					}

					if (!$error) {
						chmod($filepath, 0777);
						$this->getcontent();
					}
				}
				else $error = _DOC_ERROR_FILENOTWRITABLE;
			}

		}
		else {// update
			if ((!empty($this->tmpfile) && $this->tmpfile != 'none') || (!empty($this->draftfile) && $this->draftfile != 'none')) {
				$this->fields['version']++;

				if ($this->fields['size']>_DIMS_MAXFILESIZE) $error = _DOC_ERROR_MAXFILESIZE;

				if (!$error) {
					$this->fields['extension'] = substr(strrchr($this->fields['name'], "."),1);

					$basepath = $this->getbasepath();
					$filepath = $this->getfilepath();

					//$filepath_vers = $basepath._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}.{$this->fields['extension']}";

					if (file_exists($filepath) && !is_writable($filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;

					if (!$error) {
						// on copie le nouveau
						if (!$error && is_writable($basepath)) {
							if ($this->draftfile != 'none') {
								if (rename($this->draftfile, $filepath)) {
									chmod($filepath, 0777);
									$this->getcontent();
								}
								else $error = _DOC_ERROR_FILENOTWRITABLE;
							}
							if ($this->tmpfile != 'none') {
								if (move_uploaded_file($this->tmpfile, $filepath)) {
									chmod($filepath, 0777);
									$this->getcontent();
								}
								else $error = _DOC_ERROR_FILENOTWRITABLE;
							}
						}
						else $error = _DOC_ERROR_FILENOTWRITABLE;
					}
				}

				$this->fields['timestp_modify'] = dims_createtimestamp();
				$this->oldname = $this->fields['name'];
			}

			// renommage
			if ($this->oldname != $this->fields['name']) {
				// renommage avec modification de type
				if (($newext = substr(strrchr($this->fields['name'], "."),1)) != $this->fields['extension'])  {
					$basepath = $this->getbasepath();
					$filepath = $this->getfilepath();
					$newfilepath = substr($filepath,0,strlen($filepath)-strlen($this->fields['extension'])).$newext;

					if (file_exists($filepath) && is_writable($basepath)) {
						rename($filepath, $newfilepath);
						$this->fields['extension'] = $newext;
						$this->getcontent();
						parent::save();
					}
					else $error = _DOC_ERROR_FILENOTWRITABLE;
				}
				else {
					$this->getcontent();
					parent::save();
				}
			}
			else {
				parent::save();
			}
		}

		if ($this->fields['id_folder'] != 0) {
			$docfolder_parent = new docfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$docfolder_parent->fields['nbelements'] = doc_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}

		return($error);
	}


	function delete() {
		$filepath = $this->getfilepath();
		if (file_exists($filepath)) unlink($filepath);

		$basepath = $this->getbasepath();
		if (file_exists($basepath)) rmdir($basepath);

		parent::delete();

		if ($this->fields['id_folder'] != 0) {
			$docfolder_parent = new docfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$docfolder_parent->fields['nbelements'] = doc_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}
		die();
	}

	function getbasepath() {
		$basepath = doc_getpath($this->fields['id_module'])._DIMS_SEP.substr($this->fields['timestp_create'],0,8);
		dims_makedir($basepath);
		return($basepath);
	}

	function getfilepath() {
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}.{$this->fields['extension']}");
		//return($this->getbasepath()._DIMS_SEP.md5(sprintf("%s%d",$this->fields['timestp_create'],$this->fields['id'],$this->fields['version'])));
	}

	function getwebpath() {
		return(_DIMS_WEBPATHDATA."doc-{$this->fields['id_module']}/{$this->fields['id']}/{$this->fields['id']}_{$this->fields['version']}.{$this->fields['extension']}");
	}

	function getcontent() {
		//$db = dims::getInstance()->getDb();
	}


	function gethistory() {
		$db = dims::getInstance()->getDb();

		$rs = $db->query(	"
							SELECT		h.*,
										u.login,
										u.firstname,
										u.lastname

							FROM		dims_mod_doc_file_history h

							INNER JOIN	dims_user u
							ON			h.id_user_modify = u.id

							WHERE		h.id_docfile = :docfile

							ORDER BY	h.version DESC
							", array(':docfile' => $this->fields['id']) );

		$history = array();

		while($row = $db->fetchrow($rs))
		{
			$history[$row['version']] = $row;
		}

		return($history);
	}

	function createhistory() {
		$docfilehistory = new docfilehistory();
		$docfilehistory->fields['id_docfile'] = $this->fields['id'];
		$docfilehistory->fields['version'] = $this->fields['version'];
		$docfilehistory->fields['name'] = $this->fields['name'];
		$docfilehistory->fields['description'] = $this->fields['description'];
		$docfilehistory->fields['timestp_create'] = $this->fields['timestp_create'];
		$docfilehistory->fields['timestp_modify'] = $this->fields['timestp_modify'];
		$docfilehistory->fields['id_user_modify'] = $this->fields['id_user_modify'];
		$docfilehistory->fields['size'] = $this->fields['size'];
		$docfilehistory->fields['extension'] = $this->fields['extension'];
		$docfilehistory->fields['id_module'] = $this->fields['id_module'];
		$docfilehistory->save();
	}
}
