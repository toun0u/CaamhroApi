<script type="text/javascript">
	function appelFonctions(clef){
	changeCateg(clef);
	changeNewChamp(clef);
	changeType(clef);
	changeFormat(clef);
	}

	function changeCateg(clef){
	var value=document.getElementById("lienfield_"+clef).value ;
	dims_xmlhttprequest_todiv("admin-light.php","dims_op=adminChangeCateg&val="+value+"&row="+clef,"","div_cat"+clef);

	}
	function changeNewChamp(clef){
	var value=document.getElementById("lienfield_"+clef).value ;
	dims_xmlhttprequest_todiv("admin-light.php","dims_op=adminChangeNewChamp&val="+value+"&row="+clef,"","div_new_champ"+clef);
	}
	function changeType(clef){
	var value=document.getElementById("lienfield_"+clef).value ;
	dims_xmlhttprequest_todiv("admin-light.php","dims_op=adminChangeType&val="+value+"&row="+clef,"","div_type"+clef);
	}
	function changeFormat(clef){
	var value=document.getElementById("lienfield_"+clef).value ;
	dims_xmlhttprequest_todiv("admin-light.php","dims_op=adminChangeFormat&val="+value+"&row="+clef,"","div_format"+clef);
	}
</script>

<?php

if (isset($_POST['firstdataline'])){
	$_SESSION['dims']['importform']['firstdataline'] = dims_load_securvalue('firstdataline', dims_const::_DIMS_CHAR_INPUT, true, true, true);
	unset($_SESSION['dims']['importform']['generic']);
	unset($_SESSION['dims']['importform']['typecol']);
	unset($_SESSION['dims']['importform']['formatcol']);
}

require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_fct.php');

echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_IMPORT'],'width:100%;float:left;clear:none;','','');

echo '<form name="import_form_contact" style="margin:0;" action="'.$scriptenv.'?op=import_contact3" method="post" enctype="multipart/form-data">';
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("firstdataline");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '<div class="dims_form" style="float:left;width:100%;">';
echo	'<p>';
echo		"<label>Ligne contenant les noms des champs</label>";
echo		'<span style="text-align:left;">';
echo			'<select id="firstdataline" name="firstdataline" onChange="javascript:document.import_form_contact.submit()">';
for ($i=1;$i<= $_SESSION['dims']['importform']['nbrow'];$i++){
	if ($_SESSION['dims']['importform']['firstdataline'] == $i)
	$sel = " selected";
	else $sel = "";
	echo			'<option'.$sel.' value="'.$i.'">Ligne '.$i.'</option>';
}
echo			'</select>';
echo		'</span>';
echo	'</p>';

echo '</div>';
echo '</form>';

// recherche de champs génériques éventuels
// TODO : séparer tiers et contacts
// TODO : prendre dans convmeta les champs dynamique déjà créé pour cet utilisateur
if (!isset($_SESSION['dims']['importform']['generic'])) {
	if ($_SESSION['dims']['importform']['object_id']==dims_const::_SYSTEM_OBJECT_CONTACT) {
	foreach($_SESSION['dims']['importform']['data'][$_SESSION['dims']['importform']['firstdataline']] as $key => $value){

		$value = strtolower($value);
		$value = dims_convertaccents($value);

		switch($value){
		case "firstname":
		case "prenom":
		case "prénom":
		case "first name":
		case "name":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['firstname'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['lastname']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "lastname":
		case "nom":
		case "last name":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['lastname'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['lastname']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "email":
		case "email address":
		case "e-mail address":
		case "courriel":
		case "emailaddress":
		case "mail":
		case "adressedemessagerie":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['email'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['email']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "email2":
		case "email2address":
		case "e-mail address 2":
		case "courriel2":
		case "mail2":
		case "adressedemessagerie2":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['email2'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['email2']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "email3":
		case "email3address":
		case "e-mail address 3":
		case "courriel3":
		case "mail3":
		case "adressedemessagerie3":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['email3'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['email3']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "telephone":
		case "fixe":
		case "phone":
		case "telephone fixe":
		case "telephone domicile":
		case "personal phone":
		case "domicile":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['phone'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['phone']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;



		case "code postal":
		case "codepostal":
		case "postal code":
		case "postalcode":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['postalcode'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['postalcode']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "city":
		case "localite":
		case "ville":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['city'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['city']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "rue":
		case "street":
		case "adresse":
		case "address":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['address'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['address']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "paysregion":
		case "country/region":
		case "countryregion":
		case "déprégion":
		case "pays":
		case "country":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['country'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['country']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "job title":
		case "profession":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['professional'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['professional']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "mobile phone":
		case "mobilephone":
		case "telmobile":
		case "carphone":
		case "portable" :
		case "mobile" :
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['mobile'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['mobile']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "telephone":
		case "téléphone":
		case "phone":
		case "personal phone":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['phone'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['phone']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "telephone2":
		case "téléphone2":
		case "phone2":
		case "personal phone2":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['phone2'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['phone2']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "telecopie":
		case "télécopie":
		case "fax":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['fax'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['fax']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		}
	}
	}else{

	foreach($_SESSION['dims']['importform']['data'][$_SESSION['dims']['importform']['firstdataline']] as $key => $value){

		$value = strtolower($value);
		$value = dims_convertaccents($value);

		switch($value){

		case "company":
		case "company name":
		case "societe":
		case "société" :
		case "company name":
		case "companyname":
		case "entreprise":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['company'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['company']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "businesspostalcode":
		case "codepostalbureau":
		case "business postal code":
		case "code postal":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['cp'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['cp']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "telephonebureau":
		case "téléphonebureau":
		case "businessphone":
		case "business phone":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['phone'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['phone']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "telephonebureau2":
		case "téléphonebureau2":
		case "businessphone2":
		case "business phone2":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['phone2'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['phone2']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "telecopiebureau":
		case "télécopiebureau":
		case "businessfax":
		case "business fax":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['fax'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['fax']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case 'notes': //Traitement des commentaires
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['comment'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['comment']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "ruebureau":
		case "businessstreet":
		case "business street":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['address'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['address']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "ruebureau2":
		case "businessstreet2":
		case "business street 2":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['address2'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['address2']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "ruebureau3":
		case "businessstreet3":
		case "business street 3":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['address3'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['address3']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "city":
		case "localite":
		case "ville":
		case "businesscity":
		case "business city":
		case "villebureau":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['city'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['city']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;

		case "businesspostalcode":
		case "codepostalbureau":
		case "business postal code":
			$_SESSION['dims']['importform']['generic'][$key] = $convmeta['cp'];
			foreach ($rubgen as $lab => $list){
			if (isset($list['list'][$convmeta['cp']]))
				$_SESSION['dims']['importform']['label'][$key] = $lab;
			}
		break;
		}
	}
	}
}

// format + type
if (!isset($_SESSION['dims']['importform']['formatcol'])) {
	foreach ($_SESSION['dims']['importform']['data'] as $li => $row) {
	if ($li != $_SESSION['dims']['importform']['firstdataline']){
		foreach ($row as $ci => $col) {
		if (is_numeric($col) && !(strpos($col,'/') !== false || strpos($col, '-') !== false || strpos($col, '.') !== false)) {
			if (is_int($col)) {
				if(!isset($_SESSION['dims']['importform']['formatcol'][$ci]) &&
				   $_SESSION['dims']['importform']['formatcol'][$ci] != "date" &&
				   $_SESSION['dims']['importform']['formatcol'][$ci] != "float" &&
				   $_SESSION['dims']['importform']['formatcol'][$ci] != "string")
					$_SESSION['dims']['importform']['formatcol'][$ci]="int";
			}
			else {
				$_SESSION['dims']['importform']['formatcol'][$ci]="float";
			}

		}
		else {
			// date ???
			if ($_SESSION['dims']['importform']['formatcol'][$ci]=="date") {
				$_SESSION['dims']['importform']['formatcol'][$ci]="date";
			}
			else {
				$_SESSION['dims']['importform']['formatcol'][$ci]="string";
			}
		}
		// on définit par défaut le format
		if (!isset($_SESSION['dims']['importform']['typecol'][$ci])) {
			$_SESSION['dims']['importform']['typecol'][$ci]="text";
		}
		}
	}
	}
}

//dims_print_r($_SESSION['dims']['importform']);

// ajouter case dans business_admin.php. Créer le fichier pour l'import ...
echo '<form name="import_save_contact" style="margin:0;" action="'.$scriptenv.'?op=import_save_contact" method="post" enctype="multipart/form-data">';
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
echo '<div class="dims_form" style="float:left;width:100%;">';
echo '<table cellspacing="1" cellpadding="2" width="100%">';
echo	'<tr bgcolor="#f4f4dd">';
echo		'<th class="titre">Title</th>';
echo		'<th class="titre">Lier</th>';
echo		'<th class="titre">Type</th>';
echo		'<th class="titre">Format</th>';
echo		'<th class="titre">Cat&eacute;gorie</th>';
echo		'<th class="titre">Cr&eacute;er un nouveau champ</th>';
echo	'<tr>';

$bgcolor = "#f4f4dd" ;
foreach ($_SESSION['dims']['importform']['data'][$_SESSION['dims']['importform']['firstdataline']] as $key => $value){
	if ($bgcolor == "#f4f4dd")
	$bgcolor = "#ffffff";
	else $bgcolor = "#f4f4dd" ;

	echo '<tr bgcolor="'.$bgcolor.'">';
	echo	'<td>'.$value.'</td>';

	if (isset($_SESSION['dims']['importform']['generic'][$key])){
	foreach ($rubgen as $label) {
		if (isset($label['list'][$_SESSION['dims']['importform']['generic'][$key]])){

		echo '<td><img src="./common/img/checkdo2.png"/></td>';

		echo '<td>';
		echo $field_types[$label['list'][$_SESSION['dims']['importform']['generic'][$key]]['type']];
		echo '</td>';

		switch ($label['list'][$_SESSION['dims']['importform']['generic'][$key]]['format']){
			case 'int' :
			$format = $_DIMS['cste']['_DIMS_LABEL_INT'];
			break ;
			case 'float' :
			$format = $_DIMS['cste']['_DIMS_LABEL_FLOAT'];
			break ;
			case 'date' :
			$format = $_DIMS['cste']['_DIMS_LABEL_DATE'];
			break ;
			case 'string' :
			default :
			$format = $_DIMS['cste']['_DIMS_LABEL_STRING'];
			break ;
		}
		echo	'<td>'.$format.'</td>';
		echo '<td>'.$rubgen[$_SESSION['dims']['importform']['label'][$key]]['label'].'</td>';
		echo '<td><img src="./common/img/clipboard/close.png"/></td>';
		}
	}

	}else{
	// choix du lien
	echo	'<td>';
	echo		'<select name="lienfield_'.$key.'" id="lienfield_'.$key.'" onChange="javascript:appelFonctions(\''.$key.'\');">';
	$token->field("lienfield_".$key);
	echo			'<option value="0">Ne pas lier &agrave; un champ existant</option>';
	foreach ($convmeta as $lien => $id_lien){
		$test = true ;
		foreach ($_SESSION['dims']['importform']['generic'] as $gene){
		if ($id_lien == $gene || strpos($lien, "field") !== false)
			$test = false ;
		}
		if	($test)
		echo		'<option value="'.$id_lien.'">'.$lien.'</option>';
	}
	echo		'</select>';
	echo	'</td>';

	// choix du type
	echo	'<td>';
	echo	'<div id="div_type'.$key.'">';
	echo		'<select name="typefield_'.$key.'" id="typefield_'.$key.'">';
	$token->field("typefield_".$key);
	foreach ($field_types as $t => $v){
		if ($t == $_SESSION['dims']['importform']['typecol'][$key])
		echo		'<option value="'.$t.'" selected>'.$v.'</option>';
		else echo		'<option value="'.$t.'">'.$v.'</option>';
	}
	echo		'</select>';
	echo	'</div></td>';

	// choix du format
	echo	'<td>';
	echo	'<div id="div_format'.$key.'">';
	echo		'<select name="formatfield_'.$key.'" id="formatfield_'.$key.'">';
	$token->field("formatfield_".$key);
	$sel = array(0=>"",1=>"",2=>"",3=>"");
	switch ($_SESSION['dims']['importform']['formatcol'][$key]){
		case 'int' :
		$format = $_DIMS['cste']['_DIMS_LABEL_INT'];
		$sel[0] = " selected";
		break ;
		case 'float' :
		$format = $_DIMS['cste']['_DIMS_LABEL_FLOAT'];
		$sel[1] = " selected";
		break ;
		case 'date' :
		$format = $_DIMS['cste']['_DIMS_DATE'];
		$sel[2] = " selected";
		break ;
		case 'string' :
		default :
		$format = $_DIMS['cste']['_DIMS_LABEL_STRING'];
		$sel[3] = " selected";
		break ;
	}

	echo			'<option value="int"'.$sel[0].'>'.$_DIMS['cste']['_DIMS_LABEL_INT'].'</option>';
	echo			'<option value="float"'.$sel[1].'>'.$_DIMS['cste']['_DIMS_LABEL_FLOAT'].'</option>';
	echo			'<option value="date"'.$sel[2].'>'.$_DIMS['cste']['_DIMS_DATE'].'</option>';
	echo			'<option value="string"'.$sel[3].'>'.$_DIMS['cste']['_DIMS_LABEL_STRING'].'</option>';
	echo		'</select>';
	echo	'</div></td>';

	// choix de la catégorie
	echo	'<td>';
	echo	'<div id="div_cat'.$key.'">';
	echo		'<select name="catfield_'.$key.'" id="catfield_'.$key.'">';
	$token->field("catfield_".$key);
	foreach ($rubgen as $label => $v){
		if (isset($_SESSION['dims']['importform']['label'][$key]) && $_SESSION['dims']['importform']['label'][$key] == $label)
		echo		'<option value="'.$label.'" selected>'.$v['label'].'</option>';
		else
		echo		'<option value="'.$label.'">'.$v['label'].'</option>';
	}
	echo		'</select>';
	echo	'</div></td>';


	echo	'<td><div id="div_new_champ'.$key.'"><input type="checkbox" name="create_'.$key.'"/></div></td>';
	$token->field("create_".$key);
	}
	echo '</tr>';
}

echo '</table>';

echo '</div>';

echo '<div class="dims_form" style="float:left;width:100%;">';
echo	'<div style="clear:both;text-align:right;padding:0px;height:40px;padding-top:10px;">';
echo		dims_create_button($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'],"./common/img/save.gif","javascript:document.import_save_contact.submit();","","");
echo	'</div>';
echo '</div>';
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '</form>';

echo $skin->close_simplebloc();

?>
