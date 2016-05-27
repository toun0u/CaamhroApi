<?php

$view = view::getInstance();

$sub_action = $view->get('sa');

switch($sub_action){
	default:
	case 'edit':
		$view->assign( 'tvas', tva::getDistinctCodes() );
		$ccat = client_category::findByDefault(true);
		$article = $view->get('article');
		$view->assign( 'degressifs', $article->getDegressiveTable($ccat->get('id')));

		$view->assign( 'net_prices', $article->getPrixNets(true));
		$view->render('articles/show/tarifs_edit.tpl.php');
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
			#Pour la case à cocher dégressif
			$art->fields['degressif'] = 0;

			$message = "";
			$art->setvalues($_POST, 'article_');#écrasera notamment le dégressif ou l'actif
			#Contrôle sur l'unicité de la référence
			$by_ref = new article();
			$by_ref->findByRef(dims_sql_filter($art->fields['reference']));
			if( $by_ref->isNew() || ( ! $new && $by_ref->get('id') == $art->get('id') ) ){
				if($art->fields['ctva'] == 'dims_nan') $art->fields['ctva'] = '';
				$continue = $continue && $art->save();
			}
			else{
				$error = true;
				$message = dims_constant::getVal('REFERENCE_ALREADY_TAKEN');
				$path = get_path('articles', 'show', array('id' => $art->get('id')));
			}
		}
		if($continue){
			#Traitement des tarifs dégressifs
			##Dans tous les cas on supprime les dégressifs connus
			$art = array_shift($articles);
			$art->clearDegressiveTable();

			if($art->isDegressif()){
				$degressifs = dims_load_securvalue('degressifs', dims_const::_DIMS_CHAR_INPUT, true,true, true);
				if( ! empty($degressifs) ){
					#récupération de la catégorie de client par défaut
					$ccat = client_category::findByDefault(true);#Le true permet de forcer la création s'il n'existe pas
					ksort($degressifs); #Juste pour être sûr même si le javascript de la vue est censé avoir trié les données
					$cpt = 1;
					$discount = new tarif_qte();
					$discount->setCategoryClient($ccat->get('id'));
					$discount->setArticleID($art->get('id'));
					foreach($degressifs as $qty => $price){
						if($cpt <= 12){
							$discount->addStep($cpt, $qty, $price);
						}
						else break; #On ne va pas au delà, la structure du Modèle ne permet d'en faire plus
						$cpt++;
					}
					$discount->save();
				}
			}

			#Traitement des prix nets
			##On nettoie d'abord les prix nets précédents
			$art->clearNetPrices();
			$np_elements = dims_load_securvalue('np_elements', dims_const::_DIMS_CHAR_INPUT, true,true, true);
			if( ! empty($np_elements) ){
				foreach($np_elements as $code => $puht){
					$art->createPrixNets($code, $puht);
				}
			}
			#Redirection au bon endroit
			$view->flash(dims_constant::getVal('ARTICLE_HAS_BEEN_UPDATED'), 'success');
			dims_redirect(get_path('articles', 'show', array('id' => $art->get('id'))));
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
