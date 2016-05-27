<?php
require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
require_once(DIMS_APP_PATH . '/modules/system/class_dossier.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
require_once(DIMS_APP_PATH . '/modules/system/class_action_detail.php');
require_once(DIMS_APP_PATH . '/modules/system/class_user_planning.php');
require_once(DIMS_APP_PATH . '/modules/system/class_business_metacateg.php');
require_once(DIMS_APP_PATH . '/modules/system/class_action_etap.php');
require_once(DIMS_APP_PATH . '/modules/system/class_action_etap_file.php');
require_once(DIMS_APP_PATH . '/modules/system/class_event_inscription.php');

$groups = dims_viewworkspaces($_SESSION['dims']['moduleid']);

if (!isset($_SESSION['business']['cat'])) $_SESSION['business']['cat']=_BUSINESS_CAT_ACCUEIL;
if (!isset($_SESSION['dims']['eventstep'])) $_SESSION['dims']['eventstep']=0;

$cat=dims_load_securvalue("cat",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['business']['cat'],0);
switch($_SESSION['business']['cat']) {
	case _BUSINESS_CAT_ACCUEIL:
	$op = dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,false);
		switch($op) {
			case 'add_dmd_insc':
			$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, false, true);
			$id_action = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, false, true);
			//echo 'id_user : '.$id_user."<br/> id_action".$id_action; die();

				if($id_action > 0) {
					$act_usr = new action_user();
					$_SESSION['dims']['alert_user'] = "";
					//il faudrait tester si le user n'a pas dejà une action à faire au même moment...
					if($act_usr->open($id_action,$id_user)) {
						$_SESSION['dims']['alert_user'] = 1;
						dims_redirect('http://'.$_SERVER['HTTP_HOST'].'/admin.php?dims_mainmenu'.dims_const::_DIMS_SUBMENU_EVENT.'=&dims_desktop=block&dims_action=public&message=1');
						//dims_redirect($scriptenv."?message=1");
					}
					else {
						$act_usr->init_description();
						$act_usr->fields['user_id'] = $id_user;
						$act_usr->fields['action_id'] = $id_action;
						$act_usr->fields['participate'] = 0;
						$act_usr->fields['date_demande'] = date("YmdHis");
						$act_usr->save();
						$_SESSION['dims']['alert_user'] = 2;
						dims_redirect('http://'.$_SERVER['HTTP_HOST'].'/admin.php?dims_mainmenu'.dims_const::_DIMS_SUBMENU_EVENT.'=&dims_desktop=block&dims_action=public&message=2');
						//dims_redirect($scriptenv."?message=2");
					}

				}
			break;
			case "add_ct":
				if(!isset($_SESSION['dims']['planning_addct'])) $_SESSION['dims']['planning_addct'] = array();
				$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				//on met les id_ct en session, il seront enregistr&eacute;s dans le save du planning
				$_SESSION['dims']['planning_addct'][] = $id_ct;
			break;
			case "search_ct" :
			ob_start();

			//recuperation du parametre
			$search = dims_load_securvalue('search', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$id_action = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$tab_not = '';

			//contact venant d'être ajout&eacute;s
			if(isset($_SESSION['dims']['planning_addct'])) {
				$tab_not .= implode("','",$_SESSION['dims']['planning_addct']);
			}
			//on regarde d'abbord tous les contacts inscrits
			if(isset($id_action) && $id_action != 0) {
				$sqlinsc = "SELECT id_contact FROM dims_mod_business_event_inscription WHERE id_action = :idaction";

				$resinsc = $db->query($sqlinsc, array(':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action)));
				if($db->numrows($resinsc) > 0) {
					$tab_i = '';
					while($tab_insc = $db->fetchrow($resinsc)) {
						$tab_i[] = $tab_insc['id_contact'];
					}
					$tab_not .= implode("','",$tab_i);
				}
			}
			//requete de recherche : chaine%
			$params = array();
			$sql = 'SELECT
						ct.id AS id_ct,
						ct.lastname,
						ct.firstname,
						ct.email AS email_ct,
						u.id,
						u.email AS email_user
					FROM
						dims_mod_business_contact ct
					LEFT JOIN
						dims_user u
						ON
							u.id_contact = ct.id
					WHERE';
			if($tab_not != '')
				$sql .=		' ct.id NOT IN (\''.$db->getParamsFromArray(explode(',', $tab_not), 'tabnot', $params).'\') AND (';
			$sql .=			' ct.lastname LIKE :search
						OR
							ct.firstname LIKE :search';
			if($tab_not != '')
				$sql .=		')';
			$params[':search'] = array('type' => PDO::PARAM_STR, 'value' => $search.'%');
			$ress = $db->query($sql, $params);

			if($db->numrows($ress) > 0) { //On a des contact

				//R&eacute;cup&eacute;ration de la liste des user du workspace
				$ws= new workspace();
				$ws->open($_SESSION['dims']['workspaceid']);

				$lstusers=$ws->getusers();

				echo '<table style="width: 100%; border-collapse: collapse;">';
				$class = 'trl1';
				while($info = $db->fetchrow($ress)) {
					//Si le contact(Avec user) n'est pas d&eacute;jà dans la liste des WSusers
					if(!(isset($info['id']) && isset($lstusers[$info['id']]))) {
						$icon = '';
						$js = '';

						echo '<tr class="'.$class.'">';
						echo '<td>';
						$icon = '<img src="./data/users/icon_EFEFEF.png" alt="" border="0" />';
						echo $icon;
						echo '&nbsp;'.strtoupper(substr($info['firstname'],0,1)).'. '.$info['lastname'];
						echo '</td>';
						echo '<td>';

						if(isset($info['email_user']) && !empty($info['email_user']))
							echo $info['email_user'];
						elseif(isset($info['email_ct']) && !empty($info['email_ct']))
							echo $info['email_ct'];

						echo '</td>';
						echo '<td>';

						if((isset($info['email_ct']) && !empty($info['email_ct'])) ||
						   isset($info['email_user'])&& !empty($info['email_user'])) {
								//$js = "document.location.href='admin.php?dims_mainemenu=".dims_const::_DIMS_MENU_PLANNING."&op=add_ct&id_ct=".$info['id_ct']."';";
								$js = "dims_xmlhttprequest('admin.php', 'dims_mainemenu=".dims_const::_DIMS_MENU_PLANNING."&op=add_ct&id_ct=".$info['id_ct']."');upKeysearch()";
						   }

						echo '<a href="javascript: void(0);" onclick="javascript: '.$js.'">';
						echo '<img src="./common/img/add_user.png" alt="'.$_DIMS['cste']['_ATTACH'].'" border="0" />';
						echo '</a>';
						echo '</td>';
						echo '</tr>';

						$class = ($class == 'trl1') ? 'trl2' : 'trl1';
					}
				}
				echo '</table>';
			}

			ob_end_flush();
			die();
			break;
			case 'export_odt':
				ob_end_clean();
				require_once DIMS_APP_PATH . '/modules/system/xml_planning_event_odt.php';
				die();
				break;
			case 'update_actionetap':
				ob_end_clean();
				$num = dims_load_securvalue('step',dims_const::_DIMS_NUM_INPUT,true,true,false);
				$_SESSION['dims']['eventstep']=$num;
				ob_flush();
				die();
				break;
			case 'refresh_etap':
				ob_end_clean();
				ob_start();

				require_once DIMS_APP_PATH . '/modules/system/xml_planning_event_etap.php';

				ob_flush();
				die();
				break;
			case 'create_clone':
				$id_action = dims_load_securvalue('action_id', dims_const::_DIMS_NUM_INPUT, true, true);

				$act_tocopy = new action();
				$act_tocopy->open($id_action);

				//creation de l'action modele parente
				$newmod_act = new action();
				$newmod_act->init_description();

				$newmod_act->fields = $act_tocopy->fields;

				$newmod_act->fields['id'] = '';
				$newmod_act->fields['id_parent'] = 0;
				$newmod_act->fields['is_model'] = 0;
				$newmod_act->fields['libelle'] = $act_tocopy->fields['libelle'].' (copy)';
				$newmod_act->fields['datejour'] = date("Y")."-".date("m")."-".date("d"); //la date sera modifiee par l'utilisateur
				$newmod_act->fields['datefin'] = date("Y")."-".date("m")."-".date("d");
				$newmod_act->fields['id_user'] = $_SESSION['dims']['userid'];
				$newmod_act->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

				$newmod_act->save();

				//on recherche les docs attachés (brochures, bandeau, matchmaking)
			//le bandeau
				if(!empty($act_tocopy->fields['banner_path'])) {
					//on crée une copie de l'image
					//on reprend l'ancien nom de l'image
					$tab_imgpath = explode('/',$act_tocopy->fields['banner_path']);
					$img_name_with_ext = end($tab_imgpath);
					$tab_img = explode('.',$img_name_with_ext);
					$img_ext = end($tab_img);
					$img_name = $tab_img[0];

					//on ajoute des details pour identifier la copie
					$new_img_name = $img_name.'_copy_'.$newmod_act->fields['id'];

					//on cré la copie
					copy($act_tocopy->fields['banner_path'], DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext);

					//on indique le path dans le clone
					$newmod_act->fields['banner_path'] = DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext;
					$newmod_act->save();
				}
			//les documents liés / brochures
				$id_module=$_SESSION['dims']['moduleid'];
				$id_object=dims_const::_SYSTEM_OBJECT_EVENT;
				$id_record=$act_tocopy->fields['id'];

				require_once DIMS_APP_PATH.'include/functions/files.php';
				// collecte des fichiers existants
				$lstfiles=dims_getFiles($dims,$id_module,$id_object,$id_record);
				if(count($lstfiles)>0) {
					foreach ($lstfiles as $file) {
						$id_new_doc = 0;
						if($file['id'] > 0) {
							//on cree une copie du document
							$doc_tocopy = new docfile();
							$doc_tocopy->open($file['id']);

							$doc_tocp_path = $doc_tocopy->getfilepath();

							$newmodel_doc = new docfile();

							$newmodel_doc->fields['id_module'] = $_SESSION['dims']['moduleid'];
							$newmodel_doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$newmodel_doc->fields['id_record'] = $newmod_act->fields['id'];
							$newmodel_doc->fields['id_object'] = $id_object;
							$newmodel_doc->fields['id_folder'] = -1;
							$newmodel_doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
							$newmodel_doc->fields['id_user'] = $_SESSION['dims']['userid'];
							$newmodel_doc->fields['name'] = $doc_tocopy->fields['name'];
							$newmodel_doc->fields['size'] = $doc_tocopy->fields['size'];

							copy($doc_tocp_path, DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension']);
							$newmodel_doc->tmpuploadedfile = DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension'];

							$newmodel_doc->save();

							$id_new_doc = $newmodel_doc->fields['id'];
						}
					}
				}

			//document d'annonce
				if(!empty($act_tocopy->fields['preview_path'])) {
					//on crée une copie de l'image
					//on reprend l'ancien nom de l'image
					$tab_imgpath = explode('/',$act_tocopy->fields['preview_path']);
					$img_name_with_ext = end($tab_imgpath);
					$tab_img = explode('.',$img_name_with_ext);
					$img_ext = end($tab_img);
					$img_name = $tab_img[0];

					//on ajoute des details pour identifier la copie
					$new_img_name = $img_name.'_copy_prev_'.$newmod_act->fields['id'];

					//on cré la copie
					copy($act_tocopy->fields['preview_path'], DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext);

					//on indique le path dans le clone
					$newmod_act->fields['preview_path'] = DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext;
					$newmod_act->save();
				}
			//matchmaking
				if(!empty($act_tocopy->fields['matchmaking_path'])) {
					//on crée une copie de l'image
					//on reprend l'ancien nom de l'image
					$tab_imgpath = explode('/',$act_tocopy->fields['matchmaking_path']);
					$img_name_with_ext = end($tab_imgpath);
					$tab_img = explode('.',$img_name_with_ext);
					$img_ext = end($tab_img);
					$img_name = $tab_img[0];

					//on ajoute des details pour identifier la copie
					$new_img_name = $img_name.'_copy_ma_'.$newmod_act->fields['id'];

					//on cré la copie
					copy($act_tocopy->fields['matchmaking_path'], DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext);

					//on indique le path dans le clone
					$newmod_act->fields['matchmaking_path'] = DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext;
					$newmod_act->save();
				}

				dims_redirect('admin.php?op=xml_planning_modifier_action&id='.$newmod_act->fields['id']);
				break;
			case 'create_model':
				$id_action = dims_load_securvalue('action_id', dims_const::_DIMS_NUM_INPUT, true, true);

				$act_tocopy = new action();
				$act_tocopy->open($id_action);

				//creation de l'action modele parente
				$newmod_act = new action();
				$newmod_act->init_description();

				$newmod_act->fields = $act_tocopy->fields;

				$newmod_act->fields['id'] = '';
				$newmod_act->fields['id_parent'] = 0;
				$newmod_act->fields['is_model'] = 1;
				$newmod_act->fields['libelle'] = $act_tocopy->fields['libelle'].'_model';
				$newmod_act->fields['datejour'] = date("Y")."-".date("m")."-".date("d"); //la date sera modifiee par l'utilisateur
				$newmod_act->fields['datefin'] = date("Y")."-".date("m")."-".date("d");
				$newmod_act->fields['allow_fo'] = 0; //on met à 0 ici, mais il faudra remettre à 1 lorsque l'on créera un event à partir de ce model
				$newmod_act->fields['id_user'] = $_SESSION['dims']['userid'];
				$newmod_act->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

				$newmod_act->save();

				//recuperation des informations sur les etapes et documents rattachés
				$sql_act_etap = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

				$res_act_etap = $db->query($sql_act_etap, array(
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
					));
				while($tab_actionetap = $db->fetchrow($res_act_etap)) {
					//on duplique l'etape
					$actionetap = new action_etap();
					$actionetap->fields = $tab_actionetap;
					$actionetap->fields['id'] = '';
					$actionetap->fields['id_action'] = $newmod_act->fields['id'];
					$actionetap->save();

					//on recherche les documents attaches
					$sql_doc_etap = "SELECT * FROM dims_mod_business_event_etap_file WHERE id_etape = :idetape AND id_action = :idaction";

					$res_doc_etap = $db->query($sql_doc_etap, array(
						':idetape' => array('type' => PDO::PARAM_INT, 'value' => $tab_actionetap['id']),
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
					));

					if($db->numrows($res_doc_etap) > 0) {
						while($tab_docetap = $db->fetchrow($res_doc_etap)) {
							$id_new_doc = 0;
							if($tab_docetap['id_doc'] > 0) {
								//on cree une copie du document
								$doc_tocopy = new docfile();
								$doc_tocopy->open($tab_docetap['id_doc']);

								$doc_tocp_path = $doc_tocopy->getfilepath();

								$newmodel_doc = new docfile();

								$newmodel_doc->fields['id_module'] = $_SESSION['dims']['moduleid'];
								$newmodel_doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$newmodel_doc->fields['id_folder'] = -1;
								$newmodel_doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
								$newmodel_doc->fields['name'] = $doc_tocopy->fields['name'];
								$newmodel_doc->fields['size'] = $doc_tocopy->fields['size'];

								copy($doc_tocp_path, DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension']);
								$newmodel_doc->tmpuploadedfile = DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension'];

								$newmodel_doc->save();
								$id_new_doc = $newmodel_doc->fields['id'];
							}
							//on duplique l'action_etap_file
							$model_etap_file = new etap_file();

							$model_etap_file->fields = $tab_docetap;

							$model_etap_file->fields['id'] = '';
							$model_etap_file->fields['id_action'] = $newmod_act->fields['id'];
							$model_etap_file->fields['id_etape'] = $actionetap->fields['id'];
							if($id_new_doc > 0) $model_etap_file->fields['id_doc'] = $id_new_doc;

							$model_etap_file->save();
						}
					}
				}
				dims_redirect('admin.php?op=xml_planning_modifier_action&id='.$id_action);
				break;
			case 'import_etap_model':
				$id_action = $_SESSION['dims']['currentaction'];

				$typeMask = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT, true, true, false);

				$event = new action();
				$event->open($id_action);

				$fileModelePath = DIMS_APP_PATH . '/modules/system/modeles/fair/';

				//Etape resume
				$actionetap = new action_etap();
				$actionetap->fields['id_action']	= $id_action;
				$actionetap->fields['label']		= 'R&eacute;sum&eacute;';
				$actionetap->fields['description']	= '';
				$actionetap->fields['position']		= 1;
				$actionetap->fields['type_etape']	= 1;
				$actionetap->save();

				/*** Etape inscription ***/
				$actionetap = new action_etap();
				$actionetap->fields['id_action']	= $id_action;
				$actionetap->fields['label']		= 'Inscription';
				$actionetap->fields['description']	= "Cette &eacute;tape validera d&eacute;finitivement votre inscription en tant qu'exposant au pavillon luxembourgeois. Pour des raisons l&eacute;gales, l'original de ce document devra &ecirc;tre envoy&eacute; &agrave; la Direction du Commerce ext&eacute;rieur (DCE) par courrier postal.";
				$actionetap->fields['position']		= 2;
				$actionetap->fields['type_etape']	= 2;
				$actionetap->save();

				$filename = 'inscription.odt';
				$filename2 = 'inscription2.odt';
				copy($fileModelePath.$filename, $fileModelePath.$filename2);
				$docfile = new docfile();
				$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$docfile->fields['id_folder'] = -1;
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpuploadedfile = $fileModelePath.$filename2;
				$docfile->fields['name'] = $filename2;
				$docfile->fields['size'] = filesize($fileModelePath.$filename2);
				$docfile->save();

				$etap_file = new etap_file();
				$etap_file->fields['id_action']= $id_action;
				$etap_file->fields['id_etape']=$actionetap->fields['id'];
				$etap_file->fields['id_doc']=$docfile->fields['id'];
				$etap_file->save();

				$etap_file = new etap_file();
				$etap_file->fields['id_action']=  $id_action;
				$etap_file->fields['id_etape']= $actionetap->fields['id'];
				$etap_file->fields['id_doc']= 0;
				$etap_file->fields['label']= 'Document d\'inscription rempli';
				$etap_file->fields['content']= '';
				$etap_file->save();

				/*** Etape materiels ***/
				$actionetap = new action_etap();
				$actionetap->fields['id_action']	= $id_action;
				$actionetap->fields['label']		= 'Mat&eacute;riels';
				$actionetap->fields['description']	= "Description de votre mat&eacute;riel d'exposition et indication des &eacute;quipements &agrave; pr&eacute;voir au stand pour la pr&eacute;sentation de ce mat&eacute;riel. Afin d'incorporer au mieux vos besoins dans le concept du stand, la DCE incluera vos desideratas dans le cahier des charges relatif &agrave; la construction du pavillon Luxembourgeois.";
				$actionetap->fields['position']		= 3;
				$actionetap->fields['type_etape']	= 0;
				$actionetap->save();

				$filename = 'materiel.odt';
				$filename2 = 'materiel2.odt';
				copy($fileModelePath.$filename, $fileModelePath.$filename2);
				$docfile = new docfile();
				$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$docfile->fields['id_folder'] = -1;
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpuploadedfile = $fileModelePath.$filename2;
				$docfile->fields['name'] = $filename2;
				$docfile->fields['size'] = filesize($fileModelePath.$filename2);
				$docfile->save();

				$etap_file = new etap_file();
				$etap_file->fields['id_action']= $id_action;
				$etap_file->fields['id_etape']=$actionetap->fields['id'];
				$etap_file->fields['id_doc']=$docfile->fields['id'];
				$etap_file->save();

				$etap_file = new etap_file();
				$etap_file->fields['id_action']=  $id_action;
				$etap_file->fields['id_etape']= $actionetap->fields['id'];
				$etap_file->fields['id_doc']= 0;
				$etap_file->fields['label']= 'Document mat&eacute;riel rempli';
				$etap_file->fields['content']= '';
				$etap_file->save();

				/*** Etape brochures ***/
				if($typeMask == 1) {
					$actionetap = new action_etap();
					$actionetap->fields['id_action']	= $id_action;
					$actionetap->fields['label']		= 'Brochure';
					$actionetap->fields['description']	= "Une brochure sur la participation luxembourgeoise au format A5 sera compil&eacute;e par la DCE. Veuillez attacher votre logo, vos textes et vos photos au moyen des liens repris ci-dessous.";
					$actionetap->fields['position']		= 4;
					$actionetap->fields['type_etape']	= 0;
					$actionetap->save();
				}
				elseif($typeMask == 2) {
					$actionetap = new action_etap();
					$actionetap->fields['id_action']	= $id_action;
					$actionetap->fields['label']		= 'Brochure';
					$actionetap->fields['description']	= "Une brochure sur la participation luxembourgeoise au format “pocket” sera compil&eacute;e par la DCE. Veuillez attacher votre logo et vos textes au moyen des liens repris ci-dessous.";
					$actionetap->fields['position']		= 4;
					$actionetap->fields['type_etape']	= 0;
					$actionetap->save();
				}

				$filename = 'specimen_brochure_A5.pdf';
				$filename2 = 'specimen_brochure_A52.pdf';
				copy($fileModelePath.$filename, $fileModelePath.$filename2);
				$docfile = new docfile();
				$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$docfile->fields['id_folder'] = -1;
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpuploadedfile = $fileModelePath.$filename2;
				$docfile->fields['name'] = $filename2;
				$docfile->fields['size'] = filesize($fileModelePath.$filename2);
				$docfile->save();

				$etap_file = new etap_file();
				$etap_file->fields['id_action']= $id_action;
				$etap_file->fields['id_etape']=$actionetap->fields['id'];
				$etap_file->fields['id_doc']=$docfile->fields['id'];
				$etap_file->save();

				if($typeMask == 1) {
					$etap_file = new etap_file();
					$etap_file->fields['id_action']=  $id_action;
					$etap_file->fields['id_etape']= $actionetap->fields['id'];
					$etap_file->fields['id_doc']= 0;
					$etap_file->fields['label']= 'Texte descriptif en anglais';
					$etap_file->fields['content']= 'La taille de votre texte est fonction du nombre de photos que vous souhaitez ins&eacute;rer. Comme illustr&eacute; dans le sp&eacute;cimen ci-attach&eacute;, nous vous proposons trois formules sans que votre texte descriptif puisse d&eacute;passer 620 mots. Ledit texte en langue anglaise comprendra le descriptif de vos produits, vos coordonn&eacute;es compl&egrave;tes, y compris les noms et pr&eacute;noms des personnes de contact.';
					$etap_file->save();
				}
				elseif($typeMask == 2) {
					$etap_file = new etap_file();
					$etap_file->fields['id_action']=  $id_action;
					$etap_file->fields['id_etape']= $actionetap->fields['id'];
					$etap_file->fields['id_doc']= 0;
					$etap_file->fields['label']= 'Texte descriptif en anglais';
					$etap_file->fields['content']= 'Votre descriptif en langue anglaise et fran&ccedil;aise ne pourra d&eacute;passer 350 mots. Ce texte comprendra le descriptif de vos produits, vos coordonn&eacute;es compl&egrave;tes, y compris les noms et pr&eacute;noms des personnes de contact.';
					$etap_file->save();
				}

				$etap_file = new etap_file();
				$etap_file->fields['id_action']=  $id_action;
				$etap_file->fields['id_etape']= $actionetap->fields['id'];
				$etap_file->fields['id_doc']= 0;
				$etap_file->fields['label']= 'Photo';
				$etap_file->fields['content']= 'Vous avez la possibilit&eacute; d\'illustrer votre pr&eacute;sentation par au maximum 3 photos (format: "gif" ou "jpeg" / r&eacute;solution minimale: 300 dpi).';
				$etap_file->save();

				if($typeMask == 1) {
					$etap_file = new etap_file();
					$etap_file->fields['id_action']=  $id_action;
					$etap_file->fields['id_etape']= $actionetap->fields['id'];
					$etap_file->fields['id_doc']= 0;
					$etap_file->fields['label']= 'Texte descriptif en fran&ccedil;ais';
					$etap_file->fields['content']= 'La taille de votre texte est fonction du nombre de photos que vous souhaitez ins&eacute;rer. Comme illustr&eacute; dans le sp&eacute;cimen ci-attach&eacute;, nous vous proposons trois formules sans que votre texte descriptif puisse d&eacute;passer 620 mots. Ledit texte en langue fran&ccedil;aise comprendra le descriptif de vos produits, vos coordonn&eacute;es compl&egrave;tes, y compris les noms et pr&eacute;noms des personnes de contact.';
					$etap_file->save();
				}
				elseif($typeMask == 2) {
					$etap_file = new etap_file();
					$etap_file->fields['id_action']=  $id_action;
					$etap_file->fields['id_etape']= $actionetap->fields['id'];
					$etap_file->fields['id_doc']= 0;
					$etap_file->fields['label']= 'Texte descriptif en Fran&ccedil;ais';
					$etap_file->fields['content']= 'Votre descriptif en langue anglaise et fran&ccedil;aise ne pourra d&eacute;passer 350 mots. Ce texte comprendra le descriptif de vos produits, vos coordonn&eacute;es compl&egrave;tes, y compris les noms et pr&eacute;noms des personnes de contact.';
					$etap_file->save();
				}

				/*** Etape transport ***/
				$actionetap = new action_etap();
				$actionetap->fields['id_action']	= $id_action;
				$actionetap->fields['label']		= 'Transport';
				$actionetap->fields['description']	= "Transport par groupage organis&eacute; par la DCE";
				$actionetap->fields['position']		= 5;
				$actionetap->fields['condition']	= 1;
				$actionetap->fields['condition_content']	= "Souhaitez-vous faire transporter votre mat&eacute;riel par la DCE ?";
				$actionetap->fields['condition_label_yes']	= "Veuillez sp&eacute;cifier le mat&eacute;riel au moyen du formulaire pro-forma ci-joint. Un rendez-vous pour la livraison est &agrave; fixer avec Monsieur Tresch t&eacute;l.: 40 78 36 ou 247-84109. Souhaitez-vous continuer ?";
				$actionetap->fields['condition_label_no']	= "Nous prenons note du fait que votre soci&eacute;t&eacute; se chargera elle-m&ecirc;me du transport de son mat&eacute;riel au Salon, de la manutention sur place et de son acheminement vers le pavillon luxembourgeois. Souhaitez-vous continuer ?";
				$actionetap->save();

				$filename = 'transport.odt';
				$filename2 = 'transport2.odt';
				copy($fileModelePath.$filename, $fileModelePath.$filename2);
				$docfile = new docfile();
				$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$docfile->fields['id_folder'] = -1;
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpuploadedfile = $fileModelePath.$filename2;
				$docfile->fields['name'] = $filename2;
				$docfile->fields['size'] = filesize($fileModelePath.$filename2);
				$docfile->save();

				$etap_file = new etap_file();
				$etap_file->fields['id_action']= $id_action;
				$etap_file->fields['id_etape']=$actionetap->fields['id'];
				$etap_file->fields['id_doc']=$docfile->fields['id'];
				$etap_file->save();

				/*** Etape delegues au stand ***/
				$actionetap = new action_etap();
				$actionetap->fields['id_action']	= $id_action;
				$actionetap->fields['label']		= 'D&eacute;l&eacute;gu&eacute;s au stand';
				$actionetap->fields['description']	= "Les donn&eacute;es encod&eacute;es d&eacute;montreront que votre soci&eacute;t&eacute; assurera une pr&eacute;sence permanente au pavillon pendant toute la dur&eacute;e du salon. La DCE se basera sur ces donn&eacute;es pour mettre des badges nominatifs &agrave; disposition de vos d&eacute;l&eacute;gu&eacute;s sur place.";
				$actionetap->fields['position']		= 6;
				$actionetap->fields['type_etape']	= 4;
				$actionetap->save();

				//$filename = 'delegues.odt';
				//$docfile = new docfile();
				//$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
				//$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				//$docfile->fields['id_folder'] = -1;
				//$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				//$docfile->tmpuploadedfile = $fileModelePath.$filename;
				//$docfile->fields['name'] = $filename;
				//$docfile->fields['size'] = filesize($fileModelePath.$filename);
				//$docfile->save();

				//$etap_file = new etap_file();
				//$etap_file->fields['id_action']= $id_action;
				//$etap_file->fields['id_etape']=$actionetap->fields['id'];
				//$etap_file->fields['id_doc']=$docfile->fields['id'];
				//$etap_file->save();

				/*** Etape paiement ***/
				$actionetap = new action_etap();
				$actionetap->fields['id_action']	= $id_action;
				$actionetap->fields['label']		= 'Paiement';
				$actionetap->fields['description']	= "L'original de la facture au montant de la contribution forfaitaire vous parviendra par la poste.";
				$actionetap->fields['position']		= 7;
				$actionetap->fields['type_etape']	= 5;

				$actionetap->save();

				dims_redirect('admin.php?op=xml_planning_modifier_action&id='.$id_action);
				die();
				break;
			case 'delete_fileannonce':
				require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
				$id_file = dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,true,false);

				if($id_file != '') {
					$doc = new docfile();
					$doc->open($id_file);
					$doc->delete();
				}

				break;
			case 'delete_preview':
				require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
				$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT,true,true,false);

				if($id_evt != '') {
					$action = new action();
					$action->open($id_evt);

					unlink($action->fields['preview_path']);
					$action->fields['preview_path'] = '';

					$action->save();
				}

				break;
			case 'delete_banner':
				require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
				$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT,true,true,false);

				if($id_evt != '') {
					$action = new action();
					$action->open($id_evt);

					unlink($action->fields['banner_path']);
					$action->fields['banner_path'] = '';

					$action->save();
				}

				break;

			case 'delete_match':
				require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
				$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT,true,true,false);

				if($id_evt != '') {
					$action = new action();
					$action->open($id_evt);

					unlink($action->fields['matchmaking_path']);
					$action->fields['matchmaking_path'] = '';

					$action->save();
				}

				break;

			case 'delete_fileetap':
				$id_fileetap = dims_load_securvalue('id_fileetap',dims_const::_DIMS_NUM_INPUT,true,true,false);

				if ($id_fileetap>0) {
					$etapfile = new etap_file();

					$etapfile->open($id_fileetap);

					$etapfile->delete();
				}
				dims_redirect($scriptenv."?op=xml_planning_modifier_action&subaction="._DIMS_ACTION_ETAP);
			break;
			case 'save_eventfile':

				$id_etap = 0;

				$id_etap = dims_load_securvalue('id_etap',dims_const::_DIMS_NUM_INPUT,true,true,false);

				if($id_etap != null)
				{
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
									$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
									$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$docfile->fields['id_folder'] = -1;
									$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
									$docfile->tmpuploadedfile = $upload_dir.$filename;
									$docfile->fields['name'] = $filename;
									$docfile->fields['size'] = filesize($upload_dir.$filename);
									$error = $docfile->save();

									$id_doc=$docfile->fields['id'];

									// on cr�� l'association entre le doc et l'etape
									$etap_file = new etap_file();
									$etap_file->fields['id_action']=$_SESSION['dims']['currentaction'];
									$etap_file->fields['id_etape']=$id_etap;
									$etap_file->fields['id_doc']=$id_doc;
									$etap_file->save();
								}
							}
							closedir($dh);
							unset($_SESSION['dims']['currentetapfile']);
						}
						rmdir($upload_dir);
					}
				}
				break;
			case "delete_actionetap":
				$id_actionetap= dims_load_securvalue('id_actionetap',dims_const::_DIMS_NUM_INPUT,true,true,false);
				$actionetap = new action_etap();
				if ($id_actionetap>0) {
					$actionetap->open($id_actionetap);
					$position=$actionetap->fields['position'];
					// update position
					$db->query("update dims_mod_business_event_etap set position=position-1 where position> :position and id_action= :idaction ", array(
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
						':position' => array('type' => PDO::PARAM_INT, 'value' => $position)
					));
					$actionetap->delete();

					$sql = 'SELECT id FROM dims_mod_business_event_etap_file WHERE id_etape=:idetape';

					$ress = $db->query($sql, array(
						':idetape' => array('type' => PDO::PARAM_INT, 'value' => $id_actionetap),
					));

					if($db->numrows($ress) > 0)
					{
						$etap_file = new etap_file();
						while($f = $db->fetchrow($ress))
						{
							$etap_file->open($f['id']);
							$etap_file->delete();
						}
					}
				}
				dims_redirect($scriptenv."?op=xml_planning_modifier_action&subaction="._DIMS_ACTION_ETAP);
				break;
			case "save_actionetap":

				$id_actionetap= dims_load_securvalue("id_metacateg",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['currentactionetap']);
				$label=  dims_load_securvalue("actionetap_label",dims_const::_DIMS_CHAR_INPUT,true,true,false);

				if (mb_check_encoding($label,"UTF-8")) {
					$label=utf8_decode($label);
					$_POST['actionetap_label']		=utf8_decode($_POST['actionetap_label']);
					$_POST['actionetap_description']	=utf8_decode($_POST['actionetap_description']);
					$_POST['actionetap_condition']		=utf8_decode($_POST['actionetap_condition']);
					$_POST['actionetap_condition_content']	=utf8_decode($_POST['actionetap_condition_content']);
					$_POST['actionetap_date_fin']		=utf8_decode($_POST['actionetap_date_fin']);
					$_POST['actionetap_condition_label_yes']=utf8_decode($_POST['actionetap_condition_label_yes']);
					$_POST['actionetap_condition_label_no']	=utf8_decode($_POST['actionetap_condition_label_no']);
				}

				if($_POST['actionetap_condition'] == "true")
					$_POST['actionetap_condition'] = 1;
				else
					$_POST['actionetap_condition'] = 0;

				if($_POST['actionetap_date_fin']!=0) {
					$_POST['actionetap_date_fin'] = dims_local2timestamp($_POST['actionetap_date_fin']);
				}

				$actionetap = new action_etap();
				if ($label!="") {
					if ($id_actionetap>0) {
							$actionetap->open($id_actionetap);
							$actionetap->setvalues($_POST, "actionetap_");
					}
					else {
						 $actionetap->setvalues($_POST, "actionetap_");
						// on compte le nb + 1
						 $res=$db->query("select * from dims_mod_business_event_etap where id_action=:idaction", array(
							':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
						 ));
						 $position=$db->numrows($res);
						 $actionetap->fields['position']=$position+1;
					}
					$actionetap->fields['id_action']=$_SESSION['dims']['currentaction'];
					$actionetap->save();
					unset($_SESSION['dims']['currentactionetap']);
				}
				dims_redirect($scriptenv."?op=xml_planning_modifier_action&subaction="._DIMS_ACTION_ETAP);
				break;
			case "saveetap_position":
				// construction de la structure courante
				$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true,true);
				$move = dims_load_securvalue('move', dims_const::_DIMS_NUM_INPUT, true,true);

				$res=$db->query("select * from dims_mod_business_event_etap where id_action=:idaction order by position", array(
					':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
				));

				$tabcateg = array();
				$tabcategused = array();

				while ($mod=$db->fetchrow($res)) {
					$tabcateg[$mod['id']]=$mod;
				}

				$nb_etap = count($tabcateg);

				if($move == 1 && $tabcateg[$id_etap]['position'] < $nb_etap) { //up

					$db->query("update dims_mod_business_event_etap set position=position-1 where position=:position and id_action=:idaction order by position", array(
						':position' => array('type' => PDO::PARAM_INT, 'value' => $tabcateg[$id_etap]['position'] + 1),
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
					));
					$db->query("update dims_mod_business_event_etap set position=:position where id =:idetape and id_action=:idaction order by position", array(
						':position' => array('type' => PDO::PARAM_INT, 'value' => $tabcateg[$id_etap]['position'] + 1),
						':idetape' => array('type' => PDO::PARAM_INT, 'value' => $id_etap),
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
					));
				}
				elseif($move == -1 && $tabcateg[$id_etap]['position'] > 1) { //down

					$db->query("update dims_mod_business_event_etap set position=position+1 where position=:position and id_action=:idaction order by position", array(
						':position' => array('type' => PDO::PARAM_INT, 'value' => $tabcateg[$id_etap]['position'] - 1),
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
					));
					$db->query("update dims_mod_business_event_etap set position= :position where id = :idetape and id_action=:idaction order by position", array(
						':position' => array('type' => PDO::PARAM_INT, 'value' => $tabcateg[$id_etap]['position'] - 1),
						':idetape' => array('type' => PDO::PARAM_INT, 'value' => $id_etap),
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
					));
				}

				die();
				break;
			case 'make_payement_etap':
				$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true);

				$etap = new action_etap();
				$etap->open($id_etap);

				$etap->fields['paiement'] = 1;

				$etap->save();

				break;
			case 'unmake_payement_etap':
				$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true);

				$etap = new action_etap();
				$etap->open($id_etap);

				$etap->fields['paiement'] = 0;

				$etap->save();

				break;
			case 'save_action':
				require_once(DIMS_APP_PATH . "/modules/system/class_enum.php");
				$id_enum=dims_load_securvalue('id_enum',dims_const::_DIMS_NUM_INPUT,true,true);
				$enum = new enum();

				if ($id_enum>0) $enum->open($id_enum);

				$enum->setvalues($_POST,'action_');
				$enum->save();

				dims_redirect(DIMS_APP_PATH . "/admin.php?op=admin_actions");
				break;
			case 'delete_action':
				require_once(DIMS_APP_PATH . "/modules/system/class_enum.php");
				$id_enum=dims_load_securvalue('id_enum',dims_const::_DIMS_NUM_INPUT,true,true);
				$enum = new enum();

				if ($id_enum>0) {
					$enum->open($id_enum);
					$enum->delete();
				}
				dims_redirect(DIMS_APP_PATH . "/admin.php?op=admin_actions");
				break;

			// gestion des types d'actions
			case 'admin_actions':
			case 'modify_action':
				if (dims_isadmin()) {
					require_once(DIMS_APP_PATH . "/modules/system/class_enum.php");
					$id_enum=dims_load_securvalue('id_enum',dims_const::_DIMS_NUM_INPUT,true,true);

					echo "<div style=\"float:left;width:48%\">";
						echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LIST'],'100%','','');
						// construction de la liste des types d'�v�nement
						$res=$db->query("select * from dims_mod_business_enum where type='typeaction' order by libelle");
						?>
						<table cellpadding="2" cellspacing="1" width="60%" align="center">
							<tr bgcolor='<? echo $skin->values['bgline1']; ?>'>
								<td class="Title" align="center">&nbsp;<?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?></td>
								<td class="Title" align="center"><?php echo $_DIMS['cste']['_DIMS_LABEL_COLOR']; ?></td>
								<td class="Title" align="center">&nbsp;<?php echo $_DIMS['cste']['_MODIFY']; ?></td>
								<td class="Title" align="center">&nbsp;<?php echo $_DIMS['cste']['_DELETE']; ?></td>
							</tr>
							<?
							$cpt=0;
							while ($fields=$db->fetchrow($res)) {
								if ($cpt % 2 == 1)
									echo '<tr class="trl1">';
								else
									echo '<tr class="trl2">';

								if ($fields['value']=="") $fields['value']="#efefef";
						$title=$fields['libelle'];
					if (isset($_DIMS['cste'][$fields['libelle']])) {
						$title=$_DIMS['cste'][$fields['libelle']];
				}
								echo "<td align=\"center\">&nbsp;<b>{$title}</b>&nbsp;</td>
										<td align=\"center\" bgcolor=\"".$fields['value']."\"></td>
										<td align=\"center\">&nbsp;<a href=\"$scriptenv?op=modify_action&id_enum=".$fields['id']."\"><img border=\"0\" src='./common/img/edit.gif' alt='".$_DIMS['cste']['_MODIFY']."'></a>&nbsp;</td>
										<td align=\"center\">&nbsp;<a href=\"javascript:dims_confirmlink('$scriptenv?op=delete_action&id_enum=".$fields['id']."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\"><img border=\"0\" src='./common/modules/business/img/ico_delete.gif' alt='".$_DIMS['cste']['_DELETE']."'></a>&nbsp;</td>
									</tr>";

								if ($id_enum==$fields['id']) {
									// on fait la ligne de modification
									echo "<tr><td colspan=\"4\">";
									echo "<form action=\"admin.php\" method=\"post\">";
									// Sécurisation du formulaire par token
									require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
									$token = new FormToken\TokenField;
									$token->field("op");
									$token->field("id_enum");
									$token->field("action_type");
									$token->field("action_id_enum");
									$token->field("action_libelle");
									$token->field("action_value");
									echo "<input type=\"hidden\" name=\"op\" value=\"save_action\">";
									echo "<input type=\"hidden\" name=\"id_enum\" value=\"".$fields['id']."\">";
									echo "<input type=\"hidden\" name=\"action_type\" value=\"typeaction\">";
									echo "<input type=\"hidden\" name=\"action_id_enum\" value=\"0\">";
									echo "<div class=\"dims_form\" style=\"width:100%;\">
										<p><label>".$_DIMS['cste']['_DIMS_LABEL']."</label>
										<input type=\"text\" name=\"action_libelle\" value=\"".$fields['libelle']."\"></p>
										<p><label>".$_DIMS['cste']['_DIMS_COLOR']."</label>
										<input type=\"text\" style=\"width:80px\" id=\"action_value\" name=\"action_value\" value=\"".$fields['value']."\">
										<a href=\"javascript:void(0);\" onclick=\"javascript:dims_colorpicker_open('action_value', event);\"><img src=\"./common/img/colorpicker/colorpicker.png\" align=\"top\" border=\"0\"></a></p>
										<p><label></label><input type=\"submit\" value=\"".$_DIMS['cste']['_DIMS_VALID']."\"></p>";
									$tokenHTML = $token->generate();
									echo $tokenHTML;
									echo "</form></tr>";
								}
								$cpt++;
							}
						echo "</table>";
						echo $skin->close_simplebloc();
					echo "</div>";
					echo "<div style=\"float:left;width:48%\">";
					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_ADD'],'100%','','');
					echo "<form action=\"admin.php\" method=\"post\">";
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("op");
					$token->field("id_enum");
					$token->field("action_type");
					$token->field("action_id_enum");
					$token->field("action_value");
					$token->field("action_libelle");
					echo "<input type=\"hidden\" name=\"op\" value=\"save_action\">";
					echo "<input type=\"hidden\" name=\"id_enum\" value=\"\">";
					echo "<input type=\"hidden\" name=\"action_type\" value=\"typeaction\">";
					echo "<input type=\"hidden\" name=\"action_id_enum\" value=\"0\">";
					echo "<div class=\"dims_form\" style=\"width:100%;\">
						<p><label>".$_DIMS['cste']['_DIMS_LABEL']."</label>
						<input type=\"text\" name=\"action_libelle\" value=\"\"></p>
						<p><label>".$_DIMS['cste']['_DIMS_LABEL_COLOR']."</label>
						<input type=\"text\" style=\"width:80px\" id=\"action_value\" name=\"action_value\" value=\"#EFEFEF\">
						<a href=\"javascript:void(0);\" onclick=\"javascript:dims_colorpicker_open('action_value', event);\"><img src=\"./common/img/colorpicker/colorpicker.png\" align=\"top\" border=\"0\"></a></p>
						<p><label></label><input type=\"submit\" value=\"".$_DIMS['cste']['_DIMS_ADD']."\"></p>";
					$tokenHTML = $token->generate();
					echo $tokenHTML;
					echo "</form>";

					echo $skin->close_simplebloc();
					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_BACK'],'100%','','');
					echo "<p style=\"text-align:center;\"><a href=\"./admin.php\">".$_DIMS['cste']['_DIMS_PLANNING_RETURN']."</a></p>";
					echo $skin->close_simplebloc();
					echo "</div>";
				}
				break;

			case 'action_saisierapide':
				$action = new action();
				$action->setvalues($_POST,'action_');
				$action->fields['heuredeb'] = sprintf("%02d:%02d:00",$actionx_heuredeb_h,$actionx_heuredeb_m);
				$heurefin = $actionx_heuredeb_h*60+$actionx_heuredeb_m+$action->fields['temps_prevu'];
				$heurefin_h = ($heurefin-$heurefin%60)/60;
				$heurefin_m = $heurefin%60;
				$action->fields['heurefin'] = sprintf("%02d:%02d:00",$heurefin_h,$heurefin_m);

				$action->fields['temps_passe'] = $action->fields['temps_prevu'];
				$action->fields['timestp_modify']=dims_createtimestamp();
				$action->setugm();

				$action->save();
				dims_redirect("$scriptenv");
				break;

			case 'action_saisierapide':
				$action = new action();
				$action->setvalues($_POST,'action_');
				$action->fields['heuredeb'] = sprintf("%02d:%02d:00",$actionx_heuredeb_h,$actionx_heuredeb_m);
				$heurefin = $actionx_heuredeb_h*60+$actionx_heuredeb_m+$action->fields['temps_prevu'];
				$heurefin_h = ($heurefin-$heurefin%60)/60;
				$heurefin_m = $heurefin%60;
				$action->fields['heurefin'] = sprintf("%02d:%02d:00",$heurefin_h,$heurefin_m);

				$action->fields['temps_passe'] = $action->fields['temps_prevu'];
				$action->fields['timestp_modify']=dims_createtimestamp();
				$action->setugm();

				$action->save();
				dims_redirect("$scriptenv");
				break;

			case 'resetactionsearch':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					unset($_SESSION['dims']['planning']['currentuserresp']);
				}
				break;

			case 'search_action_contact_planning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$nomsearch	= dims_load_securvalue('nomsearch',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$type_action= $_SESSION['dims']['planning']['currenttypeaction'];

					if($type_action == dims_const::_PLANNING_ACTION_RDV){
						require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
						$dims_user= new user();
						$dims_user->open($_SESSION['dims']['userid']);

						$lstusers=$dims_user->getusersgroup($nomsearch,$_SESSION['dims']['planning']['currentworkspacesearch'],$_SESSION['dims']['planning']['currentprojectsearch'],$lstusers,'','dims_workspace_group.id_group>0');

						if (isset($_SESSION['business']['usersselected']) && !empty($_SESSION['business']['usersselected'])) $lstusers+=$_SESSION['business']['usersselected'];
						if (isset($_SESSION['dims']['planning']['currentusertemp']) && !empty($_SESSION['dims']['planning']['currentusertemp'])) $lstusers+=$_SESSION['dims']['planning']['currentusertemp'];

						$lstuserssel=array();
						if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstuserssel+=$_SESSION['dims']['planning']['currentactionusers'];

						// affichage de la liste de r�sultat
						if (!empty($lstusers)) {
							$params = array();
							// requete pour les noms
							$res=$db->query("select id,firstname,lastname,color from dims_user where id in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")", $params);
							if ($db->numrows($res)>0) {
								echo "<table style=\"width:100%;\">";
								while ($f=$db->fetchrow($res)) {
									if (!in_array($f['id'],$lstuserssel)) {
										$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";
										echo "<tr><td width=\"80%\">".$icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td><td>";
										echo "<td><a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('addActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
									}
								}
								echo "</table>";
							}
						}

						// calcul du la 2i�me liste de ceux selectionnes
						echo "||";
						echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_LABEL_CONCERNED']."</span>";
						$lstusers=array();
						if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstusers+=$_SESSION['dims']['planning']['currentactionusers'];
						if (!empty($lstusers)) {
							$params = array();
							$res=$db->query("select u.* from dims_user as u where id in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")", $params);
							if ($db->numrows($res)>0) {

								echo "<table style=\"width:100%;\">";
								while ($f=$db->fetchrow($res)) {
									//calcul de l'icon
									$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";
									echo "<tr><td width=\"5%\">
									<a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('deleteSelActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></td>";
									echo "<td  width=\"20%\">";
									// calcul si participe ou non
									if ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']]) {
										$chselected="selected=\"selected\"";
										$chunselected="";
									}
									else {
										$chselected="";
										$chunselected="selected=\"selected\"";
									}

									echo "<select onchange=\"updateUserActionFromSelectedPlanning('updatePartActionUserPlanning',".$f['id'].");\" name=\"part".$f['id'].".\">";
									$token->field("part".$f['id']);
									echo "<option $chselected value=\"0\">".$_DIMS['cste']['_DIMS_PARTICIPATES']."</option>";
									echo "<option $chunselected value=\"1\">".$_DIMS['cste']['_DIMS_TOINFO']."</option>";
									echo "</select></td><td>";
									echo $icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td></tr>";
								}
								echo "</table>";
							}
						}
					}
					elseif($type_action == dims_const::_PLANNING_ACTION_EVT || $type_action == dims_const::_PLANNING_ACTION_RCT)
					{

						$lstusers = array();
						if(!empty($nomsearch))
						{
							$sql = "SELECT id FROM dims_mod_business_contact WHERE firstname LIKE :name or lastname LIKE :name";
							$res = $db->query($sql, array(':name' => array('type' => PDO::PARAM_STR, 'value' => $nomsearch.'%')));

							while($f = $db->fetchrow($res))
								$lstusers[] =  $f['id'];
						}

						$lstuserssel=array();
						if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstuserssel+=$_SESSION['dims']['planning']['currentactionusers'];


						// affichage de la liste de r�sultat
						if (!empty($lstusers)) {
							$params = array();
							// requete pour les noms
							$res=$db->query("select id,firstname,lastname from dims_mod_business_contact where id in (".$db->getParamsFromArray($lstusers, 'idcontact', $params).")", $params);
							if ($db->numrows($res)>0) {
								echo "<table style=\"width:100%;\">";
								while ($f=$db->fetchrow($res)) {
									if (!in_array($f['id'],$lstuserssel)) {
										$icon="<img src=\"./data/users/icon_EFEFEF.png\" alt=\"\" border=\"0\">";
										echo "<tr><td width=\"80%\">".$icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td><td>";
										echo "<td><a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('addActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
									}
								}
								echo "</table>";
							}
						}
						// calcul du la 2i�me liste de ceux selectionnes
						echo "||";
						echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_EVT_CONTACT_PARTICIPATE']."</span>";
						$lstusers=array();
						if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstusers+=$_SESSION['dims']['planning']['currentactionusers'];
						if (!empty($lstusers)) {
							$params = array();
							$res=$db->query("select c.* from dims_mod_business_contact as c where id in (".$db->getParamsFromArray($lstusers, 'idcontact', $params).")", $params);
							if ($db->numrows($res)>0) {

								echo "<table style=\"width:100%;\">";
								while ($f=$db->fetchrow($res)) {
									//calcul de l'icon
									$icon="<img src=\"./data/users/icon_EFEFEF.png\" alt=\"\" border=\"0\">";
									echo "<tr><td width=\"5%\">
									<a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('deleteSelActionUserPlanning',".$f['id'].");\" id=\"part".$f['id'].".\"><img src=\"./common/img/delete.png\" border=\"0\"></td>";
									echo "<td  width=\"20%\">";

									// calcul si participe ou non
									if ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']] == 0)
									{
										$chselected_part ="selected=\"selected\"";
										$chselected_orga = '';
									}
									elseif ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']] == 1)
									{
										$chselected_part = '';
										$chselected_orga ="selected=\"selected\"";
									}
									echo '<select onchange="updateUserActionFromSelectedPlanning(\'updatePartActionUserPlanning\','.$f['id'].',\'part'.$f['id'].'\');" name="part'.$f['id'].'" id="part'.$f['id'].'">';
									$token->field("part".$f['id']);
									if($type_action == dims_const::_PLANNING_ACTION_EVT)
									{
										echo "<option $chselected_part value=\"0\">".$_DIMS['cste']['_DIMS_PARTICIPATES']."</option>";
										echo "<option $chselected_orga value=\"1\">".$_DIMS['cste']['_ORGANIZE']."</option>";
									}
									elseif($type_action == dims_const::_PLANNING_ACTION_RCT)
									{
										echo "<option $chselected_part value=\"0\">".$_DIMS['cste']['_DIMS_MEETED']."</option>";
										echo "<option $chselected_orga value=\"1\">".$_DIMS['cste']['_DIMS_ACCOMPANY']."</option>";
									}
									echo "</select></td><td>";
									echo $icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td></tr>";
								}
								echo "</table>";
							}
						}
					}
				}
				die();
				break;

			case 'search_action_partner_planning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$nomsearch	= dims_load_securvalue('nomsearch',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$type_action= $_SESSION['dims']['planning']['currenttypeaction'];

					$lstusers = array();
					$lstuserssel=array();

					//R�cup�ration de la liste des partenaire d�j� selectionn�s
					if (!empty($_SESSION['dims']['planning']['currentactionpartner']))
						$lstuserssel+=$_SESSION['dims']['planning']['currentactionpartner'];

					// affichage de la liste de r�sultat
					if(!empty($nomsearch))
					{
						$sql = "SELECT id, intitule,partenaire FROM dims_mod_business_tiers WHERE intitule LIKE :name";
						$res=$db->query($sql, array(':name' => array('type' => PDO::PARAM_STR, 'value' => $nomsearch.'%')));
						if ($db->numrows($res)>0) {
							echo "<table style=\"width:100%;\">";
							echo "<tr><td>".$_DIMS['cste']['_DIMS_LABEL_ENT_NAME']."</td><td>".$_DIMS['cste']['_DIMS_LABEL_PARTENAIRE']."</td><td>Select.</td></tr>";
							while ($f=$db->fetchrow($res)) {
								if (!in_array($f['id'],$lstuserssel)) {
									$icon="<img src=\"./common/img/partenaire.png\" alt=\"\" border=\"0\">";
									$i=($i==0) ? 1 : 0;
									// test si partenaire ou non
									if ($f['partenaire']) $iconpart="<img src=\"./common/img/checkdo2.png\" alt=\"\">";
									else $iconpart="<img src=\"./common/img/checkdo1.png\" alt=\"\">";
									echo "<tr class=\"trl$i\"><td width=\"80%\">".$icon."&nbsp;".$f['intitule']."</td><td>".$iconpart."</td><td>";
									echo "<td><a href=\"javascript:void(0);\" onclick=\"updatePartnerActionFromSelectedPlanning('addActionPartnerPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
								}
							}
							echo "</table>";
						}
					}
					// calcul du la 2i�me liste de ceux selectionnes
					echo "||";
					echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_EVT_PARTNER']."</span>";
					$lstusers=array();
					if (!empty($_SESSION['dims']['planning']['currentactionpartner'])) $lstusers+=$_SESSION['dims']['planning']['currentactionpartner'];
					if (!empty($lstusers)) {
						$params = array();
						$res=$db->query("select t.* from dims_mod_business_tiers as t where id in (".$db->getParamsFromArray($lstusers, 'idtiers', $params).")", $params);
						if ($db->numrows($res)>0) {
							echo "<table style=\"width:100%;\">";
							while ($f=$db->fetchrow($res)) {
								//calcul de l'icon
								$icon = "<img src=\"./common/img/partenaire.png\" alt=\"\" border=\"0\">";
								echo "<tr><td width=\"5%\">
								<a href=\"javascript:void(0);\" onclick=\"updatePartnerActionFromSelectedPlanning('deleteSelActionPartnerPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></td><td>";
								echo $icon."&nbsp;".$f['intitule']."</td></tr>";
							}
							echo "</table>";
						}
					}

				}
				die();
				break;

			case 'search_contact_planning':
				ob_end_clean();

				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$nomsearch=dims_load_securvalue('nomsearch',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$_SESSION['dims']['planning']['currentusersearch']=dims_load_securvalue('nomsearch', dims_const::_DIMS_CHAR_INPUT, true, true, true);
					require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
					$dims_user= new user();
					$dims_user->open($_SESSION['dims']['userid']);

					$lstusers=$dims_user->getusersgroup($nomsearch,$_SESSION['dims']['planning']['currentworkspacesearch'],$_SESSION['dims']['planning']['currentprojectsearch']);
					$_SESSION['dims']['planning']['currentuserresp']=$lstusers;
					// affichage de la liste de r�sultat
					if (isset($_SESSION['dims']['planning']['currentuserresp']) && !empty($_SESSION['dims']['planning']['currentuserresp'])) {
						$params = array();
						// requete pour les noms
						$res=$db->query("SELECT id,firstname,lastname,color
										FROM dims_user
										WHERE id in (".$db->getParamsFromArray($_SESSION['dims']['planning']['currentuserresp'], 'iduser', $params).")", $params);

						if ($db->numrows($res)>1) {
							// display link to active all elements
							echo "<span style=\"padding-top:2px;width:100%;margin:0px;text-align:left;\">";
							echo "<a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('addAllUserTempPlanning',0);\">".$_DIMS['cste']['__ALLCHECK']."</a>";
							if ($_SESSION['dims']['planning']['cptetempsekected']>1) echo "&nbsp;/&nbsp;<a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('deleteAllUserTempPlanning',0);\">".$_DIMS['cste']['_ALLUNCHECK']."</a>";
							echo "</span>";
						}
						echo "<span style=\"width:100%;\">";
						// requete pour les noms

						if ($db->numrows($res)>0) {
							echo "<table width=\"100%\" >";
							$resalreadysel="";
							$cptetemp=0;
							while ($f=$db->fetchrow($res)) {
								//calcul de l'icon
								$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$f['color']).".png";
								$icon="";
								if (!file_exists($usericon) || $f['color']=="") {
									// on g�n�re
									$user = new user();
									$user->open($f['id']);
									if ($user->fields['color']=="") {
										$user->fields['color']="#EFEFEF";
										$user->save();
									}
									// generation du logo
									$user->createPicto();
								}
								$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";
								$restemp="<tr>";

								 if ((!isset($_SESSION['dims']['planning']['currentusertemp']) || !in_array($f['id'],$_SESSION['dims']['planning']['currentusertemp'])) && (!isset($_SESSION['business']['usersselected']) || !in_array($f['id'],$_SESSION['business']['usersselected'])) && $f['id']!=$_SESSION['dims']['userid']) {
									$cptetemp++;
									$color="";

									$restemp.="<td width=\"10%\"><input type=\"checkbox\" style=\"margin:0px;padding:0px;\" onclick=\"updateUserFromSelectedPlanning('addUserTempPlanning',".$f['id'].")\"></td>
									<td width=\"10%\">".$icon."</td>";
									$restemp.="<td align=\"left\" width=\"70%\">";
									$restemp.= strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td>";

									$restemp.= "
										<td width=\"10%\"><a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('addUserPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
									echo $restemp;
								 }
								 else {
									$restemp.="<td>-</td>
									<td width=\"10%\">".$icon."</td>";
									$restemp.= "<td align=\"left\" width=\"74%\"><font class=\"fontgray\">".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</font></td>
									<td width=\"8%\">-</td></tr>";
									$resalreadysel.=$restemp;
								 }

							}
							// display users already selected
							echo $resalreadysel;
							echo "</table>";
						}
						echo "</span>";
					}
				}
				die();
				break;

			case 'addActionUserPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_user=dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_user>0) {
						if (!isset($_SESSION['dims']['planning']['currentactionusers'][$id_user])) {
							$_SESSION['dims']['planning']['currentactionusers'][$id_user]=$id_user;
							$_SESSION['dims']['planning']['currentactionusersPart'][$id_user]=1;
						}
					}
				}
				break;

			case 'deleteSelActionUserPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_user=dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_user>0) {
						if ($_SESSION['dims']['planning']['currentactionusers'][$id_user]) {
							// on l'enl�ve de la liste
							unset ($_SESSION['dims']['planning']['currentactionusers'][$id_user]);
							unset($_SESSION['dims']['planning']['currentactionusersPart'][$id_user]); // default Participate
						}
					}
				}
				break;

			case 'updatePartActionUserPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])
				{
					$type = $_SESSION['dims']['planning']['currenttypeaction'];

					if($type == dims_const::_PLANNING_ACTION_RDV)
					{
						$id_user=dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if ($id_user>0) {
							if ($_SESSION['dims']['planning']['currentactionusers'][$id_user]) {
								// on l'enl�ve de la liste
								if ($_SESSION['dims']['planning']['currentactionusersPart'][$id_user]) $_SESSION['dims']['planning']['currentactionusersPart'][$id_user]=0;
								else $_SESSION['dims']['planning']['currentactionusersPart'][$id_user]=1;
							}
						}
					}
					elseif($type == dims_const::_PLANNING_ACTION_EVT || $type == dims_const::_PLANNING_ACTION_RCT)
					{
						$id_user		= dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);

						if ($id_user>0)
						{
							$stat_particip	= dims_load_securvalue('stat_particip',dims_const::_DIMS_NUM_INPUT,true,true,true,$stat_particip,1);

							if ($_SESSION['dims']['planning']['currentactionusers'][$id_user])
								$_SESSION['dims']['planning']['currentactionusersPart'][$id_user]=$stat_particip;

						}
					}
				}
				break;

			case 'addActionPartnerPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])
				{
					$id_part=dims_load_securvalue('id_part',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_part>0)
					{
						if (!isset($_SESSION['dims']['planning']['currentactionpartner'][$id_part]))
						{
							$_SESSION['dims']['planning']['currentactionpartner'][$id_part]=$id_part;
						}
					}
				}
				break;

			case 'deleteSelActionPartnerPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])
				{
					$id_part=dims_load_securvalue('id_part',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_part>0)
					{

						if ($_SESSION['dims']['planning']['currentactionpartner'][$id_part])
						{
							// on l'enl�ve de la liste
							unset ($_SESSION['dims']['planning']['currentactionpartner'][$id_part]);
						}
					}
				}
				break;

			case 'addUserPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_user=dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_user>0) {
						$res=$db->query("select * from dims_mod_business_user_planning where id_user =:iduser and id_user_sel=:idusersel", array(
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
							':idusersel' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
						));
						if ($db->numrows($res)==0) {
							// on insert cette nouvelle correspondance
							$userp = new user_planning();
							$userp->fields['id_user']=$_SESSION['dims']['userid'];
							$userp->fields['id_user_sel']=$id_user;
							$userp->fields['display']=1;
							$userp->save();
							if (isset($_SESSION['dims']['planning']['currentusertemp'][$id_user])) unset($_SESSION['dims']['planning']['currentusertemp'][$id_user]);
						}
					}
				}
				die();
				break;

			case 'deleteUserPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_user=dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_user>0) {
						$res=$db->query("select * from dims_mod_business_user_planning where id_user =:iduser and id_user_sel=:idusersel", array(
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
							':idusersel' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
						));
						if ($db->numrows($res)>0) {
							// on supprime cette correspondance
							$userp = new user_planning();
							$userp->open($_SESSION['dims']['userid'],$id_user);
							$userp->delete();
						}
					}
				}
				die();
				break;

			case 'addUserTempPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_user=dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_user>0) {
						if (!isset($_SESSION['dims']['planning']['currentusertemp'][$id_user])) $_SESSION['dims']['planning']['currentusertemp'][$id_user]=$id_user;
					}
				}
				die();
				break;

			case 'deleteUserTempPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_user=dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_user>0) {
						if (isset($_SESSION['dims']['planning']['currentusertemp'][$id_user])) unset($_SESSION['dims']['planning']['currentusertemp'][$id_user]);
					}
				}
				die();
				break;

			case 'addAllUserPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$res=$db->query("update dims_mod_business_user_planning set display=1 where id_user = :iduser", array(
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					));
				}
				die();
				break;

			case 'deleteAllUserPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$res=$db->query("update dims_mod_business_user_planning set display=0 where id_user = :iduser", array(
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					));
				}
				die();
				break;

			case 'addAllUserTempPlanning':
				//ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					// affichage de la liste de r�sultat
					if (isset($_SESSION['dims']['planning']['currentuserresp']) && !empty($_SESSION['dims']['planning']['currentuserresp'])) {
						$params = array();
						// requete pour les noms
						$sql="select id,firstname,lastname,color from dims_user where id in (".$db->getParamsFromArray($_SESSION['dims']['planning']['currentuserresp'], 'iduser', $params).") ";

						$res=$db->query($sql, $params);
						if ($db->numrows($res)>0) {
							while($f=$db->fetchrow($res)) {
								if (!in_array($f['id'],$_SESSION['dims']['planning']['currentusertemp']) && !in_array($f['id'],$_SESSION['business']['usersselected']) && $f['id']!=$_SESSION['dims']['userid']) {
									if (!isset($_SESSION['dims']['planning']['currentusertemp'][$f['id']])) $_SESSION['dims']['planning']['currentusertemp'][$f['id']]=$f['id'];
								}
							}
						}
					}
				}
				die();
				break;

			case 'deleteAllUserTempPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					unset($_SESSION['dims']['planning']['currentusertemp']);
				}
				die();
				break;

			case 'displayUserPlanning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_user=dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_user>0) {
						$res=$db->query("select * from dims_mod_business_user_planning where id_user =:iduser and id_user_sel= :idusersel", array(
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
							':idusersel' => array('type' => PDO::PARAM_INT, 'value' => $iduser),
						));
						if ($db->numrows($res)>0) {
							// on update cette correspondance
							$userp = new user_planning();
							$userp->open($_SESSION['dims']['userid'],$id_user);
							if ($userp->fields['display']) $userp->fields['display']=0;
							else $userp->fields['display']=1;
							$userp->save();
						}
					}
				}
				die();
				break;

			case 'recherche_rapide':
				require_once(DIMS_APP_PATH . '/modules/system/public_recherche_rapide.php');
				break;

			case 'xml_planning_ajouter_action':
				include(DIMS_APP_PATH . '/modules/system/xml_planning_ajouter_action.php');
				break;

			case 'xml_planning_modifier_action':
				include(DIMS_APP_PATH . '/modules/system/xml_planning_modifier_action.php');
				break;

			case 'xml_planning_disabled_action' :
				$action_id=dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true);
				$action = new action();
				$action->open($action_id);
				$action->fields['close'] = 1;
				$action->fields['supportrelease'] = 0;
				$action->save();

				$redirect = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_admin_events";
				dims_redirect($redirect);
				break;

			case 'xml_planning_active_action':
				$action_id=dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true);
				$action = new action();
				$action->open($action_id);
				$action->fields['close'] = 0;
				$action->fields['supportrelease'] = 1;
				$action->save();

				$redirect = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_admin_events";
				dims_redirect($redirect);

				break;
			case 'xml_planning_delete_action' :
				$action_id=dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true);
				$action = new action();

				$action->open($action_id);

				// verification des inscriptions
				$res=$db->query("select id from dims_mod_business_event_inscription where id_action=:idaction", array(
					':idaction' => array('type' => PDO::PARAM_INT, 'value' => $action->getId()),
				));
				if ($db->numrows($res)==0) {
					// on delete completement
					$action->delete();
				}
				else {
					if($action->fields['close'] == 1) {
						$action->fields['close'] = 0;
					}
					else {
						$action->fields['close'] = 1;
					}
					$action->save();
				}

				$redirect = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_admin_events";

				dims_redirect($redirect);

				break;

			case 'xml_planning_action_supprimer':

				$action = new action();
				$action_id=dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
				if ($action_id>0) {
					$action->open($action_id);
					$djour= business_dateus2fr($action->fields['datejour']);
					dims_create_user_action_log(_SYSTEM_ACTION_DELETEACTION, "{$action->fields['libelle']} du $djour",dims_const::_DIMS_MODULE_SYSTEM,dims_const::_DIMS_MODULE_SYSTEM,$action->fields['id'],dims_const::_SYSTEM_OBJECT_ACTION);
					$action->delete();
					?>
					<script language="javascript">
						opener.affiche_planning_delayed('');
						window.close();
					</script>
					<?php
				}
				else {
					?>
					<script language="javascript">
						opener.affiche_planning_delayed('');
						window.close();
					</script>
					<?php
				}
				break;

			case 'xml_planning_action_enregistrer':
				if($_SESSION['dims']['browser']['pda']) {
					$rech_dossier = -1;
					$rech_tiers = -1;
					$actiondetail_dossier_id = -1;
					$actiondetail_tiers_id	= -1;
				}
				$datedebl		= dims_load_securvalue("action_datejour",dims_const::_DIMS_CHAR_INPUT,true,true,false);
				$datefinl		= dims_load_securvalue("datefin",dims_const::_DIMS_CHAR_INPUT,true,true,false);
				$action_id		= dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
				$action_type	= dims_load_securvalue('action_type',dims_const::_DIMS_NUM_INPUT,true,true,true,$action_type,dims_const::_PLANNING_ACTION_RDV);

				if(empty($datefinl))
					$datefinl = $datedebl;

				$datedeb=explode("/",$datedebl);
				$datefin=explode("/",$datefinl);
				$datedeb_timestp=mktime(0,0,0,$datedeb[1],$datedeb[0],$datedeb[2]);
				$datefin_timestp=mktime(0,0,0,$datefin[1],$datefin[0],$datefin[2]);
				$nbjour=($datefin_timestp-$datedeb_timestp)/86400;

				if($action_type == dims_const::_PLANNING_ACTION_EVT) {
					$daterel_temp	= dims_load_securvalue("daterelease",dims_const::_DIMS_CHAR_INPUT,true,true,false);
					$dateopen_temp	= dims_load_securvalue("dateopen",dims_const::_DIMS_CHAR_INPUT,true,true,false);

					if(!empty($daterel_temp)) {
						$daterel		= explode("/",$daterel_temp);
						$daterel_timestp= mktime(0,0,0,$daterel[1],$daterel[0],$daterel[2]);
						if($daterel_timestp < mktime(0,0,0))
							$daterel_timestp = mktime(0,0,0);
					}
					else
						$daterel_timestp=mktime(0,0,0);

					$daterel_timestp = date('Ymd000000',$daterel_timestp);

					if(!empty($dateopen_temp)) {
						$dateopen		= explode("/",$dateopen_temp);
						$dateopen_timestp= mktime(0,0,0,$dateopen[1],$dateopen[0],$dateopen[2]);
					}
					else
						$dateopen_timestp=mktime(0,0,0);

					$dateopen_timestp = date('Ymd000000',$dateopen_timestp);
				}

				$actionheuredeb = dims_load_securvalue('actionx_heuredeb_h', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$actionminutedeb = dims_load_securvalue('actionx_heuredeb_m', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				if ($datedeb==$datefin) {
					if ($action_id>0)
						$ticket_title = "Planning modifie le ".$datedebl." à ".$actionheuredeb.":".$actionminutedeb;
					else
						$ticket_title = "RDV ajoute le ".$datedebl." à ".$actionheuredeb.":".$actionminutedeb;
				}
				else {
					if ($action_id>0)
						$ticket_title = "Planning modifie du ".$datedebl." au ".$datefinl." à ".$actionheuredeb.":".$actionminutedeb;
					else
						$ticket_title = "RDV ajoute du ".$datedebl." au ".$datefinl." à ".$actionheuredeb.":".$actionminutedeb;
				}

				//Le premier est forcement un parent
				unset($_SESSION['dims']['business_parent']);
				$parent = true;


				//Si c'est une modification d'evenement
				//ET qu'il n'y a pas d'erreur dans les date
				if ($action_id>0 && $nbjour > 0) {
					//Il faut d&eacute;truire les enfants
					//Pour les reconstruire ensuite
					$sql = 'DELETE FROM dims_mod_business_action
							WHERE id_parent = :idaction';

					$db->query($sql, array(
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $action_id),
					));
				}

				$datefin_insc		= dims_load_securvalue("datefin_insc",dims_const::_DIMS_CHAR_INPUT,true,true,false);
				//$dendinsc=date("d/m/Y",$datefin_insc);

				for($i=0;$i<=$nbjour;$i++) {
					$id_action=0;

					// calcul du pas de jour
					$datedeb_timestp=mktime(0,0,0,$datedeb[1],$datedeb[0]+$i,$datedeb[2]);
					// calcul si jour coche
					if (1) {  //isset($arrjour[date("w",$datedeb_timestp)])
						$action = new action();

						$actionx_heuredeb_h=dims_load_securvalue('actionx_heuredeb_h',dims_const::_DIMS_CHAR_INPUT,false,true,true);
						$actionx_heuredeb_m=dims_load_securvalue('actionx_heuredeb_m',dims_const::_DIMS_CHAR_INPUT,false,true,true);

						$actionx_heurefin_h=dims_load_securvalue('actionx_heurefin_h',dims_const::_DIMS_CHAR_INPUT,false,true,true);
						$actionx_heurefin_m=dims_load_securvalue('actionx_heurefin_m',dims_const::_DIMS_CHAR_INPUT,false,true,true);

						$is_model = dims_load_securvalue('action_is_model', dims_const::_DIMS_NUM_INPUT, false, true);

						//Si on edite ET que c'est un parent -> ouverture action
						if ($action_id>0 && $parent) {
							$action->open($action_id);

							//dans le cas d'une modif, il faut indiquer si les champs date, heure ou description sont modifi&eacute;s
							//afin d'avertir les admins et users

							//DATES
							$tab_datedeb_base = explode("-", $action->fields['datejour']);
							$date_deb_base = $tab_datedeb_base[0].$tab_datedeb_base[1].$tab_datedeb_base[2]."000000";
							$date_deb_form = date("Ymd", $datedeb_timestp)."000000";
							$tab_datefin_base = explode("-", $action->fields['datefin']);
							$date_fin_base = $tab_datefin_base[0].$tab_datefin_base[1].$tab_datefin_base[2]."000000";
							$date_fin_form = date("Ymd", $datefin_timestp)."000000";

							//HEURES
							$hdeb_base = $action->fields['heuredeb'];
							$hdeb_form = $actionx_heuredeb_h.":".$actionx_heuredeb_m.":00";
							$hfin_base = $action->fields['heurefin'];
							$hfin_form = $actionx_heurefin_h.":".$actionx_heurefin_m.":00";

							//DESCRIPTION
							$desc_base = strip_tags($action->fields['description']);
							$desc_form = strip_tags(dims_load_securvalue('action_description',dims_const::_DIMS_CHAR_INPUT,false,true));

							if($date_deb_base != $date_deb_form ||
							   $date_fin_base != $datefin_timestp ||
							   $hdeb_base != $hdeb_form ||
							   $hfin_base != $hfin_form ||
							   $desc_base != $desc_form
							   ) {
								$action->fields['alert_modif'] = 1;
							}

							//GESTION DE L'UTILISATION D'UN MODELE
							if($is_model > 0) {
								//si un modele existe dejà, on verifie s'il est identique ou non
								if($action->fields['is_model'] != 0) {
									if($action->fields['is_model'] != $is_model) {
										//s'il est different, on supprime les anciens rattachements puis on recré le model
										$sql_d = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

										$res_d = $db->query($sql_d, array(
											':idaction' => array('type' => PDO::PARAM_INT, 'value' => $action->getId()),
										));
										while($tab_d = $db->fetchrow($res_d)) {

											if(!isset($tab_ee[$tab_d['id']])) $tab_ee[$tab_d['id']] = array();

											//on supprime les liens (action_etap_file)
											$del_ee = new etap_file();
											$del_ee->open($tab_d['id']);
											$tab_ee[$tab_d['id']][] = $del_ee->fields['id_doc'];
											$del_ee->delete();

										}
										foreach($tab_ee as $etap_id => $tab_doc) {
											foreach($tab_doc as $key => $id_doc_del) {
												if($id_doc_del > 0) {
													//on supprime les docs
													$del_doc = new docfile();
													$del_doc->open($id_doc_del);
													$del_doc->delete();
												}
											}

											//on supprime les etapes
											$del_act_etap = new action_etap();
											$del_act_etap->open($etap_id);
											$del_act_etap->delete();
										}

										if($is_model > 0) {
											//on crée le modèle
											$sql_act_etap = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

											$res_act_etap = $db->query($sql_act_etap, array(
												':idaction' => array('type' => PDO::PARAM_INT, 'value' => $is_model),
											));
											while($tab_actionetap = $db->fetchrow($res_act_etap)) {
												//on duplique l'etape
												$actionetap = new action_etap();
												$actionetap->fields = $tab_actionetap;
												$actionetap->fields['id'] = '';
												$actionetap->fields['id_action'] = $action_id;
												$actionetap->save();

												//on recherche les documents attaches au model
												$sql_doc_etap = "SELECT * FROM dims_mod_business_event_etap_file WHERE id_etape = :idetape AND id_action = :idaction";

												$res_doc_etap = $db->query($sql_doc_etap, array(
													':idetape' => array('type' => PDO::PARAM_INT, 'value' => $tab_actionetap['id']),
													':idaction' => array('type' => PDO::PARAM_INT, 'value' => $is_model),
												));

												if($db->numrows($res_doc_etap) > 0) {
													while($tab_docetap = $db->fetchrow($res_doc_etap)) {
														$id_new_doc = 0;
														if($tab_docetap['id_doc'] > 0) {
															//on cree une copie du document
															$doc_tocopy = new docfile();
															$doc_tocopy->open($tab_docetap['id_doc']);

															$doc_tocp_path = $doc_tocopy->getfilepath();

															$newaction_doc = new docfile();

															$newaction_doc->fields['id_module'] = $_SESSION['dims']['moduleid'];
															$newaction_doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
															$newaction_doc->fields['id_folder'] = -1;
															$newaction_doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
															$newaction_doc->fields['name'] = $doc_tocopy->fields['name'];
															$newaction_doc->fields['size'] = $doc_tocopy->fields['size'];

															copy($doc_tocp_path, DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension']);
															$newaction_doc->tmpuploadedfile = DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension'];

															$newaction_doc->save();
															$id_new_doc = $newaction_doc->fields['id'];
														}
														//on duplique l'action_etap_file
														$action_etap_file = new etap_file();

														$action_etap_file->fields = $tab_docetap;

														$action_etap_file->fields['id'] = '';
														$action_etap_file->fields['id_action'] = $action_id;
														$action_etap_file->fields['id_etape'] = $actionetap->fields['id'];
														if($id_new_doc > 0) $action_etap_file->fields['id_doc'] = $id_new_doc;

														$action_etap_file->save();
													}
												}
											}
										}
									}
									//si c'est identique on a rien à faire
								}
								else {
									//il faut créer automatiquement les étapes à partir du model
									//recuperation des informations sur les etapes et documents rattachés
									$sql_act_etap = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

									$res_act_etap = $db->query($sql_act_etap, array(
											':idaction' => array('type' => PDO::PARAM_INT, 'value' => $is_model),
										));
									while($tab_actionetap = $db->fetchrow($res_act_etap)) {
										//on duplique l'etape
										$actionetap = new action_etap();
										$actionetap->fields = $tab_actionetap;
										$actionetap->fields['id'] = '';
										$actionetap->fields['id_action'] = $action_id;
										$actionetap->save();

										//on recherche les documents attaches au model
										$sql_doc_etap = "SELECT * FROM dims_mod_business_event_etap_file WHERE id_etape = :idetap AND id_action = :idaction";

										$res_doc_etap = $db->query($sql_doc_etap, array(
											':idetape' => array('type' => PDO::PARAM_INT, 'value' => $tab_actionetap['id']),
											':idaction' => array('type' => PDO::PARAM_INT, 'value' => $is_model),
										));

										if($db->numrows($res_doc_etap) > 0) {
											while($tab_docetap = $db->fetchrow($res_doc_etap)) {
												$id_new_doc = 0;
												if($tab_docetap['id_doc'] > 0) {
													//on cree une copie du document
													$doc_tocopy = new docfile();
													$doc_tocopy->open($tab_docetap['id_doc']);

													$doc_tocp_path = $doc_tocopy->getfilepath();

													$newaction_doc = new docfile();

													$newaction_doc->fields['id_module'] = $_SESSION['dims']['moduleid'];
													$newaction_doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
													$newaction_doc->fields['id_folder'] = -1;
													$newaction_doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
													$newaction_doc->fields['name'] = $doc_tocopy->fields['name'];
													$newaction_doc->fields['size'] = $doc_tocopy->fields['size'];

													copy($doc_tocp_path, DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension']);
													$newaction_doc->tmpuploadedfile = DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension'];

													$newaction_doc->save();
													$id_new_doc = $newaction_doc->fields['id'];
												}
												//on duplique l'action_etap_file
												$action_etap_file = new etap_file();

												$action_etap_file->fields = $tab_docetap;

												$action_etap_file->fields['id'] = '';
												$action_etap_file->fields['id_action'] = $action_id;
												$action_etap_file->fields['id_etape'] = $actionetap->fields['id'];
												if($id_new_doc > 0) $action_etap_file->fields['id_doc'] = $id_new_doc;

												$action_etap_file->save();
											}
										}
									}
								}
							}
							else {
								if($action->fields['is_model'] != 0) {
									//s'il est different, on supprime les anciens rattachements puis on recré le model
									$sql_d = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction ";

									$res_d = $db->query($sql_d, array(
										':idaction' => $action->fields['id']
									));
									while($tab_d = $db->fetchrow($res_d)) {

										if(!isset($tab_ee[$tab_d['id']])) $tab_ee[$tab_d['id']] = array();

										//on supprime les liens (action_etap_file)
										$del_ee = new etap_file();
										$del_ee->open($tab_d['id']);
										$tab_ee[$tab_d['id']][] = $del_ee->fields['id_doc'];
										$del_ee->delete();

									}
									foreach($tab_ee as $etap_id => $tab_doc) {
										foreach($tab_doc as $key => $id_doc_del) {
											if($id_doc_del > 0) {
												//on supprime les docs
												$del_doc = new docfile();
												$del_doc->open($id_doc_del);
												$del_doc->delete();
											}
										}

										//on supprime les etapes
										$del_act_etap = new action_etap();
										$del_act_etap->open($etap_id);
										$del_act_etap->delete();
									}
								}
							}
						}
						//Sinon on cr&eacute;e l'action dans tout les cas
						else $action->setugm(); // nouvelle action

						$action->setvalues($_POST,'action_');

						//GESTION DE L'UTILISATION D'UN MODELE
						if($is_model > 0 && $action_id > 0) {
							$act_model = new action();
							$act_model->open($is_model);
							//on copie les champs du model dans l'action
							$act_model->fields['libelle'] = $action->fields['libelle']; //on ne doit pas changer le libelle ... on recupere celui du post et on le copie dans le model pour ne pas le changer
							//on verifie si le teaser et la description ont étés modifiées par l'utilisateur
							if($action->fields['description'] != $act_model->fields['description'] && $action->fields['description'] != '')
								$act_model->fields['description'] = $action->fields['description'];
							if($action->fields['teaser'] != $act_model->fields['teaser'] && $action->fields['teaser'] != '')
								$act_model->fields['teaser'] = $action->fields['teaser'];
							//on reprend les infos sur les createurs / responsables ...
							$act_model->fields['id_user'] = $action->fields['id_user'];
							$act_model->fields['id_responsible'] = $action->fields['id_responsible'];
							$act_model->fields['id_organizer'] = $action->fields['id_organizer'];

							$action->fields = $act_model->fields;
							if($action_id > 0 && $parent) $action->fields['id'] = $action_id;
							else $action->fields['id'] = '';
							$action->fields['is_model'] = $is_model;

						}

						$djour=date("d/m/Y",$datedeb_timestp);
						$action->fields['datejour'] = business_datefr2us($djour);

						$djour=date("d/m/Y",$datefin_timestp);
						$action->fields['datefin'] = business_datefr2us($djour);

						$action->fields['datefin_insc'] = business_datefr2us($datefin_insc);
						//$action->fields['datejour'] =$datedeb_timestp;

						//gestion de la parentee (cas des evts sur pls jours : le premier a un id_parent = 0, les autres on met id_parent � 1)
						if($i==0) {
							$action->fields['id_parent'] = 0;
						}
						else {
							$action->fields['id_parent'] = $_SESSION['dims']['business_parent'];
						}

						$action->fields['heuredeb'] = sprintf("%02d:%02d:00",$actionx_heuredeb_h,$actionx_heuredeb_m);

						/*if ($actionx_duree) {// > 0 => calcul heure de fin en fonction de dur�e
							$action->fields['temps_prevu'] = $actionx_duree;
							$heurefin = $actionx_heuredeb_h*60+$actionx_heuredeb_m+$action->fields['temps_prevu'];
							$heurefin_h = ($heurefin-$heurefin%60)/60;
							$heurefin_m = $heurefin%60;
							$action->fields['heurefin'] = sprintf("%02d:%02d:00",$heurefin_h,$heurefin_m);
						}
						else {*/// r�cup la saisie de l'heure de fin
						$action->fields['heurefin'] = sprintf("%02d:%02d:00",$actionx_heurefin_h,$actionx_heurefin_m);
						$action->fields['temps_prevu'] = ($actionx_heurefin_h-$actionx_heuredeb_h)*60+$actionx_heurefin_m-$actionx_heuredeb_m;
						//}

						if (!isset($action_personnel)) $action->fields['personnel'] = 0;
						if (!isset($action_conges)) $action->fields['conges'] = 0;
						if (!isset($action_interne)) $action->fields['interne'] = 0;

						$action->fields['temps_passe'] = $action->fields['temps_prevu'];
						$action->fields['timestp_modify']=dims_createtimestamp();

						if (!(isset($action_personnel) && $action_personnel) || (isset($action_conges) && $action_conges)) {
							if (isset($nouveau_dossier) && $nouveau_dossier = "1" && $rech_dossier != '') {
								$dossier = new dossier();
								$dossier->fields['objet_dossier'] = $rech_dossier;
								$dossier->setugm();
								$actiondetail_dossier_id = $dossier->save();
								dims_create_user_action_log(dims_const::_PLANNING_ACTION_OUVRIRDOSSIER, $actiondetail_dossier_id);
								$ticket_title .= "'{$rech_dossier}'";
							}
							else $action->fields['dossier_id']=0;

							if (isset($nouveau_tiers) && $nouveau_tiers = "1" && $rech_tiers != '') {
								$tiers = new tiers();
								$tiers->fields['intitule'] = $rech_tiers;
								$tiers->fields['actif'] = 'Oui';
								$tiers->setugm();
								$actiondetail_tiers_id = $tiers->save();
								dims_create_user_action_log(dims_const::_PLANNING_ACTION_OUVRIRTIERS, $actiondetail_tiers_id);
								$ticket_title .= "'{$rech_tiers}'";
							}
							else $action->fields['tiers_id']=0;
						}

						if (isset($_SESSION['dims']['planning']['currentactionusers'])) {
							$action->utilisateurs = $_SESSION['dims']['planning']['currentactionusers'];
							unset($_SESSION['dims']['planning']['currentactionusers']);
						}

						if (isset($_SESSION['dims']['planning']['currentactionpartner'])) {
							$action->partenaires = $_SESSION['dims']['planning']['currentactionpartner'];
							unset($_SESSION['dims']['planning']['currentactionpartner']);
						}

						//Enregistrement des infos evenements
						if($action_type == dims_const::_PLANNING_ACTION_EVT) {
							//if($is_model == 0) {

								$action->setvalues($_POST,'evt_');

								$action->fields['supportrelease'] = 0;

								if(isset($_POST['supportrelease']) && !empty($_POST['supportrelease']))
									$action->fields['supportrelease'] = 1;

								if(isset($_POST['form_level']) && $_POST['form_level'] == 'on')
									$action->fields['niveau'] = 2;
								else
									$action->fields['niveau'] = 1;
							//}
							$action->fields['timestamp_release']	= $daterel_timestp;
							$action->fields['timestp_open']			= $dateopen_timestp;


						}

						if(isset($_SESSION['dims']['planning']['currentactionusersPart']))
							$id_action = $action->save($_SESSION['dims']['planning']['currentactionusersPart']);
						else
							$id_action = $action->save();

						if($action_type == dims_const::_PLANNING_ACTION_EVT) {
							// on regarde dans la liste des responsables en session
							$action->updateResps(dims_const::_SYSTEM_OBJECT_ACTION);
						}

						if(!isset($_SESSION['dims']['business_parent']))
							$_SESSION['dims']['business_parent'] = $id_action;

						if ($action_id>0)
							dims_create_user_action_log(_SYSTEM_ACTION_MODIFYACTION, "{$action->fields['libelle']} du $djour",dims_const::_DIMS_MODULE_SYSTEM,dims_const::_DIMS_MODULE_SYSTEM,$id_action,dims_const::_SYSTEM_OBJECT_ACTION);
						else
							dims_create_user_action_log(_SYSTEM_ACTION_ADDACTION, "{$action->fields['libelle']}  du $djour",dims_const::_DIMS_MODULE_SYSTEM,dims_const::_DIMS_MODULE_SYSTEM,$id_action,dims_const::_SYSTEM_OBJECT_ACTION);

						if($parent && $action->fields['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS' && $action_id == 0) {
							//INSCRIPTION AUTOMATIQUE DU CREATEUR
							$inscr = new event_insc();
							$inscr->init_description();

							$inscr->fields['id_action'] = $action->fields['id'];
							$inscr->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
							$inscr->fields['validate'] = 2;
							$inscr->fields['lastname'] = $_SESSION['dims']['user']['lastname'];
							$inscr->fields['firstname'] = $_SESSION['dims']['user']['firstname'];
							$inscr->fields['date_validate'] = date("YmdHis");
							$inscr->save();

						}

						//tout les ajouts suivant ne seront forc&eacute;ment QUE des enfants
						$parent = false;
						$_POST['action_id'] = '';
					}
				}

				//insertion des contacts directement rattaches
				if(!empty($_SESSION['dims']['planning_addct'])) {
					$inscr = new event_insc();
					foreach($_SESSION['dims']['planning_addct'] as $key => $id_ct) {
						//on recherche les infos du contact
						$cttoadd = new contact();
						$cttoadd->open($id_ct);

						$inscr->init_description();
						$inscr->fields['id_action'] = $id_action;
						$inscr->fields['id_contact'] = $id_ct;
						$inscr->fields['validate'] = 2;
						$inscr->fields['lastname'] = $cttoadd->fields['lastname'];
						$inscr->fields['firstname'] = $cttoadd->fields['firstname'];
						$inscr->fields['date_validate'] = date("YmdHis");
						$inscr->save();
					}
					unset($_SESSION['dims']['planning_addct']);
				}

				if(isset($_SESSION['dims']['business_parent'])) unset($_SESSION['dims']['business_parent']);
				// test si ticket a envoyer
				$dims_ticket_message=dims_load_securvalue("dims_ticket_message",dims_const::_DIMS_CHAR_INPUT,false,true);
				if ($dims_ticket_message!="") {
					// on construit la liste des personnes
					$_SESSION['dims']['tickets']['users_selected']=$action->utilisateurs;
					/*foreach ($action->utilisateurs as $u =>user) {
						[]=$user['id'];
					}*/
					require_once DIMS_APP_PATH . '/include/functions/tickets.php';
					dims_tickets_send($ticket_title, 'Envoi auto', true, 0, dims_const::_SYSTEM_OBJECT_ACTION, $id_action, $ticket_title,true);
					//dims_tickets_send("Demande de r&eacute;ponse � la question : \"".dims_strcut($quest->fields['question'],60)."\" (module {$_SESSION['dims']['currentmodule']['label']})", "Ceci est un message automatique envoy� suite � une demande de r&eacute;ponse � la question \"".dims_strcut($quest->fields['question'],60)."\" du module {$_SESSION['dims']['currentmodule']['label']}<br /><br />Vous pouvez acc�der � cette question pour y r&eacutepondre en cliquant sur le lien ci-dessous.", true, 0, _FAQ_OBJECT_QUESTION, $quest->fields['id'], dims_strcut($quest->fields['question'],60),true);
				}

				//dims_tickets_send(_BUSINESS_OBJECT_ACTION, $id_action, $ticket_title);
				if(is_array($_SESSION['dims']['browser']) && isset($_SESSION['dims']['browser']['pda'])) {
					if($action_type != dims_const::_PLANNING_ACTION_EVT) {
						?>
						<script language="javascript">
							history.go(-1);
						</script>
						<?php
					}
				}

				if($action_type == dims_const::_PLANNING_ACTION_EVT)
					//dims_redirect("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&dims_desktop=block&dims_action=public&action=view_admin_events");
					dims_redirect($dims->getScriptEnv()."?op=xml_planning_modifier_action&id=".$action->fields['id']);
				else
					dims_redirect("admin.php");

				die();
				break;

			case 'xml_planning_rechercher_tiers':
				include(DIMS_APP_PATH . '/modules/system/xml_planning_rechercher_tiers.php');
				break;

			case 'xml_planning_rechercher_dossiers':
				include(DIMS_APP_PATH . '/modules/system/xml_planning_rechercher_dossiers.php');
				break;

			case 'xml_planning_recherche_dispo':
				include(DIMS_APP_PATH . '/modules/system/xml_planning_rechercher_dispo.php');
				break;

			case 'xml_planning':
				ob_end_clean();
				include(DIMS_APP_PATH . '/modules/system/xml_planning.php');
				die();
				break;

			case 'detail_action_planning':
				ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_action=dims_load_securvalue('id_action',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_action>0) {
						require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
						$skin=new skin();
						echo $skin->open_simplebloc("<a href=\"#\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';initDisplayOptions(0);\"><img src=\"./common/img/no.png\" border=\"0\"></a>","","");
						echo "<div style=\"overflow:auto;position:relative;background:#FFFFFF;\">";

						$action = new action();
						$action->open($id_action);
						$tabcorrespmulti=array();
						$sql = "SELECT		action_id,user_id,u.firstname,u.lastname,participate
								FROM		dims_user as u
								INNER JOIN	dims_mod_business_action_utilisateur as au
								ON			au.user_id = u.id
								AND			au.action_id = :idaction";

						$res=$db->query($sql, array(
							':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
						));

						while ($f=$db->fetchrow($res)) {
								$tabcorrespmulti[$f['action_id']][$f['user_id']]=strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname'];
								$tabparticipate[$f['action_id']][$f['user_id']]=$f['participate'];
						}

						if ($action->fields['type']==2 || $action->fields['type'] == dims_const::_PLANNING_ACTION_TSK || ($action->fields['personnel']==0 && (isset($tabcorrespmulti[$action->fields['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) || $action->fields['acteur']==$_SESSION['dims']['userid'])) {

								$da=array_reverse(explode("-",$action->fields['datejour']));

								$detail = implode($da,"/")." ".$_DIMS['cste']['_FROM']." ".substr($action->fields['heuredeb'],0,5)." ".strtolower($_DIMS['cste']['_DIMS_LABEL_MAIL_TO'])." ".substr($action->fields['heurefin'],0,5);
								if ($action->fields['libelle'] != '') {
									$detail .= '<div><b>'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</b> : '.$action->fields['libelle'].'</div>';
								}

								if ($action->fields['typeaction'] != '') {
									 $title =$action->fields['typeaction'];
									 if (isset($_DIMS['cste'][$action->fields['typeaction']])) $title =$_DIMS['cste'][$action->fields['typeaction']];

									$detail .= '<div><b>'.$_DIMS['cste']['_TYPE'].'</b> : '.$title.'</div>';
								}
								if ($action->fields['description'] != '') {
									$detail .= '<div><b>'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</b> : '.dims_strcut($action->fields['description'],600).'</div>';
								}

								if ($action->fields['personnel']) {
									$detail .= "<div><b>".$_DIMS['cste']['_PERSO']."</b></div>";
								}

								if ($action->fields['conges']) {
									$detail .= "<div><b>".$_DIMS['cste']['_DIMS_LABEL_CONGE']."</b></div>";
								}

								$pers = array();
								$persinfo = array();
								if (isset($tabcorrespmulti[$action->fields['id']])) {
										foreach($tabcorrespmulti[$action->fields['id']] as $iduser=>$nom) {
												// test si participe ou pour info
												if (isset($tabparticipate[$action->fields['id']][$iduser]) && $tabparticipate[$action->fields['id']][$iduser]==0) $persinfo[]=$nom;
												else $pers[]=$nom;

												if (sizeof($pers)>0) $detail .= '<div><b>'.$_DIMS['cste']['_DIMS_PARTICIP'].'</b> : '.implode(', ',$pers).'</div>';
												// calcul du pour info
												if (sizeof($pers)>0) $detail .= '<div><b>'.$_DIMS['cste']['_DIMS_TOINFO'].'</b> : '.implode(', ',$persinfo).'</div>';
										}
								}
						}
						else {
							if (isset($action->fields['firstname']) && isset($action->fields['lastname'])) {
								$detail=strtoupper(substr($action->fields['firstname'],0,1)).". ".$action->fields['lastname']."<br>".$_DIMS['cste']['_DIMS_LABEL_NOT_AVAILABLE'];
							}
							else {
								$detail="<br>".$_DIMS['cste']['_DIMS_LABEL_NOT_AVAILABLE'];
							}
						}
						echo $detail;
						/*
						if ($action['personnel']==0 && (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) || $action['acteur']==$_SESSION['dims']['userid']) {
								$detailpopup=$action['detail'];
								$onclick="onclick=\"javascript:location.href='admin.php?op=xml_planning_modifier_action&id=".$action['id']."'\"";
								$cursor="pointer";
						}
						else {
								$detailpopup=strtoupper(substr($action['firstname'],0,1)).". ".$action['lastname']."<br>Non disponible";
								$onclick="";
								$cursor="";
						}*/
						echo "</div>";

						echo $skin->close_simplebloc();

					}
				}
				die();
				break;
			default:
				include(DIMS_APP_PATH . '/modules/system/public_planning_accueil.php');
				break;
		}
		break;
	case _BUSINESS_CAT_TIERS:
		include(DIMS_APP_PATH . '/modules/system/public_tiers.php');
		break;

	case _BUSINESS_CAT_PROJECT:
		//echo $skin->create_pagetitle($_SESSION['dims']['modulelabel'],'100%');
		include(DIMS_APP_PATH . '/modules/system/public_projects.php');
		break;

	case _BUSINESS_CAT_PRODUIT:
		//echo $skin->create_pagetitle($_SESSION['dims']['modulelabel'],'100%');
		include(DIMS_APP_PATH . '/modules/system/public_produits.php');
		break;

	case _BUSINESS_CAT_SUIVI:
		//echo $skin->create_pagetitle($_SESSION['dims']['modulelabel'],'100%');
		include(DIMS_APP_PATH . '/modules/system/public_suivis.php');
		break;

	/*
	case _BUSINESS_CAT_PLANNING:
		//echo $skin->create_pagetitle($_SESSION['dims']['modulelabel'],'100%');
		switch($op) {
			default:
				include(DIMS_APP_PATH . '/modules/system/public_planning.php');
			break;
		}
	break;

	*/
		case _BUSINESS_CAT_EVENTS:

			require_once DIMS_APP_PATH . '/modules/system/public_events.php';
			break;

}
