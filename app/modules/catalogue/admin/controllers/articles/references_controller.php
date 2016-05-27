<?php

$view = view::getInstance();

$sub_action = $view->get('sa');
$article = $view->get('article');

switch($sub_action){
	default:
	case 'list':
		$view->assign('references', $article->getReferences());

		$view->render('articles/show/references_list.tpl.php');
		break;
	case 'new':
		$reference = new article_reference();

		$reference->init_description(true);
		$reference->setugm();

		$view->assign('reference', $reference);

		$view->render('articles/show/references_form.tpl.php');
		break;
	case 'edit':
		$referenceId = dims_load_securvalue('id_reference', dims_const::_DIMS_NUM_INPUT, true,true, true);

		$reference = new article_reference();
		$reference->open($referenceId);

		if ($reference->isNew()) {
			$view->flash(dims_constant::getVal('ERROR_THROWN'), 'error');

			dims_redirect(get_path('articles', 'show', array('sc' => 'references', 'sa' => 'list', 'id' => $article->get('id'))));
		}

		$view->assign('reference', $reference);

		$view->render('articles/show/references_form.tpl.php');
		break;
	case 'save':
		$referenceId = dims_load_securvalue('id_reference', dims_const::_DIMS_NUM_INPUT, true,true, true);

		$reference = new article_reference();
		$new = true;
		if (!empty($referenceId)) {
			$reference->open($referenceId);

			$oldPostion = $reference->fields['position'];
		}
		else{
			$reference->init_description(true);
			$reference->setugm();

			$oldPostion = article_reference::getMaxPosition($article->fields['id']) + 1;
		}

		$reference->fields['id_article'] = $article->fields['id'];

		$reference->setvalues($_POST, 'ref_');
		$reference->save();

		$reference->updatePosition($oldPostion);

		dims_redirect(get_path('articles', 'show', array('sc' => 'references', 'sa' => 'list', 'id' => $article->get('id'))));
		break;
	case 'delete':
		$referenceId = dims_load_securvalue('id_reference', dims_const::_DIMS_NUM_INPUT, true,true, true);

		$reference = new article_reference();
		$reference->open($referenceId);

		if (!$reference->isNew()) {
			$reference->delete();
		}

		dims_redirect(get_path('articles', 'show', array('sc' => 'references', 'sa' => 'list', 'id' => $article->get('id'))));
		break;
}
