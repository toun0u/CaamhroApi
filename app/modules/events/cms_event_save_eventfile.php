<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
require_once(DIMS_APP_PATH . '/modules/doc/include/global.php');

$id_etap = 0;

$id_etap = dims_load_securvalue('id_etape',dims_const::_DIMS_NUM_INPUT,true,true,false);
$id_ct = dims_load_securvalue('id_ct',dims_const::_DIMS_NUM_INPUT,true,true,false);
$id_doc = dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,true,false);
$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT,true,true,false);
if($id_etap != null) {
	//on recherche les infos pour le mail dans le workspace
	//il faut tout d'abord trouver l'id_workspace (on va passer par l'action)
	$sql_a = "SELECT id_workspace FROM dims_mod_business_action WHERE id = :idevent";
	$res_a = $db->query($sql_a, array(':idevent' => $id_event) );
	while($tab_w = $db->fetchrow($res_a)) {
		$id_workspace = $tab_w['id_workspace'];
	}
	$work = new workspace();
	$work->open($id_workspace);

	// enregistrement des docs
	// on va regarder ce qu'il y a dans le r�pertoire temporaire du user courant
	$sid = session_id();
	$upload_dir = realpath(DIMS_APP_PATH . '/data/uploads/'.$sid).'/';
	if (is_dir( realpath(DIMS_APP_PATH . '/data/uploads/'.$sid)) && is_dir($upload_dir)){
		if ($dh = opendir($upload_dir)){
			while (($filename = readdir($dh)) !== false){
				if ($filename!="." && $filename!=".."){
					$docfile = new docfile();
					$docfile->setvalues($_POST,'docfile_');
					$docfile->fields['id_module'] = 1;
					$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$docfile->fields['id_folder'] = -1;
					$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
					$docfile->tmpuploadedfile = $upload_dir.$filename;
					$docfile->fields['name'] = $filename;
					$docfile->fields['size'] = filesize($upload_dir.$filename);
					$error = $docfile->save();

					$id_newdoc=$docfile->fields['id'];

					// on cr�� l'association entre le doc et l'etape relative au user (dims_mod_business_event_etap_file_user)
					$etap_file = new etap_file_ct();

					//on regarde deja si le lien existe
					$sql_v = "SELECT id FROM dims_mod_business_event_etap_file_user WHERE id_etape = :idetape AND id_contact = :idcontact AND id_doc = :iddoc AND id_action = :idaction";
					$res_v = $db->query($sql_v, array(':idetape' => $id_etap, ':idcontact' => $id_ct, ':iddoc' => $id_doc, ':idaction' => $id_evt));

					if($db->numrows($res_v) > 0) {
						$tab_dfu = $db->fetchrow($res_v);
						$etap_file->open($tab_dfu['id']);
					}
					else {
						$etap_file->init_description();
					}

					$etap_file->fields['id_action'] = $id_evt;
					$etap_file->fields['id_etape'] = $id_etap;
					$etap_file->fields['id_doc'] = $id_doc;
					$etap_file->fields['id_contact'] = $id_ct;
					$etap_file->fields['valide'] = 0;
					$etap_file->fields['id_doc_frontoffice'] = $id_newdoc;
					$etap_file->fields['provenance'] = '_DIMS_LABEL_INET';
					$etap_file->fields['date_reception'] = date("YmdHis");

					$etap_file->save();

					$evt = new action();
					$evt->open($id_evt);

					$etap = new action_etap();
					$etap->open($id_etap);

					$ct = new contact();
					$ct->open($id_ct);

					$organisateur = new user();
					$organisateur->open($evt->fields['id_user']);

					//mail
					$from	= array();
					$to		= array();
					$subject= '';
					$message= '';

					$email = '';
					$email = $work->fields['events_sender_email'];
					if ($email=="") $email=_DIMS_ADMINMAIL;

					$to[0]['name']	   = $organisateur->fields['lastname'].' '.$organisateur->fields['firstname'];
					$to[0]['address']  = $organisateur->fields['email'];

					$from[0]['name'] = '';
					$from[0]['address'] = $email;

					$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}', '{step_label}', '{filename}');
					$tab_val = array($ct->fields['firstname'],$ct->fields['lastname'],$evt->fields['libelle'],$ct->fields['country'],'2', '', $etap->fields['label'], $filename);

					$subject_br = $work->fields['events_mail6_subject'];
					$mail_brouil = $work->fields['events_mail6_content'];

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

		/*			  $subject = 'R&eacute;ception d\'un document pour l\'&eacute;tape '.$etap->fields['label'].' pour l\'&eacute;v&eacute;nement '.$evt->fields['libelle'];

					$content = 'Vous &ecirc;tes organisateur de l\'&eacute;v&eacute;nement : '.$evt->fields['libelle'].'.<br /><br />';
					$content = 'Participant : '.$ct->fields['firstname'].' '.$ct->fields['lastname'].'<br /><br />';
					$content.= 'has attached a new document for level '.$etap->fields['label'].' of his registration to his personal area<br /><br />';
					$content.= 'Document attached : '.$filename.'<br />';
					$content.= 'Please go to I-net to approve this document. A confirmation (or refusal) email will be sent to the participant depending on your choice in I-Net.';
					$content.= 'If approved, the participant will receive the documents for level'.$evt->fields['niveau'].' of his registration.<br /><br />';*/


					dims_send_mail($from,$to, $subject, nl2br($mail_content));
				}
			}
			closedir($dh);
		}
		rmdir($upload_dir);
	}
}
?>
