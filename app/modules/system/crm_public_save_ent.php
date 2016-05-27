<?php

require_once(DIMS_APP_PATH . "/modules/system/class_tiersfield.php");

$entr = new tiers($db);

if (isset($_SESSION['business']['ent_id'])) {
	$entr->open($_SESSION['business']['ent_id']);

	///chargement des valeurs courantes pour ce user
	$entfields=array();
	$sql= " select		tf.*
			from		dims_mod_business_tiers_field as tf
			WHERE		id_tiers=".$entr->fields['id']."
			AND			id_workspace=".$_SESSION['dims']['workspaceid']."
			AND			id_user=".$_SESSION['dims']['userid']."
			AND			lastmodify=1";

	$res=$db->query($sql);

	if ($db->numrows($res)>0) {
		while ($f=$db->fetchrow($res)) {
			// test si champ existe
			$entfields[$f['id_metafield']]=$f['value'];
		}
	}

	//selection des champs utiles pour les historiques dans la metabase
	$res=$db->query("select * from dims_mb_field where tablename LIKE 'dims_mod_business_tiers' and name!='id_user' and name!='id'");

	$tab_mbf = array();
	while($tab = $db->fetchrow($res)) {
		$tab_mbf[$tab['name']] = $tab;
	}

	$fields = dims_load_securvalue($_POST, dims_const::_DIMS_CHAR_INPUT, true, true, true);
	foreach($fields as $field=>$value) {
		if (substr($field,0,4)=="ent_") {
			// on test si chgt
			$chp=substr($field,4);
			if ($entr->fields[$chp]!=$value) {
				// on a un changement de valeur
				$entr->updateFieldLog($chp,$value,$tab_mbf);
			}
		}
	}
}

// on recupere toutes les infos du contact
$id_from = dims_load_securvalue('id_from', dims_const::_DIMS_NUM_INPUT, true, true);
$type_from = dims_load_securvalue('type_from', dims_const::_DIMS_CHAR_INPUT, true, true);
$type_to = dims_load_securvalue('type_to', dims_const::_DIMS_CHAR_INPUT, true, true);
$date_creation = dims_load_securvalue('date_creation', dims_const::_DIMS_CHAR_INPUT, true, true);
$tmp_date = explode('/',$date_creation);
$datetosave = $tmp_date[2].$tmp_date[1].$tmp_date[0].'000000';

if(is_numeric($datetosave))
	$entr->fields['ent_datecreation'] = $datetosave;
else
	$entr->fields['ent_datecreation'] = null;

$entr->setvalues($_POST,"ent_");

// affectation du contexte de dims
$entr->setugm();

// on sauvegarde maintenant le contact
$entr->save();
$id_ent = $entr->fields['id'];
$_SESSION['business']['ent_id']=$id_ent;

//enregistrement de la photo
if(isset($_FILES['photo'])) {
	require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_add_photo.php');
}
// on regarde maintenant les champs dynamiques pour les récupérer
$lstdynfield=$entr->getDynamicFields();

// lecture des fieldnames
foreach($lstdynfield as $fd) {
	$fieldok = false;
	$value="";
	$chcour='field'.$fd['id'];
	$chdest='field'.$fd['fieldname'];
	if (isset($_POST[$chcour]) && $_POST[$chcour] != "") {
		$fieldok = true;
		if (is_array($_POST[$chcour]) ) {
			$champs = dims_load_securvalue($chcour, dims_const::_DIMS_CHAR_INPUT, true, true, true);
			foreach($champs as $val) {
				if ($value != '') $value .= '||';
				$value .= $val;
			}
		}
		else $value = dims_load_securvalue($chcour, dims_const::_DIMS_CHAR_INPUT, true, true, true);
	}
	if ($fieldok) {

		if (!isset($_POST['priv_'.$fd['id']])) $private=false;
		else $private=true;

		$oldprivate=false;
		$id_lasttiersfield=0;
		// on recherche si on a un tiersfield pesonnel pour lui auquel cas on ouvre
		$res=$db->query("select id,private from dims_mod_business_tiers_field where id_metafield=".$fd['id']." and id_user=".$_SESSION['dims']['userid']." and id_workspace=".$_SESSION['dims']['workspaceid']." and private = 1");

		if ($db->numrows($res)>0) {
			while ($fentfield=$db->fetchrow($res)) {
				$id_lasttiersfield=$fentfield['id'];
				// on ouvre pour modifier la ligne existante
				$oldprivate=$fentfield['private'];
			}
		}

		// on a qq chose de rempli
		if (!isset($entfields[$fd['id']]) || $value!=$entfields[$fd['id']] || $private!=$oldprivate) {
			// on insert la nouvelle valeur pour ce user
			$entfield = new tiersfield();
			// on log les chgts
			//dims_create_user_action_log(_SYSTEM_ACTION_MODIFYENT, $chp, 1, 1, $entr->fields['id'], dims_const::_SYSTEM_OBJECT_TIERS);
			// on update la table pour mettre à zero la valeur des champs anciens
			// on met a zero que si la nouvelle valeur n'est pas privée

			if (!$oldprivate && !$private) {
				$db->query("update dims_mod_business_tiers_field set lastmodify=0 where id_metafield=".$fd['id']." and id_user=".$_SESSION['dims']['userid']." and id_workspace=".$_SESSION['dims']['workspaceid']);
			} else {
				// on ouvre car on a soit une valeur de privée vers public si $id_lastcontactfield>0
				// ou nouvelle creation d'un enregistrement privé, on ne touche pas le statut du dernier lastmodify publique
				if ($id_lasttiersfield>0) $ctfield->open($id_lastcontactfield);
			}

			$entfield->fields['id_tiers'] = $_SESSION['business']['ent_id'];
			$entfield->fields['id_metafield'] = $fd['id'];
			$entfield->fields['id_module'] =dims_const::_DIMS_MODULE_SYSTEM;
			$entfield->fields['id_user'] = $_SESSION['dims']['userid'];
			$entfield->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$entfield->fields['lastmodify'] = 1;
			$entfield->fields['timestp_modify'] = dims_createtimestamp();
			$entfield->fields['id_lang'] = $_SESSION['dims']['currentlang'];

			$entfield->fields['value'] = $value;
			if (isset($_POST['priv_'.$fd['id']])) $entfield->fields['private'] =1;
			else $entfield->fields['private'] = 0;

			$entfield->save();
		}
	}
}

if(!empty($id_from)) {

	switch($type_from) {
		case 'cte':
			$sql_insert = "INSERT INTO `dims_mod_business_tiers_contact` (
							`id_tiers` ,
							`id_contact` ,
							`type_lien` ,
							`id_workspace` ,
							`id_user`,
							`date_create`,
							`link_level`,
							`id_ct_user_create`
							)
							VALUES (
							'".$id_ent."',
							'".$id_from."',
							'',
							".$_SESSION['dims']['workspaceid'].",
							".$_SESSION['dims']['userid'].",
							".date("YmdHis").",
							1,
							".$_SESSION['dims']['user']['id_contact']."
							);";
			$db->query($sql_insert);

			//eventuellement redirection
			break;
		case 'ent':
			$sql_ins = "INSERT INTO `dims_mod_business_ct_link` (
							`id_contact1` ,
							`id_contact2` ,
							`id_object` ,
							`type_link` ,
							`link_level` ,
							`time_create` ,
							`id_ct_user_create` ,
							`date_deb` ,
							`date_fin` ,
							`id_workspace` ,
							`id_user` ,
							`commentaire`
							)
							VALUES (
							'".$id_from."',
							'".$id_ent."',
							'".dims_const::_SYSTEM_OBJECT_TIERS."',
							'',
							'1',
							'".date('YmdHis')."',
							'".$_SESSION['dims']['user']['id_contact']."',
							'',
							'',
							".$_SESSION['dims']['workspaceid'].",
							".$_SESSION['dims']['userid'].",
							''
							);
						";
			$db->query($sql_ins);
			break;
	}
}

dims_redirect("$tabscriptenv?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_FORM."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$_SESSION['business']['ent_id']);

?>
