<?php

//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
ini_set('max_execution_time',-1);
ini_set('memory_limit','512M');

function verifContact($current_line){
	global $db;
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
	global $db;
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])){
		$sql = "SELECT id FROM dims_mod_business_tiers WHERE intitule = :intitule";
		$res = $db->query($sql, array(':intitule' => addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company']) ) );
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
	global $db;
	if(isset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])){
		$sql = "SELECT DISTINCT		c.id
		FROM				dims_mod_business_contact c
		LEFT JOIN			dims_mod_business_contact_layer l
		ON					l.id = c.id
		WHERE				l.id_layer = :layer
		AND					c.firstname LIKE :firstname
		AND					c.lastname LIKE :lastnames
		";
//		  $sql = "SELECT id FROM dims_mod_business_contact WHERE lastname email = '".addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['email'])."'";
		$res = $db->query($sql, array(
								':layer' => $_SESSION['dims']['workspaceid'],
								'firstname' => addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['firstname']),
								'lastname' => addslashes($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['lastname'])
								)
						);
		if($db->numrows()>0){
			$data = $db->fetchrow($res);
			$_SESSION['dims']['IMPORT_KNOWN_CONTACTS'][$current_line] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			if($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist_ent'] != 0){
				$_SESSION['dims']['IMPORT_NEW_LINK'][$data['id']] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line];
			}
			//unset($_SESSION['dims']['IMPORT_CONTACT'][$current_line]);
			$_SESSION['dims']['IMPORT_KNOWN_CONTACTS'][$current_line]['exist'] = $data['id'];

			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['checked']  = 1;
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['ct_id']    = $data['id'];
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['attached'] = 0;
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist'] = 1;
		}
		else {
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist'] = 0;
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['checked']  = 0;
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['ct_id']    = 0;
			$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['attached'] = 0;
			$_SESSION['dims']['IMPORT_SIMILAR_CT'][] = $current_line;
		}
	}else{
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['exist'] = 0;
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['checked']  = 0;
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['ct_id']    = 0;
		$_SESSION['dims']['IMPORT_CONTACT'][$current_line]['attached'] = 0;
		$_SESSION['dims']['IMPORT_SIMILAR_CT'][] = $current_line;
	}
}

switch($import_op) {
	default:
			echo '<div>';
			echo dims_create_button($_DIMS['cste']['_DIMS_BACK'], "./common/img/undo.gif", "dims_redirect('./admin.php?action=adm_evt&id_evt=".$id_evt."');");
			echo '</div>';
			echo '<div style="margin:10px;text-align:center;width:100%">
				<form action="#" method="post" enctype="multipart/form-data" id="import_step1">
				<input type="hidden" name="import_op" value="1" />
				'.$_DIMS['cste']['_DIMS_LABEL_IMPORTSRC'].'&nbsp;*:&nbsp;<input type="file" name="srcfilect"/><br/><br/>';
			echo '<div style="text-align:center;width:100%;flat:left;">'.
				dims_create_button_nofloat($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('import_step1').submit();")
				.'</div>
				</form>
				</div>';
		break;

	case 1:
		//Extraction du fichier : génération de $_SESSION['dims']['IMPORT_CONTACT'] (array des enregistrement)
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
			unset($_SESSION['dims']['IMPORT_SIMILAR_CT']);

			$extension	= explode(".", $_FILES['srcfilect']['name']);
			$extension	= $extension[count($extension)-1];
			$extension	= strtolower($extension);

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
				if($_FILES['srcfilect']['name'] != '' && ($extension == "xls" || $extension == "xlsx")){
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

					$file	= $_FILES['srcfilect']['tmp_name'];

					$liste_version["csv"]	= "CSV";
					$liste_version["xlsx"]	= "Excel2007";
					$liste_version["xls"]	= "Excel5";

					/** PHPExcel_IOFactory */
					require_once(DIMS_APP_PATH . '/include/PHPExcel/IOFactory.php');

					//echo date('H:i:s') . " Load from Excel2007 file\n<br>";

					//on instancie un objet de lecture
					$objReader = PHPExcel_IOFactory::createReader($liste_version[$extension]);
					$objReader->setReadDataOnly(true);

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
						$valtest=$objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre."1")->getValue();
						$val = ($valtest instanceof PHPExcel_RichText) ? $valtest->getPlainText() : $valtest;
						if (_DIMS_ENCODING!='UTF-8') {
							if (!is_array($val)) {
								$content[$fist_lettre.$lettre] = utf8_decode($val);
							}
						}
						else {

							if (!is_array($val)) {
								$content[$fist_lettre.$lettre] = $val;
							}
						}
						//$content[$fist_lettre.$lettre] = utf8_encode($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre."1")->getValue());
						$content[$fist_lettre.$lettre] = strtolower(trim(str_replace('"','',$content[$fist_lettre.$lettre])));
						//Si l'on arrive sur la derniere colone l'on arret
						if ($alphabet[$c] == "Z") {
							$d++;
							$c = 0;
							$fist_lettre = $alphabet[$d];
						}
					}

					foreach($content AS $key => $value){
						//On v�rifie si on connait la cl�
						switch($value){
							case "firstname":
							case "prenom":
							case "prénom":
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
							case "deuxièmeprénom":
								$value = "middlename";
							break;

							case "email":
							case "email address":
							case "e-mail address":
							case "courriel":
							case "emailaddress":
							case "mail":
							case "adressedemessagerie":
								$value = "email";
							break;

							case "company":
							case "societe":
							case "soci�t�" :
							case "company name":
							case "companyname":
							case "entreprise":
								$value = "company";
							break;

							case "businesspostalcode":
							case "codepostalbureau":
							case "business postal code":
								$value = "cp";
							break;

							case "city":
							case "localite":
							case "ville":
							case "businesscity":
							case "business city":
							case "villebureau":
								$value = "ville";
							break;

							case "ruebureau":
							case "businessstreet":
							case "business street":
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
							case "déprégionbureau":
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
							case "téléphonebureau":
							case "businessphone":
							case "business phone":
								$value = "phone";
							break;

							case "telephonebureau2":
							case "téléphonebureau2":
							case "businessphone2":
							case "business phone2":
								$value = "phone2";
							break;

							case "telecopiebureau":
							case "télécopiebureau":
							case "businessfax":
							case "business fax":
								$value = "fax";
							break;

							case 'notes': //Traitement des commentaires
								$value="comment";
							break;
						}
						$_FIELDS[$key] = $value;
					}
					//dims_print_r($_FIELDS);die();

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

							$valtest=$objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$i)->getValue();
							$val = ($valtest instanceof PHPExcel_RichText) ? $valtest->getPlainText() : $valtest;
							if (is_array($val)) {
								$val='';
							}

							if (_DIMS_ENCODING!='UTF-8') {
								$_SESSION['dims']['IMPORT_CONTACT'][$i][$_FIELDS[$fist_lettre.$lettre]] = utf8_decode($val);
							}
							else {
								$_SESSION['dims']['IMPORT_CONTACT'][$i][$_FIELDS[$fist_lettre.$lettre]] = ($val);
							}
							////Si l'on arrive sur la derniere colone l'on arret
							if ($alphabet[$c] == "Z") {
								$d++;
								$c = 0;
								$fist_lettre = $alphabet[$d];
							}
						}
					}
					/*
						print "<pre>";
						print_r($_SESSION['dims']['IMPORT_CONTACT']);
						print "</pre>";
					 */
					unset($obj_all_sheets);
					unset($objPHPExcel);

					foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
						// verification si non vide
						if ($data['firstname']== "" && $data['lastname'] == "" && $data['email'] == "") {
							unset($_SESSION['dims']['IMPORT_CONTACT'][$data['id']]);
						}
						else {
							if(verifContact($data['id'])){
								verifEntExist($data['id']);
								verifContactExist($data['id']);
							}
						}
					}
					//printf("Mem : %d <br>",memory_get_usage());
					//AJOUT DU 26 AOUT 2009
					//Mise a jour des donn�es des contacts connu a la vol�e
					foreach($_SESSION['dims']['IMPORT_KNOWN_CONTACTS'] AS $data['id'] => $data){
						$maj = false;
						$contact = new contact();
						$contact->open($data['exist']);

						if (isset($contact->fields['address'])) { // on peut ouvrir l'objet
							if(!empty($data['address']) && $contact->fields['address'] =="") {
								$contact->fields['address'] = $data['address'];
								if(!empty($data['address2'])) $contact->fields['address'] .= $data['address2'];
								if(!empty($data['address3'])) $contact->fields['address'] .= $data['address3'];
								$maj = 1;
							}

							if(!empty($data['cp']) && $contact->fields['postalcode']=="") {
								$contact->fields['postalcode'] = $data['cp'];
								$maj = 1;
							}

							if(!empty($data['city']) && $contact->fields['city'] =="") {
								$contact->fields['city'] = $data['city'];
								$maj = 1;
							}

							if(!empty($data['phone']) && $contact->fields['phone'] == "") {
								$contact->fields['phone'] = $data['phone'];
								$maj = 1;
							}

							if(!empty($data['phone2']) && $contact->fields['phone2'] == "") {
								$contact->fields['phone2'] = $data['phone2'];
								$maj = 1;
							}

							if(!empty($data['fax']) && $contact->fields['fax'] == "") {
								$contact->fields['fax'] = $data['fax'];
								$maj = 1;
							}

							if(!empty($data['civilite']) && $contact->fields['civilite'] == "") {
								$contact->fields['civilite'] = $data['civilite'];
								$maj = 1;
							}

							if(!empty($data['mobile']) && $contact->fields['mobile'] == "") {
								$contact->fields['mobile'] = $data['mobile'];
								$maj = 1;
							}

							if(!empty($data['country']) && $contact->fields['country'] == "") {
								$contact->fields['country'] = $data['country'];
								$maj = 1;
							}

							if($maj){
								$contact->save();
								$_SESSION['dims']['IMPORT_COUNT_UPDATE']++;
							}
						}
					}

			}
			else {

				$sql = "SELECT id,firstname, lastname, email FROM dims_mod_business_contact WHERE 1";
				$res = $db->query($sql);
				if($db->numrows()>0){
					while($data = $db->fetchrow($res))
						$_SESSION['dims']['DB_CONTACT'][$data['id']] = $data;
				}

				$created = array();
				$errors = array();
				$_FIELDS = array();
				$_SESSION['dims']['IMPORT_CONTACT']=array();
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
										elseif($_INTO_KEY) {
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
			}
			$nb_checked=0;
			foreach($_SESSION['dims']['IMPORT_CONTACT'] as $key => $line) {
				if ($line['checked']) $nb_checked++;
			}

				/*
				foreach($_SESSION['dims']['IMPORT_CONTACT'] as $key => $line) {
					if(isset($line['lastname']) && !empty($line['lastname']) &&
					   isset($line['firstname']) && !empty($line['firstname'])) {

						if(isset($line['email']) && !empty($line['email'])) {
							$sql = 'SELECT id
									FROM dims_mod_business_contact
									WHERE email like "'.$line['email'].'"';

							$ress = $db->query($sql);

							if($db->numrows($ress) > 0) {
								$result = $db->fetchrow($ress);

								$_SESSION['dims']['IMPORT_CONTACT'][$key]['checked']  = 1;
								$_SESSION['dims']['IMPORT_CONTACT'][$key]['ct_id']	  = $result['id'];
								$_SESSION['dims']['IMPORT_CONTACT'][$key]['attached'] = 0;

								$nb_checked++;
							}
							else {
								$_SESSION['dims']['IMPORT_CONTACT'][$key]['checked']  = 0;
								$_SESSION['dims']['IMPORT_CONTACT'][$key]['ct_id']	  = 0;
								$_SESSION['dims']['IMPORT_CONTACT'][$key]['attached'] = 0;

								$_SESSION['dims']['IMPORT_SIMILAR_CT'][] = $key;
							}
						}
						else {
							$_SESSION['dims']['IMPORT_CONTACT'][$key]['checked']  = 0;
							$_SESSION['dims']['IMPORT_CONTACT'][$key]['ct_id']	  = 0;
							$_SESSION['dims']['IMPORT_CONTACT'][$key]['attached'] = 0;

							$_SESSION['dims']['IMPORT_SIMILAR_CT'][] = $key;
						}
					}
					else {
						//Destruction des enregistrement "vide" (Sans nom ET prénom)
						unset($_SESSION['dims']['IMPORT_CONTACT'][$key]);
					}
				}
				*/
				echo '<div>';
				echo $nb_checked.' '.$_SESSION['cste']['_IMPORT_MISSION_CONTACT_KNOW'];
				echo '</div><div>';
				echo dims_create_button($_DIMS['cste']['_DIMS_BACK'], "./common/img/undo.gif", "dims_redirect('./admin.php?action=adm_evt&id_evt=".$id_evt."');");
				echo dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&import_op=2');");

				echo "</div>";
				$content_contact_import = "<p style='text-align:center;'>".count($_SESSION['dims']['IMPORT_IGNORED_CONTACT'])." ".$_SESSION['cste']['_IMPORT_COUNT_OF_IGNORED_CONTACTS'].".<br/><br/>
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
				foreach($_SESSION['dims']['IMPORT_IGNORED_CONTACT'] AS $tab_imp){
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
				echo $content_contact_import;

				echo '<table width="100%" style="clear: both;">';

				if($nb_checked > 0) {
					$class = 'trl1';
					foreach($_SESSION['dims']['IMPORT_CONTACT'] as $key => $line) {
						if(isset($line['checked']) && $line['checked'] == 1) {

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

				echo dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&import_op=2');");
				echo '</div>';

			}
			else {
				echo '<p style="text-align:center;">'.$_DIMS['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'].'</p>
				<div style="text-align:center;">
				'.dims_create_button_nofloat($_DIMS['cste']['_IMPORT_RETURN_TO_STEP1'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&import_op=0');").'
				</div>';
			}
		}
		else {
			dims_redirect('admin.php?action=import_insc&id_evt='.$id_evt.'&import_op=0');
		}
		break;

	case 2:
		echo '<div>';
		echo dims_create_button($_DIMS['cste']['_DIMS_BACK'], "./common/img/undo.gif", "dims_redirect('./admin.php?action=adm_evt&id_evt=".$id_evt."');");
		echo "</div>";

		$unknow_ct = count($_SESSION['dims']['IMPORT_SIMILAR_CT']);

		if(isset($_POST) && !empty($_POST)) {
			$idsimilar = dims_load_securvalue('id_similar', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$key = $_SESSION['dims']['IMPORT_SIMILAR_CT'][$idsimilar];
			$line = $_SESSION['dims']['IMPORT_CONTACT'][$key];

			$id_to_attach = dims_load_securvalue('id_ct_to_attach', dims_const::_DIMS_NUM_INPUT, true, true, true);

			if($id_to_attach != 0) {
				if($id_to_attach == -1) {

					$ct = new contact();

					$ct->fields['lastname'] = $line['firstname'];
					$ct->fields['firstname'] = $line['lastname'];
					$ct->fields['civilite'] = (isset($line['civilite'])) ? $line['civilite'] : '';
					$ct->fields['phone'] = (isset($line['phone'])) ? $line['phone'] : '';
					$ct->fields['email'] = (isset($line['email'])) ? $line['email'] : '';
					$ct->fields['id_user_create'] = $_SESSION['dims']['userid'];

					$id_to_attach = $ct->save();

					$_SESSION['dims']['DB_CONTACT'][$id_to_attach]['id']		= $id_to_attach;
					$_SESSION['dims']['DB_CONTACT'][$id_to_attach]['email']		= (isset($line['email'])) ? $line['email'] : '';
					$_SESSION['dims']['DB_CONTACT'][$id_to_attach]['lastname']	= $line['lastname'];
					$_SESSION['dims']['DB_CONTACT'][$id_to_attach]['firstname'] = $line['firstname'];
				}
				elseif($id_to_attach > 0) {
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

				$_SESSION['dims']['IMPORT_CONTACT'][$key]['checked']  = 1;
				$_SESSION['dims']['IMPORT_CONTACT'][$key]['ct_id']	  = $id_to_attach;
			}

			unset($_SESSION['dims']['IMPORT_SIMILAR_CT'][$idsimilar]);
			$unknow_ct--;


			if($unknow_ct == 0)
				dims_redirect('admin.php?action=import_insc&id_evt='.$id_evt.'&import_op=3');
		}

		if($unknow_ct > 0) {
			echo '<div>';
			echo '<p>';
			echo $unknow_ct.' '.$_SESSION['cste']['_DIMS_IMPORT_MISSION_STILL_UNKNOW'];
			echo '</p>';
			echo '<div>';

			reset($_SESSION['dims']['IMPORT_SIMILAR_CT']);
			$key = each($_SESSION['dims']['IMPORT_SIMILAR_CT']);

			$line = $_SESSION['dims']['IMPORT_CONTACT'][$key['value']];

			echo '<form method="POST" action="#" name="similar_ct" id="similar_ct">';
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
			echo '<td>';
			echo $line['email'];
			echo '</td>';
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
			echo '<td><input type="radio" name="id_ct_to_attach" value="-1"></td>';
			echo '</tr>';
			echo '<tr class="'.$class.'">';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>'.$_SESSION['cste']['_IMPORT_NEXT_SIMILAR_CONTACT'].'</td>';
			echo '<td><input checked="checked" type="radio" name="id_ct_to_attach" value="0"></td>';
			echo '</tr>';
			echo '</table>';
			echo '</form>';
			echo '<div>';
			echo dims_create_button($_DIMS['cste']['_DIMS_VALID'], "./common/img/publish.png", "dims_getelem('similar_ct').submit();");
			echo '</div>';

			echo '<div style="text-align:right; clear: both;">'.
				dims_create_button_nofloat($_DIMS['cste']['_IMPORT_GO_NEXT_STEP'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&import_op=3');").'
				</div>';
			echo '<div style="float:right;border:1px solid #738CAD;width:150px;padding:5px;margin-top:5px;"><img src="./common/img/warning.png" alt="/!\">&nbsp;'.$_DIMS['cste']['_IMPORT_MISSION_WARN_STEP3'].'</div>';
		}
		else {
			echo '<div>';
			echo '<p>';
			echo $_SESSION['cste']['_DIMS_IMPORT_MISSION_NO_INKNOW'];
			echo '</p>';
			echo '<div>';
			echo dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?action=import_insc&id_evt=".$id_evt."&import_op=3');");
		}

		break;
	case 3:

		$tabAttached	= array();
		$tabIgnored		= array();

		foreach($_SESSION['dims']['IMPORT_CONTACT'] as $line) {

			if($line['checked'] == 1 && !empty($line['ct_id'])) {

				$sql = 'SELECT id
						FROM dims_mod_business_event_inscription
						WHERE id_action = :idaction
						AND id_contact = :idcontact';

				$ress = $db->query($sql, array(':idaction' => $_SESSION['dims']['IMPORT_MISSION']['id_evt'], ':idcontact' => $line['ct_id']) );

				if($db->numrows($ress) == 0) {

					$inscrip = new event_insc();

					$inscrip->fields['id_action']	= $_SESSION['dims']['IMPORT_MISSION']['id_evt'];
					$inscrip->fields['id_contact']	= $line['ct_id'];
					$inscrip->fields['validate']	= 2;
					$inscrip->fields['email']		= (isset($line['email'])) ? $line['email'] : '';
					$inscrip->fields['lastname']	= (isset($line['lastname'])) ? $line['lastname'] : '';
					$inscrip->fields['firstname']	= (isset($line['firstname'])) ? $line['firstname']: '';
					$inscrip->fields['company']		= (isset($line['company'])) ? $line['company']: '';
					$inscrip->fields['address']		= (isset($line['address'])) ? $line['address']: '';
					$inscrip->fields['postalcode']	   = (isset($line['postalcode'])) ? $line['postalcode']: '';
					$inscrip->fields['city']	 = (isset($line['ville'])) ? $line['ville']: '';
					$inscrip->fields['country']		= (isset($line['country'])) ? $line['country']: '';
					$inscrip->fields['email']	  = (isset($line['email'])) ? $line['email']: '';
					$inscrip->fields['function']	 = (isset($line['function'])) ? $line['function']: '';

					$inscrip->fields['date_validate']= date('Ymd000000');

					$inscrip->save();

					$inscrip->verifStep($_SESSION['dims']['IMPORT_MISSION']['id_evt'], $line['ct_id']);

					$line['attached'] = 1;

				}

				$tabAttached[] = $line;
			}
			else {

				$tabIgnored[] = $line;

			}
		}

		echo '<div style="margin-left: 15px; margin-top: 20px; margin-bottom: 20px;">';
		echo $_SESSION['cste']['_IMPORT_MISSION_RATTACHED_CONTACTS'].' : <br />';

		echo '<p>';
		echo dims_create_button($_DIMS['cste']['_DIMS_EVT_BACK_REGISTRATION'], "./common/img/public.png", "dims_redirect('./admin.php?action=adm_evt&id_evt=".$_SESSION['dims']['IMPORT_MISSION']['id_evt']."');");
		echo '</p>';

		echo '<table width="100%" style="clear:both;">';

		if(count($tabAttached)) {

			$class = 'trl1';
			foreach($tabAttached as $key => $line) {

					echo '<tr class="'.$class.'">';
					echo '<td width="15%">';
					echo $line['lastname'];
					echo '</td>';
					echo '<td width="15%">';
					echo $line['firstname'];
					echo '</td>';
					echo '<td width="15%">';
					echo (isset($line['email'])) ? $line['email'] : 'n/a';
					echo '</td>';
					echo '<td width="45%">';
					if($line['attached'] == 0)
						echo $_SESSION['cste']['_IMPORT_TEXT_CT_ALREADY_ATTACHED'];
					else
						echo '&nbsp;';
					echo '</td>';
					echo '</tr>';

					$class = ($class == 'trl1') ? 'trl2' : 'trl1';
				}
		}
		else {
			echo '<tr><td>'.$_SESSION['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr>';
		}
		echo '</table>';
		echo '</div>';
		if(count($tabIgnored) > 0) {

			echo '<p>';
			echo dims_create_button($_DIMS['cste']['_DIMS_EVT_BACK_REGISTRATION'], "./common/img/public.png", "dims_redirect('./admin.php?action=adm_evt&id_evt=".$_SESSION['dims']['IMPORT_MISSION']['id_evt']."');");
			echo '</p>';
			echo '<div style="margin-left: 15px; margin-top: 20px; margin-bottom: 20px;">';
			echo $_SESSION['cste']['_IMPORT_MISSION_IGNORED_CONTACT'].' : <br />';
			echo '<table width="100%">';

			$class = 'trl1';
			foreach($tabIgnored as $key => $line) {

					echo '<tr class="'.$class.'">';
					echo '<td width="15%">';
					echo $line['lastname'];
					echo '</td>';
					echo '<td width="15%">';
					echo $line['firstname'];
					echo '</td>';
					echo '<td>';
					echo (isset($line['email'])) ? $line['email'] : 'n/a';
					echo '</td>';
					echo '</tr>';

					$class = ($class == 'trl1') ? 'trl2' : 'trl1';
				}
		}
		else {
			echo '<table width="100%"><tr><td>'.$_SESSION['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr>';
		}
		echo '</table>';
		echo '</div>';
		echo '<p>';
		echo dims_create_button($_DIMS['cste']['_DIMS_EVT_BACK_REGISTRATION'], "./common/img/public.png", "dims_redirect('./admin.php?action=adm_evt&id_evt=".$_SESSION['dims']['IMPORT_MISSION']['id_evt']."');");
		echo '</p>';

		/*unset($_SESSION['dims']['IMPORT_CONTACT']);
		unset($_SESSION['dims']['IMPORT_SIMILAR_CT']);
		unset($_SESSION['dims']['DB_CONTACT']);*/

		break;
}

?>
