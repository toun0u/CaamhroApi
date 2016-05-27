<?php
require_once(DIMS_APP_PATH . "/modules/system/class_ct_link.php");
require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
require_once(DIMS_APP_PATH . '/modules/system/class_commentaire.php');
require_once(DIMS_APP_PATH . '/modules/system/class_inscription.php');

function verifContact($current_line){
	//dims_print_r($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
	$db = dims::getInstance()->getDb();
	$_SESSION['dims']['RL']++;
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']))
		str_replace("'","",$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']);
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['firstname']) && isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['lastname']) && isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])){
		$verif_email = explode('@',$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']);

		if(count($verif_email) > 1) {
			// verification ces noms
			// nouveau cas pour supprimer les quotes => fichier P. Steichen
			foreach ($_SESSION['dims']['IMPORT_CONTACT'][$current_line] as $id=>$elem) {
				if (substr($elem,0,1)=="'") $_SESSION['dims']['IMPORT_CONTACT'][$current_line][$id]=substr($elem,1);
				if (substr($elem,strlen($elem)-1,1)=="'") $_SESSION['dims']['IMPORT_CONTACT'][$current_line][$id]=substr($elem,0,strlen($elem)-1);
			}
			return true;
		}else{

			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname']))
				$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname'] = "<span style='color:red;',>---</span>";
			if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname']))
				$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname'] = "<span style='color:red;',>---</span>";
			if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email']))
				$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email'] = "<span style='color:red;',>---</span>";
			if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['company']))
				$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['company'] = "<span style='color:red;',>---</span>";
			unset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
			return false;
		}
//		  if(dims_verifyemail($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])){
//			  return true;
//		  }else{
//			  $_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
//			  return false;
//		  }
	}else{
		$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
		if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname']))
			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['firstname'] = "<span style='color:red;',>---</span>";
		if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname']))
			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['lastname'] = "<span style='color:red;',>---</span>";
		if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email']))
			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['email'] = "<span style='color:red;',>---</span>";
		if(!isset($_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['company']))
			$_SESSION['dims']['IMPORT_IGNORED_CONTACT'][$current_line]['company'] = "<span style='color:red;',>---</span>";
		unset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
		return false;
	}
}

function verifEntExist($current_line){
	$db = dims::getInstance()->getDb();
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])){
		$sql = "SELECT id FROM dims_mod_business_tiers WHERE intitule = :intitule ";
		$res = $db->query($sql, array(
			':intitule' => $_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company']
		));
		if($db->numrows()>0){
			$data = $db->fetchrow($res);
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] = $data['id'];
		}else{
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] = 0;
		}
	}else{
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] = 0;
	}
}

function verifContactExist($current_line){
	$db = dims::getInstance()->getDb();
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])){
		$sql = "SELECT id FROM dims_mod_business_contact WHERE email = :email ";
		$res = $db->query($sql, array(
			':email' => $_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']
		));
		if($db->numrows()>0){
			$data = $db->fetchrow($res);
			$_SESSION['dims']['IMPORT_KNOWN_CONTACTS'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			if($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] != 0){
				$_SESSION['dims']['IMPORT_NEW_LINK'][$data['id']] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			}
			unset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
			$_SESSION['dims']['IMPORT_KNOWN_CONTACTS'][$current_line]['exist'] = $data['id'];
		}
	}else{
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist'] = 0;
	}
}

switch($op) {
	case 1:
		require_once(DIMS_APP_PATH . '/include/functions/mail.php');
		if(!empty($_FILES['srcfilect'])) {
			unset($_SESSION['dims']['import_current_similar'], $_SESSION['dims']['import_current_user_id'], $_SESSION['dims']['import_count_contact_similar']);
			unset($_SESSION['dims']['import_contact_similar_count'],$_SESSION['dims']['import_contact_similar']);
			unset($_SESSION['dims']['IMPORT_CONTACT']);
			unset($_SESSION['dims']['IMPORT_NEW_LINK']);
			unset($_SESSION['dims']['IMPORT_LINK_ENT']);
			unset($_SESSION['dims']['IMPORT_NEW_CT']);
			unset($_SESSION['dims']['IMPORT_IGNORED_CONTACT']);
			unset($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']);
			unset($_SESSION['dims']['RL']);
			unset($_SESSION['dims']['DB_CONTACT']);
			unset($_SESSION['dims']['IMPORT_COUNT_UPDATE']);
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

				if($dims->isAdmin() || $dims->isManager()) {
					$_SESSION['dims']['import_id_user'] = dims_load_securvalue("user_import", dims_const::_DIMS_NUM_INPUT, true, true, true);
				}else{
					$_SESSION['dims']['import_id_user'] = $_SESSION['dims']['userid'];
				}

				$ent_import=dims_load_securvalue("ent_import", dims_const::_DIMS_NUM_INPUT, true, true, true);
				if ($ent_import>0) $_SESSION['dims']['import_id_ent']=$ent_import;
				else $_SESSION['dims']['import_id_ent']=0;

				$created = array();
				$errors = array();
				$_FIELDS = array();
				$_SESSION['dims']['IMPORT_CONTACT']=array();
				$_SESSION['dims']['IMPORT_NEW_LINK']=array();
				$_SESSION['dims']['IMPORT_LINK_ENT']=array();
				$_SESSION['dims']['IMPORT_NEW_CT']=array();
				$_SESSION['dims']['IMPORT_KNOWN_CONTACTS']=array();
				$_SESSION['dims']['RL'] = 0;
				$_SESSION['dims']['IMPORT_COUNT_UPDATE'] = 0;

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
									$value="comment";
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
											if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
												$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
											}
											else {
												$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
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
										else if($_INTO_KEY) {
											if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
												$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
											}
											else {
												$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
											}
										}
									break;

									case chr(10):
										switch($_PREV_LETTER) {
											case '"':
												if($_PREV_PREV_LETTER == ',' && $_INTO_KEY) {
													if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
														$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
													}
													else {
														$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
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
													if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
														$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
													}
													else {
														$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
													}
												}
											break;

											default:
												if($_INTO_KEY) {
													if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
														$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
													}
													else {
														$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
													}
												}
											break;
										}


									break;

									default:
										if($_INTO_KEY && $letter != chr(13)) {
											if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])) {
												$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
											}
											else {
												$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
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

				/*if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE])) {
					if(count($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE])==0)
						unset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE]);
					else {
						verifContact($_CURRENT_LINE);
						verifEntExist($_CURRENT_LINE);
						verifContactExist($_CURRENT_LINE);
					}
				}*/

				//No break; !
			}
			else {
				$content_contact_import = '<p style="text-align:center;">'.$_DIMS['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'].'</p><br/>
				<div style="text-align:center;">
				'.dims_create_button_nofloat($_DIMS['cste']['_IMPORT_RETURN_TO_STEP1'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&op=1');").'
				</div>';
				break;
			}
		}
		else {
			$content_contact_import = '<div style="margin:10px;text-align:center;width:100%">
			<p>

			</p>
			<form action="#" method="post" enctype="multipart/form-data" id="import_step1">

			'.$_DIMS['cste']['_DIMS_LABEL_IMPORTSRC'].'&nbsp;*:&nbsp;<input type="file" name="srcfilect"/><br/><br/>';
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("srcfilect");
			$tokenHTML = $token->generate();
			$content_contact_import = $tokenHTML;
			$content_contact_import .= '<div style="text-align:center;width:100%;flat:left;">'.
			dims_create_button_nofloat($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('import_step1').submit();")
			.'</div>
			</form>
			</div>';
			break;
		}

	case 2:

		if($op == 1) {
			$_SESSION['dims']['IMPORT_MISSION']['CONTACT_RATTACH'] = array();
			//dims_print_r($_SESSION['dims']['IMPORT_CONTACT']);

			$tabKnowContact = array();
			foreach($_SESSION['dims']['IMPORT_CONTACT'] as $key => $data) {
				if(isset($data['email'])) {
					$sql = "SELECT id FROM dims_mod_business_contact WHERE email = :email ";
					$res = $db->query($sql, array(
						':email' => $data['email']
					));
					if($db->numrows()>0) {
						$result = $db->fetchrow($res);
						$_SESSION['dims']['IMPORT_MISSION']['CONTACT_RATTACH'][$result['id']] = $data;
						$_SESSION['dims']['IMPORT_MISSION']['CONTACT_RATTACH'][$result['id']]['id'] = $result['id'];
						$tabKnowContact[] = $data;
						unset($_SESSION['dims']['IMPORT_CONTACT'][$key]);
					}
				}
			}

			$content_contact_import = "<p style='text-align:center;'>".count($tabKnowContact)." ".$_SESSION['cste']['_IMPORT_MISSION_CONTACT_KNOW'].".<br/><br/>
				<a href='javascript:void(0);' onclick='javascript:dims_switchdisplay(\"list_ignored_contacts\");'><img src='./common/img/view.png' alt='view'/>&nbsp;".$_DIMS['cste']['_IMPORT_VIEW_LISTE']."</a>";

			$content_contact_import .= "<div id='list_ignored_contacts' style='display:none;'>";
			$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
						<tr class="trl1" style="font-size:12px;">
							 <td style="width: 20%;font-weight:bold;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
							<td style="width: 20%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
							<td style="width: 20%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
							<td style="width: 20%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
						</tr>';
			$i = 0;
			$class_col = 'trl1';
			foreach($tabKnowContact AS $tab_imp){
				if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
				$content_contact_import .= '<tr class="'.$class_col.'">
								<td>'.$tab_imp['firstname'].'</td>
								<td>'.$tab_imp['lastname'].'</td>
								<td>'.$tab_imp['email'].'</td>
								<td>'.$tab_imp['company'].'</td>
				</tr>';
				$i++;
			}

			$content_contact_import .= '</table>';
			$content_contact_import .= "</div>";
			$content_contact_import .= "</p>";
			$content_contact_import .= "<p style='text-align:center;'>";
			$content_contact_import .= '<div style="text-align:center;">
										'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&op=2');").'
										</div>';
			$content_contact_import .= "</p>";

			break;
		}
		elseif($op == 2) {
			$lev_nom = 0;
			$lev_pre = 0;
			$coef_nom = 0;
			$coef_pre = 0;
			$coef_tot = 0;

			$_SESSION['dims']['import_contact_similar'] = array();

			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $ct_id => $tab_contact_new) {
				if(isset($tab_contact_new['lastname']) && isset($tab_contact_new['firstname'])) {
					foreach($_SESSION['dims']['DB_CONTACT'] AS $tab_contact) {

						$lev_nom = levenshtein(strtoupper($tab_contact_new['lastname']), strtoupper($tab_contact['lastname']));
						$coef_nom = $lev_nom - (ceil(strlen($tab_contact_new['lastname'])/4));

						$lev_pre = levenshtein(strtoupper($tab_contact_new['firstname']), strtoupper($tab_contact['firstname']));
						$coef_pre = $lev_pre - (ceil(strlen($tab_contact_new['firstname'])/4));

						$coef_tot = $coef_nom + $coef_pre;
		//				  echo "levenshtein : ".$tab_contact_new['lastname']." ".$tab_contact_new['firstname']." et ".$tab_contact['lastname']." ".$tab_contact['firstname']." le total : ".$coef_tot."<br/>";
		//				  echo "les variables : lev du nom = ".$lev_nom." ; lev du prenom = ".$lev_pre." ; coef nom = ".$coef_nom." ; coef prenom = ".$coef_pre."<br/><br/>";
						if($coef_nom<=1 && $coef_tot < 2) {
							$_SESSION['dims']['import_contact_similar'][$ct_id][] = $tab_contact['id'];
						}
					}
				}
				else
					unset($_SESSION['dims']['IMPORT_CONTACT'][$ct_id]);
			}
			//dims_print_r($_SESSION['dims']['import_contact_similar']);
			//break;
			$_SESSION['dims']['import_count_contact_similar'] = count($_SESSION['dims']['import_contact_similar']);
			$_SESSION['dims']['import_contact_similar_count'] = 1;

			//no break;
		}

	case 3:
		//Il existe des contacts avec des similitudes dans le nom et le prenom
		if($_SESSION['dims']['import_count_contact_similar'] > 0){
			//On va chercher les variables qui pourrait etre envoy� a cette page
			$id_user_to_change = dims_load_securvalue("contact_id", dims_const::_DIMS_CHAR_INPUT, false, true, true);
			$id_user_similar = dims_load_securvalue("similar_contact", dims_const::_DIMS_CHAR_INPUT, false, true, true);
			//Si on envois un id d'utilisateur qu'on a traité
			if($id_user_to_change != ""){
				//echo "On a le user a changer.<br/>";
				//On cherche si cette utilisateur existe bien dans la table d'import
				if(isset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]) && count($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change])>0){
						//echo "On a trouvé le user a changer, il existe.<br/>";
						//Ce n'est pas un ajout l'utilisateur existe deja la personne la reconnue dans la liste
						if($id_user_similar != 0 && $id_user_similar != -1){
							$_SESSION['dims']['IMPORT_MISSION']['CONTACT_RATTACH'][$id_user_similar]['id'] = $id_user_similar;
							//echo "On a un utilisateur similaire.<br/>";
							//On regarde si l'email dans la base est rempli
							if($_SESSION['dims']['DB_CONTACT'][$id_user_similar]['email'] == "" && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email']){
								$_SESSION['dims']['IMPORT_NEW_EMAIL'][$id_user_similar] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email'];
							}
							//Ce contact a une entreprise qui porte exactement le meme nom dans le site
							if(isset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent']) && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] != 0){
								//echo "On a une entreprise similaire.<br/>";
								$sql = "SELECT count(id) AS exist_link FROM dims_mod_business_tiers_contact WHERE id_tiers = :idtiers AND id_contact = :idcontact ";
								$res = $db->query($sql, array(
									':idtiers'		=> $id_user_similar,
									':idcontact'	=> $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent']
								));
								$data = $db->fetchrow($res);
								if($data['exist_link'] == 0){
									//echo "On attache la personne a cette entreprise.<br/>";
									$_SESSION['dims']['IMPORT_NEW_LINK'][$id_user_similar] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change];
									//$_SESSION['dims']['stats_import']['ct_link'][$contact_import->fields['exist_ent']][] = $contact_import->fields;
								}
							}
							unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);
							unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
//							  $sql = "DELETE FROM dims_mod_business_contact_import WHERE id=".$id_user_to_change;
//							  $db->query($sql);

							//On cherche le prochain import a trait�
							//dims_print_r($_SESSION['dims']['import_contact_similar']);
							$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],($_SESSION['dims']['import_contact_similar_count']),1,true);
								if(count($tab_contact_new)>0){
									$user_id = array_keys($tab_contact_new);
									//dims_print_r($user_id);
									if(count($user_id) > 0){
										$user_id = $user_id[0];
									}
									//On met en session pour la suite
									$_SESSION['dims']['import_current_user_id'] = $user_id;
									$_SESSION['dims']['import_current_similar'] = $tab_contact_new;
								}


							$_SESSION['dims']['import_contact_similar_count']++;
						}elseif ($id_user_similar == 0){//C'est un ajout de contact
							//echo "On ne connait pas le contact.<br/>";

//							  $new_ct = new contact();
//							  $new_ct->init_description();
//
//							  $new_ct->fields['firstname'] = $contact_import->fields['firstname'];
//							  $new_ct->fields['lastname']	= $contact_import->fields['lastname'];
//							  $new_ct->fields['email'] = $contact_import->fields['email'];
//							  $new_ct->fields['id_user_create'] = $_SESSION['dims']['userid'];
//							  $new_ct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
//							  if(isset($contact_import->fields['cp']))		$new_ct->fields['postalcode']			= $contact_import->fields['cp'];
//							  if(isset($contact_import->fields['city']))		$new_ct->fields['city']					= $contact_import->fields['city'];
//							  if(isset($contact_import->fields['address']))	$new_ct->fields['address']		= $contact_import->fields['address'];
//							  if(isset($contact_import->fields['phone']))		$new_ct->fields['phone']		= $contact_import->fields['phone'];
//							  if(isset($contact_import->fields['fax']))		$new_ct->fields['fax']			= $contact_import->fields['fax'];
//							  if(isset($contact_import->fields['titre']))		$new_ct->fields['civilite']		= $contact_import->fields['titre'];
//							  if(isset($contact_import->fields['mobile']))	$new_ct->fields['mobile']		= $contact_import->fields['mobile'];
//							  if(isset($contact_import->fields['country']))	$new_ct->fields['country']		= $contact_import->fields['country'];
//							  $_SESSION['dims']['stats_import']['new_ct'][] = $contact_import->fields;
//							  $id_new_ct = $new_ct->save();
							$_SESSION['dims']['IMPORT_NEW_CT'][$id_user_to_change] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change];

							unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);
							unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);

							//dims_print_r($_SESSION['dims']['import_contact_similar']);
							$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
							$user_id = array_keys($tab_contact_new);
							if(count($user_id) > 0){
								$user_id = $user_id[0];
								//On met en session pour la suite
								$_SESSION['dims']['import_current_user_id'] = $user_id;
								$_SESSION['dims']['import_current_similar'] = $tab_contact_new;

							}

							//On met en session pour la suite
							$_SESSION['dims']['import_current_user_id'] = $user_id;
							$_SESSION['dims']['import_current_similar'] = $tab_contact_new;

							$_SESSION['dims']['import_contact_similar_count']++;
//							  $db->query("DELETE FROM dims_mod_business_contact_import WHERE id=".$id_user_to_change);
//							  $_SESSION['dims']['import_contact_similar_count']++;
						}else{
							unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
							//$db->query("DELETE FROM dims_mod_business_contact_import WHERE id=".$id_user_to_change);
							//dims_print_r($_SESSION['dims']['import_contact_similar']);
							$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
							$user_id = array_keys($tab_contact_new);
							if(count($user_id) > 0){
								$user_id = $user_id[0];
							}

							$_SESSION['dims']['import_current_user_id'] = $user_id;
							$_SESSION['dims']['import_current_similar'] = $tab_contact_new;

							$_SESSION['dims']['import_contact_similar_count']++;
						}
				}else{//L'utilisateur n'existe pas dans la table des imports
					//On reprend le contact pr�c�dent
					//echo "L'utilisateur n'existe pas. On reprend l'import a cette endroit<br/>";
					$user_id = $_SESSION['dims']['import_current_user_id'];
					//echo "L'utilisateur n'existe pas. On reprend l'import a cette endroit $user_id<br/>";
				}
			}else{ //Aucun id d'utilisateur n'a été envoyé, surement le premier appel de la page
				//Si la session qui défini le contact en cours de traitement
				//echo "Aucun id d'utilisateur n'a été envoyé.<br/>";
				if(!isset($_SESSION['dims']['import_current_user_id'])){
					//Dans ce cas on va chercher le premier import a traiter
					//dims_print_r($_SESSION['dims']['import_contact_similar']);
					$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
					$user_id = array_keys($tab_contact_new);
					if(count($user_id) > 0){
						$user_id = $user_id[0];
					}
					//On met en session pour la suite
					$_SESSION['dims']['import_current_user_id'] = $user_id;
					$_SESSION['dims']['import_current_similar'] = $tab_contact_new;
				}else{//Sinon on prend celui en cours
					$user_id = $_SESSION['dims']['import_current_user_id'];
				}
			}

			if (isset($user_id) && isset($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['lastname'])) {
			//Si on a le compte de contact a traiter, c'est qu'on a fini. On passe a l'etape 4
			if($_SESSION['dims']['import_count_contact_similar'] >= $_SESSION['dims']['import_contact_similar_count']){
				$content_contact_import = "<p style='font-weight:bold;text-align:center;font-size:14px;'>".$_DIMS['cste']['_DIMS_LABEL_CONTACT']." ".$_SESSION['dims']['import_contact_similar_count']."/".$_SESSION['dims']['import_count_contact_similar']."</p><br/>";

				$content_contact_import .= '<form action="./admin.php?action=import_insc&id_evt='.$id_evt.'&op=3" method="post" id="valider_similitude" name="valider_similitude">';
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("contact_id", $userid);
				$content_contact_import .= '<input type="hidden" name="contact_id" value="'.$user_id.'"/>
											<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
												<tr class="trl1" style="font-size:12px;">
														<td style="width:10%;">&nbsp;</td>
														<td style="width:5%;">&nbsp;</td>
														<td style="width:20%;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
														<td style="width:20%;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
														<td style="width:20%;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
														<td style="width:25%;">&nbsp;</td>
												</tr>';
			}
			if (isset($_SESSION['dims']['import_current_similar'][$user_id])) {
				$content_contact_import .= '	<tr style="background:#C2C2C2;border-bottom:1px solid #738CAD;">
														<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_DIMS['cste']['_IMPORT_TAB_NEW_CONTACT'].'</td>
														<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">&nbsp;</td>
														<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['lastname']).'</td>
														<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_SESSION['dims']['IMPORT_CONTACT'][$user_id]['firstname'].'</td>
														<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_SESSION['dims']['IMPORT_CONTACT'][$user_id]['email'].'</td>
														<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">&nbsp;</td>
												</tr>';
				$lastnamecompare=strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['lastname']);
				$firstnamecompare=strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['firstname']);
				$okname=false;
				$okfirstname=false;

				$rowspan = count($_SESSION['dims']['import_current_similar'][$user_id]);
				foreach($_SESSION['dims']['import_current_similar'][$user_id] AS $similar_contact){
					//$date_c = dims_timestamp2local($tab_imp['ent_datecreation']);
					if ($lastnamecompare==strtoupper($_SESSION['dims']['DB_CONTACT'][$similar_contact]['lastname'])) {
						$okname=true;
						$samname="<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
					}
					else $samname="<img src=\"./common/modules/system/img/ico_point_red.gif\" alt=\"\">";

					if ($firstnamecompare==strtoupper($_SESSION['dims']['DB_CONTACT'][$similar_contact]['firstname'])) {
						$okfirstname=true;
						$samfirstname="<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
					}
					else $samfirstname="<img src=\"./common/modules/system/img/ico_point_red.gif\" alt=\"\">";

					$content_contact_import .= '<tr style="background-color:#C2D6EB;">';
					if($rowspan == 1) $content_contact_import .= '<td>'.$_DIMS['cste']['_IMPORT_TAB_SIMILAR_CONTACT_SINGLE'].'</td>';
					if($rowspan > 1){ $content_contact_import .= '<td rowspan="'.$rowspan.'">'.$_DIMS['cste']['_IMPORT_TAB_SIMILAR_CONTACT'].'</td>';$rowspan = 0;}

					if ($okfirstname && $okname) $linkoption='onclick="javascript:document.valider_similitude.submit();" ';
					else $linkoption="";

					$content_contact_import .= '	<td><input type="radio" '.$linkoption.'  name="similar_contact" value="'.$_SESSION['dims']['DB_CONTACT'][$similar_contact]['id'].'"/></td>
													<td>'.$samname."&nbsp;".$_SESSION['dims']['DB_CONTACT'][$similar_contact]['lastname'].'</td>
													<td>'.$samfirstname."&nbsp;".$_SESSION['dims']['DB_CONTACT'][$similar_contact]['firstname'].'</td>
													<td>'.$_SESSION['dims']['DB_CONTACT'][$similar_contact]['email'].'</td>
													<td>'.$_DIMS['cste']['_DIMS_IMPORT_CT_SAME'].'</td>
											</tr>';
					$token->field("similar_contact");
				}
			}
				$content_contact_import .= '<tr>
													<td style="border-top:1px solid #738CAD;">&nbsp;</td>
													<td style="border-top:1px solid #738CAD;"><input type="radio" name="similar_contact" value="0" onclick="javascript:document.valider_similitude.submit();"/></td>
													<td colspan="4" style="border-top:1px solid #738CAD;">'.$_DIMS['cste']['_IMPORT_NEW_SIMILAR_CONTACT'].'</td>
											</tr>
											<tr>
													<td>&nbsp;</td>';
				$token->field("similar_contact");

				if ($okfirstname && $okname) {
					$content_contact_import .='<td><input type="radio" name="similar_contact" value="-1"/></td>';
				} else {
					$content_contact_import .='<td><input type="radio" name="similar_contact" value="-1" checked="checked"/></td>';
				}
				$content_contact_import .='<td colspan="4">'.$_DIMS['cste']['_IMPORT_NEXT_SIMILAR_CONTACT'].'</td>
											</tr>';
				$content_contact_import .= '</table><br/><br/>';
				$content_contact_import .= '<div style="text-align:center;">
												'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'], "./common/img/publish.png", "dims_getelem('valider_similitude').submit();").'
											</div>';
				$tokenHTML = $token->generate();
				$content_contact_import .= $tokenHTML;
				$content_contact_import .= '</form>';
				$content_contact_import .= '<div style="text-align:right;">
												'.dims_create_button_nofloat($_DIMS['cste']['_IMPORT_GO_NEXT_STEP'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&op=4');").'
											</div>';
				$content_contact_import .= '<div style="float:right;border:1px solid #738CAD;width:150px;padding:5px;margin-top:5px;"><img src="./common/img/warning.png" alt="/!\">&nbsp;'.$_DIMS['cste']['_IMPORT_WARN_STEP3'].'</div>';

				break;
			}

		}else{
			$content_contact_import = '<div style="text-align:center;margin:10px;">'.$_DIMS['cste']['_IMPORT_NO_SIMILAR_CT'].'</div>';
			$content_contact_import .= '<div style="text-align:center;">
												'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&op=4');").'
											</div>';
			break;
		}

	case 4:
		//dims_print_r($_SESSION['dims']['IMPORT_CONTACT']);
		$op = 4;

		if(isset($_SESSION['dims']['import_contact_similar'])){
			foreach($_SESSION['dims']['import_contact_similar'] as $key => $value){
				unset($_SESSION['dims']['IMPORT_CONTACT'][$key]);
			}
		}

		unset($_SESSION['dims']['import_current_similar'], $_SESSION['dims']['import_count_contact_similar']);
		unset($_SESSION['dims']['import_contact'],$_SESSION['dims']['import_contact_similar_count'],$_SESSION['dims']['import_contact_similar']);

		if(count($_SESSION['dims']['IMPORT_CONTACT'])>0){
			$content_contact_import = "<p>".$_DIMS['cste']['_IMPORT_INSTRUCTION_STEP4']."<p>";
			$content_contact_import .= "<p>".$_DIMS['cste']['_IMPORT_TAB_LAST_CONTACTS']." :</p><br/>";
			$content_contact_import .= "<p>".count($_SESSION['dims']['IMPORT_CONTACT'])." ".$_DIMS['cste']['_IMPORT_CONTACTS_RESTANT']."</p>";
			$content_contact_import .= '<br/><div style="text-align:center;width:100%">
												'.dims_create_button_nofloat($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('contacts_restants').submit();").'
											</div>';
			$content_contact_import .= '<form action="./admin.php?action=import_insc&id_evt='.$id_evt.'&op=5" method="post" id="contacts_restants">
										<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
											<tr class="trl1" style="font-size:12px;">
													<td style="width: 10%;">&nbsp;</td>
													<td style="width: 30%;">'.$_DIMS['cste']['_DIMS_LABEL_PERSONNE'].'</td>
													<td style="width: 30%;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
											</tr>';

			$class_col = 'trl1';
			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
				if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
				//$date_c = dims_timestamp2local($tab_imp['ent_datecreation']);
				$content_contact_import .= '<tr class="'.$class_col.'">
												<td><input type="checkbox" name="contact_import_'.$data['id'].'" value="'.$data['id'].'" checked="checked"/></td>
												<td>'.$data['firstname'].' '.$data['lastname'].'</td>
												<td>';
				$token->field("contact_import_".$data['id']);
				$content_contact_import .= (isset($data['email'])) ? $data['email'] : 'n/a';
				$content_contact_import .= '</td>
											</tr>';
			}

			$content_contact_import .= '</table>';

			$content_contact_import .= '<br/><div style="text-align:center;">
												'.dims_create_button_nofloat($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('contacts_restants').submit();").'
											</div>';

			$tokenHTML = $token->generate();
			$content_contact_import .= $tokenHTML;
			$content_contact_import .= '</form>';
		}else{
			$content_contact_import = '<p>'.$_DIMS['cste']['_IMPORT_ALL_CONTACTS_ALREADY_EXISTS'].'</p><br/>
										<div style="text-align:center;">
											'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&op=5');").'
										</div>';
		}
	break;

	case 5:
		//dims_print_r($_SESSION['dims']['IMPORT_NEW_LINK']);
		// il manque le rattachement de ces contacts avec la personne en $_SESSION['dims']['import_id_user']
		$user=new user();

		$user->open($_SESSION['dims']['import_id_user']);
		// tres tres important
		if (isset($user->fields['id_contact'])) $import_id_ct=$user->fields['id_contact'];
		else $import_id_ct=0;

		$content_contact_import = '';

		$mod=$dims->getModule($_SESSION['dims']['moduleid']);
		$id_module_type=$mod['id_module_type'];

		//On met a jour les emails
		if (isset($_SESSION['dims']['IMPORT_NEW_EMAIL'])) {
			foreach($_SESSION['dims']['IMPORT_NEW_EMAIL'] AS $id_contact => $email){
				$contact = new contact;
				$contact->open($id_contact);
				$contact->fields['email'] = $email;
				$contact->save();

				// on crée le lien intelligence avec la personne
				if ($import_id_ct>0) {
					$ctlink = new ctlink();
					$ctlink->fields['id_contact1']=$import_id_ct;
					$ctlink->fields['id_contact2']=$id_contact;
					$ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
					$ctlink->fields['type_link']="business";
					$ctlink->fields['link_level']=1;
					$ctlink->fields['time_create']=date("YmdHis");
					$ctlink->fields['id_ct_user_create']=$import_id_ct;
					$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
					$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
					$ctlink->save();
				}

				// si on a choisit de lier cette personne a une entreprise on crée aussi le lien entreprise
				if ($_SESSION['dims']['import_id_ent']>0) {
					$new_link = new tiersct();
					$new_link->init_description();
					$new_link->fields['id_tiers'] = $_SESSION['dims']['import_id_ent'];
					$new_link->fields['id_contact'] = $id_contact;
					if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
					$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['import_id_user'];
					$new_link->fields['link_level'] = 1;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$new_link->save();
				}

				if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
					foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
						$tagi = new tag_index();
						$tagi->fields['id_tag']=$idtag;
						$tagi->fields['id_record']=$id_contact;
						$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
						$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$tagi->fields['id_module_type']=$id_module_type;
						$tagi->save();
					}
				}
			}
		}

		//Creation des liens
		if (isset($_SESSION['dims']['IMPORT_NEW_LINK'])) {
			foreach($_SESSION['dims']['IMPORT_NEW_LINK'] AS $key => $data){
				$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers= :idtiers AND id_contact= :idcontact ";
				$res = $db->query($sql, array(
					':idtiers'		=> $data['exist_ent'],
					':idcontact'	=> $key
				));
				if($db->numrows($res)==0){
					$new_link = new tiersct();
					$new_link->init_description();

					$new_link->fields['id_tiers'] = $data['exist_ent'];
					$new_link->fields['id_contact'] = $key;
					if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
					$new_link->fields['id_ct_user_create'] = $import_id_ct;
					$new_link->fields['link_level'] = 1;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

					$new_link->save();

					$_SESSION['dims']['IMPORT_LINK_ENT'][$data['exist_ent']][] = $data;
				}
			}
		}
		//dims_print_r($_SESSION['dims']['IMPORT_CONTACT']);
		//Creation des contacts
		if (isset($_SESSION['dims']['IMPORT_CONTACT'])) {
			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
				$id_import = dims_load_securvalue("contact_import_".$data['id'], dims_const::_DIMS_CHAR_INPUT, false, true, true);
				if($id_import){
					$contact = new contact();
					$contact->init_description();

					$contact->fields['firstname']	= $data['firstname'];
					$contact->fields['lastname']	= $data['lastname'];
					$contact->fields['email']		= (isset($data['email'])) ? $data['email'] : '';
					$contact->fields['id_user_create'] = $import_id_ct;
					if(!empty($data['cp'])) $contact->fields['postalcode'] = $data['cp'];
					if(!empty($data['city'])) $contact->fields['address'] = $data['city'];
					if(!empty($data['address'])) $contact->fields['city'] = $data['address'];
					if(!empty($data['phone'])) $contact->fields['phone'] = $data['phone'];
					if(!empty($data['phone2'])) $contact->fields['phone2'] = $data['phone2'];
					if(!empty($data['fax'])) $contact->fields['fax'] = $data['fax'];
					if(!empty($data['civilite'])) $contact->fields['civilite'] = $data['civilite'];
					if(!empty($data['mobile'])) $contact->fields['mobile'] = $data['mobile'];
					if(!empty($data['country'])) $contact->fields['country'] = $data['country'];

					//dims_print_r($contact);
					$contact->save();
					$id_new_contact = $db->insertid();
					$_SESSION['dims']['IMPORT_MISSION']['CONTACT_RATTACH'][$id_new_contact]['id'] = $id_new_contact;

					if(!empty($data['comment'])) {
						// on a un commentaire
						$cmt = new commentaire();
						$cmt->fields['id_contact']=$id_new_contact;
						$cmt->fields['id_user_ct']=$import_id_ct;
						$cmt->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$cmt->fields['commentaire']=$data['comment'];
						$cmt->fields['date_create']=date("YmdHis");
						$cmt->fields['com_level']=1; // generique, voir si pas personnel
						$cmt->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$cmt->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$cmt->fields['id_module']= $_SESSION['dims']['moduleid'];
						$cmt->save();
					}

					//$content_contact_import .= "1 import de contact terminé.<br/>";
					// on crée le lien intelligence avec la personne
					if ($import_id_ct>0) {
						$ctlink = new ctlink();
						$ctlink->fields['id_contact1']=$import_id_ct;
						$ctlink->fields['id_contact2']=$id_new_contact;
						$ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$ctlink->fields['type_link']="business";
						$ctlink->fields['link_level']=1;
						$ctlink->fields['time_create']=date("YmdHis");
						$ctlink->fields['id_ct_user_create']=$import_id_ct;
						$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$ctlink->save();
					}
					//Si on a trouvé une entreprise qui porte exactement le meme nom que la sienne,
					//on l'associe a cette entreprise.

					if(isset($data['exist_ent']) && $data['exist_ent'] != 0){
						$new_link = new tiersct();
						$new_link->init_description();

						$new_link->fields['id_tiers'] = $data['exist_ent'];
						$new_link->fields['id_contact'] = $id_new_contact;
						if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
						$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['import_id_user'];
						$new_link->fields['link_level'] = 1;
						$new_link->fields['date_create'] = date("YmdHis");
						$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

						$new_link->save();

						$_SESSION['dims']['IMPORT_LINK_ENT'][$data['exist_ent']][] = $data;
						//$content_contact_import .= "1 association avec une entreprise ajout&eacute;e.<br/>";
					}

					// si on a choisit de lier cette personne a une entreprise on crée aussi le lien entreprise
					if ($_SESSION['dims']['import_id_ent']>0) {
						$new_link = new tiersct();
						$new_link->init_description();
						$new_link->fields['id_tiers'] = $_SESSION['dims']['import_id_ent'];
						$new_link->fields['id_contact'] = $id_new_contact;
						if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
						$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['import_id_user'];
						$new_link->fields['link_level'] = 1;
						$new_link->fields['date_create'] = date("YmdHis");
						$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$new_link->save();
					}
					//$content_contact_import .= "<br/>";
					// attachement des tags
					if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
						foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
							$tagi = new tag_index();
							$tagi->fields['id_tag']=$idtag;
							$tagi->fields['id_record']=$id_new_contact;
							$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
							$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
							$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
							$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
							$tagi->fields['id_module_type']=$id_module_type;
							$tagi->save();
						}
					}

				}else{
					unset($_SESSION['dims']['IMPORT_CONTACT'][$data['id']]);
				}
			}
		}
		//dims_print_r($_SESSION['dims']['IMPORT_NEW_CT']);
		//Creation des contacts avec similitude
		if (isset($_SESSION['dims']['IMPORT_NEW_CT'])) {
			foreach($_SESSION['dims']['IMPORT_NEW_CT'] AS $key => $data){
				$contact = new contact();
				$contact->init_description();

				$contact->fields['firstname'] = $data['firstname'];
				$contact->fields['lastname'] = $data['lastname'];
				$contact->fields['email'] = $data['email'];
				$contact->fields['id_user_create'] = $import_id_ct;
				if(!empty($data['cp'])) $contact->fields['postalcode'] = $data['cp'];
				if(!empty($data['city'])) $contact->fields['address'] = $data['city'];
				if(!empty($data['address'])) $contact->fields['city'] = $data['address'];
				if(!empty($data['phone'])) $contact->fields['phone'] = $data['phone'];
				if(!empty($data['phone2'])) $contact->fields['phone2'] = $data['phone2'];
				if(!empty($data['fax'])) $contact->fields['fax'] = $data['fax'];
				if(!empty($data['civilite'])) $contact->fields['civilite'] = $data['civilite'];
				if(!empty($data['mobile'])) $contact->fields['mobile'] = $data['mobile'];
				if(!empty($data['country'])) $contact->fields['country'] = $data['country'];

				$contact->save();
				$id_new_contact = $db->insertid();

				$_SESSION['dims']['IMPORT_MISSION']['CONTACT_RATTACH'][$id_new_contact]['id'] = $id_new_contact;

				// gestion des commentaires
				if(!empty($data['comment'])) {
					// on a un commentaire
					$cmt = new commentaire();
					$cmt->fields['id_contact']=$id_new_contact;
					$cmt->fields['id_user_ct']=$import_id_ct;
					$cmt->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
					$cmt->fields['commentaire']=$data['comment'];
					$cmt->fields['date_create']=date("YmdHis");
					$cmt->fields['com_level']=1; // generique, voir si pas personnel
					$cmt->fields['id_user']=$_SESSION['dims']['import_id_user'];
					$cmt->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
					$cmt->fields['id_module']= $_SESSION['dims']['moduleid'];
					$cmt->save();
				}

				// on crée le lien intelligence avec la personne
				if ($import_id_ct>0) {
					$ctlink = new ctlink();
					$ctlink->fields['id_contact1']=$import_id_ct;
					$ctlink->fields['id_contact2']=$id_new_contact;
					$ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
					$ctlink->fields['type_link']="business";
					$ctlink->fields['link_level']=1;
					$ctlink->fields['time_create']=date("YmdHis");
					$ctlink->fields['id_ct_user_create']=$import_id_ct;
					$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
					$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
					$ctlink->save();
				}
				//$content_contact_import .= "1 import de contact terminé.<br/>";
				$_SESSION['dims']['IMPORT_CONTACT'][$key] = $data;


				//Si on a trouvé une entreprise qui porte exactement le meme nom que la sienne,
				//on l'associe a cette entreprise.

				if($data['exist_ent'] != 0){
					$new_link = new tiersct();
					$new_link->init_description();

					$new_link->fields['id_tiers'] = $data['exist_ent'];
					$new_link->fields['id_contact'] = $id_new_contact;
					if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
					$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['import_id_user'];
					$new_link->fields['link_level'] = 1;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

					$new_link->save();

					$_SESSION['dims']['IMPORT_LINK_ENT'][$data['exist_ent']][] = $data;
				}

				// creation lien entreprise
				if ($_SESSION['dims']['import_id_ent']>0) {
					$new_link = new tiersct();
					$new_link->init_description();
					$new_link->fields['id_tiers'] = $_SESSION['dims']['import_id_ent'];
					$new_link->fields['id_contact'] = $id_new_contact;
					if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
					$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['import_id_user'];
					$new_link->fields['link_level'] = 1;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$new_link->save();
				}

				if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
					foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
						$tagi = new tag_index();
						$tagi->fields['id_tag']=$idtag;
						$tagi->fields['id_record']=$id_new_contact;
						$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
						$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$tagi->fields['id_module_type']=$id_module_type;
						$tagi->save();
					}
				}
			}
		}
		unset($_SESSION['dims']['IMPORT_NEW_LINK']);
		unset($_SESSION['dims']['IMPORT_NEW_CT']);

		$content_contact_import .= "<p style='text-align:center;font-size:16px;font-weight:bold;'>".$_DIMS['cste']['_IMPORT_COMPLETE']."</p><br/>";
		$content_contact_import .= "<p>".$_DIMS['cste']['_IMPORT_MISSION_RATTACHED_CONTACTS']."</p><br/>";

		$content_contact_import .= '<div style="text-align:center;width:100%;">
											'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?action=adm_evt&id_evt=".$id_evt."');").'
										</div>';

		$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">';
		$content_contact_import .= '	<tr style="background:#CECECE;">
											<td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
											<td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
											<td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
										</tr>';
		$color2 = "#738CAD";$color1 = "#F1F1F1";$color = '';

		foreach($_SESSION['dims']['IMPORT_MISSION']['CONTACT_RATTACH'] as $key => $value) {

				$ct = new contact();
				$ct->open($key);
				if($color == $color1) $color = $color2 ; else $color = $color1;
				$content_contact_import .= '<tr style="background:'.$color.';">
												<td style="border-bottom:1px solid #738CAD;">'.$ct->fields['lastname'].'</td>
												<td style="border-bottom:1px solid #738CAD;">'.$ct->fields['firstname'].'</td>
												<td style="border-bottom:1px solid #738CAD;">';
				$content_contact_import .= (isset($ct->fields['email'])) ? $ct->fields['email'] : 'n/a';
				$content_contact_import .= '</td>
											</tr>';

				$inscrip = new inscription();

				$inscrip->fields['id_action']	= $_SESSION['dims']['IMPORT_MISSION']['id_evt'];
				$inscrip->fields['id_contact']	= $ct->fields['id'];
				$inscrip->fields['validate']	= 2;
				$inscrip->fields['lastname']	= $ct->fields['lastname'];
				$inscrip->fields['firstname']	= $ct->fields['firstname'];
				$inscrip->fields['email']		= $ct->fields['email'];

				$inscrip->save();
		}

		$content_contact_import .= '</table>';


		$content_contact_import .= "<p>".$_DIMS['cste']['_IMPORT_LINKED_CONTACTS']."</p><br/>";
		$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">';
		$content_contact_import .= '	<tr style="background:#CECECE;">
											<td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
											<td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
											<td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
										</tr>';

		$color2 = "#738CAD";$color1 = "#F1F1F1";$color = '';
		foreach($_SESSION['dims']['IMPORT_LINK_ENT'] AS $new_ct_link){
			$rowspan = count($new_ct_link);
			if($color == $color1) $color = $color2 ; else $color = $color1;
			foreach($new_ct_link AS $ct_link){
				$content_contact_import .= '<tr style="background:'.$color.'">';
				if($rowspan > 1){ $content_contact_import .= '	  <td rowspan="'.$rowspan.'">'.$ct_link['company'].'</td>'; $rowspan = 0;}
				if($rowspan == 1){ $content_contact_import .= '    <td>'.$ct_link['company'].'</td>';$rowspan = 0;}
				$content_contact_import .= '	<td>'.$ct_link['lastname'].'</td>';
				$content_contact_import .= '	<td>'.$ct_link['firstname'].'</td>';
				$content_contact_import .= '</tr>';
			}
		}

		$content_contact_import .= '</table>';
		$content_contact_import .= '<div style="text-align:center;">
											'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?action=adm_evt&id_evt=".$id_evt."');").'
										</div>';

		unset($_SESSION['dims']['import_current_similar'], $_SESSION['dims']['import_current_user_id'], $_SESSION['dims']['import_count_contact_similar']);
		unset($_SESSION['dims']['import_contact_similar_count'],$_SESSION['dims']['import_contact_similar']);
		unset($_SESSION['dims']['IMPORT_CONTACT']);
		unset($_SESSION['dims']['IMPORT_NEW_LINK']);
		unset($_SESSION['dims']['IMPORT_LINK_ENT']);
		unset($_SESSION['dims']['IMPORT_NEW_CT']);
		unset($_SESSION['dims']['IMPORT_IGNORED_CONTACT']);
		unset($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']);
		unset($_SESSION['dims']['RL']);
		unset($_SESSION['dims']['DB_CONTACT']);
		unset($_SESSION['dims']['import_id_user']);
		unset($_SESSION['dims']['IMPORT_MISSION']['CONTACT_RATTACH']);
	break;
}
?>
