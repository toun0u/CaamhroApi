<?php

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
	$contact=new contact($db);
	$tab_mtf=$contact->getDynamicFields();
	$listdynfield = array(); // lsite des champs dynamiques trouves dans l'import

	if($_FILES['srcfilect']['name'] != '' && ($extension == "xls" || $extension == "xlsx")){
			$sql = "SELECT id,firstname, lastname, email FROM dims_mod_business_contact WHERE 1";
			$res = $db->query($sql);
			if($db->numrows()>0){
				while($data = $db->fetchrow($res))
					$_SESSION['dims']['DB_CONTACT'][$data['id']] = $data;
			}

			//Mise en session
			if($dims->isAdmin() || $dims->isManager()) {
				$user_imp = dims_load_securvalue("user_import", dims_const::_DIMS_NUM_INPUT, true, true, true);
				if(!empty($user_imp)) $_SESSION['dims']['import_id_user'] = $user_imp;
				else $_SESSION['dims']['import_id_user'] = $_SESSION['dims']['userid'];
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

			// import_tags
			$_SESSION['dims']['IMPORT_TAGS']=array();

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
			//printf(" %d <br>",memory_get_usage());
			$alphabet[1]			= "A";
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

			if(strlen($Column_max) > 2)
				$Column_max = 'AZ';
			//echo "Column max : ".$Column_max."<br/>";

			if ($nb_Column > 1) {
				$last_ocurence	= substr($Column_max, -1);
			}else{
				$last_ocurence	= $Column_max;
			}


			$startline=1;
			$maxline=20;
			$foundcolumn=false;
			$fisrtcolumn=1;

			for ($ii=$startline;$ii<=$maxline;$ii++) {
				$c=0;
				$d=0;
				$fist_lettre = "";
				$lettre = "";
				if (!$foundcolumn) {
					while ($fist_lettre.$lettre != $Column_max){
						$c++;
						$lettre = $alphabet[$c];

						if ($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$ii)->getValue()!='') {
							$foundcolumn=true;
							$startline=$maxline;
							$fisrtcolumn=$ii;
						}

						if ($foundcolumn) {
							if (_DIMS_ENCODING!='UTF-8') {
								$content[$fist_lettre.$lettre] = utf8_decode($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$ii)->getValue());
							}
							else {
								$content[$fist_lettre.$lettre] = $objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$ii)->getValue();
							}

							$content[$fist_lettre.$lettre] = strtolower(trim(str_replace('"','',$content[$fist_lettre.$lettre])));
							//Si l'on arrive sur la derniere colone l'on arret
							if ($alphabet[$c] == "Z") {
								$d++;
								$c = 0;
								$fist_lettre = $alphabet[$d];
							}
						}
					}
				}
			}

			foreach($content AS $key => $value){
				//echo $value."<br/>";

				//On vérifie si on connait la clé
				$val = dims_convertaccents(strtolower(trim($value)));
				$value='';
				//echo $val; die();
				switch($val){
					case "firstname":
					case "prenom":
					case "prénom":
					case "first name":
						$value = "firstname";
					break;

					case "lastname":
					case "nom":
					case 'name':
					case "last name":
						$value = "lastname";
					break;

					case "middlename":
					case "midle name":
					case "deuxièmeprénom":
						$value = "middlename";
					break;

					case "email":
					case "e-mail":
					case "email address":
					case "e-mail address":
					case "courriel":
					case "emailaddress":
					case "mail":
					case "adressedemessagerie":
						$value = "email";
					break;

					case "email2":
					case "email2address":
					case "e-mail address 2":
					case "courriel2":
					case "mail2":
					case "adressedemessagerie2":
						$value = "email2";
					break;

					case "email3":
					case "email3address":
					case "e-mail address 3":
					case "courriel3":
					case "mail3":
					case "adressedemessagerie3":
						$value = "email3";
					break;

					/*case "company":
					case "company name":
					case "societe":
					case "société" :
					case "company name":
					case "companyname":
					case "entreprise":
						$value = "company";
					break;*/

					case "businesspostalcode":
					case "codepostalbureau":
					case "business postal code":
					case "zip code":
					case "codepostal":
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

					case "country":
					case "pays":
						$value = "country";
					break;

					case "ruebureau":
					case "businessstreet":
					case "business street":
					case "adress":
					case "address 1":
						$value = "address";
					break;

					case "ruebureau2":
					case "businessstreet2":
					case "business street 2":
					case "address 2":
					case "address2":
						$value = "address2";
					break;

					case "ruebureau3":
					case "businessstreet3":
					case "business street 3":
					case "address3":
					case "address 3":
						$value = "address3";
					break;

					case "paysregionbureau":
					case "business country/region":
					case "businnescountryregion":
					case "déprégionbureau":
						$value = "country";
					break;

					case "civilite":
					//case "title":
					//case "titre":
					case "salutation 2":
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
					case "alternate phone":
						$value = "phone2";
					break;

					case "telecopiebureau":
					case "télécopiebureau":
					case "businessfax":
					case "business fax":
					case "fax phone":
						$value = "fax";
					break;

					case 'notes': //Traitement des commentaires
						$value="comment";
					break;

					// ajoutdetags
					case 'tags':
						$_SESSION['dims']['IMPORT_TAGS']['0'][]=$key;
						break;

					case 'tags_contacts':
						$_SESSION['dims']['IMPORT_TAGS']['1'][]=$key;
						break;
					case 'tags_industry':
					case 'tags_industrie':
					case 'tags_industries':
						$_SESSION['dims']['IMPORT_TAGS']['2'][]=$key;
						break;
					default :
						// check if dynamic field exists
						foreach ($tab_mtf as $k=>$dfield) {
							if (strtolower($dfield['name']) == $val) {
								$value=$dfield['namefield'];
								$listdynfield[$value]=$val;
							}
						}
					break;
				}
				if ($value!='')
					$_FIELDS[$key] = $value;
			}

			// debug du 26/11/2011 Pat
			//dims_print_r($_FIELDS);
			//die();
			//Boucle sur le nombre de ligne
			for ($i=$fisrtcolumn+1; $i <= $nb_row; $i++){
				//printf("%d %d <br>",$i,memory_get_usage());
				$c=0;
				$d=0;
				$fist_lettre = "";
				$lettre = "";

				while ($fist_lettre.$lettre != $Column_max){
					$c++;
					$lettre = $alphabet[$c];
					if (_DIMS_ENCODING!='UTF-8') {
						$_SESSION['dims']['IMPORT_CONTACT'][$i][$_FIELDS[$fist_lettre.$lettre]] = utf8_decode($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$i)->getValue());
					}
					else {
						$_SESSION['dims']['IMPORT_CONTACT'][$i][$_FIELDS[$fist_lettre.$lettre]] = $objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$i)->getValue();
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

			//echo "Total : ".(count($_SESSION['dims']['IMPORT_CONTACT']) ." ".count($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']) ." ".count($_SESSION['dims']['IMPORT_IGNORED_CONTACT']))."/".$total."<br/>";
			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
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
			//AJOUT DU 26 AOUT 2009
			//Mise a jour des données des contacts connu a la volée

			foreach($_SESSION['dims']['IMPORT_KNOWN_CONTACTS'] AS $data['id'] => $data){
				$maj_ct = false;
				$maj_ly = false;
				$contact = new contact();

				$contact->open($data['exist']);

				// recherche si layer pour workspace
				$res=$db->query("SELECT		id,type_layer,id_layer
								 FROM		dims_mod_business_contact_layer
								 WHERE		id= :id
								 AND		type_layer=1
								 AND		id_layer= :idlayer ", array(
						':id'		=> $contact->fields['id'],
						':idlayer'	=> $_SESSION['dims']['workspaceid']
				));
				$ct_layer = new contact_layer();
				if($db->numrows($res) > 0 ) {
					//echo "select id,type_layer,id_layer from dims_mod_business_contact_layer where id=".$contact->fields['id']." and type_layer=1 and id_layer=".$_SESSION['dims']['workspaceid']; die();
					$sel_layer = $db->fetchrow($res);
					//on charge le layer
					$ct_layer->open($sel_layer['id'],$sel_layer['type_layer'],$sel_layer['id_layer']);
				}
				else {
					//on cree un layer
					$ct_layer->init_description();
					$ct_layer->fields['id'] = $contact->fields['id'];
					$ct_layer->fields['type_layer'] = 1;
					$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
				}

				//if (isset($contact->fields['address'])) { // on peut ouvrir l'objet (note de Flo : $contact->fields['address'] est forcement set, test inutile non ?)

				//foreach($data as $fname => $fval) { //dommage pour le foreach, les champs de data ne sont pas forcement les méme que ceux de contact et/ou layer ...
				//	//on test si la valeur proposee existe
				//	if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta[$fname]])) {
				//		//on test s'il s'agit d'un champ generique ou metier
				//		if($_SESSION['dims']['contact_fields_mode'][$convmeta[$fname]] == 0) {
				//			//c'est un champ generique -> on enregistre dans contact
				//			$contact->fields[$fname] = $fval;
				//		}
				//		else {
				//			//c'est un champ metier -> on enrgistre dans un layer
				//		}
				//	}
				//
				//}

				// nouvel algo pour tenir compte des champs à importer
				foreach ($listdynfield as $fname=>$fval) {
					//echo $fname." ".$data[$fname]." ".$convmeta[$fname]." ".$_SESSION['dims']['contact_fields_mode'][$convmeta[$fname]];die();
					if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta[$fname]])) {
						//on test s'il s'agit d'un champ generique ou metier
						if($_SESSION['dims']['contact_fields_mode'][$convmeta[$fname]] == 0) {
							//c'est un champ generique -> on enregistre dans contact
							$contact->fields[$fname] = $data[$fname];
							$maj_ct = 1;
						}
						else {
							//c'est un champ metier -> on enrgistre dans un layer
							$ct_layer->fields[$fname] = $data[$fname];
							$maj_ly = 1;
						}
					}
				}
				/*dims_print_r($convmeta);

				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['company']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['company']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['company'] =="") {
							$contact->fields['company'] = $data['company'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['company'] =="") {
							$ct_layer->fields['company'] = $data['company'];
							$maj_ly = 1;
						}
					}
				}*/

				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['address'] =="") {
							$contact->fields['address'] = $data['address'];
							if(!empty($data['address2'])) $contact->fields['address'] .= $data['address2'];
							if(!empty($data['address3'])) $contact->fields['address'] .= $data['address3'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['address'] =="") {
							$ct_layer->fields['address'] = $data['address'];
							if(!empty($data['address2'])) $ct_layer->fields['address'] .= $data['address2'];
							if(!empty($data['address3'])) $ct_layer->fields['address'] .= $data['address3'];
							$maj_ly = 1;
						}
					}
				}

				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['postalcode'] =="") {
							$contact->fields['postalcode'] = $data['cp'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['postalcode'] =="") {
							$ct_layer->fields['postalcode'] = $data['cp'];
							$maj_ly = 1;
						}
					}
				}
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['city'] =="") {
							$contact->fields['city'] = $data['city'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['city'] =="") {
							$ct_layer->fields['city'] = $data['city'];
							$maj_ly = 1;
						}
					}
				}
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['phone'] =="") {
							$contact->fields['phone'] = $data['phone'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['phone'] =="") {
							$ct_layer->fields['phone'] = $data['phone'];
							$maj_ly = 1;
						}
					}
				}
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone2']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone2']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['phone2'] =="") {
							$contact->fields['phone2'] = $data['phone2'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['phone2'] =="") {
							$ct_layer->fields['phone2'] = $data['phone2'];
							$maj_ly = 1;
						}
					}
				}
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['fax']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['fax']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['fax'] =="") {
							$contact->fields['fax'] = $data['fax'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['fax'] =="") {
							$ct_layer->fields['fax'] = $data['fax'];
							$maj_ly = 1;
						}
					}
				}
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['civilite'] =="" || $data['civilite']!='' && $data['civilite']!=$contact->fields['civilite']) {
							$contact->fields['civilite'] = $data['civilite'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['civilite'] =="" || $data['civilite']!='' && $data['civilite']!=$ct_layer->fields['civilite']) {
							$ct_layer->fields['civilite'] = $data['civilite'];
							$maj_ly = 1;
						}
					}
				}


				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['civilite'] =="" || $data['titre']!='' && $data['titre']!=$contact->fields['civilite']) {
							$contact->fields['civilite'] = $data['titre'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['civilite'] =="" || $data['titre']!='' && $data['titre']!=$ct_layer->fields['civilite']) {
							$ct_layer->fields['civilite'] = $data['titre'];
							$maj_ly = 1;
						}
					}
				}

				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['mobile'] =="") {
							$contact->fields['mobile'] = $data['mobile'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['mobile'] =="") {
							$ct_layer->fields['mobile'] = $data['mobile'];
							$maj_ly = 1;
						}
					}
				}
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['country'] =="") {
							$contact->fields['country'] = $data['country'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['country'] =="") {
							$ct_layer->fields['country'] = $data['country'];
							$maj_ly = 1;
						}
					}
				}

				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						if($contact->fields['email'] =="") {
							$contact->fields['email'] = $data['email'];
							$maj_ct = 1;
						}
						elseif ($contact->fields['email2'] =="" && $data['email']!=$contact->fields['email']) {
							$contact->fields['email2'] = $data['email'];
							$maj_ct = 1;
						}
						elseif ($contact->fields['email3'] =="" && $data['email']!=$ct_layer->fields['email1'] && $data['email']!=$contact->fields['email2']) {
							$contact->fields['emai3'] = $data['email'];
							$maj_ct = 1;
						}
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						if($ct_layer->fields['email'] =="") {
							$ct_layer->fields['email'] = $data['email'];
							$maj_ly = 1;
						}
						elseif($ct_layer->fields['email2'] ==""  && $data['email']!=$ct_layer->fields['email']) {
							$ct_layer->fields['email2'] = $data['email'];
							$maj_ly = 1;
						}
						elseif($ct_layer->fields['email3'] ==""  && $data['email']!=$ct_layer->fields['email'] && $data['email']!=$ct_layer->fields['email2']) {
							$ct_layer->fields['email3'] = $data['email'];
							$maj_ly = 1;
						}
					}
				}

				if($maj_ct || $maj_ly){

					$_SESSION['dims']['IMPORT_COUNT_UPDATE']++;

					if($maj_ct) $contact->save();
					if($maj_ly) $ct_layer->save();
				}
				//}
			}

			require_once(DIMS_APP_PATH . '/modules/system/lfb_public_contact_import_outlook_switch_2.php');
	}else{
		$content_contact_import = '<p style="text-align:center;">'.$_DIMS['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'].'</p><br/>
								<div style="text-align:center;">
									'.dims_create_button_nofloat($_DIMS['cste']['_IMPORT_RETURN_TO_STEP1'], "./common/img/public.png", "dims_redirect('./admin.php?&cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=1');").'
								</div>';
	}
}else{
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$content_contact_import = '<div style="margin:10px;width:100%">
								<form action="#" method="post" enctype="multipart/form-data" id="import_step1">
									<table cellspacing="0" cellpadding="0">
										<tr>
											<td align="right">
												'.$_DIMS['cste']['_DIMS_LABEL_IMPORTSRC'].'&nbsp;*:&nbsp;
											</td>
											<td>
												&nbsp;<input type="file" name="srcfilect"/>&nbsp;
												<a style="text-decoration:none;" onclick="javascript:displayImportExample(event,\'ct\');">
													Example
												</a>
											</td>
										</tr>';
	$token->field("srcfilect");
				   if($dims->isAdmin() || $dims->isManager() || $_SESSION['dims']['userid'] == '151') { //exception pour michele diederich
						$content_contact_import .= '<tr>
														<td align="right">
															'.$_SESSION['cste']['_IMPORT_USER_WHO_IMPORT'].'&nbsp;
														</td>
														<td>
															&nbsp;<select name="user_import">';
						$token->field("user_import");
								$workspace = new workspace();
								$workspace->open($_SESSION['dims']['workspaceid']);
								$users = $workspace->getusers();
								$content_contact_import .= '<option value="0">-</option>';
								foreach($users as $userid => $user){
									$content_contact_import .= '<option value="'.$userid.'">'.$user['firstname'].' '.$user['lastname'].'</option>';
								}
						$content_contact_import .= '		</select>
														</td>
													</tr>';

						// import entreprise
						$content_contact_import .= '<tr>
														<td align="right">
															'.$_SESSION['cste']['_IMPORT_ENT_WHO_IMPORT'].'&nbsp;
														</td>
														<td>
															&nbsp;<select name="ent_import">';
						$token->field("ent_import");

						$res=$db->query("select * from dims_mod_business_tiers order by intitule");
						$content_contact_import .= '<option value="0">-</option>';
						if ($db->numrows($res)>0) {
							while ($ent=$db->fetchrow($res)) {
								$content_contact_import .= '<option value="'.$ent['id'].'">'.$ent['intitule'].'</option>';
							}
						}
						$content_contact_import .= '		</select>
														</td>
													</tr>';
				   }

				   // gestion des tags
					$_SESSION['dims']['tag_temp']=array();
				  $content_contact_import .= '		<tr>
														<td align="right" valign="top">
															<div style="margin-top:5px;">'.$_DIMS['cste']['_DIMS_LABEL_TAGS'].'&nbsp;</div>
														</td>
														<td>
															<span style="float:left;text-align:left;width:45%;margin-left:5px;" id="tagblockdisplay">';
				  $content_contact_import .=			dims_getBlockTag($dims, $_DIMS, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, 0);
				  $content_contact_import .='		</span>
														</td>
													</tr>';

				$content_contact_import .= '	</table>';
				$tokenHTML = $token->generate();
				$content_contact_import .= $tokenHTML;
				$content_contact_import .= '</form>
										</div>
										<div style="text-align:center;width:100%;float:left;">'.
											dims_create_button_nofloat($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('import_step1').submit();").'
										</div>';
}

?>
