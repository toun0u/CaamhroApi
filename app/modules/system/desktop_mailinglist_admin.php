<?php

require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
require_once(DIMS_APP_PATH . '/modules/system/class_liste_diffusion.php');
require_once(DIMS_APP_PATH . '/modules/system/class_liste_diffusion_email.php');
require_once(DIMS_APP_PATH . '/modules/system/class_liste_diffusion_content.php');

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

switch($action) {

/***********  Envois  ************/

	case 'delete_envoi':

		$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);

		//on supprime les liens entre listes et envoi
		$db->query("DELETE FROM dims_mailing_content_list WHERE id_content = :idenv ", array(
			':idenv' => $id_env
		));

		//on supprime l'envoi
		$env = new list_diff_content();
		$env->open($id_env);
		$env->delete();

		dims_redirect($scriptenv);

		break;

	case 'delete_link':

		$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_list = dims_load_securvalue('id_list', dims_const::_DIMS_NUM_INPUT, true, true);

		$db->query("DELETE FROM dims_mailing_content_list WHERE id_content = :idenv AND id_list = :idlist ", array(
			':idenv'	=> $id_env,
			':idlist'	=> $id_list
		));

		dims_redirect($scriptenv."?action=add_sending&id_env=".$id_env);

		break;

	case 'add_sending':
		require_once(DIMS_APP_PATH . '/modules/system/desktop_mailinglist_add_sending.php');
		break;

	case 'save_sending':

		if ($_POST['env_subject'] != ''){
			$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);
			$list_att = dims_load_securvalue('list_att', dims_const::_DIMS_NUM_INPUT, true, true);


			$env = new list_diff_content();
			if($id_env != 0) {
				$env->open($id_env);
			}

			$env->setvalues($_POST, 'env_');
			$env->setugm();
			$env->fields['date_create'] = date("YmdHis");

			$env->fields['date_modif'] = date("YmdHis");

			$new_env = $env->save();

			if(isset($_POST['add_email']) && $_POST['add_email'] != ''){
				$fields_analyse = analyzeEmailsExpression(dims_load_securvalue('add_email', dims_const::_DIMS_CHAR_INPUT, true, true, true));
				if (!empty($fields_analyse))
					$_SESSION['dims']['tmp_mailing'][$new_env][] = current($fields_analyse);
				else
					$_SESSION['dims']['mailing']['erreur_mail'] = dims_load_securvalue('add_email', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			}

			if(isset($_FILES['email_list']) && $new_env != 0 && $_FILES['email_list']['name'] != ''){

				//on fait la procédure d'import en mettant les données collectées en session
				if(!isset($_SESSION['dims']['tmp_mailing'])) $_SESSION['dims']['tmp_mailing'] = array();
				if(!isset($_SESSION['dims']['tmp_mailing'][$new_env])) $_SESSION['dims']['tmp_mailing'][$new_env] = array();

				$handle = fopen ($_FILES['email_list']['tmp_name'], "r");

				//on test l'extension
				$extension	= explode(".", $_FILES['email_list']['name']);
				$extension	= $extension[count($extension)-1];
				$extension	= strtolower($extension);

				if($extension == 'txt') {
					while ($line = fgets($handle, 4096))
					{
						$fields = analyzeEmailsExpression($line);
						if(!empty($fields)) {

							$_SESSION['dims']['tmp_mailing'][$new_env][] = current($fields);

							$nb_imp++;
						}
					}
					fclose ($handle);
				}
				elseif($extension == 'xls' || $extension == 'xlsx') {

					//pas besoin ici, on va enregistrer directment $_SESSION['dims']['import_current_mailing'] = array();
					$created = array();
					$_FIELDS = array();

					$file	= $_FILES['email_list']['tmp_name'];

					/** PHPExcel_IOFactory */
					require_once(DIMS_APP_PATH . '/include/PHPExcel/IOFactory.php');

					$liste_version["csv"]	= "CSV";
					$liste_version["xlsx"]	= "Excel2007";
					$liste_version["xls"]	= "Excel5";

					//on instancie un objet de lecture
					$objReader = PHPExcel_IOFactory::createReader($liste_version[$extension]);
					//on charge le fichier qu'on veut lire
					$objPHPExcel = PHPExcel_IOFactory::load($file);
					//printf(" %d <br>",memory_get_usage());
					$alphabet[1]	= "A";
					$alphabet[]		= "B";
					$alphabet[]		= "C";
					$alphabet[]		= "D";
					$alphabet[]		= "E";
					$alphabet[]		= "F";
					$alphabet[]		= "G";
					$alphabet[]		= "H";
					$alphabet[]		= "I";
					$alphabet[]		= "J";
					$alphabet[]		= "K";
					$alphabet[]		= "L";
					$alphabet[]		= "M";
					$alphabet[]		= "N";
					$alphabet[]		= "O";
					$alphabet[]		= "P";
					$alphabet[]		= "Q";
					$alphabet[]		= "R";
					$alphabet[]		= "S";
					$alphabet[]		= "T";
					$alphabet[]		= "U";
					$alphabet[]		= "V";
					$alphabet[]		= "W";
					$alphabet[]		= "X";
					$alphabet[]		= "Y";
					$alphabet[]		= "Z";

					$obj_all_sheets	= $objPHPExcel->getAllSheets();
					$nb_row			= $obj_all_sheets[0]->getHighestRow();			//Nombre de ligne
					$Column_max		= $obj_all_sheets[0]->getHighestColumn();//Nombre de cellule
					if (strlen($Column_max) > 2)
						$Column_max = "AZ";

					$nb_Column		= strlen($Column_max);
					//echo "Column max : ".$Column_max."<br/>";

					if ($nb_Column > 1) {
						$last_ocurence	= substr($Column_max, -1);
					}else{
						$last_ocurence	= $Column_max;
					}

					$fist_lettre	= "";

					//L'on parce l'alphabet
					$c=0;
					$d=0;
					$fist_lettre = "";
					$lettre = "";
					$content = array();
					while ($fist_lettre.$lettre != $Column_max){
						$c++;
						$lettre = $alphabet[$c];
						if ($objPHPExcel->getActiveSheet()->cellExists($fist_lettre.$lettre."1"))
							$content[$fist_lettre.$lettre] = $objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre."1")->getValue();
						//$content[$fist_lettre.$lettre] = strtolower(trim(str_replace('"','',$content[$fist_lettre.$lettre])));
						//Si l'on arrive sur la derniere colone l'on arret
						if ($alphabet[$c] == "Z") {
							$d++;
							$c = 0;
							$fist_lettre = $alphabet[$d];
						}
					}

					foreach($content AS $key => $value){
						//echo $value."<br/>";

						//On vérifie si on connait la clé
						$val = dims_convertaccents($value);
						$val = strtolower($val);

						switch($val){

							case "email":
							case "email address":
							case "e-mail address":
							case "courriel":
							case "emailaddress":
							case "mail":
							case "adressedemessagerie":
								for($i=2; $i <= $nb_row; $i++){
									if ($objPHPExcel->getActiveSheet()->cellExists($key.$i)) {
										$fields_analyse = analyzeEmailsExpression($objPHPExcel->getActiveSheet()->getCell($key.$i)->getValue());
										if (!empty($fields_analyse))
											$_SESSION['dims']['tmp_mailing'][$new_env][] = current($fields_analyse);
									}
								}
								$value = "email";
							break;

						}
						$_FIELDS[$key] = $value;
					}

					unset($obj_all_sheets);
					unset($objPHPExcel);
				}else
					$_SESSION['dims']['mailing']['file'] = '';

			}

			if($list_att > 0) {
				$db->query("INSERT INTO `dims_mailing_content_list` (`id_content`, `id_list`) VALUES ( :newenv , :listatt )", array(
					':newenv'	=> $new_env,
					':listatt'	=> $list_att
				));
			}

			dims_redirect($scriptenv."?action=add_sending&id_env=".$env->fields['id']);
		}else{
			$_SESSION['dims']['mailing']['content'] = dims_load_securvalue('env_content', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			dims_redirect($scriptenv."?action=add_sending");
		}


		break;

	case 'delete_mail_list':
		$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		if ($id_env > 0){
			if ($id_mail > 0){
				unset($_SESSION['dims']['tmp_mailing'][$id_env][$id_mail-1]);
			}elseif($id_mail == 0){
				unset($_SESSION['dims']['tmp_mailing'][$id_env]);
			}
		}

		dims_redirect($scriptenv."?action=add_sending&id_env=".$id_env);
		break ;

	case 'previewiframe':
		ob_end_clean();

		$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);

		$env = new list_diff_content();

		$env->open($id_env);

		//ici le contenu
		$content='';
		$tab_background=''; // img de fond utilise
		$root_path='';
		if ($env->fields['template']!='') {
			//$tab_inf='';
			$template_name = $env->fields['template'];
			require_once(DIMS_APP_PATH . '/modules/system/desktop_mailinglist_build_template.php');
			$message = $content;
		}
		else {
			$content = $env->fields['content'];
			$message = '<html>
							<head></head>
							<body>'.$content.'
							</body>
						</html>';
		}
		echo $message;
		die();
		break;
	case 'previewnewsletter':
		ob_end_clean();
		$skin = new skin();
		$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);

		echo $skin->open_widgetbloc($_DIMS['cste']['_PREVIEW'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', "./common/img/close.png", '26px', '26px', '-10px', '-2px', 'javascript:dims_hidepopup(\'dims_popup\');', '', '');
		echo "<div style=\"width:100%;overflow:auto;\">";
		$url="/admin.php?action=previewiframe&id_env=".$id_env;
		echo "<iframe id=\"wce_frame_editor\" style=\"border:0;width:100%;height:600px;margin:0;padding:0;\" src=\"$url\"></iframe>";
		echo "</div>";
		echo $skin->close_widgetbloc();
		die();
		break;
	case 'send_mail':

		require_once DIMS_APP_PATH.'include/functions/mail.php';

		$id_env = dims_load_securvalue('id_env', dims_const::_DIMS_NUM_INPUT, true, true);

		$env = new list_diff_content();

		$env->open($id_env);

		//ici le contenu
		$content='';
		$tab_background=''; // img de fond utilise
		$root_path='';
		//on selectionne les listes d'emails utiles pour l'envoi
		$sql =	"SELECT			e.*,
								c.firstname,
								c.lastname
				 FROM			dims_mailing_email e

				 INNER JOIN		dims_mailing_content_list cl
				 ON				cl.id_list = e.id_list
				 AND			cl.id_content = :idenv

				 LEFT JOIN		dims_mod_business_contact c
				 ON				c.id = e.id_contact

				 WHERE			e.actif = 1";

		$res = $db->query($sql, array(
			':idenv' => $id_env
		));

		while($tab_e = $db->fetchrow($res)) {
			if($tab_e['id_contact'] == '') {
				$list_email[$tab_e['id']] = $tab_e['email'];
			}
			else {

				//on doit aller chercher l'email
				$ct = new contact();
				$ct->open($tab_e['id_contact']);

				if($ct->fields['email'] != '') {
					$list_email[$tab_e['id']] = $ct->fields['email'];
				}
				else {
					//on verifie si on a un mail dans les layers
					$sql_sub = 'SELECT		cl.email, cl.id
								FROM		dims_mod_business_contact ct
								INNER JOIN	dims_mod_business_contact_layer cl
								ON			cl.id = ct.id
								AND			cl.type_layer = 1
								AND			cl.id_layer = :idlayer
								AND			cl.email NOT LIKE \'\'
								WHERE		ct.id = :id ';

					$res_sub = $db->query($sql_sub, array(
						':idlayer'	=> $_SESSION['dims']['workspaceid'],
						':id'		=> $tab_e['id_contact']
					));
					if($db->numrows($res_sub) > 0) {
						$tab_sub = $db->fetchrow($res_sub);
						$list_email[$tab_e['id']] = $tab_sub['email'];
					}
				}
			}
		}

		if ($env->fields['template']!='') {
			//$tab_inf='';
			$template_name = $env->fields['template'];
			require_once(DIMS_APP_PATH . '/modules/system/desktop_mailinglist_build_template.php');
			$message = $content;
		}
		else {
			$content = $env->fields['content'];
			$message = '<html>
									<head></head>
									<body>'.$content.'
									</body>
								</html>';
		}

		require_once DIMS_APP_PATH.'include/functions/files.php';
		//maintenant la piece jointe
		$tab_pj = dims_getFiles($dims,$_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_LIST_DIFF,$id_env);

		//on a besoin du path exact pour le fichier
		$doc = new docfile();
		$doc->open($tab_pj[0]['id']);
		$path = $doc->getfilepath();

		//on fait l'envoi
		$email = '';
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$email = $work->fields['events_sender_email'];
		if ($email=="") $email=_DIMS_ADMINMAIL;
		$from[0]['name']   = '';
		$from[0]['address']= $email;

		$subject = $env->fields['subject'];

		$file = array();


		// image de fond inséré dans le doc
		if (!empty($tab_background)) {
			foreach ($tab_background as $webpathimg) {
				// on extrait le nom de l'image et on refait le path physique
				$path=str_replace($root_path,realpath('.'), $webpathimg);
				$pos=strrpos($path,"/");

				$name=substr($path,$pos+1);
				// on ajoute le document en piece jointe et on regarde
				$elemdoc=array();
				$elemdoc['name'] = $name;
				$elemdoc['filename'] = $path;
				$elemdoc['mime-type'] = mime_content_type($path);
				$elemdoc['type']='image';
				$file[]=$elemdoc; // on ajoute le fichier image en tant que piece jointe

				// on doit remplace le chemin web par le nom local à l'email
				$message=str_replace($webpathimg,$name,$message);
			}
		}

		$elemdoc=array();
		if(!empty($tab_pj)) {
			$elemdoc['name'] = $tab_pj[0]['name'];
			$elemdoc['filename'] = $path;
			//le mime-type
			$elemdoc['mime-type'] = mime_content_type($path);
		}
		$file[]=$elemdoc;

		if(!empty($list_email)) {
			foreach($list_email as $id_mail => $addemail) {
				if($addemail != '') {
					$to[0]['name'] = '--';
					$to[0]['address'] = $addemail;

					dims_send_mail_with_files($from, $to, $subject, $message, $file);
				}
			}
		}
		if(!empty($_SESSION['dims']['tmp_mailing'][$id_env])) {
			foreach($_SESSION['dims']['tmp_mailing'][$id_env] as $id_mail => $addemail) {
				if($addemail != '') {
					$to[0]['name'] = '--';
					$to[0]['address'] = $addemail;

					dims_send_mail_with_files($from, $to, $subject, $message, $file);
				}
			}
			unset($_SESSION['dims']['tmp_mailing'][$id_env]);
		}

		$env->fields['date_envoi'] = date("YmdHis");

		$env->save();

		dims_redirect($scriptenv);

		break;

/***********  Listes de diffusion  ************/
	case "list_mailinglist":
			//Recherche des derniers envois effectues
			$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);

			if ($id_mail==0) {
				$sql_ml =	"SELECT		l.label,
													l.id
							 FROM		dims_mailing_list l
							 WHERE		l.id_workspace = :workspaceid$
							 AND		l.id_user = :userid ";

				$res_ml = $db->query($sql_ml, array(
					':workspaceid'	=>  $_SESSION['dims']['workspaceid'],
					':userid'		=>  $_SESSION['dims']['userid']
				));

				if($db->numrows($res_ml) > 0) {
					if ($tab_c = $db->fetchrow($res_ml)) {
						dims_redirect("/admin.php?action=add_mailinglist&id_mail=".$tab_c['id']);
					}
				}
				else {
					// formulaire d'ajout
					dims_redirect("/admin.php?action=add_mailinglist");
				}
			}
			require_once(DIMS_APP_PATH . "/modules/system/desktop_mailinglist_left.php");
			break;

		case 'delete_listdiff':
			$id_list = dims_load_securvalue('id_list', dims_const::_DIMS_NUM_INPUT, true, true);

			$list = new list_diff();
			$list->open($id_list);

			//on supprime tous les emails rattaches
			$db->query("DELETE FROM dims_mailing_email WHERE id_list = :idlist ", array(
				':idlist' => $id_list
			));

			//on supprime tous les liens vers cette liste
			$db->query("DELETE FROM dims_mailing_content_list WHERE id_list = :idlist ", array(
				':idlist' => $id_list
			));

			//on supprime la liste
			$list->delete();

			dims_redirect($scriptenv.'?action=list_mailinglist');
		break;

	case 'add_contact':

		$id_list = dims_load_securvalue('id_mail',dims_const::_DIMS_CHAR_INPUT,true,true);
		//$id_contact = dims_load_securvalue('id_contact',dims_const::_DIMS_CHAR_INPUT,false,true);

		$idcontacts = dims_load_securvalue($_POST, dims_const::_DIMS_NUM_INPUT, true, true, true);
		foreach ($idcontacts as $id_contact){

			$email = new list_diff_email();
			$email->init_description();

			$email->fields['id_list'] = $id_list;
			$email->fields['id_contact'] = $id_contact;
			$email->fields['actif'] = 1;
			$email->fields['date_creation'] = date("YmdHis");

			//on recherche l'email du contact
			//cas 1 : l'adresse email est publique (elle se trouve directement dans la table contact)
			$contact = new contact();
			$contact->open($id_contact);


			if($contact->fields['email'] == '') {
				//cas 2 : on regarde si un email est present dans les layers
				$sql_sub = 'SELECT		DISTINCT cl.email, cl.id
							FROM		dims_mod_business_contact ct
							INNER JOIN	dims_mod_business_contact_layer cl
							ON			cl.id = ct.id
							AND			cl.type_layer = 1
							AND			cl.id_layer = :idlayer
							WHERE		ct.id = :id ';

				$res_sub = $db->query($sql_sub, array(
					':idlayer'	=> $_SESSION['dims']['workspaceid'],
					':id'		=> $id_contact
				));
				$tab_sub = $db->fetchrow($res_sub);

				$email->fields['email'] = '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$id_contact.'">'.$contact->fields['firstname']." ".$contact->fields['lastname'].'</a>';

			}
			else {
				$tab_res = $db->fetchrow($res1);
				$email->fields['email'] = '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$id_contact.'">'.$contact->fields['firstname']." ".$contact->fields['lastname'].'</a>';
			}

			$email->save();
		}

		dims_redirect($scriptenv."?action=add_mailinglist&id_mail=$id_list");

		break;

	case 'list_search_contact' : //ATTENTION ce case n'a pas de break car il va avec le case add_mailinglist
		$search = dims_load_securvalue('ct_search',dims_const::_DIMS_CHAR_INPUT,true,true);
		$id_list = dims_load_securvalue('id_mail',dims_const::_DIMS_CHAR_INPUT,true,true);

		$view = '';
		if(isset($search) && $search != '') {
			$sql_sch = 'SELECT * FROM dims_mod_business_contact WHERE inactif = 0 AND (lastname LIKE :search OR firstname LIKE :search ) ORDER BY lastname, firstname';

			$res_sch = $db->query($sql_sch,array(
				':search' => $search."%"
			));

			if($db->numrows($res_sch) > 0) {
				$clas = 'trl1';
				$view .= '	<tr>
								<td colspan="2">
									<form style="float: left; width:100%; height:250px; overflow-x: auto;" method="POST" id="valid_insc" name="valid_insc" action="'.$scriptenv.'?action=add_contact&id_mail='.$id_list.'">';
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$view .= '			<table width="100%" cellpadding="0" cellspacing="0">
										<tr class="trl1" style="font-size:14px;">
											<td>'.$_DIMS['cste']['_DIMS_LABEL_CONTACTS'].'</td>
											<td>'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
											<td></td>
										</tr>';
				while($tab_sch = $db->fetchrow($res_sch)) {

					$nom = '';
					$nom .= '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$tab_sch['id'].'">'.strtoupper($tab_sch['lastname']).' '.$tab_sch['firstname'].'</a>';

					if($tab_sch['email'] == '') {
						//on regarde s'il y a qqchose dans le layer
						$sql_sub = 'SELECT		DISTINCT cl.email, cl.id
									FROM		dims_mod_business_contact ct
									INNER JOIN	dims_mod_business_contact_layer cl
									ON			cl.id = ct.id
									AND			cl.type_layer = 1
									AND			cl.id_layer = :idlayer
									AND			cl.email NOT LIKE \'\'
									WHERE		ct.id = :id ';

						$res_sub = $db->query($sql_sub, array(
							':idlayer'	=> $_SESSION['dims']['workspaceid'],
							':id'		=> $tab_sch['id']
						));
						if($db->numrows($res_sub) > 0) {
							if($clas == 'trl1') $clas = 'trl2';
							else $clas = 'trl1';
							$tab_ct = $db->fetchrow($res_sub);
							$view .= '		<tr class="'.$clas.'">
												<td>'.$nom.'</td>
												<td>'.$tab_ct['email'].'</td>
												<td><input type="checkbox" name="id_contact_'.$tab_ct['id'].'" id="id_contact_'.$tab_ct['id'].'" value="'.$tab_ct['id'].'"/></td>
											</tr>';
							$token->field("id_contact_".$tab_ct['id']);
						}
					}
					else {
						if($clas == 'trl1') $clas = 'trl2';
						else $clas = 'trl1';
						$view .= '			<tr class="'.$clas.'">
												<td>'.$nom.'</td>
												<td>'.$tab_sch['email'].'</td>
												<td><input type="checkbox" name="id_contact_'.$tab_sch['id'].'" id="id_contact_'.$tab_sch['id'].'" value="'.$tab_sch['id'].'"/></td>
											</tr>';
						$token->field("id_contact_".$tab_sch['id']);
					}



				}
				$view .=			'</table>';

				$tokenHTML = $token->generate();
				$view .= $tokenHTML;
				$view .=		'</form>
								</td>
							</tr>
							<tr>
								<td colspan="3">
								'.dims_create_button($_DIMS['cste']['_DIMS_VALID'], './common/img/publish.png', 'javascript:document.valid_insc.submit();', '', 'float:right;margin-top:10px;').'
								</td>
							</tr>';
			}
			else {
				$view .= '<tr><td style="font-size:13px;color:#ff0000;">'.substr($_DIMS['cste']['_DIMS_LABEL_NO_SIMILAR'], 0, -43).'.</td></tr>';
			}
		}
		//pas de break car on fait aussi le cas add_mailinglist
	case 'add_mailinglist':

		require_once(DIMS_APP_PATH . '/modules/system/desktop_mailinglist_add_list.php');

		break;
	case 'save_mailinglist':

		$id_list = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);

		if (isset($_POST['list_label']) && $_POST['list_label'] != ''){
			$list = new list_diff();
			if($id_list != 0) {
				$list->open($id_list);
			}
			else {
				$list->setvalues($_POST, 'list_');
				$list->fields['date_create'] = date("YmdHis");
			}
			$list->setugm();

			$list->save();

			dims_redirect($scriptenv.'?action=add_mailinglist&id_mail='.$list->fields['id']);
		}else{
			dims_redirect($scriptenv.'?action=add_mailinglist&id_mail=0');
		}
		break;

	case 'save_email':

		$id_list = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		$mail = dims_load_securvalue('add_mail', dims_const::_DIMS_CHAR_INPUT, true, true);

		$fields_analyse = analyzeEmailsExpression($mail);
		if (!empty($fields_analyse)){
			$email = new list_diff_email();
			$email->fields['email'] = $mail;
			$email->fields['id_list'] = $id_list;
			$email->fields['date_creation'] = date("YmdHis");
			$email->fields['actif'] = 1;

			$email->save();
		}else
			$_SESSION['dims']['mailing']['erreur_mail'] = $mail;

		dims_redirect($scriptenv."?action=add_mailinglist&id_mail=".$id_list);

		break;

	case 'change_state_email':
		//changer l'etat d'un email
		$id_mail_mod = dims_load_securvalue('id_state_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_list = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);

		$email = new list_diff_email();
		$email->open($id_mail_mod);
		$email->fields['actif'] = ($email->fields['actif'] == 1) ? 0 : 1;
		$email->save();

		dims_redirect($scriptenv."?action=add_mailinglist&id_mail=".$id_list);

		break;

	case 'delete_email':
		$id_supp_mail = dims_load_securvalue('id_supp_mail', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_list = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);

		$email = new list_diff_email();
		$email->open($id_supp_mail);
		$email->delete();

		dims_redirect($scriptenv."?action=add_mailinglist&id_mail=".$id_list);

		break;

	case 'import_email':
		ini_set('memory_limit','512M');

		$id_mail_f = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);

		if($_FILES['doc_mail']['name'] != '' && $id_mail_f != '')
		{
			$handle = fopen ($_FILES['doc_mail']['tmp_name'], "r");

			//on test l'extension
			$extension	= explode(".", $_FILES['doc_mail']['name']);
			$extension	= $extension[count($extension)-1];
			$extension	= strtolower($extension);

			if($extension == 'txt') {
				$nb_imp = 0;
				while ($line = fgets($handle, 4096))
				{
					$fields = analyzeEmailsExpression($line);
					if(!empty($fields)) {
						$email = new list_diff_email();
						$email->init_description();
						$email->fields['id_list'] = $id_mail_f;
						$email->fields['date_creation'] = date("YmdHis");
						$email->fields['actif'] = 1;
						$email->fields['email'] = current($fields);
						$email->save();

						$nb_imp++;
					}
				}
				fclose ($handle);
			}
			elseif($extension == 'xls' || $extension == 'xlsx') {

				//pas besoin ici, on va enregistrer directment $_SESSION['dims']['import_current_mailing'] = array();
				$created = array();
				$errors = array();
				$_FIELDS = array();

				$file	= $_FILES['doc_mail']['tmp_name'];

				/** PHPExcel_IOFactory */
				require_once(DIMS_APP_PATH . '/include/PHPExcel/IOFactory.php');

				$liste_version["csv"]	= "CSV";
				$liste_version["xlsx"]	= "Excel2007";
				$liste_version["xls"]	= "Excel5";

				//on instancie un objet de lecture
				$objReader = PHPExcel_IOFactory::createReader($liste_version[$extension]);
				//on charge le fichier qu'on veut lire
				$objPHPExcel = PHPExcel_IOFactory::load($file);
				//printf(" %d <br>",memory_get_usage());
				$alphabet[1]	= "A";
				$alphabet[]		= "B";
				$alphabet[]		= "C";
				$alphabet[]		= "D";
				$alphabet[]		= "E";
				$alphabet[]		= "F";
				$alphabet[]		= "G";
				$alphabet[]		= "H";
				$alphabet[]		= "I";
				$alphabet[]		= "J";
				$alphabet[]		= "K";
				$alphabet[]		= "L";
				$alphabet[]		= "M";
				$alphabet[]		= "N";
				$alphabet[]		= "O";
				$alphabet[]		= "P";
				$alphabet[]		= "Q";
				$alphabet[]		= "R";
				$alphabet[]		= "S";
				$alphabet[]		= "T";
				$alphabet[]		= "U";
				$alphabet[]		= "V";
				$alphabet[]		= "W";
				$alphabet[]		= "X";
				$alphabet[]		= "Y";
				$alphabet[]		= "Z";

				$obj_all_sheets	= $objPHPExcel->getAllSheets();
				$nb_row			= $obj_all_sheets[0]->getHighestRow();			//Nombre de ligne
				$Column_max		= $obj_all_sheets[0]->getHighestColumn();//Nombre de cellule
				$nb_Column		= strlen($Column_max);
				//echo "Column max : ".$Column_max."<br/>";

				if ($nb_Column > 1) {
					$last_ocurence	= substr($Column_max, -1);
				}else{
					$last_ocurence	= $Column_max;
				}

				$fist_lettre	= "";

				//L'on parce l'alphabet
				$c=0;
				$d=0;
				$fist_lettre = "";
				$lettre = "";
				while ($fist_lettre.$lettre != $Column_max){
					$c++;
					$lettre = $alphabet[$c];
					$content[$fist_lettre.$lettre] = utf8_decode($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre."1")->getValue());
					$content[$fist_lettre.$lettre] = strtolower(trim(str_replace('"','',$content[$fist_lettre.$lettre])));
					//Si l'on arrive sur la derniere colone l'on arret
					if ($alphabet[$c] == "Z") {
						$d++;
						$c = 0;
						$fist_lettre = $alphabet[$d];
					}
				}

				foreach($content AS $key => $value){
					//echo $value."<br/>";

					//On vérifie si on connait la clé
					$val = dims_convertaccents($value);

					switch($val){

						case "email":
						case "email address":
						case "e-mail address":
						case "courriel":
						case "emailaddress":
						case "mail":
						case "adressedemessagerie":
							$value = "email";
						break;

					}
					$_FIELDS[$key] = $value;
				}
				//Boucle sur le nombre de ligne
				for ($i=2; $i <= $nb_row; $i++){
					//printf("%d %d <br>",$i,memory_get_usage());
					$c=0;
					$d=0;
					$fist_lettre = "";
					$lettre = "";
					while ($fist_lettre.$lettre != $Column_max){
						$c++;
						$lettre = $alphabet[$c];
						$fields_analyse = analyzeEmailsExpression(utf8_decode($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$i)->getValue()));
						if(!empty($fields_analyse)) {
							//$_SESSION['dims']['import_current_mailing'][$i][$_FIELDS[$fist_lettre.$lettre]] = $fields_analyse;

							$email = new list_diff_email();
							$email->init_description();
							$email->fields['id_list'] = $id_mail_f;
							$email->fields['date_creation'] = date("YmdHis");
							$email->fields['actif'] = 1;
							$email->fields['email'] = current($fields_analyse);
							$email->save();

							$nb_imp++;
						}

						//Si l'on arrive sur la derniere colone l'on arret
						if ($alphabet[$c] == "Z") {
							$d++;
							$c = 0;
							$fist_lettre = $alphabet[$d];
						}
					}
				}

		//					  print "</pre>";
				unset($obj_all_sheets);
				unset($objPHPExcel);
			}
			else {
				$error_imp = $_DIMS['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'];
			}
		}
		else {
			$error_imp = $_DIMS['cste']['_DOC_LABEL_FILESNOFOUND'];
		}

		dims_redirect($scriptenv."?action=add_mailinglist&id_mail=".$id_mail_f);

		break;
/***********  Accueil  ************/

	default:

		require_once(DIMS_APP_PATH . "/modules/system/desktop_mailinglist_left.php");
	break;
}



?>
