<?php
include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/articles_helpers.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_tva.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_famille.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_client_category.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_tarif_qte.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_thumb.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_kit.php";

$view = view::getInstance();
$view->setLayout('layouts/lateralized_layout.tpl.php');
// infos contexuelles
$view->assign('a', $a);

switch ($a) {
	default:
	case 'index':
		#Récupération du clipboard
		$clipboard = get_clipboard();

		#Gestion des filtres
		$init_filter		= dims_load_securvalue('filter_init',	dims_const::_DIMS_NUM_INPUT, true,true, true);

		if(isset($init_filter) && $init_filter){
			unset($_SESSION['cata']['articles']['index']);
		}

		$cur_publication	= &get_sessparam($_SESSION['cata']['articles']['index']['publication'], 'all');
		$cur_type			= &get_sessparam($_SESSION['cata']['articles']['index']['type'], 'all');
		$cur_famille		= &get_sessparam($_SESSION['cata']['articles']['index']['famille'], 'dims_nan');
		$cur_unattached		= &get_sessparam($_SESSION['cata']['articles']['index']['unattached'], 0);
		$cur_in_clipboard	= &get_sessparam($_SESSION['cata']['articles']['index']['in_clipboard'], 0);
		$cur_keywords		= &get_sessparam($_SESSION['cata']['articles']['index']['keywords'], '');
		$cur_page			= &get_sessparam($_SESSION['cata']['articles']['index']['page'], 0);

		$publication		= dims_load_securvalue('published',	dims_const::_DIMS_CHAR_INPUT,	true,true, true, $cur_publication, 'all', true);
		$type				= dims_load_securvalue('type',		dims_const::_DIMS_CHAR_INPUT,	true,true, true, $cur_type, 'all', true);
		$famille			= dims_load_securvalue('families',	dims_const::_DIMS_CHAR_INPUT,	true,true, true, $cur_famille, 'dims_nan', true);
		$unattached			= dims_load_securvalue('unattached',	dims_const::_DIMS_NUM_INPUT,	true,true, true, $cur_unattached, 0, true);
		$in_clipboard		= dims_load_securvalue('in_clipboard',	dims_const::_DIMS_NUM_INPUT,	true,true, true, $cur_in_clipboard, 0, true);
		$keywords			= dims_load_securvalue('keywords',	dims_const::_DIMS_CHAR_INPUT,	true,true, true, $cur_keywords, '', true);

		$view->assign('publication', $publication);
		$view->assign('type', $type);
		$view->assign('family', $famille);
		$view->assign('unattached', $unattached);
		$view->assign('in_clipboard', $in_clipboard);
		$view->assign('keywords', $keywords);

		## Récupération des familles pour le filtre sur les familles
		$root = cata_famille::getRootCatalogue($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']]);
		$root->initDescendance($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']]);
		$familles =	familles_aplat($root);

		$view->assign('familles', $familles);

		#options de tri
		$sort_by			= &get_sessparam($_SESSION['cata']['articles']['index']['sort_by'], 'ref');
		$sort_way			= &get_sessparam($_SESSION['cata']['articles']['index']['sort_way'], 'ASC');

		$sb					= dims_load_securvalue('sort_by',	dims_const::_DIMS_CHAR_INPUT,	true,true, true, $sort_by);
		$sw					= dims_load_securvalue('sort_way',	dims_const::_DIMS_CHAR_INPUT,	true,true, true, $sort_way);

		$view->assign('sort_by', $sb);
		$view->assign('sort_way', $sw);

		#pagination
		$page = dims_load_securvalue('page',dims_const::_DIMS_NUM_INPUT,true,true, true, $cur_page);

		#option in_clipboard
		if( $in_clipboard ){
			$cliped = $clipboard;
		}
		else $cliped = null;

		#search filters
		$search_filters = array();

		# Nettoyage des filtres si aucune famille et aucun mot clé
		if ( empty($keywords) && (empty($famille) || $famille == 'dims_nan') ) {
			foreach ($_SESSION['cata']['articles']['index'] as $key => $value) {
				if (preg_match('/^filter(?P<field_id>[\d]+)$/', $key, $matches)) {
					unset($_SESSION['cata']['articles']['index'][$key]);
				}
			}
		}
		else {
			if (sizeof($_POST)) {
				foreach ($_POST as $key => $value) {
					if (preg_match('/^filter(?P<field_id>[\d]+)$/', $key, $matches)) {
						${'cur_'.$key} = &get_sessparam($_SESSION['cata']['articles']['index'][$key], 'all');
						$$key = dims_load_securvalue($key, dims_const::_DIMS_CHAR_INPUT, false, true, false, ${'cur_'.$key});
						if ($$key != 'all') {
							$search_filters[$matches['field_id']] = $$key;
						}
					}
				}
			}
			else {
				foreach ($_SESSION['cata']['articles']['index'] as $key => $value) {
					if (preg_match('/^filter(?P<field_id>[\d]+)$/', $key, $matches)) {
						${'cur_'.$key} = &get_sessparam($_SESSION['cata']['articles']['index'][$key], 'all');
						$$key = dims_load_securvalue($key, dims_const::_DIMS_CHAR_INPUT, false, true, false, ${'cur_'.$key});
						if ($$key != 'all') {
							$search_filters[$matches['field_id']] = $$key;
						}
					}
				}
			}
		}

		$art = new article();
		$art->page_courant = $page;
		$art->setPaginationParams(30, 5, false, '<<', '>>', '<', '>');
		$art->setAdditionalFilters($search_filters);

		# Mode de fonctionnement des filtres
		require_once DIMS_APP_PATH.'modules/catalogue/include/class_config.php';
		$config = cata_config::get($_SESSION['dims']['moduleid']);
		$art->setFiltersView($config->getFiltersView());

		$articles = $art->build_index($_SESSION['dims']['currentlang'], article::STATUS_OK, $publication, $type, $famille, $unattached, $keywords, $cliped, $sb, $sw, true);
		$view->assign('total_articles', $art->total_index);

		#filtres
		$a_filters = array();
		$filters = $art->getFilters();
		if (!is_null($filters)) {
			foreach ($filters as $filter_id) {
				$champ = new cata_champ();
				$champ->open($filter_id);

				// On n'affiche que les champs de type 'liste'
				if(!$champ->isNew() && $champ->fields['type'] == cata_champ::TYPE_LIST) {
					$tab_filter_values_ids = $art->getFilterValues($filter_id);
					if (!is_null($tab_filter_values_ids)) {
						$filter_values_ids = array_keys($tab_filter_values_ids);
						$filter_values = array('all' => dims_constant::getVal('_DIMS_ALLS'));
						foreach ($champ->getvaleurs($_SESSION['dims']['currentlang'], false, $filter_values_ids) as $champ_valeur) {
							$filter_values[$champ_valeur->get('id')] = $champ_valeur->getValeur();
						}

						$a_filters[$champ->get('id')] = array(
							'filter' 	=> $champ,
							'values' 	=> $filter_values,
							'selected' 	=> (isset($search_filters[$champ->get('id')]) ? $search_filters[$champ->get('id')] : 'all')
							);
					}
				}
			}
		}
		$view->assign('filters', $a_filters);

		#assignation du contenu de la pagination
		$view->assign('pagination', $art->getPagination());
		#assignation des articles à la vue
		$view->assign('articles', $articles);

		#Affichage ou non des filtres
		$cur_mode = &get_sessparam($_SESSION['cata']['articles']['filters_mode'], 'show');
		$view->assign('filters_mode', $cur_mode);

		#Actions contextuelles
		$actions = array();
		$actions[0]['picto'] = 'gfx/ajouter20.png';
		$actions[0]['text'] = dims_constant::getVal('CREATE_AN_ARTICLE');
		$actions[0]['link'] = get_path('articles', 'new');

		$view->assign('actions', $actions);
		$view->render('articles/index.tpl.php');
		break;

	case 'switch_filters':
		ob_clean();
		$cur_mode	= &get_sessparam($_SESSION['cata']['articles']['filters_mode'], 'show');
		$mode		= dims_load_securvalue('mode',	dims_const::_DIMS_CHAR_INPUT,	true,true, true, $cur_mode);
		die();
		break;

	case 'handle_selection':
		$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true,true, true);
		if( !empty($action) ){
			switch($action){
				case 'copy':
					$lst = dims_load_securvalue('selection',dims_const::_DIMS_NUM_INPUT,true,true);
					copy_articles($lst);
					$view->flash(dims_constant::getVal('ARTICLES_COPIED_IN_CLIPBOARD'), 'success');
					dims_redirect(get_path('articles', 'index'));
					break;
				case 'revert':
					$lst = dims_load_securvalue('selection', dims_const::_DIMS_NUM_INPUT,true,true);
					article::reverse_publication($lst);
					$view->flash(dims_constant::getVal('REVERSE_PUBLICATION_DONE'), 'success');
					$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT,true,true);
					if( ! empty($from) ) dims_redirect(get_path('articles', 'show', array('id' => $from)));
					else dims_redirect(get_path('articles', 'index'));
					break;
			}
		}
		break;

	case 'shift_clipboard':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true,true, true);
		if(del_clipboard_article($id))
			$view->flash(dims_constant::getVal('ARTICLE_DELETED_FROM_CLIPBOARD'), 'success');
		else $view->flash(dims_constant::getVal('ARTICLE_NOT_IN_CLIPBOARD'), 'error');
		dims_redirect(get_path('articles', 'index'));
		break;

	case 'empty_clipboard':
		empty_clipboard();
		$view->flash(dims_constant::getVal('CLIPBOARD_EMPTY'), 'success');
		dims_redirect(get_path('articles', 'index'));
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
			$i = 0;
            // ajoute aussi une limite au renvoi car ne sert a rien de faire trop
			foreach($articles as $article){
                if ($i<200) {
                    $tab[$i]['id'] = $article->fields['id'];
                    $tab[$i]['label'] = $article->fields['label'];
                    $tab[$i]['reference'] = $article->fields['reference'];
                    $i++;
                }
			}
			echo json_encode($tab);
		}
		die();
		break;

	case 'js_article_info':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$qty = dims_load_securvalue('qty', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$puht = dims_load_securvalue('puht', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$total = dims_load_securvalue('total', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$remise_ht = dims_load_securvalue('remise_ht', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$remise_pourc = dims_load_securvalue('remise_pourc', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if(empty($puht)) $puht = 0;
		if(empty($total)) $total = 0;
		if(empty($remise_ht)) $remise_ht = -1;
		if(empty($remise_pourc)) $remise_pourc = 0;

		if( ! empty($id) && !empty($qty)){
			$article = new article();
			$article->open($id);
			if( ! $article->isNew() ){
				$tab['id'] = $article->get('id');
				$tab['photo_path'] = $article->getVignette(20);
				$tab['ref'] = $article->fields['reference'];
				$tab['label'] = $article->fields['label'];
				$tab['puht'] = number_format($article->fields['putarif_0'],2,'.', '');
				$tab['total'] = $qty * $tab['puht'];
				$tab['kit_total'] = number_format( $total + ($article->fields['putarif_0'] * $qty),2,'.', '' );
				$tab['kit_win'] = number_format($total + ($article->fields['putarif_0'] * $qty) - $puht,2,'.', '' );
				$tab['remise_ht'] = $tab['puht'];
				$tab['remise_pourc'] = 0;
				if($remise_ht > 0 && $remise_ht < $tab['puht']){
					$tab['remise_ht'] = number_format($remise_ht,2,'.', '');
					$tab['remise_pourc'] = number_format((($tab['puht']-$remise_ht)*100)/$tab['puht'],2);
				}elseif($remise_pourc > 0 && $remise_pourc <= 100){
					$tab['remise_pourc'] = number_format($remise_pourc,2);
					$tab['remise_ht'] =  number_format($tab['puht']-(($tab['puht']*$remise_pourc)/100),2,'.', '');
				}
				echo json_encode($tab);
			}
		}
		die();
		break;

	case 'js_get_kit_amounts':
		ob_clean();
		$puht = dims_load_securvalue('puht', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$total = dims_load_securvalue('total', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$tab['kit_total'] = number_format($total, 2,'.', '' );
		$tab['kit_win'] = number_format($total - $puht, 2,'.', '' );
		echo json_encode($tab);
		die();
		break;

	case 'js_prix_net_info':
		ob_clean();
		$id_article = dims_load_securvalue('id_article', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$code_client = dims_load_securvalue('code_client', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$net_price = dims_load_securvalue('net_price', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		$net_reduction = dims_load_securvalue('net_reduction', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		if( ! empty($code_client) && ! empty($id_article)){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_client.php";
			$cli = new client();
			$cli->openByCode($code_client);
			$art = new article();
			$art->open($id_article);
			if( !$cli->isNew() && !$art->isNew() ){
				$tab = array();
				$tab['photo_path'] = $cli->getVignette(20);
				$tab['ref'] = $cli->getCode();
				$tab['label'] = $cli->getName();
				$base_tarif = $art->getPUHT();
				if($net_price > 0){
					$tab['net_price'] = $net_price;
					$tab['reduction'] = (1 - ($net_price / $base_tarif) ) * 100;
				}
				elseif($net_reduction > 0){
					$tab['net_price'] = $base_tarif * (1 - ($net_reduction / 100));
					$tab['reduction'] = $net_reduction;
				}
				echo json_encode($tab);
			}
		}
		die();
		break;

	case 'new':
		#Gestion des actions contextuelles
		$actions = array();
		$actions[0]['picto'] = 'gfx/retour20.png';
		$actions[0]['text'] = dims_constant::getVal('_RETURN_LIST_ARTICLES');
		$actions[0]['link'] = dims::getInstance()->getScriptEnv().'?c=articles&a=index';
		$view->assign('actions', $actions);

		## Récupération des familles pour le filtre sur les familles
		$root = cata_famille::getRootCatalogue($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']]);
		$root->initDescendance($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']]);
		$familles =	familles_aplat($root);
		$view->assign('familles', $familles);

		// Famille de provenence
		$referrer_family = dims_load_securvalue('referrer_family', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if ($referrer_family > 0) {
			$view->assign('referrer_family', $referrer_family);
		}
		else {
			$view->assign('referrer_family', '');
		}

		#Initialisation d'un nouvel article
		$article = new article();
		$article->init_description();
		$article->setugm();
		$view->assign('article', $article);

		#Récupération des codes TVA disponibles
		$view->assign( 'tvas', tva::getDistinctCodes() );
		$view->render('articles/new.tpl.php');
		break;

	case 'save':
		$idgo = dims_load_securvalue('id_globalobject', dims_const::_DIMS_NUM_INPUT, true,true, true);
		$art = new article();
		$new = true;
		if( ! empty($idgo) ){
			$art->openWithGB($idgo);
			if( ! $art->isNew() ) $new = false;
		}
		if($new){
			$art->init_description();
			$art->setugm();
			$art->fields['id_lang'] = $_SESSION['dims']['currentlang'];
		}
		#Traitement des champs stockés dans les langues
		$designation = dims_load_securvalue('lang_article_designation', dims_const::_DIMS_CHAR_INPUT, true,true, true);
		if(empty($designation)) $designation = dims_load_securvalue('article_label', dims_const::_DIMS_CHAR_INPUT, true,true, true);
		$description = dims_load_securvalue('lang_article_description', dims_const::_DIMS_CHAR_INPUT, true,true, true);

		$art->fields['label'] = $designation;
		$art->fields['description'] = $description;

		if ($new) {
			$art->setUrlrewrite();
		}

		#Pour la case à cocher dégressif
		$art->fields['degressif'] = 0;
		#Pour la case à cocher actif
		$art->fields['published'] = 0;
		#Pour le radio bouton sur le fait que c'est un kit
		$art->fields['kit'] = 0;
		$error = false;
		$message = "";
		$art->setvalues($_POST, 'article_');#écrasera notamment le dégressif ou l'actif
		#Contrôle sur l'unicité de la référence
		$by_ref = new article();
		$by_ref->findByRef(dims_sql_filter($art->fields['reference']));
		if( $by_ref->isNew() || ( ! $new && $by_ref->get('id') == $art->get('id') ) ){
			$art->enable();
			if($art->fields['ctva'] == 'dims_nan') $art->fields['ctva'] = '';
			if($art->save()){
				#Traitement du rattachement à la famille
				##Dans tous les cas on purge le rattachement de l'article
				if( ! $new ) $art->cleanFamiliesAttachment();
				$families = dims_load_securvalue('families', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				if( ! empty($families) ){
					foreach($families as $fam){
						#Enregistrement du lien avec la famille
						$link = new cata_article_famille();
						$link->create($fam, $art->get('id'));
					}
				}

				#Traitement des tarifs dégressifs
				##Dans tous les cas on supprime les dégressifs connus
				if( ! $new ){
					$art->clearDegressiveTable();
				}
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

				#Traitement de la photo - Le champ vignette n'est que dans le formulaire de création
				if(isset($_FILES['vignette']['error']) && $_FILES['vignette']['error'] === 0){
					$art->storeVignette($_FILES['vignette']);
				}

				#Traitement des kits
				##Dans tous les cas on supprime la composition connue
				if( ! $new ){
					$art->clearKitComposition();
				}
				if($art->isKit()){
					$components = dims_load_securvalue('kit_composition', dims_const::_DIMS_CHAR_INPUT, true,true, true);
					if( ! empty($components) ){
						foreach($components as $id_component => $qty){
							$composite = new article_kit();
							$composite->create($art->get('id'), $id_component, $qty);
						}
					}
				}

				#Redirection au bon endroit
				if($new) $view->flash(dims_constant::getVal('ARTICLE_HAS_BEEN_CREATED'), 'success');
				else $view->flash(dims_constant::getVal('ARTICLE_HAS_BEEN_UPDATED'), 'success');
				if(empty($_POST['continue'])) dims_redirect(get_path('articles', 'index'));
				else dims_redirect(get_path('articles', 'new'));
			}
		}
		else {
			$art->setLightAttribute('global_error', dims_constant::getVal('REFERENCE_ALREADY_TAKEN'));
			$art->setvalues($_POST, 'article_');
			$view->assign('article', $art);

			#Gestion des actions contextuelles
			$actions = array();
			$actions[0]['picto'] = 'gfx/retour20.png';
			$actions[0]['text'] = dims_constant::getVal('_RETURN_LIST_ARTICLES');
			$actions[0]['link'] = dims::getInstance()->getScriptEnv().'?c=articles&a=index';
			$view->assign('actions', $actions);

			## Récupération des familles pour le filtre sur les familles
			$root = cata_famille::getRootCatalogue($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']]);
			$root->initDescendance($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']]);
			$familles =	familles_aplat($root);
			$view->assign('familles', $familles);

			#Récupération des codes TVA disponibles
			$view->assign( 'tvas', tva::getDistinctCodes() );
			$view->render('articles/new.tpl.php');
		}
		break;

	case 'disable':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true,true, true);
		#désactivation de l'article
		$art = new article();
		$art->open($id);
		if( ! $art->isNew() ){
			$art->disable(true);#Le true indique que ça doit faire un autosave. La méthode s'occupe de chaque langue
		}
		#Eventuellement, supprimer l'article du clipboard
		if(in_clipboard($id)) del_clipboard_article($id);
		#Eventuellement, supprimer l'article des derniers articles consultés
		del_from_lastarticles($id);
		$view->flash(dims_constant::getVal('ARTICLE_DELETED'), 'notice');
		dims_redirect(get_path('articles', 'index'));
		break;

	case 'show':
		$view->setLayout('layouts/article_layout.tpl.php');

		include_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
		$article = new article();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true,true, true);

		if ($id>0) $article->open($id);

		if($id>0 && ! $article->isDeleted() ){
			store_lastarticle($id, 3);//+1
			$view->assign('article', $article);

			$view->assign('familles', $article->getFamilles($_SESSION['dims']['currentlang']));

			$sub_control = dims_load_securvalue('sc',dims_const::_DIMS_CHAR_INPUT,true,true);
			if($sub_control == '') $sub_control = "tarifs";
			$view->assign('sc', $sub_control);

			$sub_action = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
			$view->assign('sa', $sub_action);

			// Ouverture du param cata_base_ttc
			$dims = dims::getInstance();
			$mods = $dims->getModuleByType('catalogue');
			$catalogue_moduleid = $mods[0]['instanceid'];

			$oCatalogue = new catalogue();
			$oCatalogue->open($catalogue_moduleid);
			$oCatalogue->loadParams();

			$view->assign('cata_base_ttc', $oCatalogue->getParams('cata_base_ttc'));

			switch($sub_control){
				default:
				case 'tarifs':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/articles/tarifs_controller.php';
					break;
				case 'description':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/articles/description_controller.php';
					break;
				case 'kit':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/articles/kit_controller.php';
					break;
				case 'vignettes':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/articles/vignettes_controller.php';
					break;
				case 'links':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/articles/links_controller.php';
					break;
				case 'references':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/articles/references_controller.php';
					break;
			}

			#Gestion des actions contextuelles
			$actions = array();
			$actions[0]['picto'] 	= 'gfx/retour20.png';
			$actions[0]['text'] 	= dims_constant::getVal('_RETURN_LIST_ARTICLES');
			$actions[0]['link'] 	= dims::getInstance()->getScriptEnv().'?c=articles&a=index';

			$actions[1]['picto'] 	= $article->isPublished() ? 'gfx/depublier20.png' : 'gfx/publier20.png';
			$actions[1]['text'] 	= dims_constant::getVal('_INVERT_PUBLICATION');
			$actions[1]['link'] 	= get_path('articles', 'handle_selection', array('action' => 'revert', 'selection[0]' => $article->fields['id'], 'from' => $article->fields['id']));

			$actions[2]['picto']	= 'gfx/poubelle20.png';
			$actions[2]['text']	= dims_constant::getVal('DELETE_ARTICLE');
			$actions[2]['confirm']	= true;
			$actions[2]['txt_confirm']	= dims_constant::getVal('SURE_DELETE_ARTICLE');
			$actions[2]['link'] 	= get_path('articles', 'disable', array('id' => $article->fields['id']));
			$view->assign('actions', $actions);
		}
		else{
			$view->setLayout('layouts/default_layout.tpl.php');
			$view->flash(dims_constant::getVal('TRY_ACCESS_DELETED_ARTICLE').' <a href="'.get_path('articles', 'index').'">'.dims_constant::getVal('_RETURN_LIST_ARTICLES').'</a>', 'error');
		}
		break;
	case 'json_article':
		$idarticle = dims_load_securvalue('id', dims_const::_DIMS_CHAR_INPUT, true ,true, true);

		$article = article::find_by(array('id' => $idarticle), null, null, 1);

		ob_clean();
		echo json_encode(
			$article->fields + array(
				'puht'      => $article->getPUHT(),
				'remise'    => $article->getLocalRemise(),
				'puremise'  => $article->calculate_PUHTRemise(),
				'tauxtva'   => $article->getTauxTVA(),
				'puttc'     => $article->calculate_PUTTC(),
			)
		);
		die();
		break;
}

#Derniers articles consultés
$view->assign('last_articles', get_lastarticles());

#Gestion de l'objet de recherche dans la barre latérale
$default_search = dims_constant::getVal('SEARCH_A_PRODUCT');
$view->assign('default_search', $default_search); #permet d'indiquer le texte qui va se placer dans le champ de recherche de la barre latérale
$view->assign('path_lateral_search', get_path('articles', 'index'));
$cur_keywords = &get_sessparam($_SESSION['cata']['articles']['index']['keywords'], '');
if( empty($keywords) && ! empty($cur_keywords)) $keywords = $cur_keywords;
if( ! empty($keywords) && $keywords == $default_search) $keywords = '';
$view->assign('keywords', isset($keywords)?$keywords : '');

$view->render('articles/lateral.tpl.php', 'lateral');
