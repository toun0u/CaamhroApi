<?php
$sql = '' ;
$new_ct_link = dims_load_securvalue('new_ct',dims_const::_DIMS_NUM_INPUT,true,true);
$id_profil = dims_load_securvalue('id_profil',dims_const::_DIMS_NUM_INPUT,true,true);

    if (isset($contact['org']) && $contact['org'] != ''){
//correspondances exactes
	$sql = 'SELECT	*
		FROM	`dims_mod_business_tiers`
		WHERE	`intitule` LIKE :intitule ';

	$res = $db->query($sql, array(
		':intitule' => $contact['org']
	));

	$nb_res = $db->numrows($res);

	$class = 'trl2';

    //on informe que la fiche pour le contact a bien été créée
    if(isset($new_ct_link) && $new_ct_link != 0)
        print ("<b>".$_DIMS['cste']['_DIMS_LABEL_CREATE_CT_OK_TO_ENT']."</b><br>");

    if($nb_res > 0) {

		print ("<b>$nb_res ".$_DIMS['cste']['_DIMS_LABEL_LOOKLIKE_PROFIL']." : </b><br>");
		print ("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">");
		print ('	<tr>');
		print ("		<td width=\"95%\">".$_DIMS['cste']['_DIMS_LABEL_ENTERPRISES']."</td>");
		print ("		<td width=\"5%\"></td>");
		print ("	</tr>");

		while($result = $db->fetchrow($res)) {

		    $onclick = "javascript: location.href='".$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=4&type=tiers&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc."&new_ct=".$new_ct_link."&id_tiers=".$result['id']."&vcard=".$vcard."&id_profil=".$id_profil."'";

		    print ('	<tr class="'.$class.'" onclick="'.$onclick.'">');
		    print (		"<td>".$result['intitule']."</td>");
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
			t.id as id_tiers,
			t.intitule
		    FROM
			dims_mod_business_tiers t';

	    $ress = $db->query($sql);

	    if($db->numrows($ress) > 0) {
		$nom    = strtoupper($contact['org']);

		while($rslt = $db->fetchrow($ress)) {

		    $lev_nom = 0;
		    $lev_pre = 0;

		    $coef_nom = 0;
		    $coef_pre = 0;

		    $coef_tot = 0;

		    $lev_nom = levenshtein($nom, strtoupper($rslt['intitule']));
		    $coef_nom = $lev_nom - (ceil(strlen($nom)/4));

		    $coef_tot = $coef_nom;

		    if($coef_tot < 4) {
			$tab_corresp[$rslt['id_tiers']]['coef']       = $coef_tot;
			$tab_corresp[$rslt['id_tiers']]['id_tiers']   = $rslt['id_tiers'];
			$tab_corresp[$rslt['id_tiers']]['intitule']   = $rslt['intitule'];
		    }

		}
		sort($tab_corresp);
	    }

	    $nb_corresp = count($tab_corresp);

	    if ($nb_corresp > 0) {

		print ("<b>$nb_corresp {$_DIMS['cste']['_DIMS_LABEL_LOOKLIKE_PROFIL']} : </b><br>");
		print ("<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
		print ('	<tr>');
		print ("		<td width=\"48%\">".$_DIMS['cste']['_DIMS_LABEL_ENTERPRISES']."</td>");
		print ("		<td width=\"5%\"></td>");
		print ("	</tr>");

		foreach ($tab_corresp as $corresp){

		    $onclick = "javascript: location.href='".$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=4&type=tiers&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc."&new_ct=".$new_ct_link."&id_tiers=".$corresp['id_tiers'].'&vcard='.$vcard."&id_profil=".$id_profil."'";

		    print ('	<tr class="'.$class.'" onclick="'.$onclick.'">');
		    print (		"<td>".$corresp['intitule']."</td>");
		    print (		"<td width=\"5%\"><img src=\"./common/img/view.png\"/></td>");
			print ("	</tr>");
		    $class = ($class == 'trl1') ? 'trl2' : 'trl1';
		}

		print ("</table>");
		print ('<p><a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=create_tiers_fromvcard&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc."&new_ct=".$new_ct_link."&id_tiers=".$result['id'].'&vcard='.$vcard.'">'.$_DIMS['cste']['_DIMS_LABEL_VCARD_TO_TIERS'].'</a></p>');
	    }else{
			print ('<p><a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=create_tiers_fromvcard&contact_id=".$contact_id."&mail_id=".$id_mail."&doc_id=".$id_doc."&new_ct=".$new_ct_link."&id_tiers=".$result['id'].'&vcard='.$vcard.'">'.$_DIMS['cste']['_DIMS_LABEL_VCARD_TO_TIERS'].'</a></p>');
	    }

	}

    }


    $id_tiers = dims_load_securvalue('id_tiers',dims_const::_DIMS_NUM_INPUT,true,true);

    if(!empty($id_tiers) && $subaction == dims_const::_DIMS_MENU_IMPORT_VCF) {
	//$mail = $tab_infmail[$mail_id];

	$ct_comp = new tiers();
	$ct_comp->open($id_tiers);

	//r�cup�ration des informations sur l'employeur
	$sql_ct = 	"SELECT id, id_tiers, function
				FROM dims_mod_business_tiers_contact
				WHERE id_contact = :idcontact
				AND type_lien LIKE :typelien
				AND date_fin = 0";

	$res_ct = $db->query($sql_ct, array(
		':idcontact'	=> $ct_comp->fields['id'],
		':typelien'		=> $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']
	));
	$tab_lemp = $db->fetchrow($res_ct);
	if(!empty($tab_lemp)) {
		$tiers = new tiers();
		$tiers->open($tab_lemp['id_tiers']);
	}

	// filtre sur les champs importants
	$lstfield=$ct_comp->getDynamicFields();

	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_VCF_FUSION'], 'width:100%'); // changer contante -> entreprise

        echo '<form method="post" action="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF.'&do=maj_tiers&vcard='.$vcard.'&doc_id='.$id_doc.'&id_profil='.$id_profil.'">';
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("id_tiers", $id_tiers);
        $token->field("web_page", );
        $token->field("adresse");
        $tokenHTML = $token->generate();
        echo $tokenHTML;
	    //echo '<input type="hidden" name="id_contact" value="'.$id_profil.'"/>';
	    echo '<input type="hidden" name="id_tiers" value="'.$id_tiers.'"/>';

	    echo '<table id="table_vcard" width="100%" cellpadding="0" cellspacing="0" border="0">';

	    // nom société
	    echo	'<tr>';
	    echo		'<td width="20%" align="right" class="td_vcard2">';
	    echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</div>';
	    echo		'</td>';
	    echo		'<td class="td_vcard2">';
	    echo			$ct_comp->fields['intitule'];
	    echo		'</td>';
	    echo		'<td></td>';
	    echo	'</tr>';

	    // site web
	    echo	'<tr>';
	    echo		'<td width="20%" align="right" class="td_vcard2">';
	    echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_WEB_PAGE'].'</div>';
	    echo		'</td>';
	    echo		'<td class="td_vcard2">';
	    echo 			$ct_comp->fields['site_web'];
	    echo		'</td>';
	    echo		'<td class="td_vcard2">';
	    if (isset($contact['url']))
		echo			'<input type="checkbox" value="unchecked" name="web_page">';
	    echo		'</td>';
	    echo	'</tr>';

	    // adresse

	    echo	'<tr>';
	    echo		'<td width="20%" style="vertical-align:top" align="right" class="td_vcard2">';
	    echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_WORK'].'</div>';
	    echo		'</td>';
	    echo		'<td class="td_vcard2">';
	    echo			$ct_comp->fields['adresse']."<br>";
	    echo			$ct_comp->fields['codepostal']." ".$ct_comp->fields['ville']."<br>";
	    echo			$ct_comp->fields['pays'];
	    echo		'</td>';
	    echo		'<td class="td_vcard2">';
	    if (isset($contact['adr']) && isset($contact['adr']['work']))
			echo			'<input type="checkbox" value="unchecked" name="adresse">';
	    echo		'</td>';
	    echo	'</tr>';


	    echo '</table>';
	    echo	'<p align="right"><input type="submit" value="Envoyer"></p>';
	    echo '</form>';
	echo $skin->close_simplebloc();
		//dims_print_r($ct_comp) ;
    }
?>
