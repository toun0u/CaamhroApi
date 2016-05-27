<?php
require_once DIMS_APP_PATH.'modules/doc/class_docfilehistory.php';
require_once DIMS_APP_PATH.'modules/doc/include/global.php';

class docfile extends dims_data_object {
	const TABLE_NAME = "dims_mod_doc_file";
	const MY_GLOBALOBJECT_ID = 340;
	const MY_GLOBALOBJECT_CODE = dims_const::_SYSTEM_OBJECT_DOCFILE;

	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	var $oldname;
	var $tmpfile;
	var $draftfile;
	var $tmpzipfile;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
		$this->to_index(array('name', 'description', 'content', 'extension'));
		$this->fields['id_user'] = 0;
		$this->fields['timestp_create'] = dims_createtimestamp();
		$this->fields['timestp_modify'] = $this->fields['timestp_create'];
		$this->fields['description']='';
		$this->fields['size'] = 0;
		$this->fields['version'] = 1;
		$this->fields['nbclick'] = 0;
		$this->fields['parents']="";

		$this->oldname = '';
		$this->tmpfile = 'none';
		$this->tmpuploadedfile = 'none';
		$this->draftfile = 'none';
		$this->tmpzipfile = 'none';
	}

	function open() {
		$id=0;
		$numargs = func_num_args();

		for ($i = 0; $i < $numargs; $i++) {
			if ($i==0) $id = func_get_arg($i);
		}
		$res = parent::open($id);
		if (isset($this->fields['name'])) {
		$this->oldname = $this->fields['name'];
		}
		return($res);
	}

	function setid_object() {
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}
	function settitle(){
		$this->title = $this->fields['name'];
	}

	function save($id_object=0, $saveAction = true) {
		$db = dims::getInstance()->getDb();
		$dims = dims::getInstance();

		if ($id_object==0) {
			$id_object=self::MY_GLOBALOBJECT_CODE;
		}

		if ($this->fields['version']=='') $this->fields['version']=1;
		$error = 0;
		if (isset($this->fields['folder'])) unset($this->fields['folder']);

		if (!isset($this->oldname)) $this->oldname = '';

		if ($this->new) { // insert

			/*if ($this->fields['id_module']==1) {
				$isadmindoc=$dims->isModuleTypeEnabled('doc');
				if ($isadmindoc) {
					foreach($dims->getModuleByType('doc') as $i =>$mod) {
						$this->fields['id_module']=$mod['instanceid'];
					}
				}
			}*/
			if ($this->fields['version'] <=0) $this->fields['version']=1;

			if ($this->tmpfile == 'none' && $this->draftfile == 'none' && $this->tmpzipfile == 'none' && $this->tmpuploadedfile == 'none') $error = $_DIMS['cste']['_DOC_LABEL_ERROR_EMPTYFILE'];

			if ($this->fields['size']>_DIMS_MAXFILESIZE) $error = $_DIMS['cste']['_DOC_LABEL_ERROR_MAXFILESIZE'];

			if (!$error) {

				$this->fields['extension'] = substr(strrchr($this->fields['name'], "."),1);

				$id = parent::save($id_object);
				$this->fields['md5id'] = md5(sprintf("%s_%d_%d",$this->fields['timestp_create'],$id,$this->fields['version']));

				// supprimer
				//parent::save();

				$basepath = $this->getbasepath();
				$filepath = $this->getfilepath();

				if (file_exists($filepath) && !is_writable($filepath)) $error = $_DIMS['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];

				if (!$error && is_writable($basepath)) {
					if ($this->draftfile != 'none') {
						if (!rename($this->draftfile, $filepath)) $error = $_DIMS['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
					}
					elseif ($this->tmpfile != 'none') {
						if (!move_uploaded_file($this->tmpfile, $filepath)) $error = $_DIMS['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
					}
					elseif ($this->tmpzipfile != 'none') {
						if (!copy($this->tmpzipfile, $filepath)) $error = $_DIMS['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
					}
					elseif ($this->tmpuploadedfile != 'none') {
						// echo $this->tmpuploadedfile."<br>".$filepath;die();
						if (rename($this->tmpuploadedfile, $filepath)) {
							chmod($filepath, 0660);
						}
						else $error = $_DIMS['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
					}

					if (!$error) {
						chmod($filepath, 0660);
						$this->getcontent();
						parent::save($id_object);
					}
				}
				else $error = _DOC_ERROR_FILENOTWRITABLE;

			}
			else echo $error;

			/*
			 // on va rechercher la modification de l'objet principal pour attacher les docs à celui-ci
			require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
			$dims_action = new dims_action();
			$title=$action->fields['libelle'];

			$dims_action->searchByObjectAction($_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_EVENT,$action_id,$title,dims_const::_ACTION_MODIFY_EVENT);
			 */
			// on cree une action sur le mur
			if ($error==0) {

				 include_once(DIMS_APP_PATH.'include/class_dims_process.php');
								 $process = new dims_process();
								 if ($process->connect()) {
										 // on peut envoyer le fichier
										$id_process=$process->insert(_DIMS_ID,'extractpdf.sh','/usr/local/bin/extractpdf.sh . fichier.'.$this->fields['extension'].' fichier.txt',$this->getfilepath(),$this->fields['extension'],$this->getFileIndexPath());

					if ($id_process>0) {
						$this->fields['id_process'] = $id_process;
						parent::save($id_object);
					}
				}

				if ($saveAction && $this->fields['extension'] != 'eml') { // && $this->fields['id_user'] == 0){

					require_once(DIMS_APP_PATH.'include/class_dims_action.php');

					$action = new dims_action();
					$action->fields['id_parent']=0;
					$action->fields['timestp_modify']= dims_createtimestamp();
					$action->fields['id_parent']=0;
					// a modifier
					if ($id_object==9) {
						// recherche du moduledocid par defaut

					}

					if ($id_object==self::MY_GLOBALOBJECT_CODE ) {
						$action->fields['comment']= '_DIMS_LABEL_FILE_CREATED';
						$action->setModule($this->fields['id_module']);
						$action->fields['type'] = dims_const::_ACTION_CREATE_DOC; // link
						$action->addObject(0, $this->fields['id_module'], $id_object, $this->fields['id'],$this->fields['name']);
					}
					else {
						// on a un objet rattache
					}
					$action->setWorkspace($_SESSION['dims']['workspaceid']);
					$action->setUser($_SESSION['dims']['userid']);
					// adding temp tags
					$action->addTempTags();
					if ($this->fields['extension'] == 'vcf'){
						$tag = $db->query("SELECT	id
										   FROM		dims_tag
										   WHERE	tag LIKE '_DIMS_LABEL_VCARD'");
						if ($db->numrows($tag) > 0){
										$t = $db->fetchrow($tag);
										$action->addTag($t['id']);
						}else{
										require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');
										$objtag = new tag();
										$objtag->fields['type'] = 0;
										$objtag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
										$objtag->fields['tag'] = '_DIMS_LABEL_VCARD';
										$id_t = $objtag->save();
										$action->addTag($id_t);
						}
					}
					// save object action
					$action->save();
				}
			}
		}
		else {// update
			if ((!empty($this->tmpuploadedfile) && $this->tmpuploadedfile != 'none') || (!empty($this->tmpfile) && $this->tmpfile != 'none') || (!empty($this->draftfile) && $this->draftfile != 'none')) {
				$this->fields['version']++;

				if ($this->fields['size']>_DIMS_MAXFILESIZE) $error = $_SESSION['cste']['_DOC_LABEL_ERROR_MAXFILESIZE'];

				if (!$error) {
					$this->fields['extension'] = substr(strrchr($this->fields['name'], "."),1);

					$basepath = $this->getbasepath();
					$filepath = $this->getfilepath();

					//$filepath_vers = $basepath._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}.{$this->fields['extension']}";

					if (file_exists($filepath) && !is_writable($filepath)) $error = $_SESSION['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];

					if (!$error) {

						$this->deletePreview();

						// on copie le nouveau
						if (!$error && is_writable($basepath)) {
							if ($this->draftfile != 'none') {
								if (rename($this->draftfile, $filepath)) {
									chmod($filepath, 0660);
									$this->getcontent();
								}
								else $error = $_SESSION['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
							}
							elseif ($this->tmpfile != 'none') {
								if (move_uploaded_file($this->tmpfile, $filepath)) {
									chmod($filepath, 0660);
									$this->getcontent();
								}
								else $error = $_SESSION['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
							}
							elseif ($this->tmpuploadedfile != 'none') {
								if (rename($this->tmpuploadedfile, $filepath)) {
									chmod($filepath, 0660);
									$this->getcontent();
								}
								else $error = $_SESSION['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
							}
						}
						else $error = $_SESSION['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
					}
				}

				$this->fields['timestp_modify'] = dims_createtimestamp();
				$this->oldname = $this->fields['name'];
			}

			// renommage
			if ($this->oldname != $this->fields['name']) {
				// renommage avec modification de type
				if (($newext = substr(strrchr($this->fields['name'], "."),1)) != $this->fields['extension']) {
					$basepath = $this->getbasepath();
					$filepath = $this->getfilepath();
					$newfilepath = substr($filepath,0,strlen($filepath)-strlen($this->fields['extension'])).$newext;

					if (file_exists($filepath) && is_writable($basepath)) {
						rename($filepath, $newfilepath);
						$this->fields['extension'] = $newext;
						$this->getcontent();
						parent::save($id_object);
					}
					else $error = $_SESSION['cste']['_DOC_LABEL_ERROR_FILENOTWRITABLE'];
				}
				else {
					$this->getcontent();
					parent::save($id_object);
				}
			}
			else {
				$this->getcontent();
				parent::save($id_object);
			}

			if ($saveAction) {
				// on update un fichier
				require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
				$action = new dims_action(/*$this->db*/);
				$action->fields['id_parent']=0;
				$action->setModule($this->fields['id_module']);
				$action->fields['timestp_modify']= dims_createtimestamp();
				$action->fields['comment']= '_DIMS_LABEL_FILE_CREATED';
				$action->fields['type'] = dims_const::_ACTION_UPDATE_DOC; // link
				$action->addObject(0, $this->fields['id_module'], $id_object, $this->fields['id'],$this->fields['name']);
				$action->setWorkspace($_SESSION['dims']['workspaceid']);
				$action->setUser($_SESSION['dims']['userid']);
				// adding temp tags
				$action->addTempTags();
				// save object action
				$action->save();
			}
		}

		if ($this->fields['id_folder'] > 0) {
			require_once DIMS_APP_PATH."modules/doc/class_docfolder.php";
			$docfolder_parent = new docfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$docfolder_parent->fields['nbelements'] = doc_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}
		return($error);
	}

	public function deletePreview() {
		// suppression du preview
		$encoded=md5($this->getfilepath());
		$tmpdir=DIMS_WEB_PATH."data/preview/".$encoded."/";

		if (file_exists($tmpdir)) dims_deletedir($tmpdir);
	}

	function delete() {
		$filepath = $this->getfilepath();

		if (file_exists($filepath)) unlink($filepath);

		$filepath = $this->getFileIndexPath(); // fichier d'index
		if (file_exists($filepath)) unlink($filepath);

		$filepath = $this->getfilepathmini(); // fichier de miniatures
		if (file_exists($filepath)) unlink($filepath);

		$filepath = $this->getfilepathpreview(); // fichier d'image preview
		if (file_exists($filepath)) unlink($filepath);

		$this->unpublishDocFile();

		$this->deletePreview();
		$this->deleteHistory();

		parent::delete(self::MY_GLOBALOBJECT_CODE);

		if ($this->fields['id_folder'] > 0) {
			$docfolder_parent = new docfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$docfolder_parent->fields['nbelements'] = doc_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}
	}

	function getbasepath_deprecated() {
		$basepath = doc_getpath($this->fields['id_module'])._DIMS_SEP.$this->fields['id'];
		dims_makedir($basepath);
		return($basepath);
	}

	function getbasepath() {
		$basepath = doc_getpath($this->fields['id_module'],true)._DIMS_SEP.substr($this->fields['timestp_create'],0,8);

		$basephyspath = realpath(doc_getpath($this->fields['id_module'],true))._DIMS_SEP.substr($this->fields['timestp_create'],0,8);
		dims_makedir($basephyspath);
		dims_makedir($basepath);

		return($basepath);
	}

	function getfilepath_deprecated() {
		return($this->getbasepath_deprecated()._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}.{$this->fields['extension']}");
	}

	function getfilepath($version = null) {
		if(is_null($version) || empty($version)) $version = $this->fields['version'];
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id']}_{$version}.{$this->fields['extension']}");
	}

	public function publishDocFile(){
		$rep = DIMS_ROOT_PATH."www/data/doc-".$this->fields['id_module']."/".substr($this->fields['timestp_create'],0,8);
		$comm = DIMS_ROOT_PATH."data/doc-".$this->fields['id_module']."/".substr($this->fields['timestp_create'],0,8)."/".$this->fields['id']."_".$this->fields['version'].".".$this->fields['extension'];
		if(!file_exists($rep))
			dims_makedir($rep);
		if(!file_exists($rep."/".$this->fields['id']."_".$this->fields['version'].".".$this->fields['extension']) && file_exists($comm))
			copy($comm,$rep."/".$this->fields['id']."_".$this->fields['version'].".".$this->fields['extension']);
	}
	public function publishDocFileMini(){
		$rep = DIMS_ROOT_PATH."www/data/doc-".$this->fields['id_module']."/".substr($this->fields['timestp_create'],0,8);
		$comm = DIMS_ROOT_PATH."data/doc-".$this->fields['id_module']."/".substr($this->fields['timestp_create'],0,8)."/".$this->fields['id']."_".$this->fields['version'].".".$this->fields['extension'];
		if(!file_exists($rep))
			dims_makedir($rep);
		if(!file_exists($rep."/".$this->fields['id']."_".$this->fields['version']."_mini.".$this->fields['extension']))
			copy($comm,$rep."/".$this->fields['id']."_".$this->fields['version']."_mini.".$this->fields['extension']);
	}
	public function unpublishDocFileMini(){
		$comm = DIMS_ROOT_PATH."www/data/doc-".$this->fields['id_module']."/".substr($this->fields['timestp_create'],0,8)."/".$this->fields['id']."_".$this->fields['version']."_mini.".$this->fields['extension'];
		if(file_exists($comm))
			unlink($comm);
	}
	public function unpublishDocFile(){
		$comm = DIMS_ROOT_PATH."www/data/doc-".$this->fields['id_module']."/".substr($this->fields['timestp_create'],0,8)."/".$this->fields['id']."_".$this->fields['version'].".".$this->fields['extension'];
		if(file_exists($comm))
			unlink($comm);
		$this->unpublishDocFileMini();
		// on supprime aussi les thumbnails & previews
		$tmpdir=DIMS_ROOT_PATH."www/data/thumbnail/".md5($this->getfilepath());
		dims_deletedir($tmpdir);
		$tmpdir=DIMS_ROOT_PATH."www/data/preview/".md5($this->getfilepath());
		dims_deletedir($tmpdir);
	}

	//assume que le fichier est une image
	function getPicturePath($size=60,$version=null,$proportion=false){
		if(is_null($version) || empty($version)) $version = $this->fields['version'];
		$type = $this->getSearchableType();
		$directory = $this->getbasepath();
		$path = $directory._DIMS_SEP.'picture'.$size.'_'.$this->fields['id'].'_'.$version.'.png';

		if($type==search::RESULT_TYPE_PICTURE){
			if(!file_exists($path)){
				$original = $this->getfilepath($version);
				if(file_exists($original)){
					if($proportion)
						dims_resizeimage2($original, $size, 0,'png',$path);
					else
						dims_resizeimage2($original, $size, $size,'png',$path);
				}
			}
		}
		return $path;
	}

	function getPictureWebPath( $size=60,$version = null){
		if(is_null($version) || empty($version)) $version = $this->fields['version'];
		return (_DIMS_WEBPATHDATA."doc-{$this->fields['id_module']}"._DIMS_SEP.substr($this->fields['timestp_create'],0,8)._DIMS_SEP.'picture'.$size.'_'.$this->fields['id'].'_'.$version.'.png');
	}

	function getFileIndexPath() {
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}_index.txt");
	}

	function getfilepathmini() {
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}_mini.{$this->fields['extension']}");
	}

	function getfilepathminivideo($small_width,$small_height) {
		if ($small_width=='') {
			$small_width='100';
			$small_height='60';
		}
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}_minivideo_{$small_width}_{$small_height}.png");
	}

	function setMiniVideo($small_width,$small_height) {
		if ($small_width=='') {
			$small_width='100';
			$small_height='60';
		}
		$pathexec = str_replace(" ","\ ",$this->getfilepath());
		$pathfinal=$this->getbasepath()._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}_minivideo_{$small_width}_{$small_height}.png";
		$exec='ffmpeg -i "'.escapeshellarg($pathexec).'" -vframes 1 -an  -ss 20 -s '.escapeshellarg($small_width.'x'.$small_height).' -f image2 '.escapeshellarg($pathfinal);
		//echo $exec;
		exec(escapeshellcmd($exec),$tabres,$return);
	}

	function getfilepathpreview() {
		return($this->getbasepath()._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}_preview.{$this->fields['extension']}");
	}

	function getwebpath() {
		if (isset($this->fields['extension'])) {
			$this->publishDocFile();
			return(_DIMS_WEBPATHDATA."doc-{$this->fields['id_module']}"._DIMS_SEP.substr($this->fields['timestp_create'],0,8)._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}.{$this->fields['extension']}");
		}
		else {
			return("./common/img/empty.png");
		}
	}

	function getwebpathmini() {
		$this->publishDocFileMini();
		return(_DIMS_WEBPATHDATA."doc-{$this->fields['id_module']}"._DIMS_SEP.substr($this->fields['timestp_create'],0,8)._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}_mini.{$this->fields['extension']}");
	}

	function getwebpathminivideo() {
		if ($small_width=='') {
			$small_width='100';
			$small_height='60';
		}
		return(_DIMS_WEBPATHDATA."doc-{$this->fields['id_module']}"._DIMS_SEP.substr($this->fields['timestp_create'],0,8)._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}_minivideo_{$small_width}_{$small_height}.png");
	}
	function getwebpathpreview() {
		return(_DIMS_WEBPATHDATA."doc-{$this->fields['id_module']}"._DIMS_SEP.substr($this->fields['timestp_create'],0,8)._DIMS_SEP."{$this->fields['id']}_{$this->fields['version']}_preview.{$this->fields['extension']}");
	}
	function moveto($docfolder) {
		$db = dims::getInstance()->getDb();
		// verify if moduleid egals
		if($this->fields["id_module"]==$docfolder->fields['id_module']) {
			$this->fields['id_folder']=$docfolder->fields['id'];
			$this->save();
		}
	}

	function getcontent() {
		$db = dims::getInstance()->getDb();
		ini_set('max_execution_time',0);
		ini_set('memory_limit',"300M");

		if (file_exists($this->getfilepath())) {
			$this->fields['content'] = '';
			$tabres = array();
			$pathexec = str_replace(" ","\ ",$this->getfilepath());
			$exec="";
			$session_dir="";
			// on sauve le fichier
			$fileindex=$this->getFileIndexPath();

			switch(trim($this->fields['extension'])) {
				case "pdf":

					$exec="pdftotext -nopgbrk ".escapeshellarg($pathexec)." -";
					$sid = session_id();
					$temp_dir = realpath(_DIMS_TEMPORARY_UPLOADING_FOLDER);
					$session_dir = $temp_dir."/".$sid;
					dims_makedir($session_dir);
					$pathscript=realpath(".")."/scripts/";
					//$exec="bash ".$pathscript."extrait-contenu-pdf.sh \"".$session_dir."\" \"".$pathexec."\"";

					// pour le script V2 de génération d'image et index par pages
					//$exec="bash ".$pathscript."extrait-contenu-pdf-V2.sh ".$this->getbasepath()." ".$this->fields['id']." ".$this->fields['version'];
					break;
				case "doc":
					$exec="catdoc ".escapeshellarg($pathexec)."";
					break;
				case "ppt":
				case "pps":
					$exec="catppt ".escapeshellarg($pathexec)."";
					break;
				case "rtf":
					$exec="unrtf --text --nopict ".escapeshellarg($pathexec)."";
					break;
				case "msg":
					//dims_print_r($_POST['message']);
					$sid = session_id();
					$temp_dir = realpath(_DIMS_TEMPORARY_UPLOADING_FOLDER);
					$session_dir = $temp_dir."/".$sid;
					dims_makedir($session_dir);
					$currentpath=realpath(".");
					$pathscript=$currentpath."/scripts/";
					// on convertir en fichier .mime
					copy($pathexec,$session_dir."/file.msg");
					$pathexec=$session_dir."/file.msg";
					chdir($session_dir);
					$exec="perl ".escapeshellarg($pathscript)."msgconvert.pl ".escapeshellarg($session_dir)."/file.msg";

					$res=shell_exec(escapeshellcmd($exec));
					chdir ($currentpath);
					$result='';
					if (file_exists($session_dir."/file.msg.mime")) {
						//die ($session_dir."/file.msg.mime");
						require_once DIMS_APP_PATH.'modules/system/class_webmail_email.php';
						$mail = new webmail_email();
						$result=$mail->GetContentFromMsgFile($session_dir."/file.msg.mime",$session_dir);
					}
					unlink($session_dir."/file.msg.mime");
					file_put_contents($fileindex, $result);
					echo $fileindex;
					//die();
					break;
				case "txt":
				case "sql":
				case "ini":
					$exec="cat ".escapeshellarg($pathexec)."";
					break;
				case "html":
				case "htm":
					$exec="html2text ".escapeshellarg($pathexec)."";
					break;
				// need apt-get install perl libarchive-zip-perl libcompress-zlib-perl	libarchive-ar-perl libxml-twig-perl odt2txt
				case "odt":
				case "sxw":
				case "odp":
				case "odg":
				case "ods":
					//$exec="perl scripts/ooo2txt.pl \"".$pathexec."\"";
					$exec="odt2txt ".escapeshellarg($pathexec)."";
					break;
				case "xls":
					$exec="xls2csv ".escapeshellarg($pathexec)."";
					break;
				case "docx":
					$exec="unoconv -f txt --stdout ".escapeshellarg($pathexec)."";
					break;
				case "xlsx":
					$exec="ssconvert ".escapeshellarg($pathexec)."\"";
					break;
				default:
					$exec = "hachoir-metadata --raw ".escapeshellarg($pathexec)."";
					break;
			}

			if ($exec!='') {
				$exec = escapeshellcmd($exec);
				if ($this->fields['extension']=='xlsx') {
					$exec .= " ".$fileindex;
				}
				else {
					$exec .= " > ".$fileindex;
				}

				if ($this->fields['extension']=='pdf') {
					shell_exec($exec);
				}
				else {
					exec($exec);
				}
			}
			/*
			exec($exec,$tabres,$return);
			$content="";
			foreach($tabres as $key => $value) {
				if ($value!="") {
					$content.=preg_replace('/\s\s+/', ' ', strtolower($value))."\n";
				}
			}
			if (mb_check_encoding($content,"UTF-8")) $content = utf8_decode($content);

			// on ecrit dans le fichier
			file_put_contents($fileindex, $content);
			*/

			// if (file_exists($session_dir)) {
			//	dims_deletedir($session_dir);
			// }
			$this->fields['content']="[dimscontentfile]".$fileindex;
		}
	}

	function parseMail() {
		$db = dims::getInstance()->getDb();
		ini_set('max_execution_time',0);
		ini_set('memory_limit',"2000M");

		$res = array();

		if (file_exists($this->getfilepath())) {
			$this->fields['content'] = '';
			$pathexec = str_replace(" ","\ ",$this->getfilepath());
			// on sauvegarde le fichier
			$fileindex=$this->getFileIndexPath();

			$listTo = array();
			$listCC = array();
			$listBCC = array();
			$listAutre = array() ;

			if ($this->fields['extension'] == "eml") {
				$exec="cat \"".escapeshellarg($pathexec)."\" | grep \"[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\...\"";
				exec(escapeshellcmd($exec),$ligneIp);
				foreach ($ligneIp as $ligne){
					if (strpos("From: ",$ligne)){
						$regs = array();
						ereg("[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\..{2-4}",$ligne,$regs);
						$res["from"] =$regs[0];
					}elseif(ereg("To: ",$ligne)){
						$regs = array();
						if (preg_match_all("/[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\.[[:alpha:]]{2,4}/",$ligne,$regs)>0){
							foreach ($regs[0] as $val){
								$listTo[] = $val;
							}
						}
					}elseif(strpos("CC: ",$ligne)){
						$regs = array();
						if (preg_match_all("/[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\.[[:alpha:]]{2,4}/",$ligne,$regs)>0){
							foreach ($regs[0] as $val){
								$listCC[] = $val;
							}
						}
					}elseif(strpos("BCC: ",$ligne)){
						$regs = array();
						if (preg_match_all("/[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\.[[:alpha:]]{2,4}/",$ligne,$regs)>0){
							foreach ($regs[0] as $val){
								$listBCC[] = $val;
							}
						}
					}elseif(strpos("Return-Path: ",$ligne)){
						// ne rien faire
					}elseif(strpos("Message-ID: ",$ligne)){
						// ne rien faire
					}else{
						$regs = array();
						if (preg_match_all("/[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\.[[:alpha:]]{2,4}/",$ligne,$regs)>0){
							foreach ($regs[0] as $val){
								$listAutre[] = $val;
							}
						}
					}
				}
				$res["to"] = $listTo ;
				$res["cc"] = $listCC ;
				$res["bcc"] = $listBCC ;
				$res["autre"] = $listAutre ;
			}
		}
		return $res ;
	}

	function getParseVcf() {

		$liste_contact = array();
		if (file_exists($this->getfilepath())) {

			$content = fopen($this->getwebpath(), 'r');
			while($ligne = fgets($content)) {

				if (substr($ligne,0,5) == "BEGIN"){

					$contact = array();

					while(($ligne = fgets($content)) && !(substr($ligne,0,3) == "END")){
						//echo $ligne."<br>";

						if (substr($ligne,0,2) == "N:" || substr($ligne,0,2) == "N;"){
							if (substr($ligne,0,2) == "N;"){
								$li = explode(':',$ligne);
								$fn = $li[count($li)-1];
							}else
								$fn = substr($ligne,2);
							$tabfn = explode(";",$fn);

							$contact['nom'] = htmlentities(trim($tabfn[0]));
							//$contact['nom'] = $tabfn[1];
							$contact['prenom'] = htmlentities(trim($tabfn[1]));
							if (isset($tabfn[3]))
								$contact['title']=trim($tabfn[3]);

						}elseif (substr($ligne,0,2) == "FN"){
							$fn = substr($ligne,3);
							$tabfn = explode(" ",$fn);
							if (!isset($contact['prenom']) || (isset($contact['prenom']) && $contact['prenom']=='')) {
								$contact['prenom'] = htmlentities(trim($tabfn[0]));
							}
							if (!isset($contact['nom']) || (isset($contact['nom']) && $contact['nom']=='')) {
								$contact['nom'] = htmlentities(trim($tabfn[1]));
							}

						}elseif (substr($ligne,0,3) == "ORG"){
							$org = substr($ligne,4);
							$org = str_replace(';','',$org);
							$contact['org'] = trim($org) ;

						}elseif (substr($ligne,0,3) == "TEL"){
							//if (strpos($ligne,"VOICE")){
								$tel = explode(":",$ligne);

								if (strpos($ligne,"WORK")){
								$contact['tel']['work'] = $tel[1];

								}elseif (strpos($ligne,"HOME")){
								$contact['tel']['home'] = $tel[1];

								}elseif (strpos($ligne,"CELL")){
								$contact['tel']['cell'] = $tel[1];
								}
							//}
						}elseif (substr($ligne,0,5) == "EMAIL"){
							$email = explode (":",$ligne);
							$contact['email'][] = $email[1];

						}elseif (substr($ligne,0,14) == "POSTAL-ADDRESS"){
							$postal = explode (":", $ligne);
							$contact['address'] = $postal ;

						}elseif (substr($ligne,0,3) == "URL"){
							$url = explode(":",$ligne);
							$contact['url'][] = $url[1];

						}elseif (substr($ligne,0,5) == "TITLE"){
							$title = substr($ligne,6);
							$contact['title'] = $title ;

						}elseif (substr($ligne,0,3) == "ADR" || substr($ligne,0,9) == 'item1.ADR'){
							$adrr = explode(":",$ligne) ;
							$adr = explode (";",$adrr[1]);
							$address['rue'] = htmlentities($adr[2]);
							$address['city'] = htmlentities($adr[3]);
							$address['cp'] = $adr[5];
							$address['pays'] = $adr[6];

							if (strpos($ligne,"HOME")){
								$contact['adr']['home'] = $address ;
							}elseif (strpos($ligne,"WORK")){
								$contact['adr']['work'] = $address ;
							}//autre
						}elseif (substr($ligne,0,5) == "PHOTO"){
							// gestion de la photo
							$photo = '';

							$info_photo = array() ;
							$inf = explode (";",$ligne);
							foreach($inf as $value){

								$tmp = explode(":",$value);
								$value = $tmp[0];

								if (substr($value,0,4) == "TYPE"){
									$info_photo['type'] = substr($value,7);
								}elseif (trim($value) == "ENCODING=b" && isset($tmp[1])){
									$photo = $tmp[1];
								}elseif (substr($value,0,8) == "ENCODING"){
									$info_photo['encoding'] = substr($value,11);
								}
							}

							$test_arret = false;
							$len_test = 0;

							while (($ligne = fgets($content)) && !((strpos($ligne,"=3D=3D")) || $test_arret)){
								if (mb_detect_encoding($ligne,'UTF-8'))
									$photo .= trim($ligne) ;
								else
									$photo .= substr(trim($ligne),0,-1) ;

								if ($len_test == 0)
									$len_test = strlen($ligne);
								elseif ($len_test > strlen($ligne))
									$test_arret = true;
								//echo $ligne.' => '.strpos($ligne,'=').' '.strlen($ligne).'<br>';
							}
							if (!$test_arret)
								$photo .= str_replace("=3D=3D","==",$ligne) ;

							$data = base64_decode($photo);
							// création de l'image
							if ($ressource = imagecreatefromstring($data)){

								if (isset($contact['nom']) && isset($contact['prenom']))
									$filename = DIMS_TMP_PATH.$contact['nom']."_".$contact['prenom'].".png" ;
								else
									$filename = DIMS_TMP_PATH.session_id()."_".$this->fields['id']."_".count($liste_contact).".png" ;
								if (imagepng($ressource,DIMS_APP_PATH.$filename)){
									$contact['photo'] = "./".$filename;
								}
							}
						}
					}
					$liste_contact[] = $contact ;
				}
			}
			fclose($content);
		}
		return ($liste_contact);
	}

	public static function parseExternalVcf($path) {

		$liste_contact = array();
		if (file_exists($path)) {

			$content = fopen($path, 'r');
			while($ligne = fgets($content)) {

				if (substr($ligne,0,5) == "BEGIN"){

					$contact = array();

					while(($ligne = fgets($content)) && !(substr($ligne,0,3) == "END")){
						//echo $ligne."<br>";

						if (substr($ligne,0,2) == "N:" || substr($ligne,0,2) == "N;"){
							if (substr($ligne,0,2) == "N;"){
								$li = explode(':',$ligne);
								$fn = $li[count($li)-1];
							}else
								$fn = substr($ligne,2);
							$tabfn = explode(";",$fn);

							$contact['nom'] = htmlentities(trim($tabfn[0]));
							//$contact['nom'] = $tabfn[1];
							$contact['prenom'] = htmlentities(trim($tabfn[1]));
							if (isset($tabfn[3]))
								$contact['title']=trim($tabfn[3]);

						}elseif (substr($ligne,0,2) == "FN"){
							$fn = substr($ligne,3);
							$tabfn = explode(" ",$fn);
							if (!isset($contact['prenom']) || (isset($contact['prenom']) && $contact['prenom']=='')) {
								$contact['prenom'] = htmlentities(trim($tabfn[0]));
							}
							if (!isset($contact['nom']) || (isset($contact['nom']) && $contact['nom']=='')) {
								$contact['nom'] = htmlentities(trim($tabfn[1]));
							}

						}elseif (substr($ligne,0,3) == "ORG"){
							$org = substr($ligne,4);
							$org = str_replace(';','',$org);
							$contact['org'] = trim($org) ;

						}elseif (substr($ligne,0,3) == "TEL"){
							//if (strpos($ligne,"VOICE")){
								$tel = explode(":",$ligne);

								if (strpos($ligne,"WORK")){
								$contact['tel']['work'] = $tel[1];

								}elseif (strpos($ligne,"HOME")){
								$contact['tel']['home'] = $tel[1];

								}elseif (strpos($ligne,"CELL")){
								$contact['tel']['cell'] = $tel[1];
								}
							//}
						}elseif (substr($ligne,0,5) == "EMAIL"){
							$email = explode (":",$ligne);
							$contact['email'][] = $email[1];

						}elseif (substr($ligne,0,14) == "POSTAL-ADDRESS"){
							$postal = explode (":", $ligne);
							$contact['address'] = $postal ;

						}elseif (substr($ligne,0,3) == "URL"){
							$url = explode(":",$ligne);
							$contact['url'][] = $url[1];

						}elseif (substr($ligne,0,5) == "TITLE"){
							$title = substr($ligne,6);
							$contact['title'] = $title ;

						}elseif (substr($ligne,0,3) == "ADR" || substr($ligne,0,9) == 'item1.ADR'){
							$adrr = explode(":",$ligne) ;
							$adr = explode (";",$adrr[1]);
							$address['rue'] = htmlentities($adr[2]);
							$address['city'] = htmlentities($adr[3]);
							$address['cp'] = $adr[5];
							$address['pays'] = $adr[6];

							if (strpos($ligne,"HOME")){
								$contact['adr']['home'] = $address ;
							}elseif (strpos($ligne,"WORK")){
								$contact['adr']['work'] = $address ;
							}//autre
						}elseif (substr($ligne,0,5) == "PHOTO"){
							// gestion de la photo
							$photo = '';

							$info_photo = array() ;
							$inf = explode (";",$ligne);
							foreach($inf as $value){

								$tmp = explode(":",$value);
								$value = $tmp[0];

								if (substr($value,0,4) == "TYPE"){
									$info_photo['type'] = substr($value,7);
								}elseif (trim($value) == "ENCODING=b" && isset($tmp[1])){
									$photo = $tmp[1];
								}elseif (substr($value,0,8) == "ENCODING"){
									$info_photo['encoding'] = substr($value,11);
								}
							}

							$test_arret = false;
							$len_test = 0;

							while (($ligne = fgets($content)) && !((strpos($ligne,"=3D=3D")) || $test_arret)){
								if (mb_detect_encoding($ligne,'UTF-8'))
									$photo .= trim($ligne) ;
								else
									$photo .= substr(trim($ligne),0,-1) ;

								if ($len_test == 0)
									$len_test = strlen($ligne);
								elseif ($len_test > strlen($ligne))
									$test_arret = true;
								//echo $ligne.' => '.strpos($ligne,'=').' '.strlen($ligne).'<br>';
							}
							if (!$test_arret)
								$photo .= str_replace("=3D=3D","==",$ligne) ;

							$data = base64_decode($photo);
							// création de l'image
							if ($ressource = imagecreatefromstring($data)){

								if (isset($contact['nom']) && isset($contact['prenom']))
									$filename = DIMS_TMP_PATH.$contact['nom']."_".$contact['prenom'].".png" ;
								else
									$filename = DIMS_TMP_PATH.session_id()."_".count($liste_contact).".png" ;
								if (imagepng($ressource,DIMS_APP_PATH.$filename)){
									$contact['photo'] = "./".$filename;
								}
							}
						}
					}
					$liste_contact[] = $contact ;
				}
			}
			fclose($content);
		}
		return ($liste_contact);
	}

	function getThumbnail($width = 40, $version = null,$proportion = false){
		if ($width < 1) $width = 40;
		$pathfile = null;
		if (file_exists($this->getfilepath($version))) {
			$currentpath=realpath(".");
			if (!file_exists(DIMS_WEB_PATH."data/thumbnail")) mkdir(DIMS_WEB_PATH."data/thumbnail");
			$encoded=md5($this->getfilepath($version));
			$tmpdir=DIMS_WEB_PATH."data/thumbnail/".$encoded."/";
			$webpath = _DIMS_WEBPATHDATA."thumbnail/".$encoded."/";
			if (!file_exists($tmpdir)) dims_makedir($tmpdir);

			$pathfile=$tmpdir."index_".$this->fields['id']."_".$this->fields['version']."_$width.png";

			$size=0;
			if (in_array(strtolower($this->fields['extension']),array("png","jpg","jpeg","gif","bmp","wmf","tif"))){
				$pathfile=$tmpdir."index_".$this->fields['id']."_".$this->fields['version']."_$width.".$this->fields['extension'];
				copy($this->getPicturePath($width,$version,$proportion),$pathfile);
				$size=filesize($pathfile);
			}elseif(file_exists($pathfile)) {
				$size=filesize($pathfile);
			}

			if (!file_exists($pathfile) || $size<=1) {
				copy($this->getfilepath(),$tmpdir."index_".$this->fields['id']."_".$this->fields['version']."_$width.".$this->fields['extension']);
				switch($this->fields['extension']) {
					case "pdf":
						// -size $widthx(hauteur)
						$exec = "convert -thumbnail ".escapeshellarg($width)." ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version']."_$width.pdf[0]")." ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version']."_$width.png");
						break;
					case "doc":
					case "rtf":
					case "odt":
					case "sxw":
					case "odp":
					case "odg":
					case "ods":
					case "xls":
					case "ppt":
					case "pps":
					case "docx":
					case "xlsx":
					case "pptx":

						/*require_once(DIMS_APP_PATH.'/modules/doc/include/class_doc_converter_to_pdf.php');

						$converter = new doc_converter_to_pdf();
						$tabFile = explode('.',$pathfile);
						$tabFile[count($tabFile)-1] = 'pdf';
						$outputFile = implode('.',$tabFile);

						if (file_exists($outputFile) || file_exists($outputFile) && filesize($outputFile)<=0) {
							$converter->convert($tmpdir."index_".$this->fields['id']."_".$this->fields['version']."_$width.".$this->fields['extension'],$outputFile);
						}
						$exec = "";
						if(file_exists($outputFile) && filesize($outputFile)>0){
							$exec = "convert -thumbnail ".escapeshellarg($width)." ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version']."_$width.pdf[0]")." ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version']."_$width.png");
						}*/
						break;
					case "png":
					case "jpg":
					case "jpeg":
					case "gif":
					case "bmp":
					case "wmf":
					case "tif":
						break;
					case "mp3":
					case "swf":
					case "fla":
					case "flv":
					default:
						break;
					case "mp4":
					case "mkv":
					case "avi":
					case "mpeg":
					case "mpg":
					case 'webm':
						// TODO : nécessite l'installation de ffmpeg
						$exec = "ffmpeg  -itsoffset -4	-vframes 1 -i ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version']."_$width.".$this->fields['extension'])." -an -s ".escapeshellarg($width."x$width")." ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version']."_$width.png");
						unlink("index_".$this->fields['id']."_".$this->fields['version']."_$width.".$this->fields['extension']);
						break;
					case "php":
					case "css":
					case "txt":
					case "sql":
					case "ini":
					case "html":
					case "htm":
						$exec = "convert -thumbnail ".escapeshellarg($width)." ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version']."_$width.".$this->fields['extension']."[0]")." ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version']."_$width.png");
						break;

				}
				chdir($tmpdir);
				if (isset($exec) && $exec!=""){
					exec($exec,$tabres,$return);
					$pathfile=$tmpdir."index_".$this->fields['id']."_".$this->fields['version']."_$width.png";
					if($proportion)
						dims_resizeimage2($pathfile, $width, 0,'png',$pathfile,false);
					else
						dims_resizeimage2($pathfile, $width, $width,'png',$pathfile);
				}
				chdir($currentpath);
			}
			$path = explode('/',$pathfile);
			if (file_exists($pathfile))
				$pathfile = "$webpath".$path[count($path)-1];
			else
				$pathfile = null;
		}
		return $pathfile;
	}

	function getPreview($display=true, $version=null, $template = '') {

		$currentpath=realpath(".");
		$return = 0;
		if (!defined('_DIMS_BINPATH')) define ('_DIMS_BINPATH','');

		if (file_exists($this->getfilepath($version))) {
			// generation du fichier tmp
			// on genere un code en sha

			if (!file_exists(DIMS_WEB_PATH."data/preview")) mkdir(DIMS_WEB_PATH."data/preview");
			$encoded=md5($this->getfilepath($version));
			$tmpdir=DIMS_ROOT_PATH."www/data/preview/".$encoded."/";

			//$webpath = "/"._DIMS_WEBPATHDATA."/preview/".$encoded."/";
			$webpath = _DIMS_WEBPATHDATA."preview/".$encoded."/";

			$apachegrp=_DIMS_APACHE_GROUP;

			if (!file_exists($tmpdir)) {
				dims_makedir($tmpdir);
				//chgrp($tmpdir,$apachegrp);
				//chmod($tmpdir, 0770);

				$pathfile=$tmpdir."index_".$this->fields['id']."_".$this->fields['version'].".".$this->fields['extension'];

				// test if render already exists
				$size=0;
				if (file_exists($tmpdir."index_".$this->fields['id']."_".$this->fields['version'].".html")) {
					$size=filesize($tmpdir."index_".$this->fields['id']."_".$this->fields['version'].".html");
				}

				if (!file_exists($tmpdir."index_".$this->fields['id']."_".$this->fields['version'].".html") || $size<=1) {

					copy($this->getfilepath(),$pathfile);

					$pathexec = str_replace(" ","\ ",$pathfile);
					$exec="";

					switch(strtolower($this->fields['extension'])) {
						case "pdf":
							//$exec="pdftohtml -l 20 -c \"".$tmpdir."index.".$this->fields['extension']."\" ".$tmpdir."index.html";

							//$exec="pdftohtml -enc 'UTF-8' -zoom 1.19 -c index.".$this->fields['extension']." index.html";
							$exec = "convert -density 125 ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version'].".pdf")." ".escapeshellarg("image_".$this->fields['id']."_".$this->fields['version'].".png");
							//$exec = "pdfimages index.pdf -j image_".$this->fields['id']."_".$this->fields['version'];
							break;
						case "doc":
						case "rtf":
						case "odt":
						case "sxw":
						case "odp":
						case "odg":
						case "ods":
						case "xls":
						case "ppt":
						case "pps":
						case "docx":
						case "xlsx":
						case "pptx":
							require_once(DIMS_APP_PATH.'modules/doc/include/class_doc_converter_to_pdf.php');
							$converter = new doc_converter_to_pdf();
							$tabFile = explode('.',$pathfile);
							$tabFile[count($tabFile)-1] = 'pdf';
							$outputFile = implode('.',$tabFile);
							$converter->convert($pathfile,$outputFile);
							//$exec="pdftohtml -enc 'UTF-8' -zoom 1.19 -c index.pdf index.html";
							$exec = "convert -density 125 ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version'].".pdf")." ".escapeshellarg("image_".$this->fields['id']."_".$this->fields['version'].".png");
							//$exec = "pdfimages index_".$this->fields['id']."_".$this->fields['version'].".pdf -j image_".$this->fields['id']."_".$this->fields['version'];

							//$exec=_DIMS_BINPATH."unoconv -f html \"$pathfile\"";
							break;

						case "jpg":
						case "jpeg":
						case "gif":
						case "bmp":
						case "wmf":
						case "tif":
							$exec = "convert ".escapeshellarg("index_".$this->fields['id']."_".$this->fields['version'].".".$this->fields['extension'])." ".escapeshellarg("image_".$this->fields['id']."_".$this->fields['version'].".png");
						case "png":
							//$exec='echo "<html><body><p style=\'text-align:center;\'><img src=\'index_'.$this->fields['id']."_".$this->fields['version'].'.'.$this->fields['extension'].'\' alt=\'\'></p></body></html>" > index_'.$this->fields['id']."_".$this->fields['version'].'.html';
							$pathfile="";
							break;
						case "mp3":
						case "swf":
						case "fla":
						default:
							break;
						case "mp4":
							$pathfile="";
						case "mkv":
						case "avi":
						case "mpeg":
						case "mpg":
							$exec = 'ffmpeg -i '.escapeshellarg('index_'.$this->fields['id']."_".$this->fields['version'].'.'.$this->fields['extension']).' -s 640x480 '.escapeshellarg('index_'.$this->fields['id']."_".$this->fields['version'].'.webm');
							break;
						case "php":
						case "css":
						case "txt":
						case "sql":
						case "ini":
						case "html":
						case "htm":
							$exec='convert '.escapeshellarg('index_'.$this->fields['id']."_".$this->fields['version'].'.'.$this->fields['extension']).' '.escapeshellarg('index_'.$this->fields['id']."_".$this->fields['version'].'.png');
							break;

					}
					chdir($tmpdir);

					if($exec!="")
						exec($exec,$tabres,$return);

					if($return == 127) {
						?>
						<div>
							Veuillez vérifier que les programmes convert et ffmpeg sont bien installés
						</div>
						<?
						dims_deletedir($tmpdir);
					}

					chdir($currentpath);
				}
				// on parcourt les fichiers html pour modifier l'encoding
				if ($return != 127 && $dh = opendir($tmpdir)) {
					while (($filename = readdir($dh)) !== false) {
						if ($filename!="." && $filename!="..") {
							$extension = substr(strrchr($filename, "."),1);
							if ($extension=="html" && _DIMS_ENCODING!="UTF-8") {
								// on ouvre et on convertit si besoin
								$handle = fopen ($tmpdir."/".$filename, "r");
								$contents = fread ($handle, filesize ($filename));
								fclose ($handle);

								//unlink($tmpdir."/".$filename);
								$a_from =array("�","é","�","è");
								$a_to=array("&eacute;","&eacute;","&egrave;","&egrave;");
								// on remplace dans $contents
								$contents=str_replace($a_from,$a_to,$contents);

								// on ecrit
								$handle = fopen ($tmpdir."/".$filename, "w");
								fwrite ($handle, $contents);
								fclose ($handle);
							}
						}
						//chgrp($tmpdir."/".$filename,$apachegrp);
						//chmod($tmpdir."/".$filename, 0770);
					}
				}
				//delete source file renamed on index.{extension}
				if ($pathfile!="" && file_exists($pathfile) && $this->fields['extension'] != 'html') unlink($pathfile);
			}
			if ($display){
				if (in_array($this->fields['extension'],array("mp4","mkv","avi","mpeg","mpg"))){
					?>
					<link href="/js/videojs/video-js.css" rel="stylesheet">
					<script src="/js/videojs/video.js"></script>
					<video id="my_video_1" class="video-js vjs-default-skin" controls
					preload="auto" width="640" height="480" data-setup="{}">
					<!--<video controls="controls" width="640" id="video">-->
					<!--[if IE]>
						<source src="<? echo substr($webpath,1).'index_'.$this->fields['id']."_".$this->fields['version'].'.mp4'; ?>" type="video/mp4">
					<![endif]-->
					<!--[if !IE]-->
						<source src="<? echo substr($webpath,1).'index_'.$this->fields['id']."_".$this->fields['version'].'.webm'; ?>" type="video/webm">
					<!-- <[endif]-->
					</video>
					<?
				} else if( $return != 127) {
					// init de la classe dims_preview
					require_once(DIMS_APP_PATH.'modules/system/class_dims_preview.php');

					//$dpreview = new dims_preview($this->fields['md5id'],"/preview/".$encoded."/",'doc',"image_".$this->fields['id']."_".$this->fields['version'].'-');
					$dpreview = new dims_preview($this->fields['md5id'],"preview/".$encoded."/",'doc',$this->fields['id']."_".$this->fields['version']);

					// affichage du template qui correspond au preview correspondant
					if ($template == '') $template = DIMS_APP_PATH.'modules/doc/templates/previewDoc.tpl.php';
					$dpreview->displayAjax($template);
				}
			}else{
				if (in_array($this->fields['extension'],array("mp4","mkv","avi","mpeg","mpg")))
					return substr($webpath,1).'index_'.$this->fields['id']."_".$this->fields['version'].'.webm';
				else
					return $pathfile;
			}
		}
	}

	function gethistory() {
		$db = dims::getInstance()->getDb();

		$rs = $db->query(	"
							SELECT		h.*,f.md5id,
										u.login,
										u.firstname,
										u.lastname

							FROM		dims_mod_doc_file_history h
							INNER JOIN	dims_mod_doc_file as f
							ON			f.id=h.id_docfile
							INNER JOIN	dims_user u
							ON			h.id_user_modify = u.id
							WHERE		h.id_docfile = :docfile
							ORDER BY	h.version DESC
							",
							array(':docfile' => $this->fields['id'] ) );
		$history = array();

		while($row = $db->fetchrow($rs)) {
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
		$docfilehistory->fields['size']		= $this->fields['size'];
		$docfilehistory->fields['extension'] = $this->fields['extension'];
		$docfilehistory->fields['id_module'] = $this->fields['id_module'];
		$docfilehistory->fields['id_user_modify'] = $this->fields['id_user'];
		$docfilehistory->save();
	}

	public function deleteHistory() {
		foreach($this->gethistory() as $historyRaw) {
			$history_doc = new docfilehistory();
			$history_doc->open($historyRaw['id_docfile'],$historyRaw['version']);

			$history_doc->delete();
		}
	}

	public static function openByMd5($md5id) {
		$dims = dims::getInstance();
		$db = $dims->getDb();

		$doc = new docfile();

		if(!empty($md5id)) {
			$sql = 'SELECT id FROM dims_mod_doc_file WHERE md5id = :md5 LIMIT 1';

			$res = $db->query($sql, array(':md5' => $md5id) );

			$data = $db->fetchrow($res);

			$doc->open($data['id']);
		}

		return $doc;
	}

	// icones disponible en 16, 32, 48, 64, 96 px
	function getFileIcon($size = 32) {
		switch(strtolower($this->fields['extension'])) {
			case "pdf":
				return "./common/img/file_types/icon_pdf_".$size."x".$size.".png";
				break;
			case "docx":
			case "doc":
			case "rtf":
			case "odt":
			case "sxw":
			case "odg":
				return "./common/img/file_types/icon_doc_".$size."x".$size.".png";
				break;
			case "ods":
			case "xls":
			case "xlsx":
				return "./common/img/file_types/icon_excel_".$size."x".$size.".png";
				break;
			case "ppt":
			case "pps":
			case "odp":
				return "./common/img/file_types/icon_ptt_".$size."x".$size.".png";
				break;
			case "png":
			case "jpg":
			case "jpeg":
			case "gif":
			case "bmp":
			case "wmf":
			case "tif":
				return "./common/img/file_types/icon_image_".$size."x".$size.".png";
				break;
			case "mp3":
			case "mp4":
			case "avi":
			case "mpeg":
			case "mpg":
				return "./common/img/file_types/icon_sound_".$size."x".$size.".png";
				break;
			case "html":
			case "htm":
			case "php":
			case "css":
			case "txt":
			case "sql":
			case "ini":
			case "xml":
				return "./common/img/file_types/icon_xml_".$size."x".$size.".png";
				break;
			case "eml":
				return "./common/img/file_types/icon_mail_".$size."x".$size.".png";
				break;
			case "vcf":
				return "./common/img/file_types/icon_vcard_".$size."x".$size.".png";
				break;
			case "zip":
			case "rar":
			case "ace":
			case "tar.bz2":
			case "tar.gz":
			case "tgz":
			case "gz":
			case "bz2":
			case "7z":
				return "./common/img/file_types/icon_compress_".$size."x".$size.".png";
				break;
			default:
				return "./common/img/file_types/icon_default_".$size."x".$size.".png";
				break;

		}
	}

	public function getType() {
		return $this->getAttribut("type", self::TYPE_ATTRIBUT_KEY);
	}

	public function setType($type, $save = false){
		$this->setAttribut("type", self::TYPE_ATTRIBUT_KEY, $type, $save);
	}



	public function lock($save = true) {
		$oldLock = $this->fields['lock'];
		$this->fields['lock'] = 1;

		if($save) $this->save();

		return $oldLock;
	}

	public function unlock($save = true) {
		$oldLock = $this->fields['lock'];
		$this->fields['lock'] = 0;

		if($save) $this->save();

		return $oldLock;
	}

	public function isLocked() {
		return ($this->fields['lock'] == 1);
	}

	public function getIdGlobalobject() {
		return $this->getAttribut("id_globalobject", self::TYPE_ATTRIBUT_KEY);
	}

	public function getSearchableType(){

		require_once DIMS_APP_PATH.'modules/system/class_search.php';

		switch(strtolower($this->fields['extension'])){
			case 'gif':
			case 'png':
			case 'jpg':
			case 'jpeg':
			case 'tiff':
			case 'bmp':
				return search::RESULT_TYPE_PICTURE;
				break;
			case 'mov':
			case 'flv':
			case 'avi':
			case 'mp4':
				return search::RESULT_TYPE_MOVIE;
				break;
			default:
				return search::RESULT_TYPE_DOCUMENT;
				break;
		}
	}

	public static function isWebPicture($mimeType) {
		$imageMimeTypeList = array(
			'image/gif',
			'image/jpeg',
			'image/pjpeg',
			'image/png'
		);

		return in_array(strtolower($mimeType), $imageMimeTypeList);
	}

	public function initConverted(){
		$this->fields['converted'] = 1;
		$this->save();
	}

	public function setConverted(){
		$this->fields['converted'] = 2;
		$this->save();
	}

	public function resetConverted(){
		$this->fields['converted'] = 0;
		$this->save();
	}

	public function getDownloadLink(){
		return dims_urlencode('/'.dims::getInstance()->getScriptEnv()."?dims_op=doc_file_download&docfile_md5id=".$this->fields['md5id']);
	}

	public function initFolder(){
		if ($this->fields['id_folder'] == '' || $this->fields['id_folder'] <= 0){
			require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
			$tmstp = dims_createtimestamp();
			$fold = new docfolder();
			$fold->init_description();
			$fold->fields['name'] = 'root_'.$this->fields['id_globalobject'];
			$fold->fields['parents'] = 0;
			$fold->setugm();
			$fold->fields['timestp_create'] = $tmstp;
			$fold->save();
			$this->fields['id_folder'] = $fold->fields['id'];
			$fold->save(); // pr la synchro
			$this->save();
		}
	}

	public function getAttachedObj(){
		require_once DIMS_APP_PATH."modules/doc/class_docfolder.php";
		$fold = docfolder::find_by(array('id'=>$this->get('id_folder'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($fold)){
			$lst = array();
			if($fold->get('id_folder') != '' && $fold->get('id_folder') > 0){
				$lst = explode(',', $fold->get('parents'));
				$zero = array_search(0,$lst);
				if($zero !== false)
					unset($lst[$zero]);
			}
			$lst[] = $fold->get('id');

			require_once DIMS_APP_PATH."modules/system/class_contact.php";
			$contact = contact::find_by(array('id_folder'=>$lst,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($contact)){
				return $contact;
			}else{
				require_once DIMS_APP_PATH."modules/system/class_tiers.php";
				$tiers = tiers::find_by(array('id_folder'=>$lst,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if(!empty($tiers)){
					return $tiers;
				}
			}
		}
		return null;
	}

	public static function countDocfileModule($id_module = 0){
		if($id_module <= 0){
			$id_module = $_SESSION['dims']['moduleid'];
		}
		$db = dims::getInstance()->getDb();
		$params = array(
			':idm' => $id_module,
		);
		$sel = "SELECT 		COUNT(id) as nb
				FROM 		".self::TABLE_NAME."
				WHERE 		id_module = :idm";
		$res = $db->query($sel,$params);
		if($r = $db->fetchrow($res)){
			return $r['nb'];
		}else{
			return 0;
		}
	}
}
