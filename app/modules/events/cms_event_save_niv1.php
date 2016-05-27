<?php

$id_event = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true);
$nb_inscrip = 1;
if (!isset($_SESSION['dims']['tmp_nb_insc'])) $_SESSION['dims']['tmp_nb_insc']=1;

$nb_form = dims_load_securvalue('nb_inscrip', dims_const::_DIMS_NUM_INPUT, false, true, false, $_SESSION['dims']['tmp_nb_insc'],$nb_inscrip);

// on va recuperer la liste des events rattaches
$workspace_code=dims_load_securvalue('workspace_code', dims_const::_DIMS_CHAR_INPUT, true);
$front=$dims->getWebWorkspaces();
$id_workspace_verify=0;

// verification de l'existence du code
foreach($front as $id=>$worksp) {
	if ($worksp['code']==$workspace_code) {
		$id_workspace_verify=$worksp['id'];
	}
}

if ($id_workspace==0) {
	$back=$dims->getAdminWorkspaces();
	foreach($back as $id=>$worksp) {
		if ($worksp['code']==$workspace_code) {
			$id_workspace_verify=$worksp['id'];
		}
	}
}

if(isset($_POST) && !empty($_POST) && isset($_POST['nb_inscrip']) &&
   (isset($_SESSION['dims']['captcha']) && !empty($_SESSION['dims']['captcha'])) || $id_workspace_verify>0) {

	$_SESSION['dims']['tmpevent']= dims_load_securvalue($_POST, dims_const::_DIMS_CHAR_INPUT, true, true, true);
	$captcha = $_SESSION['dims']['captcha'];

	if($captcha == $_POST['captcha'] || $id_workspace_verify>0) {
		$errors     = false;
		//on recherche les infos pour le mail dans le workspace
		//il faut tout d'abord trouver l'id_workspace (on va passer par l'action)
		$sql_a = "SELECT id_workspace FROM dims_mod_business_action WHERE id = :idevent";
		$res_a = $db->query($sql_a, array(':idevent' => $id_event));
		if ($db->numrows($res_a)>0) {
                    while($tab_w = $db->fetchrow($res_a)) {
			$id_workspace = $tab_w['id_workspace'];
                    }
                }
                else {
                    $id_workspace=$_SESSION['dims']['workspaceid'];
                }

		$work = new workspace();
		$work->open($id_workspace);

		for($i = 0; $i < $nb_form; $i++) {
			$inscription = new inscription();

			$inscription->setvalues($_POST, $i.'_');
			$inscription->fields['id_action'] = $id_event;
			$inscription->fields['host'] = $dims->getProtocol().$dims->getHttpHost();

			if(!empty($inscription->fields['lastname']) &&
			   !empty($inscription->fields['firstname']) &&
			   !empty($inscription->fields['phone']) &&
			   !empty($inscription->fields['email'])) {
				//cas d'un utilisateur venant s'inscrire alors qu'il est deja connect�
				if($_SESSION['dims']['connected']) {
					$inscription->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
					$inscription->fields['validate'] = 0;
				}
				$inscription->save();

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

				$work->open($id_workspace);
				$email = $work->fields['events_sender_email'];
				if ($email=="") $email=_DIMS_ADMINMAIL;

				$evt->open($id_event);

				$firstname = dims_load_securvalue($i.'_firstname', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$lastname = dims_load_securvalue($i.'_lastname', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$company = dims_load_securvalue($i.'_company', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}');
				$tab_val = array($firstname, $lastname, $evt->fields['libelle'], $company, $evt->fields['niveau']);

                                // on recupere le @ et on prend le reste
                                $pos=strpos($email,"@");
                                if ($pos>0) $name=substr($email,$pos+1);
                                else $name=$email;
                                $from[0]['name']   = $name;//$email;
				$from[0]['address'] = $email;

				$res=$db->query("select id from dims_user where id_contact= :idcontact", array(':idcontact' => $evt->fields['id_organizer']));
				if ($db->numrows($res)>0) {
					$f=$db->fetchrow($res);
					$id_orga=$f['id'];
				}

				//idem to responsable
				$res=$db->query("select id from dims_user where id_contact= :idcontact", array(':idcontact' => $evt->fields['id_responsible'] ));
				if ($db->numrows($res)>0) {
					$f=$db->fetchrow($res);
					$id_resp=$f['id'];
				}

				// recherche si il y a des groupes d'organisateur
				$lstusers=$evt->getRespsByUsers(false);

				if (!empty($lstusers)) {
					foreach ($lstusers as $usr) {
						if ($usr['email']!='') {
							//$organisateur->open($id_orga);
							$to[0]['name']     = $usr['lastname'].' '.$usr['firstname'];
							$to[0]['address']  = $usr['email'];

							/*if ($id_orga!=$id_resp && $id_resp>0) {
									$responsable->open($id_resp);
									// on ajout le responsable en copie de l'email
									$to[1]['name']     = $responsable->fields['lastname'].' '.$responsable->fields['firstname'];
									$to[1]['address']  = $responsable->fields['email'];
							}*/

							$subject_br = $work->fields['events_mail1_subject'];
							$mail_brouil = $work->fields['events_mail1_content'];

							//on fait le remplacement des tags
							$subject = str_replace($tab_rep, $tab_val, $subject_br);
							$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
							$mail_content .= '<br><br>';
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
				$to[0]['name']     = dims_load_securvalue($i.'_firstname', dims_const::_DIMS_CHAR_INPUT, true, true, true).' '.dims_load_securvalue($i.'_lastname', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$to[0]['address']  = dims_load_securvalue($i.'_email', dims_const::_DIMS_CHAR_INPUT, true, true, true);

				$subject_br = $work->fields['events_mail2_subject'];
				$mail_brouil = $work->fields['events_mail2_content'];

				//on fait le remplacement des tags
				$subject = str_replace($tab_rep, $tab_val, $subject_br);
				$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
				$mail_content .= '<br><br>';
				if($work->fields['events_signature'] != '') {
					$mail_content .= $work->fields['events_signature'];
				}
				elseif($work->fields['signature'] != '') {
					$mail_content .= $work->fields['signature'];
				}

                                //$mailcontent= str_replace(array("\r","\n"),"<br>",$mailcontent);
                                $mailcontent=nl2br($mailcontent);
				dims_send_mail($from, $to, $subject, nl2br($mail_content));

			}
			else
				$errors = true;
		}
	}
	else
		$errors = true;
}
if($errors)
	dims_redirect('index.php?id_event='.$id_event.'&action=form_niv1&error=1');
else
	dims_redirect('index.php?action=valid_niv1&id_event='.$id_event."&workspace_code=".$workspace_code);
?>
