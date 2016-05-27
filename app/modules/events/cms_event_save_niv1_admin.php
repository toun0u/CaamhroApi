<?php
require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
require_once(DIMS_APP_PATH . '/modules/events/classes/class_event_inscription.php');
require_once(DIMS_APP_PATH . '/modules/events/classes/class_action_etap.php');
require_once(DIMS_APP_PATH . '/modules/events/classes/class_action_etap_ct.php');
require_once(DIMS_APP_PATH . '/modules/events/classes/class_action_etap_file.php');
require_once(DIMS_APP_PATH . '/modules/events/classes/class_action_etap_file_ct.php');

$id_event = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true,true);
$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true,true);
$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true,true);

$nb_inscrip = 1;
if (!isset($_SESSION['dims']['tmp_nb_insc'])) $_SESSION['dims']['tmp_nb_insc']=1;

$nb_form = dims_load_securvalue('nb_inscrip', dims_const::_DIMS_NUM_INPUT, false, true, false, $_SESSION['dims']['tmp_nb_insc'],$nb_inscrip);

// on va recuperer la liste des events rattaches
$workspace_code=dims_load_securvalue('workspace_code',dims_const::_DIMS_CHAR_INPUT, true);
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

$errors		= false;
//on recherche les infos pour le mail dans le workspace
//il faut tout d'abord trouver l'id_workspace (on va passer par l'action)
$sql_a = "SELECT id_workspace FROM dims_mod_business_action WHERE id = :idevent";
$res_a = $db->query($sql_a, array(':idevent' => $id_event));
while($tab_w = $db->fetchrow($res_a)) {
		$id_workspace = $tab_w['id_workspace'];
}
$work = new workspace();
$work->open($id_workspace);

$ct = new contact();
$tier = new tiers();

for($i = 0; $i < $nb_form; $i++) {
		$inscription = new event_insc();

		// collecte des données
		$inscription->setvalues($_POST, $i.'_');
		$newcontact=true;
		$function='';

		if ($id_contact>0) {
			// on a un contact
			$ct->open($id_contact);
			$inscription->fields['lastname']=$ct->fields['lastname'];
			$inscription->fields['firstname']=$ct->fields['firstname'];
			$inscription->fields['phone']=$ct->fields['phone'];
			$inscription->fields['address']=$ct->fields['address'];
			$inscription->fields['city']=$ct->fields['city'];
			$inscription->fields['postalcode']=$ct->fields['postalcode'];
			$inscription->fields['country']=$ct->fields['country'];

			$newcontact=false;
		}
		else {
			// on cree le contact
			$ct->setvalues($_POST, $i.'_');
			$function=$ct->fields['function'];
			unset($ct->fields['function']);
			unset($ct->fields['company']);

			//$ct->fields['id']='';
			$ct->fields['id_module']=_DIMS_MODULE_SYSTEM;
			$ct->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
			$ct->save();
			$id_contact=$ct->fields['id'];
		}

		// controle si email rempli ou non
		if ($inscription->fields['email']=='' && $ct->fields['email']!='') {
			$inscription->fields['email'] = $ct->fields['email'];
		}
		$inscription->fields['id_contact']=$id_contact;

		// verification du compte user
		if ($ct->fields['id']>0) {
			// verification du compte de creation
			$res=$db->query("select id from dims_user where id_contact = :idcontact",array(':idcontact' => $ct->fields['id']));

			// verification du compte user, si 0 n'existe pas
			if ($db->numrows($res)==0) {
				// on doit créer un compte d'acces
				// on regarde le login, pour créer eventuellement des logins complémentaires
				$login=strtolower(dims_convertaccents(substr($ct->fields['firstname'], 0,1).$ct->fields['lastname']));
				$res=$db->query("select login from dims_user where login like :login", array(':login' => addslashes($login) ));
				$ind=0;

				if ($db->numrows($res)>0) {
					$ind=$db->numrows($res);
				}

				// on regarde la valeur, si 0 pas de changement, sinon ajout
				if ($ind>0) {
					$login.=$ind;
				}

				$user = new user();
				$user->fields['firstname']=$ct->fields['firstname'];
				$user->fields['lastname']=$ct->fields['lastname'];
				$user->fields['email']=$ct->fields['email'];
				$user->fields['id_contact']=$id_contact;
				$user->fields['login']=$login;

				$char_list = 'abcdefghijklmnopqrstuvwxyz0123456789';
				$size_list	= strlen($char_list)-1;
				$password='';

				for($i = 0; $i < 8; $i++)
				{
					$rand_nb	= mt_rand(0, $size_list);
					$password  .= $char_list[$rand_nb];
				}

				//echo "Login : ".$login." ".$password;
				$hash_pwd = dims_getPasswordHash($password);
				$user->fields['password'] = $hash_pwd;
				$user->fields['initial_password'] = $password;
				//ALTER TABLE `dims_user` ADD `initial_password` VARCHAR( 48 ) NOT NULL DEFAULT ''
				// enregistrement du compte user
				$user->save();

			}

		}

		if ($id_tiers==0) {

			// on a une entreprise
			$tiers = new tiers();
			$tiers->setugm();
			$company=dims_load_securvalue("0_company",dims_const::_DIMS_CHAR_INPUT,false,true);
			$tiers->fields['intitule']=$company;
			$tiers->save();
			$id_tiers=$tiers->fields['id'];
			$inscription->fields['company']=$tiers->fields['intitule'];

			// creation du link entreprise contact

		}
		else {
			// on a choisit l'entreprise
			$tiers = new tiers();
			$tiers->open($id_tiers);

			$inscription->fields['company']=$tiers->fields['intitule'];
		}


		// on verifie la relation tiers->contact puis event->tiers-contact
		$sql_a = "SELECT * FROM dims_mod_business_tiers_contact WHERE id_tiers = :idtiers and id_contact= :idcontact";
		$res_a = $db->query($sql_a, array(':idtiers' => $id_tiers, ':idcontact' => $id_contact));
		if ($db->numrows($res_a)==0) {
			require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');
			$ct_tiers = new tiersct();
			$ct_tiers->fields['id_tiers']=$id_tiers;
			$ct_tiers->fields['id_contact']=$id_contact;
			$ct_tiers->fields['type_lien']='employer';
			$ct_tiers->fields['function']=$function;
			$ct_tiers->fields['departement']='';
			$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
			$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];
			$ct_tiers->fields['date_create']=date("YmdHis");
			$ct_tiers->fields['link_level']=$ent_link_lvl;
			$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
			$ct_tiers->fields['date_deb']=	dims_createtimestamp();
			$ct_tiers->fields['date_fin']=0;
			$ct_tiers->fields['commentaire']='';
			$ct_tiers->save();
			//dims_print_r($ct_tiers);
		}

		// on cree maintenant la matrice
		// on doit ouvrir l'evenement
		$act = new action();
		$act->open($id_event);

		$querystring='insert into dims_matrix values ';

		$id_action=0;
		$id_opportunity=0;
		$id_contact=$ct->fields['id_globalobject'];
		$id_contact2=0;
		$id_tiers=$tiers->fields['id_globalobject'];
		$id_tiers2=0;
		$id_doc=0;
		$id_country=$act->fields['id_country'];
		$id_workspace=$_SESSION['dims']['workspaceid'];
		$year=date('Y');
		$month=date('m');
		$timestp=dims_createtimestamp();

		$querystring.= '(null,'.$act->fields['id_globalobject'].','.$id_opportunity.','.$id_tiers.','.$id_tiers2.','.$id_contact.','.$id_contact2.','.$id_doc.','.$id_country.','.$year.','.$month.','.$timestp.','.$id_workspace.') ';
		$db->query($querystring);
		//echo $querystring;

//dims_print_r($inscription)->fields;
		//die();
		// on remet les valeurs par defaut
		$id_contact=$ct->fields['id'];
		$id_tiers=$tiers->fields['id'];
		// on valide l'incription
		$inscription->fields['validate'] = 2; // inscription validee
		$inscr->fields['date_validate'] = date("YmdHis");

		$inscription->fields['id_action'] = $id_event;
		$inscription->fields['host'] = $dims->getProtocol().$dims->getHttpHost();


		if(!empty($inscription->fields['lastname']) &&
		   !empty($inscription->fields['firstname'])) {

				$inscription->save();

				// verification des etapes
				$inscription->verifStep($id_event,$id_contact);

				$evt = new action();
				$organisateur = new user();
				$responsable = new user();

				$id_orga=0;
				$id_resp=0;
				$subject = 'Event registration';
				$from	= array();
				$to		= array();
				$message= '';
				$email = '';
				$work = new workspace();
				$work->open($_SESSION['dims']['workspaceid']);
				$email = $work->fields['events_sender_email'];
				if ($email=="") $email=_DIMS_ADMINMAIL;

				$evt->open($id_event);

				$firstname = dims_load_securvalue($i.'_firstname', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$lastname = dims_load_securvalue($i.'_lastname', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$company = dims_load_securvalue($i.'_company', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}');
				$tab_val = array($firstname, $lastname, $evt->fields['libelle'], $company, $evt->fields['niveau']);

				$from[0]['name'] = '';
				$from[0]['address'] = $email;

				$res=$db->query("select id from dims_user where id_contact= :idcontact", array(':idcontact' => $evt->fields['id_organizer']));
				if ($db->numrows($res)>0) {
						$f=$db->fetchrow($res);
						$id_orga=$f['id'];
				}

				//idem to responsable
				$res=$db->query("select id from dims_user where id_contact= :idcontact", array(':idcontact' => $evt->fields['id_responsible']));
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
										$to[0]['name']	   = $usr['lastname'].' '.$usr['firstname'];
										$to[0]['address']  = $usr['email'];

										/*if ($id_orga!=$id_resp && $id_resp>0) {
														$responsable->open($id_resp);
														// on ajout le responsable en copie de l'email
														$to[1]['name']	   = $responsable->fields['lastname'].' '.$responsable->fields['firstname'];
														$to[1]['address']  = $responsable->fields['email'];
										}*/

										$subject_br = $work->fields['events_mail1_subject'];
										$mail_brouil = $work->fields['events_mail1_content'];

										//on fait le remplacement des tags
										$subject = str_replace($tab_rep, $tab_val, $subject_br);
										$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
										$mail_content .= '\n\n';
										if($work->fields['events_signature'] != '') {
														$mail_content .= $work->fields['events_signature'];
										}
										elseif($work->fields['signature'] != '') {
														$mail_content .= $work->fields['signature'];
										}

										// deactivation de l'envoi de l'email pour la creation
										//dims_send_mail($from, $to, $subject, ($mail_content));
								}
						}
				}

				// envoi d'un email pour la personne qui demande
				$to[0]['name']	   = dims_load_securvalue($i.'_firstname', dims_const::_DIMS_CHAR_INPUT, true, true, true).' '.dims_load_securvalue($i.'_lastname', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$to[0]['address']  = dims_load_securvalue($i.'_email', dims_const::_DIMS_CHAR_INPUT, true, true, true);

				$subject_br = $work->fields['events_mail2_subject'];
				$mail_brouil = $work->fields['events_mail2_content'];

				//on fait le remplacement des tags
				$subject = str_replace($tab_rep, $tab_val, $subject_br);
				$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
				$mail_content .= '\n\n';
				if($work->fields['events_signature'] != '') {
						$mail_content .= $work->fields['events_signature'];
				}
				elseif($work->fields['signature'] != '') {
						$mail_content .= $work->fields['signature'];
				}

				dims_send_mail($from, $to, $subject, ($mail_content));

		}
		else
				$errors = true;
}

//dims_print_r($inscription);
//die($errors);
// on redirige
dims_redirect('/admin.php');
?>
