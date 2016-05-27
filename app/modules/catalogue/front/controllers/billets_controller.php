<?php
$view = view::getInstance();
$lang = $_SESSION['dims']['currentlang'];

switch($view->get('a')){
	default:
	case 'index':
		//Récupération des articles des familles visibles de 1er niveau
		$familles = cata_famille::conditions(array('visible' => 1, 'depth' => 2, 'id_lang' => $lang))->run();
		$prestations = array();
		foreach($familles as $famille){
			//pour chaque famille on récupère d'une part ses sous-familles et d'autres part les articles qui seraient directement sous la famille courante
			$subfamilles = cata_famille::conditions(array('id_parent' => $famille->get('id'), 'id_lang' => $lang))->run();
			$links = cata_article_famille::pick(array('id_article'))->conditions(array('id_famille' => $famille->get('id')))->run();
			$articles = array();
			if(!empty($links)){
				$articles = article::conditions(
					array(
						'id' => array(
							'op'	=> 'in',
							'value'	=> $links
						),
						'id_lang'	=> $lang
					))->run();
			}
			if(!empty($subfamilles)) $prestations['familles'][] = $subfamilles;
			if(!empty($articles)) $prestations['articles'][] = $articles;
		}

		$view->assign('prestations', $prestations);
		$view->render('billets/index.tpl.php');
		break;
	case 'show':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true);
		if(!empty($id) && !empty($type) && ($type == 'art' || $type == 'fam')){
			switch($type){
				case 'art':
					$obj = new article();
					$obj->open($id, $lang);
					if( !empty($obj) && ! $obj->isNew() ){
						$view->assign('object', $obj);
						$view->assign('type', 'art');
						$view->assign('articles', array($obj));
						//gestion du kit
						if($obj->isKit()){
							$compo = $obj->getKit();
							$ids = array();
							foreach($compo as $c){
								$ids[$c->get('id_article_attach')] = $c->get('quantity');
							}
							$articles = article::pick(array('label', 'id'))->conditions(array('id_lang' => $lang, 'id' => array('op' => 'in', 'value' => array_keys($ids))))->run();
							$compo = array();
							foreach($articles as $art){
								$compo[$art['id']] = array('label' => $art['label'], 'qty' => $ids[$art['id']]);
							}
							$view->assign('kit', $compo);
						}
					}
					break;
				case 'fam':
					$obj = new cata_famille();
					$view->assign('type', 'fam');
					$obj->open($id, $lang);
					if( !empty($obj) && ! $obj->isNew() ){
						$view->assign('object', $obj);
						$view->assign('type', 'fam');
						//récupération des sous-articles sous la famille
						$links = cata_article_famille::pick(array('id_article'))->conditions(array('id_famille' => $id))->run();
						$articles = array();
						if(!empty($links)){
							$articles = article::conditions(
								array(
									'id' => array(
										'op'	=> 'in',
										'value'	=> $links
									),
									'id_lang'	=> $lang
								))->run();
						}
						$view->assign('articles', $articles);
					}
					break;
			}
			$view->render('billets/show.tpl.php');
		}
		break;
	case 'validpanier':
		$step = dims_load_securvalue('step', dims_const::_DIMS_NUM_INPUT, true, true);
		if(empty($step)) $step = 1;
		$view->assign('currentstep', $step);
		switch($step){
			default:
			case 1://------------------------------------------------------------ Récapitulatif
				$to_panier = dims_load_securvalue('to_panier', dims_const::_DIMS_NUM_INPUT, true, true);
				if(!empty($to_panier)){
					global $a_tva;
					$total_ht = $total_ttc = 0;
					foreach($to_panier as $id => $qty){
						if($qty){
							$article = article::find_by(array('id_lang' => $lang, 'id' => $id), null, 1);
							if(!empty($article)){
								$puht = catalogue_getprixarticle($article, $qty);
								$puttc = $puht * (1 + ($a_tva[$article->fields['ctva']] / 100));
								$src = '/assets/images/frontoffice/zooparis/design/logo_zoo.png';
								$img = $article->getVignette(100);
								if( ! empty($img) )	$src = $img;

								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['ref']		= $article->fields['reference'];
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['qte']		= $qty;
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['label']	= $article->fields['label'];
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['description']	= $article->fields['description'];
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['photo_path']	= $src;
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['puht']		= catalogue_formateprix($puht);
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['rawpuht']		= $puht;
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['totalht']	= catalogue_formateprix($puht * $qty);
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['rawtotalht']	= $puht * $qty;
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['puttc']		= catalogue_formateprix($puttc);
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['rawputtc']		= $puttc;
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['totalttc']	= catalogue_formateprix($puttc * $qty);
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['rawtotalttc']	= $puttc * $qty;
								$_SESSION['catalogue']['panier']['articles'][$article->fields['reference']]['url']		= ''; ///article/'.$article->fields['urlrewrite'].'.html';

								$total_ht += $puht * $qty;
								$total_ttc += $puttc * $qty;
							}
						}
					}
					$_SESSION['catalogue']['panier']['megatotalht'] = $total_ht;
					$_SESSION['catalogue']['panier']['megatotalttc'] = $total_ttc;
				}

				if(!empty($_SESSION['catalogue']['panier']['articles'])){
					$view->assign('articles', $_SESSION['catalogue']['panier']['articles']);
					$view->render('billets/validpanier/step1.tpl.php');
				}
				else dims_redirect(get_path('billets', 'index'));
				break;

			case 2://------------------------------------------------------------ Saisie des coordonnées
				require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
				$need_new = true;
				if(! empty($_SESSION['catalogue']['client_id'])){
					$client = client::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);
					if(!empty($client) && !$client->isNew()){
						$need_new = false;
					}
				}
				else{
					$site = wce_site::getInstance(dims::getInstance()->getDb());
					$_SESSION['dims']['before_connexion_url'] = get_path('billets', 'validpanier', array('articleid'=> $site->getArticleIDByObject('catalogue', 'Billetterie', $extra), 'step' => 3));
				}

				if($need_new){
					$client = client::build();
				}



				$view->assign('client', $client);
				$view->render('billets/validpanier/step2.tpl.php');
				break;

			case 3://------------------------------------------------------------ Enregistrement du client - Etape de paiement
				require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
				require_once DIMS_APP_PATH.'modules/system/class_group.php';
				$email = dims_load_securvalue('email', dims_const::_DIMS_CHAR_INPUT, true, true);
				$confirmemail = dims_load_securvalue('confirmemail', dims_const::_DIMS_CHAR_INPUT, true, true);
				$from_connexion = dims_load_securvalue('from_connexion', dims_const::_DIMS_NUM_INPUT, true, true);

				if(!empty($email) && !empty($confirmemail)){
					if( $email == $confirmemail ){
						unset($_POST['confirmemail']);
						$need_new = true;
						if(! empty($_SESSION['catalogue']['client_id'])){//on doit gérer le formulaire post
							$client = client::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);
							if(!empty($client) && !$client->isNew()){
								$need_new = false;
							}
						}
						$salt = null;
						if($need_new){
							$existing = user::find_by(array('email' => $email), null, 1);
							if(empty($existing)){
								//Génération d'un code client unique
								$max = client::pick('MAX(id_client)')->run();
								//Récupération du groupe web B2C
								$password  = passgen();
								$res = dims::getInstance()->getPasswordHash($password, $hash, $salt);
								$group = group::find_or_create_by(array('code' => 'WEBB2C', 'id_group' => 1, 'label' => 'Web B2C', 'parents' => '0;1', 'depth' => '2'));
								$client = client::build( array(
									'code_client' 		=> '411W0'.($max[0]+1),
									'dims_group' 		=> $group->get('id'),
									'adr_liv' 			=> '',
									'id_globalobject'	=> 0,
									'bloque'			=> 0,
									'commentaire'		=> '',
									'login'				=> $email,
									'password'			=> $hash
									));

								$client->setUserinfos(array(
									'lastname'	=> 	dims_load_securvalue('nom', dims_const::_DIMS_CHAR_INPUT, true, true),
									'firstname'	=>	dims_load_securvalue('prenom', dims_const::_DIMS_CHAR_INPUT, true, true),
									'salt'		=> 	$salt,
									));
							}
							else{
								$view->flash('Cette adresse email est déjà connue de nos services. Êtes-vous sûr de ne pas être déjà client chez nous ?', 'error');
								dims_redirect(get_path('billets', 'validpanier', array('step' => 2)));
							}
						}

						if($client->get('email') != $email){//il faut mettre à jour le login
							$client->set('login', $email);
							$client->setUserinfos(array(
								'lastname'	=> 	dims_load_securvalue('nom', dims_const::_DIMS_CHAR_INPUT, true, true),
								'firstname'	=>	dims_load_securvalue('prenom', dims_const::_DIMS_CHAR_INPUT, true, true),
								'salt'		=> 	$salt,
								));
							$_SESSION['dims']['login'] = $email;
						}
						$client->setvalues($_POST, '');



						$_SESSION['catalogue']['panier']['client_id'] = $client->save();

						if($need_new){
							//Il faut qu'on mette en session le mec pour lui dire qu'il est connecté
							$user = user::find_by(array('id' => $client->get('dims_user')), null, 1);
							$fields = $user->fields;
							$_SESSION['dims']['connected']				= 1;
							$_SESSION['dims']['login']					= isset($fields['login']) ? $fields['login'] : '';
							$_SESSION['dims']['password']				= isset($fields['password']) ? $fields['password'] : '';
							$_SESSION['dims']['userid']					= isset($fields['id']) ? $fields['id'] : '';
							$_SESSION['dims']['salt']					= isset($fields['salt']) ? $fields['salt'] : '';
							$_SESSION['dims']['usertype']				= isset($fields['type']) ? $fields['type'] : '';
							$_SESSION['dims']['user_code_of_conduct'] 	= isset($fields['code_of_conduct']) ? $fields['code_of_conduct'] : '';
							//astuce pour paraître connecté alors qu'on a pas fait de redirect
							global $smarty;
							$smarty->assign('swich_user_logged_in', true);
						}
						$view->render('billets/validpanier/step3.tpl.php');
					}
					else{
						$view->flash('Les adresses email ne correspondent pas', 'error');
						dims_redirect(get_path('billets', 'validpanier', array('step' => 2)));
					}
				}
				else{
					dims_redirect(get_path('billets', 'validpanier', array('step' => 2)));
				}
				break;

			case 4://------------------------------------------------------------ Remerciements
				require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';

				if (isset($_SESSION['catalogue']['client_id']) && $_SESSION['catalogue']['client_id']>0) {
					$client = client::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);
					if(!empty($client) && !$client->isNew()){
						if (isset($_SESSION['catalogue']['panier']['articles']) &&
							!empty($_SESSION['catalogue']['panier']['articles'])) {

							// on va créer une commande avec le detail de chaque ligne,
							// générer un hash pour l'id unique d'article commandé
							require DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
							$cde = new commande();

							// construction de la structure attendue par la méthode save de la classe commande
							$refarticles=array();

							foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $obj) {
								$art = article::find_by(array('reference' => $ref), null, 1);
								$refarticles[$art->fields['id']]=$obj['qte'];
							}

							$cde->setArticles($refarticles);
							$cde->setMode(commande::_UNITAIRE);
							$cde->fields['id_client'] = $_SESSION['catalogue']['client_id'];
							$cde->fields['code_client'] = $client->fields['code_client'];
							$cde->fields['hors_cata'] = 0;
							$cde->fields['date_cree'] = dims_createtimestamp();
							$cde->fields['date_validation'] = dims_createtimestamp();
							$cde->fields['cli_email'] = $client->fields['login'];
							$cde->fields['mode_paiement'] = 'CB'; // a modifier avec la valeur transmise par le type de paiement
							$cde->fields['user_name'] = $_SESSION['dims']['user']['firstname'].' '.$_SESSION['dims']['user']['lastname'];
							$cde->save();

							// on affecte la nouvelle commande pour avoir le detail
							$_SESSION['catalogue']['id_cmde']=$cde->fields['id_cde'];

							// on delete le panier
							unset($_SESSION['catalogue']['panier']['articles']);

							// on redirige pour avoir le detail de la commande
							dims_redirect(get_path('billets', 'validpanier', array('step' => 41,'id_cmde' =>$_SESSION['catalogue']['id_cmde'])));
						}
						else {
							// la personne est revenue en arrière
							if (isset($_SESSION['catalogue']['id_cmde']) && $_SESSION['catalogue']['id_cmde']>0) {
								dims_redirect(get_path('billets', 'validpanier', array('step' => 41,'id_cmde' =>$_SESSION['catalogue']['id_cmde'])));
							}
							else dims_redirect(get_path('billets', 'validpanier', array('step' => 1)));
						}

					}
					else dims_redirect(get_path('billets', 'validpanier', array('step' => 3)));
				}
				else dims_redirect(get_path('billets', 'validpanier', array('step' => 3)));
				break;
			case 41:
				require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';

				// test si la ref client est bien en session, permet le rapprochement avec la ref commande passée en paramètre
				if (isset($_SESSION['catalogue']['client_id']) && $_SESSION['catalogue']['client_id']>0) {
					$client = client::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);

					// test si le client existe
					if(!empty($client) && !$client->isNew()){
						require DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
						$cde = new commande();
						$id_cmde = dims_load_securvalue('id_cmde', dims_const::_DIMS_NUM_INPUT, true, true);

						// test si l'id de commande est bien > 0
						if ($id_cmde>0) {
							$commande = commande::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);

							if(!empty($commande) && !$commande->isNew() && $commande->fields['id_client']==$_SESSION['catalogue']['client_id']){
								// on traite les articles contenus dans la commande
								$view->flash('Votre paiement a été effectué avec succès');
								$view->assign('articles', array());
								$view->assign('id_cde', $id_cmde);
								$view->render('billets/validpanier/step4.tpl.php');
							}
							else {
								// on est en erreur sur la consultation d'une commande
								$view->flash('Consultation non autorisée', 'error');
								session_destroy();
								dims_redirect(get_path('billets', 'index'));
							}

						}
					}
				}
				break;
			case 5://------------------------------------------------------------ Download

				// c'est la que cela devient sympa
				// on va prendre le modèle ODT pour générer les PDF

				break;

		}
		break;
	case 'generator':
		$id_cde = dims_load_securvalue('id_cde', dims_const::_DIMS_NUM_INPUT, true, true);
		$success = generator_tickets($id_cde);
		die('rr');
		if(!$success){
			$view->flash('Une erreur s\'est produite', 'error');
			dims_redirect(get_path('espace', 'historique'));
		}
		generator_tickets();
		break;
}
?>