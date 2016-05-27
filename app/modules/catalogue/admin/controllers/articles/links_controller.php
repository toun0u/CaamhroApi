<?php

$view = view::getInstance();

$sub_action = $view->get('sa');

switch($sub_action){
	default:
	case 'index':
		$article = $view->get('article');
		$view->assign('link_types', link_type::all());
		$view->assign('links', $article->getLinkedArticles());
		$view->render('articles/show/links_index.tpl.php');
		break;
	case 'new':
		#Création d'un objet vierge, prêt à la création
		$link = new article_link();
		$link->init_description(true);
		$link->setugm();
		$link->setArticleFrom($view->get('article')->get('id'));
		$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true,true, true, $type, link_type::TYPE_COMPLEMENTAIRE);
		$link->setType($type);
		$link->setSymetric(article_link::SYM_LINK);
		$view->assign('link', $link);
		#Assignation des types connus
		$view->assign('types', link_type::getLabels());
		$view->render('articles/show/edit_link.tpl.php');
		break;
	case 'edit':
		$id = dims_load_securvalue('id_link', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$link = new article_link();
		$link->open($id);
		if(!$link->isNew()){
			$view->assign('link', $link);
			$view->assign('types', link_type::getLabels());
			$linked_to = new article();
			$linked_to->open($link->getArticleTo());
			$view->assign('linked_to', $linked_to);
			$view->render('articles/show/edit_link.tpl.php');
		}
		else{
			$view->flash(dims_constant::getVal('ERROR_THROWN'), 'error');
			dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => 'index', 'id' => $article->get('id'))) );
		}
		break;
	case 'detach':
		$id = dims_load_securvalue('id_link', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$link = new article_link();
		$link->open($id);
		if(!$link->isNew()){
			if( ! $link->isSymetric()){ #On peut directement le détacher
				$link->delete();
				$view->flash(dims_constant::getVal('LINK_DELETED'), 'success');
				dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => 'index', 'id' => $article->get('id'))) );
			}
			else{
				$view->assign('link', $link);
				$view->render('articles/show/detach_symetric_link.tpl.php');
			}
		}
		else{
			$view->flash(dims_constant::getVal('ERROR_THROWN'), 'error');
			dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => 'index', 'id' => $article->get('id'))) );
		}
		break;
	case 'detach_symetric':
		$id = dims_load_securvalue('id_link', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$symetric = dims_load_securvalue('symetric', dims_const::_DIMS_CHAR_INPUT, true,true, true);
		if( ! empty($id) && ! empty($symetric)){
			$link = new article_link();
			$link->open($id);
			if(!$link->isNew()){
				switch($symetric){
					case 'sym':
						$other = article_link::findByCouple($link->getArticleTo(), $link->getArticleFrom());
						if( !is_null($other) && !$other->isNew() ) $other->delete();
						$link->delete();
						break;
					case 'asym':
						$link->delete();
						break;
				}
				$view->flash(dims_constant::getVal('LINK_DELETED'), 'success');
				dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => 'index', 'id' => $article->get('id'))) );
			}
			else $error = true;
		}
		else $error = true;

		if($error){
			$view->flash(dims_constant::getVal('ERROR_THROWN'), 'error');
			dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => 'index', 'id' => $article->get('id'))) );
		}

		break;
	case 'save':
		$id_link = dims_load_securvalue('id_link', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$link = new article_link();
		$new = true;
		if( ! empty($id_link) ){
			$link->open($id_link);
			if( !$link->isNew() ) $new = false;
		}
		else{
			$link->init_description(true);
			$link->setugm();
		}
		$link_id_article_to =  dims_load_securvalue('link_id_article_to', dims_const::_DIMS_NUM_INPUT, true,true, true);
		if( ! empty($link_id_article_to) ){
			if( $article->get('id') != $link_id_article_to){

				// On vérifie que l'article est tenu en stock
				$linked_article = new article();
				$linked_article->open($link_id_article_to);

				// Si l'article n'est pas tenu en stock, on ne l'attache pas
				if (!$linked_article->isHeldInStock()) {
					$view->flash(dims_constant::getVal('ARTICLE_NOT_HELD_IN_STOCK'), 'error');
					dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => ($new) ? 'new' : 'edit', 'id' => $article->get('id'), 'id_link' => $id_link ) ) );
				}

				$link->setSymetric(article_link::ASYM_LINK);
				$link->setvalues($_POST, 'link_');
				$link->setArticleFrom($article->get('id'));

				if($link->save()){
					if($new) $view->flash(dims_constant::getVal('LINK_CREATED'), 'success');
					else $view->flash(dims_constant::getVal('LINK_UPDATED'), 'success');
					if( ! isset($_POST['continue']))
						dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => 'index', 'id' => $article->get('id'))) );
					else dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => 'new', 'id' => $article->get('id'))) );
				}
				else{
					$view->flash(dims_constant::getVal('ERROR_THROWN'), 'error');
					dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => ($new) ? 'new' : 'edit', 'id' => $article->get('id'), 'id_link' => $id_link ) ) );
				}
			}
			else{
				$view->flash(dims_constant::getVal('SAME_ARTICLE'), 'error');
				dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => ($new) ? 'new' : 'edit', 'id' => $article->get('id'), 'id_link' => $id_link ) ) );
			}
		}
		else{
			$view->flash(dims_constant::getVal('LINKED_ARTICLE_EMPTY'), 'error');
			dims_redirect( get_path('articles', 'show', array('sc' => 'links', 'sa' => ($new) ? 'new' : 'edit', 'id' => $article->get('id'), 'id_link' => $id_link ) ) );
		}
		break;
}
?>
