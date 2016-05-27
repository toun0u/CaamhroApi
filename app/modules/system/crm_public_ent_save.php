<?php
require_once(DIMS_APP_PATH . '/modules/system/class_tiers_layer.php');
require_once(DIMS_APP_PATH . "/modules/system/class_tiersfield.php");

$tiers=new tiers($db);

if (isset($_SESSION['business']['tiers_id'])) {
	$tiers->open($_SESSION['business']['tiers_id']);
}
else {
	$tiers->init_description();

	// on r�init la liste du dernier ent saisi
	$_SESSION['business']['lastent']=array();
}

//selection des champs utiles pour les historiques dans la metabase
$tab_mbf = array();
$tab_mbf=$tiers->getMbFields();
$tab_mtf=$tiers->getDynamicFields();
$convmeta=array();
// enregistrement de la conversion
foreach ($tab_mtf as $k=>$fields) {
	$convmeta[$fields['namefield']]=$fields['id'];
}

// construction des deux autres dimensions
$tiersworkspace = new tiers_layer();
$tiersworkspace->init_description();
$updateworkspace=false;
$updatecontact=false;
$tiersuser = new tiers_layer();
$tiersuser->init_description();
$updateuser=false;

if (isset($_SESSION['business']['tiers_id'])) {
	// recherche si layer pour workspace
	$res=$db->query("select id,type_layer,id_layer from dims_mod_business_tiers_layer where id=".$_SESSION['business']['tiers_id']." and type_layer=1 and id_layer=".$_SESSION['dims']['workspaceid']);

	if ($db->numrows($res)) {
		while ($f=$db->fetchrow($res)) {
			$tiersworkspace->open($_SESSION['business']['tiers_id'],1,$_SESSION['dims']['workspaceid']);
		}
	}
	else {
		$tiersworkspace->fields['id']=$_SESSION['business']['tiers_id'];
		$tiersworkspace->fields['type_layer']=1;
		$tiersworkspace->fields['id_layer']=$_SESSION['dims']['workspaceid'];
	}

	// recherche si ligne pour user
	$res=$db->query("select id,type_layer,id_layer from dims_mod_business_tiers_layer where id=".$_SESSION['business']['tiers_id']." and type_layer=2 and id_layer=".$_SESSION['dims']['userid']);

	if ($db->numrows($res)) {
		while ($f=$db->fetchrow($res)) {
			$tiersuser->open($_SESSION['business']['tiers_id'],2,$_SESSION['dims']['userid']);
		}
	}
	else {
		$tiersuser->fields['id']=$_SESSION['business']['tiers_id'];
		$tiersuser->fields['type_layer']=2;
		$tiersuser->fields['id_layer']=$_SESSION['dims']['userid'];
	}
}

$usershare=false;

/* boucle sur l'ensemble des champs recus */
$fields = dims_load_securvalue($_POST, dims_const::_DIMS_CHAR_INPUT, true, true, true);
foreach($fields as $field=>$value) {
	$chp="";
	if (substr($field,0,5)=="field") {
		// on test si chgt
		$id_metafield=substr($field,5);
		// on regarde si on a un champ g�n�rique
		$id_mbfield=$tab_mtf[$id_metafield]['id_mbfield'];

		$chp=$tab_mtf[$id_metafield]['namefield'];

		if ($id_metafield==78) {
			echo $_SESSION['dims']['tiers_fields_view'][$id_metafield]." ";
			echo $id_mbfield. " ".$chp;
		}
	}
	//echo $id_metafield." ".$id_mbfield." ".$chp."<br>";
	if ($chp!="") {

		if (is_array($value)) {
			$value=$value[0];
		}

		// recherche du statut public
		if (!isset($_SESSION['dims']['tiers_fields_view'][$id_metafield]) || $_SESSION['dims']['tiers_fields_view'][$id_metafield]==0
			|| isset($_SESSION['dims']['tiers_fields_mode'][$id_metafield])==0) {
			if ($tiers->fields[$chp]!=$value) {
				// on a un changement de valeur
				$tiers->updateFieldLog($chp,$value,$id_metafield,0,0);
				// update du champ, on n'utilise plus la fonction setvalues
				$tiers->fields[$chp]=$value;
				$updatecontact=true;

				// on g�re les changements de valeurs
				if (isset($_SESSION['dims']['tiers_fields_view_old'][$id_metafield]) && $_SESSION['dims']['tiers_fields_view_old'][$id_metafield]==1) {
					// on a pass� une valeur en espace, plus de private
					$tiersworkspace->fields[$chp]="";
					$updateworkspace=true;
				}

				if (isset($_SESSION['dims']['tiers_fields_view_old'][$id_metafield]) && $_SESSION['dims']['tiers_fields_view_old'][$id_metafield]==2) {
					// on a pass� une valeur en espace, plus de private
					$tiersuser->fields[$chp]="";
					$updateuser=true;
				}

				// on traite des champs qui pour la 1ere fois passe en mode partage
				if ($_SESSION['dims']['tiers_fields_mode'][$id_metafield]==1) {
					// on est en partage
					$usershare=true;
				}
			}
		}
		else {
			if (isset($_SESSION['dims']['tiers_fields_view'][$id_metafield]) && $_SESSION['dims']['tiers_fields_view'][$id_metafield]==2) {
				// on regarde si user
				if ($tiersuser->fields[$chp]!=$value) {
					$updateuser=true;
					// on a un changement de valeur
					//$tiers->updateFieldLog($chp,$value,$id_metafield,1,1);
					// update du champ, on n'utilise plus la fonction setvalues
					$tiersuser->fields[$chp]=$value;
				}
			}
			else {
				if (isset($_SESSION['dims']['tiers_fields_view'][$id_metafield]) && $_SESSION['dims']['tiers_fields_view'][$id_metafield]==1) {
					// on regarde si workspace
					$updateworkspace=true;

					// on a un changement de valeur
					$tiers->updateFieldLog($chp,$value,$id_metafield,1,2);

					// update du champ, on n'utilise plus la fonction setvalues
					$tiersworkspace->fields[$chp]=$value;

					// on g�re les changements de valeurs
					if (isset($_SESSION['dims']['tiers_fields_view_old'][$id_metafield]) && $_SESSION['dims']['tiers_fields_view_old'][$id_metafield]==2) {
						// on a pass� une valeur en espace, plus de private
						$tiersuser->fields[$chp]="";
						$updateuser=true;
					}
				}
			}
		}
	}
}

// update de fiche
$id_coun = dims_load_securvalue('id_country',_DIMS_NUM_INPUT,true,true,true);
$tiers->fields['id_country'] = $id_coun;
if ($id_coun != '' && $id_coun > 0){
	require_once DIMS_APP_PATH."modules/system/class_country.php";
	$country = new country();
	$country->open($id_coun);
	$tiers->fields['pays'] = $country->fields['name'];
}else
	$tiers->fields['pays'] = '';
$tiers->fields['type_tiers']=3;// a voir valeur par defaut
$tiers->fields['id_user']=$_SESSION['dims']['userid'];
$tiers->fields['timestp_modify']=dims_createtimestamp();
$tiers->save();

$_SESSION['business']['lastent'][]=$tiers->fields['id'];

dims_create_user_action_log(_SYSTEM_ACTION_MODIFYENT, $chp, 1, 1, $tiers->fields['id'], dims_const::_SYSTEM_OBJECT_TIERS);

if ($updateworkspace) {
	if(!isset($_SESSION['business']['tiers_id']) || $_SESSION['business']['tiers_id'] != '') {
		$tiersworkspace->fields['id']=$tiers->fields['id'];
		$tiersworkspace->fields['type_layer']=1;
		$tiersworkspace->fields['id_layer']=$_SESSION['dims']['workspaceid'];
	}

	$tiersworkspace->save();
}

if ($updateuser) {
	if(!isset($_SESSION['business']['tiers_id']) || $_SESSION['business']['tiers_id'] != '') {
		$tiersuser->fields['id']=$tiers->fields['id'];
		$tiersuser->fields['type_layer']=2;
		$tiersuser->fields['id_layer']==$_SESSION['dims']['userid'];
	}
	$tiersuser->save();
}

// on recupere toutes les infos du contact
$id_from = dims_load_securvalue('id_from', dims_const::_DIMS_NUM_INPUT, true, true);
$type_from = dims_load_securvalue('type_from', dims_const::_DIMS_CHAR_INPUT, true, true);
$type_to = dims_load_securvalue('type_to', dims_const::_DIMS_CHAR_INPUT, true, true);
//$tiers->setvalues($_POST,"ct_");

//on va verifier qu'il n'existe pas deja ce contact en base ou un nom proche
//ssi on n'est pas dans le cas d'une modif
if($tiers->new){
	$sql_idem = "SELECT id, lastname, firstname FROM dims_mod_business_tiers WHERE intitule LIKE '".$tiers->fields['intitule']."'";
	$res_idem = $db->query($sql_idem);
	if($db->numrows($res_idem) > 0) {
		//on va comparer au niveau du prenom
		while($tab_ctcomp = $db->fetchrow($res_idem)) {
			if($tiers->fields['firstname'] == $tab_ctcomp['firstname']) {

			}
		}
	}
}

// affectation du contexte de dims
if ($tiers->fields['id']>0) {
	$tiers->setugm();
}

// on sauvegarde maintenant le contact
if ($updatecontact) { // ne mettre a jour que si on update la fiche contact (ex fiche en veille)
	// on update
	$tiers->save();
	$id_share=0;
	// test si mode share active ou non
	if ($usershare) {
		// on verifie si le lien de partage existe ou non
		$sql = "select		id
				from		dims_share
				where		id_module=1
				and			id_object=".dims_const::_SYSTEM_OBJECT_TIERS."
				and			id_record=".$tiers->fields['id']."
				and			type_from=0
				and			id_from=".$_SESSION['dims']['workspaceid']."
				and			level_from=0";

		$res=$db->query($sql);

		if ($db->numrows($res)>0) {
			while ($sh=$db->fetchrow($res)) {
				$id_share=$sh['id'];
			}
		}
	}

	if ($id_share==0) {
		// on cr�� le partage vers les autres
		$share = new share();
		$share->fields['id_module']=1;
		$share->fields['id_module_type']=1;
		$share->fields['id_object']=dims_const::_SYSTEM_OBJECT_TIERS;
		$share->fields['id_record']=$tiers->fields['id'];
		$share->fields['type_from']=0;
		$share->fields['id_from']=$_SESSION['dims']['workspaceid'];
		$share->fields['level_from']=0;
		$share->save();
	}
}

$id_contact = $tiers->fields['id'];
$_SESSION['business']['tiers_id']=$id_contact;

//enregistrement de la photo
$id_ent = $tiers->fields['id'];
if(isset($_FILES['photo'])) {
	require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_add_photo.php');
}
/*

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
*/

// traitement des tags
$lsttagsTemp=dims_getTagsTemp($dims);

if (!empty($lsttagsTemp)) {
		require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
		foreach ($lsttagsTemp as $idtag=>$t) {
			$tag_index = new tag_index();
			$tag_index->fields['id_tag']=$idtag;
			$tag_index->fields['id_record']=$ent->fields['id'];
			$tag_index->fields['id_object']=dims_const::_SYSTEM_OBJECT_TIERS;
			$tag_index->fields['id_user']=$_SESSION['dims']['userid'];
			$tag_index->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
			$tag_index->fields['id_module']=1;
			$tag_index->fields['id_module_type']=1;
			$tag_index->save();
		}
		unset($_SESSION['dims']['tag_temp']);
}

$addr_list = dims_load_securvalue('addr_list', dims_const::_DIMS_NUM_INPUT, true, true,true);
if(!empty($addr_list)){
	require_once DIMS_APP_PATH.'modules/system/class_address.php';
	require_once DIMS_APP_PATH.'modules/system/class_address_type.php';
	foreach($addr_list as $idAdr){
		$adr = new address();
		$adr->open($idAdr);
		$adr->setvalues($_POST,'adr_'.$idAdr.'_');
		$adr->save();

		$type = dims_load_securvalue('type_address_'.$idAdr, dims_const::_DIMS_NUM_INPUT, true, true,true);
		$lk = $adr->getLinkCt($ent->get('id_globalobject'));
		if(!is_null($lk)){
			$lk->set('id_type',$type);
			$lk->save();
		}else{
			$adr->addLink($ent->get('id_globalobject'),$type);
		}
	}
}

//indexation
chdir(realpath("."));
$cmd="php ./cronindex.php";
//shell_exec($cmd);


if (isset($_SESSION['dims']['crm_newent_saveredirect']) && $_SESSION['dims']['crm_newent_saveredirect']!='') {
	$redir=str_replace("<ID_TIERS>", $tiers->fields['id'], $_SESSION['dims']['crm_newent_saveredirect']);

	$_SESSION['assurance']['currentdossier']['id']=$tiers->fields['id'];

	if(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_CONTACT) {
		$to_ent_pers = $_SESSION['dims']['desktopv2']['matrice']['id_from'];
		$ent_from_id = $tiers->fields['id'];

		$type_ent_pers = dims_load_securvalue('ent_pers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
		$ent_pers_link_lvl = dims_load_securvalue('ent_pers_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);

		$date_deb_d = dims_load_securvalue('date_deb_day', dims_const::_DIMS_NUM_INPUT, true, true);
		$date_deb_m = dims_load_securvalue('date_deb_month', dims_const::_DIMS_NUM_INPUT, true, true);
		$date_deb_y = dims_load_securvalue('date_deb_year', dims_const::_DIMS_NUM_INPUT, true, true);
		$date_fin_d = dims_load_securvalue('date_fin_day', dims_const::_DIMS_NUM_INPUT, true, true);
		$date_fin_m = dims_load_securvalue('date_fin_month', dims_const::_DIMS_NUM_INPUT, true, true);
		$date_fin_y = dims_load_securvalue('date_fin_year', dims_const::_DIMS_NUM_INPUT, true, true);

		$date_deb = $date_deb_y.$date_deb_m.$date_deb_d."000000";
		$date_fin = $date_fin_y.$date_fin_m.$date_fin_d."000000";

		$commentaire = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, true, true);
		$fonction = dims_load_securvalue('fonction', dims_const::_DIMS_CHAR_INPUT, true, true);
		$departement = dims_load_securvalue('departement', dims_const::_DIMS_CHAR_INPUT, true, true);

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
	}

	dims_redirect($redir);
}
else
	dims_redirect("$tabscriptenv?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_FORM."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$_SESSION['business']['tiers_id']."");

//on sauvegarde la personne rattachee
//dims_print_r($_POST);
//$type = dims_load_securvalue('type_rat_pers', dims_const::_DIMS_CHAR_INPUT, true, true);
//if($type != "") { //si $type est vide, c'est que l'on connait deja une personne rattachée
//	  switch($type) {
//		  case 'exist' :
//			  $id_pers = dims_load_securvalue('pers_id', dims_const::_DIMS_CHAR_INPUT, true, true);
//			  $type_link = dims_load_securvalue('pers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
//			  $sql_insert = "INSERT INTO `dims_mod_business_tiers_contact` (
//							  `id_tiers` ,
//							  `id_contact` ,
//							  `type_lien` ,
//							  `id_workspace` ,
//							  `id_user`,
//							  `date_create`,
//							  `link_level`,
//							  `id_ct_user_create`
//							  )
//							  VALUES (
//							  '".$id_ent."',
//							  '".$id_pers."',
//							  '".$type_link."',
//							  ".$_SESSION['dims']['workspaceid'].",
//							  ".$_SESSION['dims']['userid'].",
//							  ".date("YmdHis").",
//							  1,
//							  ".$_SESSION['dims']['user']['id_contact']."
//							  );";
//
//			  $db->query($sql_insert);
//			  if (isset($_SESSION['business']['ent_id']) && $_SESSION['business']['ent_id']>0)
//				  dims_redirect($scriptenv."?cat=0&action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$_SESSION['business']['ent_id']);
//			  else dims_redirect($scriptenv."?cat=0&action="._BUSINESS_TAB_CONTACTSSEEK);
//
//			  break;
//		  default :
//		  case 'create' :
//			  $name = dims_load_securvalue('ct_lastname', dims_const::_DIMS_CHAR_INPUT, true, true);
//			  $type_link = dims_load_securvalue('pers	_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
//			  $sql_insert = "INSERT INTO `dims_mod_business_contact` (
//							  `date_create` ,
//							  `lastname`
//							  )
//							  VALUES (
//							  '".dims_getdatetime()."',
//							  '".$name."'
//							  );";
//			  $db->query($sql_insert);
//			  $id_new_pers = $db->insertid();
//
//			  $sql_insert2 = "INSERT INTO `dims_mod_business_tiers_contact` (
//							  `id_tiers` ,
//							  `id_contact` ,
//							  `type_lien` ,
//							  `id_workspace` ,
//							  `id_user`,
//							  `date_create`,
//							  `link_level`,
//							  `id_ct_user_create`
//							  )
//							  VALUES (
//							  '".$id_ent."',
//							  '".$id_new_pers."',
//							  '".$type_link."',
//							  ".$_SESSION['dims']['workspaceid'].",
//							  ".$_SESSION['dims']['userid'].",
//							  ".date("YmdHis").",
//							  1,
//							  ".$_SESSION['dims']['user']['id_contact']."
//							  );";
//
//			  $db->query($sql_insert2);
//
//			  dims_redirect("$tabscriptenv?cat=0&action="._BUSINESS_TAB_CONTACTSTIERS."&part=1&contact_id=$id_new_pers&id_ent=$id_ent");
//
//			  break;
//	  }
//}

?>
