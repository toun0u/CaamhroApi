<?php

require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_site.php';

global $recursive_mode;
$recursive_mode = array();

// Gestion version mobile
if(!isset($_SESSION['dims']['is_mobile'])){
	require_once DIMS_APP_PATH."include/Browser.php";
	$browser = new Browser();
	$_SESSION['dims']['is_mobile'] = $browser->isMobile();
}

require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_visite.php";
article_visite::updateVisite();

// initialisation du wce_site
// NB : on suppose que que le moduleid ne peux pas être égal à 1 (system)
//Cyril - 01/10/2013 --> ajout d'un contrôle sur le nom du module type parce que qu'on vient du back, plus précisément d'un module qui n'est pas le wce, bah ça ne marche pas du tout
if(!isset($_SESSION['dims']['moduleid']) || $_SESSION['dims']['moduleid'] <= 1 || $_SESSION['dims']['moduletype'] != 'wce') $_SESSION['dims']['moduleid'] = $_SESSION['dims']['wcemoduleid'];
$site = new wce_site(dims::getInstance()->getDb(),$_SESSION['dims']['moduleid']);
wce_site::setInstance($site);

$site->loadBlockModels();
if(!isset($_SESSION['dims']['homepageurl'])) $_SESSION['dims']['homepageurl']=$site->getHomePageUrl();
if(!isset($_SESSION['dims']['wce']['homePageUrl'])) $_SESSION['dims']['wce']['homePageUrl'] = $site->getHomePageUrl();
if(!isset($_SESSION['dims']['wce_default_lg'])) $_SESSION['dims']['wce_default_lg'] = $site->getDefaultLanguage();
if(!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $_SESSION['dims']['wce_default_lg'];
$wce_site = $site;

require_once DIMS_APP_PATH.'modules/wce/wiki/include/class_wce_lang.php';
$lstLang = array();
$defLang = isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])?$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']:'';
$IddefLang = isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])?$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']:0;

foreach(wce_lang::getAllFront() as $lang){
	$elem = array();
	$elem['label'] = $lang->fields['label'];
	$elem['ref'] = $lang->fields['ref'];
	$elem['ico'] = $lang->getFlag();
	$lstLang[] = $elem;
	if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']) && $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] == $lang->fields['id']){
		$defLang = $lang->fields['ref'];
		$IddefLang = $lang->fields['id'];
	}elseif ($defLang == '' && $lang->fields['default']){
		$defLang = $lang->fields['ref'];
		$IddefLang = $lang->fields['id'];
	}
}
$smarty->assign('languages',$lstLang);

//on recupere la langue par defaut de l'article
$lang = dims_load_securvalue('lang',dims_const::_DIMS_CHAR_INPUT,true,true,true);
if(isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']))
	$oldLG = $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'];
else
	$oldLG = 0;
if (is_numeric($lang) || empty($lang)){
	if (!empty($lang) && $lang > 0){
		$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']=$lang;
	}elseif(!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])){
		$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $_SESSION['dims']['wce_default_lg'];
	}
}else{
	$objLang = wce_lang::getLangFromRef($lang);
	if (!$objLang->isNew() && $objLang->fields['is_active']){
		$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $objLang->fields['id'];
	}elseif(!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])){
		$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $_SESSION['dims']['wce_default_lg'];
	}
}
if($oldLG != $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])
	unset($_SESSION['dims']['URLREWRITE']);

if(!isset($_SESSION['dims']['wce_default_lg']) || (isset($_SESSION['dims']['wce_default_lg']) && ($_SESSION['dims']['wce_default_lg'] == '' || $_SESSION['dims']['wce_default_lg'] <= 0)))
	$_SESSION['dims']['wce_default_lg'] = $IddefLang;
if(!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']) || (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']) && ($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] == '' || $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] <= 0)))
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $_SESSION['dims']['wce_default_lg'];

global $wce_mode;
$wce_mode = 'render';
global $adminedit;
$adminedit = 0;
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article_tags.php';
$lstTags = array();
$langTag = dims_load_securvalue("lang",dims_const::_DIMS_CHAR_INPUT,true,true,false,$defLang,$defLang);
foreach(article_tags::getTags(15) as $tag){
	$elem = array();
	if (isset($tag->fields['tag_'.$langTag]) && trim($tag->fields['tag_'.$langTag]) != '')
		$elem['label'] = $tag->fields['tag_'.$langTag];
	else
		$elem['label'] = $tag->fields['tag'];
	$elem['id'] = $tag->fields['id'];
		$elem['indice'] = $tag->fields['indice'];
	$lstTags[strtolower($tag->fields['tag'])] = $elem;
}

ksort($lstTags);
$smarty->assign('articleTags',$lstTags);

if (file_exists(DIMS_APP_PATH.'/modules/catalogue/display.php') && dims::getInstance()->isModuleTypeEnabled('catalogue')) {
	$tmpCataMod = $_SESSION['dims']['catalogue_mode'];
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['display_dyncontent']=false;
	include_once DIMS_APP_PATH.'/modules/catalogue/display.php';
	$_SESSION['dims']['catalogue_mode'] = $tmpCataMod;
}

// Send user login informations to template.
if ($_SESSION['dims']['connected']) {
	$smarty->assign('switch_user_logged_in','');
	$tpl_user=array(
		'LOGIN'				=> $_SESSION['dims']['login'],
		'FIRSTNAME'			=> $_SESSION['dims']['user']['firstname'],
		'LASTNAME'			=> $_SESSION['dims']['user']['lastname'],
		'EMAIL'				=> $_SESSION['dims']['user']['email'],
		'SHOWPROFILE'		=> dims_urlencode($dims->getRootPath().'/index.php?modcontent='.dims_const::_DIMS_MODULE_SYSTEM.'&op=showprofile'),
		'SHOWTICKETS'		=> dims_urlencode($dims->getRootPath().'/index.php?modcontent='.dims_const::_DIMS_MODULE_SYSTEM.'&op=showtickets'),
		'SHOWFAVORITES'		=> dims_urlencode($dims->getRootPath().'/index.php?modcontent='.dims_const::_DIMS_MODULE_SYSTEM.'&op=showfavorites'),
		'ADMINISTRATION'	=> dims_urlencode($dims->getRootPath().'/admin.php?dims_mainmenu=0&dims_desktop=portal'.''),
		'DECONNECT'			=> dims_urlencode($dims->getRootPath().'/index.php?dims_logout=1'));

	$smarty->assign('user',$tpl_user);
} else {
	$smarty->assign('switch_user_logged_out','');
}

$object = dims_load_securvalue('object',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($object){
	case 'appointment': // <domain>/appointment/<md5>(?param=)
		$md5 = dims_load_securvalue('md5',dims_const::_DIMS_CHAR_INPUT,true,true);
		if ($md5 != ''){
			require_once DIMS_APP_PATH.'modules/system/appointment_offer/class_appointment_offer.php';
			if(($obj = dims_appointment_offer::openByRef($md5)) !== false){
				$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
				switch($action){
					case 'save':
						$reponses = array();
						if (isset($_POST['lstReponses'])){
							$reponses = dims_load_securvalue('lstReponses', dims_const::_DIMS_CHAR_INPUT, true, true, true);
						}
						$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
						$user = dims_load_securvalue('user',dims_const::_DIMS_CHAR_INPUT,true,true,true);
						$obj->saveReponses($name,$reponses,$user);
						$_SESSION['dims']['appointment']['reponse'] = true;
						dims_redirect($obj->getFrontUrl("&ct=".$user));
						break;
				}
				require_once DIMS_APP_PATH.'modules/wce/display_appointment.tpl.php';
			} else {
				dims_redirect("/".dims::getInstance()->getScriptEnv());
			}
		} else {
			dims_redirect("/".dims::getInstance()->getScriptEnv());
		}
		break;
	case 'invitation':
		$md5 = dims_load_securvalue('md5',dims_const::_DIMS_CHAR_INPUT,true,true);
		$id = dims_load_securvalue('id',dims_const::_DIMS_CHAR_INPUT,true,true);
		if ($md5 != '' && $id != '' && file_exists(DIMS_APP_PATH.'modules/invitation/include/class_invitation.php')){
			require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation.php';
			$obj = invitation::openByRef($md5);
			$contact = contact::find_by(array('ref'=>$id),null,1);
			if(!empty($obj) && !empty($contact)){
				$lstCt = $obj->getCtLinks();
				if(isset($lstCt[$contact->get('id')]) || (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])){
					$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
					switch($action){
						case 'save':
							$date = dims_load_securvalue('date',dims_const::_DIMS_NUM_INPUT,true,true);
							$child = invitation::find_by(array('type'=>dims_const::_PLANNING_ACTION_INVITATION, 'id_parent'=>$obj->get('id'), 'id_globalobject'=>$date),null,1);
							if(!empty($child)){
								require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse.php';
								require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_reponse_value.php';
								$rep = invitation_reponse::find_by(array('go_appointment'=>$obj->get('id_globalobject'),'id_contact'=>$contact->get('id')),null,1);
								if(empty($rep)){
									$rep = new invitation_reponse();
									$rep->init_description();
									$rep->set('go_appointment',$obj->get('id_globalobject'));
									$rep->set('id_contact',$contact->get('id'));
									$rep->set('name',$contact->get('firstname')." ".$contact->get('lastname'));
									$rep->save();
								}
								$val = invitation_reponse_val::find_by(array('id_reponse'=>$rep->get('id')),null,1);
								if(empty($val)){
									$val = new invitation_reponse_val();
									$val->init_description();
									$val->set('id_reponse',$rep->get('id'));
									$val->set('presence',1);
								}
								$val->set('id_appointment',$child->get('id'));
								$val->save();

								require_once DIMS_APP_PATH.'modules/invitation/include/class_invitation_accompany.php';
								$accomp = invitation_accompany::find_by(array('id_action'=>$obj->get('id'),'id_reponse'=>$rep->get('id')));
								if($obj->get('max_allowed') > 0){
									while(count($accomp) > $obj->get('max_allowed')){
										$a = array_pop($accomp);
										$a->delete();
									}
									$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true);
									$age = dims_load_securvalue('age',dims_const::_DIMS_CHAR_INPUT,true,true);
									$a = current($accomp);
									$nbForm = 0;
									for($i=0;$i<$obj->get('max_allowed');$i++){
										if(isset($name[$i]) && isset($age[$i]) && trim($name[$i]) != ''){
											if($a === false){
												$a = new invitation_accompany();
												$a->init_description();
												$a->set('id_action',$obj->get('id'));
												$a->set('id_reponse',$rep->get('id'));
												$a->set('id_action_child',$child->get('id'));
												$a->set('name',trim($name[$i]));
												$a->set('age',trim($age[$i]));
												$a->save();
											}else{
												$a->set('id_action',$obj->get('id'));
												$a->set('id_reponse',$rep->get('id'));
												$a->set('id_action_child',$child->get('id'));
												$a->set('name',trim($name[$i]));
												$a->set('age',trim($age[$i]));
												$a->save();
											}
											$nbForm++;
											$a = next($accomp);
										}
									}
									while(count($accomp) > $nbForm){
										$a = array_pop($accomp);
										$a->delete();
									}

								}else{
									foreach($accomp as $a){
										$a->delete();
									}
								}
							}
							dims_redirect($obj->getFrontUrl("&id=".$contact->get('ref')."&save=1"));
							break;
					}
					require_once DIMS_APP_PATH.'modules/wce/display_invitation.tpl.php';
				} else {
					dims_redirect("/".dims::getInstance()->getScriptEnv());
				}
			} else {
				dims_redirect("/".dims::getInstance()->getScriptEnv());
			}
		} else {
			dims_redirect("/".dims::getInstance()->getScriptEnv());
		}
		break;
	default:
		$query_string=trim(dims_load_securvalue("query_string",dims_const::_DIMS_CHAR_INPUT,true,true,false));
		if ($query_string != ''){
			$_SESSION['dims']['wce']['prev_search'] = $query_string;
			$uri = str_replace('&action=search', '', $_SERVER['REQUEST_URI']);
			$uri = preg_replace('/[&?]query_string=.*(&|$)/U', "$1", $uri);
			$uri = preg_replace('/(\.html|\.php)&/',"$1?",$uri);
			dims_redirect($uri."&action=search");
		} elseif(isset($_SESSION['dims']['wce']['prev_search']) && !isset($_POST['query_string']) && dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true) == 'search'){
			$query_string = $_SESSION['dims']['wce']['prev_search'];
		}
		$tag = trim(dims_load_securvalue("tag",dims_const::_DIMS_NUM_INPUT,true,true,false));
		if ($query_string != '' || ($tag != '' && $tag > 0)) {// recherche
			require_once DIMS_APP_PATH.'modules/wce/display_search.php';
		} else {
			$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true);
			$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true);

			require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article.php';
			require_once DIMS_APP_PATH.'modules/wce/include/classes/class_heading.php';

			$article = new wce_article();

			if ($articleid==0 && isset($_SESSION['dims']['currentarticleid']) && $_SESSION['dims']['currentarticleid']>0 && empty($headingid)) {
				$articleid=$_SESSION['dims']['currentarticleid'];
			}

			if($articleid != '' && $articleid > 0){
				require_once DIMS_APP_PATH.'modules/wce/wiki/include/class_module_wiki.php';

				$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
				if (!isset($article->fields['id_module'])){
					//http_response_code(404); php >= 5.4
					dims_404();
				}else{

					$head = module_wiki::getRootHeadingFront();
					if(!is_null($head)){
						if($article->fields['id_heading'] == $head->fields['id']){
							$user=new user();
							$user->open($article->fields['id_user']);
							$contactadd = new contact();
							$contactadd->init_description();
							$contactadd->open($user->fields['id_contact']);
							$dd = dims_timestamp2local($article->fields['timestp_modify']);
							include module_wiki::getTemplatePath('/article/display_wiki.php');
						}else{
							$article->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
						}
					}else{
						//$FilArianne = $article->contructAriane();
						$article->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
					}
				}
			}else{
				if ($headingid != '' && $headingid > 0){
					$heading = new wce_heading();
					$heading->open($headingid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
					$articleid = $heading->getRedirectArticle();
					if(is_numeric($articleid)){
						if ($articleid != '' && $articleid > 0){
							$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
							$article->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
						}else{
							$lstHeadings = $heading->getAllRubriques();
							$articleid=0;
							if (sizeof($lstHeadings)>0) {
								$heading = current($lstHeadings);
								$articleid = $heading->getRedirectArticle();
							}

							if ($articleid != '' && $articleid > 0){
								$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
								$article->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
							}else
								dims_redirect(dims::getInstance()->getScriptEnv());
						}
					}else{
						dims_redirect($articleid);
					}
				}else{
					$select = " SELECT		DISTINCT wd.id_workspace,d.id, d.id_home_wce_article, d.id_post_connexion_wce_article
								FROM		dims_workspace_domain as wd
								INNER JOIN	dims_domain as d
								ON			d.id=wd.id_domain
								AND			(d.domain = :domain OR d.domain = '*')
								AND			(wd.access=1 or wd.access=2)";
					$params = array();
					$params[':domain'] = array('value'=>$_SERVER['HTTP_HOST'],'type'=>PDO::PARAM_STR);
					$res=dims::getInstance()->db->query($select,$params);

					if ($fields = dims::getInstance()->db->fetchrow($res)) {

						if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']
							&& isset($_SESSION['dims']['from_connexion_user']) && $_SESSION['dims']['from_connexion_user']) {

							$_SESSION['dims']['from_connexion_user']=false;

							unset($_SESSION['dims']['from_connexion_user']);
							$_SESSION['dims']['currentdomain_id']=$fields['id'];


							if ($fields['id_post_connexion_wce_article'] != '' && $fields['id_post_connexion_wce_article'] > 0){
								//$article->open($fields['id_post_connexion_wce_article']);
								//$article->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
								$_SESSION['dims']['currentarticleid'] = $fields['id_post_connexion_wce_article'];
								if (isset($_SERVER['QUERY_STRING'])){
									$query = preg_replace('/urlrewrite=.*[^&]pathrewrite=.*[^&]?/','',$_SERVER['QUERY_STRING']);
									dims_redirect("/index.php?articleid=".$fields['id_post_connexion_wce_article']."&".$query);
								} else {
									dims_redirect("/index.php?articleid=".$fields['id_post_connexion_wce_article']);
								}
							}
						}
						// traitement de la home page
						if ($fields['id_home_wce_article'] != '' && $fields['id_home_wce_article'] > 0){
							$article->open($fields['id_home_wce_article'],$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
							$article->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
						}else{
							$lstHeadings = wce_heading::getAllHeadings();
							$heading = current($lstHeadings);
							dims_redirect(dims::getInstance()->getScriptEnv()."?headingid=".$heading->fields['id']);
						}

					}
				}
			}
		}
		break;
}

?>
