<?php

require_once DIMS_APP_PATH.'modules/system/include/business.php';
require_once DIMS_APP_PATH.'modules/system/class_inscription.php';
require_once DIMS_APP_PATH.'modules/system/class_action.php';
require_once DIMS_APP_PATH.'include/functions/mail.php';

?>
<link rel="stylesheet" type="text/css" href="./common/modules/system/include/design.css" />
<!--<script type="text/javascript" src="/js/prototype.js"></script>
<script type="text/javascript" src="/js/effects.js"></script>
<script type="text/javascript" src="/js/scriptaculous.js"></script>-->
<?php

	$style= '';
	switch ($op) {
		case 'courtier':
			require_once(DIMS_APP_PATH . "/modules/system/courtier/cms.php");
			break;
		case 'projects':
		case 'event':
			$style = 'width:675px;height:700px;';
			break;
		case 'newsletter':
			$style = '';
			break;
	}
?>
<?php
echo '<div style="'.$style.' >';
?>
<?php
switch ($op) {
	case 'last_newsletter':
		if(isset($_SESSION['dims']['currentworkspace']['newsletter']) && $_SESSION['dims']['currentworkspace']['newsletter']){
			require_once DIMS_APP_PATH.'modules/system/class_newsletter.php';
			require_once DIMS_APP_PATH.'modules/system/class_news_article.php';
			$sel = "SELECT 		c.*
					FROM 		".news_article::TABLE_NAME." c
					INNER JOIN 	".newsletter::TABLE_NAME." n
					ON 			n.id = c.id_newsletter
					WHERE 		n.etat = 1
					AND 		n.id_workspace = :idw
					AND 		c.date_envoi > 0
					ORDER BY 	c.date_envoi DESC
					LIMIT 		1";
			$params = array(
				':idw'=>array('value'=>$_SESSION['dims']['currentworkspace']['id'],'type'=>PDO::PARAM_INT),
			);
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,$params);
			if($r = $db->fetchrow($res)){
				$c = new news_article();
				$c->openFromResultSet($r);
				if(isset($_SESSION['dims']['front_template_path']) && file_exists($_SESSION['dims']['front_template_path']."/wce_last_newsletter.tpl.php"))
					$c->display($_SESSION['dims']['front_template_path']."/wce_last_newsletter.tpl.php");
				else
					$c->display(DIMS_APP_PATH."modules/system/desktopV2/templates/newsletters/wce_last_newsletter.tpl.php");
			}
		}
		break;
	case 'history_newsletter':
		if(isset($_SESSION['dims']['currentworkspace']['newsletter']) && $_SESSION['dims']['currentworkspace']['newsletter']){
			require_once DIMS_APP_PATH.'modules/system/class_newsletter.php';
			require_once DIMS_APP_PATH.'modules/system/class_news_article.php';
			$sel = "SELECT 		*
					FROM 		".news_article::TABLE_NAME." c
					INNER JOIN 	".newsletter::TABLE_NAME." n
					ON 			n.id = c.id_newsletter
					WHERE 		n.etat = 1
					AND 		n.id_workspace = :idw
					AND 		c.date_envoi > 0
					ORDER BY 	c.date_envoi DESC";
			$params = array(
				':idw'=>array('value'=>$_SESSION['dims']['currentworkspace']['id'],'type'=>PDO::PARAM_INT),
			);
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,$params);
			$path = DIMS_APP_PATH."modules/system/desktopV2/templates/newsletters/wce_history_newsletter.tpl.php";
			if(isset($_SESSION['dims']['front_template_path']) && file_exists($_SESSION['dims']['front_template_path']."/wce_history_newsletter.tpl.php"))
				$path = $_SESSION['dims']['front_template_path']."/wce_history_newsletter.tpl.php";
			while($r = $db->fetchrow($res)){
				$c = new news_article();
				$c->openFromResultSet($r);
				$c->display($path);
			}
		}
		break;
	case 'newsletter':
		$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

		if($action == 'news_unsubscribe') {
			$id_news = dims_load_securvalue('id_news', dims_const::_DIMS_NUM_INPUT, true);
			$id_ct = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true);
			$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true);
			$id_inscr = dims_load_securvalue('id_subscription', dims_const::_DIMS_NUM_INPUT, true);

			if($id_ct == '' && $id_mail != '') { //cas des imports de mail
				require_once DIMS_APP_PATH.'modules/system/class_mailing-ct.php';
				$mail = new mailing_ct();
				$mail->open($id_mail);
				$mail->fields['actif'] = 0;
				$mail->save();

				// recherche du workspace
				$idwork=$_SESSION['dims']['workspaceid'];
				$res=$db->query("SELECT id_workspace from dims_mod_newsletter_mailing_list as ml where ml.id= :idmailing ", array(
					':idmailing' => $mail->fields['id_mailing']
				));
				if ($db->numrows($res)>0) {
					while ($wo=$db->fetchrow($res)) {
						$idwork=$wo['id_workspace'];
					}
				}

				$work = new workspace();
				$work->open($idwork);

				$from[0]['name']   = '';
				$from[0]['address']= $work->fields['newsletter_sender_email'];//'contact@luxembourgforbusiness.lu';

				$to[0]['name'] = '--';
				$to[0]['address'] = $mail->fields['email'];

				$subject = $work->fields['newsletter_unsubscribe_subject'];

				$message=$work->getMessage('newsletter_unsubscribe_content');


				dims_send_mail($from, $to, $subject, $message);
				dims_redirect("http://www.luxembourgforbusiness.lu");
			}
			elseif($id_ct != '' && $id_mail == '') { //cas des contacts non users
				require_once DIMS_APP_PATH.'modules/system/class_news_subscribed.php';
				$nct = new news_subscribed();
				$nct->open($id_news, $id_ct);
				$nct->fields['etat'] = 0;
				$nct->fields['date_desinscription'] = date("YmdHis");
				$nct->save();

				// recherche du workspace
				$idwork=$_SESSION['dims']['workspaceid'];
				$res=$db->query("SELECT id_workspace from dims_mod_newsletter where id= :idnews ", array(
					':idnews' => $id_news
				));
				if ($db->numrows($res)>0) {
					while ($wo=$db->fetchrow($res)) {
						$idwork=$wo['id_workspace'];
					}
				}

				$work = new workspace();
				$work->open($idwork);

				$ct = new contact();
				$ct->open($id_ct);
				$tab_fields = $ct->getDynamicFields();

				$sql = "SELECT email FROM dims_mod_business_contact_layer WHERE id = :idct AND type_layer = 1 AND id_layer = :idlayer ";
				$res = $db->query($sql, array(
					':idct' => $id_ct,
					':idlayer' => $ct->fields['id_workspace']
				));
				if($db->numrows($res) > 0) {
					$tab_mail = $db->fetchrow($res);
					$to[0]['name'] = '--';
					$to[0]['address'] = $tab_mail['email'];
				}
				elseif($ct->fields['email'] != '') {
					$to[0]['name'] = '--';
					$to[0]['address'] = $ct->fields['email'];
				}
				else {

					echo dims_nl2br($work->getMessage('newsletter_unsubscribe_content','','',''));
				}

				$from[0]['name']   = '--';
				$from[0]['address']= $work->fields['email_noreply'];
				//$from[0]['name']	 = 'luxembourgforbusiness';
				//$from[0]['address']= 'contact@luxembourgforbusiness.lu';

				$subject = $work->fields['newsletter_unsubscribe_subject'];//"Newsletter LFB";
				$message = dims_nl2br($work->fields['newsletter_unsubscribe_content']);

				dims_send_mail($from, $to, $subject, $message);

			}
			elseif($id_inscr != '') { //cas des personnes ayant rempli le formulaire d'inscription qui se désinscrivent avant même que l'admin ait validé leur inscription
				require_once DIMS_APP_PATH.'modules/system/class_newsletter_inscription.php';

				// recherche du workspace
				$idwork=$_SESSION['dims']['workspaceid'];
				$res=$db->query("SELECT id_workspace from dims_mod_newsletter where id= :idnews ", array(
					':idnews' => $id_news
				));
				if ($db->numrows($res)>0) {
					while ($wo=$db->fetchrow($res)) {
						$idwork=$wo['id_workspace'];
					}
				}

				$work = new workspace();
				$work->open($idwork);

				$inscr = new newsletter_inscription();
				$inscr->open($id_inscr);

				$to[0]['name'] = $inscr->fields['nom'].' '.$inscr->fields['prenom'];
				$to[0]['address'] = $inscr->fields['email'];

				$inscr->delete();

				$from[0]['name']   = '--';
				$from[0]['address']= $work->fields['email_noreply'];

				$subject = $work->getMessage('newsletter_unsubscribe_subject', '', '', '');//"Newsletter LFB";
				$message = dims_nl2br($work->getMessage('newsletter_unsubscribe_content', $to[0]['name'], '', '',''));

				dims_send_mail($from, $to, $subject, $message);

				echo "Thank you for your participation.";

			}

		}
		else {
			//require_once DIMS_APP_PATH.'modules/system/cms_newsletter.php';
			require_once DIMS_APP_PATH.'modules/system/cms_newsletter_accueil.php';
		}
		break;

	case 'projects':
		require_once DIMS_APP_PATH.'modules/system/cms_projects.php';
		break;
	// objet cms pour l'affichage des événements publiques de l'URPS
	case 'planning_public_urps' :
	case 'planning_public' :
		require_once DIMS_APP_PATH.'modules/system/cms_planning_urps.php';
		break;
	case 'account':
		$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
		switch($action) {
			case 'authentication':
				if($dims->isConnected()) {
					dims_redirect($dims->getScriptEnv().'?action=informations');
				}
				include DIMS_APP_PATH . 'modules/system/templates/identificationform.tpl.php';
				break;
			case 'saveinformations':
				if(!$dims->isConnected()) {
					dims_redirect($dims->getScriptEnv().'?action=authentication');
				}

				$password           = dims_load_securvalue('password', dims_const::_DIMS_CHAR_INPUT, true, true, false);
				$password_confirm   = dims_load_securvalue('password_confirm', dims_const::_DIMS_CHAR_INPUT, true, true, false);

				$errors = array();

				$user = $dims->getCurrentUser();
				$user->setvalues($_POST, 'u_');

				if(!empty($password) || !empty($password_confirm)) {
					if($password != $password_confirm) {
						$errors['password'] = dims_constant::getVal('_OEUVRE_ERROR_PASSWORDS_NOT_CORRESPOND');
					} else {
						dims::getInstance()->getPasswordHash($password, $user->fields['password'], $user->fields['salt']);
						$_SESSION['dims']['password'] = $user->fields['password'];
					}
				}

				if(empty($errors)) {
					$user->save();
					dims_redirect($dims->getScriptEnv().'?action=informations&success=1');
				} else {
					$user->setLightAttribute('errors', $errors);
					$user->display(DIMS_APP_PATH . 'modules/system/templates/account/accountform.tpl.php');
				}
				break;
			default:
			case 'informations':
				if(!$dims->isConnected()) {
					dims_redirect($dims->getScriptEnv().'?action=authentication');
				}
				require_once DIMS_APP_PATH.'modules/system/class_address.php';

				$success = dims_load_securvalue('success', dims_const::_DIMS_NUM_INPUT, true, true);

				$user = $dims->getCurrentUser();
				$user->setLightAttribute('success', $success);

				$contact = $user->getContact();

				$subscribedmailinglists = $user->getsubscribedmailinglists();
				$subscribedoptions = newsletter_subscribed_options::finduseroptions($user);

				$user->setLightAttribute('subscribedmailinglists', $subscribedmailinglists);
				$user->setLightAttribute('subscribedoptions', $subscribedoptions);
				$user->display(DIMS_APP_PATH . 'modules/system/templates/account/accountform.tpl.php');
				break;
			case 'savemailinglists':
				if(!$dims->isConnected()) {
					dims_redirect($dims->getScriptEnv().'?action=authentication');
				}

				$mailinglistoptions = dims_load_securvalue('mailinglistoptions', dims_const::_DIMS_CHAR_INPUT, true, true, false);

				$errors = array();

				$user = $dims->getCurrentUser();

				if(!empty($mailinglistoptions)) {
					$mailinglists = $user->getsubscribedmailinglists();
					$subscribedoptions = newsletter_subscribed_options::finduseroptions($user);

					foreach($mailinglists as $mailinglist) {
						if(isset($subscribedoptions[$mailinglist->getId()])) {
							$useroptions = $subscribedoptions[$mailinglist->getId()];
						} else {
							$useroptions = new newsletter_subscribed_options();
							$useroptions->init_description();
							$useroptions->setugm();

							$useroptions->fields['id_mailinglist']       = $mailinglist->getId();
							$useroptions->fields['id_subscribeduser']    = $user->getId();
						}

						$useroptions->fields['nomail'] = $mailinglistoptions[$mailinglist->getId()]['nomail'];
						$useroptions->save();
					}
				}

				if(empty($errors)) {
					$user->save();
					dims_redirect($dims->getScriptEnv().'?action=mailinglists&success=1');
				} else {
					$user->setLightAttribute('errors', $errors);
					$user->display(DIMS_APP_PATH . 'modules/system/templates/account/mailinglists.tpl.php');
				}
				break;
			case 'mailinglists':
				if(!$dims->isConnected()) {
					dims_redirect($dims->getScriptEnv().'?action=authentication');
				}

				$success = dims_load_securvalue('success', dims_const::_DIMS_NUM_INPUT, true, true);

				$user = $dims->getCurrentUser();
				$user->setLightAttribute('success', $success);

				$subscribedmailinglists = $user->getsubscribedmailinglists();
				$subscribedoptions = newsletter_subscribed_options::finduseroptions($user);

				$user->setLightAttribute('subscribedmailinglists', $subscribedmailinglists);
				$user->display(DIMS_APP_PATH . 'modules/system/templates/account/mailinglists.tpl.php');
				break;
			case 'saveaddresses':
				require_once DIMS_APP_PATH.'modules/system/class_address.php';
				if(!$dims->isConnected()) {
					dims_redirect($dims->getScriptEnv().'?action=authentication');
				}

				$idaddress = dims_load_securvalue('idaddress', dims_const::_DIMS_NUM_INPUT, true, true);

				$errors = array();

				$user = $dims->getCurrentUser();

				$contact = $user->getContact();

				$address = new address();
				if(!empty($idaddress)) {
					$address->open($idaddress);
				} else {
					$address->init_description();
					$address->setugm();
				}

				$address->setvalues($_POST, 'addr_');
				$address->save();

				$idtype = dims_load_securvalue('id_type', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$link = $address->getLinkCt($contact->get('id_globalobject'));
				if(!empty($link)) {
					$link->set('id_type', $idtype);
				} else {
					$link = $address->addLink($contact->get('id_globalobject'), $idtype);
				}

				$link->setvalues($_POST, 'addrlink_');
				$link->save();

				if(empty($errors)) {
					$user->save();
					dims_redirect($dims->getScriptEnv().'?action=addresses&success=1');
				} else {
					$user->setLightAttribute('errors', $errors);
					$user->display(DIMS_APP_PATH . 'modules/system/templates/account/addresses.tpl.php');
				}
				break;
			case 'addresses':
				require_once DIMS_APP_PATH.'modules/system/class_address.php';
				if(!$dims->isConnected()) {
					dims_redirect($dims->getScriptEnv().'?action=authentication');
				}

				$success = dims_load_securvalue('success', dims_const::_DIMS_NUM_INPUT, true, true);

				$user = $dims->getCurrentUser();
				$user->setLightAttribute('success', $success);

				$contact = $user->getContact();

				$subscribedmailinglists = $user->getsubscribedmailinglists();
				$subscribedoptions = newsletter_subscribed_options::finduseroptions($user);

				$addresses = address::getAddressesFromGo($contact->get('id_globalobject'));
				if(count($addresses)){
					foreach($addresses as $key => $addr){
						$addr->setLightAttribute('go_parent', $contact->get('id_globalobject'));
						$addr->setLightAttribute('id_ct', $contact->get('id'));
						//$addr->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/display_mini_address.tpl.php');
					}
				}

				$user->setLightAttribute('addresses', $addresses);
				$user->setLightAttribute('subscribedmailinglists', $subscribedmailinglists);
				$user->setLightAttribute('subscribedoptions', $subscribedoptions);
				$user->display(DIMS_APP_PATH . 'modules/system/templates/account/addresses.tpl.php');
				break;
			case 'deladdress':
				require_once DIMS_APP_PATH.'modules/system/class_address.php';
				if(!$dims->isConnected()) {
					die();
				}

				$idlink = dims_load_securvalue('idaddress', dims_const::_DIMS_CHAR_INPUT, true, true, true);

				$linkaddr = new address_link();
				$linkaddr->open($idlink);

				if(!$linkaddr->isNew()) {
					$linkaddr->delete();
				}

				dims_redirect($dims->getScriptEnv().'?action=addresses&success=1');
				break;
			case 'searchcity':
				require_once DIMS_APP_PATH.'modules/system/class_city.php';
				if(!$dims->isConnected()) {
					die();
				}

				$cities = array();

				$postalcode = dims_load_securvalue('postalcode', dims_const::_DIMS_CHAR_INPUT, true, true, true);

				$rowcities = city::find_by(array('cp' => $postalcode));

				foreach($rowcities as $city) {
					$cities[$city->getId()] = array(
						'id'        => $city->getId(),
						'label'     => $city->get('label'),
						'code_dep'  => $city->get('code_dep'),
					);
				}

				ob_clean();
				echo json_encode($cities);
				ob_flush();
				die();
				break;
		}
		break;
}
