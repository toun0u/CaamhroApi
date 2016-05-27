<?php
require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_commande_ligne.php';
require_once DIMS_APP_PATH.'modules/system/class_group.php';

$view = view::getInstance();
$lang = $_SESSION['dims']['currentlang'];

if(in_array($view->get('a'), array('', 'moncompte', 'historique'))){
	$view->setLayout('layouts/espace_layout.tpl.php');
}

switch($view->get('a')){
	default:
	case 'moncompte':
		if( ! (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])){
			dims_redirect(get_path( 'espace', 'connexion', array('from' => 'moncompte')) );
		}
		else{
			$client = client::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);
			if(!empty($client)){
				$view->assign('client', $client);
				$view->render('espace/moncompte.tpl.php');
			}
			else{
				dims_redirect(get_path('espace', 'moncompte'));
			}
		}
		break;

	case 'historique':
		if( ! (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])){
			dims_redirect(get_path( 'espace', 'connexion', array('from' => 'historique')) );
		}
		else{
			$client = client::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);
			if(!empty($client)){
				$commandes = commande::conditions(array('code_client'=> $client->get('code_client')))->order('date_cree DESC')->run();
				$view->assign('commandes', $commandes);
				$view->render('espace/historique.tpl.php');
			}
			else{
				dims_redirect(get_path('espace', 'moncompte'));
			}
		}
		break;

	case 'commande':
		if( ! (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])){
			dims_redirect(get_path( 'espace', 'connexion', array('from' => 'historique')) );
		}
		else{
			$client = client::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);
			if(!empty($client)){
				$id_cde = dims_load_securvalue('id_cde', dims_const::_DIMS_NUM_INPUT, true, true);
				$no_object = false;
				if(!empty($id_cde)){
					$commande = commande::find_by(array('id_cde' => $id_cde, 'code_client' => $client->get('code_client')), null, 1);//on s'assure par la même occas que ça appartient bien au client connecté
					if(!empty($commande) && ! $commande->isNew()){
						//récupération des lignes de commandes
						$lignes = commande_ligne::conditions(array('id_cde' => $commande->get('id_cde')))->run();
						$view->assign('commande', $commande);
						$view->assign('lignes', $lignes);
						$articles_id = array();
						foreach($lignes as $line){
							$articles_id[$line->get('id_article')] = $line->get('id_article');
						}
						$articles = array();
						if(!empty($articles_id)){
							$articles = article::conditions(array('id' => array('op' => 'in', 'value' => $articles_id), 'id_lang' => $lang))->run();
							//obligé de réindexé le tableau depuis que thomas a fait en sorte de concaténer les ids des objets dans la méthode getId() de DDO en les séparant par '-'
							$temp = array();
							foreach($articles as $article){
								$temp[$article->get('id')] = $article;
							}
							$articles = $temp;
						}
						$view->assign('articles', $articles);
						$view->render('espace/commande.tpl.php');
					}
					else $no_object = true;
				}
				else $no_object = true;

				if($no_object){
					$view->flash('Cette commande est inconnue', 'error');
					dims_redirect(get_path('espace', 'historique'));
				}
			}
			else{
				dims_redirect(get_path('espace', 'moncompte'));
			}
		}
		break;

	case 'generator':
		$id_cde = dims_load_securvalue('id_cde', dims_const::_DIMS_NUM_INPUT, true, true);
		$success = generator_tickets($id_cde);
		if(!$success){
			$view->flash('Une erreur s\'est produite', 'error');
			dims_redirect(get_path('espace', 'historique'));
		}
		break;

	case 'savecompte':
		$email = dims_load_securvalue('email', dims_const::_DIMS_CHAR_INPUT, true, true);
		$confirmemail = dims_load_securvalue('confirmemail', dims_const::_DIMS_CHAR_INPUT, true, true);
		$password = dims_load_securvalue('password', dims_const::_DIMS_CHAR_INPUT, true, true);
		$passwordconfirm = dims_load_securvalue('passwordconfirm', dims_const::_DIMS_CHAR_INPUT, true, true);

		if( ! empty($email) && !empty($confirmemail) && $email == $confirmemail){
			if( ! ( ( !empty($password) || !empty($passwordconfirm) ) && $password != $passwordconfirm ) ){
				$id_client = dims_load_securvalue('id_client', dims_const::_DIMS_NUM_INPUT, true, true);
				$no_object = false;
				if(!empty($id_client)){
					$client = client::find_by(array('id_client' => $id_client), null, 1);
					if(!empty($client) && ! $client->isNew()){
						if($email != $client->get('email')){//= changement de login
							$ex = user::find_by(array('login' => $email), null, 1);
							if( ! empty($ex)){ //c'est que le login est déjà existant
								$view->flash('Vous ne pouvez pas utiliser cette adresse email, est est réservée', 'error');
								dims_redirect(get_path('espace', 'moncompte'));
							}
						}
						$salt = null;
						if(!empty($password)){
							$res = dims::getInstance()->getPasswordHash($password, $hash, $salt);
							$client->set('password', $hash);
							$_SESSION['dims']['password'] = $hash;
						}
						unset($_POST['password']);
						unset($_POST['passwordconfirm']);
						unset($_POST['confirmemail']);
						unset($_POST['submit']);
						$client->setvalues($_POST, '');
						$client->set('login', $email);//le login sert d'email
						$_SESSION['dims']['login'] = $email;
						$client->setUserinfos(array('lastname' => $client->get('nom'), 'firstname' => $client->get('prenom'), 'salt' => $salt));
						$client->save();

						$view->flash('Vos informations ont été enregistrées avec succès');
						dims_redirect(get_path('espace', 'moncompte'));
					}
					else $no_object = true;
				}
				else $no_object = true;

				if($no_object){
					$view->flash('Une erreur s\'est produite, veuillez réessayer', 'error');
					dims_redirect(get_path('espace', 'moncompte'));
				}
			}
			else{
				$view->flash('Les mots de passe ne correspondent pas', 'error');
				dims_redirect(get_path('espace', 'moncompte'));
			}
		}
		else{
			$view->flash('Les adresses email ne correspondent pas', 'error');
			dims_redirect(get_path('espace', 'moncompte'));
		}

		break;
	case 'connexion':
	$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, true);
	$from2 = dims_load_securvalue('from2', dims_const::_DIMS_CHAR_INPUT, true, true);
		if( ! (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])){
			if(!empty($from2) && $from2 == 'connexion'){ //C'est que le mec ne s'est pas bien connecté (erreur password)
				$view->flash('Mauvais login ou mot de passe', 'error');
			}
			$site = wce_site::getInstance(dims::getInstance()->getDb());
			$_SESSION['dims']['before_connexion_url'] = get_path('espace', 'connexion', array('articleid' => $site->getArticleIDByObject('catalogue', 'Espace client', $extra), 'from'=> $from, 'from2' => 'connexion'));

			$view->render('espace/connexion.tpl.php');
		}
		else{
			$user = user::find_by(array('id' => $_SESSION['dims']['userid']	), null, 1);
			$view->flash('Bienvenue <strong>'.$user->get('firstname').' '.$user->get('lastname').'</strong>');
			dims_redirect(get_path('espace', $from));
		}
		break;
}


if(in_array($view->get('a'), array('', 'moncompte', 'historique'))){
	$sub_content = $view->compile();//compilation du subcontent
	$view->setLayout('layouts/default_layout.tpl.php'); //réinitialisation du layout principal
	$view->assign('sub_content', $sub_content);
	$view->render('layouts/sub_layout.tpl.php');
}

?>