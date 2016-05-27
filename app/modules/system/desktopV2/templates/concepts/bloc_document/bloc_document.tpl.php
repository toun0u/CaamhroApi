<?php
$_SESSION['desktopv2']['concepts']['document_search'] = dims_load_securvalue('document_search',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['document_search']);

// initialisation des filtres
$init_document_search = dims_load_securvalue('init_document_search', dims_const::_DIMS_NUM_INPUT, true, true);
if ($init_document_search) {
	$_SESSION['desktopv2']['concepts']['document_search'] = '';
}

// texte du champ de recherche
if ($_SESSION['desktopv2']['concepts']['document_search'] != '') {
	$text_document_search = $_SESSION['desktopv2']['concepts']['document_search'];
	$button['class'] = 'searching';
	$button['href'] = '/admin.php?init_document_search=1';
	$button['onclick'] = '';
}
else {
	$text_document_search = $_SESSION['cste']['LOOKING_FOR_A_DOCUMENT']. ' ?';
	$button['class'] = '';
	$button['href'] = 'Javascript: void(0);';
	$button['onclick'] = 'Javascript: if($(\'input#editbox_search_document\').val() != \''.$text_document_search.'\') $(this).closest(\'form\').submit();';
}

// affichage a gauche ou a droite en fonction de la présence du bloc suivis
if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
	$style = 'float: right;';
}
else {
	$style = 'float: left; clear: left;';
}
?>

<div class="bloc_document" style="<?php echo $style; ?>">
	<div class="title_bloc_document"><h2>Documents</h2></div>
	<div class="bloc_zone_search_document bloc_zone_search">
		<div class="bloc_searchform_document">
			<form action="admin.php" method="post" name="formsearch" id="bloc_formsearch_document">
                <?
                    // Sécurisation du formulaire par token
                    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                    $token = new FormToken\TokenField;
					$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
					$token->field("button_search_y");
                    $token->field("document_search");
                    $tokenHTML = $token->generate();
                    echo $tokenHTML;
                ?>
				<span>
					<input type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left">
					<input type="text" name="document_search" id="bloc_editbox_search_document" class="bloc_editbox_search editbox_search<? if ($button['class'] == 'searching') echo ' working'; ?>" maxlength="80" value="<?php echo htmlspecialchars($text_document_search); ?>" <? if ($button['class'] != 'searching') echo 'onfocus="Javascript:this.value=\'\'; $(this).addClass(\'working\');"'; ?> onblur="Javascript:if (this.value==''){ $(this).removeClass('working'); this.value='<?php echo htmlspecialchars($text_document_search); ?>'; }">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left">

					<a class="<?php echo $button['class']; ?>" href="<?php echo $button['href']; ?>" onclick="<?php echo $button['onclick']; ?>"></a>
				</span>
			</form>
		</div>
	</div>
	<div class="cadre_bloc_document">
		<?php
		if ($_SESSION['desktopv2']['concepts']['sel_type'] != dims_const::_SYSTEM_OBJECT_DOCFILE) {
			$dims=dims::getInstance();
			?>
			<div class="add_document">
				<?php
				//<a href="Javascript: void(0);" onclick="javascript:addDocumentConcepts(event);">
				?>


				&nbsp;
				<a href="<?php echo $dims->getScriptEnv().'?more='.dims_const_desktopv2::_DOCUMENTS_EDIT_FILE;?>">
					<span><?php echo $_SESSION['cste']['ADD_DOCUMENT']; ?></span>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png">
				</a>
				<span>&nbsp;</span>
				<a href="<?php echo $dims->getScriptEnv().'?more='.dims_const_desktopv2::_DOCUMENTS_ADD_FOLDER;?>">
					<span ><?php echo $_SESSION['cste']['_ADD_FOLDER']; ?></span>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/ajouter_dossier.png">
				</a>


				<?php
				$sharefile = dims::getInstance()->getModuleByType('sharefile');

				if(!empty($sharefile) && $this instanceof tiers) {
					// TODO : Could allow selecting sharefile module instance if several
					$sharefileModule = current($sharefile);

					require_once DIMS_APP_PATH . '/include/class_urlbuilder.php';

					$url = new dims_urlBuilder(dims::getInstance()->getScriptEnv());
					?>
					<a href="<?= dims_urlencode($url->addParams(array('dims_moduleid' => $sharefileModule['instanceid'], 'op' => 'share', 'action' => 'add', 'from_entity' => $this->getId()))); ?>">
						<span><?php echo $_SESSION['cste']['SHARE_DOCUMENT']; ?></span>
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png">
					</a>
					<?php
				}
				?>
			</div>
		<?php
		}
		$display_doclist=true;

		$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true,false);
		switch ($action) {
			case 'file_process_getcontent':
				$id_process=dims_load_securvalue('id_process',dims_const::_DIMS_NUM_INPUT,true,false);
				$id_doc=dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,false);
				ob_end_clean();
				include_once(DIMS_APP_PATH.'include/class_dims_process.php');
                                 $process = new dims_process();
                                 if ($process->connect()) {
                                         // on peut envoyer le fichier
                                        $content= $process->getContent($id_process);

					if ($content!='') {
						$docfile = new docfile();
						$docfile->open($docfile_id);
						$fileindex=$docfile->getFileIndexPath();
						$docfile->fields['content']="[dimscontentfile]".$fileindex;
						$docfile->save();

					}
                                 }
				die();
				break;
			case 'file_process':
				$id_process=dims_load_securvalue('id_process',dims_const::_DIMS_NUM_INPUT,true,false);
				$id_doc=dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,false);
				ob_end_clean();
				include_once(DIMS_APP_PATH.'include/class_dims_process.php');
                                 $process = new dims_process();
                                 if ($process->connect()) {
                                         // on peut envoyer le fichier
                                        $status_process=$process->getStatus($id_process);

					$docfile = new docfile();
					$docfile->open($docfile_id);
					$docfile->fields['status']=$status_process;
					$docfile->save();
					echo $status_process;
                                 }
				die();
				break;
			//case "file_extract":
			case dims_const_desktopv2::_DOCUMENTS_EXTRACT_FILE:
				$docfile_id=dims_load_securvalue('docfile_id',dims_const::_DIMS_NUM_INPUT,true,false);

				if ($docfile_id>0) {
					$docfile = new docfile();
					$docfile->open($docfile_id);
					$currentfolder=$docfile->fields['id_folder'];
					$_SESSION['dims']['docs']['docfile_id']=$docfile_id;
					$_SESSION['dims']['docs']['currentfolder']=$currentfolder;
					$_SESSION['dims']['docs']['start']=0;
					$_SESSION['dims']['docs']['current']=0;
					$_SESSION['dims']['docs']['total']=0;

					dims_redirect("{$scriptenv}?action=file_extract_suite");
				}
				break;
			case "file_extract_suite":
				include _DESKTOP_TPL_LOCAL_PATH."/concepts/bloc_document/document_file_extract.php";
				break;
			case "file_extract_init":
				ob_end_clean();
				ini_set('max_execution_time',-1);
				ini_set('memory_limit',"1024M");

				require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
				require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
				require_once DIMS_APP_PATH.'modules/doc/include/global.php';

				//$_GET['docfile_id']=$_SESSION['dims']['docs']['docfile_id'];
				$_SESSION['dims']['docs']['start']=1;
				// copy extract file  to current folder, follow files and folder recursively and create line
				$docfile = new docfile();
				$docfile->open($_SESSION['dims']['docs']['docfile_id']);
				$currentfolder=$docfile->fields['id_folder'];

				$_SESSION['dims']['docs']['basepath']=_DIMS_PATHDATA;
				if ($docfile->fields['extension']=="zip" || $docfile->fields['extension']=="tar.gz" || $docfile->fields['extension']=="tgz") {

					if (file_exists($docfile->getfilepath()) && is_writeable(doc_getpath()._DIMS_SEP)) {
						// TODO changer pour mettre un aléatoire
						$pathdest=$docfile->getbasepath()._DIMS_SEP."tmp_".$_SESSION['dims']['docs']['docfile_id'];
						$_SESSION['dims']['docs']['pathdest']=$pathdest;
						session_write_close();
						if (is_dir($pathdest)) dims_deletedir($pathdest);
						mkdir ($pathdest);

						if (strtolower($docfile->fields['extension'])=="zip") {
							$filename=$docfile->fields['id']."_".$docfile->fields['version'].".".$docfile->fields['extension'];
							dims_unzip($filename,$docfile->getbasepath(),$pathdest);
						}
						else {
							exec(_DIMS_BINPATH."tar -zxvf ".$docfile->getfilepath()." -C ".$pathdest);
						}

						// extract finished
						session_start();
						$_SESSION['dims']['docs']['start']=2;
						session_write_close();

						$pathdest=$_SESSION['dims']['docs']['pathdest'];
						// on s'occupe maintenant de parcourir l'ensemble du r�pertoire et d�placer au fur et � mesure les fichiers et dossiers
						$docfolder = new docfolder();
						$docfolder->open($_SESSION['dims']['docs']['currentfolder']);

						if ($_SESSION['dims']['docs']['currentfolder']==0) {
							$docfolder->init_description();
							$docfolder->fields['foldertype']="public";
							$docfolder->fields['parents']="";
							$docfolder->fields['id']=0;
							$docfolder->fields['published']=1;
						}
						$result=explode("\t",exec(_DIMS_BINPATH."du -s ".$pathdest),2);
						dims_print_r($result);
						if (isset($result)) {
							session_start();
							$_SESSION['dims']['docs']['total']= $result[0];
							session_write_close();
						}

						createRecursiveFiles($pathdest._DIMS_SEP,$_SESSION['dims']['docs']['basepath']._DIMS_SEP,"777",$docfolder);
						dims_deletedir($pathdest);
						session_start();
						// files created finished
						$_SESSION['dims']['docs']['start']=3;
					}
				}
				die();
				break;
			case "file_extract_ajax":
				ob_end_clean();
				include _DESKTOP_TPL_LOCAL_PATH."/concepts/bloc_document/document_extract_progress.php";
				die();
				break;
			case 'preview':
			$id_file = dims_load_securvalue('id_file',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($id_file != '' && $id_file > 0){
				require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
				$file = new docfile();
				$file->open($id_file);

				$file->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/doc/previsu.tpl.php');
				$display_doclist=false;
			}else
				dims_redirect(dims::getInstance()->getScriptEnv()."?op=".module_smile::_SMILE_CONCEPTS."&go_contact=".$_SESSION['smile']['admin']['concepts']['go_contact']."&concept_contact_op=".module_smile::_SMILE_CONCEPTS_DOCUMENTS);
			break;
			case 'add_file':
				//dims_print_r($_FILES);die();
				$id_folder = dims_load_securvalue('id_folder',dims_const::_DIMS_NUM_INPUT,true,true,false);
				if ($id_folder != '' && $id_folder > 0 && file_exists($_FILES['file']['tmp_name'])){
						$id_folder = dims_load_securvalue('id_folder',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$file = new docfile();
						$file->init_description();
						$file->setugm();
						$file->fields['version'] = 1;
						$file->tmpfile = $_FILES['file']['tmp_name'];
						$file->fields['size'] = $_FILES['file']['size'];
						$file->fields['name'] = $_FILES['file']['name'];
						$file->fields['description'] = dims_load_securvalue('description',dims_const::_DIMS_CHAR_INPUT,true,true,true);
						$file->fields['id_folder'] = $id_folder;
						$file->save();
						/*TODO
						switch($_SESSION['smile']['admin']['type_concepts']){
							case dims_const::_SYSTEM_OBJECT_TIERS:
								$contact = new smile_company();
								$contact->openWithGB($_SESSION['smile']['admin']['concepts']['go_contact']);
								//$contact->addHistory(module_smile::_ACTION_ADD_FILE,array());
								$elem = array('id_tiers' => $_SESSION['smile']['admin']['concepts']['go_contact'],
											  'id_doc' => $file->fields['id_globalobject']);
								$matrice = new smile_matrix();
								$matrice->addLink($elem);
								break;
							case dims_const::_SYSTEM_OBJECT_CONTACT:
								$contact = new smile_contact();
								$contact->openWithGB($_SESSION['smile']['admin']['concepts']['go_contact']);
								//$contact->addHistory(module_smile::_ACTION_ADD_FILE,array());
								$elem = array('id_contact' => $_SESSION['smile']['admin']['concepts']['go_contact'],
											  'id_doc' => $file->fields['id_globalobject']);
								$matrice = new smile_matrix();
								$matrice->addLink($elem);
								break;

								break;
						}*/

						// pour se positionner dessus
						$_SESSION['dims']['gedfinder'][]='FILE_'.$file->fields['id'];
				}
				dims_redirect(dims::getInstance()->getScriptEnv());
				break;
			case 'add_folder':
				$id_folder = dims_load_securvalue('id_folder',dims_const::_DIMS_NUM_INPUT,true,true,false);
				if ($id_folder != '' && $id_folder > 0){
					require_once(DIMS_APP_PATH . "/modules/doc/class_docfolder.php");
					$folder = new docfolder();
					$editId = dims_load_securvalue('editId',dims_const::_DIMS_NUM_INPUT,true,true,false);
					if ($editId != '' && $editId > 0)
						$folder->open($editId);
					else{
						$folder->init_description();
						$folder->setugm();
					}
					$folder->fields['name'] = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$folder->fields['description'] = dims_load_securvalue('description',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$folder->fields['id_folder'] = $id_folder;
					$folder->save();
				}
				dims_redirect(dims::getInstance()->getScriptEnv());
				break;
			case 'file_delete':
				require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
				require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

				$docfile_id = dims_load_securvalue('docfile_id',dims_const::_DIMS_NUM_INPUT,true,true,false);
				if ($docfile_id != '' && $docfile_id > 0){
					$file = new docfile();
					$file->open($docfile_id);
					$file->delete();
					/*switch($_SESSION['smile']['admin']['type_concepts']){
						case dims_const::_SYSTEM_OBJECT_TIERS:
							$contact = new smile_company();
							$contact->openWithGB($_SESSION['smile']['admin']['concepts']['go_contact']);
							$contact->addHistory(module_smile::_ACTION_DELETE_FILE,array());
							break;
						case dims_const::_SYSTEM_OBJECT_CONTACT:
							$contact = new smile_contact();
							$contact->openWithGB($_SESSION['smile']['admin']['concepts']['go_contact']);
							$contact->addHistory(module_smile::_ACTION_DELETE_FILE,array());
							break;
						case dims_const::_SYSTEM_OBJECT_PROJECT:
							$contact = new smile_project();
							$contact->openWithGB($_SESSION['smile']['admin']['concepts']['go_contact']);
							$contact->addHistory(module_smile::_ACTION_DELETE_FILE,array());
							break;
					}*/
				}
				dims_redirect(dims::getInstance()->getScriptEnv());
				break;
		}

		if ($display_doclist)
			require_once (_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_document/documents.tpl.php');
		/*
		?>
		<div class="add_document_content">
		<?php
		global $lstObj;
		if (isset($lstObj['doc'])) {
			foreach ($lstObj['doc'] as $doc) {
				if ( $_SESSION['desktopv2']['concepts']['document_search'] == '' || stristr($doc->fields['name'], $_SESSION['desktopv2']['concepts']['document_search']) ) {
					$doc->setLightAttribute('concept_not_event', $this->getLightAttribute('concept_not_event'));
					$doc->setLightAttribute('type', $doc->getSearchableType());
					$doc->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_document/fiche_bloc_document.tpl.php');
				}
			}
		}
		</div>
		 */
		?>

	</div>
</div>
