<?php
require_once DIMS_APP_PATH.'include/class_documentsfolder.php';

class documentsfile extends dims_data_object
{
	var $oldname;
	var $tmpfile;
	var $draftfile;
	var $tmpzipfile;

	function documentsfile() {
		parent::dims_data_object('dims_documents_file');
		$this->fields['id_user'] = 0;
		$this->fields['timestp_create'] = dims_createtimestamp();
		$this->fields['timestp_modify'] = $this->fields['timestp_create'];
		$this->fields['description']='';
		$this->fields['size'] = 0;
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

		if ($this->new) // insert
		{

			if ($this->tmpfile == 'none' && $this->draftfile == 'none') $error = _DOC_ERROR_EMPTYFILE;

			if ($this->fields['size']>_DIMS_MAXFILESIZE) $error = _DOC_ERROR_MAXFILESIZE;

			if (!$error)
			{
				$this->fields['extension'] = substr(strrchr($this->fields['name'], "."),1);

				$id = parent::save();

				$basepath = $this->getbasepath();
				$filepath = $this->getfilepath();

				if (file_exists($filepath) && !is_writable($filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;

				if (!$error && is_writable($basepath))
				{
					if ($this->draftfile != 'none')
					{
						if (!rename($this->draftfile, $filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;
					}
					elseif ($this->tmpfile != 'none')
					{
						if (!move_uploaded_file($this->tmpfile, $filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;
					}

					if (!$error)
					{
						chmod($filepath, 0777);
						$this->getcontent();
					}
				}
				else $error = _DOC_ERROR_FILENOTWRITABLE;
			}

		}
		else // update
		{
			//$this->getcontent();

			if ((!empty($this->tmpfile) && $this->tmpfile != 'none') || (!empty($this->draftfile) && $this->draftfile != 'none'))
			{
				if ($this->fields['size']>_DIMS_MAXFILESIZE) $error = _DOC_ERROR_MAXFILESIZE;

				if (!$error)
				{
					$this->fields['extension'] = substr(strrchr($this->fields['name'], "."),1);

					$basepath = $this->getbasepath();
					$filepath = $this->getfilepath();

					if (file_exists($filepath) && !is_writable($filepath)) $error = _DOC_ERROR_FILENOTWRITABLE;

					if (!$error)
					{
						// on déplace l'ancien fichier
						/*
						if (file_exists($filepath) && is_writable($basepath))
						{
							rename($filepath, $filepath_vers);
							//$this->createhistory();
						}
						*/

						// on copie le nouveau
						if (!$error && is_writable($basepath))
						{
							if ($this->draftfile != 'none')
							{
								if (rename($this->draftfile, $filepath))
								{
									chmod($filepath, 0777);
									$this->getcontent();
								}
								else $error = _DOC_ERROR_FILENOTWRITABLE;
							}
							if ($this->tmpfile != 'none')
							{
								if (move_uploaded_file($this->tmpfile, $filepath))
								{
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
			if ($this->oldname != $this->fields['name'])
			{
				// renommage avec modification de type
				if (($newext = substr(strrchr($this->fields['name'], "."),1)) != $this->fields['extension'])
				{
					$basepath = $this->getbasepath();
					$filepath = $this->getfilepath();
					$newfilepath = substr($filepath,0,strlen($filepath)-strlen($this->fields['extension'])).$newext;

					if (file_exists($filepath) && is_writable($basepath))
					{
						rename($filepath, $newfilepath);
						$this->fields['extension'] = $newext;
						$this->getcontent();
						parent::save();
					}
					else $error = _DOC_ERROR_FILENOTWRITABLE;
				}
				else
				{
					$this->getcontent();
					parent::save();
				}
			}
			else
			{
				parent::save();
			}
		}

		if ($this->fields['id_folder'] != 0)
		{
			$docfolder_parent = new documentsfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$docfolder_parent->fields['nbelements'] = dims_documents_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}

		return($error);
	}


	function delete() {
		$filepath = $this->getfilepath();
		if (file_exists($filepath)) unlink($filepath);

		parent::delete();

		if ($this->fields['id_folder'] != 0)
		{
			$docfolder_parent = new documentsfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$docfolder_parent->fields['nbelements'] = dims_documents_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}
	}

	function getbasepath() {
		$basepath = dims_documents_getpath()._DIMS_SEP.substr($this->fields['timestp_create'],0,8);
		dims_makedir($basepath);
		return($basepath);
	}

	function getfilepath() {
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id']}.{$this->fields['extension']}");
	}

	function getwebpath() {
		return(_DIMS_WEBPATHDATA."doc-{$this->fields['id_module']}/{$this->fields['id']}/{$this->fields['id']}.{$this->fields['extension']}");
	}

	function getcontent() {
		//$db = dims::getInstance()->getDb();
	}
}
