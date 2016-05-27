<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$id_event = dims_load_securvalue('id_event', dims_const::_DIMS_CHAR_INPUT, true);
$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_CHAR_INPUT, true);

// verification pour etre sur que ce n'est pas rattache
$res=$db->query("SELECT id
				FROM dims_mod_business_event_inscription
				WHERE id_action= :idaction
				AND id_contact= :idcontact",
				array(':idaction' => $id_event, ':idcontact' => $id_contact));
if ($db->numrows($res)==0) {
	$inscr = new event_insc();
	$inscr->init_description();

	$inscr->fields['id_action'] = $id_event;
	$inscr->fields['id_contact'] =$id_contact;
	$inscr->fields['validate'] = 2;
	$inscr->fields['lastname'] = $_SESSION['dims']['user']['lastname'];
	$inscr->fields['firstname'] = $_SESSION['dims']['user']['firstname'];
	$inscr->fields['date_validate'] = date("YmdHis");

	$inscr->save();

	$evt = new action();
	$organisateur = new user();
	$responsable = new user();

	$id_orga=0;
	$id_resp=0;
	$subject = 'Event registration';
	$from   = array();
	$to     = array();
	$message= '';
	$email = '';
	$work = new workspace();
	$work->open($_SESSION['dims']['workspaceid']);
	$email = $work->fields['events_sender_email'];
	if ($email=="") $email=_DIMS_ADMINMAIL;

	$evt->open($id_event);

	$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}');
	$tab_val = array($_SESSION['dims']['user']['firstname'],$_SESSION['dims']['user']['lastname'],$evt->fields['libelle'], '', $evt->fields['niveau']);

	$from[0]['name'] = '';
	$from[0]['address'] = $email;

	// recherche si il y a des groupes d'organisateur
	$lstusers=$evt->getRespsByUsers(false);

	if (!empty($lstusers)) {
		foreach ($lstusers as $usr) {
			if ($usr['email']!='') {
				//$organisateur->open($id_orga);
				$to[0]['name']     = $usr['lastname'].' '.$usr['firstname'];
				$to[0]['address']  = $usr['email'];

				$subject_br = $work->fields['events_mail1_subject'];
				$mail_brouil = $work->fields['events_mail1_content'];

				//on fait le remplacement des tags
				$subject = str_replace($tab_rep, $tab_val, $subject_br);
				$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
				$mail_content .= '<br/><br/>';
				if($work->fields['events_signature'] != '') {
						$mail_content .= $work->fields['events_signature'];
				}
				elseif($work->fields['signature'] != '') {
						$mail_content .= $work->fields['signature'];
				}

				dims_send_mail($from, $to, $subject, nl2br($mail_content));
			}
		}
	}

	// envoi d'un email pour la personne qui demande
	$to[0]['name']     = $_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
	$to[0]['address']  = $_SESSION['dims']['user']['email'];

	$subject_br = $work->fields['events_mail2_subject'];
	$mail_brouil = $work->fields['events_mail2_content'];

	//on fait le remplacement des tags
	$subject = str_replace($tab_rep, $tab_val, $subject_br);
	$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
	$mail_content .= '<br/><br/>';
	if($work->fields['events_signature'] != '') {
			$mail_content .= $work->fields['events_signature'];
	}
	elseif($work->fields['signature'] != '') {
			$mail_content .= $work->fields['signature'];
	}

	dims_send_mail($from, $to, $subject, nl2br($mail_content));
}

dims_redirect('/index.php?op=fairs');
?>
