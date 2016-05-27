<?php

switch ($op) {
	case 'create':
	case 'edit':
		include_once './common/modules/catalogue/public_article_edit.php';
		break;
	case 'save':
		// Ouverture de l'article
		include_once './common/modules/catalogue/include/class_article.php';
		$obj_art = new article();
		if (!empty($_POST['id_article']) && is_numeric($_POST['id_article'])) {
			$obj_art->findById($_POST['id_article']);
		}

		// Enregistrement de la photo si une ref article est donnee
		if (!empty($_POST['imageref'])) {
			$photoref = trim($_POST['imageref']);
			$obj_tmp = new article();
			if ( $obj_tmp->open($photoref) && $obj_tmp->fields['image'] != '' ) {
				//dims_print_r($obj_tmp->fields);
				$obj_art->fields['image'] = $obj_tmp->fields['image'];
			}
		}

		// Enregistrement de la photo si presente
		if (!empty($_FILES['image']) && !$_FILES['image']['error']) {
			$error = 0;

			if ($_FILES['image']['size'] > 0) {
				if ($_FILES['image']['size'] < _CATA_PHOTO_MAX_UPLOAD_SIZE) {
					$path = realpath('.').'/photos/orig';
					$fileName = $_SESSION['dims']['usertype'].'_'.strtolower($_FILES['image']['name']);
					$fullFileName = $path.'/'.$fileName;

					if (!file_exists($fullFileName)) {
						if (move_uploaded_file($_FILES['image']['tmp_name'], $fullFileName)) {
							// On redimentionne la photo
							dims_resizeimage($fullFileName, 0, _CATA_PHOTO_MAX_WIDTH, _CATA_PHOTO_MAX_HEIGHT, '', 0, $fullFileName);
							dims_resizeimage($fullFileName, 0, 42, 32, '', 0, realpath('.').'/photos/42x32/'.$fileName);
							dims_resizeimage($fullFileName, 0, 100, 100, '', 0, realpath('.').'/photos/100x100/'.$fileName);
							dims_resizeimage($fullFileName, 0, 50, 50, '', 0, realpath('.').'/photos/50x50/'.$fileName);
							dims_resizeimage($fullFileName, 0, 300, 300, '', 0, realpath('.').'/photos/300x300/'.$fileName);

							// On met a jour la photo dans la table
							chmod($fullFileName, 0660);

							$obj_art->fields['image'] = $fileName;
						} else {
							$error = _CATA_PHOTO_COPY_ERROR;
						}
					} else {
						$error = _CATA_PHOTO_ALREADY_EXISTS;
					}
				} else {
					$error = _CATA_PHOTO_HUGE_DOC;
				}
			} else {
				$error = _CATA_PHOTO_EMPTY_DOC;
			}

			if (false) { //$error) {
				$_SESSION['catalogue']['erreur_photos'] = $error;
			}
		}

		$obj_art->setvalues($_POST, 'article_');
		$obj_art->save();

		// Si erreur lors de l'upload, renvoi vers le formulaire d'edition
		if (isset($_SESSION['catalogue']['erreur_photos'])) {
			dims_redirect($dims->getScriptEnv().'?op=edit&id_article='.$obj_art->fields['id_article']);
		}

		// si on était en édition des rattachements
		if (isset($_SESSION['catalogue']['artratt_retour'])) {
			// recherche d'un groupe de rattachement au pif
			$rs = $db->query('SELECT id FROM dims_mod_vpc_article_ratt_grp LIMIT 0, 1');
			if ($db->numrows($rs)) {
				$grp = $db->fetchrow($rs);

				// on fait le rattachement
				$obj_art_base = new article();
				if ($obj_art_base->findById($_SESSION['catalogue']['artratt_retour'])) {
					$db->query('INSERT INTO dims_mod_vpc_article_ratt VALUES(\''.$obj_art_base->fields['reference'].'\', \''.$obj_art->fields['reference'].'\', '.$grp['id'].', \'ind\')');
				}
			}
			// on renvoie vers sa fiche de rattachement
			dims_redirect($dims->getScriptEnv().'?subtab=artratt&op=edit&id_article='.$_SESSION['catalogue']['artratt_retour']);
		}

		$retour = dims_load_securvalue('retour', dims_const::_DIMS_CHAR_INPUT, false, true);
		if (!empty($retour) && $retour == 'rech') {
			$_SESSION['dims']['moduletabid'] = _ADMIN_TAB_CATA_ARTRECH;
		}

		dims_redirect($dims->getScriptEnv().'#a'.$obj_art->fields['id_article']);
		break;
	case 'save_publish':
		if (!empty($_POST['articles'])) {
			$db->query('
				UPDATE	dims_mod_cata_article
				SET		published = !published,
						date_modify = '.dims_createtimestamp().'
				WHERE	id_article IN ('.implode(',', $_POST['articles']).')
				AND		id_module = '.$_SESSION['dims']['moduleid'].'
				AND		id_group = '.dims_viewworkspaces($_SESSION['dims']['moduleid']));
		}
		dims_redirect($dims->getScriptEnv());
		break;
	case 'cut_articles':
		if (!empty($_POST['articles'])) {
			$_SESSION['catalogue']['selArticles'] = $_POST['articles'];

			include_once './common/modules/catalogue/include/class_article_cata.php';
			include_once './common/modules/catalogue/include/class_article_famille.php';

			foreach ($_SESSION['catalogue']['selArticles'] as $id_article) {
				if (is_numeric($id_article)) {
					$art = new article();
					if ($art->findById($id_article)) {
						$artfam = new cata_article_famille();
						$artfam->open($id_article, $art->fields['id_adh'], $_SESSION['catalogue']['familyId']);
						$artfam->delete();
					}
				}
			}
		}
		dims_redirect($dims->getScriptEnv());
		break;
	case 'copy_articles':
		if (!empty($_POST['articles'])) {
			$_SESSION['catalogue']['selArticles'] = $_POST['articles'];
		}
		dims_redirect($dims->getScriptEnv());
		break;
	case 'rattach_articles':
		if (!empty($_SESSION['catalogue']['selArticles'])) {
			include_once './common/modules/catalogue/include/class_article.php';
			include_once './common/modules/catalogue/include/class_article_famille.php';

			foreach ($_SESSION['catalogue']['selArticles'] as $id_article) {
				if (is_numeric($id_article)) {
					$art = new article();
					if ($art->findById($id_article)) {
						$artfam = new cata_article_famille();
						$artfam->open($id_article, $art->fields['id_adh'], $_SESSION['catalogue']['familyId']);
						$artfam->save();
					}
				}
			}
			unset ($_SESSION['catalogue']['selArticles']);
		}
		dims_redirect($dims->getScriptEnv());
		break;
	case 'affect_group':
		include_once './common/modules/catalogue/public_article_affect_group.php';
		break;
	case 'save_affect_group':
		// Enregistrement de la photo si presente
		if (!empty($_FILES['image']) && !$_FILES['image']['error']) {
			$error = 0;

			if ($_FILES['image']['size'] > 0) {
				if ($_FILES['image']['size'] < _CATA_PHOTO_MAX_UPLOAD_SIZE) {
					$path = realpath('.').'/photos/orig';
					$fileName = $_SESSION['dims']['usertype'].'_'.strtolower($_FILES['image']['name']);
					$fullFileName = $path.'/'.$fileName;

					if (!file_exists($fullFileName)) {
						if (move_uploaded_file($_FILES['image']['tmp_name'], $fullFileName)) {
							// On redimentionne la photo
							dims_resizeimage($fullFileName, 0, _CATA_PHOTO_MAX_WIDTH, _CATA_PHOTO_MAX_HEIGHT, '', 0, $fullFileName);
							dims_resizeimage($fullFileName, 0, 42, 32, '', 0, realpath('.').'/photos/42x32/'.$fileName);

							// On met a jour la photo dans la table
							chmod($fullFileName, 0660);

							$_POST['article_image'] = $fileName;
						} else {
							$error = _CATA_PHOTO_COPY_ERROR;
						}
					} else {
						$error = _CATA_PHOTO_ALREADY_EXISTS;
					}
				} else {
					$error = _CATA_PHOTO_HUGE_DOC;
				}
			} else {
				$error = _CATA_PHOTO_EMPTY_DOC;
			}

			if (false) { //$error) {
				$_SESSION['catalogue']['erreur_photos'] = $error;
			}
		}

		// Enregistrement de la photo si une ref article est donnee
		if (!empty($_POST['imageref'])) {
			include_once './common/modules/catalogue/include/class_article.php';
			$photoref = trim($_POST['imageref']);
			$obj_tmp = new article();
			if ( $obj_tmp->open($photoref) && $obj_tmp->fields['image'] != '' ) {
				$_POST['article_image'] = $obj_tmp->fields['image'];
			}
		}

		// Enregistrement des articles
		$prefix = 'article_';
		$nb_fields = 0;
		$sql = "UPDATE dims_mod_cata_article SET ";
		foreach ($_POST as $field => $value) {
			if (substr($field, 0, strlen($prefix)) == 'article_') {
				if ((is_numeric($value) && $value > 0) || (!is_numeric($value) && $value != '')) {
					$nb_fields++;
					$field_name = substr($field, strlen($prefix));
					$sql .= "$field_name = '$value',";
				}
			}
		}
		if ($nb_fields > 0) {
			$sql = substr($sql, 0, -1)." WHERE id_article IN (".implode(',', $_SESSION['catalogue']['articles']).")";
			$db->query($sql);
		}

		unset($_SESSION['catalogue']['articles']);
		dims_redirect($dims->getScriptEnv());
		break;
	case 'set_photo':
		$id_article = dims_load_securvalue('id_article', dims_const::_DIMS_NUM_INPUT, true, false);
		$filename = dims_load_securvalue('filename', dims_const::_DIMS_NUM_INPUT, true, false);

		if (!empty($id_article) && !empty($filename)) {
			include_once './common/modules/catalogue/include/class_article.php';
			$obj_art = new article();
			$obj_art->findById($id_article);
			$obj_art->set_photo($filename);
		}
		dims_redirect($dims->getScriptEnv().'?op=edit&id_article='.$obj_art->fields['id_article']);
		break;
	case 'drop_photo':
		$id_article = dims_load_securvalue('id_article', dims_const::_DIMS_NUM_INPUT, true, false);

		if (!empty($id_article)) {
			include_once './common/modules/catalogue/include/class_article.php';
			$obj_art = new article();
			$obj_art->findById($id_article);
			$obj_art->drop_photo();
		}
		dims_redirect($dims->getScriptEnv().'?op=edit&id_article='.$obj_art->fields['id_article']);
		break;
	case 'drop_eval':
		$id_article = dims_load_securvalue('id_article', dims_const::_DIMS_NUM_INPUT, true, false);

		if (!empty($id_article)) {
			include_once './common/modules/catalogue/include/class_article.php';
			$obj_art = new article();
			$obj_art->findById($id_article);
			$obj_art->drop_eval();
		}
		dims_redirect($dims->getScriptEnv().'?op=edit&id_article='.$obj_art->fields['id_article']);
		break;
	case 'httpr_add_value':
		ob_clean();
		ob_start();

		$id_field = dims_load_securvalue('id_field', dims_const::_DIMS_NUM_INPUT, true, false);
		$sup_value = dims_load_securvalue('sup_value', dims_const::_DIMS_CHAR_INPUT, true, false);
		$lst = dims_load_securvalue('lst', dims_const::_DIMS_CHAR_INPUT, true, false);

		if (!empty($id_field) && !empty($sup_value) && !empty($lst)) {
			include_once './common/modules/catalogue/include/class_champ_valeur.php';
			$val = new cata_champ_valeur();
			$val->fields['id_chp'] = $id_field;
			$val->fields['valeur'] = $sup_value;
			$val->save();

			if (is_numeric($val->fields['id']) && $val->fields['id'] > 0) {
				echo "{$val->fields['id']}||{$val->fields['valeur']}||{$lst}";
			}
			else {
				echo '-1';
			}
		}
		else echo '-1';

		ob_end_flush();
		die();
		break;
	case 'httpr_add_marque':
		ob_clean();
		ob_start();

		$id_field = dims_load_securvalue('id_field', dims_const::_DIMS_CHAR_INPUT, true, false);
		$sup_value = dims_load_securvalue('sup_value', dims_const::_DIMS_CHAR_INPUT, true, false);
		$lst = dims_load_securvalue('lst', dims_const::_DIMS_CHAR_INPUT, true, false);

		if (!empty($id_field) && !empty($sup_value) && !empty($lst)) {
			include_once './common/modules/catalogue/include/class_marque.php';
			$val = new cata_marque();
			$val->fields['libelle'] = $sup_value;
			$val->save();

			if (is_numeric($val->fields['id']) && $val->fields['id'] > 0) {
				echo "{$val->fields['id']}||{$val->fields['libelle']}||{$lst}";
			}
			else {
				echo '-1';
			}
		}
		else echo '-1';

		ob_end_flush();
		die();
		break;
	case 'delete':
		$id_article = dims_load_securvalue('id_article', dims_const::_DIMS_NUM_INPUT, true, false);

		if (isset($_SESSION['catalogue']['add_image']) && ($_SESSION['catalogue']['add_image'] > '0')) {
			$_SESSION['catalogue']['add_image'] = 0;
		}
		if (!empty($id_article)) {
			include_once './common/modules/catalogue/include/class_article.php';
			$obj_article = new article();
			$obj_article->findById($id_article);
			$obj_article->delete();
		}
		if (isset($action) && $action == 'art_norattach') dims_redirect($dims->getScriptEnv().'?op=art_norattach');
		else dims_redirect($dims->getScriptEnv());
		break;
	case 'rattach':
		include_once './common/modules/catalogue/public_article_rattach.php';
		break;
	case 'save_rattach':
		if (!empty($_POST['articles'])) {
			include_once './common/modules/catalogue/include/class_article.php';
			include_once './common/modules/catalogue/include/class_article_famille.php';

			foreach ($_POST['articles'] as $id_article) {
				if (is_numeric($id_article)) {
					$art = new article();
					if ($art->findById($id_article)) {
						$artfam = new cata_article_famille();
						$artfam->open($id_article, $art->fields['id_adh'], $_SESSION['catalogue']['familyId']);
						$artfam->save();
					}
				}
			}
		}
		dims_redirect($dims->getScriptEnv());
		break;
	case 'detach':
		$id_article = dims_load_securvalue('id_article', dims_const::_DIMS_NUM_INPUT, true, false);

		if (!empty($id_article)) {
			include_once './common/modules/catalogue/include/class_article.php';
			include_once './common/modules/catalogue/include/class_article_famille.php';

			$art = new article();
			if ($art->findById($id_article)) {
				$artfam = new cata_article_famille();
				if ($artfam->open($id_article, $art->fields['id_adh'], $_SESSION['catalogue']['familyId'])) {
					$artfam->delete();
				}
			}
		}
		dims_redirect($dims->getScriptEnv());
		break;
	case 'art_norattach':
		include_once './common/modules/catalogue/admin_article_norattch.php';
		break;
	case 'httpr_refresh_prixachat':
		ob_end_clean();
		ob_start();

		if (!empty($_GET['id_article']) && is_numeric($_GET['id_article'])) {
			if (
				!empty($_GET['id_cond']) && is_numeric($_GET['id_cond']) &&
				!empty($_GET['pu']) && is_numeric($_GET['pu'])
			) {
				include_once './common/modules/catalogue/class_prixachat.php';
				$pa = new cata_prixachat();
				$pa->open($_GET['id_article'], $_GET['id_cond']);
				// on peut virer le lien adherent, il est deja dans article
				// et on se base pas sur la ref, mais sur l'id article dc pas de pb
				//$pa->fields['id_adh'] = _ID_ADHERENT;
				$pa->fields['pu'] = $_GET['pu'];
				$pa->save();
			}

			$a_pa = array();
			$db->query("
				SELECT	pa.*, c.libelle AS libcond
				FROM	dims_mod_cata_prixachat pa

				INNER JOIN	dims_mod_cata_conditionnement c
				ON			c.id = pa.id_cond

				WHERE	pa.id_article = {$_GET['id_article']}");
			while ($row = $db->fetchrow()) {
				$a_pa[] = $row;
			}

			echo "<table cellpadding=\"2\" cellspacing=\"0\">";
			foreach ($a_pa as $pa) {
				echo "
					<tr>
						<td>{$pa['libcond']} :&nbsp;</td>
						<td>".formatprice($pa['pu'])."&nbsp;&euro;</td>
					</tr>";
			}
			echo "</table>";
		}

		ob_end_flush();
		die();
		break;
	case 'reporting':
	case 'export':
		include './common/modules/catalogue/admin_article_reporting.php';
		break;
	default:
		include './common/modules/catalogue/public_article_list.php';
		break;
}
