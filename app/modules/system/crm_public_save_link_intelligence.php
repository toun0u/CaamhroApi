<?

$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true);
$id_object = dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, true, true);
$ent_from_id = dims_load_securvalue('id_ent_from', dims_const::_DIMS_CHAR_INPUT, true, true);
$pers_from_id = dims_load_securvalue('id_pers_from', dims_const::_DIMS_CHAR_INPUT, true, true);
$to_ent = dims_load_securvalue('tiers_id', dims_const::_DIMS_NUM_INPUT, true, true);
$to_ent_ent = dims_load_securvalue('ent_ent_id', dims_const::_DIMS_NUM_INPUT, true, true);
$to_pers = dims_load_securvalue('pers_id', dims_const::_DIMS_NUM_INPUT, true, true);
$to_ent_pers = dims_load_securvalue('ent_pers_id', dims_const::_DIMS_NUM_INPUT, true, true);
$type_ent = dims_load_securvalue('tiers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
$ent_type_link = dims_load_securvalue('ent_ent_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
$type_pers = dims_load_securvalue('pers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
$type_ent_pers = dims_load_securvalue('ent_pers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
$ent_link_lvl = dims_load_securvalue('tiers_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);
$ent_ent_link_lvl = dims_load_securvalue('ent_ent_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);
$pers_link_lvl = dims_load_securvalue('pers_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);
$ent_pers_link_lvl = dims_load_securvalue('ent_pers_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);
$date_deb_d = dims_load_securvalue('date_deb_day', dims_const::_DIMS_NUM_INPUT, true, true);
$date_deb_m = dims_load_securvalue('date_deb_month', dims_const::_DIMS_NUM_INPUT, true, true);
$date_deb_y = dims_load_securvalue('date_deb_year', dims_const::_DIMS_NUM_INPUT, true, true);
$date_fin_d = dims_load_securvalue('date_fin_day', dims_const::_DIMS_NUM_INPUT, true, true);
$date_fin_m = dims_load_securvalue('date_fin_month', dims_const::_DIMS_NUM_INPUT, true, true);
$date_fin_y = dims_load_securvalue('date_fin_year', dims_const::_DIMS_NUM_INPUT, true, true);
$part = dims_load_securvalue('part', dims_const::_DIMS_NUM_INPUT, true, true);
$date_deb = $date_deb_y.$date_deb_m.$date_deb_d."000000";
$date_fin = $date_fin_y.$date_fin_m.$date_fin_d."000000";
$commentaire = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, true, true);
$fonction = dims_load_securvalue('fonction', dims_const::_DIMS_CHAR_INPUT, true, true);
$departement = dims_load_securvalue('departement', dims_const::_DIMS_CHAR_INPUT, true, true);

if($date_fin == "000000") $date_fin = "";
//dims_print_r($_POST);
//echo $type; die();
switch($type) {
	case "pers" :
		/*
		$sql_insert = "INSERT INTO `dims_mod_business_ct_link` (
							`id_contact1` ,
							`id_contact2` ,
							`id_object` ,
							`type_link` ,
							`link_level`,
							`time_create`,
							`id_ct_user_create`,
							`date_deb`,
							`date_fin`,
							`id_workspace`,
							`id_user`,
							`commentaire`
							)
							VALUES (
							'".$pers_from_id."',
							'".$to_pers."',
							'".$id_object."',
							'".$type_pers."',
							'".$pers_link_lvl."',
							'".date("YmdHis")."',
							'".$_SESSION['dims']['user']['id_contact']."',
							'".$date_deb."',
							'".$date_fin."',
							'".$_SESSION['dims']['workspaceid']."',
							'".$_SESSION['dims']['userid']."',
							'".addslashes($commentaire)."'
							);";

		$db->query($sql_insert);
		*/
		require_once(DIMS_APP_PATH . '/modules/system/class_ct_link.php');
		$ct_link = new ctlink();
		$ct_link->fields['id_contact1']=$pers_from_id;
		$ct_link->fields['id_contact2']=$to_pers;
		$ct_link->fields['id_object']=$id_object;
		$ct_link->fields['type_link']=$type_pers;
		$ct_link->fields['link_level']=$pers_link_lvl;
		$ct_link->fields['time_create']=date("YmdHis");
		$ct_link->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
		$ct_link->fields['date_deb']=$date_deb;
		$ct_link->fields['date_fin']=$date_fin;
		$ct_link->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
		$ct_link->fields['id_user']=$_SESSION['dims']['userid'];
		$ct_link->fields['commentaire']=$commentaire;
		$ct_link->save();

		$c = new contact();
		$c->open($pers_from_id);
		$c->save();

		$c = new contact();
		$c->open($to_pers);
		$c->save();

		dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$pers_from_id);
		break;
	case "tiers" :
	   /* $sql_insert = "INSERT INTO `dims_mod_business_tiers_contact` (
							`id_tiers` ,
							`id_contact` ,
							`type_lien` ,
							`function`,
							`departement`,
							`id_workspace` ,
							`id_user`,
							`date_create`,
							`link_level`,
							`id_ct_user_create`,
							`date_deb`,
							`date_fin`,
							`commentaire`
							)
							VALUES (
							'".$to_ent."',
							'".$pers_from_id."',
							'".$type_ent."',
							'".$fonction."',
							'".$departement."',
							".$_SESSION['dims']['workspaceid']." ,
							".$_SESSION['dims']['userid'].",
							".date("YmdHis").",
							".$ent_link_lvl.",
							".$_SESSION['dims']['user']['id_contact'].",
							'".$date_deb."',
							'".$date_fin."',
							'".addslashes($commentaire)."'
							);";
		$db->query($sql_insert);
		*/

			require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');
			$ct_tiers = new tiersct();
			$ct_tiers->fields['id_tiers']=$to_ent;
			$ct_tiers->fields['id_contact']=$pers_from_id;
			$ct_tiers->fields['type_lien']=$type_ent;
			$ct_tiers->fields['function']=$fonction;
			$ct_tiers->fields['departement']=$departement;
			$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
			$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];
			$ct_tiers->fields['date_create']=date("YmdHis");
			$ct_tiers->fields['link_level']=$ent_link_lvl;
			$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
			$ct_tiers->fields['date_deb']=$date_deb;
			$ct_tiers->fields['date_fin']=$date_fin;
			$ct_tiers->fields['commentaire']=$commentaire;
			$ct_tiers->save();


			// modif de la fiche tiers
			$t = new tiers();
			$t->open($to_ent);
			$t->save();

			$c = new contact();
			$c->open($pers_from_id);
			$c->save();
			//modif de la fiche contact

			dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$pers_from_id);

		break;
	case "ent_ent" :
		$sql_insert = "INSERT INTO `dims_mod_business_ct_link` (
							`id_contact1` ,
							`id_contact2` ,
							`id_object` ,
							`type_link` ,
							`link_level`,
							`time_create`,
							`id_ct_user_create`,
							`date_deb`,
							`date_fin`,
							`id_workspace`,
							`id_user`,
							`commentaire`
							)
							VALUES (
							'".$ent_from_id."',
							'".$to_ent_ent."',
							'".$id_object."',
							'".$ent_type_link."',
							'".$ent_ent_link_lvl."',
							'".date("YmdHis")."',
							'".$_SESSION['dims']['user']['id_contact']."',
							'".$date_deb."',
							'".$date_fin."',
							'".$_SESSION['dims']['workspaceid']."',
							'".$_SESSION['dims']['userid']."',
							'".addslashes($commentaire)."'
							);";

		$t = new tiers();
		$t->open($ent_from_id);
		$t->save();

		$t = new tiers();
		$t->open($to_ent_ent);
		$t->save();

		$db->query($sql_insert);
		dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ent_from_id);
		break;

	case "ent_pers" :
				/*$sql_insert = "INSERT INTO `dims_mod_business_tiers_contact` (
							`id_tiers` ,
							`id_contact` ,
							`type_lien` ,
							`function`,
							`departement`,
							`id_workspace` ,
							`id_user`,
							`date_create`,
							`link_level`,
							`id_ct_user_create`,
							`date_deb`,
							`date_fin`,
							`commentaire`
							)
							VALUES (
							'".$ent_from_id."',
							'".$to_ent_pers."',
							'".$type_ent_pers."',
							'".$fonction."',
							'".$departement."',
							".$_SESSION['dims']['workspaceid']." ,
							".$_SESSION['dims']['userid'].",
							".date("YmdHis").",
							".$ent_pers_link_lvl.",
							".$_SESSION['dims']['user']['id_contact'].",
							'".$date_deb."',
							'".$date_fin."',
							'".addslashes($commentaire)."'
							);";

		$db->query($sql_insert);*/
				require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');
				$ct_tiers = new tiersct();
				$ct_tiers->fields['id_tiers']=$ent_from_id;
				$ct_tiers->fields['id_contact']=$to_ent_pers;
				$ct_tiers->fields['type_lien']=$type_ent_pers;
				$ct_tiers->fields['function']=$fonction;
				$ct_tiers->fields['departement']=$departement;
				$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
				$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];
				$ct_tiers->fields['date_create']=date("YmdHis");
				$ct_tiers->fields['link_level']=$ent_pers_link_lvl;
				$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
				$ct_tiers->fields['date_deb']=$date_deb;
				$ct_tiers->fields['date_fin']=$date_fin;
				$ct_tiers->fields['commentaire']=$commentaire;
				$ct_tiers->save();

				//modif de la fiche contact
		$t = new tiers();
		$t->open($ent_from_id);
		$t->save();

		$c = new contact();
		$c->open($to_ent_pers);
		$c->save();

				dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ent_from_id);

		break;
}

?>
