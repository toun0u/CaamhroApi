<?php
$vcard = dims_load_securvalue('vcard',dims_const::_DIMS_NUM_INPUT,true,true);
$id_mail = dims_load_securvalue('mail_id', dims_const::_DIMS_NUM_INPUT, true, true);
$id_doc = dims_load_securvalue('doc_id', dims_const::_DIMS_NUM_INPUT, true, true);
$contact_id = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true, true);

$sql = '' ;
    if (isset($contact['nom']) && isset($contact['prenom'])){
//correspondances exactes
	$sql = 'SELECT	*
			FROM	`dims_mod_business_contact`
			WHERE	`lastname` LIKE :nom
			AND	`firstname` LIKE :prenom ';

	$res = $db->query($sql, array(
		':nom' => $contact['nom'],
		':prenom' => $contact['prenom']
	));

	$nb_res = $db->numrows($res);

	$class = 'trl2';

	if($nb_res > 0) {

		print ("<b>$nb_res ".$_DIMS['cste']['_DIMS_LABEL_LOOKLIKE_PROFIL']." : </b><br>");
		print ("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">");
		print ('	<tr>');
		print ("		<td width=\"48%\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</td>");
		print ("		<td width=\"47%\">".$_DIMS['cste']['_DIMS_LABEL_FIRSTNAME']."</td>");
		print ("		<td width=\"5%\"></td>");
		print ("	</tr>");

		while($result = $db->fetchrow($res)) {

		    $onclick = "javascript: location.href='".$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=4&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc."&id_profil=".$result['id']."&vcard=".$vcard."'";

		    print ('	<tr class="'.$class.'" onclick="'.$onclick.'">');
		    print (		"<td>".$result['lastname']."</td>");
		    print (		"<td>".$result['firstname']."</td>");
			print (		"<td width=\"5%\"><img src=\"./common/img/view.png\"/></td>");
		    print ("	</tr>");
		    $class = ($class == 'trl1') ? 'trl2' : 'trl1';
		}

		print ("</table>");
		//on ne met pas de lien si la correspondance est exacte pour éviter les doublons
		//print ('<p><a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=create_ct_fromvcard&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc."&id_profil=".$result['id'].'&vcard='.$vcard.'">'.$_DIMS['cste']['_DIMS_LABEL_VCARD_TO_CONTACT'].'</a></p>');
	}else {
// levenshtein

	    $tab_corresp = array();

	    $sql = 'SELECT
			ct.id as id_contact,
			ct.lastname,
			ct.firstname
		    FROM
			dims_mod_business_contact ct';

	    $ress = $db->query($sql);

	    if($db->numrows($ress) > 0) {
		$nom    = strtoupper($contact['nom']);
		$prenom = strtoupper($contact['prenom']);

		while($rslt = $db->fetchrow($ress)) {

		    $lev_nom = 0;
		    $lev_pre = 0;

		    $coef_nom = 0;
		    $coef_pre = 0;

		    $coef_tot = 0;

		    $lev_nom = levenshtein($nom, strtoupper($rslt['lastname']));
		    $coef_nom = $lev_nom - (ceil(strlen($nom)/4));

		    $lev_pre = levenshtein($prenom, strtoupper($rslt['firstname']));
		    $coef_pre = $lev_pre - (ceil(strlen($prenom)/4));

		    $coef_tot = $coef_nom + $coef_pre;

		    $lev_nom2 = 0;
		    $lev_pre2 = 0;

		    $coef_nom2 = 0;
		    $coef_pre2 = 0;

		    $coef_tot2 = 0;

		    $lev_nom2 = levenshtein($nom, strtoupper($rslt['firstname']));
		    $coef_nom2 = $lev_nom2 - (ceil(strlen($nom)/4));

		    $lev_pre2 = levenshtein($prenom, strtoupper($rslt['lastname']));
		    $coef_pre2 = $lev_pre2 - (ceil(strlen($prenom)/4));

		    $coef_tot2 = $coef_nom2 + $coef_pre2;

		    if($coef_tot < 4 || $coef_tot2 < 4) {
			$tab_corresp[$rslt['id_contact']]['coef']       = $coef_tot;
			$tab_corresp[$rslt['id_contact']]['id_contact'] = $rslt['id_contact'];
			$tab_corresp[$rslt['id_contact']]['lastname']   = $rslt['lastname'];
			$tab_corresp[$rslt['id_contact']]['firstname']  = $rslt['firstname'];
		    }

		}
		sort($tab_corresp);
	    }

	    $nb_corresp = count($tab_corresp);

	    if ($nb_corresp > 0) {

		print ("<b>$nb_corresp {$_DIMS['cste']['_DIMS_LABEL_LOOKLIKE_PROFIL']} : </b><br>");
		print ("<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
		print ('	<tr>');
		print ("		<td width=\"48%\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</td>");
		print ("		<td width=\"47%\">".$_DIMS['cste']['_DIMS_LABEL_FIRSTNAME']."</td>");
		print ("		<td width=\"5%\"></td>");
		print ("	</tr>");

		foreach ($tab_corresp as $corresp){

		    $onclick = "javascript: location.href='".$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=4&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc."&id_profil=".$corresp['id_contact']."&vcard=".$vcard."'";

		    print ('	<tr class="'.$class.'" onclick="'.$onclick.'">');
		    print (		"<td>".$corresp['lastname']."</td>");
		    print (		"<td>".$corresp['firstname']."</td>");
		    print (		"<td width=\"5%\"><img src=\"./common/img/view.png\"/></td>");
			print ("	</tr>");
		    $class = ($class == 'trl1') ? 'trl2' : 'trl1';
		}

		print ("</table>");
		print ('<p><a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=create_ct_fromvcard&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc.'&vcard='.$vcard.'">'.$_DIMS['cste']['_DIMS_LABEL_VCARD_TO_CONTACT'].'</a></p>');
	    }else{
			print ('<p><a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=create_ct_fromvcard&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc.'&vcard='.$vcard.'">'.$_DIMS['cste']['_DIMS_LABEL_VCARD_TO_CONTACT'].'</a></p>');

	    }

	}

    }


    $id_profil = dims_load_securvalue('id_profil',dims_const::_DIMS_NUM_INPUT,true,true);

    if(!empty($id_profil) && $subaction == dims_const::_DIMS_MENU_IMPORT_VCF) {
	//$mail = $tab_infmail[$mail_id];

	$ct_comp = new contact();
	$ct_comp->open($id_profil);

	//on va chercher le layer correspondant
	$lay_ct = new contact_layer();
	$lay_ct->open($id_profil,1,$_SESSION['dims']['workspaceid']);

	//r�cup�ration des informations sur l'employeur
	$sql_ct = "	SELECT 	id, id_tiers, function
				FROM 	dims_mod_business_tiers_contact
				WHERE 	id_contact = :idcontact
				AND 	type_lien LIKE :typelien
				AND 	date_fin = 0";

	$res_ct = $db->query($sql_ct, array(
		':idcontact' 	=> $ct_comp->fields['id'],
		':typelien' 	=> $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']
	));
	$tab_lemp = $db->fetchrow($res_ct);

	$societe = false ;
	if(!empty($tab_lemp)) {
		$tiers = new tiers();
		$tiers->open($tab_lemp['id_tiers']);
		$societe = true ;

		$lay_tiers = new tiers_layer();
		$lay_tiers->open($tab_lemp['id_tiers'],1,$_SESSION['dims']['workspaceid']);
	}

	// filtre sur les champs importants
	$lstfield=$ct_comp->getDynamicFields();

	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_VCF_FUSION'], 'width:100%');

	echo '<form method="post" action="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF.'&do=maj_contact&vcard='.$vcard.'&doc_id='.$id_doc.'&contact_id='.$contact_id.'">';
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("id_contact",	$id_profil);
	$token->field("id_tiers",	$tab_lemp['id_tiers']);
	echo '<input type="hidden" name="id_contact" value="'.$id_profil.'"/>';
	echo '<input type="hidden" name="id_tiers" value="'.$tab_lemp['id_tiers'].'"/>';

	echo '<table id="table_vcard" width="100%" cellpadding="0" cellspacing="0" border="0">';
	// informations sur le contact à mettre à jour

	// gestion du rowspan de l'image
	$rowspan = 1 ;

	// contact rattaché à une société
	if ($societe)
	    $rowspan ++ ;

	// image

	$row_image = 10+$rowspan;

	echo	'<tr>';
	echo		'<td width="20%" rowspan="2" align="right" class="td_vcard2">';
	if($ct_comp->fields['photo'] != ''){
	    echo			'<img src="'._DIMS_WEBPATHDATA.'photo_cts/contact_'.$id_profil.'/photo60'.$ct_comp->fields['photo'].'.png"/>';
	}else {
	    echo			'<img src="./common/img/photo_user.png"/>';
	}
	echo		'</td>';

	echo		'<td class="td_vcard1">';

	echo 			'<div class="div_name">';
	// prénom & nom
	echo				$ct_comp->fields['firstname']." ".$ct_comp->fields['lastname'];
	echo 			'</div>';
	echo		'</td>';
	echo		'<td class="td_vcard1">';
	if (isset($contact['photo']))
	    echo			'<input type="checkbox" value="unchecked" name="image"> '.$_DIMS['cste']['_LABEL_IMPORT_IMAGE'];
		$token->field("image");
	echo		'</td>';
	echo	'</tr>';

	// société
	echo	'<tr style="vertical-align:top">';
	echo		'<td class="td_vcard2">';
	echo			'<div class="div_orga">'.$tiers->fields['intitule'].'</div>';
	echo		'</td>';
	echo		'<td class="td_vcard2">';
	if (isset($contact['org']))
	echo			'<input type="checkbox" value="unchecked" name="societe">'; //a voir s'il faut le laisser ...
	$token->field("societe");
	echo		'</td>';
	echo	'</tr>';

	// url
	if ($societe){
	    /*$url_colspan = count($contact['url']);
	    echo	'<tr>';
	    echo		'<td colspan="'.$url_colspan.'" width="20%" align="right" class="td_vcard2">';
	    echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_WEB_PAGE'].'</div>';
	    echo		'</td>';

	    $td_vcard = "td_vcard1";
	    foreach ($contact['url'] as $key => $value) {
		$num = $key+1 ;
		if ($num >= count ($contact['url'])){
		    $td_vcard = "td_vcard2";
		}
		echo		'<td class="'.$td_vcard.'">';
		echo			$value;
		echo		'</td>';
	    }
	    echo	'</tr>';*/

	    echo	'<tr>';
	    echo		'<td width="20%" align="right" class="td_vcard2">';
	    echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_WEB_PAGE'].'</div>';
	    echo		'</td>';
	    echo		'<td class="td_vcard2">';
						if($tiers->fields['site_web'] != '') echo $tiers->fields['site_web'];
						else echo $lay_tiers->fields['site_web'];
	    echo		'</td>';
	    echo		'<td class="td_vcard2">';
	    if (isset($contact['url']))
		echo			'<input type="checkbox" value="unchecked" name="web_page">';
		$token->field("web_page");
	    echo		'</td>';
	    echo	'</tr>';
	}

	// téléphones

	if (isset($contact['tel'])){
	    $select_tel = '<option value="unchanged"></option>';
	    foreach ($contact['tel'] as $type => $tel){
		if ($type == "work"){
		    $select_tel .= '<option value="work">'.$tel.'</option>';
		}elseif ($type == "home"){
		    $select_tel .= '<option value="home">'.$tel.'</option>';
		}elseif ($type == "cell"){
		    $select_tel .= '<option value="cell">'.$tel.'</option>';
		}

	    }
	}

	echo	'<tr>';
	echo		'<td width="20%" align="right" class="td_vcard1">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_TEL_WORK'].'</div>';
	echo		'</td>';
	echo		'<td class="td_vcard1">';
					if($ct_comp->fields['phone'] != '') echo $ct_comp->fields['phone'];
						else echo $lay_ct->fields['phone'];
	echo		'</td>';
	echo		'<td class="td_vcard1">';
	if (isset($contact['tel'])){
	    echo			'<select name="tel1">';
	    echo				$select_tel;
	    echo			'</select>';
		$token->field("tel1");
	}
	echo		'</td>';
	echo	'</tr>';
	echo	'<tr>';
	echo		'<td width="20%" align="right" class="td_vcard1">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_TEL_DOMICILE'].'</div>';
	echo		'</td>';
	echo		'<td class="td_vcard1">';
					if($ct_comp->fields['pers_phone'] != '') echo $ct_comp->fields['pers_phone'];
					else echo $lay_ct->fields['pers_phone'];
	echo		'</td>';
	echo		'<td class="td_vcard1">';
	if (isset($contact['tel'])){
	    echo			'<select name="tel2">';
	    echo				$select_tel;
	    echo			'</select>';
		$token->field("tel2");
	}
	echo		'</td>';
	echo	'</tr>';
	echo	'<tr>';
	echo		'<td width="20%" align="right" class="td_vcard1">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_MOBILE'].'</div>';
	echo		'</td>';
	echo		'<td class="td_vcard1">';
					if($ct_comp->fields['mobile'] != '') echo $ct_comp->fields['mobile'];
					else echo $lay_ct->fields['mobile'];
	echo		'</td>';
	echo		'<td class="td_vcard1">';
	if (isset($contact['tel'])){
	    echo			'<select name="tel3">';
	    echo				$select_tel;
	    echo			'</select>';
		$token->field("tel3");
	}
	echo		'</td>';
	echo	'</tr>';
	echo	'<tr>';
	echo		'<td width="20%" align="right" class="td_vcard2">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'</div>';
	echo		'</td>';
	echo		'<td class="td_vcard2">';
					if($ct_comp->fields['phone2'] != '') echo $ct_comp->fields['phone2'];
					else echo $lay_ct->fields['phone2'];
	echo		'</td>';
	echo		'<td class="td_vcard2">';
	if (isset($contact['tel'])){
	    echo			'<select name="tel4">';
	    echo				$select_tel;
	    echo			'</select>';
		$token->field("tel4");
	}
	echo		'</td>';
	echo	'</tr>';


	// Emails
	if (isset($contact['email'])){
	    $select_email = '<option value="unchanged"></option>';
	    foreach ($contact['email'] as $key => $val){
		    $select_email .= '<option value="'.$val.'">'.$val.'</option>';
	    }
	}

	echo	'<tr>';
	echo		'<td width="20%" align="right" class="td_vcard1">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].' 1</div>';
	echo		'</td>';
	echo		'<td class="td_vcard1">';
					if($ct_comp->fields['email'] != '') echo $ct_comp->fields['email'];
					else echo $lay_ct->fields['email'];
	echo		'</td>';
	echo		'<td class="td_vcard1">';
	if (isset($contact['email'])){
	    echo			'<select name="email1">';
	    echo				$select_email;
	    echo			'</select>';
		$token->field("email1");
	}
	echo		'</td>';
	echo	'</tr>';
	echo	'<tr>';
	echo		'<td width="20%" align="right" class="td_vcard1">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].' 2</div>';
	echo		'</td>';
	echo		'<td class="td_vcard1">';
					if($ct_comp->fields['email2'] != '') echo $ct_comp->fields['email2'];
					else echo $lay_ct->fields['email2'];
	echo		'</td>';
	echo		'<td class="td_vcard1">';
	if (isset($contact['email'])){
	    echo			'<select name="email2">';
	    echo				$select_email;
	    echo			'</select>';
		$token->field("email2");
	}
	echo		'</td>';
	echo	'</tr>';
	echo	'<tr>';
	echo		'<td width="20%" align="right" class="td_vcard2">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].' 3</div>';
	echo		'</td>';
	echo		'<td class="td_vcard2">';
					if($ct_comp->fields['email3'] != '') echo $ct_comp->fields['email3'];
					else echo $lay_ct->fields['email3'];
	echo		'</td>';
	echo		'<td class="td_vcard2">';
	if (isset($contact['email'])){
	    echo			'<select name="email3">';
	    echo				$select_email;
	    echo			'</select>';
		$token->field("email3");
	}
	echo		'</td>';
	echo	'</tr>';

	// adresses
	echo	'<tr style="vertical-align:top">';
	echo		'<td width="20%" align="right" class="td_vcard1">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_ADR_HOME'];
	echo		'</td>';
	echo		'<td class="td_vcard1">';
		if($ct_comp->fields['adresse'] != '') {
			echo			$ct_comp->fields['address']."<br>";
			echo			$ct_comp->fields['postalcode']." ".$ct_comp->fields['city']."<br>";
			echo			$ct_comp->fields['country'];
		}
		else {
			echo			$lay_ct->fields['address']."<br>";
			echo			$lay_ct->fields['postalcode']." ".$lay_ct->fields['city']."<br>";
			echo			$lay_ct->fields['country'];
		}
	echo		'</td>';
	echo		'<td class="td_vcard1">';
	if (isset($contact['adr']['home']))
	    echo			'<input type="checkbox" value="unchecked" name="adr_home">';
		$token->field("adr_home");
	echo		'</td>';
	echo	'</tr>';
	echo	'<tr style="vertical-align:top">';
	echo		'<td width="20%" align="right" class="td_vcard2">';
	echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_WORK'];
	echo		'</td>';
	echo		'<td class="td_vcard2">';
	if ($societe){
		if($tiers->fields['adresse'] != '') {
			echo			$tiers->fields['adresse']."<br>";
			echo			$tiers->fields['codepostal']." ".$tiers->fields['ville']."<br>";
			echo			$tiers->fields['pays'];
		}
		else {
			echo			$lay_tiers->fields['adresse']."<br>";
			echo			$lay_tiers->fields['codepostal']." ".$lay_tiers->fields['ville']."<br>";
			echo			$lay_tiers->fields['pays'];
		}

	}
	echo		'</td>';
	echo		'<td class="td_vcard2">';
	if (isset($contact['adr']['work']))
	    echo			'<input type="checkbox" value="unchecked" name="adr_work">';
		$token->field("adr_work");
	echo		'</td>';
	echo	'</tr>';

	echo '</table>';
	echo	'<p align="right"><input type="submit" value="Envoyer"></p>';
	$tokenHTML = $token->generate();
	echo $tokenHTML;
	echo '</form>';

	//dims_print_r($ct_comp) ;
	//dims_print_r($tiers);

	echo $skin->close_simplebloc();
    }
?>
