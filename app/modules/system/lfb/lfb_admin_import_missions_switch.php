<?php

//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
ini_set('max_execution_time',-1);
ini_set('memory_limit','512M');

switch($import_op) {
	default:
			echo '<div style="margin:10px;text-align:center;width:100%">
				<form action="#" method="post" enctype="multipart/form-data" id="import_step1">';
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("import_op", "1");
			$token->field("srcfilect");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
			echo '<input type="hidden" name="import_op" value="1" />
				'.$_DIMS['cste']['_DIMS_LABEL_IMPORTSRC'].'&nbsp;*:&nbsp;<input type="file" name="srcfilect"/><br/><br/>';
			echo '<div style="text-align:center;width:100%;flat:left;">'.
				dims_create_button_nofloat($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('import_step1').submit();")
				.'</div>
				</form>
				</div>';
		break;

	case 1:
		//Extraction du fichier : génération de $_SESSION['dims']['IMPORT_ARRAY'] (array des enregistrement)
		if(!empty($_FILES['srcfilect'])) {
			unset($_SESSION['dims']['import_current_similar'], $_SESSION['dims']['import_current_user_id'], $_SESSION['dims']['import_count_contact_similar']);
			unset($_SESSION['dims']['import_contact_similar_count'],$_SESSION['dims']['import_contact_similar']);
			unset($_SESSION['dims']['IMPORT_ARRAY']);
			unset($_SESSION['dims']['IMPORT_SIMILAR_CT']);
			unset($_SESSION['dims']['IMPORT_MISSIONS']);
			unset($_SESSION['dims']['DB_CONTACT']);

			$num = 1;
			$cpt_not_exist = 0;
			$cpt_exist = 0;
			$cpt_err_intit = 0;
			$cpt_not_exist_ct = 0;
			$cpt_not_exist_ent = 0;
			$cpt_all_exist = 0;
			$cpt_new_ct_no_ent = 0;
			$cpt_old_ct_no_ent = 0;

			if($_FILES['srcfilect']['name'] != ''){
				$sql = "SELECT id,firstname, lastname, email FROM dims_mod_business_contact WHERE 1";
				$res = $db->query($sql);
				if($db->numrows()>0){
					while($data = $db->fetchrow($res))
						$_SESSION['dims']['DB_CONTACT'][$data['id']] = $data;
				}

				$created = array();
				$errors = array();
				$_FIELDS = array();
				$_SESSION['dims']['IMPORT_ARRAY']=array();
				$_SESSION['dims']['IMPORT_MISSIONS']=array();
				$_SESSION['dims']['IMPORT_CONTACT']=array();

				$handle = fopen($_FILES['srcfilect']['tmp_name'], "r");
				$_CURRENT_KEY = 0;
				$_CURRENT_LINE = 1;
				$_PREV_LETTER = "";
				$_PREV_PREV_LETTER = "";
				$_PREV_PREV_PREV_LETTER = "";
				$_INTO_KEY = false;
				while ($line = fgets($handle)) {
					// Ligne de description de la structure du fichier
					if(count($_FIELDS) == 0) {
						$content = explode(',',$line);
						foreach($content AS $key => $value) {
							$value = strtolower(trim(str_replace('"','',$value)));
							$value = preg_replace('#^cc_#','',$value);
							//On vérifie si on connait la clé
							switch($value) {
								case "mission":
								case "mission name":
									$value = "mission";
									break;
								case "date":
									$value = "date";
									break;
								case "firstname":
								case "prenom":
								case "pr".utf8_decode("é")."nom":
								case "first name":
									$value = "firstname";
									break;

								case "lastname":
								case "nom":
								case "last name":
								case "surname":
								case "surnom":
									$value = "lastname";
									break;

								case "middlename":
								case "midle name":
								case "deuxi".utf8_decode("è")."mepr".utf8_decode("é")."nom":
									$value = "middlename";
									break;

								case "email":
								case "email address":
								case "e-mail address":
								case "courriel":
								case "emailaddress":
								case "mail":
								case "e-mail":
								case "adressedemessagerie":
									$value = "email";
									break;

								case "adresse site":
								case "url":
								case "website":
									$value = "website";
									break;

								case "company":
								case "societe":
								case "soci".utf8_decode("é")."t".utf8_decode("é") :
								case "company name":
								case "companyname":
								case "entreprise":
								case "name":
									$value = "company";
									break;

								case "company description":
									$value = "companydescription";
									break;

								case "businesspostalcode":
								case "codepostalbureau":
								case "business postal code":
								case "code postal":
								case "codepostal":
								case "zip":
								case "postal_code":
									$value = "cp";
									break;

								case "city":
								case "localite":
								case "ville":
								case "businesscity":
								case "business city":
								case "villebureau":
								case "address":
									$value = "ville";
									break;

								case "ruebureau":
								case "businessstreet":
								case "business street":
								case "street":
									$value = "address";
									break;

								case "ruebureau2":
								case "businessstreet2":
								case "business street 2":
									$value = "address2";
									break;

								case "ruebureau3":
								case "businessstreet3":
								case "business street 3":
									$value = "address3";
									break;

								case "paysregionbureau":
								case "business country/region":
								case "businnescountryregion":
								case "d".utf8_decode("é")."pr".utf8_decode("é")."gionbureau":
									$value = "country";
									break;

								case "civilite":
								case "title":
								case "titre":
								case "appellation":
									$value = "civilite";
									break;

								case "job title":
								case "profession":
								case "function":
									$value = "professional";
									break;

								case "mobile phone":
								case "mobilephone":
								case "telmobile":
								case "carphone":
								case "mobile":
									$value = "mobile";
									break;

								case "telephonebureau":
								case "businessphone":
								case "business phone":
								case "phone":
								case "t".utf8_decode("é")."l".utf8_decode("é")."phone":
								case "office phone":
									$value = "phone";
									break;

								case "telephonebureau2":
								case "businessphone2":
								case "business phone2":
									$value = "phone2";
									break;

								case "telecopiebureau":
								case "businessfax":
								case "business fax":
								case "fax":
									$value = "fax";
									break;

								case "tag":
								case "tags":
								case "business fax":
									$value = "tag";
									break;

								case 'notes': //Traitement des commentaires
								case 'comments':
									$value = "comment";
									break;
							}
							$_FIELDS[$key] = $value;
						}

						$_NB_COL = count($_FIELDS);
					}
					else {
						//Variable
						for($i=0;$i<(strlen($line));$i++) {
							$letter = $line[$i];
							if($letter != chr(13)) {
								switch ($letter) {
									// On rencontre une double quote
									case '"':
										if(($_PREV_LETTER == ",")&&(!$_INTO_KEY)) {
											$_CURRENT_KEY++;
											if($_CURRENT_KEY == $_NB_COL) {
												$_CURRENT_KEY = 0;
												$_CURRENT_LINE++;
											}
											$_INTO_KEY = true;
										}
										elseif($_PREV_LETTER == "") {
											$_CURRENT_KEY = 0;
											$_INTO_KEY = true;
										}
										elseif(($_PREV_LETTER == "\\")&&($_INTO_KEY)) {
											if(isset($_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
												$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
											}
											else {
												$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
											}
										}
										elseif($_PREV_LETTER == chr(10) && !$_INTO_KEY) {
											$_INTO_KEY = true;
											$_CURRENT_KEY = 0;
										}
										break;

									case ',':
										if(($_PREV_LETTER == '"') && $_INTO_KEY) {
											$_INTO_KEY = false;
										}
										elseif($_PREV_LETTER == ',' && !$_INTO_KEY) {
											$_CURRENT_KEY++;
											if($_CURRENT_KEY == $_NB_COL) {
												$_CURRENT_KEY = 0;
												$_CURRENT_LINE++;
											}
										}
										elseif(($_PREV_LETTER == ' ') && !$_INTO_KEY) {
											$_CURRENT_KEY++;
											if($_CURRENT_KEY == $_NB_COL) {
												$_CURRENT_KEY = 0;
												$_CURRENT_LINE++;
											}
										}
										elseif($_INTO_KEY) {
											if(isset($_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
												$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
											}
											else {
												$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
											}
										}
										break;

									case chr(10):
										switch($_PREV_LETTER) {
											case '"':
												if($_PREV_PREV_LETTER == ',' && $_INTO_KEY) {
													if(isset($_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
														$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
													}
													else {
														$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
													}
												}
												elseif($_PREV_PREV_LETTER != ',' && $_INTO_KEY) {
													$_CURRENT_LINE++;

													$_INTO_KEY = false;
													$_CURRENT_KEY=0;
												}
											break;

											case ",":
												if(!$_INTO_KEY) {
													$_CURRENT_LINE++;

													$_INTO_KEY = false;
													$_CURRENT_KEY=0;
												}
												break;

											case chr(10):
												if($_INTO_KEY) {
													if(isset($_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
														$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
													}
													else {
														$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
													}
												}
												break;

											default:
												if($_INTO_KEY) {
													if(isset($_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
														$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
													}
													else {
														$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
													}
												}
												break;
										}
										break;

									default:
										if($_INTO_KEY && $letter != chr(13)) {
											if(isset($_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
												$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
											}
											else {
												$_SESSION['dims']['IMPORT_ARRAY'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
											}
										}
										break;
								}
								$_PREV_PREV_LETTER = $_PREV_LETTER;
								$_PREV_LETTER = $letter;
							}
						}
					}
				}

				foreach($_SESSION['dims']['IMPORT_ARRAY'] as $line) {
					if((isset($line['mission']) && !empty($line['mission'])) &&
					   (isset($line['date']) && !empty($line['date']))) {
						$_SESSION['dims']['IMPORT_MISSIONS']['mission']		= $line['mission'];
						$_SESSION['dims']['IMPORT_MISSIONS']['date']		= $line['date'];
					}
				}

				$_SESSION['dims']['IMPORT_MISSIONS']['date'] = str_replace('.', '/', $_SESSION['dims']['IMPORT_MISSIONS']['date']);

				if(strpos($_SESSION['dims']['IMPORT_MISSIONS']['date'], '-') === false) {
					$_SESSION['dims']['IMPORT_MISSIONS']['date_deb'] = dims_local2timestamp($_SESSION['dims']['IMPORT_MISSIONS']['date']);
					$_SESSION['dims']['IMPORT_MISSIONS']['date_fin'] = $_SESSION['dims']['IMPORT_MISSIONS']['date_deb'];
				}
				else {
					$date	= explode('/', $_SESSION['dims']['IMPORT_MISSIONS']['date']);
					$jours	= explode('-', $date[0]);

					$date_deb = $jours[0].'/'.$date[1].'/'.$date[2];
					$date_fin = $jours[1].'/'.$date[1].'/'.$date[2];

					$_SESSION['dims']['IMPORT_MISSIONS']['date_deb'] = dims_local2timestamp($date_deb);
					$_SESSION['dims']['IMPORT_MISSIONS']['date_fin'] = dims_local2timestamp($date_fin);
				}

				$date_deb = dims_timestamp2local($_SESSION['dims']['IMPORT_MISSIONS']['date_deb']);
				$date_fin = dims_timestamp2local($_SESSION['dims']['IMPORT_MISSIONS']['date_fin']);

				echo '<div style="margin-left: 15px; margin-top: 20px; margin-bottom: 20px;">';
				echo $_SESSION['cste']['_DIMS_IMPORT_LABEL_MISSION'].' : '.$_SESSION['dims']['IMPORT_MISSIONS']['mission'].'<br />';
				echo $_SESSION['cste']['_INFOS_START_DATE'].' : '.$date_deb['date'].'<br />';
				echo $_SESSION['cste']['_INFOS_END_DATE'].' : '.$date_fin['date'].'<br />';
				echo '<p>';
				echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?op=admin_actions&import_op=2');");
				echo '</p>';
				echo '</div>';

			}
			else {
				echo '<p style="text-align:center;">'.$_DIMS['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'].'</p>
				<div style="text-align:center;">
				'.dims_create_button_nofloat($_DIMS['cste']['_IMPORT_RETURN_TO_STEP1'], "./common/img/public.png", "dims_redirect('./admin.php?op=dims_actions&import_op=0');").'
				</div>';
			}
		}
		else {
			dims_redirect('admin.php?op=dims_actions&import_op=0');
		}
		break;

	case 2:

		if(!isset($_SESSION['dims']['IMPORT_MISSIONS']['id_action']) && empty($_SESSION['dims']['IMPORT_MISSIONS']['id_action'])) {

			$date_deb = dims_timestamp2local($_SESSION['dims']['IMPORT_MISSIONS']['date_deb']);
			$date_deb = explode('/', $date_deb['date']);

			$date_fin = dims_timestamp2local($_SESSION['dims']['IMPORT_MISSIONS']['date_fin']);
			$date_fin = explode('/', $date_fin['date']);

			$timestp_deb = mktime(0,0,0,$date_deb[1],$date_deb[0],$date_deb[2]);
			$timestp_fin = mktime(0,0,0,$date_fin[1],$date_fin[0],$date_fin[2]);

			$delta = $timestp_fin - $timestp_deb;

			$delta_jour = date('j',$delta);

			$id_parent = 0;
			for($i = 0; $i < $delta_jour; $i++) {
				$action = new action();

				$djour=date("d/m/Y",$timestp_deb + (86400 * $i));
				$dfin =date("d/m/Y",$timestp_fin);

				$action->fields['id_parent']	= $id_parent;
				$action->fields['libelle']		= $_SESSION['dims']['IMPORT_MISSIONS']['mission'];
				$action->fields['datejour']		= business_datefr2us($djour);
				$action->fields['datefin']		= business_datefr2us($dfin);
				$action->fields['heuredeb']		= '08:00:00';
				$action->fields['heurefin']		= '18:00:00';
				$action->fields['type']			= dims_const::_PLANNING_ACTION_EVT;
				$action->fields['allow_fo']		= 1;
				$action->fields['close']		= 0;
				$action->fields['niveau']		= 1;
				$action->fields['id_user']		= $_SESSION['dims']['userid'];

				$id = $action->save();

				if($id_parent == 0) {
					$_SESSION['dims']['IMPORT_MISSIONS']['id_action'] = $id;
					$id_parent = $id;
				}
			}
		}

		$nb_know = 0;
		foreach($_SESSION['dims']['IMPORT_ARRAY'] as $key => $line) {
			if((!isset($line['checked']) || $line['checked'] == 0 ) &&
				(isset($line['email']) && !empty($line['email'])) &&
			   (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $line['email']))) {

				$sql = 'SELECT id FROM dims_mod_business_contact WHERE email LIKE :email ';

				$ress = $db->query($sql, array(
					':email' => $line['email']
				));

				if($db->numrows($ress) > 0) {
					$result = $db->fetchrow($ress);

					$sql_verif = 'SELECT id
								FROM dims_mod_business_event_inscription
								WHERE id_action = :idaction
								AND id_contact = :idcontact ';

					$ress_verif = $db->query($sql_verif, array(
						':idaction'		=> $_SESSION['dims']['IMPORT_MISSIONS']['id_action'],
						':idcontact'	=> $result['id']
					));

					if($db->numrows($ress_verif) == 0) {

						$inscrip = new inscription();

						$inscrip->fields['id_action']	= $_SESSION['dims']['IMPORT_MISSIONS']['id_action'];
						$inscrip->fields['id_contact']	= $result['id'];
						$inscrip->fields['validate']	= 2;
						$inscrip->fields['email']		= $line['email'];
						$inscrip->fields['lastname']	= (isset($line['lastname'])) ? $line['lastname'] : '';
						$inscrip->fields['firstname']	= (isset($line['firstname'])) ? $line['firstname']: '';
						$inscrip->fields['company']		= (isset($line['company'])) ? $line['company']: '';
						$inscrip->fields['date_validate']= date('Ymd000000');

						$inscrip->save();

					}

					if(isset($line['company']) && !empty($line['company'])) {
						$sql_ent = 'SELECT id FROM dims_mod_business_tiers WHERE intitule = :intitule ';

						$ress_ent = $db->query($sql_ent, array(
							':intitule' => $line['company']
						));

						if($db->numrows($ress_ent) > 0) {
							$result_ent = $db->fetchrow($ress_ent);

							$sql_verif_ent = 'SELECT action_id
											FROM dims_mod_business_action_detail
											WHERE action_id = :actionid
											AND tiers_id = :tiersid ';

							$ress_verif_ent = $db->query($sql_verif_ent, array(
								':actionid'	=> $_SESSION['dims']['IMPORT_MISSIONS']['id_action'],
								':tiersid'	=> $result_ent['id']
							));

							if($db->numrows($ress_verif_ent) == 0) {
								$action_detail = new action_detail();

								$action_detail->fields['contact_id'] = $_SESSION['dims']['userid'];
								$action_detail->fields['action_id'] = $_SESSION['dims']['IMPORT_MISSIONS']['id_action'];
								$action_detail->fields['tiers_id'] = $result_ent['id'];
								$action_detail->fields['dossier_id'] = 0;
								$action_detail->fields['duree'] = 0;

								$action_detail->save();
							}

							$new_link = new tiersct();
							$new_link->init_description();

							$new_link->fields['id_tiers'] = $result_ent['id'];
							$new_link->fields['id_contact'] = $result['id'];
							$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
							$new_link->fields['link_level'] = 2;
							$new_link->fields['date_create'] = date("YmdHis");
							$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

							$new_link->save();

						}
					}

					$_SESSION['dims']['IMPORT_ARRAY'][$key]['ct_checked'] = 1;
					$_SESSION['dims']['IMPORT_ARRAY'][$key]['ct_id'] = $result['id'];
					$nb_know++;
				}
				elseif(isset($line['lastname']) && !empty($line['lastname']) &&
					   isset($line['firstname']) && !empty($line['firstname'])) {
					$_SESSION['dims']['IMPORT_SIMILAR_CT'][] = $key;
					$_SESSION['dims']['IMPORT_ARRAY'][$key]['ct_checked'] = 0;
				}
				else
					unset($_SESSION['dims']['IMPORT_ARRAY'][$key]);
			}
			elseif(isset($line['lastname']) && !empty($line['lastname']) &&
				   isset($line['firstname']) && !empty($line['firstname'])) {
				$_SESSION['dims']['IMPORT_SIMILAR_CT'][] = $key;
				$_SESSION['dims']['IMPORT_ARRAY'][$key]['ct_checked'] = 0;
			}
			else
				unset($_SESSION['dims']['IMPORT_ARRAY'][$key]);
		}

		echo '<div>';
		echo '<p>';
		echo $nb_know.' '.$_SESSION['cste']['_IMPORT_MISSION_CONTACT_KNOW'];
		echo '</p>';
		echo '<div>';
		echo dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?op=admin_actions&import_op=3');");
		echo '</div>';
		echo '<table width="100%">';
		if($nb_know > 0) {
			$class = 'trl1';
			foreach($_SESSION['dims']['IMPORT_ARRAY'] as $key => $line) {
				if(isset($line['ct_checked']) && $line['ct_checked'] == 1) {

					echo '<tr class="'.$class.'">';
					echo '<td>';
					echo $line['lastname'];
					echo '</td>';
					echo '<td>';
					echo $line['firstname'];
					echo '</td>';
					echo '<td>';
					echo $line['email'];
					echo '</td>';
					echo '</tr>';

					$class = ($class == 'trl1') ? 'trl2' : 'trl1';
				}
			}
		}
		else
			echo '<tr><td>'.$_SESSION['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr>';

		echo '</table>';
		echo '</div>';
		echo '<div>';
		echo dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?op=admin_actions&import_op=3');");
		echo '</div>';

		break;
	case 3:
		if(isset($_POST) && !empty($_POST)) {
			$idsimilar = dims_load_securvalue('id_similar', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$key = $_SESSION['dims']['IMPORT_SIMILAR_CT'][$idsimilar];
			$line = $_SESSION['dims']['IMPORT_ARRAY'][$key];

			$id_to_attach = dims_load_securvalue('id_ct_to_attach', dims_const::_DIMS_NUM_INPUT, true, true, true);

			if($id_to_attach == -1) {

				$ct = new contact();

				$ct->fields['lastname'] = $line['firstname'];
				$ct->fields['firstname'] = $line['lastname'];
				$ct->fields['civilite'] = (isset($line['civilite'])) ? $line['civilite'] : '';
				$ct->fields['phone'] = (isset($line['phone'])) ? $line['phone'] : '';
				$ct->fields['email'] = (isset($line['email'])) ? $line['email'] : '';
				$ct->fields['id_user_create'] = $_SESSION['dims']['userid'];

				$id_to_attach = $ct->save();
			}
			else {
				$ct = new contact();
				$ct->open($id_to_attach);

				if(empty($ct->fields['email']) && isset($line['email']))
					$ct->fields['email'] = isset($line['email']);

				if(empty($ct->fields['civilite']) && isset($line['civilite']))
					$ct->fields['civilite'] = isset($line['civilite']);

				if(empty($ct->fields['phone']) && isset($line['phone']))
					$ct->fields['phone'] = isset($line['phone']);

				$ct->save();
			}
			$sql_verif = 'SELECT id
					FROM dims_mod_business_event_inscription
					WHERE id_action = :idaction
					AND id_contact = :idcontact ';

			$ress_verif = $db->query($sql_verif, array(
				':idaction'		=> $_SESSION['dims']['IMPORT_MISSIONS']['id_action'],
				':idcontact'	=> $id_to_attach
			));

			if($db->numrows($ress_verif) == 0) {

				$inscrip = new inscription();

				$inscrip->fields['id_action']	= $_SESSION['dims']['IMPORT_MISSIONS']['id_action'];
				$inscrip->fields['id_contact']	= $id_to_attach;
				$inscrip->fields['validate']	= 2;
				$inscrip->fields['email']		= (isset($line['email'])) ? $line['email'] : '';
				$inscrip->fields['lastname']	= (isset($line['lastname'])) ? $line['lastname'] : '';
				$inscrip->fields['firstname']	= (isset($line['firstname'])) ? $line['firstname']: '';
				$inscrip->fields['company']		= (isset($line['company'])) ? $line['company']: '';
				$inscrip->fields['date_validate']= date('Ymd000000');

				$inscrip->save();

			}

			if(isset($line['company']) && !empty($line['company'])) {
				$sql_ent = 'SELECT id FROM dims_mod_business_tiers WHERE intitule = :intitule ';

				$ress_ent = $db->query($sql_ent, array(
					':intitule'	=> $line['company']
				));

				if($db->numrows($ress_ent) > 0) {
					$result_ent = $db->fetchrow($ress_ent);

					$sql_verif_ent = 'SELECT action_id
									FROM dims_mod_business_action_detail
									WHERE action_id = :actionid
									AND tiers_id = :tiersid ';

					$ress_verif_ent = $db->query($sql_verif_ent, array(
						':idaction'		=> $_SESSION['dims']['IMPORT_MISSIONS']['id_action'],
						':tiersid'	=> $result_ent['id']
					));

					if($db->numrows($ress_verif_ent) == 0) {
						$action_detail = new action_details();

						$action_detail->fields['contact_id'] = $_SESSION['dims']['userid'];
						$action_detail->fields['action_id'] = $_SESSION['dims']['IMPORT_MISSIONS']['id_action'];
						$action_detail->fields['tiers_id'] = $result_ent['id'];
						$action_detail->fields['dossier_id'] = 0;
						$action_detail->fields['duree'] = 0;

						$action_detail->save();
					}

					$new_link = new tiersct();
					$new_link->init_description();

					$new_link->fields['id_tiers'] = $result_ent['id'];
					$new_link->fields['id_contact'] = $id_to_attach;
					$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
					$new_link->fields['link_level'] = 2;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

					$new_link->save();

				}
			}

			$_SESSION['dims']['IMPORT_ARRAY'][$key]['ct_checked'] = 1;
			$_SESSION['dims']['IMPORT_ARRAY'][$key]['ct_id'] = $id_to_attach;

			unset($_SESSION['dims']['IMPORT_SIMILAR_CT'][$idsimilar]);
		}


		$unknow_ct = count($_SESSION['dims']['IMPORT_SIMILAR_CT']);
		if($unknow_ct > 0) {
			echo '<div>';
			echo '<p>';
			echo $unknow_ct.' '.$_SESSION['cste']['_DIMS_IMPORT_MISSION_STILL_UNKNOW'];
			echo '</p>';
			echo '<div>';

			reset($_SESSION['dims']['IMPORT_SIMILAR_CT']);
			$key = each($_SESSION['dims']['IMPORT_SIMILAR_CT']);

			$line = $_SESSION['dims']['IMPORT_ARRAY'][$key['value']];

			echo '<form method="POST" action="#" name="similar_ct" id="similar_ct">';
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("id_similar", $key['key']);
			$token->field("id_ct_to_attach");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
			echo '<input type="hidden" name="id_similar" value="'.$key['key'].'" />';
			echo '<table width="100%" style="border-collapse: collapse;">';
			echo '<tr>';
			echo '<td width="15%">';
			echo $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT'];
			echo '</td>';
			echo '<td width="20%" text-align="left">';
			echo $line['firstname'];
			echo '</td>';
			echo '<td width="20%" text-align="left">';
			echo $line['lastname'];
			echo '</td>';
			echo '<td>&nbsp;</td>';
			echo '</tr>';

			$class = 'trl1';

			foreach($_SESSION['dims']['DB_CONTACT'] as $value) {
				$lev_nom = levenshtein(strtoupper($line['lastname']), strtoupper($value['lastname']));
				$coef_nom = $lev_nom - (ceil(strlen($line['lastname'])/4));

				$lev_pre = levenshtein(strtoupper($line['firstname']), strtoupper($value['firstname']));
				$coef_pre = $lev_pre - (ceil(strlen($line['firstname'])/4));

				$coef_tot = $coef_nom + $coef_pre;
//				  echo "levenshtein : ".$tab_contact_new['lastname']." ".$tab_contact_new['firstname']." et ".$tab_contact['lastname']." ".$tab_contact['firstname']." le total : ".$coef_tot."<br/>";
//				  echo "les variables : lev du nom = ".$lev_nom." ; lev du prenom = ".$lev_pre." ; coef nom = ".$coef_nom." ; coef prenom = ".$coef_pre."<br/><br/>";
				if($coef_nom<=1 && $coef_tot < 2) {
					echo '<tr class="'.$class.'">';
					echo '<td>&nbsp;</td>';
					echo '<td>'.$value['firstname'].'</td>';
					echo '<td>'.$value['lastname'].'</td>';
					echo '<td>'.$_SESSION['cste']['_DIMS_IMPORT_CT_SAME'].'</td>';
					echo '<td><input type="radio" name="id_ct_to_attach" value="'.$value['id'].'"></td>';
					echo '</tr>';

					$class = ($class == 'trl1') ? 'trl2' : 'trl1';
				}
			}
			echo '<tr class="'.$class.'">';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>'.$_SESSION['cste']['_DIMS_IMPORT_CT_NO_SAME'].'</td>';
			echo '<td><input checked="checked" type="radio" name="id_ct_to_attach" value="-1"></td>';
			echo '</tr>';
			echo '</table>';
			echo '</form>';
			echo '<div>';
			echo dims_create_button($_DIMS['cste']['_DIMS_VALID'], "./common/img/publish.png", "dims_getelem('similar_ct').submit();");
			echo '</div>';

			echo '<div style="text-align:right; clear: both;">'.
				dims_create_button_nofloat($_DIMS['cste']['_IMPORT_GO_NEXT_STEP'], "./common/img/public.png", "dims_redirect('./admin.php?op=admin_actions&import_op=4');").'
				</div>';
			echo '<div style="float:right;border:1px solid #738CAD;width:150px;padding:5px;margin-top:5px;"><img src="./common/img/warning.png" alt="/!\">&nbsp;'.$_DIMS['cste']['_IMPORT_MISSION_WARN_STEP3'].'</div>';


		}
		else {
			echo '<div>';
			echo '<p>';
			echo $_SESSION['cste']['_DIMS_IMPORT_MISSION_NO_INKNOW'];
			echo '</p>';
			echo '<div>';
			echo dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?op=admin_actions&import_op=4');");
		}

		break;
	case 4:


		$date_deb = dims_timestamp2local($_SESSION['dims']['IMPORT_MISSIONS']['date_deb']);
		$date_fin = dims_timestamp2local($_SESSION['dims']['IMPORT_MISSIONS']['date_fin']);

		echo '<div style="margin-left: 15px; margin-top: 20px; margin-bottom: 20px;">';
		echo $_SESSION['cste']['_DIMS_IMPORT_LABEL_MISSION'].' : '.$_SESSION['dims']['IMPORT_MISSIONS']['mission'].'<br />';
		echo $_SESSION['cste']['_INFOS_START_DATE'].' : '.$date_deb['date'].'<br />';
		echo $_SESSION['cste']['_INFOS_END_DATE'].' : '.$date_fin['date'].'<br />';
		echo $_SESSION['cste']['_IMPORT_IMPORTED_CONTACTS'].' : <br />';

		echo '<table width="100%">';
		$class = 'trl1';
		$contacts = false;
		foreach($_SESSION['dims']['IMPORT_ARRAY'] as $key => $line) {
			if(isset($line['lastname']) && !empty($line['lastname']) &&
			   isset($line['firstname']) && !empty($line['firstname']) &&
			   isset($line['ct_checked']) && $line['ct_checked'] == 1) {
				$contacts = true;

				echo '<tr class="'.$class.'">';
				echo '<td>';
				echo $line['lastname'];
				echo '</td>';
				echo '<td>';
				echo $line['firstname'];
				echo '</td>';
				echo '<td>';
				echo (isset($line['email'])) ? $line['email'] : 'n/a';
				echo '</td>';
				echo '</tr>';

				$class = ($class == 'trl1') ? 'trl2' : 'trl1';
			}
		}
		if(!$contacts) {
			echo '<tr><td>'.$_SESSION['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr>';
		}
		echo '</table>';
		echo '<p>';
		echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?op=admin_actions');");
		echo '</p>';
		echo '</div>';

		unset($_SESSION['dims']['IMPORT_ARRAY']);
		unset($_SESSION['dims']['IMPORT_SIMILAR_CT']);
		unset($_SESSION['dims']['IMPORT_MISSIONS']);
		unset($_SESSION['dims']['DB_CONTACT']);

		break;
}

?>
