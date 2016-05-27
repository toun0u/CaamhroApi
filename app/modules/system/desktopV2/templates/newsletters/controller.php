<?php
require_once DIMS_APP_PATH.'modules/system/class_newsletter.php';
require_once DIMS_APP_PATH.'modules/system/class_news_article.php';
require_once DIMS_APP_PATH.'modules/system/class_newsletter_inscription.php';
require_once DIMS_APP_PATH.'modules/system/class_news_subscribed.php';
require_once DIMS_APP_PATH.'modules/system/class_mailing.php';
require_once DIMS_APP_PATH.'modules/system/class_mailing-news.php';
require_once DIMS_APP_PATH.'modules/system/class_mailing-ct.php';
require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
require_once DIMS_APP_PATH.'include/functions/mail.php';

if(!isset($_SESSION['dims']['current_newsletter'])) $_SESSION['dims']['current_newsletter'] = 0;
if (!isset($_SESSION['dims']['default_newsletter'])) $_SESSION['dims']['default_newsletter']=0;

$planning_mois[1]=$_DIMS['cste']['_JANUARY'];
$planning_mois[2]=$_DIMS['cste']['_FEBRUARY'];
$planning_mois[3]=$_DIMS['cste']['_MARCH'];
$planning_mois[4]=$_DIMS['cste']['_APRIL'];
$planning_mois[5]=$_DIMS['cste']['_MAY'];
$planning_mois[6]=$_DIMS['cste']['_JUNE'];
$planning_mois[7]=$_DIMS['cste']['_JULY'];
$planning_mois[8]=$_DIMS['cste']['_AUGUST'];
$planning_mois[9]=$_DIMS['cste']['_SEPTEMBER'];
$planning_mois[10]=$_DIMS['cste']['_OCTOBER'];
$planning_mois[11]=$_DIMS['cste']['_NOVEMBER'];
$planning_mois[12]=$_DIMS['cste']['_DECEMBER'];

$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
$subaction = dims_load_securvalue('subaction',dims_const::_DIMS_CHAR_INPUT,true,true);
$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['dims']['current_newsletter'], $_SESSION['dims']['default_newsletter']);

$listworkspace_nl = '0';

$sql_in = "	SELECT	id_to
			FROM	dims_workspace_share
			WHERE	id_from = :idworkspacefrom
			AND		active = 1
			AND		id_object = :idobject ";
$res_in = $db->query($sql_in, array(
	':idworkspacefrom' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
	':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_NEWSLETTER),
));

if($db->numrows($res_in) >= 1) {
	while($tabw = $db->fetchrow($res_in)) {
		$listworkspace_nl .= ", ".$tabw['id_to'];
	}
	$listworkspace_nl .= ", ".$_SESSION['dims']['workspaceid']; //on ajoute le workspace courant sinon il sera exclu des recherches
}
else {
	$listworkspace_nl = $_SESSION['dims']['workspaceid'];
}
switch($op) {
	case 'wiki':
		dims_init_module('wce');
		require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_article.php';
		require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_heading.php';
		require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_block.php');
		require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_block_model.php');
		require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_site.php');
		require_once DIMS_APP_PATH."modules/wce/wiki/include/global.php";

		$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true, false, $_SESSION['dims']['wiki']['article']['action'], module_wiki::_ACTION_SHOW_ARTICLE);

		if ($action==module_wiki::_ACTION_SHOW_ARTICLE) {
			$_GET['action']=module_wiki::_ACTION_SHOW_ARTICLE_NEWSLETTER;
			require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_add_article.tpl.php';
		}
		require_once module_wiki::getTemplatePath('/article/controller.php');
		break;
	default:
		if( !isset ( $_SESSION['dims']['newsletters']['op'] ) ) $_SESSION['dims']['newsletters']['op'] = dims_const_desktopv2::_NEWSLETTERS_DESKTOP;
		$newsletter_op = dims_load_securvalue('news_op',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['newsletters']['op'],$_SESSION['dims']['newsletters']['op']);
		//die($newsletter_op);
		switch($newsletter_op){
			case dims_const_desktopv2::_NEWSLETTER_GET_EMAILS:
					ob_clean();

					$id_news = dims_load_securvalue('id_news', dims_const::_DIMS_NUM_INPUT, true, true);

					//on recherche tous les contacts a qui envoyer la news
					//avec 1/ les emails qui viennent du net : table "newsletter_subscribed"
					// 2/ les emails issus de la table "mailing_ct"
					// 3/ cas provisoire (du fait de l'ajout des layers) : on prend les email directement dans la fiche contact

					//1
					$tabemail=array();
					$doublons=array();
					$sql_sub = 'SELECT	  	DISTINCT cl.email, cl.id, u.login
								FROM		dims_mod_business_contact ct
								INNER JOIN  dims_mod_newsletter_subscribed sub
								ON		  	sub.id_contact = ct.id
								AND		 	sub.id_newsletter = :idnewsletter
								INNER JOIN 	dims_mod_business_contact_layer cl
								ON 			cl.id = ct.id
								AND 		cl.type_layer = 1
								LEFT JOIN 	dims_user u
								ON 			u.id_contact = ct.id';

					$params[':idnewsletter'] = array('type' => PDO::PARAM_INT, 'value' => $id_news);

					$res_sub = $db->query($sql_sub, $params);
					$res_sub = $db->query($sql_sub);

					$list_ct="0";

					if($db->numrows($res_sub)) {
						while($tab_sub = $db->fetchrow($res_sub)) {
							if($tab_sub['email'] != '') {
								$nom_compare=strtolower($tab_sub['email']);
								if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
									if($tab_sub['login'] == '') {
										//si le contact n'est pas un user il doit pouvoir se désinscrire via l'email
										$list_email['contact'][$tab_sub['id']] = $tab_sub['email'];
									}
									else {
										$list_email[$tab_sub['id']] = $tab_sub['email'];
									}
									$list_ct .= ' ,'.$tab_sub['id'];

									$tabemail[$nom_compare]=$nom_compare;
								}
								else {
									if (!isset($doublons[$tab_sub['email']])) $doublons[$tab_sub['email']]=1;
									$doublons[$tab_sub['email']]++;
								}
							}
						}
					}

					//2
					$sql_mail = 'SELECT	 	ct.email, ct.id
								FROM		dims_mod_newsletter_mailing_ct ct
								INNER JOIN  dims_mod_newsletter_mailing_news mn
								ON		  	mn.id_mailing = ct.id_mailing
								AND		 	mn.id_newsletter = '.$id_news.'
								WHERE 		ct.actif = 1';
					$res_mail = $db->query($sql_mail);

					if($db->numrows($res_mail)) {
						while($tab_mail = $db->fetchrow($res_mail)) {
							$nom_compare=strtolower($tab_mail['email']);
							if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
								$list_email['mailing'][$tab_mail['id']] = $tab_mail['email'];
								$tabemail[$nom_compare]=$nom_compare;
							}
							else {
								if (!isset($doublons[$tab_mail['email']])) $doublons[$tab_mail['email']]=1;
								$doublons[$tab_mail['email']]++;
							}
						}
					}

					//3
					$sql_sub = 'SELECT	  	DISTINCT ct.email, ct.id
								FROM		dims_mod_business_contact ct
								INNER JOIN 	dims_mod_newsletter_subscribed sub
								ON		  	sub.id_contact = ct.id
								AND		 	sub.id_newsletter = '.$id_news.'
								AND 		ct.id NOT IN ('.$list_ct.')';

					$res_sub = $db->query($sql_sub);

					if($db->numrows($res_sub)) {
						while($tab_sub = $db->fetchrow($res_sub)) {
							$nom_compare=strtolower($tab_sub['email']);
							if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
								$list_email[$tab_sub['id']] = $tab_sub['email'];
								$tabemail[$nom_compare]=$nom_compare;
							}
							else {
								if (!isset($doublons[$tab_sub['email']])) $doublons[$tab_sub['email']]=1;
								$doublons[$tab_sub['email']]++;
							}
						}
					}

					//on reprend les infos a envoyer
					$inf_news = new newsletter();
					$inf_news->open($id_news);
					$listAttachTags=$inf_news->attachContactsByTag();

					$listalltags=array();
					if (!empty($listAttachTags)) {
						foreach ($listAttachTags as $id =>$ct) {
							$email='';
							if ($ct['email']<>'')
								$email= $ct['email'];
							elseif ($ct['emai2']<>'')
								$email= $ct['email2'];
							if ($email!='' && !isset($listalltags[$email]) && !isset($tabemail[$email])) {
								$tabemail[$email]=$email;

								$listalltags[$email]=$email;
							}
						}
					}

					// on a la liste on va l'ecrire maintenant
					$nomfile=realpath('.').'/tmp/newsletter_'.rand(1,10000000000).".txt";
					file_put_contents($nomfile, implode($tabemail,"\n"));
					dims_downloadfile($nomfile, $inf_news->fields['label']."_export.csv");
					die();
					break;

				case dims_const_desktopv2::_NEWSLETTER_TAG_DELETE:
					// traitement de l'association
					$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_NUM_INPUT,true,true);
					$id_attach = dims_load_securvalue('id_attach',dims_const::_DIMS_NUM_INPUT,true,true);

					require_once DIMS_APP_PATH . '/modules/system/desktopV2/templates/newsletters/record/class_newsletter_tag.php';
					//die($id_news." ".$add_tag);
					if ($id_news>0 && $id_attach>0) {

						$nt= new newsletter_tag();

						$nt->open($id_attach);

						if ($nt->fields['id_newsletter']==$id_news) {
							$nt->delete();
						}
					}

					dims_redirect("/admin.php?news_op=".dims_const_desktopv2::_FICHE_NEWSLETTER."&new_record_op=".dims_const_desktopv2::_NEWS_TAGS."&id_news=".$id_news);
					break;

				case dims_const_desktopv2::_NEWS_SAVE_TAG:
					// traitement de l'association
					$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_NUM_INPUT,true,true);
					$add_tag = dims_load_securvalue('add_tag',dims_const::_DIMS_NUM_INPUT,true,true);

					require_once DIMS_APP_PATH . '/modules/system/desktopV2/templates/newsletters/record/class_newsletter_tag.php';
					//die($id_news." ".$add_tag);
					if ($id_news>0 && $add_tag>0) {

						$nt= new newsletter_tag();

						$nt->fields['id_newsletter']=$id_news;
						$nt->fields['id_tag']=$add_tag;
						$nt->save();

						// on recherche tous les contacts qui sont concernes par le tag
						$news = new newsletter();
						$news->open($id_news);
					}

					dims_redirect("/admin.php?news_op=".dims_const_desktopv2::_FICHE_NEWSLETTER."&new_record_op=".dims_const_desktopv2::_NEWS_TAGS."&id_news=".$id_news);
					break;
				case dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST:
					$id_mail = dims_load_securvalue('id_mail',dims_const::_DIMS_NUM_INPUT,true,true);
					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_import_email.php';

					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_add_mailinglist.php';
					break;
				default:
				case dims_const_desktopv2::_NEWSLETTERS_DESKTOP:
					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletters.tpl.php';
					break;
				case dims_const_desktopv2::_FICHE_NEWSLETTER:
					//CYRIL - TODO - tant que c'est statique ça passe, après faudra récupérer l'id ou le globalobject_id de la newsletter à ouvrir et éventuellement
					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/record/controller.php';
					break;
				case dims_const_desktopv2::_NEWS_DELETE_NEWSLETTER:
					$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_NUM_INPUT,true,true);
					$news = new newsletter();
					$news->open($id_news);

					if ($news->getNbInscription==0) $news->delete();
					else {
							$news->fields['etat'] = 0;
							$news->save();
					}

					$_SESSION['dims']['default_newsletter'] = 0;

					$redirect="/admin.php?news_op=".dims_const_desktopv2::_NEWSLETTERS_DESKTOP;
					dims_redirect($redirect);
					break;
				case dims_const_desktopv2::_NEWS_ADD_NEWSLETTER_MODEL:
					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_add_form.tpl.php';
					break;

				case dims_const_desktopv2::_NEWS_SAVE_NEWSLETTER_MODEL:
					$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
					//si on a pas d'id_news on est dans le cas d'un ajout
					//si on a l'id_news, on est dans le cas d'une modif
					if(isset($id_news) && $id_news != '') {
						$news = new newsletter();
						$news->open($id_news);
					}
					else {
						$news = new newsletter();
						$news->init_description();
					}

					$news->setvalues($_POST, 'news_');
					$news->fields['descriptif'] = preg_replace('/<span.*>(\[\[ [0-9]+,[0-9]+(,[0-9]+)?\/.+ &gt; .+( &gt; .+)? \]\])<\/span>/','${1}',$news->fields['descriptif']);
					$id_curr_news = $news->save();

					 $_SESSION['dims']['current_newsletter'] = $id_curr_news;

					$redirect="/admin.php?news_op=".dims_const_desktopv2::_FICHE_NEWSLETTER."&id_news=".$id_curr_news;
					dims_redirect($redirect);
					break;
				case dims_const_desktopv2::_NEWSLETTER_ARTICLE_EDIT:
					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_add_article.tpl.php';
					break;

				case dims_const_desktopv2::_NEWSLETTER_ARTICLE_SAVE:
					include_once DIMS_APP_PATH . 'modules/wce/include/classes/class_article.php';
					$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
					$id_env = dims_load_securvalue('id_env',dims_const::_DIMS_CHAR_INPUT,true,true);

					if(isset($id_env) && $id_env != '') {
						$env = new news_article();
						$env->open($id_env);
					}
					else {
						$env = new news_article();
						$env->init_description();
					}

					$env->setvalues($_POST, 'env_');
					$env->fields['content'] = preg_replace('/<span.*>(\[\[ [0-9]+,[0-9]+(,[0-9]+)?\/.+ &gt; .+( &gt; .+)? \]\])<\/span>/','${1}',$env->fields['content']);

					$env->fields['id_newsletter'] = $id_news;

					$env->save();

					if(!empty($env->fields['id_article'])) {
						$article = new wce_article();
						$article->open($env->fields['id_article'], $env->fields['id_lang']);
						$article->publish($env->fields['id_lang']);
						$article->valide($env->fields['id_lang']);
						$article->save();
					}

					if ($id_env=='') $id_env=$env->fields['id'];

					$redirect = $scriptenv.'?news_op='.dims_const_desktopv2::_NEWSLETTER_ARTICLE_EDIT."&id_news=".$id_news."&id_env=".$id_env;
					dims_redirect($redirect);
					break;
				case dims_const_desktopv2::_NEWSLETTER_ARTICLE_NEW:
					// Reset session memory.
					$_SESSION['dims']['NEWSLETTER']['id_env'] = '';
					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_add_article.tpl.php';
					break;

				case dims_const_desktopv2::_NEWSLETTER_ARTICLE_DELETE:
					$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
					$id_env = dims_load_securvalue('id_env',dims_const::_DIMS_CHAR_INPUT,true,true);

					$env = new news_article();
					$env->open($id_env);

					$env->delete();

					//$redirect='admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news;
					$redirect = $scriptenv.'?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER;
					dims_redirect($redirect);

					break;
				case dims_const_desktopv2::_NEWSLETTER_TEST_SENDING:

					$id_news = dims_load_securvalue('id_news', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);

					//on ouvre le layer correspondant
					$ct_l = new contact();
					$ct_l->open($_SESSION['dims']['user']['id_contact']);

					//on reprend les infos a envoyer
					$inf_news = new newsletter();
					$inf_news->open($id_news);
					//ici le sujet et le contenu
					$tab_inf = $inf_news->getContent($id_env);
					require_once DIMS_APP_PATH.'include/functions/files.php';
					//maintenant la piece jointe
					$tab_pj = dims_getFiles($dims,$_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_NEWSLETTER,$id_env);

					//on a besoin du path exact pour le fichier

					//on recherche le nom de domaine pour la desinscription
					$sql_r = "	SELECT d.domain
								FROM dims_domain d
								INNER JOIN dims_workspace w
								ON w.newsletter_id_domain = d.id
								AND w.id = :idworkspace";

					$res_r = $db->query($sql_r, array(
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
					));
					$dom = $db->fetchrow($res_r);
					if($dom['domain']=='*') $dom['domain'] = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

					//on fait l'envoi
					$workspace = new workspace();
					$workspace->open($_SESSION['dims']['workspaceid']);
					$email = $workspace->fields['newsletter_sender_email'];
					if ($email=="") $email=_DIMS_ADMINMAIL;

					// on recupere le @ et on prend le reste
					$pos=strpos($email,"@");
					if ($pos>0) $name=substr($email,$pos+1);
					else $name=$email;
					$from[0]['name']   = $name;//$email;
					$from[0]['address']= $email;

					$subject = 'TEST : '.$tab_inf['label'];

					$file = array();

					if (isset($tab_pj) && !empty($tab_pj)) {
						foreach ($tab_pj as $i => $fi) {
							$file[$i]['name'] = $tab_pj[$i]['name'];
							$doc = new docfile();
							$doc->open($tab_pj[$i]['id']);
							$path = $doc->getfilepath();
							$file[$i]['filename'] = $path;
							//le mime-type
							$file[$i]['mime-type'] = mime_content_type($path);
						}
					}

					$to[0]['name'] = '--';
					if ($ct_l->fields['email']!="") {
						$to[0]['address'] = $ct_l->fields['email'];
					}
					else {
						$to[0]['address'] = $_SESSION['dims']['user']['email'];
					}

					$template_name=$tab_inf['template'];
					$ch = DIMS_APP_PATH . "/templates/frontoffice/".$template_name;
					/*if (substr($ch, -1)=="/")
						$ch=  DIMS_APP_PATH . "/templates/frontoffice/".$template_name;
					else
						$ch=  DIMS_APP_PATH . "templates/frontoffice/".$template_name;*/


					// create online version
					$path_newsletter=realpath('.').'/data/newsletter/online/';
					if(!file_exists($path_newsletter)) mkdir($path_newsletter);

					$namefile_html=strtolower(date('F'))."-".date('Y')."-".$id_env.".html";
					$namefile_html_to_pdf=strtolower(date('F'))."-".date('Y')."-".$id_env."-pdf.html";
					$namefile_pdf=strtolower(date('F'))."-".date('Y')."-".$id_env.".pdf";
					$online='/data/newsletter/online/'.$namefile_html;
					$online_to_pdf='/data/newsletter/online/'.$namefile_html_to_pdf;
					$onlinepdf='/data/newsletter/online/'.$namefile_pdf;

					if (isset($tab_inf['template']) && $tab_inf['template']!='' && is_dir($ch)) {
						if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
							$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
						}

						$smartypath=$_SESSION['dims']['smarty_path'];

						$smartyobject = new Smarty();
						$smartyobject->cache_dir = $smartypath.'/cache';
						$smartyobject->config_dir = $smartypath.'/configs';
						$smartyobject->template_dir = $ch;


						if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
						$smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";

						require_once DIMS_APP_PATH . '/modules/system/include/functions.php';

						ob_end_clean();
						ob_start();

						$link_unsubscribe=dims_urlencode($dims->getProtocol().$dom['domain'].'/index.php?op=newsletter&action=news_unsubscribe&id_news='.$id_news.'&id_contact='.$ct_l->fields['id'], false);

						// on remplace les contenus dynamiques
						require_once DIMS_APP_PATH .'/modules/wce/include/global.php';
						require_once DIMS_APP_PATH .'/modules/wce/include/classes/class_wce_site.php';

						// From gescom i.e : backoffice, need to find frontoffice
						$wceModule = current($dims->getModuleByType('wce'));
						$wce_site = new wce_site($db, $wceModule['instanceid']);
						// on calcul la langue par defaut

						$id_lang=$wce_site->getDefaultLanguage();
						$_SESSION['dims']['wce_default_lg']=$id_lang;
						$_SESSION['wce'][$wceModule['instanceid']]['id_lang']=$id_lang;

						$tab_inf['content'] = preg_replace_callback('/\[\[(.*)\]\]/i','wce_getobjectcontent',$tab_inf['content']);

						// on va allouer les variables comme il faut
						$smartyobject->assign('newsletter',array(
								'TITLE' => $tab_inf['label'],
								'MONTH' => $planning_mois[date('n')],
								'YEAR' => date('Y'),
								'TEMPLATE_ROOT_PATH' => $dims->getProtocol().$dom['domain']."/templates/frontoffice/".$template_name,
								'DATA_PATH' => "/data", //$dims->getProtocol().$dom['domain']."/data",
								'DEAR_FIRSTNAME' => "Dear ".$ct_l->fields['firstname'],
								'FIRSTNAME' => $ct_l->fields['firstname'],
								'UNSUBSCRIBELINK' => $link_unsubscribe,
								'LASTNAME' => $ct_l->fields['lastname'],
								'ONLINE_LINK' =>  $dims->getProtocol().$dom['domain'].$online,
								'ONLINE_PDF' =>  $dims->getProtocol().$dom['domain'].$onlinepdf,
								'CONTENT' => $tab_inf['content']
							)
						);

						$smartyobject->display('index.tpl');
						$message =	ob_get_contents();
						ob_end_clean();

						//////////////////////////////////////////////////////////////////
						// on fait la version pour le PDF
						ob_start();
						$message_to_pdf="";
						$root_path=realpath(".");

						// on va allouer les variables comme il faut
						$smartyobject->assign('newsletter',array(
							'TITLE' => $tab_inf['label'],
							'MONTH' => $planning_mois[date('n')],
							'YEAR' => date('Y'),
							'TEMPLATE_ROOT_PATH' => DIMS_APP_PATH."templates/frontoffice/".$template_name,
							'DATA_PATH' => "/data", //$root_path."/data",
							'DEAR_FIRSTNAME' => "",
							'FIRSTNAME' => "",
							'UNSUBSCRIBELINK',"",
							'LASTNAME' => "",
							'ONLINE_LINK' =>  $dims->getProtocol().$dom['domain'].$online,
							'ONLINE_PDF' =>  $dims->getProtocol().$dom['domain'].$onlinepdf,
							'CONTENT' => $tab_inf['content']
							)
						);

						$smartyobject->display('index.tpl');
						$message_to_pdf =  ob_get_contents();
						ob_end_clean();

						// on va traiter aussi les data ecrits dans le contenu
						$rep_from[]= "'./index.php";
						$rep_from[]= "'/index.php";
						$rep_from[]= "'./index-quick.php";
						$rep_from[]= "'./data/";
						$rep_from[]= "\"/templates";
						$rep_from[]= "'/templates";
						$rep_from[]= "&quot;/templates";
						$rep_from[]= '"./index.php';
						$rep_from[]= '"/index.php';
						$rep_from[]= '"./index-quick.php';
						$rep_from[]= '"./data/';
						$rep_from[]= '"/data/';

						$rep_to[]="'".$root_path."/index.php";
						$rep_to[]="'".$root_path."/index.php";
						$rep_to[]="'".$root_path."/index-quick.php";
						$rep_to[]="'".$root_path."/data/";
						$rep_to[]= "\"".$root_path."/templates";
						$rep_to[]= "'".$root_path."/templates";
						$rep_to[]= "&quot;".$root_path."/templates";
						$rep_to[]='"'.$root_path."/index.php";
						$rep_to[]='"'.$root_path."/index.php";
						$rep_to[]='"'.$root_path."/index-quick.php";
						$rep_to[]='"'.$root_path."/data/";
						$rep_to[]='"'.$root_path."/data/";

						$message_to_pdf= str_replace($rep_from,$rep_to,$message_to_pdf);
					}
					else {

						$message = '<html>
									<head></head>
									<body>'.$tab_inf['content'].'</body>
								</html>';

						$message_to_pdf=$message;
					}

					// on génère les contenus et PDF
					//$onlinefile=realpath(".").$online;
					//file_put_contents($onlinefile, $message);

					// on génère les contenus et PDF
					$onlinefile=realpath(".").$online;
					$rep_from=array();
					$rep_to=array();
					$rep_from[]= "'./data/";
					$rep_from[]= '"./data/';
					$rep_to[]="'/data/";
					$rep_to[]= '"/data/';
					$messageonline=str_replace($rep_from,$rep_to,$message);
					file_put_contents($onlinefile, $messageonline);

					$onlinefilepdf=realpath(".").$online_to_pdf;
					file_put_contents($onlinefilepdf, $message_to_pdf);

					// on convertit vers le pdf
					$pathscript=realpath(".")."/scripts/";
					$currentpath=realpath(".");

					chdir($path_newsletter);

					$exec=escapeshellarg(DIMS_APP_PATH."scripts/convert_pdf.sh")." ".escapeshellarg($path_newsletter). " ".escapeshellarg($namefile_html_to_pdf)." ".escapeshellarg($namefile_pdf)." ".DIMS_APP_PATH."scripts/ 2>&1";
//echo $exec;die();
					$res=shell_exec(escapeshellcmd($exec));

					// on supprime le fichier temporaire
					unlink($path_newsletter.$namefile_html_to_pdf);

					// on se replace dans le dossier courant
					chdir($currentpath);

					//dims_print_r($to);die();
					$textmessage=$message;
									//dims_send_mail_with_files($from, $to, $subject, $message, $file);
					dims_send_mail_with_pear($from, $to, $subject, $message, $file);

					$redirect = $scriptenv.'?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER;
					dims_redirect($redirect);

					break;

				case dims_const_desktopv2::_NEWSLETTER_SEND_ARTICLE:
					//die('no send before testing');
					$id_news = dims_load_securvalue('id_news', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);

					$env = new news_article();
					$env->open($id_env);

					if ($env->fields['date_envoi'] =='') {

						$file = array();
						$env->fields['date_envoi'] = date("YmdHis");
						$env->save();
						$list_email = array();
						$list_ct = '0';

						//on recherche le nom de domaine pour la desinscription
						$sql_r = "	SELECT 		d.domain
									FROM 		dims_domain d
									INNER JOIN 	dims_workspace w
									ON 			w.newsletter_id_domain = d.id
									AND 		w.id = :idworkspace";

						$res_r = $db->query($sql_r, array(
							':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						));
						$dom = $db->fetchrow($res_r);
						if($dom['domain']=='*') $dom['domain'] = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

						//on recherche tous les contacts a qui envoyer la news
						//avec 1/ les emails qui viennent du net : table "newsletter_subscribed"
						// 2/ les emails issus de la table "mailing_ct"
						// 3/ cas provisoire (du fait de l'ajout des layers) : on prend les email directement dans la fiche contact

						//1
						$tabemail=array();
						$doublons=array();
						$sql_sub = 'SELECT		DISTINCT cl.email, cl.id, u.login
									FROM		dims_mod_business_contact ct
									INNER JOIN	dims_mod_newsletter_subscribed sub
									ON			sub.id_contact = ct.id
									AND			sub.id_newsletter = :idnewsletter
									INNER JOIN	dims_mod_business_contact_layer cl
									ON			cl.id = ct.id
									AND			cl.type_layer = 1
									LEFT JOIN	dims_user u
									ON			u.id_contact = ct.id';

						$res_sub = $db->query($sql_sub, array(
							':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $id_news),
						));

						if($db->numrows($res_sub)) {
							while($tab_sub = $db->fetchrow($res_sub)) {
								if($tab_sub['email'] != '') {
									$nom_compare=strtolower($tab_sub['email']);
									if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
										if($tab_sub['login'] == '') {
											//si le contact n'est pas un user il doit pouvoir se désinscrire via l'email
											$list_email['contact'][$tab_sub['id']] = $tab_sub['email'];
										}
										else {
											$list_email[$tab_sub['id']] = $tab_sub['email'];
										}
										$list_ct .= ' ,'.$tab_sub['id'];

										$tabemail[$nom_compare]=$nom_compare;
									}
									else {
										if (!isset($doublons[$tab_sub['email']])) $doublons[$tab_sub['email']]=1;
										$doublons[$tab_sub['email']]++;
									}
								}
							}
						}

						//2
						$sql_mail = 'SELECT		ct.email, ct.id
									FROM		dims_mod_newsletter_mailing_ct ct
									INNER JOIN	dims_mod_newsletter_mailing_news mn
									ON			mn.id_mailing = ct.id_mailing
									AND			mn.id_newsletter = :idnewsletter
									WHERE		ct.actif = 1';
						$res_mail = $db->query($sql_mail, array(
							':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $id_news),
						));

						if($db->numrows($res_mail)) {
							while($tab_mail = $db->fetchrow($res_mail)) {
								$nom_compare=strtolower($tab_mail['email']);
								if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
									$list_email['mailing'][$tab_mail['id']] = $tab_mail['email'];
									$tabemail[$nom_compare]=$nom_compare;
								}
								else {
									if (!isset($doublons[$tab_mail['email']])) $doublons[$tab_mail['email']]=1;
									$doublons[$tab_mail['email']]++;
								}
							}
						}

						//3
						$params = array();
						$sql_sub = 'SELECT		DISTINCT ct.email, ct.id
									FROM		dims_mod_business_contact ct
									INNER JOIN	dims_mod_newsletter_subscribed sub
									ON			sub.id_contact = ct.id
									AND			sub.id_newsletter = :idnewsletter
									AND			ct.id NOT IN ('.$db->getParamsFromArray(explode(',', $list_ct), 'listct', $params).')';
						$params[':idnewsletter'] = array('type' => PDO::PARAM_INT, 'value' => $id_news);

						$res_sub = $db->query($sql_sub, $params);

						if($db->numrows($res_sub)) {
							while($tab_sub = $db->fetchrow($res_sub)) {
								$nom_compare=strtolower($tab_sub['email']);
								if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
									$list_email[$tab_sub['id']] = $tab_sub['email'];
									$tabemail[$nom_compare]=$nom_compare;
								}
								else {
									if (!isset($doublons[$tab_sub['email']])) $doublons[$tab_sub['email']]=1;
									$doublons[$tab_sub['email']]++;
								}
							}
						}

						//on reprend les infos a envoyer
						$inf_news = new newsletter();
						$inf_news->open($id_news);
						//ici le sujet et le contenu
						$tab_inf = $inf_news->getContent($id_env);
						require_once DIMS_APP_PATH.'include/functions/files.php';
						//maintenant la piece jointe
						$tab_pj = dims_getFiles($dims,$_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_NEWSLETTER,$id_env);

						if (isset($tab_pj) && !empty($tab_pj)) {
							foreach ($tab_pj as $i => $fi) {
								$file[$i]['name'] = $tab_pj[$i]['name'];
								$doc = new docfile();
								$doc->open($tab_pj[0]['id']);
								$path = $doc->getfilepath();

								$file = array();
								$file[0]['name'] = $tab_pj[0]['name'];
								$file[0]['filename'] = $path;
								//le mime-type
								$file[0]['mime-type'] = mime_content_type($path);
							}
						}

						//on fait l'envoi
						// create online version
						$path_newsletter=realpath('.').'/data/newsletter/online/';
						if(!file_exists($path_newsletter)) mkdir($path_newsletter);

						//$namefile_html=strtolower($planning_mois[date('n')])."-".date('Y').".html";
						//$namefile_html_to_pdf=strtolower($planning_mois[date('n')])."-".date('Y')."-pdf.html";
						//$namefile_pdf=strtolower($planning_mois[date('n')])."-".date('Y').".pdf";
						$namefile_html=strtolower(date('F'))."-".date('Y')."-".$id_env.".html";
						$namefile_html_to_pdf=strtolower(date('F'))."-".date('Y')."-".$id_env."-pdf.html";
						$namefile_pdf=strtolower(date('F'))."-".date('Y')."-".$id_env.".pdf";
						$online='/data/newsletter/online/'.$namefile_html;
						$online_to_pdf='/data/newsletter/online/'.$namefile_html_to_pdf;
						$onlinepdf='/data/newsletter/online/'.$namefile_pdf;

						$workspace = new workspace();
						$workspace->open($_SESSION['dims']['workspaceid']);
						$email = $workspace->fields['newsletter_sender_email'];
						if ($email=="") $email=_DIMS_ADMINMAIL;

						$pos=strpos($email,"@");
						if ($pos>0) $name=substr($email,$pos+1);
						else $name=$email;
						$from[0]['name']   = $name;//$email;
						$from[0]['address']= $email;

						$subject = $tab_inf['label'];

						$listAttachTags=$inf_news->attachContactsByTag();

						ob_end_clean();
						// on regarde si on a un template ou non
						$template_name=$tab_inf['template'];

						$template_name=$tab_inf['template'];
						$ch = DIMS_APP_PATH . "/templates/frontoffice/".$template_name;
						/*if (substr($ch, -1)=="/")
							$ch=  DIMS_APP_PATH . "/templates/frontoffice/".$template_name;
						else
							$ch=  DIMS_APP_PATH . "templates/frontoffice/".$template_name;*/

						if (isset($tab_inf['template']) && $tab_inf['template']!='' && is_dir($ch)) {
						if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
							$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
						}

						$smartypath=$_SESSION['dims']['smarty_path'];

						$smartyobject = new Smarty();
						$smartyobject->cache_dir = $smartypath.'/cache';
						$smartyobject->config_dir = $smartypath.'/configs';
						$smartyobject->template_dir = $ch;

						if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
						$smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";

						include_once './modules/system/include/functions.php';

						// on va allouer les variables comme il faut
						$smartyobject->assign('newsletter',array(
							'TITLE' => $tab_inf['label'],
							'MONTH' => $planning_mois[date('n')],
							'YEAR' => date('Y'),
							'TEMPLATE_ROOT_PATH' => $dims->getProtocol().$dom['domain']."/templates/frontoffice/".$template_name,
							'DATA_PATH' => "/data", //$dims->getProtocol().$dom['domain']."/data",
							'UNSUBSCRIBELINK' => "",
							'ONLINE_LINK' =>  $dims->getProtocol().$dom['domain'].$online,
							'ONLINE_PDF' =>  $dims->getProtocol().$dom['domain'].$onlinepdf,
							'CONTENT' => $tab_inf['content']
						));

						$smartyobject->display('index.tpl');
						$message =  ob_get_contents();
						ob_end_clean();

						//////////////////////////////////////////////////////////////////
						// on fait la version pour le PDF
						ob_end_clean();
						ob_start();
						$message_to_pdf="";
						$root_path=realpath(".");

						// on va allouer les variables comme il faut
						$smartyobject->assign('newsletter',array(
							'TITLE' => $tab_inf['label'],
							'MONTH' => $planning_mois[date('n')],
							'YEAR' => date('Y'),
							'TEMPLATE_ROOT_PATH' => DIMS_APP_PATH."templates/frontoffice/".$template_name,
							'DATA_PATH' => "/data", //$root_path."/data",
							'DEAR_FIRSTNAME' => "",
							'FIRSTNAME' => "",
							'UNSUBSCRIBELINK' => "",
							'LASTNAME' => "",
							'ONLINE_LINK' =>  $dims->getProtocol().$dom['domain'].$online,
							'ONLINE_PDF' =>  $dims->getProtocol().$dom['domain'].$onlinepdf,
							'CONTENT' => $tab_inf['content']
						));

						$smartyobject->display('index.tpl');
						$message_to_pdf =  ob_get_contents();
						ob_end_clean();

						// on va traiter aussi les data ecrits dans le contenu
						$rep_from[]= "'./index.php";
						$rep_from[]= "'/index.php";
						$rep_from[]= "'./index-quick.php";
						$rep_from[]= "'./data/";
						$rep_from[]= "\"/templates";
						$rep_from[]= "'/templates";
						$rep_from[]= "&quot;/templates";
						$rep_from[]= '"./index.php';
						$rep_from[]= '"/index.php';
						$rep_from[]= '"./index-quick.php';
						$rep_from[]= '"./data/';
						$rep_from[]= '"/data/';

						$rep_to[]="'".$root_path."/index.php";
						$rep_to[]="'".$root_path."/index.php";
						$rep_to[]="'".$root_path."/index-quick.php";
						$rep_to[]="'".$root_path."/data/";
						$rep_to[]= "\"".$root_path."/templates";
						$rep_to[]= "'".$root_path."/templates";
						$rep_to[]= "&quot;".$root_path."/templates";
						$rep_to[]='"'.$root_path."/index.php";
						$rep_to[]='"'.$root_path."/index.php";
						$rep_to[]='"'.$root_path."/index-quick.php";
						$rep_to[]='"'.$root_path."/data/";
						$rep_to[]='"'.$root_path."/data/";

						$message_to_pdf= str_replace($rep_from,$rep_to,$message_to_pdf);


						// on génère les contenus et PDF
						$onlinefile=realpath(".").$online;
						$rep_from=array();
						$rep_to=array();
						$rep_from[]= "'./data/";
						$rep_from[]= '"./data/';
						$rep_to[]="'/data/";
						$rep_to[]= '"/data/';
						$messageonline=str_replace($rep_from,$rep_to,$message);
						file_put_contents($onlinefile, $messageonline);

						$onlinefilepdf=realpath(".").$online_to_pdf;
						file_put_contents($onlinefilepdf, $message_to_pdf);

						// on convertit vers le pdf
						$pathscript=realpath(".")."/scripts/";
						$currentpath=realpath(".");

						chdir($path_newsletter);
						$exec=escapeshellarg(DIMS_APP_PATH."scripts/convert_pdf.sh")." ".escapeshellarg($path_newsletter). " ".escapeshellarg($namefile_html_to_pdf)." ".escapeshellarg($namefile_pdf)." ".DIMS_APP_PATH."scripts/ 2>&1";
						$res=shell_exec(escapeshellcmd($exec));

						// on supprime le fichier temporaire
						unlink($path_newsletter.$namefile_html_to_pdf);

						// on se replace dans le dossier courant
						chdir($currentpath);

						// on construit le message modèle avec une variante pour le lien de désincription
						ob_end_clean();
						ob_start();
						$message_tpl="";
						// on va allouer les variables comme il faut
						$smartyobject->assign('newsletter',array(
							'TITLE' => $tab_inf['label'],
							'MONTH' => $planning_mois[date('n')],
							'YEAR' => date('Y'),
							'TEMPLATE_ROOT_PATH' => $dims->getProtocol().$dom['domain']."/templates/frontoffice/".$template_name,
							'DATA_PATH' => $dims->getProtocol().$dom['domain']."/data",
							'UNSUBSCRIBELINK' => "[UNSUBSCRIBELINK]",
							'ONLINE_LINK' =>  $dims->getProtocol().$dom['domain'].$online,
							'ONLINE_PDF' =>  $dims->getProtocol().$dom['domain'].$onlinepdf,
							'CONTENT' => $tab_inf['content']
						));

						$smartyobject->display('index.tpl');
						$message_tpl =  ob_get_contents();
						ob_end_clean();

						//////////////////////////////////////////////////////////////////////////
						// on fait le parcours des différents elements
						//////////////////////////////////////////////////////////////////////////
						if(!empty($list_email)) {
							//dims_print_r($list_email);die();
							foreach($list_email as $id_ct => $email) {
								if($id_ct == 'mailing') { //cas des personnes justes inscrites via un email (cas des imports notamment)

									foreach($email as $id_mail => $addemail) {
										if($addemail != '') {
											$to[0]['name'] = $addemail;
											$to[0]['address'] = $addemail;

											$link_unsubscribe=dims_urlencode($dims->getProtocol().$dom['domain'].'/index.php?op=newsletter&action=news_unsubscribe&id_news='.$id_news.'&id_mail='.$id_mail, true);

											// on remplace et on construit le message final
											$message=str_replace("[UNSUBSCRIBELINK]",$link_unsubscribe,$message_tpl);

											//dims_print_r($message);die();
											//if(!empty($tab_pj)) {
											dims_send_mail_with_pear($from, $addemail, $subject, $message, $file);
											//}
											//else dims_send_mail($from, $to, $subject, $message);
										}
									}
								}
								elseif($id_ct == 'contact') { //cas des personnes inscrites via le formulaire en front (on un id_contact mais pas d'id_user)
									foreach($email as $id_contact => $addemail) {
										if($addemail != '') {
											$to[0]['name'] = $addemail;
											$to[0]['address'] = $addemail;

											$link_unsubscribe=dims_urlencode($dims->getProtocol().$dom['domain'].'/index.php?op=newsletter&action=news_unsubscribe&id_news='.$id_news.'&id_contact='.$id_contact, true);

											// on remplace et on construit le message final
											$message=str_replace("[UNSUBSCRIBELINK]",$link_unsubscribe,$message_tpl);

											//if(!empty($tab_pj)) {
											dims_send_mail_with_pear($from, $addemail, $subject, $message, $file);
											//}
											//else dims_send_mail($from, $to, $subject, $message);
										}
									}
								}
								elseif($email != '') { //cas des personnes ayant accès au backoffice
									$to[0]['name'] = $email;
									$to[0]['address'] = $email;

									ob_end_clean();
									ob_start();
									$message="";
									$link_unsubscribe="javascript:alert('You are an authenticated user on this platform, to cancel your subscription please open your personal contact form');";

									// on remplace et on construit le message final
									$message=str_replace("[UNSUBSCRIBELINK]",$link_unsubscribe,$message_tpl);

									//if(!empty($tab_pj)) {
									//dims_send_mail_with_files($from, $to, $subject, $message, $file);
									dims_send_mail_with_pear($from, $email, $subject, $message, $file);
									//}
									//else dims_send_mail($from, $to, $subject, $message);
								}
							}
						}

						$listalltags=array();
						if (!empty($listAttachTags)) {
							foreach ($listAttachTags as $id =>$ct) {
								$email='';
								if ($ct['email']<>'')
									$email= $ct['email'];
								elseif ($ct['email2']<>'')
									$email= $ct['email2'];

								if ($email!='' && !isset($listalltags[$email])) {
									$to[0]['name'] = '--';
									$to[0]['address'] = $email;

									$link_unsubscribe=dims_urlencode($dims->getProtocol().$dom['domain'].'/index.php?op=newsletter&action=news_unsubscribe&id_news='.$id_news.'&id_mail='.$id_mail, true);

									// on remplace et on construit le message final
									$message=str_replace("[UNSUBSCRIBELINK]",$link_unsubscribe,$message_tpl);
									//if(!empty($tab_pj)) {
									//dims_send_mail_with_files($from, $to, $subject, $message, $file);
									dims_send_mail_with_pear($from, $email, $subject, $message, $file);
									//}
									//else dims_send_mail($from, $to, $subject, $message);

									$listalltags[$email]=$email;
								}
							}

						}
					}
					else {
						//////////////////////////////////////////////////////////////////////////
						// on a pas de template !!
						//////////////////////////////////////////////////////////////////////////
						if(!empty($list_email)) {
							foreach($list_email as $id_ct => $email) {
								if($id_ct == 'mailing') { //cas des personnes justes inscrites via un email (cas des imports notamment)

									foreach($email as $id_mail => $addemail) {
										if($addemail != '') {
											$to[0]['name'] = '--';
											$to[0]['address'] = $addemail;

											$message = '<html>
											<head></head>
											<body>'.$tab_inf['content'].'

											</body>
											</html>';
											// <div><a href="'.dims_urlencode($dims->getProtocol().$dom['domain'].'/index.php?op=newsletter&action=news_unsubscribe&id_news='.$id_news.'&id_mail='.$id_mail, false).'">To cancel your subscription please click here.</a></div>
											if(!empty($tab_pj)) {
												//dims_send_mail_with_files($from, $to, $subject, $message, $file);
												dims_send_mail_with_pear($from, $addemail, $subject, $tab_inf['content'], $file);
											}
											else dims_send_mail($from, $to, $subject, $message);
										}
									}
								}
								elseif($id_ct == 'contact') { //cas des personnes inscrites via le formulaire en front (on un id_contact mais pas d'id_user)
									foreach($email as $id_contact => $addemail) {
										if($addemail != '') {
											$to[0]['name'] = '--';
											$to[0]['address'] = $addemail;

											$message = '<html>
											<head></head>
											<body>'.$tab_inf['content'].'
												<div><a href="'.dims_urlencode($dims->getProtocol().$dom['domain'].'/index.php?op=newsletter&action=news_unsubscribe&id_news='.$id_news.'&id_contact='.$id_contact, false).'">To cancel your subscription please click here.</a></div>
											</body>
											</html>';
											if(!empty($tab_pj)) {
												//dims_send_mail_with_files($from, $to, $subject, $message, $file);
												dims_send_mail_with_pear($from, $addemail, $subject, $tab_inf['content'], $file);
											}

											else dims_send_mail($from, $to, $subject, $message);
										}
									}
								}
								elseif($email != '') { //cas des personnes ayant accès au backoffice
									$to[0]['name'] = '--';
									$to[0]['address'] = $email;

									$message = '<html>
									<head></head>
									<body>'.$tab_inf['content'].'
									<div>You are an I-net user : to cancel your subscription please go to your personal profil on I-net portal (see "Newsletter\'s bloc" on the right).</div>
									</body>
									</html>';
									if(!empty($tab_pj)) {
										//dims_send_mail_with_files($from, $to, $subject, $message, $file);
										dims_send_mail_with_pear($from, $email, $subject, $tab_inf['content'], $file);
									}
									else dims_send_mail($from, $to, $subject, $message);
								}
							}
						}

						$listalltags=array();
						if (!empty($listAttachTags)) {
							foreach ($listAttachTags as $id =>$ct) {
								$email='';
								if ($ct['email']<>'')
									$email= $ct['email'];
								elseif ($ct['email2']<>'')
									$email= $ct['email2'];


								if ($email!='' && !isset($listalltags[$email])) {
									$to[0]['name'] = '--';
									$to[0]['address'] = $email;

									if(!empty($tab_pj)) {
										//dims_send_mail_with_files($from, $to, $subject, $message, $file);
										dims_send_mail_with_pear($from, $email, $subject, $tab_inf['content'], $file);
									}
									else dims_send_mail($from, $to, $subject, $tab_inf['content']);

									$listalltags[$email]=$email;
								}
							}

						}
					}// fin du test si on a un template ou non
					/*$env = new news_article();
					$env->open($id_env);

					$env->fields['date_envoi'] = date("YmdHis");

					$env->save();*/
					}
					//$redirect='admin.php?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'&sent=1';
					//$redirect = $scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER;
					//dims_redirect($redirect);
					$redirect = $scriptenv.'?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER;
					dims_redirect($redirect);
					break;

				case dims_const_desktopv2::_NEWSLETTER_INSC_FROMBACK :
					//$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
					$id_contact = dims_load_securvalue('id_contact',dims_const::_DIMS_CHAR_INPUT,true,true);

					$subs = new news_subscribed();
					$subs->init_description();

					$subs->fields['id_newsletter'] = $id_news;
					$subs->fields['id_contact'] = $id_contact;
					$subs->fields['etat'] = 1;
					$subs->fields['date_inscription'] = date("YmdHis");
					$subs->fields['date_desinscription'] = '';

					$subs->save();

					$redirect=$scriptenv.'?action='._NEWSLETTER_VIEW_INSC.'&subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_inscr';
					dims_redirect($redirect);

					break;
				case dims_const_desktopv2::_NEWS_RECIPIENTS_RESET_FILTER:
					unset($_SESSION['dims']['current_choiceregistration']);
					unset($_SESSION['dims']['current_nameregistration']);
					dims_redirect('/admin.php?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER);
					break;
				case dims_const_desktopv2::_NEWSLETTER_RECREATE_INSC :
				case dims_const_desktopv2::_NEWSLETTER_DELETE_INSC :

					$id_contact = dims_load_securvalue('id_contact',dims_const::_DIMS_CHAR_INPUT,true,true);
					$from = dims_load_securvalue('from',dims_const::_DIMS_CHAR_INPUT,true,true);
					$mailing_ct=dims_load_securvalue('mailing_ct',dims_const::_DIMS_NUM_INPUT,true,true);

					if ($mailing_ct==1) {
						$subs = new mailing_ct();
						$subs->open($id_contact);

						if($newsletter_op == dims_const_desktopv2::_NEWSLETTER_RECREATE_INSC) {
							$subs->fields['actif'] = 1;
							$subs->fields['date_desinscription'] = "";
						}
						else {
							$subs->fields['actif'] = 0;
							$subs->fields['date_desinscription'] = dims_createtimestamp();
						}

						$subs->save();
					}
					else {
						$subs = new news_subscribed();
						$subs->open($id_news, $id_contact);

						if($newsletter_op == dims_const_desktopv2::_NEWSLETTER_DELETE_INSC) {
							$subs->fields['etat'] = 0;
							$subs->fields['date_desinscription'] = date("YmdHis");
						}
						elseif($newsletter_op == dims_const_desktopv2::_NEWSLETTER_RECREATE_INSC) {
							$subs->fields['etat'] = 1;
							$subs->fields['date_inscription'] = date("YmdHis");
							$subs->fields['date_desinscription'] = '';
						}

						$subs->save();
					}
					//dims_print_r($subs);die();
					dims_redirect('/admin.php?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER);
					break;

				 case dims_const_desktopv2::_NEWSLETTER_DELETE_MAILING_LIST :
					$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
					$mail = new mailing();

					$mail->open($id_mail);

					//on supprime tous les rattachements
					$db->query("DELETE FROM dims_mod_newsletter_mailing_ct WHERE id_mailing = :idmailing", array(
						':idmailing' => array('type' => PDO::PARAM_INT, 'value' => $id_mail),
					));
					//dims_print_r($mail);die();
					//on supprime la liste
					$mail->delete();

					//$redirect = 'admin.php?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL;
					dims_redirect("/admin.php?news_op=".dims_const_desktopv2::_NEWSLETTERS_DESKTOP."&mode=newsletters");
					break;
				case dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST_ADD:
					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/mailing/description.tpl.php';
					break;
				case dims_const_desktopv2::_NEWSLETTER_SAVE_LIST_EMAIL :

					$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
					$mail = new mailing();

					//Cas de modification
					if($id_mail != '') {
						$mail->open($id_mail);
					}
					else {
						$mail->init_description();
					}
					$mail->setvalues($_POST,'list_');

					$id_mail = $mail->save();

					$inurl = '&id_mail='.$id_mail;
					$redirect = 'admin.php?news_op='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST.$inurl;
					dims_redirect($redirect);
					break;

				case dims_const_desktopv2::_NEWSLETTER_SAVE_RATTACH_NEWS :

					$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_news_ratt = dims_load_securvalue('news_linked', dims_const::_DIMS_NUM_INPUT, true, true);
					$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, true);

					if($id_news != '') {
						$mail_news = new mailing_news();
						$mail_news->init_description();

						$mail_news->fields['id_mailing'] = $id_mail;
						$mail_news->fields['id_newsletter'] = $id_news_ratt;
						$mail_news->fields['id_user_create'] = $_SESSION['dims']['userid'];
						$mail_news->fields['date_create'] = date("YmdHis");
						$mail_news->save();
					}

					$inurl = '&id_mail='.$id_mail;
					$redirect = 'admin.php?news_op='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST.$inurl;

					dims_redirect($redirect);
					break;

				case dims_const_desktopv2::_NEWSLETTER_ACTION_SUPP_LIST :

					$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_link = dims_load_securvalue('id_link', dims_const::_DIMS_NUM_INPUT, true, true);
					$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, true);

					$mail_news = new mailing_news();
					$mail_news->open($id_link);
					$mail_news->delete();

					$inurl = '&id_mail='.$id_mail;
					$redirect = 'admin.php?news_op='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST.$inurl;
					dims_redirect($redirect);
					break;

				case dims_const_desktopv2::_NEWSLETTER_ACTION_SUPP_EMAIL :

					$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_supp = dims_load_securvalue('id_supp_mail', dims_const::_DIMS_NUM_INPUT, true, true);
					$search_val = dims_load_securvalue('search_val', dims_const::_DIMS_CHAR_INPUT, true,true);
					$viewduplicateemails = dims_load_securvalue('viewduplicateemails', dims_const::_DIMS_CHAR_INPUT, true, true,false);
					$mail_ct = new mailing_ct();
					$mail_ct->open($id_supp);
					$mail_ct->delete();

					$inurl = '&id_mail='.$id_mail;
					$redirect = 'admin.php?news_op='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST.$inurl."&search_val=".$search_val.'&viewduplicateemails='.$viewduplicateemails;
					dims_redirect($redirect);
					break;

				case dims_const_desktopv2::_NEWSLETTER_SAVE_RATTACH_EMAIL :

					$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
					$email = dims_load_securvalue('add_mail', dims_const::_DIMS_CHAR_INPUT, true, true);

					$mail_ct = new mailing_ct();
					$mail_ct->init_description();
					$mail_ct->fields['id_mailing'] = $id_mail;
					$mail_ct->fields['email'] = $email;
					$mail_ct->fields['date_creation'] = date("YmdHis");
					$mail_ct->fields['actif'] = 1;
					$mail_ct->save();

					$inurl = '&id_mail='.$id_mail;
					$redirect = 'admin.php?news_op='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST.$inurl;
					dims_redirect($redirect);
					break;

				case dims_const_desktopv2::_NEWSLETTER_ACTION_CHG_EMAIL_STATE :
					$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_state_mail = dims_load_securvalue('id_state_mail', dims_const::_DIMS_NUM_INPUT, true, true);

					$mail_ct = new mailing_ct();
					$mail_ct->open($id_state_mail);
					if($mail_ct->fields['actif'] == 1) $mail_ct->fields['actif'] = 0;
					else $mail_ct->fields['actif'] = 1;
					$mail_ct->save();

					$inurl = '&id_mail='.$id_mail;
					$redirect = '/admin.php?news_op='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST;
					dims_redirect($redirect);
					break;
				case dims_const_desktopv2::_NEWSLETTER_ATTACH_REGISTRATION:
					require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/registration_attach.tpl.php';
					break;
				case dims_const_desktopv2::_NEWSLETTER_ATTACH_REGISTRATION_DELETE:
					$id_dmd = dims_load_securvalue('id_dmd', dims_const::_DIMS_NUM_INPUT, true, true, true);

					if($id_dmd != 0) {
							$inscription = new newsletter_inscription();

							$inscription->open($id_dmd);

							$inscription->delete();
					}
					$redirect = '/admin.php?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER;
					dims_redirect($redirect);
					break;
				case dims_const_desktopv2::_NEWSLETTER_ATTACH_REGISTRATION_SAVE:
					require_once(DIMS_APP_PATH . '/modules/system/class_contact_layer.php');

					if($convmeta == '' || !isset($_SESSION['dims']['contact_fields_mode'])) {
						//on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
						$sql = " SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
												mb.protected,mb.name as namefield,mb.label as titlefield
								FROM		dims_mod_business_meta_field as mf
								INNER JOIN	dims_mb_field as mb
								ON		mb.id=mf.id_mbfield
								RIGHT JOIN	dims_mod_business_meta_categ as mc
								ON		mf.id_metacateg=mc.id
								WHERE		mf.id_object = :idobject
								AND		mc.admin=1
								AND		mf.used=1
								ORDER BY	mc.position, mf.position ";
						$rs_fields=$db->query($sql, array(
								':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
						));

						$rubgen=array();
						$convmeta = array();

						while ($fields = $db->fetchrow($rs_fields)) {
							if (!isset($rubgen[$fields['id_cat']]))  {
								$rubgen[$fields['id_cat']]=array();
								$rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
								$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
								if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
							}

							// on ajoute maintenant les champs dans la liste
							$fields['use']=0;// par defaut non utilise
							$fields['enabled']=array();
							if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

							$_SESSION['dims']['contact_fields_mode'][$fields['id']]=$fields['mode'];

							// enregistrement de la conversion
							$convmeta[$fields['namefield']]=$fields['id'];
						}
					}

					if(isset($_POST['id_dmd']) && !empty($_POST['id_dmd']) && isset($_POST['id_contact']) && !empty($_POST['id_contact'])) {

						$id_dmd = dims_load_securvalue('id_dmd', dims_const::_DIMS_NUM_INPUT, true, true);
						$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true);
						$inscription = new newsletter_inscription();
						$inscription->open($id_dmd);

						if(!$inscription->new) {
							$contact = new contact();
							$ct_layer = new contact_layer();
							if($id_contact != -1) {
								$maj_ct = 0;
								$maj_ly = 0;
								$contact->open($id_contact);

								// recherche si layer pour workspace
								$res=$db->query("SELECT 	id,type_layer,id_layer
												FROM 		dims_mod_business_contact_layer
												WHERE 		id= :idcontactlayer
												AND 		type_layer=1
												AND 		id_layer= :idlayer", array(
									':idcontactlayer'	=> array('type' => PDO::PARAM_INT, 'value' => $contact->getId()),
									':idlayer'			=> array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));

								if($db->numrows($res) > 0 ) {
									//echo "select id,type_layer,id_layer from dims_mod_business_contact_layer where id=".$contact->fields['id']." and type_layer=1 and id_layer=".$_SESSION['dims']['workspaceid']; die();
									$sel_layer = $db->fetchrow($res);
									//on charge le layer
									$ct_layer->open($sel_layer['id'],$sel_layer['type_layer'],$sel_layer['id_layer']);
								}
								else {
									//on cree un layer
									$ct_layer->init_description();
									$ct_layer->fields['id'] = $contact->fields['id'];
									$ct_layer->fields['type_layer'] = 1;
									$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
								}

								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(empty($contact->fields['address']   ) && !empty($inscription->fields['adresse'])) {
											$contact->fields['address'] = $inscription->fields['adresse'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(empty($ct_layer->fields['address']	) && !empty($inscription->fields['adresse'])) {
											$ct_layer->fields['address'] = $inscription->fields['adresse'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(empty($contact->fields['postalcode']) && !empty($inscription->fields['cp'])) {
											$contact->fields['postalcode'] = $inscription->fields['cp'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(empty($ct_layer->fields['postalcode']) && !empty($inscription->fields['cp'])) {
											$ct_layer->fields['postalcode'] = $inscription->fields['cp'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(empty($contact->fields['city']	   ) && !empty($inscription->fields['ville'])) {
											$contact->fields['city'] = $inscription->fields['ville'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(empty($ct_layer->fields['city']		) && !empty($inscription->fields['ville'])) {
											$ct_layer->fields['city'] = $inscription->fields['ville'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(empty($contact->fields['country']   ) && !empty($inscription->fields['pays'])) {
											$contact->fields['country'] = $inscription->fields['pays'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(empty($ct_layer->fields['country']	) && !empty($inscription->fields['pays'])) {
											$ct_layer->fields['country'] = $inscription->fields['pays'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(empty($contact->fields['phone']	   ) && !empty($inscription->fields['tel'])) {
											$contact->fields['phone'] = $inscription->fields['tel'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(empty($ct_layer->fields['phone']		) && !empty($inscription->fields['tel'])) {
											$ct_layer->fields['phone'] = $inscription->fields['tel'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(empty($contact->fields['email']	   ) && !empty($inscription->fields['email'])) {
											$contact->fields['email'] = $inscription->fields['email'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(empty($ct_layer->fields['email']		) && !empty($inscription->fields['email'])) {
											$ct_layer->fields['email'] = $inscription->fields['email'];
											$maj_ly = 1;
										}
									}
								}

								if($maj_ly == 1) {
									$ct_layer->save();
								}
							}
							else {
								//on cree un layer
								$ct_layer->init_description();
								$ct_layer->fields['type_layer'] = 1;
								$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

								//cas particulier : nom et prenom toujours dans la table contact
								$contact->fields['lastname']	= (!empty($inscription->fields['nom'])) ? $inscription->fields['nom'] : '';
								$contact->fields['firstname']	= (!empty($inscription->fields['prenom'])) ? $inscription->fields['prenom'] : '';

								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(!empty($inscription->fields['adresse'])) {
											$contact->fields['address'] = $inscription->fields['adresse'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(!empty($inscription->fields['adresse'])) {
											$ct_layer->fields['address'] = $inscription->fields['adresse'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(!empty($inscription->fields['cp'])) {
											$contact->fields['postalcode'] = $inscription->fields['cp'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(!empty($inscription->fields['cp'])) {
											$ct_layer->fields['postalcode'] = $inscription->fields['cp'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(!empty($inscription->fields['ville'])) {
											$contact->fields['city'] = $inscription->fields['ville'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(!empty($inscription->fields['ville'])) {
											$ct_layer->fields['city'] = $inscription->fields['ville'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(!empty($inscription->fields['pays'])) {
											$contact->fields['country'] = $inscription->fields['pays'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(!empty($inscription->fields['pays'])) {
											$ct_layer->fields['country'] = $inscription->fields['pays'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(!empty($inscription->fields['tel'])) {
											$contact->fields['phone'] = $inscription->fields['tel'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(!empty($inscription->fields['tel'])) {
											$ct_layer->fields['phone'] = $inscription->fields['tel'];
											$maj_ly = 1;
										}
									}
								}
								if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
									if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
										//c'est un champ generique -> on enregistre dans contact
										if(!empty($inscription->fields['email'])) {
											$contact->fields['email'] = $inscription->fields['email'];
											$maj_ct = 1;
										}
									}
									else {
										//c'est un champ metier -> on enregistre dans un layer
										if(!empty($inscription->fields['email'])) {
											$ct_layer->fields['email'] = $inscription->fields['email'];
											$maj_ly = 1;
										}
									}
								}
							}
							$id_ct = $contact->save();
							if($maj_ly == 1) {
								$ct_layer->fields['id'] = $contact->fields['id'];
								$ct_layer->save();
							}

							$subscribed = new news_subscribed();

							$subscribed->fields['id_newsletter']		= $id_news;
							$subscribed->fields['id_contact']			= $id_ct;
							$subscribed->fields['date_inscription']		= date('YmdHis');
							$subscribed->fields['date_desinscription']	= '';
							$subscribed->fields['etat']					= 1;

							$subscribed->save();

							$inf_news = new newsletter();
							$inf_news->open($id_news);
							//ici le sujet et le contenu

							$inscription->delete();
						}
					}

					dims_redirect($scriptenv.'?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER);
				 break;
		}
}
