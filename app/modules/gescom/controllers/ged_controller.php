<?php

$view = view::getInstance();
$aa = $view->get('aa');

// FIXME require_once DIMS_APP_PATH.'modules/system/desktopV2/ged/helpers/application_helper.php';
require_once DIMS_APP_PATH . 'modules/doc/class_docfolder.php';

$id_folder = $this->initFolder();

// on remonte le folder courant
$foldercourant = new docfolder();
$foldercourant->open($id_folder);

if( ! isset($_SESSION['ged']['documents']["render"]) ) $_SESSION['ged']['documents']["render"] = "folder";

if( ! isset($_SESSION['ged']['documents'][$foldercourant->get('id')]['current']) ) $_SESSION['ged']['documents'][$foldercourant->get('id')]['current'] = $foldercourant->get('id');
$currentfolder = dims_load_securvalue('folder', dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['ged']['documents'][$foldercourant->get('id')]['current'],$_SESSION['ged']['documents'][$foldercourant->get('id')]['current'], true);

if (empty($currentfolder)) $currentfolder = $id_folder;

$objcourant = $this;
$id_objcourant = $this->get('id');

$view->assign('foldercourant', $foldercourant);
$view->assign('objcourant', $objcourant);

switch($aa) {
	default:
	case 'index':
		$error = false;
		if (!isset($_SESSION['ged']['documents']["render"]) || $_SESSION['ged']['documents']["render"]=="folder") {
			if (!empty($currentfolder)) {
				$folder = docfolder::find_by(array('id' => $currentfolder), null, 1);

				if (!empty($folder)) {
					// recherche de la categ courante pour identifier les fils eventuels
					$listnewfolders = $folder->getAvailableCategs();

					$view->assign('listnewfolders', $listnewfolders);

					$view->assign('current', $folder);

					//Récupération et assignation des subfolders
					$view->assign('folders', docfolder::conditions(array('id_folder' => $currentfolder))->order('name ASC')->run());

					//Récupération et assignation des documents du dossier courant
					$view->assign('documents', docfile::conditions(array('id_folder' => $currentfolder))->order('name ASC')->run());

					//reconstruction du fil d'ariane
					if(!empty($folder->fields['parents'])){
						//les parents sont déjà séparés par des virgules, Dims assume cela
						$parents = docfolder::conditions('id IN ('.$folder->get('parents').')')->order('id ASC')->run();
						$view->assign('parents', $parents);
					}

					$uploaded = dims_load_securvalue('uploaded', dims_const::_DIMS_NUM_INPUT,true,true);
					if (!empty($uploaded)) {
						$view->assign('uploaded', $uploaded);
					}

					$view->render('documents/index.tpl.php');
				} else {
					$error = true;
				}
			} else {
				$error = true;
			}

			if ($error) {
				$view->flash(dims_constant::getVal('ERROR_THROWN'),'error');
			}
		} else {
			// on va ramener la liste des documents
		}
		break;

	case 'create_newfolder_categ':
		include_once(DIMS_APP_PATH.'/modules/system/class_matrix.php');
		require_once DIMS_APP_PATH.'modules/system/class_category.php';
		$categ = new category();
		$foldid = dims_load_securvalue('id_folder', dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_categ = dims_load_securvalue('id_categ', dims_const::_DIMS_NUM_INPUT,true,true,true);
		if (!empty($foldid) && $id_categ>0) {
			$folder = docfolder::find_by(array('id' => $foldid), null, 1);
			if (!empty($folder)) {
				$categ->open($id_categ);

				$id_globalobject_categ=$categ->fields['id_globalobject'];

				$newfolder = docfolder::build(array('id_folder' => $foldid,'name' => $categ->fields['label']));
				$newfolder->save();

				// on cree la relation avec la matrice
				$matrice = new matrix();
				$matrice->fields['id_category']     = $id_globalobject_categ;
				$matrice->fields['id_docfolder']    = $newfolder->fields['id_globalobject'];
				$matrice->fields['timestp_modify']  = dims_createtimestamp();
				$matrice->fields['id_workspace']    = $newfolder->fields['id_workspace'];
				$matrice->save();

				dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $newfolder->get('id'), 'cc' => 'documents')));
			}
		}
		die();
		break;

	case 'add_folder':
		if (true) {
			$foldid = dims_load_securvalue('foldid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($foldid)) {
				$folder = docfolder::find_by(array('id' => $foldid), null, 1);
				if (!empty($folder)) {
					$new = docfolder::build(array('id_folder' => $foldid));
					$view->assign('new_folder', $new);
					$view->render('documents/edit_folder.tpl.php');
				} else {
					$error = true;
				}
			} else {
				$error = true;
			}

			if ($error) {
				$view->flash(dims_constant::getVal('ERROR_THROWN'),'error');
				dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $foldercourant->get('id'), 'cc' => 'documents')));
			}
		} else {
			$view->flash(dims_constant::getVal('NOT_AUTHORIZED'),'error');
			dims_redirect(get_path('desktop', 'index'));
		}
		break;

	case 'create_folder':
		if (true) {
			$folder = docfolder::build();
			$folder->setvalues($_POST, 'fold_');

			$folder->save();
			$foldercourant->save();//pour mettre à jour son timest_update (pour la recherche)
			$view->flash(dims_constant::getVal('WELL_SAVED'));
			dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $folder->get('id'), 'cc' => 'documents', 'aa' => 'index')));
		} else {
			$view->flash(dims_constant::getVal('NOT_AUTHORIZED'),'error');
			dims_redirect(get_path('desktop', 'index'));
		}
		break;

	case 'edit_folder':
		if (true) {
			$foldid = dims_load_securvalue('foldid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if(!empty($foldid)){
				$folder = docfolder::find_by(array('id' => $foldid), null, 1);
				if ( ! empty($folder) ){
					$view->assign('new_folder', $folder);
					$view->render('documents/edit_folder.tpl.php');
				} else {
					$error = true;
				}
			} else {
				$error = true;
			}

			if ($error) {
				$view->flash(dims_constant::getVal('ERROR_THROWN'),'error');
				dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $foldercourant->get('id'), 'cc' => 'documents')));
			}
		} else {
			$view->flash(dims_constant::getVal('NOT_AUTHORIZED'),'error');
			dims_redirect(get_path('desktop', 'index'));
		}
		break;

	case 'update_folder':
		if (true) {
			$foldid = dims_load_securvalue('foldid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($foldid)) {
				$folder = docfolder::find_by(array('id' => $foldid), null, 1);
				if ( ! empty($folder)) {
					$folder->setvalues($_POST, 'fold_');

					$folder->save();

					$view->flash(dims_constant::getVal('WELL_SAVED'));
					dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $foldercourant->get('id'), 'cc' => 'documents', 'aa' => 'index', 'folder' => $folder->get('id'))));
				} else {
					$error = true;
				}
			} else {
				$error = true;
			}

			if ($error) {
				$view->flash(dims_constant::getVal('ERROR_THROWN'),'error');
				dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $foldercourant->get('id'), 'cc' => 'documents')));
			}
		} else {
			$view->flash(dims_constant::getVal('NOT_AUTHORIZED'),'error');
			dims_redirect(get_path('desktop', 'index'));
		}
		break;

	case 'delete_folder':
		if (true) {
			$foldid = dims_load_securvalue('foldid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($foldid)) {
				$folder = docfolder::find_by(array('id' => $foldid), null, 1);
				if (!empty($folder)) {
					//if($folder->get('convention_id') == $foldercourant->get('id')){
						$parent = $folder->get('id_folder');
						$folder->delete();
						$foldercourant->save();//pour mettre à jour son timest_update (pour la recherche)
						$view->flash(dims_constant::getVal('WELL_DELETED'));
						dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $foldercourant->get('id'), 'cc' => 'documents', 'aa' => 'index', 'folder' => $parent)));
					//}
					//else $error = true;
				} else {
					$error = true;
				}
			} else {
				$error = true;
			}

			if ($error) {
				$view->flash(dims_constant::getVal('ERROR_THROWN'),'error');
				dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $foldercourant->get('id'), 'cc' => 'documents')));
			}
		} else {
			$view->flash(dims_constant::getVal('NOT_AUTHORIZED'),'error');
			dims_redirect(get_path('desktop', 'index'));
		}
		break;

	case 'save_files':
		if (true) {
			$foldid = dims_load_securvalue('foldid', dims_const::_DIMS_NUM_INPUT,true,true,true);

			if (!empty($foldid)) {
				$folder = docfolder::find_by(array('id' => $foldid), null, 1);

				if ( ! empty($folder)) {
					//Vu que le formulaire n'est pas tokenizé à cause de fileupload je dois vérifier que le formulaire appartient bien
					$tab = explode(',', $folder->get('parents'));

					//En fait c'est un seul doc à la fois - pas réussi à attendre la fin de l'upload multiple avant de submiter le form
					$lstFiles = dims_load_securvalue('files_name', dims_const::_DIMS_CHAR_INPUT, true, true,true);
					$tmp_path = DIMS_ROOT_PATH.'www/data/upload/'.session_id();
					if(!empty($lstFiles) && file_exists($tmp_path)){
						$dir = scandir($tmp_path);

						foreach($lstFiles as $key => $name){
							if(in_array($name, $dir)){
								$doc = new docfile();
								$doc->init_description();
								$doc->setugm();
								$doc->set('name',$name);
								$doc->set('size',filesize($tmp_path."/".$name));
								$doc->set('id_folder', $folder->get('id'));
								$doc->tmpuploadedfile = $tmp_path."/".$name;
								$doc->save();
							}
						}
						$foldercourant->save();//pour mettre à jour son timest_update (pour la recherche)
						$view->flash(dims_constant::getVal('WELL_SAVED'));
						dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $folder->get('id'), 'cc' => 'documents', 'uploaded' => $doc->get('id'))));
					} else {
						$error = true;
					}
				} else {
					$error = true;
				}
			} else {
				$error = true;
			}

			if ($error) {
				$view->flash(dims_constant::getVal('ERROR_THROWN'),'error');
				dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $foldercourant->get('id'), 'cc' => 'documents')));
			}
		} else {
			$view->flash(dims_constant::getVal('NOT_AUTHORIZED'),'error');
			dims_redirect(get_path('desktop', 'index'));
		}
		break;
	case 'docinfos':
		ob_clean();
		$view->setLayout('layouts/empty_layout.tpl.php');
		if (true) {
			$docid = dims_load_securvalue('docid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($docid)) {
				$document = docfile::find_by(array('id' => $docid), null, 1);
				if (!empty($document)) {
					$view->assign('document', $document);
				} else {
					$view->assign('error', dims_constant::getVal('ERROR_THROWN'));
				}
			} else {
				$view->assign('error', dims_constant::getVal('ERROR_THROWN'));
			}
		} else {
			$view->assign('error', dims_constant::getVal('NOT_AUTHORIZED'));
		}

		$view->render('documents/docinfos.tpl.php');
		$view->compute();
		die();
		break;
	case 'docdesc':
		ob_clean();
		$view->setLayout('layouts/empty_layout.tpl.php');
		if (true) {
			$docid = dims_load_securvalue('docid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($docid)) {
				$document = docfile::find_by(array('id' => $docid), null, 1);
				if (!empty($document)) {
					$view->assign('document', $document);
				} else {
					$view->assign('error', dims_constant::getVal('ERROR_THROWN'));
				}
			} else {
				$view->assign('error', dims_constant::getVal('ERROR_THROWN'));
			}
		} else {
			$view->assign('error', dims_constant::getVal('NOT_AUTHORIZED'));
		}

		$view->render('documents/docdesc.tpl.php');
		$view->compute();
		die();
		break;
		case 'docmove':
			ob_clean();
			$docid = dims_load_securvalue('docid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($docid)) {
				$document = docfile::find_by(array('id' => $docid), null, 1);
				if( ! empty($document) ){
					require_once DIMS_APP_PATH.'modules/system/class_category.php';
					require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
					$categ = new category();
					$resultelems=$categ->getAllByTree(3);

					$folder=new docfolder();
					$folder->open($document->fields['id_folder']);
					dims_print_r($resultelems);
					?>
					<select name="category" class="select-category">
					<?php
					foreach($resultelems as $d){
						$label="";

						$label=$d['parentslabel'];
						if ($d['label']==$folder->fields['name']) {
							?>
							<option selected=true value="<?= $d['id']; ?>"><?= $label; ?></option>
							<?php
						} else {
							?>
							<option value="<?= $d['id']; ?>"><?= $label; ?></option>
							<?php
						}
					}
					?>
					</select>
					<?php
				}
			}

			echo "ici";
			die();
			break;
	case 'savedesc':
		ob_clean();
		$view->setLayout('layouts/empty_layout.tpl.php');
		if (true) {
			$docid = dims_load_securvalue('docid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($docid)) {
				$document = docfile::find_by(array('id' => $docid), null, 1);
				if ( ! empty($document) ) {
					$description = dims_load_securvalue('description', dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$document->set('description', $description);
					$document->save();
					$foldercourant->save();//pour mettre à jour son timest_update (pour la recherche)
					echo 'done';
				}
			}
		}
		die();
		break;
	case 'getdesc':
		ob_clean();
		$view->setLayout('layouts/empty_layout.tpl.php');
		$error = false;
		if (true) {
			$docid = dims_load_securvalue('docid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($docid)) {
				$document = docfile::find_by(array('id' => $docid), null, 1);
				if (!empty($document)) {
					echo nl2br($document->get('description'));//trop simple pour passer par une vue dédiée
				} else {
					$error = true;
				}
			} else {
				$error = true;
			}
		} else {
			$error = true;
		}

		if ($error) {
			echo dims_constant::getVal('NO_DESCRIPTION');
		}
		die();
		break;
	case 'delete_doc':
		ob_clean();
		$view->setLayout('layouts/empty_layout.tpl.php');
		$error = false;

		if (true) {
			$docid = dims_load_securvalue('docid', dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!empty($docid)) {
				$document = docfile::find_by(array('id' => $docid), null, 1);
				if ( ! empty($document) ) {
					$id_docfoldercourant=$document->fields['id_folder'];
					$document->delete();
					$foldercourant->save();//pour mettre à jour son timest_update (pour la recherche)
					$view->flash(dims_constant::getVal('WELL_DELETED'));
					dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $id_docfoldercourant, 'cc' => 'documents')));
				} else {
					$error = true;
				}
			} else {
				$error = true;
			}

			if($error){
				$view->flash(dims_constant::getVal('ERROR_THROWN'),'error');
				dims_redirect(get_path('show', 'show', array('id' => $id_objcourant,'folder' => $foldercourant->get('id'), 'cc' => 'documents')));
			}
		} else{
			$view->flash(dims_constant::getVal('NOT_AUTHORIZED'),'error');
			dims_redirect(get_path('desktop', 'index'));
		}

		if ($error) {
			echo dims_constant::getVal('NO_DESCRIPTION');
		}
		die();
		break;
}

$sub_content = $view->compile();//compilation du subcontent
