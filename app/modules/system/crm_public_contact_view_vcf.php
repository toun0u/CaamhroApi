<?php

$id_mail = dims_load_securvalue('mail_id', dims_const::_DIMS_NUM_INPUT, true, true);
$id_doc = dims_load_securvalue('doc_id', dims_const::_DIMS_NUM_INPUT, true, true);
$contact_id = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true, true);
$vcard = dims_load_securvalue('vcard',dims_const::_DIMS_NUM_INPUT,true,true);
$type_import = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true);
$id_profil = dims_load_securvalue('id_profil',dims_const::_DIMS_CHAR_INPUT,true,true);

$doc = new docfile();
$doc->open($id_doc);
$contacts = $doc->getParseVcf();
$nb_vcards = count($contacts);
$num_vcard = 0 ;

if (!empty($vcard)){
    $num_vcard = $vcard ;
}

if (count($contacts) > $num_vcard){

    $contact = $contacts[$num_vcard] ;

//foreach ($contacts as $contact){
    //dims_print_r($contact);

    ?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
	    <td style="width:47%;vertical-align:top;">
    <?php

    echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_IMPORT'], 'width:100%');

    echo '<table id="table_vcard" width="100%" cellpadding="0" cellspacing="0" border="0">';
    echo	'<tr>';
    // informations sur la personne issue de l'import
    /*echo		'<td align="center" colspan="2" class="td_vcard2">';
    echo			$_DIMS['cste']['_DIMS_TITLE_INF_PERS_IMP'];
    echo		'</td>';
    echo	'</tr>';*/

    // gestion du rowspan de l'image
    $rowspan = 0 ;
    if (isset($contact['title']) || isset($contact['nom']))
	$rowspan ++ ;

    if (isset($contact['org']))
	$rowspan ++ ;

    // image
    echo	'<tr>';
    echo		'<td width="20%" rowspan="'.$rowspan.'" align="right" class="td_vcard2">';
    if(isset($contact['photo'])){
	echo			'<img src="'.$contact['photo'].'"/>';
    }else {
	echo			'<img src="./common/img/photo_user.png"/>';
    }
    echo		'</td>';

    $td_vcard = "td_vcard1";
    if(!isset($contact['org']))
	$td_vcard = "td_vcard2";
    echo		'<td class="'.$td_vcard.'">';

    echo '<div class="div_name">';

     // prenom
    if (isset($contact['prenom']))
	echo			$contact['prenom']." ";

    // nom
    if (isset($contact['nom']))
	echo			$contact['nom'];

    echo '</div>';
    echo		'</td>';
    echo	'</tr>';

    // société
    if (isset($contact['org'])){
	echo	'<tr>';
	echo		'<td class="td_vcard2">';
	echo			$contact['org'];
	echo		'</td>';
	echo	'</tr>';
    }

    // url
    if (isset($contact['url'])){

	$url_colspan = count($contact['url']);
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
	echo	'</tr>';
    }

    // téléphone
    if (isset($contact['tel'])){
	$td_vcard = "td_vcard1";
	$nb_tel = 1 ;
	foreach ($contact['tel'] as $type => $tel){
	    if ($nb_tel<count($contact['tel'])){
		$nb_tel ++;
	    }else {
		$td_vcard = "td_vcard2";
	    }

	    if ($type == "work"){
		echo	'<tr>';
		echo		'<td width="20%" align="right" class="'.$td_vcard.'">';
		echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_TEL_WORK'].'</div>';
		echo		'</td>';
		echo		'<td class="'.$td_vcard.'">';
		echo			$tel;
		echo		'</td>';
		echo	'</tr>';
	    }elseif ($type == "home"){
		echo	'<tr>';
		echo		'<td width="20%" align="right" class="'.$td_vcard.'">';
		echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_TEL_DOMICILE'].'</div>';
		echo		'</td>';
		echo		'<td class="'.$td_vcard.'">';
		echo			$tel;
		echo		'</td>';
		echo	'</tr>';
	    }elseif ($type == "cell"){
		echo	'<tr>';
		echo		'<td width="20%" align="right" class="'.$td_vcard.'">';
		echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_MOBILE'].'</div>';
		echo		'</td>';
		echo		'<td class="'.$td_vcard.'">';
		echo			$tel;
		echo		'</td>';
		echo	'</tr>';
	    }

	}
    }

    // Email
    if (isset($contact['email'])){
	$td_vcard = "td_vcard1";
	foreach($contact['email'] as $key => $value){
	    $num = $key+1 ;
	    if ($num >= count ($contact['email'])){
		$td_vcard = "td_vcard2";
	    }
	    echo	'<tr>';
	    echo		'<td width="20%" align="right" class="'.$td_vcard.'">';
	    echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL']." ".$num.'</div>';
	    echo		'</td>';
	    echo		'<td class="'.$td_vcard.'">';
	    echo			$value;
	    echo		'</td>';
	    echo	'</tr>';
	}
    }

    // adresse
    if (isset($contact['adr'])){
	$td_vcard = "td_vcard1";
	$nb_adr = 1 ;
	foreach($contact['adr'] as $key => $value){

	    if ($nb_adr<count($contact['adr'])){
		$nb_adr ++;
	    }else {
		$td_vcard = "td_vcard2";
	    }

	    if ($key == "home"){
		echo	'<tr style="vertical-align:top">';
		echo		'<td width="20%" align="right" class="'.$td_vcard.'">';
		echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_ADR_HOME'];
		echo		'</td>';
		echo		'<td class="'.$td_vcard.'">';
		echo			$value['rue']."<br>";
		echo			$value['cp']." ".$value['city']."<br>";
		echo			$value['pays'];
		echo		'</td>';
		echo	'</tr>';

	    }if ($key == "work"){
		echo	'<tr style="vertical-align:top">';
		echo		'<td width="20%" align="right" class="'.$td_vcard.'">';
		echo			'<div class="div_intitule">'.$_DIMS['cste']['_DIMS_LABEL_WORK'];
		echo		'</td>';
		echo		'<td class="'.$td_vcard.'">';
		echo			$value['rue']."<br>";
		echo			$value['cp']." ".$value['city']."<br>";
		echo			$value['pays'];
		echo		'</td>';
		echo	'</tr>';
	    }
	}
    }


    echo '</table>';

    //dims_print_r($contact);

    echo $skin->close_simplebloc();

    ?>
	    </td>
	    <td style="width:53%;vertical-align:top;">
    <?php

    echo $skin->open_simplebloc($_DIMS['cste']['_PROFIL'], 'width:100%');

	if($type_import == '')
		include DIMS_APP_PATH."./common/modules/system/crm_public_contact_compare_vcf.php";
	elseif($type_import == 'tiers')
		include DIMS_APP_PATH."/modules/system/crm_public_ent_compare_vcf.php";

    echo $skin->close_simplebloc();

    ?>
		</td>
	    </tr>
	</table>
    <?php

    // flèches précédent / suivant

    /*echo '<table width="100%">';
    echo	'<tr>';
    echo		'<td align="left">';
    if ($num_vcard > 0){
	$prev_vcard = $num_vcard -1 ;
	echo			'<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&contact_id=".$contact_id."&mail_id=".$id_mail.'&doc_id='.$id_doc.'&vcard='.$prev_vcard.'" style="text-decoration:none;"><img src="./common/img/calendar/prev.gif" alt="'.$_DIMS['cste']['_DIMS_LABEL_PREVIOUS_VCARD'].'" title="'.$_DIMS['cste']['_DIMS_LABEL_PREVIOUS_VCARD'].'"/></a>';
    }
    echo		'</td>';
    echo		'<td align="right">';
    if ($num_vcard < count($contacts)-1){
	$next_vcard = $num_vcard +1 ;
	echo			'<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&contact_id=".$contact_id."&mail_id=".$id_mail.'&doc_id='.$id_doc.'&vcard='.$next_vcard.'" style="text-decoration:none;"><img src="./common/img/calendar/next.gif" alt="'.$_DIMS['cste']['_DIMS_LABEL_NEXT_VCARD'].'" title="'.$_DIMS['cste']['_DIMS_LABEL_NEXT_VCARD'].'"/></a>';
    }
    echo		'</td>';
    echo	'</tr>';
    echo '</table>';*/

}


?>
