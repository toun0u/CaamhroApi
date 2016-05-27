<script language="JavaScript" type="text/JavaScript">
	function view_news() {
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo _DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=view_new_ent","", 'new_ent_import');
		dims_switchdisplay('new_ent_import');
	}

	function view_old() {
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo _DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=view_old_ent","", 'old_ent_import');
		dims_switchdisplay('old_ent_import');
	}

	function view_errors(){
		dims_switchdisplay('probs_ent_import');
	}

	//ajouts et suppression des imports entreprise
	function add_imp_ent(id_imp) {
		dims_xmlhttprequest_tofunction("admin.php", "dims_mainmenu=<?php echo _DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=add_imp_ent&id_imp="+id_imp, view_news);
		var div_content = document.getElementById('new_ent_import');
		div_content.style.display = "block";
	}

	function add_imp_ent_multi(ids_imp) {
		var retour = dims_xmlhttprequest_post("admin.php?dims_mainmenu=9&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=add_imp_ent", "id_imp="+id_imp);
		var div_content = document.getElementById('new_ent_import');
		div_content.style.display = "block";
	}

	function add_imp_ent_old(id_imp, id_tiers) {
		dims_xmlhttprequest_tofunction("admin.php", "dims_mainmenu=<?php echo _DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=add_imp_ent_old&id_imp="+id_imp+"&id_tiers="+id_tiers, view_old);
		var div_content = document.getElementById('old_ent_import');
		div_content.style.display = "block";
	}

	function del_imp_ent(id_imp) {
		dims_xmlhttprequest_tofunction("admin.php", "dims_mainmenu=<?php echo _DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=del_imp_ent&id_imp="+id_imp, view_news);
		var div_content = document.getElementById('new_ent_import');
		div_content.style.display = "block";
	}

	function del_old_ent(id_imp) {
		dims_xmlhttprequest_tofunction("admin.php", "dims_mainmenu=<?php echo _DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=del_imp_ent&id_imp="+id_imp, view_old);
		var div_content = document.getElementById('old_ent_import');
		div_content.style.display = "block";
	}


	//#############################
	// Faut vérifier que j'en ai pas
	// aussi besoin pour les contacts
	//#############################

	function view_detail_imp(div){
		dims_switchdisplay(div);
	}

	function dropTableImport() {
		dims_xmlhttprequest("admin.php", "op=droptableimport_ent", true, '');
	}

	function selectAll(){
		var i = 0;
		while(dims_getelem('ent_imp_'+i) != false){
			var e = dims_getelem('ent_imp_'+i);
			e.checked = "checked";
			i++;
		}
	}

	function unselectAll(){
		var i = 0;
		while(dims_getelem('ent_imp_'+i) != false){
			var e = dims_getelem('ent_imp_'+i);
			e.checked = "";
			i++;
		}
	}

</script>
<?

require_once(DIMS_APP_PATH . "/modules/system/class_tiers.php");
require_once(DIMS_APP_PATH . "/modules/system/class_tiers_import.php");
require_once(DIMS_APP_PATH . "/modules/system/class_contact_import.php");
//initialisation des variables
$num = 1;
$cpt_not_exist = 0;
$cpt_exist = 0;
$cpt_err_intit = 0;
$cpt_not_exist_ct = 0;
$cpt_not_exist_ent = 0;
$cpt_all_exist = 0;
$cpt_new_ct_no_ent = 0;
$cpt_old_ct_no_ent = 0;

$type_imp = 'ent';

$id_creator = dims_load_securvalue('imp_creator', _DIMS_NUM_INPUT, true, true);

//si $_FILES est set, on vient d'importer un fichier
//dans le cas contraire, c'est que l'on revient sur un ancien import
if(!empty($_FILES['srcfileent'])) {
	if($_FILES['srcfileent']['name'] != '')
	{
		$created = array();
		$errors = array();
		$fields = array();

		$handle = fopen ($_FILES['srcfileent']['tmp_name'], "r");
		while ($line = fgets($handle, 4096))
		{
			$content = explode('|',$line);
			$to_unset = count($content)-1;
			unset($content[$to_unset]);

			// Ligne de description de la structure du fichier
			if(!count($fields)) $fields = array_flip($content);
			// Ligne d'utilisateur à aujouter
			else
			{
				// On vérifie que la première ligne du fichier est bien
				// la ligne de description de la structure du fichier
				if(array_key_exists('intitule',$fields))
				{
					// On supprime les espaces en trop
					foreach($content as $key => $value)
					{
						$content[$key] = trim($value);
					}

					// On vérifie que l'e login et le mot de passe ne sont'intitule n'est pas vides
					if($content[$fields['intitule']] != '')
					{

						//on enregistre les valeurs dans une table temporaire
						$new_ent = new tiers_import();
						$new_ent->init_description();

						$new_ent->fields['date_creation']		= date("YmdHis");
						$new_ent->fields['date_maj']			= $new_ent->fields['date_creation'];
						$new_ent->fields['intitule']			= trim($content[$fields['intitule']]);
						$new_ent->fields['intitule_search']		= strtoupper($new_ent->fields['intitule']);
						if(isset($fields['adresse']))				$new_ent->fields['adresse']					= trim($content[$fields['adresse']]);
						if(isset($fields['codepostal']))			$new_ent->fields['codepostal']				= trim($content[$fields['codepostal']]);
						if(isset($fields['ville']))					$new_ent->fields['ville']					= trim($content[$fields['ville']]);
						if(isset($fields['telephone']))				$new_ent->fields['telephone']				= trim($content[$fields['telephone']]);
						if(isset($fields['telecopie']))				$new_ent->fields['telecopie']				= trim($content[$fields['telecopie']]);
						if(isset($fields['mel']))					$new_ent->fields['mel']						= trim($content[$fields['mel']]);
						if(isset($fields['ent_capital']))			$new_ent->fields['ent_capital']				= trim($content[$fields['ent_capital']]);
						if(isset($fields['ent_activiteprincipale']))$new_ent->fields['ent_activiteprincipale']	= trim($content[$fields['ent_activiteprincipale']]);
						if(isset($fields['ent_effectif']))			$new_ent->fields['ent_effectif']			= trim($content[$fields['ent_effectif']]);
						if(isset($fields['ent_datecreation']))		$new_ent->fields['ent_datecreation']		= trim($content[$fields['ent_datecreation']]);
						if(isset($fields['pays']))					$new_ent->fields['pays']					= trim($content[$fields['pays']]);
						if(isset($fields['ent_codenace']))			$new_ent->fields['ent_codenace']			= trim($content[$fields['ent_codenace']]);
						if(isset($fields['site_web']))				$new_ent->fields['site_web']				= trim($content[$fields['site_web']]);
						if(isset($fields['dirigeant']))				$new_ent->fields['dirigeant']				= trim($content[$fields['dirigeant']]);
						if(isset($fields['presentation']))			$new_ent->fields['presentation']			= trim($content[$fields['presentation']]);

						// On vérifie que l'intitule n'existe pas
						$sql = "SELECT * FROM dims_mod_business_tiers WHERE intitule LIKE :intitule '";
						$res=$db->query($sql, array(
							':intitule' => $content[$fields['intitule']]."%"
						));

						// Si il n'existe pas
						if(!$db->numrows())
						{
							//on met le champ exist a 0 pour indiquer qu'il s'agit d'une nouvelle entite
							$new_ent->fields['exist'] = "0";
							$cpt_not_exist++;
						}
						else
						{
							//si une ou plusieurs entreprises correspondent, on enregistre leurs id dans le champ "exist"
							$ent_exist = "";
							while($tab_ent_exist = $db->fetchrow($res)) {
								$ent_exist .= $tab_ent_exist['id'].';';
							}
							$new_ent->fields['exist'] = $ent_exist;
							$cpt_exist++;
						}

						$new_ent->save();
						$num++;
					}
					else
					{
						if(!isset($errors['no_intit'])) $errors['no_intit'] = '';
						$errors['no_intit'] .= $num."; ";
						$num++;
						$cpt_err_intit++;
					}
				}
				elseif(array_key_exists('CC_ID_COMPANY',$fields)) {
					// On supprime les espaces en trop
					foreach($content as $key => $value)
					{
						$content[$key] = trim($value);
					}

					// On vérifie que l'e login et le mot de passe ne sont'intitule n'est pas vides
					if(isset($content[$fields['CC_NAME']]) && $content[$fields['CC_NAME']] != '')
					{

						//on enregistre les valeurs dans une table temporaire
						$new_ent = new tiers_import();
						$new_ent->init_description();

						$new_ent->fields['date_creation']		= date("YmdHis");
						$new_ent->fields['date_maj']			= $new_ent->fields['date_creation'];
						$new_ent->fields['intitule']			= trim(utf8_decode($content[$fields['CC_NAME']]));
						$new_ent->fields['intitule_search']		= strtoupper($new_ent->fields['intitule']);
						if(isset($content[$fields['CC_ADDRESS']]))			$new_ent->fields['adresse']					= trim(utf8_decode($content[$fields['CC_ADDRESS']]));
						if(isset($content[$fields['CC_POSTAL_CODE']]))		$new_ent->fields['codepostal']				= trim($content[$fields['CC_POSTAL_CODE']]);
						if(isset($content[$fields['CC_CITY']]))				$new_ent->fields['ville']					= trim($content[$fields['CC_CITY']]);
						if(isset($content[$fields['CC_PHONE']]))				$new_ent->fields['telephone']				= trim($content[$fields['CC_PHONE']]);
						if(isset($content[$fields['CC_FAX']]))				$new_ent->fields['telecopie']				= trim($content[$fields['CC_FAX']]);
						if(isset($content[$fields['CC_EMAIL']]))				$new_ent->fields['mel']						= trim($content[$fields['CC_EMAIL']]);
						if(isset($content[$fields['CC_CAPITAL']]))			$new_ent->fields['ent_capital']				= trim($content[$fields['CC_CAPITAL']]);
						//if(isset($fields['ent_activiteprincipale']))$new_ent->fields['ent_activiteprincipale']	= trim($content[$fields['ent_activiteprincipale']]);
						if(isset($content[$fields['CC_EFFECTIVE']]))		$new_ent->fields['ent_effectif']			= trim($content[$fields['CC_EFFECTIVE']]);
						if(!empty($id_creator))								$new_ent->fields['id_ct_create']			= $id_creator;

						if(isset($content[$fields['CC_FOUNDATION']])) {
							$dattostr = '';
							$dattostr .= $content[$fields['CC_FOUNDATION']];
							$lg = strlen($dattostr);
							if($lg <= 8) {
								switch($lg) {
									case 4 :
										$dat = $content[$fields['CC_FOUNDATION']]."0101000000";
										break;
									case 6 :
										$dat = $content[$fields['CC_FOUNDATION']]."01000000";
										break;
									case 8 :
										$dat = $content[$fields['CC_FOUNDATION']]."000000";
										break;
									default :
										$dat = '';
										break;
								}
							}
							$new_ent->fields['ent_datecreation']		= $dat;
						}
						//if(isset($fields['pays']))					$new_ent->fields['pays']					= trim($content[$fields['pays']]);
						if(isset($content[$fields['CC_ID_NACE']])) {
							$nace = substr($content[$fields['CC_ID_NACE']], 0, 2);
							$sql_nace = "SELECT phpvalue FROM dims_constant WHERE phpvalue LIKE :phpvalue LIMIT 0,1";
							$res_nace = $db->query($sql_nace, array(
								'phpvalue' => '_DIMS_NACE__'.$nace
							));
							$code_nace = $db->fetchrow($res_nace);
							$new_ent->fields['ent_codenace'] = $code_nace['phpvalue'];
						}
						if(isset($content[$fields['CC_URL']]))					$new_ent->fields['site_web']				= trim($content[$fields['CC_URL']]);
						//if(isset($fields['dirigeant']))				$new_ent->fields['dirigeant']				= trim($content[$fields['dirigeant']]);
						if(isset($content[$fields['CC_COMPANY_DESCRIPTION_FR']]))	$new_ent->fields['presentation']			= utf8_decode($content[$fields['CC_COMPANY_DESCRIPTION_FR']]);
						//$new_ent->fields['inactif'] = 0;
						// On vérifie que l'intitule n'existe pas
						$name_to_test = addslashes($content[$fields['CC_NAME']]);
						$sql = "SELECT * FROM dims_mod_business_tiers WHERE intitule LIKE :intitule ";
						$res=$db->query($sql, array(
							':intitule' => $name_to_test."%"
						));

						// Si il n'existe pas
						if(!$db->numrows())
						{
							//on met le champ exist a 0 pour indiquer qu'il s'agit d'une nouvelle entite
							$new_ent->fields['exist'] = "0";
							$cpt_not_exist++;
						}
						else
						{
							//si une ou plusieurs entreprises correspondent, on enregistre leurs id dans le champ "exist"
							$ent_exist = "";
							while($tab_ent_exist = $db->fetchrow($res)) {
								$ent_exist .= $tab_ent_exist['id'].';';
							}
							$new_ent->fields['exist'] = $ent_exist;
							$cpt_exist++;
						}

						$new_ent->save();
						$num++;
					}
					else
					{
						if(!isset($errors['no_intit'])) $errors['no_intit'] = '';
						if($num != 1) {
							$errors['no_intit'] .= $num."; ";
							$cpt_err_intit++;
						}
						$num++;

					}
				}
				elseif(array_key_exists('Societe',$fields)) {
					// On supprime les espaces en trop
					foreach($content as $key => $value)
					{
						$content[$key] = trim($value);
					}

					// On vérifie que l'e login et le mot de passe ne sont'intitule n'est pas vides
					if(isset($content[$fields['Societe']]) && $content[$fields['Societe']] != '')
					{

						//on enregistre les valeurs dans une table temporaire
						$new_ent = new tiers_import();
						$new_ent->init_description();

						$new_ent->fields['date_creation']				= date("YmdHis");
						$new_ent->fields['date_maj']					= $new_ent->fields['date_creation'];
						$new_ent->fields['intitule']					= trim(utf8_decode($content[$fields['Societe']]));
						$new_ent->fields['intitule_search']				= strtoupper($new_ent->fields['intitule']);
						if(isset($content[$fields['Adresse']]))			$new_ent->fields['adresse']					= trim(utf8_decode($content[$fields['Adresse']]));
						if(isset($content[$fields['Adresse_2']]))		$new_ent->fields['adresse']					.= " ".trim(utf8_decode($content[$fields['Adresse_2']]));
						if(isset($content[$fields['C_P']]))				$new_ent->fields['codepostal']				= trim($content[$fields['C_P']]);
						if(isset($content[$fields['Localite']]))		$new_ent->fields['ville']					= trim($content[$fields['Localite']]);
						if(isset($content[$fields['Pays']]))			$new_ent->fields['pays']					= trim($content[$fields['Pays']]);
						if(isset($content[$fields['Tel']]))				$new_ent->fields['telephone']				= trim($content[$fields['Tel']]);
						if(isset($content[$fields['Fax']]))				$new_ent->fields['telecopie']				= trim($content[$fields['Fax']]);
						//if(isset($content[$fields['CC_EMAIL']]))		$new_ent->fields['mel']						= trim($content[$fields['CC_EMAIL']]);
						if(isset($content[$fields['Cap_Social']]))		$new_ent->fields['ent_capital']				= trim($content[$fields['Cap_Social']]);
						//if(isset($fields['ent_activiteprincipale']))	$new_ent->fields['ent_activiteprincipale']	= trim($content[$fields['ent_activiteprincipale']]);
						if(isset($content[$fields['Effectif_Pour_Tri']])) $new_ent->fields['ent_effectif']			= trim($content[$fields['Effectif_Pour_Tri']]);
						if(!empty($id_creator))								$new_ent->fields['id_ct_create']			= $id_creator;
						if(isset($content[$fields['Fondation']])) {
							$dattostr = '';
							$dattostr .= $content[$fields['Fondation']];
							$lg = strlen($dattostr);
							$dat = '';
							if($lg <= 8) {
								switch($lg) {
									case 4 :
										$dat = $content[$fields['Fondation']]."0101000000";
										break;
									case 6 :
										$dat = $content[$fields['Fondation']]."01000000";
										break;
									case 8 :
										$dat = $content[$fields['Fondation']]."000000";
										break;
									default :
										$dat = '';
										break;
								}
							}
							$new_ent->fields['ent_datecreation']		= $dat;
						}
						if(isset($content[$fields['Site_Web']]))			$new_ent->fields['site_web']				= trim($content[$fields['Site_Web']]);
						if(isset($content[$fields['Dirigeant']]))			$new_ent->fields['dirigeant']				= utf8_decode(trim($content[$fields['Dirigeant']]));
						if(isset($content[$fields['Divers']]))				$new_ent->fields['presentation']			= utf8_decode($content[$fields['Divers']]);
						// On vérifie que l'intitule n'existe pas
						$name_to_test = addslashes($content[$fields['Societe']]);
						$sql = "SELECT * FROM dims_mod_business_tiers WHERE intitule LIKE :intitule ";
						$res=$db->query($sql, array(
							':intitule' => $name_to_test."%"
						));

						// Si il n'existe pas
						if(!$db->numrows())
						{
							//on met le champ exist a 0 pour indiquer qu'il s'agit d'une nouvelle entite
							$new_ent->fields['exist'] = "0";
							$cpt_not_exist++;
						}
						else
						{
							//si une ou plusieurs entreprises correspondent, on enregistre leurs id dans le champ "exist"
							$ent_exist = "";
							while($tab_ent_exist = $db->fetchrow($res)) {
								$ent_exist .= $tab_ent_exist['id'].';';
							}
							$new_ent->fields['exist'] = $ent_exist;
							$cpt_exist++;
						}

						$new_ent->save();
						$num++;
					}
					else
					{
						if(!isset($errors['no_intit'])) $errors['no_intit'] = '';
						if($num != 1) {
							$errors['no_intit'] .= $num."; ";
							$cpt_err_intit++;
						}
						$num++;

					}
				}
///////////////// IMPORT DES CONTACTS ////////////////////////////
				elseif(array_key_exists('FirstName',$fields)) {
					$type_imp = 'ct';
					// On supprime les espaces en trop
					foreach($content as $key => $value)
					{
						$content[$key] = trim($value);
					}

					// On vérifie que l'e login et le mot de passe ne sont'intitule n'est pas vides
					if(isset($content[$fields['FirstName']]) && $content[$fields['FirstName']] != '')
					{

						//on enregistre les valeurs dans une table temporaire
						$new_ct = new contact_import();
						$new_ct->init_description();

						$new_ct->fields['firstname']														= trim(utf8_decode($content[$fields['FirstName']]));
						if(isset($content[$fields['LastName']]))			$new_ct->fields['lastname']		= trim(utf8_decode($content[$fields['LastName']]));
						if(isset($content[$fields['Company']]))				$new_ct->fields['company']		= trim(utf8_decode($content[$fields['Company']]));
						if(isset($content[$fields['JobTitle']]))			$new_ct->fields['jobtitle']		= trim($content[$fields['JobTitle']]);
						if(isset($content[$fields['BusinessStreet']]))		$new_ct->fields['address']		= trim($content[$fields['BusinessStreet']]);
						if(isset($content[$fields['BusinessCity']]))		$new_ct->fields['city']			= trim($content[$fields['BusinessCity']]);
						if(isset($content[$fields['BusinessPostalCode']]))	$new_ct->fields['cp']			= trim($content[$fields['BusinessPostalCode']]);
						if(isset($content[$fields['BusinessCountryRegion']])) $new_ct->fields['country']	= trim($content[$fields['BusinessCountryRegion']]);
						if(isset($content[$fields['BusinessFax']]))			$new_ct->fields['fax']			= trim($content[$fields['BusinessFax']]);
						if(isset($content[$fields['BusinessPhone']]))		$new_ct->fields['phone']		= trim($content[$fields['BusinessPhone']]);
						if(isset($content[$fields['MobilePhone']]))			$new_ct->fields['mobile']		= trim($content[$fields['MobilePhone']]);
						if(isset($content[$fields['EmailAddress']]))		$new_ct->fields['email']		= trim($content[$fields['EmailAddress']]);
						if(isset($content[$fields['Title']]))				$new_ct->fields['titre']		= trim($content[$fields['Title']]);
						if(!empty($id_creator))								$new_ct->fields['id_ct_create'] = $id_creator;

						// On test l'existance du contact et de l'entreprise rattachée
						$name_to_test = addslashes(trim(utf8_decode($content[$fields['LastName']])));
						$fname_to_test = addslashes(trim(utf8_decode($content[$fields['FirstName']])));
						$intitule_to_test = addslashes(trim(utf8_decode($content[$fields['Company']])));

						$sql_ct = "SELECT id FROM dims_mod_business_contact WHERE lastname LIKE :lastname AND firstname LIKE :firstname ";
						$res_ct = $db->query($sql_ct, array(
							':lastname'		=> $name_to_test,
							':firstname'	=> $fname_to_test
						));

						$sql_ent = "SELECT id FROM dims_mod_business_tiers WHERE intitule LIKE :intitule ";
						$res_ent = $db->query($sql_ent, array(
							':intitule' => $intitule_to_test
						));

						// Si le contact n'existe pas
						if(!$db->numrows($res_ct))
						{
							$new_ct->fields['exist'] = "0";
							//si l'entreprise n'existe pas dans l'import
							if($new_ct->fields['company'] == '') {
								$new_ct->fields['exist_ent'] = "-1";
								$cpt_new_ct_no_ent++;
							}
							elseif(!$db->numrows($res_ent)) {
								//si l'entreprise n'existe pas dans la base
								$new_ct->fields['exist_ent'] = "0";
								$cpt_not_exist++;
							}
							else {
								$tab_ent = $db->fetchrow($res_ent);
								$new_ct->fields['exist_ent'] = $tab_ent['id'];
								$cpt_not_exist_ct++;
							}
						}
						else
						{
							//on enregistre la correspondance contact
							$tab_ct = $db->fetchrow($res_ct);
							$new_ct->fields['exist'] = $tab_ct['id'];

							//si l'entreprise n'existe pas dans l'import
							if($new_ct->fields['company'] == '') {
								$new_ct->fields['exist_ent'] = "-1";
								$cpt_old_ct_no_ent++;
							}
							elseif(!$db->numrows($res_ent)) {
								//si l'entreprise n'existe pas dans la base
								$new_ct->fields['exist_ent'] = "0";
								$cpt_not_exist_ent++;
							}
							else {
								$tab_ent = $db->fetchrow($res_ent);
								$new_ct->fields['exist_ent'] = $tab_ent['id'];
								$cpt_all_exist++;
							}
						}

						$new_ct->save();
						$num++;
					}
					else
					{
						if(!isset($errors['no_intit'])) $errors['no_intit'] = '';
						if($num != 1) {
							$errors['no_intit'] .= $num."; ";
							$cpt_err_intit++;
						}
						$num++;

					}
				}
				else
				{
					$errors['Attention'] = $_DIMS['cste']['_LABEL_ADMIN_IMPORT_ERROR'];
				}
			}
		}
		fclose ($handle);
	}
}
else {
	//on est dans le cas ou on revient sur l'import
	switch($op) {
		case 'import_ent' :
			$sql = "SELECT * FROM dims_mod_business_tiers_import";
			$res = $db->query($sql);
			while($tab_res = $db->fetchrow($res)) {
				$num++;
				if($tab_res['exist'] != 0) $cpt_exist++;
				if($tab_res['exist'] == 0) $cpt_not_exist++;
			}
			break;
	}
}


// Affichage du récapitulatif et des erreurs
if($type_imp == 'ent') {
	echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_ADMIN_IMPORT_ENT'],'width:50%;float:left;clear:none;','','');
	$affichage = '';
	$affichage .= '<table cellpadding="2" cellspacing="1" width="100%">';

	if(!empty($errors['Attention'])) {
		$affichage .= '<tr><td width="100%" align="center">'.$errors['Attention'].'</td></tr>';
	}
	else {
		//on presente un rapport pour l'import
		//nombre de lignes traitees
		//nombre de nouvelle fiches
		//nombre de doublons
		//nombre d'erreurs (intitule invalide)
		$num--;
		$affichage .= '	<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NBLINES'].' : </td>
							<td width="20%" align="left">'.$num.'</td>
							<td width="40%" align="left"></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NEW_ENT'].' : </td>
							<td width="20%" align="left">'.$cpt_not_exist.'</td>
							<td width="40%" align="left"><a href="javascript:void(0);" onclick="javascript:view_news();"><img src="./common/img/view.png"/></a></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_DOUBLE'].' : </td>
							<td width="20%" align="left">'.$cpt_exist.'</td>
							<td width="40%" align="left"><a href="javascript:void(0);" onclick="javascript:view_old();"><img src="./common/img/view.png"/></a></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NB_ERROR'].' : </td>
							<td width="20%" align="left">'.$cpt_err_intit.'</td>
							<td width="40%" align="left">';
		if($cpt_err_intit > 0) {
			$affichage .= '		<a onclick="javascript:view_errors();"><img src="./common/img/view.png"/></a>';
		}
		$affichage .= '		</td>
						</tr>';
	}

	$affichage .= '</table>';

	echo $affichage;
	echo $skin->close_simplebloc();
	echo '<div id="new_ent_import" style="width:50%;float:left;display:none;"></div>';
	echo '<div id="old_ent_import" style="width:50%;float:left;display:none;"></div>';
	echo '<div id="probs_ent_import" style="width:50%;float:left;display:none;">';
	echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_ADMIN_IMPORT_LINERROR'],'width:100%;float:left;clear:none;','','');
	echo $errors['no_intit'];
	echo $skin->close_simplebloc();
	echo '</div>';
}
else {
	echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_ADMIN_IMPORT_CT'],'width:50%;float:left;clear:none;','','');
	$affichage = '';
	$affichage .= '<table cellpadding="2" cellspacing="1" width="100%">';

	if(!empty($errors['Attention'])) {
		$affichage .= '<tr><td width="100%" align="center">'.$errors['Attention'].'</td></tr>';
	}
	else {
		//on presente un rapport pour l'import
		//nombre de lignes traitees
		//nombre de nouvelle fiches (pers + ent) -> $cpt_not_exist
		//nombre de doublons sur les personnes -> $cpt_not_exist_ent
		//nombre de doublons sur les entreprise -> $cpt_not_exist_ct
		//nombre de doublons sur pers + ent -> $cpt_all_exist
		//nombre d'erreurs (prenom invalide) -> $cpt_err_intit
		//nombre de nouveaux contacts sans entreprise -> $cpt_new_ct_no_ent
		//nombre de doublons sur les contact n'ayant pas d'entreprise -> $cpt_old_ct_no_ent = 0;
		$num--;
		$affichage .= '	<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NBLINES'].' : </td>
							<td width="20%" align="left">'.$num.'</td>
							<td width="40%" align="left"></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NEW_CTANDENT'].' : </td>
							<td width="20%" align="left">'.$cpt_not_exist.'</td>
							<td width="40%" align="left"><a href="javascript:void(0);" onclick="javascript:view_ct_ent();"><img src="./common/img/view.png"/></a></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NEW_CT_OLDENT'].' : </td>
							<td width="20%" align="left">'.$cpt_not_exist_ct.'</td>
							<td width="40%" align="left"><a href="javascript:void(0);" onclick="javascript:view_ct_oldent();"><img src="./common/img/view.png"/></a></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NEW_ENT_OLDCT'].' : </td>
							<td width="20%" align="left">'.$cpt_not_exist_ent.'</td>
							<td width="40%" align="left"><a href="javascript:void(0);" onclick="javascript:view_ent_oldct();"><img src="./common/img/view.png"/></a></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NEW_OLDCTENT'].' : </td>
							<td width="20%" align="left">'.$cpt_all_exist.'</td>
							<td width="40%" align="left"><a href="javascript:void(0);" onclick="javascript:view_oldct_ent();"><img src="./common/img/view.png"/></a></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NEW_CT_SS_ENT'].' : </td>
							<td width="20%" align="left">'.$cpt_new_ct_no_ent.'</td>
							<td width="40%" align="left"><a href="javascript:void(0);" onclick="javascript:view_ct_ss_ent();"><img src="./common/img/view.png"/></a></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_OLD_CT_SS_ENT'].' : </td>
							<td width="20%" align="left">'.$cpt_old_ct_no_ent.'</td>
							<td width="40%" align="left"><a href="javascript:void(0);" onclick="javascript:view_oldct_ss_ent();"><img src="./common/img/view.png"/></a></td>
						</tr>
						<tr>
							<td width="40%" align="right">'.$_DIMS['cste']['_LABEL_ADMIN_NB_ERROR'].' : </td>
							<td width="20%" align="left">'.$cpt_err_intit.'</td>
							<td width="40%" align="left">';
		if($cpt_err_intit > 0) {
			$affichage .= '		<a onclick="javascript:view_errors_ct();"><img src="./common/img/view.png"/></a>';
		}
		$affichage .= '		</td>
						</tr>';
	}

	$affichage .= '</table>';

	echo $affichage;
	echo $skin->close_simplebloc();
	echo '<div id="new_ct_ent_import" style="width:50%;float:left;display:none;"></div>';
	echo '<div id="ent_old_ct_import" style="width:50%;float:left;display:none;"></div>';
	echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_ADMIN_IMPORT_LINERROR'],'width:100%;float:left;clear:none;','','');
	echo $errors['no_intit'];
	echo $skin->close_simplebloc();
	echo '</div>';
}
?>
