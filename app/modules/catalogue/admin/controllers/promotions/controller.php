<?php
require_once DIMS_APP_PATH."modules/catalogue/include/class_promotion.php";

$view = view::getInstance();
$view->setLayout('layouts/lateralized_layout.tpl.php');

// infos contexuelles
$view->assign('a', $a);

switch ($a) {
	default:
	case 'index':
		$view->render('promotions/index.tpl.php');

		#Actions contextuelles
		$actions = array();
		$actions[0]['picto'] = 'gfx/ajouter20.png';
		$actions[0]['text'] = dims_constant::getVal('CREATE_A_PROMOTION');
		$actions[0]['link'] = get_path('promotions', 'new');

		$promo = new cata_promotion();
		// $promo->page_courant = $page;
		$promo->setPaginationParams(10, 5, false, '<<', '>>', '<', '>');
		$promotions = $promo->build_index();
		$view->assign('total_promotions', $promo->total_index);

		#assignation du contenu de la pagination
		$view->assign('pagination', $promo->getPagination());
		#assignation des articles à la vue
		$view->assign('promotions', $promotions);

		$view->assign('actions', $actions);
		break;

	case 'new':
		$promo = new cata_promotion();
		$promo->init_description();

		$view->assign('promo', $promo);

		$actions = array();
		$actions[0]['picto'] = 'gfx/retour20.png';
		$actions[0]['text'] = dims_constant::getVal('BACK_TO_LIST_PROMOTIONS');
		$actions[0]['link'] = get_path('promotions', 'index');

		$view->assign('actions', $actions);
		$view->render('promotions/edit.tpl.php');
		break;

	case 'edit':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		if ($id > 0) {
			$promo = new cata_promotion();
			if ($promo->open($id)) {
				$view->assign('promo', $promo);

				$actions = array();
				$actions[0]['picto'] = 'gfx/retour20.png';
				$actions[0]['text'] = dims_constant::getVal('BACK_TO_LIST_PROMOTIONS');
				$actions[0]['link'] = get_path('promotions', 'index');

				$view->assign('actions', $actions);
				$view->render('promotions/edit.tpl.php');
			}
			else {
				dims_redirect(get_path('promotions', 'index'));
			}
		}
		else {
			dims_redirect(get_path('promotions', 'index'));
		}
		break;

	case 'save':
		$id					= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, false, true);
		$libelle			= dims_load_securvalue('intitule', dims_const::_DIMS_CHAR_INPUT, false, true);
		$code				= dims_load_securvalue('code_activation', dims_const::_DIMS_CHAR_INPUT, false, true);
		$date_debut			= dims_load_securvalue('date_debut', dims_const::_DIMS_CHAR_INPUT, false, true);
		$date_fin			= dims_load_securvalue('date_fin', dims_const::_DIMS_CHAR_INPUT, false, true);
		$articles_keep		= dims_load_securvalue('articles_keep', dims_const::_DIMS_NUM_INPUT, false, true);

		$promo = new cata_promotion();
		if ($id > 0) {
			$promo->open($id);
		}
		else {
			$promo->init_description();
		}
		$promo->setLibelle($libelle);
		$promo->setCode($code);
		$promo->setDateDebut(dims_local2timestamp($date_debut));
		$promo->setDateFin(dims_local2timestamp($date_fin, '23:59:59'));

		// on enregistre le visuel sur la promo
		if (!$_FILES['image']['error']) {
			$file = $_FILES['image'];
			$doc = new docfile();
			$doc->setugm();
			$doc->fields['name'] = $file['name'];
			$doc->tmpuploadedfile = $file['tmp_name'];
			$doc->fields['size'] = $file['size'];
			$doc->fields['id_folder'] = 0;
			$error = $doc->save();

			$promo->setImage($doc);
		}

		// On traite le fichier des articles
		if (!$_FILES['articles_file']['error']) {

			if ($articles_keep) {
				$a_articles = $promo->getArticles();
			}
			else {
				$a_articles = array();
			}

			$handle = fopen ($_FILES['articles_file']['tmp_name'], 'r');
			while ($line = trim(fgets($handle, 4096))) {
				$fields = explode(';', $line);
				$a_articles[$fields[0]] = str_replace(',', '.', $fields[1]);
			}
			fclose($handle);
			$promo->setArticles($a_articles);
		}
		else {
			// Traitement des articles
			$components = dims_load_securvalue('promo_composition', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			if( ! empty($components) ){
				$a_articles = array();
				foreach($components as $article_id => $prix){
					$article = new article();
					if ($article->open($article_id)) {
						$a_articles[$article->getReference()] = $prix;
					}
				}
				$promo->setArticles($a_articles);
			}
		}

		$promo->save();
		dims_redirect(get_path('promotions', 'edit', array('id' => $promo->get('id'))));
		break;

	case 'switch_filters':
		ob_clean();
			$cur_mode	= &get_sessparam($_SESSION['cata']['clients']['filters_mode'], 'show');
			$mode		= dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_mode);
		die();
		break;

	case 'activate':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		$promo = new cata_promotion();
		if ($promo->open($id)) {
			$promo->activate();
			$promo->save();
		}
		dims_redirect(get_path('promotions', 'index'));
		break;
	case 'deactivate':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		$promo = new cata_promotion();
		if ($promo->open($id)) {
			$promo->deactivate();
			$promo->save();
		}
		dims_redirect(get_path('promotions', 'index'));
		break;

	case 'delete':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		$promo = new cata_promotion();
		if ($promo->open($id)) {
			$promo->delete();
		}
		dims_redirect(get_path('promotions', 'index'));
		break;

	#Recherche pour autocomplete sur les articles (utilisé notamment dans la composition des kits)
	case 'ac_articles':
		ob_clean();
		$text = dims_load_securvalue('text', dims_const::_DIMS_CHAR_INPUT, true,true, true);
		if( isset($text)){
			$art = new article();
			$art->activePagination(false);
			$articles = $art->build_index($_SESSION['dims']['currentlang'], article::STATUS_OK, 'all', 'all', 'dims_nan', 0, $text);
			$tab = array();
			foreach($articles as $article){
				$tab[] = array(
					'id' => $article->fields['id'],
					'label' => $article->fields['label'],
					'reference' => $article->fields['reference'],
				);
			}
			echo json_encode($tab);
		}
		die();
		break;

	case 'js_article_info':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		$prix = dims_load_securvalue('prix', dims_const::_DIMS_CHAR_INPUT, true, false);
		if ($id > 0 && $prix != '') {
			$article = new article();
			if ($article->open($id)) {
				$tab['id']		= $article->get('id');
				$tab['ref']		= $article->getReference();
				$tab['label']	= $article->getLabel();
				$tab['prix']	= floatval(str_replace(',', '.', $prix));
				echo json_encode($tab);
			}
		}
		die();
		break;
}

$view->render('promotions/lateral.tpl.php', 'lateral');
