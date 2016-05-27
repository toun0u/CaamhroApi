<?php

$view = view::getInstance();

$sub_action = $view->get('sa');

switch($sub_action){
	default:
	case 'edit':
		#Récupération de la composition de l'article
		$article = $view->get('article');
		$view->assign('kit', $article->getKit(true));
		$view->render('articles/show/kit_edit.tpl.php');
		break;
	case 'update':
		$idgo = dims_load_securvalue('id_globalobject', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$articles = array();
		$new = true;
		if(isset($idgo)){
			$articles = article::findByGO($idgo);
		}
		$continue = true;
		$error = false;
		foreach($articles as $art){
			$new = false;
			#Pour les radios boutons on réinitialise à 0 avant de faire le set value
			$art->fields['kit'] = 0;

			$message = "";
			$art->setvalues($_POST, 'article_');#écrasera notamment le dégressif ou l'actif
			#Contrôle sur l'unicité de la référence
			$by_ref = new article();
			$by_ref->findByRef(dims_sql_filter($art->fields['reference']));
			if( $by_ref->isNew() || ( ! $new && $by_ref->get('id') == $art->get('id') ) ){
				$continue = $continue && $art->save();
			}
			else{
				$error = true;
				$message = dims_constant::getVal('REFERENCE_ALREADY_TAKEN');
				$path = get_path('articles', 'show', array('id' => $art->get('id')));
			}
		}
		if($continue){
			#Traitement des kits
			##Dans tous les cas on supprime la composition connue
			$art = array_shift($articles);
			$art->clearKitComposition();

			if($art->isKit()){
				$components = dims_load_securvalue('kit_composition', dims_const::_DIMS_CHAR_INPUT, true,true, true);
				if( ! empty($components) ){
					foreach($components as $id_component => $qty){
						$composite = new article_kit();
						$composite->create($art->get('id'), $id_component, $qty);
					}
				}
			}
			$view->flash(dims_constant::getVal('ARTICLE_HAS_BEEN_UPDATED'), 'success');
			dims_redirect(get_path('articles', 'show', array('sc' => 'kit', 'sa' => 'edit', 'id' => $art->get('id'))));
		}
		if(!$continue || $new){
			$error = true;
			$message = dims_constant::getVal('ERROR_THROWN');
			$path = get_path('articles', 'index');
		}

		if($error){
			$art->setLightAttribute('global_error', $message);
			dims_redirect($path);
		}
		break;
}
?>
