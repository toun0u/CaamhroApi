<?php
require_once(DIMS_APP_PATH . "/modules/system/class_ct_link.php");
require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
require_once(DIMS_APP_PATH . '/modules/system/class_commentaire.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers_layer.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers_import.php');
require_once(DIMS_APP_PATH . '/modules/system/class_user.php');

function verifEnt($current_line){
	//dims_print_r($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
	$db = dims::getInstance()->getDb();
	$_SESSION['dims']['RL']++;
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']))
		str_replace("'","",$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email']);
	if(!isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])){
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
	else
		return true;
}

function verifEntExist($current_line){
	$db = dims::getInstance()->getDb();
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])){
		$sql = "SELECT	id
				FROM	dims_mod_business_tiers
				WHERE	intitule = :intitule ";
		$res = $db->query($sql, array(
			':intitule' => addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])
		));

		$sql = "SELECT DISTINCT	t.id
				FROM			dims_mod_business_tiers t
				LEFT JOIN		dims_mod_business_tiers_layer l
				ON				l.id = t.id
				AND				l.id_layer = :idlayer
				AND				l.intitule LIKE :intitule
				WHERE			t.intitule LIKE :intitule ";
		$res = $db->query($sql, array(
			':idlayer' 	=> $_SESSION['dims']['workspaceid'],
			':intitule' => addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])
		));


		if($db->numrows()>0){
			$data = $db->fetchrow($res);
			$_SESSION['dims']['IMPORT_KNOWN_CONTACTS'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist'] = $data['id'];
			if($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ct'] != 0){
				$_SESSION['dims']['IMPORT_NEW_LINK'][$data['id']] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			}
			$_SESSION['dims']['IMPORT_KNOWN_CONTACTS'][$current_line]['exist'] = $data['id'];
			unset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
		}else{
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist'] = 0;
		}
	}else{
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist'] = 0;
	}
}

function verifContactExist($current_line){
	$db = dims::getInstance()->getDb();
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email_pers'])){
		$sql = "SELECT id FROM dims_mod_business_contact_layer
				WHERE email = :email
				AND  id_layer = :idlayer ";
		$res = $db->query($sql, array(
			':email'	=> addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email_pers']),
			':idlayer'	=> $_SESSION['dims']['workspaceid']
		));
		if($db->numrows()>0){
			$data = $db->fetchrow($res);
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ct'] = $data['id'];
		}
	}else{
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ct'] = 0;
	}
}

//on construit la liste des champs generiques afin d'enregistrer les infos ent directement dans la table tiers ou dans un layer
$sql =	"
			SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
						mb.protected,mb.name as namefield,mb.label as titlefield
			FROM		dims_mod_business_meta_field as mf
			INNER JOIN	dims_mb_field as mb
			ON			mb.id=mf.id_mbfield
			RIGHT JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			WHERE		mf.id_object = :idobject
			AND			mc.admin=1
			AND			mf.used=1
			ORDER BY	mc.position, mf.position
			";
$rs_fields=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
));

$rubgen=array();
$convmeta = array();

while ($fields = $db->fetchrow($rs_fields)) {
	if (!isset($rubgen[$fields['id_cat']]))  {
		$rubgen[$fields['id_cat']]=array();
		$rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
		$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
		if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
	}

	// on ajoute maintenant les champs dans la liste
	$fields['use']=0;// par defaut non utilise
	$fields['enabled']=array();
	if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

	$_SESSION['dims']['tiers_fields_mode'][$fields['id']]=$fields['mode'];

	// enregistrement de la conversion
	$convmeta[$fields['namefield']]=$fields['id'];
}

//dims_print_r($convmeta);
//dims_print_r($_SESSION['dims']['tiers_fields_mode']);

switch($op){
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

			$extension	= explode(".", $_FILES['srcfilect']['name']);
			$extension	= $extension[count($extension)-1];
			$extension	= strtolower($extension);

			if($_FILES['srcfilect']['name'] != ''){

				$sql = "SELECT id,intitule FROM dims_mod_business_tiers WHERE 1";
					$res = $db->query($sql);
					if($db->numrows()>0){
						while($data = $db->fetchrow($res))
							$_SESSION['dims']['DB_CONTACT'][$data['id']] = $data;
					}

					if($dims->isAdmin() || $dims->isManager()) {
						$_SESSION['dims']['import_id_user'] = dims_load_securvalue("user_import", dims_const::_DIMS_NUM_INPUT, true, true, true);
						if(empty($_SESSION['dims']['import_id_user']))
							$_SESSION['dims']['import_id_user'] = $_SESSION['dims']['userid'];

						$workspace = new workspace();
						$workspace->open($_SESSION['dims']['workspaceid']);
						$users = $workspace->getusers();
						$_SESSION['dims']['more_rattach_user'] = array();
						$user = new user();
						if(count($users)>0) {
							//dims_print_r($users);
							foreach($users as $userid => $pers){
								$user_rattach = dims_load_securvalue($userid, dims_const::_DIMS_NUM_INPUT, true, true, true);

								if($user_rattach == 'on') {
									// update rattachement sur le contact et non sur le user_id
									$user->open($userid);
									// tres tres important
									if (isset($user->fields['id_contact'])) $_SESSION['dims']['more_rattach_user'][] = $user->fields['id_contact'];
								}
							}
						}

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
					$_SESSION['dims']['IMPORT_IGNORED_CONTACT']=array();
					$_SESSION['dims']['RL'] = 0;
					$_SESSION['dims']['IMPORT_COUNT_UPDATE'] = 0;

					//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
					ini_set('max_execution_time',-1);
					ini_set('memory_limit','512M');

				switch ($extension) {
					case "xls":
					case "xlsx":

						$file	= $_FILES['srcfilect']['tmp_name'];

						$liste_version["csv"]	= "CSV";
						$liste_version["xlsx"]	= "Excel2007";
						$liste_version["xls"]	= "Excel5";

						/** PHPExcel_IOFactory */
						require_once(DIMS_APP_PATH . '/include/PHPExcel/IOFactory.php');

						//echo date('H:i:s') . " Load from Excel2007 file\n<br>";

						//on instancie un objet de lecture
						$objReader = PHPExcel_IOFactory::createReader($liste_version[$extension]);
						//on charge le fichier qu'on veut lire
						$objPHPExcel = PHPExcel_IOFactory::load($file);

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
						if(strlen($Column_max) > 2)
							$Column_max = 'AZ';

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

						//dims_print_r($content);
						foreach($content AS $key => $value){
							//On verifie si on connait la cle
							$val = dims_convertaccents($value);
							switch($val){
								case "firstname":
								case "prenom":
								case "prenom":
								case "first name":
									$value = "firstname";
								break;

								case "lastname":
								case "nom":
								case "last name":
									$value = "lastname";
								break;

								case "middlename":
								case "midle name":
								case "deuxièmeprenom":
									$value = "middlename";
								break;

								case "email":
								case "email address":
								case "e-mail address":
								case "courriel":
								case "emailaddress":
								case "mail":
								case "adressedemessagerie":
								case "cc_email":
								case "e_mail":
									$value = "email";
								break;

								case "company":
								case "societe":
								case "societe" :
								case "company name":
								case "companyname":
								case "entreprise":
								case "cc_name":
								case "raison sociale":
									$value = "company";
								break;

								case "businesspostalcode":
								case "codepostalbureau":
								case "business postal code":
								case "cc_postal_code":
								case "code postal":
								case "cp":
									$value = "cp";
								break;

								case "city":
								case "localite":
								case "ville":
								case "businesscity":
								case "business city":
								case "villebureau":
								case "ville":
								case "cc_city":
									$value = "ville";
								break;

								case "ruebureau":
								case "businessstreet":
								case "business street":
								case "cc_address":
								case "adresse":
								case "adresse_autre":
									$value = "address";
								break;

								case "ruebureau2":
								case "businessstreet2":
								case "business street 2":
								//case "adresse_situation_2":
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
								case "depregionbureau":
								case "pays":
									$value = "country";
								break;

								case "civilite":
								case "title":
								case "titre":
									$value = "civilite";
								break;

								case "job title":
								case "profession":
									$value = "professional";
								break;

								case "mobile phone":
								case "mobilephone":
								case "telmobile":
								case "carphone":
									$value = "mobile";
								break;

								case "telephonebureau":
								case "telephonebureau":
								case "businessphone":
								case "business phone":
								case "telephone":
								case 'cc_phone':
								case "tel":
									$value = "phone";
								break;

								case "telephonebureau2":
								case "telephonebureau2":
								case "businessphone2":
								case "business phone2":
								case "tel2":
									$value = "phone2";
								break;

								case "telecopiebureau":
								case "telecopiebureau":
								case "businessfax":
								case "business fax":
								case "cc_fax":
								case "fax":
									$value = "fax";
								break;

								case "site_web":
								case "cc_url":
									$value = "website";
								break;

								case "activite_fr":
								case "company description":
									$value = "companydescription";
									break;

								case 'notes': //Traitement des commentaires
									$value="comment";
								break;
							}
							$_FIELDS[$key] = $value;
						}
						//dims_print_r($_FIELDS);

						//Boucle sur le nombre de ligne
						for ($i=2; $i <= $nb_row; $i++){
							$c=0;
							$d=0;
							$fist_lettre = "";
							$lettre = "";
							while ($fist_lettre.$lettre != $Column_max){
								$c++;
								$lettre = $alphabet[$c];
								$_SESSION['dims']['IMPORT_CONTACT'][$i][$_FIELDS[$fist_lettre.$lettre]] = utf8_decode($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$i)->getValue());
								//Si l'on arrive sur la derniere colone l'on arret
								if ($alphabet[$c] == "Z") {
									$d++;
									$c = 0;
									$fist_lettre = $alphabet[$d];
								}
							}
						}

						break;
					default:
						$handle = fopen($_FILES['srcfilect']['tmp_name'], "r");
						$_CURRENT_KEY = 0;
						$_CURRENT_LINE = 1;
						$_PREV_LETTER = "";
						$_PREV_PREV_LETTER = "";
						$_PREV_PREV_PREV_LETTER = "";
						$_INTO_KEY = false;
						while ($line = fgets($handle)){
								// Ligne de description de la structure du fichier
								if(count($_FIELDS) == 0){
									//on test le type de separateur

									$is_coma = strpos($line, ',');
									$is_pipe = strpos($line, '|');

									if($is_coma !== false) $separateur = ",";
									elseif($is_pipe !== false) $separateur = "|";

									$content = explode($separateur,$line);
						//dims_print_r($content); die();
									foreach($content AS $key => $value){
										$value = strtolower(trim(str_replace('"','',$value)));
										$value = preg_replace('#^cc_#','',$value);
										//On verifie si on connait la cle
										switch($value){
											case "mission":
												$value = "mission";
												break;
											case "date":
												$value = "date";
												break;
											case "firstname":
											case "prenom":
											case "pr".utf8_decode("e")."nom":
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
											case "deuxi".utf8_decode("è")."mepr".utf8_decode("e")."nom":
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

											case "e-mail_pers":
											case "email_pers":
												$value = "email_pers";

											case "adresse site":
											case "url":
											case "website":
											case "internet":
												$value = "website";
												break;

											case "company":
											case "societe":
											case "soci".utf8_decode("e")."t".utf8_decode("e") :
											case "company name":
											case "companyname":
											case "entreprise":
											case "raison sociale":
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
											case "adresse":
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
											case "d".utf8_decode("e")."pr".utf8_decode("e")."gionbureau":
											case "pays":
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
											case "t".utf8_decode("e")."l".utf8_decode("e")."phone":
											case "telephone":
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
								}else{

									//Variable
									for($i=0;$i<(strlen($line));$i++){
			//dims_print_r($line);
										$letter = $line[$i];
										if($letter != chr(13)){
											switch ($letter){
												// On rencontre une double quote
												case '"':
													if(($_PREV_LETTER == $separateur)&&(!$_INTO_KEY)){
														$_CURRENT_KEY++;
														if($_CURRENT_KEY == $_NB_COL){
															$_CURRENT_KEY = 0;
															$_CURRENT_LINE++;
														}
														$_INTO_KEY = true;
													}else if($_PREV_LETTER == ""){
														$_CURRENT_KEY = 0;
														$_INTO_KEY = true;
													}else if(($_PREV_LETTER == "\\")&&($_INTO_KEY)){
														if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
															$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
														}else{
															$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
														}
													}elseif($_PREV_LETTER == chr(10) && !$_INTO_KEY){
														$_INTO_KEY = true;
														$_CURRENT_KEY = 0;
													}
												break;

												case $separateur:
													if(($_PREV_LETTER == '"') && $_INTO_KEY){
														$_INTO_KEY = false;
													}else if($_PREV_LETTER == $separateur && !$_INTO_KEY){
														$_CURRENT_KEY++;
														if($_CURRENT_KEY == $_NB_COL){
															$_CURRENT_KEY = 0;
															$_CURRENT_LINE++;

														}
													}else if(($_PREV_LETTER == ' ') && !$_INTO_KEY){
														$_CURRENT_KEY++;
														if($_CURRENT_KEY == $_NB_COL){
															$_CURRENT_KEY = 0;
															$_CURRENT_LINE++;

														}
													}else if($_INTO_KEY){
														if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
															$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
														}else{
															$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
														}
													}
												break;

												case chr(10):
													switch($_PREV_LETTER){
														case '"':
															if($_PREV_PREV_LETTER == $separateur && $_INTO_KEY){
																if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
																	$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
																}else{
																	$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
																}
															}else if($_PREV_PREV_LETTER != $separateur && $_INTO_KEY){
																$_CURRENT_LINE++;

																$_INTO_KEY = false;
																$_CURRENT_KEY=0;
															}
														break;

														case $separateur:
															if(!$_INTO_KEY){
																$_CURRENT_LINE++;

																$_INTO_KEY = false;
																$_CURRENT_KEY=0;
															}
														break;

														case chr(10):
															if($_INTO_KEY){
																if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
																	$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
																}else{
																	$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
																}
															}
														break;

														default:
															if($_INTO_KEY){
																if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
																	$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
																}else{
																	$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
																}
															}
														break;
													}


												break;

												default:
													if($_INTO_KEY && $letter != chr(13)){
														if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
															$_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
														}else{
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
							if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE])){
								if(count($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE])==0)
								unset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE]);
							}
						break;
				}

					$total = count($_SESSION['dims']['IMPORT_CONTACT']);
//dims_print_r($_SESSION['dims']['IMPORT_CONTACT']); die();
					foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
						if(verifEnt($data['id'])){
							verifContactExist($data['id']);
							verifEntExist($data['id']);
						}
					}
//dims_print_r($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']); die();
//			SI TU VEUX VOIR LE BEAU TOTAL QUE CA FAIT C'EST ICI :P
//			  echo "Total : ".(count($_SESSION['dims']['IMPORT_CONTACT']).'+'. count($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']).' + '.count($_SESSION['dims']['IMPORT_IGNORED_CONTACT']))."/".$total."<br/>";
//
//			dims_print_r($_SESSION['dims']['IMPORT_CONTACT']);
//			echo $_SESSION['dims']['RL'];
//			dims_print_r($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']);
//			dims_print_r($_SESSION['dims']['IMPORT_IGNORED_CONTACT']);


					//AJOUT DU 26 AOUT 2009
					//Mise a jour des donnees des contacts connu a la volee
					foreach($_SESSION['dims']['IMPORT_KNOWN_CONTACTS'] AS $data['id'] => $data){

						$maj_ct = false;
						$maj_ly = false;

						$tiers = new tiers();
						$tiers->open($data['exist']);

						// recherche si layer pour workspace
						$res=$db->query("SELECT id,type_layer,id_layer
										FROM dims_mod_business_tiers_layer
										WHERE id= :id
										AND type_layer=1
										AND id_layer= :idlayer ", array(
								':id' 		=> $tiers->fields['id'],
								':idlayer'	=> $_SESSION['dims']['workspaceid']
						));

						$ct_layer = new tiers_layer();

						if($db->numrows($res) > 0 ) {
							//echo "select id,type_layer,id_layer from dims_mod_business_contact_layer where id=".$contact->fields['id']." and type_layer=1 and id_layer=".$_SESSION['dims']['workspaceid']; die();
							$sel_layer = $db->fetchrow($res);
							//on charge le layer
							$ct_layer->open($sel_layer['id'],$sel_layer['type_layer'],$sel_layer['id_layer']);
						}
						else {
							//on cree un layer
							$ct_layer->init_description();
							$ct_layer->fields['id'] = $tiers->fields['id'];
							$ct_layer->fields['type_layer'] = 1;
							$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
						}


						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['adresse']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['adresse']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['adresse'] =="") {
									$tiers->fields['adresse'] = $data['address'];
									if(!empty($data['address2'])) $tiers->fields['adresse'] .= $data['address2'];
									if(!empty($data['address3'])) $tiers->fields['adresse'] .= $data['address3'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['adresse'] =="") {
									$ct_layer->fields['adresse'] = $data['address'];
									if(!empty($data['address2'])) $ct_layer->fields['adresse'] .= $data['address2'];
									if(!empty($data['address3'])) $ct_layer->fields['adresse'] .= $data['address3'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['codepostal']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['codepostal']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['codepostal'] =="") {
									$tiers->fields['codepostal'] = $data['cp'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['codepostal'] =="") {
									$ct_layer->fields['codepostal'] = $data['cp'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['ville']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['ville']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['ville'] =="") {
									$tiers->fields['ville'] = $data['city'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['ville'] =="") {
									$ct_layer->fields['ville'] = $data['city'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['telephone']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['telephone']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['telephone'] =="") {
									$tiers->fields['telephone'] = $data['phone'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['telephone'] =="") {
									$ct_layer->fields['telephone'] = $data['phone'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['telecopie']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['telecopie']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['telecopie'] =="") {
									$tiers->fields['telecopie'] = $data['fax'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['telecopie'] =="") {
									$ct_layer->fields['telecopie'] = $data['fax'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['site_web']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['site_web']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['site_web'] =="") {
									$tiers->fields['site_web'] = $data['website'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['site_web'] =="") {
									$ct_layer->fields['site_web'] = $data['website'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['site_web']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['site_web']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['site_web'] =="") {
									$tiers->fields['site_web'] = $data['website'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['site_web'] =="") {
									$ct_layer->fields['site_web'] = $data['website'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['mel']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['mel']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['mel'] =="") {
									$tiers->fields['mel'] = $data['mail'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['mel'] =="") {
									$ct_layer->fields['mel'] = $data['mail'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['telmobile']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['telmobile']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['telmobile'] =="") {
									$tiers->fields['telmobile'] = $data['mobile'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['telmobile'] =="") {
									$ct_layer->fields['telmobile'] = $data['mobile'];
									$maj_ly = 1;
								}
							}
						}

						if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['pays']])) {
							if($_SESSION['dims']['tiers_fields_mode'][$convmeta['pays']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if($tiers->fields['pays'] =="") {
									$tiers->fields['pays'] = $data['country'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if($ct_layer->fields['pays'] =="") {
									$ct_layer->fields['pays'] = $data['country'];
									$maj_ly = 1;
								}
							}
						}

						if($maj_ct || $maj_ly){

							$_SESSION['dims']['IMPORT_COUNT_UPDATE']++;

							if($maj_ct) $tiers->save();
							if($maj_ly) $ct_layer->save();
						}
//dims_print_r($ct_layer); die();
					}

			}else{
				$content_contact_import = '<p style="text-align:center;">'.$_DIMS['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'].'</p><br/>
										<div style="text-align:center;">
											'.dims_create_button_nofloat($_DIMS['cste']['_IMPORT_RETURN_TO_STEP1'], "./common/img/public.png", "dims_redirect('./admin.php?&cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=1');").'
										</div>';
				break;
			}
		}else{
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("srcfilect");
			$content_contact_import = '<div style="margin:10px;width:100%">
											<form action="#" method="post" enctype="multipart/form-data" id="import_step1">
												<table cellpadding="0" cellspacing="4">';
			$tmp =									'<tr>
														<td align="right">
															'.$_DIMS['cste']['_DIMS_LABEL_IMPORTSRC'].'&nbsp;*:&nbsp;
														</td>
														<td>
															&nbsp;<input type="file" name="srcfilect"/>&nbsp;
															<a style="text-decoration:none;" onclick="javascript:displayImportExample(event,\'ent\');">
																Example
															</a>
														</td>
													<tr>';

						   if($dims->isAdmin() || $dims->isManager()) {
								//$content_contact_import .= '<div style="text-align:center;margin-bottom:10px;width:100%;float:left;"><span style="float:left;width:50%;text-align:right">';
								$tmp .=						'<tr>
																<td align="right">'.
																	$_SESSION['cste']['_IMPORT_USER_WHO_IMPORT'].' :&nbsp;
																</td>';
								$tmp .=					'	<td align="left">
																	&nbsp;<select name="user_import">';
								$token->field("user_import");
								//$content_contact_import .= $_SESSION['cste']['_IMPORT_USER_WHO_IMPORT'].'</span><span style="float:left;width:50%;text-align:left">&nbsp;<select name="user_import">';
										$workspace = new workspace();
										$workspace->open($_SESSION['dims']['workspaceid']);
										$users = $workspace->getusers();
										$tmp .=						'<option value="0">-</option>';
										$more_rattach = '';
										foreach($users as $userid => $user){
											$tmp .=					'<option value="'.$userid.'">'.$user['firstname'].' '.$user['lastname'].'</option>';

											$more_rattach .=			'<input type="checkbox" name="'.$userid.'" />'.$user['firstname'].' '.$user['lastname'].'<br />';
											$token->field($userid);
										}
								$tmp .=								'</select>
																</td>
															</tr>';
								//$content_contact_import .= '</div>';

								$content_contact_import .= '<tr>
																<td align="right" valign="top">';
								$content_contact_import .=			'<div style="margin-top:3px;">'.$_SESSION['cste']['_IMPORT_TIERS_ATTACH_MORE'].'</div>
																</td>
																<td>';
								$content_contact_import .=			$more_rattach.'
																</td>
															</tr>';
								$content_contact_import .= $tmp;

								// import entreprise
								$content_contact_import .= '<tr>
																<td align="right">'.$_SESSION['cste']['_IMPORT_ENT_WHO_IMPORT'].' </td>';
								$content_contact_import .= '	<td>
																	&nbsp;<select name="ent_import">';
								$token->field("ent_import");
								//$content_contact_import .= '<div style="text-align:center;margin-bottom:10px;width:100%;float:left;"><span style="float:left;width:50%;text-align:right">';
								//$content_contact_import .= $_SESSION['cste']['_IMPORT_ENT_WHO_IMPORT'].'</span><span style="float:left;width:50%;text-align:left">&nbsp;<select name="ent_import">';

								$res=$db->query("SELECT		*
												 FROM		dims_mod_business_tiers
												 ORDER BY	intitule");
								$content_contact_import .= '<option value="0">-</option>';
								if ($db->numrows($res)>0) {
									while ($ent=$db->fetchrow($res)) {
										$content_contact_import .= '<option value="'.$ent['id'].'">'.$ent['intitule'].'</option>';
									}
								}
								$content_contact_import .= '</select>
														</td>
													</tr>';
								//$content_contact_import .= '</div>';
						   }

						   // gestion des tags
							$_SESSION['dims']['tag_temp']=array();
						  $content_contact_import .= '<tr>
														<td align="right" valign="top">';
						  $content_contact_import .= '		<div style="margin-top:5px;">'.
																$_DIMS['cste']['_DIMS_LABEL_TAGS'].'&nbsp;
															</div>
														</td>
														<td>
															<span style="float:left;text-aling:left;width:45%;margin-left:5px" id="tagblockdisplay">';
						  $content_contact_import .=			dims_getBlockTag($dims, $_DIMS, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, 0);
						  $content_contact_import .='		</span>
														</td>
													 </tr>';

						$content_contact_import .= '</table>';

						$tokenHTML = $token->generate();
						$content_contact_import .= $tokenHTML;

						$content_contact_import .='</form>
											</div>
											<div style ="text-align:center;width:100%;float:left;">'.
												dims_create_button_nofloat($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('import_step1').submit();")
											.'</div>';
		break;
		}

	case 2:

		if($op == 1){
			$content_contact_import = "<p style='text-align:center;'>".count($_SESSION['dims']['IMPORT_IGNORED_CONTACT'])." ".$_SESSION['cste']['_IMPORT_COUNT_OF_IGNORED_TIERS'].".<br/><br/>
				<a href='javascript:void(0);' onclick='javascript:dims_switchdisplay(\"list_ignored_contacts\");'><img src='./common/img/view.png' alt='view'/>&nbsp;".$_DIMS['cste']['_IMPORT_VIEW_LISTE']."</a>";

			$content_contact_import .= "<div id='list_ignored_contacts' style='display:none;'>";
			$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
											<tr class="trl1" style="font-size:12px;">
												<td style="width: 100%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
											</tr>';
			$i = 0;
			$class_col = 'trl1';
			foreach($_SESSION['dims']['IMPORT_IGNORED_CONTACT'] AS $tab_imp){
				if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
				$content_contact_import .= '<tr class="'.$class_col.'">
								<td>'.$tab_imp['company'].'</td>
				</tr>';
				$i++;
			}
			$content_contact_import .= '</table>';
			$content_contact_import .= "</div>";
			$content_contact_import .= "</p>";


			$content_contact_import .=	"<p style='text-align:center;'>";

			$content_contact_import .= count($_SESSION['dims']['IMPORT_KNOWN_CONTACTS'])." ".$_DIMS['cste']['_IMPORT_TIERS_ALREADY_EXISTS']." ".str_replace('[x]', $_SESSION['dims']['IMPORT_COUNT_UPDATE'] ,$_SESSION['cste']['_IMPORT_UPDATED_TIERS'])."<br/><br/>
				<a href='javascript:void(0);' onclick='javascript:dims_switchdisplay(\"list_exist_contacts\");'><img src='./common/img/view.png' alt='view'/>&nbsp;".$_DIMS['cste']['_IMPORT_VIEW_LISTE']."</a>";

			$content_contact_import .= "<div id='list_exist_contacts' style='display:none;'>";
			$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
						<tr class="trl1" style="font-size:12px;">
							<td style="width: 100%;font-weight:bold;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
						</tr>';
			$i = 0;
			$class_col = 'trl1';
			foreach($_SESSION['dims']['IMPORT_KNOWN_CONTACTS'] AS $tab_imp['id'] => $tab_imp){
				if($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
				if(isset($tab_imp['modif']))
					$modif = "<img src='./common/img/icon_model.gif' alt=''/>";
				else
					$modif = "&nbsp;";
				$content_contact_import .= '<tr class="'.$class_col.'">
								<td>'.$tab_imp['company'].'</td>
				</tr>';
				$i++;
			}
			$content_contact_import .= '</table>';
			$content_contact_import .= "</div>";
			$content_contact_import .= "</p>";
			$content_contact_import .= dims_create_button($_DIMS['cste']['_DIMS_BACK'], "./common/img/undo.gif", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=1');");
			$content_contact_import .= "<p style='text-align:center;'>";
			$content_contact_import .= '<div style="text-align:center;">
										'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=2');").'
										</div>';
			$content_contact_import .= "</p>";

			break;
		}

		if($op == 2){
			///////// Calcul des similitudes ///////////
			$lev_nom = 0;
			$lev_pre = 0;
			$coef_nom = 0;
			$coef_pre = 0;
			$coef_tot = 0;
			$count_similariy = 0;
			$_SESSION['dims']['import_contact_similar'] = array();

			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $ct_id => $tab_contact_new){
				foreach($_SESSION['dims']['DB_CONTACT'] AS $tab_contact){

					$lev_nom = levenshtein(strtoupper($tab_contact_new['company']), strtoupper($tab_contact['intitule']));
					$coef_nom = $lev_nom - (ceil(strlen($tab_contact_new['company'])/4));

					$coef_tot = $coef_nom;
	//				  echo "levenshtein : ".$tab_contact_new['lastname']." ".$tab_contact_new['firstname']." et ".$tab_contact['lastname']." ".$tab_contact['firstname']." le total : ".$coef_tot."<br/>";
	//				  echo "les variables : lev du nom = ".$lev_nom." ; lev du prenom = ".$lev_pre." ; coef nom = ".$coef_nom." ; coef prenom = ".$coef_pre."<br/><br/>";
					if($coef_tot < 5) {

						//On verifie qu'il n'y ait pas déjà ce contact dans la table d'import
						$sqlv ="SELECT		*
								FROM		dims_mod_business_tiers_import
								WHERE		intitule LIKE :intitule
								AND		(id_user = :iduser
								OR		id_ct_create = :idcontact )";

						$resv = $db->query($sqlv, array(
							':intitule'		=> strtoupper($tab_contact_new['company']),
							':iduser'		=> $_SESSION['dims']['userid'],
							':idcontact'	=> $_SESSION['dims']['user']['id_contact']
						));
						if($db->numrows($resv) > 0) {
							$count_ignored_similarity++;
						} else {

						//on stock les contacts similaires en base
						$imp = new tiers_import();
						$imp->init_description();

						//donnees obligatoires
						$imp->fields['intitule'] = strtoupper($tab_contact_new['company']);
						$imp->fields['id_user'] = $_SESSION['dims']['import_id_user'];
						$imp->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

						//donnees facultatives
						if(!empty($tab_contact_new['address']))			$imp->fields['adresse'] = $tab_contact_new['address'];
						if(!empty($tab_contact_new['cp']))				$imp->fields['codepostal'] = $tab_contact_new['cp'];
						if(!empty($tab_contact_new['ville']))			$imp->fields['ville'] = $tab_contact_new['ville'];
						if(!empty($tab_contact_new['phone']))			$imp->fields['telephone'] = $tab_contact_new['phone'];
						if(!empty($tab_contact_new['fax']))				$imp->fields['telecopie'] = $tab_contact_new['fax'];
						if(!empty($tab_contact_new['email']))			$imp->fields['mel'] = $tab_contact_new['email'];
						if(!empty($tab_contact_new['website']))			$imp->fields['site_web'] = $tab_contact_new['website'];

						$imp->save();
						$count_similariy++;

						}
						//on ote de le contact de la session
						unset($_SESSION['dims']['IMPORT_CONTACT'][$ct_id]);

						//on a trouve au moins 1 contact similaire, on peut stopper le foreach
						//on se souhaite pas enregistrer plusieurs fois le même contact s'il y a plusieurs similitudes
						break;
					}
				}
			}
			//dims_print_r($_SESSION['dims']['import_contact_similar']);
			//break;
			$_SESSION['dims']['import_count_similar'] = $count_similariy;
			$_SESSION['dims']['import_contact_similar_count'] = 1;
		}


	case 3: /////// Traitement des tiers avec similarites ////////////


		if($op == 3) {
			//on est dans le cas ou on importe depuis la table d'import
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_ent_similarity_case.php');
			break;
		}elseif($_SESSION['dims']['import_count_similar'] > 0) {
			$content_contact_import = '<div style="text-align:center;margin:10px;">'.$_SESSION['dims']['import_count_similar'].'&nbsp;'.$_DIMS['cste']['_IMPORT_SIMILAR_CT'].'</div>';
			$content_contact_import .= '<div style="text-align:center;">
												'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=4');").'
											</div>';
			break;
		}else {
			$content_contact_import = '<div style="text-align:center;margin:10px;">'.$_DIMS['cste']['_IMPORT_NO_SIMILAR_CT'].'</div>';
			$content_contact_import .= '<div style="text-align:center;">
												'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=4');").'
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
			$content_contact_import = "<p>".$_DIMS['cste']['_IMPORT_INSTRUCTION_TIER_STEP4']."<p>";
			$content_contact_import .= "<p>".$_DIMS['cste']['_IMPORT_TAB_LAST_TIER']." :</p><br/>";
			$content_contact_import .= "<p>".count($_SESSION['dims']['IMPORT_CONTACT'])." ".$_DIMS['cste']['_IMPORT_TIER_RESTANT']."</p>";
			$content_contact_import .= '<br/><div style="text-align:center;width:100%">
												'.dims_create_button_nofloat($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('contacts_restants').submit();").'
											</div>';
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$content_contact_import .= '<form action="./admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_IMPORT_ENTREPRISES.'&part='._BUSINESS_TAB_IMPORT_ENTREPRISES.'&op=5" method="post" id="contacts_restants">
										<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
											<tr class="trl1" style="font-size:12px;">
													<td style="width: 10%;">&nbsp;</td>
													<td style="width: 30%;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
											</tr>';

			$class_col = 'trl1';
			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
				if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
				//$date_c = dims_timestamp2local($tab_imp['ent_datecreation']);
				$content_contact_import .= '<tr class="'.$class_col.'">
												<td><input type="checkbox" name="contact_import_'.$data['id'].'" value="'.$data['id'].'" checked="checked"/></td>';
				$token->field("contact_import_".$data['id']);
				$content_contact_import .= '<td>'.$data['company'].'</td>
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
											'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=5');").'
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

		// construction des links existants entre contact et entreprise
		$lsttierslinks=array();
		$res=$db->query("SELECT id_contact from dims_mod_business_tiers_contact where id_tiers= :idtiers ", array(
			':idtiers' => $_SESSION['dims']['import_id_ent']
		));

		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				$lsttierslinks[$f['id_contact']]=1;
			}
		}

		// construction des links existants entre contact et entreprise
		$lsttierslinks=array();
		$res=$db->query("SELECT id_tiers from dims_mod_business_tiers_contact where id_contact= :idcontact ", array(
			':idcontact' => $import_id_ct
		));

		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				$lsttierslinks[$f['id_tiers']]=1;
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

					$new_link->fields['id_tiers'] = $key;
					$new_link->fields['id_contact'] = $data['exist_ct'];
					if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
					$new_link->fields['id_ct_user_create'] = $import_id_ct;
					$new_link->fields['link_level'] = 2;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

					$new_link->save();

					$_SESSION['dims']['IMPORT_LINK_ENT'][$data['exist_ent']][] = $data;
				}
			}
		}
		//dims_print_r($_SESSION['dims']['IMPORT_CONTACT']); die();
		//Creation des contacts
		if (isset($_SESSION['dims']['IMPORT_CONTACT'])) {
			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
				$id_import = dims_load_securvalue("contact_import_".$data['id'], dims_const::_DIMS_CHAR_INPUT, false, true, true);
				if($id_import){
					$tier = new tiers();
					$tier->init_description();

					//on cree un layer
					$ct_layer = new tiers_layer();
					$ct_layer->init_description();

					$ct_layer->fields['type_layer'] = 1;
					$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

					$tier->fields['id_user_create'] = $_SESSION['dims']['import_id_user'];

					if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['intitule']])) {
						if($_SESSION['dims']['tiers_fields_mode'][$convmeta['intitule']] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$tier->fields['intitule'] = $data['company'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['intitule'] = $data['company'];
						}
					}

					if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['mel']])) {
						if($_SESSION['dims']['tiers_fields_mode'][$convmeta['mel']] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$tier->fields['mel'] = $data['email'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['mel'] = $data['email'];
						}
					}

					if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['adresse']])) {
						if($_SESSION['dims']['tiers_fields_mode'][$convmeta['adresse']] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$tier->fields['adresse'] = $data['address'];
							if(!empty($data['address2'])) $tiers->fields['adresse'] .= $data['address2'];
							if(!empty($data['address3'])) $tiers->fields['adresse'] .= $data['address3'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['adresse'] = $data['address'];
							if(!empty($data['address2'])) $ct_layer->fields['adresse'] .= $data['address2'];
							if(!empty($data['address3'])) $ct_layer->fields['adresse'] .= $data['address3'];
						}
					}

					if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['ville']])) {
						if($_SESSION['dims']['tiers_fields_mode'][$convmeta['ville']] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$tier->fields['ville'] = $data['ville'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['ville'] = $data['ville'];
						}
					}

					if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['telephone']])) {
						if($_SESSION['dims']['tiers_fields_mode'][$convmeta['telephone']] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$tier->fields['telephone'] = $data['phone'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['telephone'] = $data['phone'];
						}
					}

					if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['telecopie']])) {
						if($_SESSION['dims']['tiers_fields_mode'][$convmeta['telecopie']] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$tier->fields['telecopie'] = $data['fax'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['telecopie'] = $data['fax'];
						}
					}

					if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['telmobile']])) {
						if($_SESSION['dims']['tiers_fields_mode'][$convmeta['telmobile']] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$tier->fields['telmobile'] = $data['mobile'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['telmobile'] = $data['mobile'];
						}
					}

					if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['pays']])) {
						if($_SESSION['dims']['tiers_fields_mode'][$convmeta['pays']] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$tier->fields['pays'] = $data['country'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['pays'] = $data['country'];
						}
					}

//					if(!empty($data['email'])) $tier->fields['mel']			= $data['email'];
//					if(!empty($data['cp'])) $tier->fields['codepostal']		= $data['cp'];
//					  if(!empty($data['address'])) $tier->fields['adresse']		= $data['address'];
//					  if(!empty($data['address2'])) $tier->fields['adresse'] .= $data['address2'];
//					  if(!empty($data['address3'])) $tier->fields['adresse'] .= $data['address3'];
//					  if(!empty($data['city'])) $tier->fields['ville']		= $data['city'];
//					if(!empty($data['phone'])) $tier->fields['telephone']	= $data['phone'];
//					if(!empty($data['fax'])) $tier->fields['telecopie']		= $data['fax'];
//					if(!empty($data['mobile'])) $tier->fields['telmobile']	= $data['mobile'];
//					if(!empty($data['country'])) $tier->fields['pays']		= $data['country'];

					$tier->save();
					$id_new_contact = $tier->fields['id'];
					$ct_layer->fields['id'] = $id_new_contact;

					$ct_layer->save();

					//$content_contact_import .= "1 import de contact termine.<br/>";
					// on cree le lien intelligence avec la personne
					if ($import_id_ct>0 ) {
						$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers= :idtiers AND id_contact= :idcontact and link_level=2";
						$res = $db->query($sql, array(
							':idtiers'		=> $id_new_contact,
							':idcontact'	=> $import_id_ct
						));
						if($db->numrows($res)==0) {
							$ctlink = new tiersct();
							$ctlink->fields['id_contact']=$import_id_ct;
							$ctlink->fields['id_tiers']=$id_new_contact;
							$ctlink->fields['type_lien']="";
							$ctlink->fields['link_level']=2;
							$ctlink->fields['link_since']=date("YmdHis");
							$ctlink->fields['date_create']=date("YmdHis");
							$ctlink->fields['id_ct_user_create']=$import_id_ct;
							$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
							$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
							$ctlink->save();
						}
					}
					//Si on a trouve une entreprise qui porte exactement le meme nom que la sienne,
					//on l'associe a cette entreprise.
					if($data['exist_ct'] != 0){
						$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers= :idtiers AND id_contact= :idcontact and link_level=2";
						$res = $db->query($sql, array(
							':idtiers'		=> $id_new_contact,
							':idcontact'	=> $data['exist_ent']
						));
						if($db->numrows($res)==0) {
							$new_link = new tiersct();
							$new_link->init_description();
							$new_link->fields['id_tiers'] = $id_new_contact;
							$new_link->fields['id_contact'] = $data['exist_ent'];
							if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
							$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['import_id_user'];
							$new_link->fields['link_level'] = 2;
							$new_link->fields['date_create'] = date("YmdHis");
							$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

							$new_link->save();

							$_SESSION['dims']['IMPORT_LINK_ENT'][$data['exist_ent']][] = $data;
							//$content_contact_import .= "1 association avec une entreprise ajout&eacute;e.<br/>";
						}
					}

					// si on a choisit de lier cette personne a une entreprise on cree aussi le lien entreprise
					if ($_SESSION['dims']['import_id_ent']>0) {
						$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers= :idtiers AND id_contact= :idcontact and link_level=2";
						$res = $db->query($sql, array(
							':idtiers'		=> $id_new_contact,
							':idcontact'	=> $_SESSION['dims']['import_id_ent']
						));
						if($db->numrows($res)==0) {
							$new_link = new ctlink();
							$new_link->init_description();
							$new_link->fields['id_contact1'] = $_SESSION['dims']['import_id_ent'];
							$new_link->fields['id_contact2'] = $id_new_contact;
							if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
							$new_link->fields['id_ct_user_create'] = $_SESSION['dims']['import_id_user'];
							$new_link->fields['link_level'] = 2;
							$new_link->fields['id_object'] = dims_const::_SYSTEM_OBJECT_TIERS;
							$new_link->fields['date_create'] = date("YmdHis");
							$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$new_link->save();
						}
					}

					//On ajoute les rattachement supplementaires
					foreach($_SESSION['dims']['more_rattach_user'] as $id_user_more) {
						$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers= :idtiers AND id_contact= :idcontact and link_level=2 and type_lien='business'";
						$res = $db->query($sql, array(
							':idtiers'		=> $id_new_contact,
							':idcontact'	=> $id_user_more
						));
						if($db->numrows($res)==0) {
							$ctlink = new tiersct();
							$ctlink->fields['id_contact']=$id_user_more;
							$ctlink->fields['id_tiers']=$id_new_contact;
							$ctlink->fields['type_lien']="business";
							$ctlink->fields['link_level']=2;
							$ctlink->fields['link_since']=date("YmdHis");
							$ctlink->fields['date_create']=date("YmdHis");
							$ctlink->fields['id_ct_user_create']=$import_id_ct;
							$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
							$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
							$ctlink->save();
						}
					}

					// attachement des tags
					if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
						foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
							$res=$db->query("SELECT id_tag
											FROM dims_tag_index
											WHERE id_tag= :idtag
											AND id_record= :idrecords
											AND id_user= :iduser
											AND id_module_type=  :idmodule
											AND id_object= :idobject ", array(
								':idtag'		=> $idtag,
								':idrecord'		=> $id_new_contact,
								':iduser'		=> $_SESSION['dims']['import_id_user'],
								':idmodule'		=> $id_module_type,
								':idobject'		=> dims_const::_SYSTEM_OBJECT_TIERS
							));
							if ($db->numrows($res)==0) {
								$tagi = new tag_index();
								$tagi->fields['id_tag']=$idtag;
								$tagi->fields['id_record']=$id_new_contact;
								$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_TIERS;
								$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
								$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
								$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
								$tagi->fields['id_module_type']=$id_module_type;
								$tagi->save();
							}
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
				$tier = new tiers();
				$tier->init_description();

				//on cree un layer
				$ct_layer = new tiers_layer();
				$ct_layer->init_description();

				$ct_layer->fields['type_layer'] = 1;
				$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

				$tier->fields['id_user_create'] = $_SESSION['dims']['import_id_user'];

				if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['intitule']])) {
					if($_SESSION['dims']['tiers_fields_mode'][$convmeta['intitule']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$tier->fields['intitule'] = $data['intitule'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['intitule'] = $data['intitule'];
					}
				}

				if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['mel']])) {
					if($_SESSION['dims']['tiers_fields_mode'][$convmeta['mel']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$tier->fields['mel'] = $data['mel'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['mel'] = $data['mel'];
					}
				}

				if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['adresse']])) {
					if($_SESSION['dims']['tiers_fields_mode'][$convmeta['adresse']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$tier->fields['adresse'] = $data['adresse'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['adresse'] = $data['adresse'];
					}
				}

				if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['ville']])) {
					if($_SESSION['dims']['tiers_fields_mode'][$convmeta['ville']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$tier->fields['ville'] = $data['ville'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['ville'] = $data['ville'];
					}
				}

				if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['telephone']])) {
					if($_SESSION['dims']['tiers_fields_mode'][$convmeta['telephone']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$tier->fields['telephone'] = $data['telephone'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['telephone'] = $data['telephone'];
					}
				}

				if(isset($_SESSION['dims']['tiers_fields_mode'][$convmeta['telecopie']])) {
					if($_SESSION['dims']['tiers_fields_mode'][$convmeta['telecopie']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$tier->fields['telecopie'] = $data['telecopie'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['telecopie'] = $data['telecopie'];
					}
				}

				$tier->save();
				$id_new_contact = $tier->fields['id'];
				$ct_layer->fields['id'] = $id_new_contact;

				$ct_layer->save();

				//$content_contact_import .= "1 import de contact termine.<br/>";
				// on cree le lien intelligence avec la personne
				if ($import_id_ct>0) {
					$ctlink = new tiersct();
					$ctlink->fields['id_contact']=$import_id_ct;
					$ctlink->fields['id_tiers']=$id_new_contact;
					$ctlink->fields['type_lien']="business";
					$ctlink->fields['link_level']=2;
					$ctlink->fields['link_since']=date("YmdHis");
					$ctlink->fields['date_create']=date("YmdHis");
					$ctlink->fields['id_ct_user_create']=$import_id_ct;
					$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
					$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
					$ctlink->save();
				}

				//On ajoute les rattachement supplementaires
				foreach($_SESSION['dims']['more_rattach_user'] as $id_user_more) {
					$ctlink = new tiersct();
					$ctlink->fields['id_contact']=$id_user_more;
					$ctlink->fields['id_tiers']=$id_new_contact;
					$ctlink->fields['type_lien']="business";
					$ctlink->fields['link_level']=2;
					$ctlink->fields['link_since']=date("YmdHis");
					$ctlink->fields['date_create']=date("YmdHis");
					$ctlink->fields['id_ct_user_create']=$import_id_ct;
					$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
					$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
					$ctlink->save();
				}

				//$content_contact_import .= "<br/>";
				// attachement des tags
				if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
					foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
						$tagi = new tag_index();
						$tagi->fields['id_tag']=$idtag;
						$tagi->fields['id_record']=$id_new_contact;
						$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_TIERS;
						$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
						$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$tagi->fields['id_module_type']=$id_module_type;
						$tagi->save();
					}
				}
				$_SESSION['dims']['IMPORT_CONTACT'][$key] = $data;
			}
		}
		unset($_SESSION['dims']['IMPORT_NEW_LINK']);
		unset($_SESSION['dims']['IMPORT_NEW_CT']);

		$content_contact_import .= "<p style='text-align:center;font-size:16px;font-weight:bold;'>".$_DIMS['cste']['_IMPORT_TIER_COMPLETE']."</p><br/>";
		$content_contact_import .= "<p>".$_DIMS['cste']['_IMPORT_IMPORTED_TIER']."</p><br/>";

		$content_contact_import .= '<div style="text-align:center;width:100%;">
											'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=1');").'
										</div>';

		$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">';
		$content_contact_import .= '	<tr style="background:#CECECE;">
											<td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
										</tr>';
		$color2 = "#738CAD";$color1 = "#F1F1F1";$color = '';
		foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $new_ct){
				if($color == $color1) $color = $color2 ; else $color = $color1;
				$content_contact_import .= '<tr style="background:'.$color.';">
												<td style="border-bottom:1px solid #738CAD;">'.$new_ct['company'].'</td>
											</tr>';
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
											'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=1');").'
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
	break;
};
?>
