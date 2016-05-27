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

$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
$subaction = dims_load_securvalue('subaction',dims_const::_DIMS_CHAR_INPUT,true,true);
$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['dims']['current_newsletter'], $_SESSION['dims']['default_newsletter']);

$listworkspace_nl = '0';

$sql_in = "	SELECT	id_to
						FROM	dims_workspace_share
						WHERE	id_from = :idfrom
						AND		active = 1
						AND		id_object = :idobject ";
$res_in = $db->query($sql_in, array(
	':idfrom'	=> $_SESSION['dims']['workspaceid'],
	':idobject'	=> dims_const::_SYSTEM_OBJECT_NEWSLETTER
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

//blocs de gauche
echo '<div style="float:left;clear:both;width:30%;">';

require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_newsletter_bloc_menu.php';
echo "<br>";
require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_newsletter_view_mailinglist.php';

echo '</div>';

//bloc de droite
echo '<div style="float:right;width:70%;">';

switch($action) {

	default:
		//on ouvre la newsletter courante
		if ($id_news>0) {
			if(!empty($id_news)) {
				$inf_news = new newsletter();
				$inf_news->open($id_news);

				if (!isset($inf_news->fields['label']))				 dims_redirect ('/admin.php?id_news=0');
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER'].' : '.$inf_news->fields['label']);
			}
			else {
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER']);
			}

			// on cré les onglets
			$tab_onglet = array();

			if (!isset($tab_news[$id_news]['nb_dmd'])) $tab_news[$id_news]['nb_dmd']=array();
			$tab_onglet[_DIMS_NEWSLETTER_INSCR]['title'] = $_DIMS['cste']['_DIMS_NEWSLETTER_GEST_INSC']." (".count($tab_news[$id_news]['nb_dmd']).")";
			$tab_onglet[_DIMS_NEWSLETTER_INSCR]['url'] = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_NEWSLETTER."&cat=0&dims_desktop=block&dims_action=public&id_news=$id_news&subaction="._DIMS_NEWSLETTER_INSCR;
			$tab_onglet[_DIMS_NEWSLETTER_INSCR]['width'] = '';
			$tab_onglet[_DIMS_NEWSLETTER_INSCR]['position'] = 'left';

			$tab_onglet[_DIMS_NEWSLETTER_NEWSLETTER]['title'] = $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER'].'s';
			$tab_onglet[_DIMS_NEWSLETTER_NEWSLETTER]['url'] = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_NEWSLETTER."&cat=0&dims_desktop=block&dims_action=public&id_news=$id_news&subaction="._DIMS_NEWSLETTER_NEWSLETTER;
			$tab_onglet[_DIMS_NEWSLETTER_NEWSLETTER]['width'] = '';
			$tab_onglet[_DIMS_NEWSLETTER_NEWSLETTER]['position'] = 'left';

			$tab_onglet[_DIMS_NEWSLETTER_DESCRIPTION]['title'] = $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'];
			$tab_onglet[_DIMS_NEWSLETTER_DESCRIPTION]['url'] = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_NEWSLETTER."&cat=0&dims_desktop=block&dims_action=public&id_news=$id_news&subaction="._DIMS_NEWSLETTER_DESCRIPTION;
			$tab_onglet[_DIMS_NEWSLETTER_DESCRIPTION]['width'] = '';
			$tab_onglet[_DIMS_NEWSLETTER_DESCRIPTION]['position'] = 'left';

			echo '<br/>';
			echo $skin->create_toolbar($tab_onglet,$subaction,true,'0',"onglet");

			switch($subaction) {
				default:
				case _DIMS_NEWSLETTER_DESCRIPTION :
					require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_description.php');
					break;
				case _DIMS_NEWSLETTER_INSCR :
					require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_inscription.php');
					break;
				case _DIMS_NEWSLETTER_NEWSLETTER :
					require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_articles.php');
					break;
			}
			echo $skin->close_simplebloc();
		}

	break;

	case _NEWSLETTER_VIEW_LIST_EMAIL :
		$submail = dims_load_securvalue('submail', dims_const::_DIMS_CHAR_INPUT, true, true);

		switch($submail) {
			case _NEWSLETTER_IMPORT_EMAIL : //ATTENTION : pas de break ici car on enchaine tout de suite sur le case _NEWSLETTER_ACTION_ADD_LIST
				require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_import_email.php');
			case _NEWSLETTER_ACTION_ADD_LIST :
				require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_newsletter_add_mailinglist.php';
				break;

		}
		break;

	case 'change_view':
	//default:
		//lister l'ensemble des news (avec les personnes rattachees et les demandes en cours)
		require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_listall.php');
		break;

	case _NEWSLETTER_ADD_ARTICLE :
		require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_add_article.php');
		break;

	case _NEWSLETTER_ACTION_ADD :
		require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_add.php');
		break;

	case _NEWSLETTER_VIEW_DMDINSC :
		require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_newsletter_dmd.php';
		break;

	case _NEWSLETTER_ACTION_SAVE :
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
		$id_curr_news = $news->save();

		$_SESSION['dims']['current_newsletter'] = $id_curr_news;

		$redirect="admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_NEWSLETTER."&cat=0&dims_desktop=block&dims_action=public";
		dims_redirect($redirect);
		break;

	case _NEWSLETTER_ACTION_DELETE :
		$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_NUM_INPUT,true,true);
		$news = new newsletter();
		$news->open($id_news);

		if ($news->getNbInscription==0) $news->delete();
		else {
			$news->fields['etat'] = 0;
			$news->save();
		}

		$_SESSION['dims']['default_newsletter'] = 0;

		$redirect="admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_NEWSLETTER."&cat=0&dims_desktop=block&dims_action=public&id_news=0";
		dims_redirect($redirect);
		break;

	case _NEWSLETTER_ACTION_SAVE_ENV :

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
		$env->fields['id_newsletter'] = $id_news;

		$env->save();

		//$redirect='admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ADD_ARTICLE.'&id_news='.$id_news.'&id_env='.$env->fields['id'];
		$redirect = $scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER.'&news_act=add_article&id_env='.$env->fields['id'];
		dims_redirect($redirect);
		break;

	case _NEWSLETTER_SUPPR_ARTICLE:

		$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
		$id_env = dims_load_securvalue('id_env',dims_const::_DIMS_CHAR_INPUT,true,true);

		$env = new news_article();
		$env->open($id_env);

		$env->delete();

		//$redirect='admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news;
		$redirect = $scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER;
		dims_redirect($redirect);

		break;

	case 'test_sending':

		$id_news = dims_load_securvalue('id_news', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);

		//on ouvre le layer correspondant
		$ct_l = new contact_layer();
		$ct_l->open($_SESSION['dims']['user']['id_contact'], 1, $_SESSION['dims']['workspaceid']);

		//on reprend les infos a envoyer
		$inf_news = new newsletter();
		$inf_news->open($id_news);
		//ici le sujet et le contenu
		$tab_inf = $inf_news->getContent($id_env);
		require_once DIMS_APP_PATH.'include/functions/files.php';
		//maintenant la piece jointe
		$tab_pj = dims_getFiles($dims,$_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_NEWSLETTER,$id_env);

		//on a besoin du path exact pour le fichier
		$doc = new docfile();
		$doc->open($tab_pj[0]['id']);
		$path = $doc->getfilepath();

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
		$file[0]['name'] = $tab_pj[0]['name'];
		$file[0]['filename'] = $path;
		//le mime-type
		$file[0]['mime-type'] = mime_content_type($path);

		$to[0]['name'] = '--';
		$to[0]['address'] = $ct_l->fields['email'];

		$message = '<html>
										<head></head>
										<body>'.$tab_inf['content'].'</body>
								</html>';

		dims_send_mail_with_files($from, $to, $subject, $message, $file);

		//$redirect='admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'&sent=2';
		$redirect = $scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER;
		dims_redirect($redirect);

	break;

	case _NEWSLETTER_SEND_ARTICLE:

		$id_news = dims_load_securvalue('id_news', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);

		$env = new news_article();
		$env->open($id_env);

		if ($env->fields['date_envoi'] =='') {

			$env->fields['date_envoi'] = date("YmdHis");
			$env->save();
			$list_email = array();
			$list_ct = '0';

			//on recherche le nom de domaine pour la desinscription
			$sql_r =   "SELECT d.domain
						FROM dims_domain d
						INNER JOIN dims_workspace w
						ON w.newsletter_id_domain = d.id
						AND w.id = :workspaceid ";

			$res_r = $db->query($sql_r, array(
				':workspaceid'	=> $_SESSION['dims']['workspaceid']
			));
			$dom = $db->fetchrow($res_r);

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
				':idnewsletter'	=> $id_news
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
				':idnewsletter'	=> $id_news
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
			$params = array(
				':idnewsletter'	=> $id_news
			);
			$sql_sub = 'SELECT		DISTINCT ct.email, ct.id
						FROM		dims_mod_business_contact ct
						INNER JOIN	dims_mod_newsletter_subscribed sub
						ON			sub.id_contact = ct.id
						AND			sub.id_newsletter = :idnewsletter
						AND			ct.id NOT IN ('.$db->getParamsFromArray($list_ct, 'listct', $params).')';

			$res_sub = $db->query($sql_sub, $params );

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

			if(!empty($tab_pj)) {
				//on a besoin du path exact pour le fichier
				$doc = new docfile();
				$doc->open($tab_pj[0]['id']);
				$path = $doc->getfilepath();

				$file = array();
				$file[0]['name'] = $tab_pj[0]['name'];
				$file[0]['filename'] = $path;
				//le mime-type
				$file[0]['mime-type'] = mime_content_type($path);
			}

			//on fait l'envoi
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
								if(!empty($tab_pj)) dims_send_mail_with_files($from, $to, $subject, $message, $file);
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
								if(!empty($tab_pj)) dims_send_mail_with_files($from, $to, $subject, $message, $file);
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
											<div>You are an I-net user : to cancel your subscription please go to your personal profil on I-net portal (see "Newsletter\'s bloc" on the left).</div>
										</body>
									</html>';
						if(!empty($tab_pj)) dims_send_mail_with_files($from, $to, $subject, $message, $file);
						else dims_send_mail($from, $to, $subject, $message);
					}
				}
			}

			/*$env = new news_article();
			$env->open($id_env);

			$env->fields['date_envoi'] = date("YmdHis");

			$env->save();*/
		}
		//$redirect='admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'&sent=1';
		$redirect = $scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER;
		dims_redirect($redirect);

		break;

	case _NEWSLETTER_INSC_FROMBACK :
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

	case _NEWSLETTER_RECREATE_INSC :
	case _NEWSLETTER_DELETE_INSC :

		$id_contact = dims_load_securvalue('id_contact',dims_const::_DIMS_CHAR_INPUT,true,true);
		$from = dims_load_securvalue('from',dims_const::_DIMS_CHAR_INPUT,true,true);

		$subs = new news_subscribed();
		$subs->open($id_news, $id_contact);

		if($action == _NEWSLETTER_DELETE_INSC) {
			$subs->fields['etat'] = 0;
			$subs->fields['date_desinscription'] = date("YmdHis");
		}
		elseif($action == _NEWSLETTER_RECREATE_INSC) {
			$subs->fields['etat'] = 1;
			$subs->fields['date_inscription'] = date("YmdHis");
			$subs->fields['date_desinscription'] = '';
		}

		$subs->save();

		if($from != 1) {
			$redirect=$scriptenv.'?action='._NEWSLETTER_VIEW_INSC.'&subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_inscr';
			dims_redirect($redirect);
		}

		break;

	case 'delete_mailing_list':

		$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		$mail = new mailing();

		$mail->open($id_mail);

		//on supprime tous les rattachements
		$db->query("DELETE FROM dims_mod_newsletter_mailing_ct WHERE id_mailing = :idmailing ", array(
			':idmailing' => $id_mail
		));

		//on supprime la liste
		$mail->delete();

		//$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL;
		dims_redirect($scriptenv);
		break;

	case _NEWSLETTER_SAVE_LIST_EMAIL :

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
		$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.$inurl;
		dims_redirect($redirect);
		break;

	case _NEWSLETTER_SAVE_RATTACH_NEWS :

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

		switch($from) {
			default:
				$inurl = '&id_mail='.$id_mail;
				$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.$inurl;
			break;
			case 'to_insc':
				//$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_INSC.'&id_news='.$id_news;
				$redirect = $scriptenv.'?action='._NEWSLETTER_VIEW_INSC.'&subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_inscr';
			break;
		}
		dims_redirect($redirect);
		break;

	case _NEWSLETTER_ACTION_SUPP_LIST :

		$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_link = dims_load_securvalue('id_link', dims_const::_DIMS_NUM_INPUT, true, true);
		$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, true);

		$mail_news = new mailing_news();
		$mail_news->open($id_link);
		$mail_news->delete();

		switch($from) {
			default:
				$inurl = '&id_mail='.$id_mail;
				$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.$inurl;
			break;
			case 'to_insc':
				//$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_INSC.'&id_news='.$id_news;
				$redirect = $scriptenv.'?action='._NEWSLETTER_VIEW_INSC.'&subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_inscr';
			break;
		}
		dims_redirect($redirect);
		break;

	case _NEWSLETTER_ACTION_SUPP_EMAIL :

		$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_supp = dims_load_securvalue('id_supp_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		$search_val = dims_load_securvalue('search_val', dims_const::_DIMS_CHAR_INPUT, true,true);
		$viewduplicateemails = dims_load_securvalue('viewduplicateemails', dims_const::_DIMS_CHAR_INPUT, true, true,false);
		$mail_ct = new mailing_ct();
		$mail_ct->open($id_supp);
		$mail_ct->delete();

		$inurl = '&id_mail='.$id_mail;
		$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.$inurl."&search_val=".$search_val.'&viewduplicateemails='.$viewduplicateemails;
		dims_redirect($redirect);
		break;

	case _NEWSLETTER_SAVE_RATTACH_EMAIL :

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
		$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.$inurl;
		dims_redirect($redirect);
		break;

	case _NEWSLETTER_ACTION_CHG_EMAIL_STATE :
		$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_state_mail = dims_load_securvalue('id_state_mail', dims_const::_DIMS_NUM_INPUT, true, true);

		$mail_ct = new mailing_ct();
		$mail_ct->open($id_state_mail);
		if($mail_ct->fields['actif'] == 1) $mail_ct->fields['actif'] = 0;
		else $mail_ct->fields['actif'] = 1;
		$mail_ct->save();

		$inurl = '&id_mail='.$id_mail;
		$redirect = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.$inurl;
		dims_redirect($redirect);
		break;


}
echo '</div>';

//echo $skin->close_backgroundbloc();
?>
