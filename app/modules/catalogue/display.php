<?php
dims_init_module('catalogue');

// init de la var catalogue
if (!isset($catalogue)) $catalogue = array();

// chargement des parametres du catalogue
require_once DIMS_APP_PATH.'/modules/catalogue/include/params_catalogue.php';

//Class
require_once DIMS_APP_PATH.'modules/catalogue/include/class_const.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_context.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_famille.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_filter.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_catalogue.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_promotion.php';

include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/application_helpers.php";

//$db->query("SET NAMES 'UTF8'");//temporaire ? qui dure ?

$dims = dims::getInstance();

// Activation d'un filtre global
if (isset($_GET['global_filter'])) {
	$global_filter = dims_load_securvalue('global_filter', dims_const::_DIMS_NUM_INPUT, true, false, true);

	if ($global_filter > 0) {
		// On vérifie que c'est bien un filtre global
		$filter = new cata_champ();
		$filter->open($global_filter);

		if (!$filter->isNew() && $filter->isGlobalFilter()) {
			// On le colle en session
			$_SESSION['catalogue']['global_filter'] = array(
				'filter_id' 	=> $filter->get('id'),
				'filter_label' 	=> $filter->getGlobalFilterLabel($_SESSION['dims']['currentlang']),
				'filter_value' 	=> $filter->get('global_filter_value')
				);
		}
	}
	else {
		unset($_SESSION['catalogue']['global_filter']);
	}

	dims_redirect(cata_dropParamFromURI($_SERVER['REQUEST_URI'], 'global_filter'));
}

// Si on a un filtre global actif, on affiche un message à l'utilisateur
if (isset($_SESSION['catalogue']['global_filter'])) {
	$returnURI = cata_addParamToURI($_SERVER['REQUEST_URI'], 'global_filter', 0);
	$smarty->assign('global_filter_label', $_SESSION['catalogue']['global_filter']['filter_label']);
	$smarty->assign('returnURI', $returnURI);
}

// Affichage du message d'information certiphyto si pas d'enregistrement phyto
$smarty->assign('show_info_certiphyto', false);
if (isset($_SESSION['catalogue']['enr_certiphyto']) && $_SESSION['catalogue']['enr_certiphyto'] == 0) {
	$smarty->assign('show_info_certiphyto', true);
}



// TODO gérer plrs instances du catalogue
$mods = $dims->getModuleByType('catalogue');
$catalogue_moduleid = $mods[0]['instanceid'];
$_SESSION['catalogue']['moduleid'] = $catalogue_moduleid;

$oCatalogue = new catalogue();
$oCatalogue->open($catalogue_moduleid);
$oCatalogue->loadParams();


// TVA par défaut
define ('_DEFAULT_TVA', $oCatalogue->getParams('default_tva') / 100);

// remise sur les commandes web
$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

// chargement des paramètres utilisateur
if ($_SESSION['dims']['connected'] && empty($_SESSION['catalogue']['code_client'])) {
	$envReturn = cata_context::loadUserEnvironment($oCatalogue);

	if ($envReturn['response'] > 0) {
		dims_redirect('/index.php?dims_logout=1&msg='.$envReturn['response']);
	}
}

if (!$_SESSION['dims']['connected']) {
	// Si pas d'id_company attaché, on va voir si un n'est pas forcé
	// pour n'afficher que le stock de cette société
	if ( !isset($_SESSION['catalogue']['id_company']) && $oCatalogue->getParams('show_stock_from_company') > 0 ) {
		$_SESSION['catalogue']['id_company'] = $oCatalogue->getParams('show_stock_from_company');
	}

	// Recherche d'un marché forcé
	if ( !isset($_SESSION['catalogue']['market']) && $oCatalogue->getParams('default_market') != '' ) {
		$market = cata_market::getByCode('6_DIVPRO');
		if ($market !== null) {
			$_SESSION['catalogue']['market'] = $market->fields;
		}
	}
}



// Connecté ou pas -> smarty ?
if ($_SESSION['dims']['connected']) {
	$smarty->assign('swich_user_logged_in', true);
}
else {
	$smarty->assign('swich_user_logged_out', true);
}

// Récupération du panier
if ($oCatalogue->getParams('cart_management') == 'cookie') {
	cookie2session();
}
elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
	bdd2session();
}

// fonctionnement du site en B2C ?
$smarty->assign('cata_mode_B2C', $oCatalogue->getParams('cata_mode_B2C'));

// catalogue visible si non connecté
$smarty->assign('cata_visible_not_connected', $oCatalogue->getParams('cata_visible_not_connected'));

// affichage du popup quand on ajoute au panier ?
$smarty->assign('cata_show_ajt_panier_popup', $oCatalogue->getParams('cata_show_ajt_panier_popup'));

// affichage des filtres et tri par marque si activé
$smarty->assign('cata_active_marques', $oCatalogue->getParams('cata_active_marques'));

// retrait en magasin
$smarty->assign('retrait_magasin', $oCatalogue->getParams('retrait_magasin'));

if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
	$smarty->assign('switch_user_logged_in','');
	$smarty->assign('user', $dims_user->fields);
}else{
	$smarty->assign('switch_user_logged_out','');
}

// navigation en mode finder ou arborescente ?
// si arbo, on déplie pas les menus de famille
if ($oCatalogue->getParams('cata_nav_style') == 'finder') {
	$smarty->assign('onglet_multi_fam', true);
}
elseif ($oCatalogue->getParams('cata_nav_style') == 'arbo') {
	$smarty->assign('onglet_multi_fam', false);
	$a_pagination_per_page = array(12, 24, 36, 48);
}

// outils de gestion frontoffice
$smarty->assign('cata_account_saisie_rapide',		$oCatalogue->getParams('saisie_rapide'));
$smarty->assign('cata_account_panier',				$oCatalogue->getParams('active_cart'));
$smarty->assign('cata_account_paniers_types',		$oCatalogue->getParams('panier_type'));
$smarty->assign('cata_account_school_lists',		$oCatalogue->getParams('school_lists'));
$smarty->assign('cata_account_commandes_cours',		$oCatalogue->getParams('wait_commandes'));
$smarty->assign('cata_account_historique',			$oCatalogue->getParams('history_cmd'));
$smarty->assign('cata_account_bonslivraison',		$oCatalogue->getParams('bon_livraison'));
$smarty->assign('cata_account_reliquats',			$oCatalogue->getParams('remainings'));
$smarty->assign('cata_account_factures',			$oCatalogue->getParams('invoices'));
$smarty->assign('cata_account_cmd_exceptionnelles',	$oCatalogue->getParams('exceptional_orders'));
$smarty->assign('cata_account_infos_persos',		$oCatalogue->getParams('personal_informations'));
$smarty->assign('cata_account_extraits_compte',		$oCatalogue->getParams('account_statements'));
$smarty->assign('cata_validation',					$oCatalogue->getParams('hierarchy_validation'));


// options par defaut
if (!isset($_SESSION['catalogue']['aff_prix'])) $_SESSION['catalogue']['aff_prix']         		= true;
$_SESSION['catalogue']['afficher_prix']         = 1;
$_SESSION['catalogue']['cata_restreint']        = 0;
$_SESSION['catalogue']['budget_non_bloquant']   = 0;
$_SESSION['catalogue']['budget_reconduction']   = 0;
$_SESSION['catalogue']['limite_budget']         = 0;
$_SESSION['catalogue']['change_livraison']      = 1;
$_SESSION['catalogue']['hors_catalogue']        = 0;
$_SESSION['catalogue']['utiliser_selection']    = 0;
$_SESSION['catalogue']['imprimer_selection']    = 0;
$_SESSION['catalogue']['statistiques']          = 0;
$_SESSION['catalogue']['export_catalogue']      = 0;
$_SESSION['catalogue']['ttc']                   = 1;
$_SESSION['catalogue']['retours']               = 0;
$_SESSION['catalogue']['ref_cde_oblig']         = 0;
$_SESSION['catalogue']['relance_auto']          = 0;
$_SESSION['catalogue']['root_group']			= 6;

$ts = dims_createtimestamp();

// on affiche l'onglet promos que si y'a des promos
$rs = $db->query('SELECT COUNT(*) AS nbPromos FROM dims_mod_cata_promotions WHERE date_debut <= '.$ts.' AND date_fin >= '.$ts);
$row = $db->fetchrow($rs);
$smarty->assign('is_promos', ($row['nbPromos']) ? 1 : 0);

if (!isset($_SESSION['catalogue']['op'])) $_SESSION['catalogue']['op'] = '';

// on test si affichage de prix ou non
$afficheprix = dims_load_securvalue('aff_prix', dims_const::_DIMS_NUM_INPUT, true, true);

if ($afficheprix!=0) {
	if ($afficheprix==1) $_SESSION['catalogue']['aff_prix'] = true;
	else $_SESSION['catalogue']['aff_prix'] = false;
}

$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true, false);//, $_SESSION['catalogue']['op']);

/** Pour fil d'ariane fiche article - voir fin de fichier **/
$saveOpForArti = false;
$ariane_op = $op;
/****/

$familys = cata_getfamilys();
$smarty_famille = array();

// page d'accueil
if ($op == 'home') {
	unset($_SESSION['catalogue']['op']);
	unset($_SESSION['catalogue']['param']);
	unset($_SESSION['catalogue']['rubriques']);

	// insertion du javascript jQuery
	// $smarty->assign('tpl_name', 'accueil');

	$accueil = array('promos' => array(), 'ecoprod' => array());

	/** Assignation smarty des infos accueil **/
	$smarty->assign('accueil', $accueil);
}

$rubriques = dims_load_securvalue('param', dims_const::_DIMS_CHAR_INPUT, true, true, true,$_SESSION['catalogue']['param']);

if (!empty($rubriques) && is_numeric($rubriques) && isset($familys['list'][$rubriques])) {
	$_SESSION['catalogue']['rubriques'] = $rubriques;
}
// si on est pas dans les familles, on désactive l'onglet de couleur
if ( !empty($_SESSION['catalogue']['rubriques']) && !in_array($op, array('catalogue', 'recherche', 'promotions', 'ecoprod', 'consommables', 'nouveautes', 'goodprices')) ) {
	unset($_SESSION['catalogue']['rubriques']);
}
if (isset($_SESSION['catalogue']['rubriques'])) {
	$smarty->assign('rubriques', $_SESSION['catalogue']['rubriques']);
}

if (!empty($_SESSION['catalogue']['rubriques']) && isset($familys['list'][$_SESSION['catalogue']['rubriques']]['nav'])) {
	$array_nav = explode('-', $familys['list'][$_SESSION['catalogue']['rubriques']]['nav']);
}
else {
	$array_nav = array();
}

if (isset($_SESSION['catalogue']['rubriques']) && isset($familys['list'][$_SESSION['catalogue']['rubriques']]['label'])) {
	$smarty->assign('current_selection', $familys['list'][$_SESSION['catalogue']['rubriques']]['label']);
	if (!empty($familys['list'][$_SESSION['catalogue']['rubriques']]['description'])) {
		$smarty->assign('current_description', $familys['list'][$_SESSION['catalogue']['rubriques']]['description']);
	}
}
else {
	$smarty->assign('current_selection', '');
}
// construction de la racine du site
//$res=$db->query("select color,id_famille from dims_mod_cata_famille where depth IN(2,3)");
//while($f=$db->fetchrow($res)) {
//	$familys['list'][$f['id_famille']]['color']=$f['color'];
//}

// ouverture de la famille
if (isset($_SESSION['catalogue']['rubriques'])) {
	$curFam = new cata_famille();
	$curFam->open($_SESSION['catalogue']['rubriques']);
	if (isset($curFam->fields['consommables']) && $curFam->fields['consommables'] && $op == 'catalogue') {
		$op = 'consommables';
	}
}

// recherche du template
if (!isset($_SESSION['dims']['front_template_name'])) {
	$template_name = gettemplatename();
	$_SESSION['dims']['front_template_name'] = $template_name;
}
else {
	$template_name = $_SESSION['dims']['front_template_name'];
}
$template_path = realpath('..').'/app/templates/frontoffice/'.$template_name;
$template_web_path = '/app/templates/frontoffice/'.$template_name;

if (file_exists("{$template_path}/config.php")) require_once "{$template_path}/config.php";

global $root_path;
smarty_catalogue_template_assign($smarty, $smarty_famille, $familys, $array_nav, 0, '', 0, '', $root_path);

$smarty->assign('familles', $smarty_famille);

/* Initialisation a zéro par défaut */
$smarty->assign('promotions', 0);
$smarty->assign('ecoprod', 0);

/** Envoie du panier a smarty **/
$panier = array('montant' => 0, 'nb_art' => 0, 'nb_total' => 0, 'articles' => array());

if (!empty($_SESSION['catalogue']['panier']['articles'])) {
	foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $info) {
		$art = new article();
		if ($art->findByRef($ref)) {
			$panier['nb_art']++;
			$panier['nb_total'] += $info['qte'];

			$puht = catalogue_getprixarticle($art, $info['qte']);
			$puttc = $puht * (1 + ($a_tva[$art->fields['ctva']] / 100));

			$panier['montant'] += $puht * $info['qte'];
			$panier['articles'][$art->fields['reference']]['ref']        = $art->fields['reference'];
			$panier['articles'][$art->fields['reference']]['qte']        = $info['qte'];
			$panier['articles'][$art->fields['reference']]['label']      = $art->fields['label'];
			$panier['articles'][$art->fields['reference']]['image']      = $art->fields['image'];
			$panier['articles'][$art->fields['reference']]['puht']       = catalogue_formateprix($puht);
			$panier['articles'][$art->fields['reference']]['totalht']    = catalogue_formateprix($puht * $info['qte']);
			$panier['articles'][$art->fields['reference']]['puttc']      = catalogue_formateprix($puttc);
			$panier['articles'][$art->fields['reference']]['totalttc']   = catalogue_formateprix($puttc * $info['qte']);
		}
	}
}

// formattage du montant
$panier['montant'] = catalogue_formateprix($panier['montant']);

$smarty->assign('panier', $panier);

// mots cles de la recherche
if(!isset($_SESSION['catalogue']['recherche']['motscles'])) $_SESSION['catalogue']['recherche']['motscles'] = '';
$motscles = dims_load_securvalue('motscles', dims_const::_DIMS_CHAR_INPUT, true, false,true,$_SESSION['catalogue']['recherche']['motscles']);
if (!empty($motscles)) {
	$smarty->assign('motscles', stripslashes($motscles));
}

/******
 * Récupération de la variable smarty "page"
 * Certain op redéfinissent des sous-element de page (e.g : keyword, title)
 *****/
$page = $smarty->getTemplateVars('page');

if ($op == 'xml_detail_groups') {
	$fid = dims_load_securvalue('fid', dims_const::_DIMS_NUM_INPUT, true, false);
	$str = dims_load_securvalue('str', dims_const::_DIMS_CHAR_INPUT, true, false);
	ob_end_clean();
	echo cata_buildGroupsTree(cata_getGroups(), $fid, $str);
	die();
}

// affichage / masquage des prix
if (isset($_SESSION['catalogue']['aff_prix'])) {
	$smarty->assign('aff_prix', $_SESSION['catalogue']['aff_prix']);
}

// base de calcul (HT / TTC ?)
$smarty->assign('cata_base_ttc', $oCatalogue->getParams('cata_base_ttc'));

// Has promotion ?
$smarty->assign('has_promotion', isTherePromotions());

// Favoris
require_once DIMS_APP_PATH.'/modules/catalogue/include/class_paniertype.php';
if ($_SESSION['dims']['connected']) {
	// Ouverture du panier type 'favoris'
	$pt_favoris = new paniertype();
	if (!$pt_favoris->getByLabel('FAVORIS')) {
		$pt_favoris->fields['libelle'] = 'FAVORIS';
		$pt_favoris->save();
	}
	$smarty->assign('favorites_pt_id', $pt_favoris->get('id'));

	// nombre de paniers types
	$a_paniers_types = paniertype::all();
	$smarty->assign('a_paniers_types', $a_paniers_types);
}

// PROFIL CLIENT
if (!$_SESSION['dims']['connected'] || !empty($_SESSION['catalogue']['code_client'])) {
	if (!empty($_SESSION['catalogue']['vrp']['id_commercial'])) {
		$smarty->assign('vrp', true);
	}

	switch ($op) {
		case 'sw_affprix':
			// affichage / masquage des prix
			$_SESSION['catalogue']['aff_prix'] = ($_SESSION['catalogue']['aff_prix']) ? false : true;
			dims_redirect($_SERVER['HTTP_REFERER']);
			break;
		case 'trouver_revendeur':
			ob_start();

			$revendeur_cp = dims_load_securvalue('revendeur_cp', dims_const::_DIMS_CHAR_INPUT, true, false);

			if ($revendeur_cp != '') {
				// cas special DOM / TOM
				if ($revendeur_cp == '97-98') {
					$rs = $db->query('SELECT nom, adr1, adr2, adr3, cp, ville, tel1, fax, email FROM dims_mod_cata_client WHERE (cp LIKE \'97%\' OR cp LIKE \'98%\' ) AND id_pays IN (1, 809, 372, 822, 458, 373, 462, 377, 496) ORDER BY cp, ville');
				}
				else {
					$revendeur_cp = substr($revendeur_cp, 0, 2);
					$cp_sql = $revendeur_cp;

					// cas special corse
					if ($cp_sql == '2A') $cp_sql = '201';
					if ($cp_sql == '2B') $cp_sql = '202';

					$rs = $db->query('SELECT nom, adr1, adr2, adr3, cp, ville, tel1, fax, email FROM dims_mod_cata_client WHERE cp LIKE \''.$cp_sql.'%\' AND id_pays = 1 ORDER BY cp, ville');
				}

				$smarty->assign('revendeurs_dept', $revendeur_cp);

				if ($db->numrows($rs)) {
					$revendeurs = array();

					while ($row = $db->fetchrow($rs)) {
						foreach ($row as $k => $v) { $row[$k] = trim(preg_replace('/^(.)$/', '', $v)); }

						$adresse = '';
						if ($row['adr1'] != '') $adresse .= $row['adr1'];
						if ($row['adr2'] != '') {
							if ($adresse != '') $adresse .= '<br/>';
							$adresse .= $row['adr2'];
						}
						if ($row['adr3'] != '') {
							if ($adresse != '') $adresse .= '<br/>';
							$adresse .= $row['adr3'];
						}
						if ($adresse != '') $adresse .= '<br/>';
						$adresse .= $row['cp'].' '.$row['ville'];

						$revendeurs[] = array( 'nom' => $row['nom'], 'adresse' => $adresse, 'tel' => $row['tel1'], 'fax' => $row['fax'], 'email' => $row['email'] );
					}

					$smarty->assign('revendeurs', $revendeurs);
				}
			}

			$smarty->assign('depts', $a_depts);

			$smarty->assign('tpl_name', 'trouver_revendeur');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			$page['TITLE'] = 'Trouver un revendeur';
			$page['META_DESCRIPTION'] = 'Trouver un revendeur près de chez vous';
			$page['META_KEYWORDS'] = 'revendeur, proximité';
			$page['CONTENT'] = '';

			ob_end_clean();
			break;
		case 'connexion':
			$dims_errorcode = '';
			$dims_errorcode = dims_load_securvalue('dims_errorcode',dims_const::_DIMS_CHAR_INPUT, true, true);

			$error = 0;
			if(!empty($_SESSION['catalogue']['connexion']['oldquery']))
				$error = 1;
			if(defined('_DIMS_ERROR_LOGINEXPIRE') && $dims_errorcode == _DIMS_ERROR_LOGINEXPIRE)
				$error = 2;

			if (!empty($_SESSION['catalogue']['connexion']['oldquery'])) {
				if (is_array($_SESSION['catalogue']['connexion']['oldquery'])) {
					// on crée un champ hidden pour chaque valeur
					$smarty->assign('hidden_fields', $_SESSION['catalogue']['connexion']['oldquery']);
				}
				else {
					// On renvoie le formulaire sur l'adresse
					$smarty->assign('query_string', '?'.$_SESSION['catalogue']['connexion']['oldquery']);
				}
				unset($_SESSION['catalogue']['connexion']['oldquery']);
			}

			// Enregistrement du panier pour éviter de le perdre en cas d'erreur de login
			if ($oCatalogue->getParams('cart_management') == 'cookie') {
				panier2cookie();
			}
			elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
				panier2bdd();
			}

			$page['TITLE'] = 'Connexion';
			$page['META_DESCRIPTION'] = 'Interface de connexion';
			$page['META_KEYWORDS'] = 'Connexion';
			$page['CONTENT'] = '';

			$smarty->assign('connexion_page', array('error' => $error));
			$smarty->assign('ariane', ariane_factory('Connexion','','','', $catalogue));
			$smarty->assign('tpl_name', 'connexion');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			break;

		case 'creer_compte':
			if ($oCatalogue->getParams('cata_mode_B2C')) {
				$page['TITLE'] = 'Créer votre compte';
				$page['META_DESCRIPTION'] = 'Interface de création de compte';
				$page['META_KEYWORDS'] = 'création compte';
				$page['CONTENT'] = '';

				if (empty($_POST)) {
					$key = dims_load_securvalue('key', dims_const::_DIMS_CHAR_INPUT, true, false);

					if (empty($key)) {
						if (!empty($_SESSION['catalogue']['errors'])) {
							$inscription = array(
								'errors' => $_SESSION['catalogue']['errors'],
								'values' => $_SESSION['catalogue']['inscription']['values']
								);
							$smarty->assign('inscription', $inscription);
							unset($_SESSION['catalogue']['errors']);
						}

						require_once DIMS_APP_PATH.'modules/system/class_country.php';
						$smarty->assign('a_countries', country::getAllCountries());
						$smarty->assign('tpl_name', 'creer_compte');
						$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
					}
				}
				else {
					require_once DIMS_APP_PATH.'modules/catalogue/display_creer_compte.php';
				}
				$smarty->assign('ariane', ariane_factory('Créer votre compte','','','', $catalogue));
			}
			else {
				dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'verif_loginDispo':
			if ($oCatalogue->getParams('cata_mode_B2C')) {
				ob_end_clean();
				ob_start();
				header('Content-type: text/html; charset='._DIMS_ENCODING);
				header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
				$login = dims_load_securvalue('login', dims_const::_DIMS_CHAR_INPUT, true, false);
				if (trim($login) == '') {
					$data = array('response' => 0);
				}
				else {
					$rs = $db->query('SELECT id FROM dims_user WHERE login = \''.$login.'\' LIMIT 0,1');
					if ($db->numrows($rs)) {
						$data = array('response' => 0);
					}
					else {
						$data = array('response' => 1);
					}
				}
				ob_end_clean();
				die(json_encode($data));
			}
			break;
		case 'compte_activation':
			if ($oCatalogue->getParams('cata_mode_B2C')) {
				$page['TITLE'] = 'Activer votre compte';
				$page['META_DESCRIPTION'] = 'Activation de votre compte';
				$page['META_KEYWORDS'] = 'Activation';
				$page['CONTENT'] = '';

				$smarty->assign('ariane', ariane_factory('Activation de votre compte','','','', $catalogue));
				$smarty->assign('tpl_name', 'compte_activation');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			}
			else {
				dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'activer_compte':
			if ($oCatalogue->getParams('cata_mode_B2C')) {
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_demande_activation.php';

				$key = dims_load_securvalue('key', dims_const::_DIMS_CHAR_INPUT, true, false);

				$dmd_act = new demande_activation();
				if ($dmd_act->findByActivationKey($key)) {
					$client = new client();
					if ($client->open($dmd_act->fields['code_client'])) {
						$rs = $db->query('SELECT email, firstname, lastname FROM dims_user WHERE login =\''.$client->fields['adminlogin'].'\' LIMIT 0,1');
						$row = $db->fetchrow($rs);

						if ($row['email'] != '') {
							// Envoi du mail au client avec ses identifiants
							$from[0]['name'] = '';
							$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');

							$to[0]['name'] = $row['firstname'].' '.$row['lastname'];
							$to[0]['address'] = $row['email'];

							$subject = "Activation de votre compte";
							$message = str_replace("<LOGIN>", $client->fields['adminlogin'], str_replace("<PASSWD>", $client->fields['adminpwd'], _MAIL_ACCOUNT_CONTENT));

							require_once './include/functions/mail.php';
							dims_send_mail($from, $to, $subject, $message);
						}

						// on debloque le client
						$client->fields['CBLOQUE'] = '';
						$client->save();
					}
					// suppression de la demande d'activation
					$dmd_act->delete();

					dims_redirect('/index.php?op=compte_confirmation');
				}
				else {
					dims_redirect('/index.php?op=mdp_perdu');
				}
			}
			else {
				dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'compte_confirmation':
			if ($oCatalogue->getParams('cata_mode_B2C')) {
				$page['TITLE'] = 'Confirmation de votre compte';
				$page['META_DESCRIPTION'] = 'Confirmation de votre compte';
				$page['META_KEYWORDS'] = 'Confirmation';
				$page['CONTENT'] = '';

				$smarty->assign('ariane', ariane_factory('Confirmation de votre inscription','','','', $catalogue));
				$smarty->assign('tpl_name', 'compte_confirmation');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			}
			else {
				dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'compte_active':
			if ($oCatalogue->getParams('cata_mode_B2C')) {
				$page['TITLE'] = 'Compte activé';
				$page['META_DESCRIPTION'] = 'Compte activé';
				$page['META_KEYWORDS'] = 'Activé';
				$page['CONTENT'] = '';

				$smarty->assign('ariane', ariane_factory('Compte activé','','','', $catalogue));
				$smarty->assign('tpl_name', 'compte_active');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			}
			else {
				dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'compte_active_connecte':
			if ($oCatalogue->getParams('cata_mode_B2C')) {
				$page['TITLE'] = 'Compte activé';
				$page['META_DESCRIPTION'] = 'Compte activé';
				$page['META_KEYWORDS'] = 'Activé';
				$page['CONTENT'] = '';

				$smarty->assign('ariane', ariane_factory('Compte activé','','','', $catalogue));
				$smarty->assign('tpl_name', 'compte_active_connecte');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			}
			else {
				dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'mdp_perdu':
			$page['TITLE'] = 'Mot de passe perdu';
			$page['META_DESCRIPTION'] = 'Récupérer votre mot de passe';
			$page['META_KEYWORDS'] = 'mot de passe, récupération';
			$page['CONTENT'] = '';

			$smarty->assign('ariane', ariane_factory('Mot de passe perdu','','','', $catalogue));
			require_once DIMS_APP_PATH.'modules/catalogue/display_mdp_perdu.php';
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			break;
		case 'mdp_envoye':
			$page['TITLE'] = 'Mot de passe envoyé';
			$page['META_DESCRIPTION'] = 'Récupérer votre mot de passe';
			$page['META_KEYWORDS'] = 'mot de passe, récupération';
			$page['CONTENT'] = '';

			$smarty->assign('ariane', ariane_factory('Mot de passe envoyé','','','', $catalogue));
			$smarty->assign('tpl_name', 'mdp_envoye');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			break;
		case 'mdp_confirmation':
			$page['TITLE'] = 'Mot de passe perdu - confirmation';
			$page['META_DESCRIPTION'] = 'Récupérer votre mot de passe';
			$page['META_KEYWORDS'] = 'mot de passe, récupération';
			$page['CONTENT'] = '';

			$smarty->assign('tpl_name', 'mdp_confirmation');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			break;
		case 'panier':
			$saveOpForArti = true;
			$page['TITLE'] = 'Panier';
			$page['META_DESCRIPTION'] = 'Visualisation votre panier';
			$page['META_KEYWORDS'] = 'Panier, articles, commande';
			$page['CONTENT'] = '';

			$smarty->assign('ariane', ariane_factory('Mon panier','','','', $catalogue, 0, true));
			$smarty->assign('tpl_name', 'panier');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			if (
				defined('TVA_SUR_TAXE_PHYTO')
				&& TVA_SUR_TAXE_PHYTO
				&& defined('_CATA_VARIANTE')
				&& file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_panier.php')
			) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_panier.php';
			}
			else {
				require_once DIMS_APP_PATH.'modules/catalogue/display_panier.php';
			}
			break;
		case 'viderpanier':
			unset($_SESSION['catalogue']['panier']);
			if ($oCatalogue->getParams('cart_management') == 'cookie') {
				panier2cookie();
			}
			elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
				panier2bdd();
			}
			dims_redirect("/index.php?op=panier");
			break;
		case 'modifier_panier':
			if (isset($_POST['id'])) {

				// unité de vente
				$uventeField = 'uvente';

				if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php')) {
					require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
					require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';

					$oClient = new client();
					$oClient->openByCode($_SESSION['catalogue']['code_client']);
					$oClientCplmt = new cata_client_cplmt();
					$oClientCplmt->open($oClient->fields['id_client']);

					if ($OclientCplmt->fields['soldeur'] == 'Oui') {
						$uventeField = 'uventesolde';
					}
				}

				$old_panier = $_SESSION['catalogue']['panier'];

				$_SESSION['catalogue']['panier']['articles'] = array();
				$_SESSION['catalogue']['panier']['montant'] = 0;

				foreach ($_POST['id'] as $id_article) {
					$qte = dims_load_securvalue('qte'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true, true);
					$ref = dims_load_securvalue('ref'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true, true);
					$del = dims_load_securvalue('del'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true, true);

					// Prix forcés
					if (!empty($_SESSION['dims']['previous_user'])) {
						$forced_price = dims_load_securvalue('price'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true, true);
						if ($forced_price == '' && isset($old_panier['articles'][$ref]['forced_price'])) {
							$forced_price = $old_panier['articles'][$ref]['forced_price'];
						}
						cart_modRef($ref, $qte, $del, $oCatalogue->getParams('cata_base_ttc'), $uventeField, $forced_price);
					}
					else {
						cart_modRef($ref, $qte, $del, $oCatalogue->getParams('cata_base_ttc'), $uventeField);
					}
				}

				// Frais de port renseignés par le commercial
				$pricefp = dims_load_securvalue('pricefp', dims_const::_DIMS_CHAR_INPUT, false, true, true);
				if (!empty($_SESSION['dims']['previous_user']) && $pricefp > 0) {
					$_SESSION['catalogue']['panier']['forced_frais_port'] = str_replace(',', '.', $pricefp);
				}
			}

			if ($oCatalogue->getParams('cart_management') == 'cookie') {
				panier2cookie();
			} elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
				panier2bdd();
			}

			dims_redirect('/index.php?op=panier');
			break;
		case 'enlever_article':
			$pref = dims_load_securvalue('pref', dims_const::_DIMS_CHAR_INPUT, true, false);
			$redir = dims_load_securvalue('redir', dims_const::_DIMS_CHAR_INPUT, true, false);

			if (!empty($pref)) {
				// Recherche de la ref dans le panier
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_article.php';
				$article = new article();
				$article->findByRef($pref);
				$PREF = $article->fields['reference'];

				if (isset($_SESSION['catalogue']['panier']['articles'][$PREF])) unset($_SESSION['catalogue']['panier']['articles'][$PREF]);

				// On recalcule le panier
				$_SESSION['catalogue']['panier']['montant'] = 0;
				foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $det) {
					$article = new article();
					$article->open($ref);

					$prix = catalogue_getprixarticle($article, $det['qte']);
					$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

					if ($oCatalogue->getParams('cata_base_ttc')) {
						$_SESSION['catalogue']['panier']['montant'] += $prixaff * $det['qte'];
					}
					else {
						$_SESSION['catalogue']['panier']['montant'] += $prix * $det['qte'];
					}
				}
			}
			if ($oCatalogue->getParams('cart_management') == 'cookie') {
				panier2cookie();
			}
			elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
				panier2bdd();
			}
			switch ($redir) {
				case 'saisierapide':
					dims_redirect('/index.php?op=saisierapide');
					break;
				default:
					dims_redirect('/index.php?op=panier');
					break;
			}
			break;
		case 'catalogue':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$saveOpForArti = true;

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			// L'affichage et les infos à remontere dépendent du mode d'affichage de la famille
			$cata_famille = new cata_famille();
			$cata_famille->open(dims_load_securvalue('param', dims_const::_DIMS_NUM_INPUT, true, false));

			switch ($cata_famille->fields['display_mode']) {
				case cata_famille::DISPLAY_MODE_LIST:
					require_once DIMS_APP_PATH.'modules/catalogue/display_family_list.php';
					$smarty->assign('tpl_name', 'famille_liste');
					break;
				case cata_famille::DISPLAY_MODE_COMPARATOR:
					require_once DIMS_APP_PATH.'modules/catalogue/display_family_comparator.php';
					$smarty->assign('tpl_name', 'famille_comparateur');
					break;
				case cata_famille::DISPLAY_MODE_CMS:
					require_once DIMS_APP_PATH.'modules/catalogue/display_famille_cms.php';
					$smarty->assign('tpl_name', 'famille_cms');
					break;
			}

			$smarty->assign('ariane', ariane_factory('','','','', $catalogue));
			break;
		case 'ecoprod':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$saveOpForArti = true;

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$smarty->assign('tpl_name', 'ecoprod');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_ecoprod.php';
			$smarty->assign('ariane', ariane_factory('Eco-produit','','/index.php?op=ecoprod','', $catalogue));
			break;
		case 'consommables':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$saveOpForArti = true;

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$smarty->assign('tpl_name', 'consommables');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_consommables.php';
			$smarty->assign('ariane', ariane_factory('','','','', $catalogue));
			break;
		case 'recherche':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$saveOpForArti = true;

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$smarty->assign('tpl_name', 'recherche');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_recherche.php';
			$smarty->assign('ariane', ariane_factory('','Recherche sur '.$_GET['motscles'],'','/index.php?op=recherche&motscles='.$motscles, $catalogue,1));
			break;
		case 'search_promo':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'modules/catalogue/search_promotion.php';
			break;
		case 'promotions':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$saveOpForArti = true;

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$smarty->assign('tpl_name', 'promotions');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_promotions.php')) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_promotions.php';
			}
			else {
				require_once DIMS_APP_PATH.'modules/catalogue/display_promotions.php';
			}

			$smarty->assign('ariane', ariane_factory('Promotions : '.$promoLabel,'','/index.php?op=promotions&id_promo='.$id_promo, '', $catalogue, 1));
			break;
		case 'imprimer_promotions':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'modules/catalogue/imprimer_promotions.php';
			break;
		case 'nouveautes':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$saveOpForArti = true;

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$smarty->assign('tpl_name', 'nouveautes');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_nouveautes.php';
			$smarty->assign('ariane', ariane_factory('Nouveautés','','/index.php?op=nouveautes', '', $catalogue, 1));
			break;
		case 'goodprices':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$saveOpForArti = true;

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];
			$smarty->assign('tpl_name', 'goodprices');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			require_once DIMS_APP_PATH.'modules/catalogue/display_goodprices.php';
			$smarty->assign('ariane', ariane_factory('Good Prices','','/index.php?op=goodprices', '', $catalogue, 1));
			break;
		case 'fiche_article':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$smarty->assign('tpl_name', 'fiche_article');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_article.php';

			if(isset($_SESSION['catalogue']['ariane_oldop']) && !empty($_SESSION['catalogue']['ariane_oldop'])) {
				switch($_SESSION['catalogue']['ariane_oldop']) {
					default:
						$smarty->assign('ariane', ariane_factory('Fiche de l\'article '.$article['reference'],'','','', $catalogue));
						break;
					case 'promotions':
						if(!empty($_SESSION['catalogue']['ariane_oldparam'])) {
							$fam = new cata_famille();
							$fam->open($_SESSION['catalogue']['ariane_oldparam']);

							$promo = new cata_promotion();
							$promo->open($_SESSION['catalogue']['ariane_oldidpromo']);

							$smarty->assign('ariane', ariane_factory('Promotions : '.$promo->getLibelle().' : '.$fam->fields['label'],'Fiche de l\'article '.$article['reference'],'/index.php?op=promotions&id_promo='.$promo->get('id').'&rubriques='.$_SESSION['catalogue']['ariane_oldparam'],'','', $catalogue));
						}
						elseif(!empty($_SESSION['catalogue']['ariane_oldidpromo'])) {
							$promo = new cata_promotion();
							$promo->open($_SESSION['catalogue']['ariane_oldidpromo']);

							$smarty->assign('ariane', ariane_factory('Promotions : '.$promo->getLibelle(),'Fiche de l\'article '.$article['reference'],'/index.php?op=promotions&id_promo='.$promo->get('id'),'','', $catalogue));
						}
						else {
							$smarty->assign('ariane', ariane_factory('Promotions','Fiche de l\'article '.$article['reference'],'/index.php?op=promotions','','', $catalogue));
						}
						break;
					case 'ecoprod':
						if(!empty($_SESSION['catalogue']['ariane_oldparam'])) {
							$fam = new cata_famille();
							$fam->open($_SESSION['catalogue']['ariane_oldparam']);

							$smarty->assign('ariane', ariane_factory('Eco produits : '.$fam->fields['label'],'Fiche de l\'article '.$article['reference'],'?op=ecoprod&rubriques='.$_SESSION['catalogue']['ariane_oldparam'],'','', $catalogue));
						}
						else {
							$smarty->assign('ariane', ariane_factory('Eco produits','Fiche de l\'article '.$article['reference'],'?op=ecoprod','','', $catalogue));
						}
						break;
					case 'recherche':
						if(!empty($_SESSION['catalogue']['ariane_oldparam'])) {
							$fam = new cata_famille();
							$fam->open($_SESSION['catalogue']['ariane_oldparam']);

							$smarty->assign('ariane', ariane_factory('Recherche sur '.$_SESSION['catalogue']['ariane_oldmotscles'].' - '.$fam->fields['label'],'Fiche de l\'article '.$article['reference'],'?op=recherche&motscles='.$_SESSION['catalogue']['ariane_oldmotscles'].'&rubriques='.$_SESSION['catalogue']['ariane_oldparam'],'','', $catalogue,1));
						}
						else {
							$smarty->assign('ariane', ariane_factory('Recherche sur '.$_SESSION['catalogue']['ariane_oldmotscles'],'Fiche de l\'article '.$article['reference'],'?op=recherche&motscles='.$_SESSION['catalogue']['ariane_oldmotscles'],'','', $catalogue));
						}
						break;
					case 'catalogue':
						$fam = new cata_famille();
						$fam->open($_SESSION['catalogue']['ariane_oldparam']);

						if(!empty($familys['list'][$_SESSION['catalogue']['ariane_oldparam']]['urlrewrite']))
							$smarty->assign('ariane', ariane_factory($fam->fields['label'],'Fiche de l\'article '.$article['reference'],$familys['list'][$_SESSION['catalogue']['ariane_oldparam']]['urlrewrite'],'','', $catalogue));
						else
							$smarty->assign('ariane', ariane_factory($fam->fields['label'],'Fiche de l\'article '.$article['reference'],'?op=catalogue&param='.$_SESSION['catalogue']['ariane_oldparam'],'','', $catalogue));
						break;
					case 'alphabetique':
						$smarty->assign('ariane', ariane_factory('Produits A-Z','Fiche de l\'article '.$article['reference'],'?op=alphabetique&param='.$_SESSION['catalogue']['ariane_oldparam'],'', $catalogue));
						break;
					case 'panier':
						$smarty->assign('ariane', ariane_factory('Mon panier','Fiche de l\'article '.$article['reference'],'?op=panier','', $catalogue));
						break;
					case 'panierstype':
						$smarty->assign('ariane', ariane_factory('Mes paniers types','Fiche de l\'article '.$article['reference'],'?op=panierstype&id_panier='.$_SESSION['catalogue']['ariane_oldparam'],'', $catalogue));
						break;
				}
			}
			else
				$smarty->assign('ariane', ariane_factory('Fiche de l\'article '.$article['reference'],'','','', $catalogue));
			break;
		case 'ajouter_panierart':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'/modules/catalogue/xml_ajouter_panierart.php';
			break;
		case 'get_degressif':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'/modules/catalogue/xml_get_degressif.php';
			break;
		case 'get_frais_port_infos':
			// suppression du op en session pour les appels ajax
			$_SESSION['catalogue']['op'] = '';
			include_once DIMS_APP_PATH.'/modules/catalogue/xml_get_frais_port_infos.php';
			break;
		case 'marques':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$smarty->assign('ariane', ariane_factory('Toutes nos marques','','?op=marques', '', $catalogue));
			$smarty->assign('tpl_name', 'marques');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_marques.php';
			break;
		case 'alphabetique':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			// pour sauvegarder la page des achats sur laquelle on était
			// sert pour le bouton "Continuer mes achats" dans le panier
			$_SESSION['catalogue']['achats_url'] = $_SERVER['REQUEST_URI'];

			$saveOpForArti = true;
			$smarty->assign('ariane', ariane_factory('Produits A-Z','','?op=alphabetique', '', $catalogue));
			$smarty->assign('tpl_name', 'alphabetique');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_alphabetique.php';
			break;
		case 'compte':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','','?op=compte', '', $catalogue));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_compte.php';
			break;
		case 'hors_catalogue':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Commandes exceptionnelle','?op=compte', '?op=hors_catalogue', $catalogue));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_hors_catalogue.php')) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_hors_catalogue.php';
			}
			else {
				require_once DIMS_APP_PATH.'modules/catalogue/display_hors_catalogue.php';
			}
			break;
		case 'enregistrer_horscata':
		case 'mettre_en_attente_hc':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$cmd_libelle = dims_load_securvalue('cmd_libelle', dims_const::_DIMS_CHAR_INPUT, false, true);

			if (isset($_SESSION['catalogue']['errors'])) unset($_SESSION['catalogue']['errors']);

			if (isset($_POST['refqte']) && count($_POST['refqte'])) {
				$ensrefqte = array();

				if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
					$i = 0;
					foreach ($_POST['refqte'] as $key => $value) {
						$ref = trim($value[0]);
						$des = trim($value[1]);
						$pu = str_replace(',','.',trim($value[2]));
						$qte = trim($value[3]);

						if ($qte != '') {
							if ($des != '') {
								if (is_numeric($qte)) {
									if ($qte > 0) {
										$ensrefqte[$i]['reference'] = $ref;
										$ensrefqte[$i]['designation'] = $des;
										$ensrefqte[$i]['pu'] = $pu;
										$ensrefqte[$i]['qte'] = $qte;
									}
									else {
										$_SESSION['catalogue']['errors']["'$ref'"] = "La quantité doit être positive.";
									}
								}
								else {
									$_SESSION['catalogue']['errors']["'$ref'"] = "La quantité doit être un nombre.";
								}
							}
							else {
								$_SESSION['catalogue']['errors']["'$ref'"] = "Vous devez spécifier une désignation.";
							}
						}
						$i++;
					}
				}
				else {
					$i = 0;
					foreach ($refqte as $key => $value) {
						$ref = trim($value[0]);
						$des = trim($value[1]);
						$qte = trim($value[3]);

						if ($qte != '') {
							if ($des != '') {
								if (is_numeric($qte)) {
									if ($qte > 0) {
										$ensrefqte[$i]['reference'] = $ref;
										$ensrefqte[$i]['designation'] = $des;
										$ensrefqte[$i]['qte'] = $qte;
									}
									else {
										$_SESSION['catalogue']['errors']["'$ref'"] = "La quantité doit être positive.";
									}
								}
								else {
									$_SESSION['catalogue']['errors']["'$ref'"] = "La quantité doit être un nombre.";
								}
							}
							else {
								$_SESSION['catalogue']['errors']["'$ref'"] = "Vous devez spécifier une désignation.";
							}
						}
						$i++;
					}
				}

				if (count($ensrefqte) && !isset($_SESSION['catalogue']['errors'])) {
					require_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
					$commande = new commande();
					$commande->fields['hors_cata'] = 1;
					if ($oCatalogue->getParams('commandes_exceptionnelles')) {
						$commande->fields['exceptionnelle'] = 1;
					}
					$commande->fields['ref_client'] = $_SESSION['catalogue']['code_client'];
					$commande->fields['libelle'] = $cmd_libelle;
					$commande->fields['commentaire'] = htmlentities($commentaire, ENT_QUOTES);
					$commande->fields['user_name'] = $_SESSION['catalogue']['client_firstname'] .' '. $_SESSION['catalogue']['client_lastname'];
					$commande->fields['id_user'] = $_SESSION['dims']['userid'];

					$user = new user();
					$user->open($_SESSION['dims']['userid']);
					$groups = $user->getgroups(true);
					$commande->fields['id_group'] = key($groups);

					$commande->articles = $ensrefqte;
					$commande->save();

					// On met a jour le nb de commandes a valider
					$_SESSION['catalogue']['nb_cmd_val']++;
				}

				if (isset($_SESSION['catalogue']['errors']) && count($_SESSION['catalogue']['errors'])) {
					$_SESSION['catalogue']['horscata']['cmd_libelle'] = $cmd_libelle;
					$_SESSION['catalogue']['horscata']['commentaire'] = $commentaire;
					$_SESSION['catalogue']['horscata']['refqte'] = $refqte;
					dims_redirect($dims->getScriptEnv()."?op=hors_catalogue&ok=0");
				}
				else {
					switch ($op) {
						case 'enregistrer_horscata':
							if (isset($commande->fields['id']) && $commande->fields['id'] != '') dims_redirect($dims->getScriptEnv()."?op=valider_commande&id_cmd={$commande->fields['id']}");
							else dims_redirect($dims->getScriptEnv()."?op=hors_catalogue");
							break;
						case 'mettre_en_attente_hc':
							dims_redirect($dims->getScriptEnv."?op=commandes&id_cmd={$commande->fields['id']}");
							break;
					}
				}
			} else {
				dims_redirect($dims->getScriptEnv()."?op=hors_catalogue&ok=0");
			}
			break;
		case 'infospersos':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if ($oCatalogue->getParams('personal_informations')) {
				$smarty->assign('ariane', ariane_factory('Mon compte','Mes informations personnelles','?op=compte', '?op=infospersos', $catalogue));
				$smarty->assign('tpl_name', 'catalogue_content');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'modules/catalogue/display_infospersos.php';
			}
			break;
		case 'save_infospersos':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$password = dims_load_securvalue('password', dims_const::_DIMS_CHAR_INPUT, false, true, true);
			$passconf = dims_load_securvalue('passconf', dims_const::_DIMS_CHAR_INPUT, false, true, true);

			// compte utilisateur
			$user = new user();
			$user->open($_SESSION['dims']['userid']);
			$current_email = $user->fields['email'];

			$user->setvalues($_POST,'user_');

			if (!empty($password) || !empty($passconf)) {
				if ($password == $passconf) {
					if (strlen($password) >= 4) {
						$dims->getPasswordHash($password, $hash, $saltuser);
						$user->fields['salt'] = $saltuser;
						$user->fields['password'] = $hash;

						// Mise à jour du mot de passe en session pour pas se faire déco via updateConnect
						$_SESSION['dims']['password'] = $hash;
					}
					else {
						$error = 2;
					}
				}
				else {
					$error = 1;
				}
			}
			require_once DIMS_APP_PATH.'include/functions/mail.php';
			if(!dims_verifyemail($user->fields['email'])) {
				$user->fields['email'] = $current_email;
				$error = 3;
			}
			$user->save();

			// adresse de facturation / livraison
			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
			$client = new client();
			$client->openByCode($_SESSION['catalogue']['code_client']);

			// Les infos sont éditables si la fonction est activée en backoffice, ou que le client est un particulier
			if ($oCatalogue->getParams('cata_infos_persos_editable') || $client->isParticular()) {
				$client->setvalues($_POST, 'client_');
				$client->set('librcha2', dims_constant::getVal('PERSONNALISE'));
				$client->save();
			}

			if (isset($error)) dims_redirect("/index.php?op=infospersos&error=$error");
			else dims_redirect("/index.php?op=compte");
			break;
		case 'depot_edit':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			// Les infos sont éditables si la fonction est activée en backoffice, ou que le client est un particulier
			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
			$client = new client();
			$client->openByCode($_SESSION['catalogue']['code_client']);

			if ($oCatalogue->getParams('cata_infos_persos_editable') || $client->isParticular()) {
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_cata_depot.php';
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);

				$a_countries = country::getAllCountries();

				$depot = new cata_depot();
				if ($id > 0) {
					$depot->open($id);
					// on vérifie que c'est bien une adresse du client
					if ($depot->fields['client'] != $_SESSION['catalogue']['code_client']) {
						dims_404();
					}
				}
				else {
					$depot->init_description();
					$depot->set('id_country',_DIMS_DEFAULT_COUNTRY);
				}

				$smarty->assign('a_countries', $a_countries);
				$smarty->assign('return', dims_load_securvalue('return',dims_const::_DIMS_CHAR_INPUT,true,true,true));
				$smarty->assign('depot', $depot->fields);
				$smarty->assign('buttons', array(
					'btn_back'	=> catalogue_makegfxbutton('<i class="txtwhite icon2-reply orange title-icon"></i>','Retour',"document.location.href='/index.php?op=infospersos'","*"),
					'btn_save'	=> catalogue_makegfxbutton('Enregistrer','<i class="txtwhite icon2-checkmark orange title-icon"></i>',"document.form.submit()","*")
					));
				$smarty->assign('tpl_name', 'depot_edit');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

				$page['TITLE'] = 'Edition d\'adresse de livraison';
				$page['META_DESCRIPTION'] = 'Editer une adresse de livraison';
				$page['META_KEYWORDS'] = 'informations, infos, personnelles, modifier, livraison, adresse';
				$page['CONTENT'] = '';
			}
			break;
		case 'depot_save':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			// Les infos sont éditables si la fonction est activée en backoffice, ou que le client est un particulier
			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
			$client = new client();
			$client->openByCode($_SESSION['catalogue']['code_client']);

			if ($oCatalogue->getParams('cata_infos_persos_editable') || $client->isParticular()) {
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_cata_depot.php';
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, false, true);

				$depot = new cata_depot();
				if ($id > 0) {
					$depot->open($id);
					$save_action = 'update';
					// on vérifie que c'est bien une adresse du client
					if ($depot->fields['client'] != $_SESSION['catalogue']['code_client']) {
						dims_404();
					}
				}
				else {
					$depot->init_description();
					$depot->setClient($_SESSION['catalogue']['code_client']);
					$depot->setNumDepot();
					$save_action = 'insert';
				}

				$nomlivr 		= dims_load_securvalue('depot_nomlivr', dims_const::_DIMS_CHAR_INPUT, false, true);
				$adr1 			= dims_load_securvalue('depot_adr1', dims_const::_DIMS_CHAR_INPUT, false, true);
				$adr2 			= dims_load_securvalue('depot_adr2', dims_const::_DIMS_CHAR_INPUT, false, true);
				$cp 			= dims_load_securvalue('depot_cp', dims_const::_DIMS_CHAR_INPUT, false, true);
				$ville 			= dims_load_securvalue('depot_ville', dims_const::_DIMS_CHAR_INPUT, false, true);
				$id_country 	= dims_load_securvalue('depot_country', dims_const::_DIMS_CHAR_INPUT, false, true);

				$depot->setNomLivr($nomlivr);
				$depot->setAddress1($adr1);
				$depot->setAddress2($adr2);
				$depot->setPostalCode($cp);
				$depot->setCountry($id_country);
				$depot->setCity($ville);
				$depot->save();

				// On synchronise l'enregistrement de l'adresse
				$params = array(
					'action' 		=> $save_action,
					'address_type' 	=> 'liv',
					'client' 		=> $client,
					'depot' 		=> $depot
					);

				sync_address($params);

				$return = base64_decode(dims_load_securvalue('return',dims_const::_DIMS_CHAR_INPUT,true,true,true));
				if(empty($return)){
					dims_redirect($dims->getScriptEnv().'?op=infospersos');
				}else{
					dims_redirect($return);
				}
			}
			break;
		case 'depot_delete':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			// Les infos sont éditables si la fonction est activée en backoffice, ou que le client est un particulier
			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
			$client = new client();
			$client->openByCode($_SESSION['catalogue']['code_client']);

			if ($oCatalogue->getParams('cata_infos_persos_editable') || $client->isParticular()) {
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_cata_depot.php';
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);

				$depot = new cata_depot();
				if ($id > 0) {
					$depot->open($id);
					// on vérifie que c'est bien une adresse du client
					if ($depot->fields['client'] != $_SESSION['catalogue']['code_client']) {
						dims_404();
					}
					$depot->delete();

					// On synchronise la suppression de l'adresse
					$params = array(
						'action' 		=> 'delete',
						'address_type' 	=> 'liv',
						'client' 		=> $client,
						'depot' 		=> $depot
						);

					sync_address($params);
				}
				dims_redirect($dims->getScriptEnv().'?op=infospersos');
			}
			break;
		case 'commandes':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$orders_view = dims_load_securvalue('view', dims_const::_DIMS_CHAR_INPUT, true, false);
			$smarty->assign('ariane', ariane_factory('Mon compte','Mes paniers en attente','?op=compte', '?op=commandes&view='.$orders_view, $catalogue));

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_commandes.php')) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_commandes.php';
			}
			else {
				require_once DIMS_APP_PATH.'modules/catalogue/display_commandes.php';
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			}
			if (isset($_SESSION['catalogue']['msg_confirm']) && $_SESSION['catalogue']['msg_confirm'] ==  dims_constant::getVal('_CATA_MSG_CONFIRM_7')){
				$smarty->assign('msg_confirm', $_SESSION['catalogue']['msg_confirm']);
				$smarty->assign('tpl_name', 'confirmation');
				$_SESSION['catalogue']['msg_confirm'] = "";
			}
			break;
		case 'imprimer_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'/modules/catalogue/imprimer_commande.php';
			break;
		case 'modifier_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			include DIMS_APP_PATH.'modules/catalogue/display_commande_modifier.php';
			break;
		case 'modifier_commande_fin':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if ($_SESSION['dims']['connected']) {
				include DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
				$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, false, true);

				$commande = new commande();
				$commande->open($id_cmd);

				if (isset($_POST['id'])) {
					$montant = 0;
					$articles = array();
					$i = 0;

					foreach ($_POST['id'] as $id_article) {
						if (!$commande->fields['hors_cata']) {
							$qte = dims_load_securvalue('qte'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);
							$ref = dims_load_securvalue('ref'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true);
							$del = dims_load_securvalue('del'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);

							$article = new article();
							$article->open($ref);
							$prix = catalogue_getprixarticle($article, $qte);

							// reference a ajouter au panier
							if (isset($ref) && isset($qte) && isset($prix) && empty($del)) {
								$articles[$ref]['qte'] = $qte;
								$montant += $qte * $prix;
							}
						}
						else {
							$qte = dims_load_securvalue('qte'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);
							$ref = dims_load_securvalue('ref'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true);
							$des = dims_load_securvalue('des'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);
							$prix = dims_load_securvalue('prix'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true);
							$del = dims_load_securvalue('del'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);

							// reference a ajouter au panier
							if (isset($ref) && isset($qte) && isset($prix) && empty($del)) {
								$articles[$i]['id'] = $id;
								$articles[$i]['reference'] = $ref;
								$articles[$i]['designation'] = $des;
								$articles[$i]['qte'] = $qte;
								$articles[$i]['pu'] = $prix;
								$montant += $qte * $prix;
							}
						}
						$i++;
					}
				}

				$commande->articles = $articles;
				$commande->save();

				dims_redirect('/index.php?op=modifier_commande&id_cmd='.$id_cmd);
			}
			else {
				$_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
				dims_redirect($dims->getScriptEnv().'?op=connexion');
			}
			break;
		case 'refuser_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Refus de commande','?op=compte', '?op=refuser_commande'));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'/modules/catalogue/display_refuser_commande.php';
			break;
		case 'refuser_commande_fin':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once './modules/system/class_user.php';
			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';

			$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, false, true);
			$motif = dims_load_securvalue('motif', dims_const::_DIMS_CHAR_INPUT, false, true);

			$commande = new commande();
			$commande->open($id_cmd);

			if ($commande->fields['etat'] == 'en_cours1' || $commande->fields['etat'] == 'en_cours2') {
				$commande->fields['etat'] = 'refusee';
				$commande->fields['refus_motif'] = $motif;
				$commande->fields['refus_user'] = $_SESSION['catalogue']['client_firstname'] .' '. $_SESSION['catalogue']['client_lastname'];

				$commande->save_lite();
				$_SESSION['catalogue']['nb_cmd_val']--;

				// Envoi du mail pour signaler le refus
				$a_to = array();
				foreach ($commande->articles as $art) {
					if (!in_array($art['id_user'], $a_to)) {
						$a_to[] = $art['id_user'];
					}
				}
				foreach ($a_to as $id_user) {
					$user = new user();
					$user->open($id_user);
					if ($user->fields['email'] != '') {
						$to[] = array(
							'name'		=> $user->fields['firstname'].' '.$user->fields['lastname'],
							'address'	=> $user->fields['email']
							);
					}
				}

				if ( sizeof($to) && $oCatalogue->getParams('cata_send_mail') ) {
					$from[0]['name'] = '';
					$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');

					$subject = "Commande '{$commande->fields['libelle']}' refusée";

					$message = "
						Votre commande '{$commande->fields['libelle']}' a été refusée par <b>{$commande->fields['refus_user']}</b>.<br><br>
						<b>Motif du refus :</b> ". stripslashes(nl2br($commande->fields['refus_motif']));

					dims_send_mail($from,$to,$subject,$message);
				}
			}
			dims_redirect($dims->getScriptEnv().'?op=commandes');
			break;
		case 'supprimer_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			include DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
			$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, false);
			$cde = new commande();
			$cde->open($id_cmd);

			// on supprime la commande que si on en est le propriétaire
			// et que la commande est en cours ou refusée
			if ( $cde->fields['id_user'] == $_SESSION['dims']['userid'] && ( $cde->fields['etat'] == commande::_STATUS_PROGRESS || $cde->fields['etat'] == commande::_STATUS_REFUSED ) ) {
				$cde->delete();
			}
			dims_redirect('/index.php?op=commandes');
			break;
		case 'reprendre_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
			$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, false);

			if (!empty($id_cmd)) {
				$cde = new commande();
				$cde->open($id_cmd);

				$modifiable = ( $cde->fields['id_user'] == $_SESSION['dims']['userid'] && ( $cde->fields['etat'] == commande::_STATUS_PROGRESS || $cde->fields['etat'] == commande::_STATUS_REFUSED ) );

				if ($modifiable) {
					$_SESSION['catalogue']['panier']['articles'] = array();
					$_SESSION['catalogue']['panier']['montant'] = 0;

					$lignes = $cde->getlignes();
					foreach ($lignes as $l) {
						$article = new article();
						$article->open($l->fields['ref']);
						$prix = catalogue_getprixarticle($article, $l->fields['qte']);

						if (!isset($_SESSION['catalogue']['panier']['articles'][$l->fields['ref']]['qte'])) {
							$_SESSION['catalogue']['panier']['articles'][$l->fields['ref']]['qte'] = 0;
						}
						$_SESSION['catalogue']['panier']['articles'][$l->fields['ref']]['qte'] += $l->fields['qte'];
						$_SESSION['catalogue']['panier']['montant'] += $l->fields['qte'] * $prix;
					}
					$cde->delete();

					if ($oCatalogue->getParams('cart_management') == 'cookie') {
						panier2cookie();
					} elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
						panier2bdd();
					}
				}
			}
			dims_redirect('/index.php?op=panier');
			break;
		case 'valider_panier':
			if (isset($_POST['id'])) {
				// unité de vente
				$uventeField = 'uvente';

				if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php')) {
					require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
					require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';

					$oClient = new client();
					$oClient->openByCode($_SESSION['catalogue']['code_client']);
					$oClientCplmt = new cata_client_cplmt();
					$oClientCplmt->open($oClient->fields['id_client']);

					if ($OclientCplmt->fields['soldeur'] == 'Oui') {
						$uventeField = 'uventesolde';
					}
				}

				$old_panier = $_SESSION['catalogue']['panier'];

				$_SESSION['catalogue']['panier']['articles'] = array();
				$_SESSION['catalogue']['panier']['montant'] = 0;

				foreach ($_POST['id'] as $id_article) {
					$qte = dims_load_securvalue('qte'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);
					if($qte < 0){
						$qte = 0-$qte;
						$_POST['qte'.$id_article] = $qte;
					}
					$ref = dims_load_securvalue('ref'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true);
					$del = dims_load_securvalue('del'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);

					// Prix forcés
					if (!empty($_SESSION['dims']['previous_user'])) {
						$forced_price = dims_load_securvalue('price'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true, true);
						if ($forced_price == '' && isset($old_panier['articles'][$ref]['forced_price'])) {
							$forced_price = $old_panier['articles'][$ref]['forced_price'];
						}
						cart_modRef($ref, $qte, $del, $oCatalogue->getParams('cata_base_ttc'), $uventeField, $forced_price);
					}
					else {
						cart_modRef($ref, $qte, $del, $oCatalogue->getParams('cata_base_ttc'), $uventeField);
					}
				}
			}

			if ($oCatalogue->getParams('cart_management') == 'cookie') {
				panier2cookie();
			} elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
				panier2bdd();
			}

			// NO BREAK
		case 'valider_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Validation de commande','?op=compte', '?op=valider_commande', $catalogue));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			if (
				defined('TVA_SUR_TAXE_PHYTO')
				&& TVA_SUR_TAXE_PHYTO
				&& defined('_CATA_VARIANTE')
				&& file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_commander.php')
			) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_commander.php';
			}
			else {
				require_once DIMS_APP_PATH.'modules/catalogue/display_commander.php';
			}
			break;
		case 'validation_multiple':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Validation de commande','?op=compte', '?op=valider_commande', $catalogue));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_commander_multiple.php')) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_commander_multiple.php';
			}
			else {
				require_once DIMS_APP_PATH.'modules/catalogue/display_commander_multiple.php';
			}
			break;
		case 'valider_group':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Validation de commande','?op=compte', '?op=valider_commande', $catalogue));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;

			if(!isset($_SESSION['catalogue']['id_group'])) $_SESSION['catalogue']['id_group'] = '';
			$id_group = dims_load_securvalue('id_group', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['catalogue']['id_group']);

			if(!empty($id_group)) {
				$sql = 'SELECT id_cde FROM dims_mod_cata_cde WHERE id_regroupement = '.$id_group;

				$res = $db->query($sql);

				if($db->numrows($res) > 1) {
					while($cmd_list = $db->fetchrow($res)) {
						$_POST['cde_sel'][] = $cmd_list['id_cde'];
					}

					if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_commander_multiple.php')) {
						require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_commander_multiple.php';
					}
					else {
						require_once DIMS_APP_PATH.'modules/catalogue/display_commander_multiple.php';
					}
				}
				else {
					dims_redirect($dims->getScriptEnv().'?op=commandes');
				}
			}
			else {
				dims_redirect($dims->getScriptEnv().'?op=commandes');
			}
			break;
		case 'mettre_en_attente':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';

			$CLNO = dims_load_securvalue($list_adr_livraison, dims_const::_DIMS_CHAR_INPUT, false, true);
			$sel_adr = dims_load_securvalue('sel_adr', dims_const::_DIMS_NUM_INPUT, false, true);

			if ($CLNO != "" || $sel_adr == 0) {
				switch ($sel_adr) {
					case 0:
						$CLNO = -1;
						$adr['CNOML'] = dims_load_securvalue('new_adr_CNOML', dims_const::_DIMS_CHAR_INPUT, false, true);
						$adr['CRUEL'] = dims_load_securvalue('new_adr_CRUEL', dims_const::_DIMS_CHAR_INPUT, false, true);
						$adr['CAUXL'] = dims_load_securvalue('new_adr_CAUXL', dims_const::_DIMS_CHAR_INPUT, false, true);
						$adr['CPPTLL'] = dims_load_securvalue('new_adr_CPPTLL', dims_const::_DIMS_CHAR_INPUT, false, true);
						$adr['CVILL'] = dims_load_securvalue('new_adr_CVILL', dims_const::_DIMS_CHAR_INPUT, false, true);
						break;
					case -1:
						$CLNO = -1;
						$list_adr_livraison = "";
						$adr['CLNO'] = -1;
						$adr['CNOML'] = "";
						$adr['CRUEL'] = "";
						$adr['CAUXL'] = "";
						$adr['CPPTLL'] = "__NOPORT__"; // Valeur spéciale
						$adr['CVILL'] = "";
						break;
					default:
						if ($CLNO != '-1') {
							$sql = "SELECT * FROM dims_mod_vpc_livraison WHERE CLREF = '{$_SESSION['catalogue']['code_client']}'";
							if($CLNO != '') $sql .= " AND CLNO = '". addslashes($CLNO) ."'";

							$db->query($sql);
							$adr = $db->fetchrow();
						}
						break;
				}
			}

			$commande_id = dims_load_securvalue('commande_id', dims_const::_DIMS_NUM_INPUT, false, true);
			$commande_libelle = dims_load_securvalue('commande_libelle', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_nom = dims_load_securvalue('cli_nom', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_rue = dims_load_securvalue('cli_rue', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_aux = dims_load_securvalue('cli_aux', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_cp = dims_load_securvalue('cli_cp', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_vil = dims_load_securvalue('cli_vil', dims_const::_DIMS_CHAR_INPUT, false, true);

			if (isset($commande_id) && $commande_id != '') {
				$commande = new commande();
				$commande->open($commande_id);
				$articles = $commande->getarticles();

				// on récupère les articles de la commande
				foreach ($articles as $article) {
					$commande->articles[$article['ref_article']]['qte'] = $article['qte'];
				}

				// on ajoute les articles du panier
				foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $article) {
					if (!isset($commande->articles[$ref]['qte'])) $commande->articles[$ref]['qte'] = 0;
					$commande->articles[$ref]['qte'] += $article['qte'];
				}
				if (isset($commande_libelle) && trim($commande_libelle) != "") $commande->fields['libelle'] = $commande_libelle;
				$commande->fields['commentaire'] = htmlentities(dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, false, true));

				if (isset($adr)) {
					$commande->fields['CNOML']  = htmlentities($adr['CNOML'],ENT_QUOTES);
					$commande->fields['CRUEL']  = htmlentities($adr['CRUEL'],ENT_QUOTES);
					$commande->fields['CAUXL']  = htmlentities($adr['CAUXL'],ENT_QUOTES);
					$commande->fields['CPPTLL'] = htmlentities($adr['CPPTLL'],ENT_QUOTES);
					$commande->fields['CVILL']  = htmlentities($adr['CVILL'],ENT_QUOTES);
					$commande->fields['CLNO']   = addslashes($CLNO);
				}

				// Adresse de facturation
				$commande->fields['cli_nom']    = addslashes($cli_nom);
				$commande->fields['cli_rue']    = addslashes($cli_rue);
				$commande->fields['cli_aux']    = addslashes($cli_aux);
				$commande->fields['cli_cp']     = addslashes($cli_cp);
				$commande->fields['cli_vil']    = addslashes($cli_vil);

				// Enregistrement de l'adresse de facturation dans le client
				$save_address_fact = dims_load_securvalue('save_address_fact', dims_const::_DIMS_CHAR_INPUT, false, true);
				if (!empty($save_address_fact)) {
					require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client_simple.php';
					$client = new client_simple();
					$client->open($_SESSION['catalogue']['code_client']);
					$client->fields['CNOM'] = $cli_nom;
					$client->fields['CRUE'] = $cli_rue;
					$client->fields['CAUX'] = $cli_aux;
					$client->fields['CCPTL'] = $cli_cp;
					$client->fields['CVIL'] = $cli_vil;
					$client->save();
				}

				$commande->save();
			}
			else {
				$user = new user();
				$user->open($_SESSION['dims']['userid']);
				$groups = $user->getgroups(true);

				//recherche des groupes enfants
				$group = new group();
				$group->open(key($groups));

				$commande = new commande();
				$commande->fields['id_group'] = $group->fields['id'];
				$commande->fields['ref_client'] = $_SESSION['catalogue']['code_client'];
				$commande->fields['libelle'] = dims_load_securvalue('commande_libelle', dims_const::_DIMS_CHAR_INPUT, false, true);
				$commande->fields['commentaire'] = htmlentities(dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, false, true));
				$commande->fields['commentaire2'] = htmlentities(dims_load_securvalue('commentaire2', dims_const::_DIMS_CHAR_INPUT, false, true));
				$commande->articles = $_SESSION['catalogue']['panier']['articles'];
				$commande->fields['id_budget'] = 0;
				$commande->fields['user_name'] = $_SESSION['catalogue']['client_firstname'] ." ". $_SESSION['catalogue']['client_lastname'];
				$commande->fields['id_user'] = $_SESSION['dims']['userid'];
				if (isset($adr)) {
					$commande->fields['CNOML'] = htmlentities($adr['CNOML'],ENT_QUOTES);
					$commande->fields['CRUEL'] = htmlentities($adr['CRUEL'],ENT_QUOTES);
					$commande->fields['CAUXL'] = htmlentities($adr['CAUXL'],ENT_QUOTES);
					$commande->fields['CPPTLL'] = htmlentities($adr['CPPTLL'],ENT_QUOTES);
					$commande->fields['CVILL'] = htmlentities($adr['CVILL'],ENT_QUOTES);
					$commande->fields['CLNO'] = addslashes($CLNO);
				}

				// Adresse de facturation
				$commande->fields['cli_nom']    = addslashes($cli_nom);
				$commande->fields['cli_rue']    = addslashes($cli_rue);
				$commande->fields['cli_aux']    = addslashes($cli_aux);
				$commande->fields['cli_cp']     = addslashes($cli_cp);
				$commande->fields['cli_vil']    = addslashes($cli_vil);

				// Enregistrement de l'adresse de facturation dans le client
				$save_address_fact = dims_load_securvalue('save_address_fact', dims_const::_DIMS_NUM_INPUT, false, true);
				if (!empty($save_address_fact)) {
					require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client_simple.php';
					$client = new client_simple();
					$client->open($_SESSION['catalogue']['code_client']);
					$client->fields['CNOM'] = $cli_nom;
					$client->fields['CRUE'] = $cli_rue;
					$client->fields['CAUX'] = $cli_aux;
					$client->fields['CCPTL'] = $cli_cp;
					$client->fields['CVIL'] = $cli_vil;
					$client->save();
				}

				$commande->save();

				// On met a jour le nb de commandes a valider
				$_SESSION['catalogue']['nb_cmd_val']++;
			}

			$_SESSION['catalogue']['panier']['articles'] = array();
			$_SESSION['catalogue']['panier']['montant'] = 0;

			$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, false, true);

			switch ($op) {
				case 'enregistrer_commande_fin':
					if (isset($commande->fields['id']) && $commande->fields['id'] != '') dims_redirect("/index.php?op=valider_commande&id_cmd={$commande->fields['id']}");
					else dims_redirect('/index.php?op=commandes');
					break;
				case 'mettre_en_attente':
					dims_redirect("/index.php?op=commandes&id_cmd={$commande->fields['id']}");
					break;
				case 'enregistrer_commande_deco':
					dims_redirect('/index.php?dims_logout=1');
					break;
			}
			break;
		case 'confirm_cmd':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Confirmation de commande','?op=compte', '?op=confirm_cmd', $catalogue));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'/modules/catalogue/display_confirm_cmd.php';
			break;
		case 'enregistrer_adresse':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$user = new user();
			$user->open($_SESSION['dims']['userid']);
			$groups = $user->getgroups(true);

			//recherche des groupes enfants
			$group = new group();
			$group->open(key($groups));

			// Enregistrement de la commande
			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
			$commande = new commande();

			$validee                = dims_load_securvalue('validee', dims_const::_DIMS_NUM_INPUT, false, true);
			$id_cmd                 = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, false, true);
			$commande_id            = dims_load_securvalue('commande_id', dims_const::_DIMS_NUM_INPUT, false, true);
			$save                   = dims_load_securvalue('save', dims_const::_DIMS_NUM_INPUT, false, true);
			$list_adr_livraison     = dims_load_securvalue('list_adr_livraison', dims_const::_DIMS_CHAR_INPUT, false, true);
			$sel_adr                = dims_load_securvalue('sel_adr', dims_const::_DIMS_NUM_INPUT, false, true);
			$list_adr_livraison2    = dims_load_securvalue('list_adr_livraison2', dims_const::_DIMS_CHAR_INPUT, false, true);
			$new_adr_CNOML          = dims_load_securvalue('new_adr_CNOML', dims_const::_DIMS_CHAR_INPUT, false, true);
			$new_adr_CRUEL          = dims_load_securvalue('new_adr_CRUEL', dims_const::_DIMS_CHAR_INPUT, false, true);
			$new_adr_CAUXL          = dims_load_securvalue('new_adr_CAUXL', dims_const::_DIMS_CHAR_INPUT, false, true);
			$new_adr_CPPTLL         = dims_load_securvalue('new_adr_CPPTLL', dims_const::_DIMS_CHAR_INPUT, false, true);
			$new_adr_CVILL          = dims_load_securvalue('new_adr_CVILL', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_nom                = dims_load_securvalue('cli_nom', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_rue                = dims_load_securvalue('cli_rue', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_aux                = dims_load_securvalue('cli_aux', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_cp                 = dims_load_securvalue('cli_cp', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cli_vil                = dims_load_securvalue('cli_vil', dims_const::_DIMS_CHAR_INPUT, false, true);
			$commande_libelle       = dims_load_securvalue('commande_libelle', dims_const::_DIMS_CHAR_INPUT, false, true);
			$commentaire            = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, false, true);
			$nb_dest                = dims_load_securvalue('nb_dest', dims_const::_DIMS_NUM_INPUT, false, true);

			// Si validation d'une commande existante
			if (!empty($commande_id)) {
				$commande->open($commande_id);

				if (!$commande->fields['hors_cata']) {
					// Recuperation des articles de la commande
					$articles = $commande->getarticles();
					foreach($articles as $article) {
						$commande->articles[$article['ref_article']]['qte'] = $article['qte'];
					}
					// Ajout des articles du panier
					if (!empty($_SESSION['catalogue']['panier']['articles'])) {
						foreach($_SESSION['catalogue']['panier']['articles'] as $ref => $article) {
							if (!isset($commande->articles[$ref]['qte'])) $commande->articles[$ref]['qte'] = 0;
							$commande->articles[$ref]['qte'] += $article['qte'];
						}
					}
				} else {
					// Recuperation des articles de la commande
					$articles = $commande->getarticles();
					foreach($articles as $article) {
						$commande->articles[] = array(
							'reference'     => $article['reference'],
							'designation'   => $article['designation'],
							'qte'           => $article['qte'],
							'pu'            => $article['pu']
						);
					}
				}
			} else {
				// On met a jour le nb de commandes à valider
				$_SESSION['catalogue']['nb_cmd_val']++;

				$commande->articles = $_SESSION['catalogue']['panier']['articles'];

				$commande->fields['id_budget']  = 0;
				$commande->fields['id_group']   = $group->fields['id'];
				$commande->fields['ref_client'] = $_SESSION['catalogue']['code_client'];
				$commande->fields['libelle']    = $commande_libelle;
				$commande->fields['id_user']    = $_SESSION['dims']['userid'];
				$commande->fields['user_name']  = $_SESSION['catalogue']['client_firstname'] ." ". $_SESSION['catalogue']['client_lastname'];
			}

			if (isset($commentaire)) {
				$commande->fields['commentaire'] = $commentaire;
			}

			if ( !empty($nb_dest) && $nb_dest > 0 ) {
				$commande->fields['nb_dest'] = $nb_dest;
			} else {
				$commande->fields['nb_dest'] = 0;
			}

			if (isset($commentaire2)) {
				$commande->fields['commentaire2'] = $commentaire2;
			}
			if (isset($commande_libelle) && trim($commande_libelle) != '') {
				$commande->fields['libelle'] = $commande_libelle;
			}

			// Si l'utilisateur a le droit de modifier son adresse de facturation
			if (!isset($_SESSION['catalogue']['params']['nomodadrfact'])) {
				// Adresse de facturation
				$commande->fields['cli_nom']    = $cli_nom;
				$commande->fields['cli_rue']    = $cli_rue;
				$commande->fields['cli_aux']    = $cli_aux;
				$commande->fields['cli_cp']     = $cli_cp;
				$commande->fields['cli_vil']    = $cli_vil;

				// Enregistrement de l'adresse de facturation dans le client
				if (isset($save_address_fact)) {
					require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client_simple.php';
					$client = new client_simple();
					$client->open($_SESSION['catalogue']['code_client']);
					$client->fields['CNOM'] = $cli_nom;
					$client->fields['CRUE'] = $cli_rue;
					$client->fields['CAUX'] = $cli_aux;
					$client->fields['CCPTL'] = $cli_cp;
					$client->fields['CVIL'] = $cli_vil;
					$client->save();
				}
			}
			else {
				// reouverture du bon client si validation d'une commande via un validateur multi-compte
				if (in_array($commande->fields['ref_client'], $_SESSION['catalogue']['liste_clients'])) {
					require_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
					$client = new client();
					$client->open($commande->fields['ref_client']);
					$_SESSION['catalogue']['code_client'] = $client->fields['CREF'];
				}

				// Enregistrement de l'adresse de facturation dans la commande
				$commande->fields['cli_nom']    = $client->fields['CNOM'];
				$commande->fields['cli_rue']    = $client->fields['CRUE'];
				$commande->fields['cli_aux']    = $client->fields['CAUX'];
				$commande->fields['cli_cp']     = $client->fields['CCPTL'];
				$commande->fields['cli_vil']    = $client->fields['CVIL'];
			}

			// Enregistrement de l'adresse
			switch ($sel_adr) {
				case 0:
					$CLNO = -1;
					$adr['CLNO'] = -1;
					$adr['CNOML'] = $new_adr_CNOML;
					$adr['CRUEL'] = $new_adr_CRUEL;
					$adr['CAUXL'] = $new_adr_CAUXL;
					$adr['CPPTLL'] = $new_adr_CPPTLL;
					$adr['CVILL'] = $new_adr_CVILL;

					if ($save_address == 1) {
						require_once DIMS_APP_PATH.'/modules/catalogue/include/class_livraison.php';
						$livraison = new livraison();
						foreach ($adr as $key => $value) {
							$livraison->fields[$key] = $value;
						}
						$livraison->fields['CLREF'] = $_SESSION['catalogue']['code_client'];
						$livraison->save();

						$CLNO = $livraison->fields['CLNO'];
					}
					break;
				case -1:
					$list_adr_livraison = "";
					$adr['CLNO'] = -1;
					$adr['CNOML'] = "";
					$adr['CRUEL'] = "";
					$adr['CAUXL'] = "";
					$adr['CPPTLL'] = "__NOPORT__"; // Valeur spéciale
					$adr['CVILL'] = "";
					break;
				default:
					$db->query("
						SELECT  *
						FROM    dims_mod_vpc_livraison
						WHERE   CLREF = '{$_SESSION['catalogue']['code_client']}'
						AND     CLNO = '". $$list_adr_livraison ."'");
					$adr = $db->fetchrow();
					break;
			}

			// Adresse de livraison
			$commande->fields['CLNO']       = addslashes($adr['CLNO']);
			$commande->fields['CNOML']      = htmlentities($adr['CNOML'],ENT_QUOTES);
			$commande->fields['CRUEL']      = htmlentities($adr['CRUEL'],ENT_QUOTES);
			$commande->fields['CAUXL']      = htmlentities($adr['CAUXL'],ENT_QUOTES);
			$commande->fields['CPPTLL']     = htmlentities($adr['CPPTLL'],ENT_QUOTES);
			$commande->fields['CVILL']      = htmlentities($adr['CVILL'],ENT_QUOTES);

			$commande->save();

			// Vidage du panier
			$_SESSION['catalogue']['panier']['articles'] = array();
			$_SESSION['catalogue']['panier']['montant'] = 0;
			if ($oCatalogue->getParams('cart_management') == 'cookie') {
				panier2cookie();
			}
			elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
				panier2bdd();
			}

			// si pas d'etape 4, on valide automatiquement la commande
			if (isset($_SESSION['catalogue']['params']['noetape4']) && isset($_SESSION['catalogue']['params']['paiem'])) {
				// on valide la commande
				dims_redirect("/index.php?op=confirmer_commande&id_cmd={$commande->fields['id']}&mode_paiement={$_SESSION['catalogue']['params']['paiem']}");
			}
			else {
				// on affiche les frais de port
				dims_redirect("/index.php?op=afficher_fraisport&id_cmd={$commande->fields['id']}");
			}
			break;
		case 'renseigner_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$commande_id = dims_load_securvalue('commande_id', dims_const::_DIMS_NUM_INPUT, false, true);
			$commentaire = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, false, true);

			// reprise du panier pour ecraser la commande hors cata
			// du client et la repasser en commande exceptionnelle
			if ( isset($_SESSION['catalogue']['iwasadmin']) && $_SESSION['catalogue']['iwasadmin'] && !empty($commande_id) ) {
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
				$commande = new commande();
				$commande->open($commande_id);
				$commande->fields['hors_cata'] = 0;
				$commande->fields['etat'] = 'en_cours';
				$commande->fields['date_validation'] = dims_createtimestamp();
				$commande->articles = array();

				if ($commande->fields['commentaire'] != '') $commande->fields['commentaire'] .= "\n";
				$commande->fields['commentaire'] .= addslashes($commentaire);

				// Ajout des articles du panier
				if (!empty($_SESSION['catalogue']['panier']['articles'])) {
					foreach($_SESSION['catalogue']['panier']['articles'] as $ref => $article) {
						if (!isset($commande->articles[$ref]['qte'])) $commande->articles[$ref]['qte'] = 0;
						$commande->articles[$ref]['qte'] += $article['qte'];
					}
				}

				$commande->save();
				$_SESSION['catalogue']['nb_cmd_val']--;

				// Vidage du panier
				$_SESSION['catalogue']['panier']['articles'] = array();
				$_SESSION['catalogue']['panier']['montant'] = 0;

				// Suppression des anciennes lignes de commande
				$db->query('DELETE FROM dims_mod_vpc_cmd_detail_hc WHERE id_cmd = '. $commande->fields['id']);

				// Envoi du mail de notification au createur de la commande
				$user = new user();
				$user->open($commande->fields['id_user']);

				if (trim($user->fields['email']) != '' && trim($user->fields['email']) != 'null' && trim($user->fields['email']) != 'NULL') {
					require_once './include/functions/mail.php';

					$to[0]['name'] = dims_convertaccents($user->fields['firstname'] ." ". $user->fields['lastname']);
					$to[0]['address'] = $user->fields['email'];

					$from[0]['name'] = '';
					$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');

					$login = $user->fields['login'];
					$passwd = $user->fields['password'];

					$subject = "Demande de validation de commande";

					$message = "Votre commande exceptionnelle a &eacute;t&eacute; chiffr&eacute;e et attend votre validation.<br/><br/>";
					$message .= 'Pour visualiser cette commande, cliquez <a href="'.dims_urlencode('http://'.$_SERVER['HTTP_HOST'].'/index.php?dims_moduleid='._MODULEID_CATALOGUE.'&op=commandes&id_cmd=5772&dims_login='.$login.'&dims_password='.$passwd.'&dims_pwdmd5=1').'">ici</a>.<br/><br/>';

					dims_send_mail($from, $to, $subject, $message);
				}
				dims_redirect($dims->getScriptEnv()."?op=commandes");
			}
			else {
				dims_redirect($dims->getScriptEnv()."?op=valider_panier&etape=3");
			}
			break;
		case 'afficher_fraisport':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Frais de port','','?op=afficher_fraisport', '', $catalogue));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'/modules/catalogue/display_fraisport.php';
			break;
		case 'confirmer_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'/modules/catalogue/confirmer_commande.php';
			break;
		case 'renouveler_commande':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$id_cde = dims_load_securvalue('id_cde', dims_const::_DIMS_NUM_INPUT, true, false);

			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
			$commande = new commande();
			$commande->open($id_cde);

			if (!$commande->fields['hors_cata']) {
				$_SESSION['catalogue']['panier']['articles'] = array();
				$_SESSION['catalogue']['panier']['montant'] = 0;

				foreach ($commande->getlignes() as $ligne) {
					$article = new article();
					$article->open($ligne->fields['ref']);
					$prix = catalogue_getprixarticle($article, $ligne->fields['qte']);
					$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

					if (!isset($_SESSION['catalogue']['panier']['articles'][$ligne->fields['ref']]['qte'])) {
						$_SESSION['catalogue']['panier']['articles'][$ligne->fields['ref']]['qte'] = 0;
					}
					$_SESSION['catalogue']['panier']['articles'][$ligne->fields['ref']]['qte'] += $ligne->fields['qte'];

					if ($oCatalogue->getParams('cata_base_ttc')) {
						$_SESSION['catalogue']['panier']['montant'] += $ligne->fields['qte'] * $prixaff;
					}
					else {
						$_SESSION['catalogue']['panier']['montant'] += $ligne->fields['qte'] * $prix;
					}
				}

				if ($oCatalogue->getParams('cart_management') == 'cookie') {
					panier2cookie();
				}
				elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
					panier2bdd();
				}

				dims_redirect('/index.php?op=panier');
			}
			else {
				dims_redirect('/index.php?op=compte');
			}
			break;
		case 'historique':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Historique','?op=compte', '?op=historique', $catalogue));

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_historique.php')) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_historique.php';
			}
			else {
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'modules/catalogue/display_historique.php';
			}
			break;
		case 'bonslivraison':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Mes bons de livraison','?op=compte', '?op=bonslivraison', $catalogue));

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_bonslivraison.php')) {
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_bonslivraison.php';
			}
			else {
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'modules/catalogue/display_bonslivraison.php';
			}
			break;
		case 'reliquats':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Reliquats','?op=compte', '?op=reliquats', $catalogue));
			require_once DIMS_APP_PATH.'modules/catalogue/display_reliquats.php';
			break;
		case 'saisierapide':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Saisie rapide','','?op=saisierapide', '', $catalogue,0,true));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			require_once DIMS_APP_PATH.'modules/catalogue/display_saisierapide.php';
			break;
		case 'detail_art':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'modules/catalogue/xml_detail_art.php';
			break;
		case 'enregistrer_saisierapide':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			require_once DIMS_APP_PATH.'modules/catalogue/xml_enregistrer_saisierapide.php';
			break;
		case 'panierstype':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if ( $oCatalogue->getParams('panier_type') || $oCatalogue->getParams('school_lists') ) {
				$saveOpForArti = true;
				$smarty->assign('ariane', ariane_factory('Mon compte','Mes paniers types','?op=compte', '?op=panierstype', $catalogue));
				$smarty->assign('tpl_name', 'catalogue_content');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'modules/catalogue/display_panierstype.php';
			}
			break;
		case 'effacer_pt_article':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if ($oCatalogue->getParams('panier_type')) {
				$id_panier = dims_load_securvalue('id_panier', _DIM_NUM_INPUT, true, false);
				$pref = dims_load_securvalue('pref', _DIM_CHAR_INPUT, true, false);

				if (!empty($pref)) {
					$db->query("DELETE FROM dims_mod_cata_panierstypes_details WHERE id_paniertype = $id_panier AND ref_article = '$pref'");
				}
				dims_redirect('/index.php?op=panierstype&id_panier='.$id_panier);
			}
			break;
		case 'ajouter_panier':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$nbarticles = dims_load_securvalue('nbarticles', dims_const::_DIMS_NUM_INPUT, false, true);

			for ($i = 1; $i <= $nbarticles; $i++) {
				$ref = dims_load_securvalue('ref'.$i, dims_const::_DIMS_CHAR_INPUT, false, true);
				$qte = dims_load_securvalue('qte'.$i, dims_const::_DIMS_NUM_INPUT, false, true);

				if ($qte > 0) {
					$article = new article();
					$article->open($ref);

					$prix = catalogue_getprixarticle($article, $qte);
					$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['PCTVA']]);

					if (!isset($_SESSION['catalogue']['panier']['articles'][$ref]['qte'])) $_SESSION['catalogue']['panier']['articles'][$ref]['qte'] = 0;
					$_SESSION['catalogue']['panier']['articles'][$ref]['qte'] += $qte;

					if (!isset($_SESSION['catalogue']['panier']['montant'])) $_SESSION['catalogue']['panier']['montant'] = 0;
					if ($oCatalogue->getParams('cata_base_ttc')) {
						$_SESSION['catalogue']['panier']['montant'] += $qte*$prixaff;
					}
					else {
						$_SESSION['catalogue']['panier']['montant'] += $qte*$prix;
					}
				}
			}
			dims_redirect("$scriptenv?op=panier");
			break;
		case 'supprimer_panier':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if ($oCatalogue->getParams('panier_type')) {
				$id_panier = dims_load_securvalue('id_panier', dims_const::_DIMS_NUM_INPUT, true, false);
				$paniertype = new paniertype();
				$paniertype->open($id_panier);
				$paniertype->delete();
				dims_redirect("$scriptenv?op=panierstype");
			}
			break;
		case 'enregistrer_panier':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if ($oCatalogue->getParams('panier_type')) {
				$smarty->assign('tpl_name', 'catalogue_content');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				$smarty->assign('ariane', ariane_factory('Mon compte','Mes paniers types','?op=compte', '?op=panierstype', $catalogue));
				include DIMS_APP_PATH.'/modules/catalogue/display_paniertype_ajout.php';
			}
			break;
		case 'enregistrer_panier_fin':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if ($oCatalogue->getParams('panier_type')) {
				$panier_libelle = dims_load_securvalue('panier_libelle', dims_const::_DIMS_CHAR_INPUT, false, true);
				$panier_id = dims_load_securvalue('panier_id', dims_const::_DIMS_NUM_INPUT, false, true);
				$panier_type = dims_load_securvalue('panier_type', dims_const::_DIMS_NUM_INPUT, true, true);
				$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, false, true);

				switch ($action) {
					case 'create_new':

						if(empty($panier_libelle)) {
							dims_redirect($dims->getScriptEnv().'?op=enregistrer_panier&err=nolabel&type='.$panier_type);
						}
						$user = new user();
						$user->open($_SESSION['dims']['userid']);
						$groups = $user->getgroups(true);

						//recherche des groupes enfants
						$group = new group();
						$group->open(key($groups));

						$paniertype = new paniertype();
						$paniertype->fields['id_group'] = $group->fields['id'];
						$paniertype->fields['id_user'] = $_SESSION['dims']['userid'];
						$paniertype->fields['libelle'] = $panier_libelle;
						$paniertype->fields['type'] = $panier_type;
						if ($panier_type == _CATA_PANIER_TYPE_LIST_SCOLAIRE) {
							$paniertype->fields['code_liste'] = trim(strtoupper(base_convert(uniqid(), 16,36)));
						}
						$paniertype->articles = $_SESSION['catalogue']['panier']['articles'];
						$paniertype->save();
						break;
					case 'add_to_existing':
						if (empty($panier_id)) {
							dims_redirect($dims->getScriptEnv().'?op=enregistrer_panier&err=nocart&type='.$panier_type);
						}

						$paniertype = new paniertype();
						$paniertype->open($panier_id);
						$paniertype->articles = $paniertype->getrefqte();
						foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $art) {
							$paniertype->articles[$ref]['qte'] = $art['qte'];
						}
						$paniertype->save();
						break;
				}

				if($paniertype->fields['type'] == _CATA_PANIER_TYPE_LIST_SCOLAIRE) {
					dims_redirect("$scriptenv?op=panierstype_listscolaire&id_paniertype=".$paniertype->get('id'));
				}
				else {
					dims_redirect("$scriptenv?op=panierstype");
				}
			}
			break;
		case 'panierstype_listscolaire':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if ($oCatalogue->getParams('panier_type')) {
				$smarty->assign('tpl_name', 'catalogue_content');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				$smarty->assign('ariane', ariane_factory('Mon compte','liste scolaire','?op=compte', '?op=panierstype', $catalogue));
				include DIMS_APP_PATH.'/modules/catalogue/display_paniertype_listscolaire.php';
			}
			break;
		case 'search_schoollist':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			include DIMS_APP_PATH.'/modules/catalogue/display_paniertype_listscolaire_search.php';
			break;
		case 'add_schoollist':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if(!empty($_POST['sel'])) {
				foreach($_POST['sel'] as $key => $val) {
					$ref = dims_load_securvalue('ref'.$key, dims_const::_DIMS_CHAR_INPUT, true, true);
					$qte = dims_load_securvalue('qte'.$key, dims_const::_DIMS_NUM_INPUT, true, true);

					if(!isset($_SESSION['catalogue']['panier']['articles'][$ref]['qte'])) $_SESSION['catalogue']['panier']['articles'][$ref]['qte'] = 0;
					$_SESSION['catalogue']['panier']['articles'][$ref]['qte'] += $qte;
				}
			}
			dims_redirect($dims->getScriptEnv().'?op=panier');
			break;
		case 'factures':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			$smarty->assign('ariane', ariane_factory('Mon compte','Mes factures','?op=compte', '?op=factures', $catalogue));

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_factures.php')) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_factures.php';
			}
			else {
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'modules/catalogue/display_factures.php';
			}
			break;
		case 'imprimer_facture':
			if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptenv());
			}

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/imprimer_facture.php')) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/imprimer_facture.php';
			}
			else {
				require_once DIMS_APP_PATH.'modules/catalogue/imprimer_facture.php';
			}
			break;
		case 'statistiques':
			if ($_SESSION['dims']['connected'] && $_SESSION['session_adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER) {
				$smarty->assign('ariane', ariane_factory('Mon compte','Statistiques','?op=compte', '?op=statistiques', $catalogue));
				$smarty->assign('tpl_name', 'catalogue_content');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'/modules/catalogue/display_statistiques.php';
			}
			break;
		case 'administration':
			// echo 'ici';die();
			if ($_SESSION['session_adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER) {
				$smarty->assign('ariane', ariane_factory('Mon compte','Administration','?op=compte', '?op=administration', $catalogue));
				$smarty->assign('tpl_name', 'catalogue_content');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'/modules/catalogue/display_admin.php';
			}
			break;
		case 'selection_plus':
			$ref_article = dims_load_securvalue('ref_article', dims_const::_DIMS_CHAR_INPUT, true, true);
			if (!empty($ref_article) && ($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP)) {
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_selection.php';

				$selection = new selection();
				$selection->fields['ref_client'] = $_SESSION['catalogue']['code_client'];
				$selection->fields['ref_article'] = $ref_article;
				$selection->fields['selection'] = 1;
				$selection->save();
			}
			dims_redirect($_SERVER['HTTP_REFERER']);
			break;
		case 'selection_moins':
			$ref_article = dims_load_securvalue('ref_article', dims_const::_DIMS_CHAR_INPUT, true, true);
			if (!empty($ref_article) && ($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP)) {
				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_selection.php';

				$selection = new selection();
				$selection->open($_SESSION['catalogue']['code_client'],$ref_article);
				$selection->delete();
			}
			dims_redirect($_SERVER['HTTP_REFERER']);
			break;
		case 'extraits_compte':
			$smarty->assign('ariane', ariane_factory('Mon compte','Extraits de compte','?op=compte', '?op=extraits_compte', $catalogue));

			if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_extraits_compte.php')) {
				require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/display_extraits_compte.php';
			}
			else {
				$smarty->assign('tpl_name', 'catalogue_content');
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
				require_once DIMS_APP_PATH.'modules/catalogue/display_extraits_compte.php';
			}
			break;
		case 'return_to_admin':
			// repasser en administration
			if (!empty($_SESSION['catalogue']['iwasadmin'])) {
				// recherche de l'id_user de l'admin
				$rs = $db->query('SELECT id FROM dims_user WHERE login = \'admin\' LIMIT 0, 1');
				$row = $db->fetchrow($rs);
				$_SESSION['dims']['userid'] = $row['id'];
				unset($_SESSION['catalogue']['iwasadmin']);
				dims_redirect('/admin.php?reloadsession=1');
			}
			break;
		case 'call_request':
		case 'call_response':
		case 'call_autoresponse':
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			$page['TITLE'] = 'Catalogue / E-payement';
			$page['META_DESCRIPTION'] = 'Interface de payement en ligne.';
			$page['META_KEYWORDS'] = 'Catalogue, produits, articles, cyberplus';
			$page['CONTENT'] = '';

			ob_start();
			require_once './plugins/epayment/gateway.php';
			$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));
			ob_end_clean();
			break;
		case 'newsletter_subscription':
			// inscription a la newsletter
			$newsletter_email = dims_load_securvalue('newsletter_email', _DIMS_MAIL_INPUT, false, true);
			if ($newsletter_email != '') {
				// recherche de la mailing list
				require_once DIMS_APP_PATH.'modules/system/class_mailinglist_attach.php';
				$rs = $db->query('SELECT id FROM dims_mailinglist WHERE id_workspace = '.$_SESSION['dims']['workspaceid'].' AND public = 1 LIMIT 0, 1');
				if ($db->numrows($rs)) {
					$row = $db->fetchrow($rs);
					$ml_id = $row['id'];

					// on regarde si l'email y est pas deja
					$rs = $db->query('SELECT COUNT(*) AS c FROM dims_mailinglist_attach WHERE id_mailinglist = '.$ml_id.' AND email = \''.$newsletter_email.'\'');
					$row = $db->fetchrow($rs);
					if ($row['c'] == 0) {
						$mla = new mailinglist_attach();
						$mla->fields['id_mailinglist'] = $ml_id;
						$mla->fields['email'] = $newsletter_email;
						$mla->save();
					}

					$smarty->assign('catalogue', array('CONTENT' => '&nbsp;Merci de vous être abboné(e) à la newsletter.'));
				}
				else {
					$smarty->assign('catalogue', array('CONTENT' => '&nbsp;Une erreur est survenue lors de votre inscription.'));
				}
			}
			else {
				$smarty->assign('catalogue', array('CONTENT' => '&nbsp;Une erreur est survenue lors de votre inscription.'));
			}

			$page['TITLE'] = 'Panier';
			$page['META_DESCRIPTION'] = 'Visualisation votre panier';
			$page['META_KEYWORDS'] = 'Panier, articles, commande';
			$page['CONTENT'] = '';

			$smarty->assign('ariane', ariane_factory('Inscription à la newsletter','','','', $catalogue, 0, true));
			$smarty->assign('tpl_name', 'catalogue_content');
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
			break;
		case 'vrp_retour':
			unset($_SESSION['catalogue']['client_id']);
			unset($_SESSION['catalogue']['code_client']);
			unset($_SESSION['catalogue']['clientpro']);
			unset($_SESSION['catalogue']['csp']);
			unset($_SESSION['catalogue']['aff_prix']);
			unset($_SESSION['catalogue']['panier']);
			unset($_SESSION['catalogue']['prix']);
			unset($_SESSION['catalogue']['prix_cplmt']);

			// on recherche le compte utilisateur pour le reconnecter
			$rs = $db->query('SELECT login, password, date_creation FROM dims_user WHERE id = '.$_SESSION['catalogue']['vrp']['id_user'].' LIMIT 0, 1');
			if (!$db->numrows($rs)) {
				dims_redirect('/index.php?dims_logout=1');
			}
			$row = $db->fetchrow($rs);
			$connectParams = 'dims_login='.$row['login'].'&dims_password='.$row['password'].'&dims_pwdmd5=1';
			$connectUrl = 'index.php?dims_url='.urlencode(base64_encode($connectParams));
			dims_redirect($connectUrl);
			break;
		case 'fusion_cmd':
			if(!empty($_POST['cde_sel'])) {
				require DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_cde.php';

				$_SESSION['catalogue']['cde_operation']['cmd_fusion'] = array();

				if(count($_POST['cde_sel']) >= 2) {
					$fusion_cde = new cata_cde();

					foreach($_POST['cde_sel'] as $id_cde) {
						$err = false;
						$commande = new cata_cde();
						$commande->open(dims_sql_filter($id_cde));

						if(!$commande->isModifiable()) {
							$err = true;
							$_SESSION['catalogue']['cde_operation']['cmd_fusion']['error'][$commande->get('id')] = 'Vous n\'avez pas le droit de modifier cette commande.';
						}
						if($commande->fields['id_user'] != $_SESSION['dims']['userid']) {
							$err = true;
							$_SESSION['catalogue']['cde_operation']['cmd_fusion']['error'][$commande->get('id')] = 'Cette commande ne vous appartient.';
						}
						if(!empty($fusion_cde->fields['cli_liv_nom']) &&
						  ($fusion_cde->fields['cli_liv_nom'] 	!= $commande->fields['cli_liv_nom'] ||
						   $fusion_cde->fields['cli_liv_adr1'] 	!= $commande->fields['cli_liv_adr1'] ||
						   $fusion_cde->fields['cli_liv_adr2'] 	!= $commande->fields['cli_liv_adr2'] ||
						   $fusion_cde->fields['cli_liv_adr3'] 	!= $commande->fields['cli_liv_adr3'] ||
						   $fusion_cde->fields['cli_liv_cp']!= $commande->fields['cli_liv_cp'] ||
						   $fusion_cde->fields['cli_liv_ville']!= $commande->fields['cli_liv_ville'] ||
						   $fusion_cde->fields['cli_liv_id_pays'] != $commande->fields['cli_liv_id_pays'] ||
						   $fusion_cde->fields['cli_liv_pays'] 	!= $commande->fields['cli_liv_pays'])) {
							$err = true;
							$_SESSION['catalogue']['cde_operation']['cmd_fusion']['error'][$commande->get('id')] = 'Les adresses de livraison ne correspondent pas.';
						}
						if(!empty($fusion_cde->fields['etat']) && $fusion_cde->fields['etat'] != $commande->fields['etat']) {
							$err = true;
							$_SESSION['catalogue']['cde_operation']['cmd_fusion']['error'][$commande->get('id')] = 'Cette commande n\'a pas le même état.';
						}

						if(!$err) {
							$fusion_cde->fields['id_user']			= $commande->fields['id_user'];
							$fusion_cde->fields['id_module']		= $commande->fields['id_module'];
							$fusion_cde->fields['id_workspace']		= $commande->fields['id_workspace'];
							$fusion_cde->fields['id_client']		= $commande->fields['id_client'];
							$fusion_cde->fields['code_client']		= $commande->fields['code_client'];
							$fusion_cde->fields['etat']				= $commande->fields['etat'];
							$fusion_cde->fields['id_budget']		= $commande->fields['id_budget'];
							$fusion_cde->fields['date_cree']		= dims_createtimestamp();
							$fusion_cde->fields['date_validation']	= 0;

							$fusion_cde->fields['mode_paiement']	= $commande->fields['mode_paiement'];
							$fusion_cde->fields['commentaire']		= $commande->fields['commentaire'];
							$fusion_cde->fields['hors_cata']		= $commande->fields['hors_cata'];
							$fusion_cde->fields['user_name']		= $commande->fields['user_name'];
							$fusion_cde->fields['classe']			= $commande->fields['classe'];
							$fusion_cde->fields['adrfact']			= $commande->fields['adrfact'];
							$fusion_cde->fields['cli_nom']			= $commande->fields['cli_nom'];
							$fusion_cde->fields['cli_adr1']			= $commande->fields['cli_adr1'];
							$fusion_cde->fields['cli_adr2']			= $commande->fields['cli_adr2'];
							$fusion_cde->fields['cli_adr3']			= $commande->fields['cli_adr3'];
							$fusion_cde->fields['cli_cp']			= $commande->fields['cli_cp'];
							$fusion_cde->fields['cli_ville']		= $commande->fields['cli_ville'];
							$fusion_cde->fields['cli_id_pays']		= $commande->fields['cli_id_pays'];
							$fusion_cde->fields['cli_pays']			= $commande->fields['cli_pays'];
							$fusion_cde->fields['cli_tel1']			= $commande->fields['cli_tel1'];
							$fusion_cde->fields['cli_tel2']			= $commande->fields['cli_tel2'];
							$fusion_cde->fields['cli_fax']			= $commande->fields['cli_fax'];
							$fusion_cde->fields['cli_port']			= $commande->fields['cli_port'];
							$fusion_cde->fields['cli_email']		= $commande->fields['cli_email'];
							$fusion_cde->fields['cli_liv_nom']		= $commande->fields['cli_liv_nom'];
							$fusion_cde->fields['cli_liv_adr1']		= $commande->fields['cli_liv_adr1'];
							$fusion_cde->fields['cli_liv_adr2']		= $commande->fields['cli_liv_adr2'];
							$fusion_cde->fields['cli_liv_adr3']		= $commande->fields['cli_liv_adr3'];
							$fusion_cde->fields['cli_liv_cp']		= $commande->fields['cli_liv_cp'];
							$fusion_cde->fields['cli_liv_ville']	= $commande->fields['cli_liv_ville'];
							$fusion_cde->fields['cli_liv_id_pays']	= $commande->fields['cli_liv_id_pays'];
							$fusion_cde->fields['cli_liv_pays']		= $commande->fields['cli_liv_pays'];

							foreach($commande->getlignes() as $article) {
								$fusion_cde->addArticle($article['ref'], $article['qte']);
							}

							$_SESSION['catalogue']['cde_operation']['cmd_fusion']['success'][$commande->get('id')]['id'] = $commande->get('id');
							$_SESSION['catalogue']['cde_operation']['cmd_fusion']['success'][$commande->get('id')]['message'] = 'Commande utilisée pour la fusion.';
						}
					}

					if(count($_SESSION['catalogue']['cde_operation']['cmd_fusion']['success']) >= 2) {
						$fusion_cde->save();

						foreach($_SESSION['catalogue']['cde_operation']['cmd_fusion']['success'] as $id_cmd_success => $msg) {
							$commande = new cata_cde();
							$commande->open($id_cmd_success);

							$commande->delete();
						}

						$_SESSION['catalogue']['cde_operation']['cmd_fusion']['success'][$fusion_cde->get('id')]['id'] = $fusion_cde->get('id');
						$_SESSION['catalogue']['cde_operation']['cmd_fusion']['success'][$fusion_cde->get('id')]['message'] = 'Commande créée.';
					}
					else {
						unset($_SESSION['catalogue']['cde_operation']['cmd_fusion']['success']);
					}
				}
				else {
					$_SESSION['catalogue']['cde_operation']['cmd_fusion']['error'] = 'Vous devez selectionner au moins deux commandes.';
				}
			}

			dims_redirect($dims->getScriptEnv().'?op=commandes');
			break;
		case 'group_cmd':
			if(!empty($_POST['cde_sel'])) {
				require DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_cde.php';

				$_SESSION['catalogue']['cde_operation']['cmd_group'] = array();

				if(count($_POST['cde_sel']) >= 2) {
					$refData = array();
					foreach($_POST['cde_sel'] as $id_cde) {
						$err = false;
						$commande = new cata_cde();
						$commande->open(dims_sql_filter($id_cde));

						if(!$commande->isValideable()) {
							$err = true;
							$_SESSION['catalogue']['cde_operation']['cmd_group']['error'][] = $commande->get('id').' : Vous n\'avez pas le droit de modifier cette commande.';
						}
						if(!empty($refData) &&
						  ($refData['cli_liv_nom'] 		!= $commande->fields['cli_liv_nom'] ||
						   $refData['cli_liv_adr1'] 	!= $commande->fields['cli_liv_adr1'] ||
						   $refData['cli_liv_adr2'] 	!= $commande->fields['cli_liv_adr2'] ||
						   $refData['cli_liv_adr3'] 	!= $commande->fields['cli_liv_adr3'] ||
						   $refData['cli_liv_cp']		!= $commande->fields['cli_liv_cp'] ||
						   $refData['cli_liv_ville']	!= $commande->fields['cli_liv_ville'] ||
						   $refData['cli_liv_id_pays'] 	!= $commande->fields['cli_liv_id_pays'] ||
						   $refData['cli_liv_pays'] 	!= $commande->fields['cli_liv_pays'])) {
							$err = true;
							$_SESSION['catalogue']['cde_operation']['cmd_group']['error'][] = $commande->get('id').' : Les adresses de livraison ne correspondent pas.';
						}
						if(!empty($fusion_cde->fields['etat']) && $fusion_cde->fields['etat'] 	!= $commande->fields['etat']) {
							$err = true;
							$_SESSION['catalogue']['cde_operation']['cmd_group']['error'][] =$commande->get('id').' : Cette commande n\'a pas le même état.';
						}

						if(!$err) {
							$refData['cli_liv_nom'] 	= $commande->fields['cli_liv_nom'];
							$refData['cli_liv_adr1'] 	= $commande->fields['cli_liv_adr1'];
							$refData['cli_liv_adr2'] 	= $commande->fields['cli_liv_adr2'];
							$refData['cli_liv_adr3'] 	= $commande->fields['cli_liv_adr3'];
							$refData['cli_liv_cp']		= $commande->fields['cli_liv_cp'];
							$refData['cli_liv_ville']	= $commande->fields['cli_liv_ville'];
							$refData['cli_liv_id_pays'] = $commande->fields['cli_liv_id_pays'];
							$refData['cli_liv_pays'] 	= $commande->fields['cli_liv_pays'];

							$_SESSION['catalogue']['cde_operation']['cmd_group']['success'][$commande->get('id')]['id'] = $commande->get('id');
							$_SESSION['catalogue']['cde_operation']['cmd_group']['success'][$commande->get('id')]['message'] = 'Commande utilisé pour le groupe.';
						}
					}

					if(count($_SESSION['catalogue']['cde_operation']['cmd_group']['success']) >= 2) {
						$sql = 'SELECT MAX(id_regroupement)+1 AS id_regroup FROM dims_mod_cata_cde';
						$res = $db->query($sql);
						$regroup = $db->fetchrow($res);

						foreach($_SESSION['catalogue']['cde_operation']['cmd_group']['success'] as $successOrder) {
							$commande = new cata_cde();
							$commande->open($successOrder['id']);

							$commande->fields['id_regroupement'] = $regroup['id_regroup'];

							$commande->save();
						}

						$_SESSION['catalogue']['cde_operation']['cmd_group']['success'][] = array('id' => $regroup['id_regroup'], 'message' => 'Identifiant du groupe de commandes');
					}
					else {
						unset($_SESSION['catalogue']['cde_operation']['cmd_group']['success']);
					}
				}
				else {
					$_SESSION['catalogue']['cde_operation']['cmd_group']['error'] = 'Vous devez selectionner au moins deux commandes.';
				}
			}

			dims_redirect($dims->getScriptEnv().'?op=commandes');
			break;
		case 'degrouper_command':
			$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, true, true);

			if(!empty($id_cmd)) {
				require DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
				$commande = new commande();
				$commande->open($id_cmd);

				if($commande->isValideable()) {
					$sql = 'SELECT id_cde FROM dims_mod_cata_cde WHERE id_regroupement = '.$commande->fields['id_regroupement'];

					$commande->fields['id_regroupement'] = 0;
					$commande->save();

					$res = $db->query($sql);
					if($db->numrows($res) == 1) {
						$data = $db->fetchrow($res);

						$commande = new commande();
						$commande->open($data['id_cde']);

						$commande->fields['id_regroupement'] = 0;
						$commande->save();
					}
				}
			}

			dims_redirect($dims->getScriptEnv().'?op=commandes');
			break;
		case 'add_to_favorites':
			$artid = dims_load_securvalue('artid', dims_const::_DIMS_NUM_INPUT, true, false, true);

			if ($artid > 0) {
				$article = new article();
				if ($article->open($artid)) {
					// Ajout de l'article au panier type
					$pt_favoris->articles = $pt_favoris->getarticles();

					if (!isset($pt_favoris->articles[$article->getReference()])) {
						$pt_favoris->articles[$article->getReference()]['qte'] = 1;
						$pt_favoris->save();
					}
				}
			}

			dims_redirect($_SERVER['HTTP_REFERER']);
			break;
		case 'add_to_panier_type':
			$id_pt 		= dims_load_securvalue('id_pt', dims_const::_DIMS_NUM_INPUT, false, true, true);
			$artid		= dims_load_securvalue('artid', dims_const::_DIMS_NUM_INPUT, false, true, true);

			if ($id_pt > 0 && $artid > 0) {
				// Ouverture du panier type sélectionné
				$pt = new paniertype();
				if ($pt->open($id_pt)) {
					$article = new article();
					if ($article->open($artid)) {
						// Ajout de l'article au panier type
						$pt->articles = $pt->getarticles();
						if (!isset($pt->articles[$article->getReference()])) {
							$pt->articles[$article->getReference()]['qte'] = 1;
							$pt->save();
						}
					}
				}
			}

			dims_redirect($_SERVER['HTTP_REFERER']);
			break;
		case 'remove_from_favorites':
			$artid = dims_load_securvalue('artid', dims_const::_DIMS_NUM_INPUT, true, false, true);

			if ($artid > 0) {
				$article = new article();
				if ($article->open($artid)) {
					// Suppression de l'article du panier type
					$pt_favoris->articles = $pt_favoris->getarticles();

					if (isset($pt_favoris->articles[$article->getReference()])) {
						unset($pt_favoris->articles[$article->getReference()]);
						$pt_favoris->save();
					}
				}
			}

			dims_redirect($_SERVER['HTTP_REFERER']);
			break;
	}
}
elseif (!empty($_SESSION['catalogue']['vrp']['id_commercial'])) {
/*	dims_print_r($_SESSION);
	ob_flush();
	die();
*/
	switch ($op) {
		case 'clients_search':
			$i = 0;
//			$search = $_POST['search'];
//			$search_sql = mysql_real_escape_string($_POST['search']);
			$search = dims_load_securvalue('search', dims_const::_DIMS_CHAR_INPUT, true, true);
			$search_sql = dims_load_securvalue('search', dims_const::_DIMS_CHAR_INPUT, true, true, true);

			if ($_SESSION['catalogue']['vrp']['tout_voir'] > 0) {
				$filtre_client = '1';
			}
			else {
				$filtre_client = 'c.atcext = '.$_SESSION['catalogue']['vrp']['id_commercial'];
			}

			$rs = $db->query('
				SELECT	c.*, u.login AS user_login, u.password AS user_password, u.date_creation
				FROM	dims_mod_cata_client c

				INNER JOIN	dims_user u
				ON			u.id = c.dims_user

				WHERE	'.$filtre_client.'
				AND	(
					c.code_client LIKE \'%'.$search_sql.'%\' OR
					c.nom LIKE \'%'.$search_sql.'%\' OR
					c.adr1 LIKE \'%'.$search_sql.'%\' OR
					c.adr2 LIKE \'%'.$search_sql.'%\' OR
					c.adr3 LIKE \'%'.$search_sql.'%\' OR
					c.cp LIKE \'%'.$search_sql.'%\' OR
					c.ville LIKE \'%'.$search_sql.'%\' OR
					c.tel1 LIKE \'%'.$search_sql.'%\' OR
					c.tel2 LIKE \'%'.$search_sql.'%\' OR
					c.fax LIKE \'%'.$search_sql.'%\' OR
					c.email LIKE \'%'.$search_sql.'%\'
				)');
			if ($db->numrows($rs)) {
				if ($_SESSION['catalogue']['vrp']['tout_voir'] == 2) {
					$smarty->assign('sw_login_passwd', 1);
				}

				while ($row = $db->fetchrow($rs)) {
					$i++;
					$adresse = '';
					if ($row['adr1'] != '') $adresse .= $row['adr1'].'<br/>';
					if ($row['adr2'] != '') $adresse .= $row['adr2'].'<br/>';
					if ($row['adr3'] != '') $adresse .= $row['adr3'].'<br/>';

					if ($row['login'] != '') {
						$connectParams = 'dims_login='.$row['user_login'].'&dims_password='.$row['user_password'].'&dims_pwdmd5=1&params='.md5($row['date_creation']);
//						$connectUrl = 'index.php?'.$connectParams;
						$connectUrl = 'index.php?dims_url='.urlencode(base64_encode($connectParams));
					}
					else {
						$connectUrl = '';
					}

					$clients[] = array(
						'code'			=> $row['code_client'],
						'nom'			=> $row['nom'],
						'adresse'		=> $adresse.$row['cp'].' '.$row['ville'],
						'tel1'			=> $row['tel1'],
						'tel2'			=> $row['tel2'],
						'fax'			=> $row['fax'],
						'email'			=> ($row['email'] != '') ? '<a href="mailto:'.$row['email'].'">'.$row['email'].'</a>' : '',
						'class'			=> 'ligne'.($i % 2),
						'login'			=> $row['login'],
						'password'		=> $row['librcha2'],
						'connectUrl'	=> ($connectUrl != '') ? '<a href="'.$connectUrl.'"><img src="./templates/frontoffice/valdenaire/gfx/loupe.png" alt="Se connecter sur le compte" /></a>' : ''
						);
					$smarty->assign('clients', $clients);
				}
			}
			break;
	}

	if (!isset($search)) $search = '';
	$smarty->assign('search', $search);
	$smarty->assign('tpl_name', 'vrp_recherche_form');
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=true;
	$page['CONTENT'] = '';
}
elseif (dims_isadmin()) {
	if ($op == 'get_frais_port_infos' || $op == 'ajouter_panierart') {
		die();
	}
}

$smarty->assign('page',$page);

if (!isset($colorfamily)) {
	$colorfamily = "#A78EB6";
	$bg_image = 'none';
	$colorfamily2 = "#805F94";
	$colorfamily3 = "#805F94";
	$colorfamily4 = "#f8edf";
	$smarty->assign('colorfamily', $colorfamily);
	$smarty->assign('colorfamily2', $colorfamily2);
	$smarty->assign('colorfamily3', $colorfamily3);
	$smarty->assign('colorfamily4', $colorfamily4);
	$smarty->assign('bg_image', $bg_image);
}

//Utilisé pour le fil d'ariane de la fiche article
if($saveOpForArti) {
	$_SESSION['catalogue']['ariane_oldop'] = $ariane_op;
	$_SESSION['catalogue']['ariane_oldparam'] = $rubriques;
	$_SESSION['catalogue']['ariane_oldmotscles'] = $motscles;
	$_SESSION['catalogue']['ariane_oldidpromo'] = isset($id_promo) ? $id_promo : '';
}
else {
	$_SESSION['catalogue']['ariane_oldop'] = '';
	$_SESSION['catalogue']['ariane_oldparam'] = '';
	$_SESSION['catalogue']['ariane_oldmotscles'] = '';
	$_SESSION['catalogue']['ariane_oldidpromo'] = '';
}

if( $_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent'] ){
	// quand on rentre dans le catalogue, on supprime la rubrique et l'article du WCE de la session
	// car ca pose problème quand on vient d'une page SSL, ca force toujours le SSL à cause de l'article en session
	if (isset($_SESSION['dims']['currentheadingid'])) unset($_SESSION['dims']['currentheadingid']);
	if (isset($_SESSION['dims']['currentarticleid'])) unset($_SESSION['dims']['currentarticleid']);
	// Aussi, on dit au TPL qu'on est dans le catalogue pour qu'il nous envoie pas dans la CMS
	$smarty->assign('into_cata', true);
	$view->assign('into_cata', true);
}

$_SESSION['dims']['catalogue_mode'] = true;
$_SESSION['dims']['tpl_page']['ARIANE'] = $smarty->getTemplateVars('ariane');
