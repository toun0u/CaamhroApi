<?php
// sauvegarde des champs contacts dynamiques
// sauvegarde des contacts

// test si au moins les champs nom et prénom sont présents
if ($_SESSION['dims']['importform']['object_id']==dims_const::_SYSTEM_OBJECT_CONTACT){
	$test_nom = false ;
	$test_prenom = false ;
	foreach ($_SESSION['dims']['importform']['generic'] as $id_lien){
	if ($id_lien == 54)
		$test_nom = true ;
	if ($id_lien == 53)
		$test_prenom = true ;
	}
	if (!($test_nom && $test_prenom)){
	foreach($_SESSION['dims']['importform']['typecol'] as $key => $v){
		if (isset($_POST['lienfield_'.$key])){
		if ($_POST['lienfield_'.$key] == 54)
			$test_nom = true ;
		if ($_POST['lienfield_'.$key] == 53)
			$test_prenom = true ;
		}
	}
	}
	if (!($test_nom && $test_prenom)){
	unset($_SESSION['dims']['importform']);
	dims_redirect("$scriptenv?cat=0&action=500&part=500");
	}
}else{
	$test_nom = false ;
	foreach ($_SESSION['dims']['importform']['generic'] as $id_lien){
	if ($id_lien == 55)
		$test_nom = true ;
	}
	if (!$test_nom){
	foreach($_SESSION['dims']['importform']['typecol'] as $key => $v){
		if (isset($_POST['lienfield_'.$key])){
		if ($_POST['lienfield_'.$key] == 55)
			$test_nom = true ;
		}
	}
	}
	if (!$test_nom){
	unset($_SESSION['dims']['importform']) ;
	dims_redirect("$scriptenv?cat=0&action=500&part=500");
	}
}

foreach($_SESSION['dims']['importform']['typecol'] as $key => $v){
	if (isset($_POST['lienfield_'.$key])){
	// on affecte les données du post dans la variable de session si $_POST['create_'] == "on" ou $_POST['lienfield_'] != 0 ...
	if ($_POST['create_'.$key] == "on" || $_POST['lienfield_'.$key] != 0){
		$_SESSION['dims']['importform']['generic'][$key] = dims_load_securvalue('lienfield_'.$key, dims_const::_DIMS_CHAR_INPUT, true, true, true);
		if (isset($_POST['formatfield_'.$key])){
		$_SESSION['dims']['importform']['label'][$key] = dims_load_securvalue('catfield_'.$key, dims_const::_DIMS_CHAR_INPUT, true, true, true);
		$_SESSION['dims']['importform']['formatcol'][$key] = dims_load_securvalue('formatfield_'.$key, dims_const::_DIMS_CHAR_INPUT, true, true, true);
		$_SESSION['dims']['importform']['typecol'][$key] = dims_load_securvalue('typefield_'.$key, dims_const::_DIMS_CHAR_INPUT, true, true, true);
		}else{
		require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_fct.php');
		$_SESSION['dims']['importform']['formatcol'][$key] = $rubgen[$_SESSION['dims']['importform']['label'][$key]]['list'][$_SESSION['dims']['importform']['generic'][$key]]['format'] ;
		$_SESSION['dims']['importform']['typecol'][$key] = $rubgen[$_SESSION['dims']['importform']['label'][$key]]['list'][$_SESSION['dims']['importform']['generic'][$key]]['type'] ;
		}
	}else{
		// sinon on supprime toutes les colonnes qui ne seront pas importées
		unset($_SESSION['dims']['importform']['generic'][$key]);
		unset($_SESSION['dims']['importform']['formatcol'][$key]);
		unset($_SESSION['dims']['importform']['typecol'][$key]);
		foreach($_SESSION['dims']['importform']['data'] as $k => $row){
		foreach($row as $clef => $val){
			if ($key == $clef)
			unset($_SESSION['dims']['importform']['data'][$k][$key]);
		}
		}
	}
	}
}

// définition du type date
foreach($_SESSION['dims']['importform']['formatcol'] as $key => $v){
	if($v == 'date'){
	foreach($_SESSION['dims']['importform']['data'] as $clef => $date){
		if ($clef != $_SESSION['dims']['importform']['firstdataline']){
		if (isset($date[$key])){
			echo $date[$key];
			$_SESSION['dims']['importform']['data'][$clef][$key] = date('YmdHis',mktime(0,0,0,1,$date[$key]-1,1900));
			echo ' => '.$_SESSION['dims']['importform']['data'][$clef][$key]."<br>" ;
		}
		}
	}
	}
}

//dims_print_r($_SESSION['dims']['importform']);
//die();

// à partir d'ici toutes les données ont été traitées ... on ne travaillera plus que sur $_SESSION['dims']['importform']
$import_values = $_SESSION['dims']['importform'] ;

require_once(DIMS_APP_PATH . "/modules/system/class_mb_field.php");
$id_newmbfield=0;

$list_dynam = array();

foreach($import_values['generic'] as $colonne => $id_generic){
	if ($id_generic == 0){
	// créer un nouveau champ dynamique
	$metafield = new metafield();

	// position du champ
	$select = " SELECT	max(position) as maxpos
			FROM	dims_mod_business_meta_field
			WHERE	id_object = :idobject
			AND		id_metacateg= :idmetacateg ";

	$res = $db->query($select, array(
		':idobject' 	=> $import_values['object_id'],
		':idmetacateg' 	=> $import_values['label'][$colonne]
	));
	$fields = $db->fetchrow($res);
	$maxpos = $fields['maxpos'];

	if (!is_numeric($maxpos) || $maxpos == 0)
		$maxpos = 0;
	$metafield->fields['position'] = $maxpos+1;

	$ind=1;
	$slots=array();
	$trouve=false;
	// on remplit avec ce qui est pris
	$res=$db->query("SELECT	fieldname
					 FROM	dims_mod_business_meta_field
					 WHERE	id_object = :idobject ", array(
		':idobject' => $import_values['object_id']
	));

	if ($db->numrows($res)>0) {
		while ($f=$db->fetchrow($res)) {
		$slots[$f['fieldname']]=1;
		}
	}else {
		$ind=1; // 1er element
		$trouve=true;
	}
	// on boucle jusqu'a trouver un slot de libre (=non définit)
	while (!$trouve && $ind<=200) {
		if (isset($slots[$ind])) {
		$ind++;
		}else {
		$trouve=true; // on a trouve un slot de dispo
		}
	}

	if ($trouve) {

		// on ajout cette nouvelle référence dans le champ
		$metafield->fields['fieldname'] = $ind;

		if ($import_values['object_id']==dims_const::_SYSTEM_OBJECT_CONTACT) {
		$tablename="dims_mod_business_contact";
		$tablename_layer="dims_mod_business_contact_layer";
		}else {
		$tablename="dims_mod_business_tiers";
		$tablename_layer="dims_mod_business_tiers_layer";
		}

		// check de la colonne si column existe ou non
		$sql = "SELECT	column_name
			FROM	information_schema.columns
			WHERE	TABLE_SCHEMA = :tableschema
			AND		table_name = :tablename
			AND		column_name LIKE :columnname ";

		$res=$db->query($sql, array(
			':tableschema' 	=> _DIMS_DB_DATABASE,
			':tablename' 	=> $tablename,
			':columnname' 	=> "field".$ind
		));

		if ($db->numrows($res)==0) {
		$complet="";
		// on crée la colonne sur contact ou enteprise
		if (isset($slots[$ind+1])) {
			// deux cas, soit on est >1 soit = 1
			if ($ind==1) {
			// on va rechercher le dernier champ générique

			}else {
			$complet=" AFTER field".($ind-1);
			}
		}

		// on ajoute le nouveau champ
		$db->query("ALTER TABLE `".$tablename."` ADD `field".$ind."` VARCHAR( 255 ) NULL ".$complet);
		$db->query("ALTER TABLE `".$tablename_layer."` ADD `field".$ind."` VARCHAR( 255 ) NULL ".$complet);
		}

		// on créé la référence dans la table mb_fields
		$mbf = new mb_field();
		$mbf->fields['tablename']=$tablename;
		$mbf->fields['name']="field".$ind;
		$mbf->fields['label']=$import_values['data'][$import_values['firstdataline']][$colonne];
		$mbf->fields['type']="varchar(255)";
		$mbf->fields['visible']="1";
		$mbf->fields['id_module_type']=1;
		$mbf->fields['id_object']=0;

		// verification de l'indexation
		if (isset($_POST['is_indexed'])) $mbf->fields['indexed']=1;
		else $mbf->fields['indexed']=0;

		$mbf->fields['protected']=0;
		$mbf->save();

		$id_newmbfield=$mbf->fields['id'];

	}else {
		echo "Error no empty space";
	}

	if ($trouve) {
		$ancien_idmetacateg=$metafield->fields['id_metacateg'];
		$ancienne_position=$metafield->fields['position'];

		$metafield->setvalues($_POST,'field_');
		$metafield->fields['name'] = $import_values['data'][$import_values['firstdataline']][$colonne];
		$metafield->fields['type'] = $import_values['typecol'][$colonne];
		$metafield->fields['format'] = $import_values['formatcol'][$colonne];
		$metafield->fields['id_object'] = $import_values['object_id'];
		$metafield->fields['id_metacateg'] = $import_values['label'][$colonne];

		if ($id_newmbfield>0) {
		// ajout de la nouvelle valeur de mbfield pour ce metachamp
		$metafield->fields['id_mbfield'] = $id_newmbfield;
		}

		if ($id_newmbfield==0 && $metafield->fields['id_mbfield']>0) {
		$mbf = new mb_field();
		$mbf->open($metafield->fields['id_mbfield']);
		$mbf->fields['indexed']=isset($_POST['is_indexed']);
		$mbf->save();
		}

		$metafield->fields['option_needed'] = 0;
		$metafield->fields['option_arrayview'] = 0;
		$metafield->fields['option_exportview'] = 0;
		$metafield->fields['option_search'] = 0;
		/*
		if (!isset($field_option_arrayview)) $metafield->fields['option_arrayview'] = 0;
		if (!isset($field_option_exportview)) $metafield->fields['option_exportview'] = 0;
		if (!isset($field_option_cmsgroupby)) $metafield->fields['option_cmsgroupby'] = 0;
		if (!isset($field_option_cmsorderby)) $metafield->fields['option_cmsorderby'] = 0;
		if (!isset($field_option_cmsdisplaylabel)) $metafield->fields['option_cmsdisplaylabel'] = 0;
		if (!isset($field_option_cmsshowfilter)) $metafield->fields['option_cmsshowfilter'] = 0;
		*/

		$metafield->save();
		$list_dynam[$colonne] = $metafield->fields['fieldname'];
	}

	}
}

// on enregistre tous les contacts
require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_fct.php');
if ($import_values['object_id']==dims_const::_SYSTEM_OBJECT_CONTACT) {

	require_once(DIMS_APP_PATH . '/modules/system/class_contact_layer.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_contact_mbfield.php');

	foreach($import_values['data'] as $clef => $data){

	if ($clef > $import_values['firstdataline']){

		$contact = new contact();
		$layer = new contact_layer();
		$layer->init_description();
		$layer->fields['type_layer'] = 1;
		$layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

		$list_mb_field = array();

		foreach ($data as $colonne => $value){
		// go !!!

		if ($import_values['generic'][$colonne] > 0){

			if ($import_values['generic'][$colonne] == 54){ // nom
			$contact->fields['lastname'] = $value;

			}elseif($import_values['generic'][$colonne] == 53){ // prénom
			$contact->fields['firstname'] = $value;

			}else{
			print($convmeta[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['namefield']]);

			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['namefield']]])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['namefield']]] == 0) {
				$contact->fields[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['namefield']] = $value ;
				}else {
				$layer->fields[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['namefield']] = $value ;
				}
			}
			}
		}elseif (isset($list_dynam[$colonne])) {
			$contact->fields['field'.$list_dynam[$colonne]] = $value ;
		}
		}
		$contact->save();
		$layer->fields['id'] = $contact->fields['id'];
		$layer->save();
	}
	}
	// et ici les tiers
}else{
	require_once(DIMS_APP_PATH . '/modules/system/class_tiers_layer.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_tiers_mbfield.php');

	foreach($import_values['data'] as $clef => $data){

	if ($clef > $import_values['firstdataline']){

		$contact = new tiers();
		$layer = new tiers_layer();
		$layer->init_description();
		$layer->fields['type_layer'] = 1;
		$layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

		$list_mb_field = array();

		foreach ($data as $colonne => $value){
		// go !!!

		if ($import_values['generic'][$colonne] > 0){

			if ($import_values['generic'][$colonne] == 55){ // intitulé
			$contact->fields['intitule'] = $value;

			}else{
			if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['name']]])) {
				if($_SESSION['dims']['contact_fields_mode'][$convmeta[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['name']]] == 0) {
				$contact->fields[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['name']] = $value ;
				}else {
				$layer->fields[$rubgen[$import_values['label'][$colonne]]['list'][$import_values['generic'][$colonne]]['name']] = $value ;
				}
			}
			}
		}elseif (isset($list_dynam[$colonne])) {
			$contact->fields['field'.$list_dynam[$colonne]] = $value ;
		}
		}
		$contact->save();
		$layer->fields['id'] = $contact->fields['id'];
		$layer->save();
	}
	}

}
unset ($_SESSION['dims']['importform']);
dims_redirect($scriptenv.'?cat=0&action=500&part=500');


?>
