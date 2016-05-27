<?php

$view = view::getInstance();

$sub_action = $view->get('sa');

switch($sub_action){
	default:
	case 'edit':
		## Assignation des langues disponibles
		$view->assign('langs', cata_param::getActiveLang());
		$view->assign('translations', $view->get('article')->getTranslations());
		$pick_language = dims_load_securvalue('pick_language', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$view->assign('pick_language', $pick_language);

		## Récupération des familles pour le filtre sur les familles
		$root = cata_famille::getRootCatalogue($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']]);
		$root->initDescendance($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']]);
		$familles =	familles_aplat($root);
		$view->assign('cata_familles', $familles);
		#Récupération des champs libres des familles associées
		$my_families = $view->get('familles');
		$champs_libres = array();
		foreach($my_families as $fam){
			$champs_libres += $fam->getChampsLibre(); #Le += permet de garder les clefs numériques telles quelles
		}

		$champs_libres = cata_champ::sortByCategories(cata_champ::completeListOfValuesFor($champs_libres));
		$view->assign('champs_libres', $champs_libres);

		#Récupération de tous les champs libres indépendamment des familles de l'article
		$view->assign('all_fields', cata_champ::sortByCategories(cata_champ::completeListOfValuesFor(cata_champ::getAll())));
		$view->render('articles/show/description_edit.tpl.php');
		break;
	case 'update':
		$idgo = dims_load_securvalue('id_globalobject', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$pick_language = dims_load_securvalue('pick_language', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$scope = dims_load_securvalue('fields_scope', dims_const::_DIMS_CHAR_INPUT, true,true, true);
		$articles = array();
		$new = true;
		if(isset($idgo)){
			$articles = article::findByGO($idgo);
		}
		$continue = true;
		$error = false;
		#Traitement uniquement des champs communs
		foreach($articles as $art){
			$new = false;
			$art->fields['published'] = 0;
			$art->setvalues($_POST, 'article_');#Principalement pour savoir s'il est published ou non
			$art->setFieldsScope( ($scope == 'full') ? article::FIELDS_SCOPE_FULL : article::FIELDS_SCOPE_FAMILY);
			$continue = $continue && $art->save();
		}
		if($continue){
			$art = array_shift($articles);
			#Traitement du rattachement aux familles
			##Dans tous les cas on purge le rattachement de l'article
			if( ! $new ) $art->cleanFamiliesAttachment();
			$families = dims_load_securvalue('families', dims_const::_DIMS_CHAR_INPUT, true,true, true);
			if( ! empty($families) ){
				foreach($families as $fam){
					#Enregistrement du lien avec la famille
					$link = new cata_article_famille();
					$link->create($fam, $art->get('id'));
				}
			}

			#Traitement des traductions
			$translations = dims_load_securvalue('translation', dims_const::_DIMS_CHAR_INPUT, true,true, true);
			#Traitement des champs libres
			$champs_libres = dims_load_securvalue('champs_libres', dims_const::_DIMS_CHAR_INPUT, true,true, true);

			if( empty($scope)) $scope = 'full';
			$go_lang = array();
			#Pour pour connaître les langues pour lesquelles on a de l'info, sinon on risque de créer des articles pour rien
			foreach( $champs_libres[$scope] as $id_lang => $tab){
				foreach($tab as $field => $value){
					if( ! empty($value)) {
						if ($value != 'dims_nan'){
							$go_lang[$id_lang][$field] = $value;
						}
						else {
							$go_lang[$id_lang][$field] = null;
						}
					}
				}
			}
			#Contrôle sur le champ obligatoire de la désignation. Au moins le label sur la langue par défaut doit être setté
			if(empty($translations[cata_param::getDefaultLang()]['designation'])){
				$error = true;
				$message = dims_constant::getVal('DEFAULT_DESIGNATION_EMPTY');
				$path = get_path('articles', 'show', array('sc' => 'description', 'sa' => 'edit', 'id' => $art->get('id')));
			}
			else{
				#Alors on peut parcourir les traductions s'il y en a
				foreach($translations as $id_lang => $fields){
					if( ! empty($fields['designation']) || ! empty($fields['description']) ){
						#Dans ce cas on récupère l'objet en question
						if($art->fields['id_lang'] == $id_lang){ #Permet d'éviter de faire des select pour rien
							$article = $art;
						}
						else if( isset($articles[$id_lang])){
							$article = $articles[$id_lang];
						}
						else{#Dans ce cas on crée l'objet en clonant un de ses potes
							$article = new article();
							$article->open($art->fields['id'], $id_lang);
							if( $article->isNew() ){
								$article = $art->createclone(true);
								$article->fields['id'] = $art->fields['id'];#Par défaut la méthode createclone vide l'id
								$article->fields['id_lang'] = $id_lang;
								$article->setugm();
							}
						}
						$article->fields['label'] = $fields['designation'];
						$article->fields['description'] = str_replace('\r\n', '', $fields['description']);

						#traitement des champs libres
						if( isset($go_lang[$id_lang])){
							foreach($go_lang[$id_lang] as $chp => $value){
								$article->fields[$chp] = $value;
							}
						}
						$article->save();
					}
					else if( !empty($go_lang[$id_lang])){#C'est qu'on a un mec qui a renseigné des champs libres sans mettre de désignation
						$error = true;
						$message = dims_constant::getVal('DESIGNATION_SHOULD_NOT_BE_EMPTY'). ' ('.$_SESSION['dims']['lang'][$id_lang].')';
						$path = get_path('articles', 'show', array('sc' => 'description', 'sa' => 'edit', 'id' => $art->get('id'), 'pick_language' => $pick_language));
					}
				}
				if( ! $error){
					$view->flash(dims_constant::getVal('ARTICLE_HAS_BEEN_UPDATED'), 'success');
					dims_redirect(get_path('articles', 'show', array('sc' => 'description', 'sa' => 'edit', 'id' => $art->get('id'), 'pick_language' => $pick_language)));
				}
			}

		}
		if( ! $continue || $new){
			$error = true;
			$message = dims_constant::getVal('ERROR_THROWN');
			$path = get_path('articles', 'index');
		}

		if($error){
			$view->flash($message, 'error');
			dims_redirect($path);
		}
		break;
}
?>
