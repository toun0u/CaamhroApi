<?php
require_once(DIMS_APP_PATH . "/modules/system/class_contactfield.php");

$contact=new contact($db);

if (isset($_SESSION['business']['contact_id']) && $_SESSION['business']['contact_id']>0) {
	$contact->open($_SESSION['business']['contact_id']);
}
else {
	$contact->init_description(false);
	$contact->setugm();
}

//selection des champs utiles pour les historiques dans la metabase
$tab_mbf = array();
$tab_mbf=$contact->getMbFields();
$tab_mtf=$contact->getDynamicFields();
$convmeta=array();
// enregistrement de la conversion
foreach ($tab_mtf as $k=>$fields) {
	$convmeta[$fields['namefield']]=$fields['id'];
}

// construction des deux autres dimensions
$contactworkspace = new contact_layer();
$contactworkspace->init_description();
$updateworkspace=false;
$updatecontact=false;
$contactuser = new contact_layer();
$contactuser->init_description();
$updateuser=false;
$createlayer = false;
$contactworkspace->setugm();
$contactuser->setugm();

if (isset($_SESSION['business']['contact_id'])) {
	// recherche si layer pour workspace
	$res=$db->query("SELECT id,type_layer,id_layer
					FROM dims_mod_business_contact_layer
					WHERE id= :id
					AND type_layer=1
					AND id_layer= :idlayer ", array(
			':id'		=> $_SESSION['business']['contact_id'],
			':idlayer'	=> $_SESSION['dims']['workspaceid']
	));

	if ($db->numrows($res)) {
		while ($f=$db->fetchrow($res)) {
			$contactworkspace->open($_SESSION['business']['contact_id'],1,$_SESSION['dims']['workspaceid']);
		}
	}
	else {
		//on indique que l'on connait le contact mais qu'il n'a pas de layer dans le workspace courant
		$createlayer = true;
		$contactworkspace->fields['id']=$_SESSION['business']['contact_id'];
		$contactworkspace->fields['type_layer']=1;
		$contactworkspace->fields['id_layer']=$_SESSION['dims']['workspaceid'];
	}

	// recherche si ligne pour user
	$res=$db->query("SELECT id,type_layer,id_layer
					FROM dims_mod_business_contact_layer
					WHERE id= :id
					AND type_layer=2
					AND id_layer= :idlayer ", array(
			':id'		=> $_SESSION['business']['contact_id'],
			':idlayer'	=> $_SESSION['dims']['userid']
	));

	if ($db->numrows($res)) {
		while ($f=$db->fetchrow($res)) {
			$contactuser->open($_SESSION['business']['contact_id'],2,$_SESSION['dims']['userid']);
		}
	}
	else {
		$contactuser->fields['id']=$_SESSION['business']['contact_id'];
		$contactuser->fields['type_layer']=2;
		$contactuser->fields['id_layer']=$_SESSION['dims']['userid'];
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
	}

	if ($chp!="") {
		if (is_array($value)) {
			if (isset($tab_mtf[$id_metafield]['type']) && $tab_mtf[$id_metafield]['type']=='checkbox') {
				$value=implode("||",$value);
			}
			else {
				$value=$value[0];
			}
		}

		if ($chp=='lastname' || $chp=='firstname') {
			$contact->fields[$chp]=$value;
		}

		// recherche du statut public
		if (!isset($_SESSION['dims']['contact_fields_view'][$id_metafield]) || $_SESSION['dims']['contact_fields_view'][$id_metafield]==0
			|| isset($_SESSION['dims']['contact_fields_mode'][$id_metafield])==0 ) {

			if ($contact->fields[$chp]!=$value) {
				// on a un changement de valeur
				//$contact->updateFieldLog($chp,$value,$id_metafield,0,0);

				// update du champ, on n'utilise plus la fonction setvalues
				$contact->fields[$chp]=$value;
				$updatecontact=true;

				// on g�re les changements de valeurs
				if (isset($_SESSION['dims']['contact_fields_view_old'][$id_metafield]) && $_SESSION['dims']['contact_fields_view_old'][$id_metafield]==1) {
					// on a pass� une valeur en espace, plus de private
					$contactworkspace->fields[$chp]="";
					$updateworkspace=true;
				}

				if (isset($_SESSION['dims']['contact_fields_view_old'][$id_metafield]) && $_SESSION['dims']['contact_fields_view_old'][$id_metafield]==2) {
					// on a pass� une valeur en espace, plus de private
					$contactuser->fields[$chp]="";
					$updateuser=true;
				}

				// on traite des champs qui pour la 1ere fois passe en mode partage
				if ($_SESSION['dims']['contact_fields_mode'][$id_metafield]==1) {
					// on est en partage
					$usershare=true;
				}
			}
		}
		else {
			if (isset($_SESSION['dims']['contact_fields_view'][$id_metafield]) && $_SESSION['dims']['contact_fields_view'][$id_metafield]==2) {
				// on regarde si user
				if ($contactuser->fields[$chp]!=$value) {
					$updateuser=true;
					// on a un changement de valeur
					//$contact->updateFieldLog($chp,$value,$id_metafield,1,1);
					// update du champ, on n'utilise plus la fonction setvalues
					$contactuser->fields[$chp]=$value;
				}
			}
			else {
				if (isset($_SESSION['dims']['contact_fields_view'][$id_metafield]) && $_SESSION['dims']['contact_fields_view'][$id_metafield]==1) {
					// on regarde si workspace
					$updateworkspace=true;
					// on a un changement de valeur
					//$contact->updateFieldLog($chp,$value,$id_metafield,1,2);
					// update du champ, on n'utilise plus la fonction setvalues
					$contactworkspace->fields[$chp]=$value;

					// on g�re les changements de valeurs
					if (isset($_SESSION['dims']['contact_fields_view_old'][$id_metafield]) && $_SESSION['dims']['contact_fields_view_old'][$id_metafield]==2) {
						// on a pass� une valeur en espace, plus de private
						$contactuser->fields[$chp]="";
						$updateuser=true;
					}
				}
			}
		}
	}
}

// update de fiche
$id_coun = dims_load_securvalue('id_country',dims_const::_DIMS_NUM_INPUT,true,true,true);
$contact->fields['id_country'] = $id_coun;
if ($id_coun != '' && $id_coun > 0){
	require_once DIMS_APP_PATH."modules/system/class_country.php";
	$country = new country();
	$country->open($id_coun);
	$contact->fields['country'] = $country->fields['name'];
}else
	$contact->fields['country'] = '';
$contact->fields['id_user']=$_SESSION['dims']['userid'];
$contact->fields['id_user_create']=$_SESSION['dims']['userid'];
$contact->fields['timestp_modify']=dims_createtimestamp();
$contact->fields['id_module']= 1; // module system

if (
	(empty($contact->fields['email']) || preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $contact->fields['email'])) &&
	(empty($contact->fields['email2']) || preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $contact->fields['email2'])) &&
	(empty($contact->fields['email3']) || preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $contact->fields['email3']))
){
	// L'email est valide
	$contact->save();
} else {
	// L'email n'est pas valide
	if (isset($_SESSION['dims']['fail_email_redirect_url'])){
		$redirect_link=$_SESSION['dims']['fail_email_redirect_url'];
		unset($_SESSION['dims']['fail_email_redirect_url']);
		dims_redirect($redirect_link);
	}
}

if ($updateworkspace) {
	if(!isset($_SESSION['business']['contact_id']) || $_SESSION['business']['contact_id'] == '' || $createlayer) {
		$contactworkspace->fields['id']=$contact->fields['id'];
		$contactworkspace->fields['type_layer']=1;
		$contactworkspace->fields['id_layer']=$_SESSION['dims']['workspaceid'];
	}
	if ($contactworkspace->fields['id_user_create']=='') $contactworkspace->fields['id_user_create']=$_SESSION['dims']['userid'];
	$contactworkspace->save();
}

if ($updateuser) {
	if(!isset($_SESSION['business']['contact_id']) || $_SESSION['business']['contact_id'] == '') {
		$contactuser->fields['id']=$contact->fields['id'];
		$contactuser->fields['type_layer']=2;
		$contactuser->fields['id_layer']=$_SESSION['dims']['userid'];
	}
	if ($contactuser->fields['id_user_create']=='') $contactuser->fields['id_user_create']=$_SESSION['dims']['userid'];
	$contactuser->save();
}

// on recupere toutes les infos du contact
$id_from = dims_load_securvalue('id_from', dims_const::_DIMS_NUM_INPUT, true, true);
$type_from = dims_load_securvalue('type_from', dims_const::_DIMS_CHAR_INPUT, true, true);
$type_to = dims_load_securvalue('type_to', dims_const::_DIMS_CHAR_INPUT, true, true);
//$contact->setvalues($_POST,"ct_");

//on va verifier qu'il n'existe pas deja ce contact en base ou un nom proche
//ssi on n'est pas dans le cas d'une modif
if($contact->new){
	$sql_idem = "SELECT id, lastname, firstname FROM dims_mod_business_contact WHERE lastname LIKE :lastname ";
	$res_idem = $db->query($sql_idem,array(
		':lastname' => $contact->fields['lastname']
	));
	if($db->numrows($res_idem) > 0) {
		//on va comparer au niveau du prenom
		while($tab_ctcomp = $db->fetchrow($res_idem)) {
			if($contact->fields['firstname'] == $tab_ctcomp['firstname']) {

			}
		}
	}
}

// affectation du contexte de dims
if ($contact->fields['id']>0) {
	$contact->setugm();
	$contact->fields['id_module']=1; // hack for system module
}

// on sauvegarde maintenant le contact
if ($updatecontact) { // ne mettre a jour que si on update la fiche contact (ex fiche en veille)
	// on update
	if (
		(empty($contact->fields['email']) || preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $contact->fields['email'])) &&
		(empty($contact->fields['email2']) || preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $contact->fields['email2'])) &&
		(empty($contact->fields['email3']) || preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $contact->fields['email3']))
	){
		// L'email est valide
		$contact->save();
	} else {
		// L'email n'est pas valide
		if (isset($_SESSION['dims']['fail_email_redirect_url'])){
			$redirect_link=$_SESSION['dims']['fail_email_redirect_url'];
			unset($_SESSION['dims']['fail_email_redirect_url']);
			dims_redirect($redirect_link);
		}
	}
	$id_share=0;
	// test si mode share active ou non
	if ($usershare) {
		// on verifie si le lien de partage existe ou non
		$sql = "select		id
				from		dims_share
				where		id_module=1
				and			id_object= :idobject
				and			id_record= :idrecord
				and			type_from=0
				and			id_from= :idfrom
				and			level_from=0";

		$res=$db->query($sql, array(
			':idobject'	=> dims_const::_SYSTEM_OBJECT_CONTACT,
			':idrecord'	=> $contact->fields['id'],
			':idfrom'	=> $_SESSION['dims']['workspaceid']
		));

		if ($db->numrows($res)>0) {
			while ($sh=$db->fetchrow($res)) {
				$id_share=$sh['id'];
			}
		}
		if ($id_share==0) {
			// on cr�� le partage vers les autres
			$share = new share();
			$share->fields['id_module']=1;
			$share->fields['id_module_type']=1;
			$share->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
			$share->fields['id_record']=$contact->fields['id'];
			$share->fields['type_from']=0;
			$share->fields['id_from']=$_SESSION['dims']['workspaceid'];
			$share->fields['level_from']=0;
			$share->save();
		}
	}


}

$id_contact = $contact->fields['id'];
$_SESSION['business']['contact_id']=$id_contact;

//enregistrement de la photo
if(isset($_FILES['photo'])) {
	require_once(DIMS_APP_PATH . '/modules/system/crm_contact_add_photo.php');
}

if(!empty($id_from)) {
	switch($type_from) {
		case 'cte':
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
							:idfrom ,
							:idcontact ,
							:idobject
							'',
							'2',
							'".date('YmdHis')."',
							:usercontact ,
							'',
							'',
							:workspaceid ,
							:userid ,
							''
							);
						";
			$db->query($sql_ins, array(
				':idfrom'		=> $id_from,
				':idcontact'	=> $id_contact,
				':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT,
				':usercontact'	=> $_SESSION['dims']['user']['id_contact'],
				':workspaceid'	=> $_SESSION['dims']['workspaceid'],
				':userid'		=> $_SESSION['dims']['userid']
			));

			//eventuellement redirection
			break;
		case 'ent':
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
							:idfrom ,
							:idcontact ,
							'',
							:workspaceid ,
							:userid ,
							".date("YmdHis").",
							2,
							:usercontact
							);";
			$db->query($sql_insert, array(
				':idfrom'		=> $id_from,
				':idcontact'	=> $id_contact,
				':usercontact'	=> $_SESSION['dims']['user']['id_contact'],
				':workspaceid'	=> $_SESSION['dims']['workspaceid'],
				':userid'		=> $_SESSION['dims']['userid']
			));
			break;
	}
}

// traitement des tags
$lsttagsTemp=dims_getTagsTemp($dims);

if (!empty($lsttagsTemp)) {
		require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
		foreach ($lsttagsTemp as $idtag=>$t) {
			$tag_index = new tag_index();
			$tag_index->fields['id_tag']=$idtag;
			$tag_index->fields['id_record']=$contact->fields['id'];
			$tag_index->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
			$tag_index->fields['id_user']=$_SESSION['dims']['userid'];
			$tag_index->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
			$tag_index->fields['id_module']=1;
			$tag_index->fields['id_module_type']=1;
			$tag_index->save();
		}
		unset($_SESSION['dims']['tag_temp']);
}

/*if (isset($_POST['type_from']) && $_POST['type_from']=="cte") {
	// on redirige sur la fiche de lien
	dims_redirect("/admin.php?cat=0&action=307&part=303&contact_id=".$contact->fields['id']);
}
else {*/

//indexation
//chdir(realpath("."));
//$cmd="php /cronindex.php";
//shell_exec($cmd);
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
		$lk = $adr->getLinkCt($contact->get('id_globalobject'));
		if(!is_null($lk)){
			$lk->set('id_type',$type);
			$lk->save();
		}else{
			$adr->addLink($contact->get('id_globalobject'),$type);
		}
	}
}

if (isset($_SESSION['dims']['crm_newcontact_saveredirect']) && $_SESSION['dims']['crm_newcontact_saveredirect']!='') {
	$redir = str_replace("<ID_CONTACT>", $id_contact, $_SESSION['dims']['crm_newcontact_saveredirect']);

	if (isset($_SESSION['courrier']['currentdossier'])) {
		if (isset($_SESSION['courrier']['currentdossier']['type']) && isset($_SESSION['courrier']['currentdossier']['id']) && $_SESSION['courrier']['currentdossier']['type'] == 3 && $_SESSION['courrier']['currentdossier']['id'] > 0){
			require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');
			$lk = new tiersct();
			$lk->init_description();
			require_once DIMS_APP_PATH.'/modules/notaire/include/class_courrier_client_entreprise.php';
			$current_dossier = new courrier_client_entreprise();
			$current_dossier->openWithGB($_SESSION['courrier']['currentdossier']['id']);

			$lk->fields['id_tiers'] = $current_dossier->fields['id'];// $_SESSION['courrier']['currentdossier']['id'];
			$lk->fields['id_contact'] = $contact->fields['id'];
			$lk->fields['type_lien'] = 1;
			$lk->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$lk->fields['id_user'] = $_SESSION['dims']['userid'];
			$lk->fields['date_create'] = dims_createtimestamp();
			$lk->fields['link_since'] = 0;
			$lk->fields['link_level'] = 0;
			$lk->save();

		}

		$_SESSION['courrier']['currentdossier']['type']=1;
		$_SESSION['courrier']['currentdossier']['id']=$contact->fields['id_globalobject'];
	}
	elseif(isset($_SESSION['assurance']['currentdossier'])) {
		if (isset($_SESSION['assurance']['currentdossier']['type']) && isset($_SESSION['assurance']['currentdossier']['id']) && $_SESSION['assurance']['currentdossier']['type'] == 3 && $_SESSION['assurance']['currentdossier']['id'] > 0){
			require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');
			$lk = new tiersct();
			$lk->init_description();
			$lk->fields['id_tiers'] = $_SESSION['assurance']['currentdossier']['id'];
			$lk->fields['id_contact'] = $contact->fields['id'];
			$lk->fields['type_lien'] = 1;
			$lk->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$lk->fields['id_user'] = $_SESSION['dims']['userid'];
			$lk->fields['date_create'] = dims_createtimestamp();
			$lk->fields['link_since'] = 0;
			$lk->fields['link_level'] = 0;
			$lk->fields['assurance_est_employe_numero_matricule'] = '';
			$lk->save();
			require_once(DIMS_APP_PATH . '/modules/assurance/include/class_assurance_client_entreprise.php');
			$tiers = new assurance_client_entreprise();
			$tiers->open($_SESSION['assurance']['currentdossier']['id']);
			$tiers->setNbEmployes($tiers->getNbEmployes()+1,true);
		}
		$_SESSION['assurance']['currentdossier']['type']=1;
		$_SESSION['assurance']['currentdossier']['id']=$contact->fields['id'];


	}
	elseif(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_TIERS) {
		$to_ent = $_SESSION['dims']['desktopv2']['matrice']['id_from'];
		$pers_from_id = $id_contact;

		$type_ent = dims_load_securvalue('tiers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
		$ent_link_lvl = dims_load_securvalue('tiers_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);

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
	}
	elseif(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_CONTACT) {
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
						:idfrom ,
						:idcontact ,
						:idobject ,
						'',
						'2',
						'".date('YmdHis')."',
						:usercontact ,
						'',
						'',
						:workspaceid ,
						:userid ,
						''
						);
					";
			$db->query($sql_ins, array(
				':idfrom'		=> $_SESSION['dims']['desktopv2']['matrice']['id_from'],
				':idcontact'	=> $id_contact,
				':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT,
				':usercontact'	=> $_SESSION['dims']['user']['id_contact'],
				':workspaceid'	=> $_SESSION['dims']['workspaceid'],
				':userid'		=> $_SESSION['dims']['userid']
			));
	}

	dims_redirect ($redir);
}
else
	dims_redirect($scriptenv."?action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$_SESSION['business']['contact_id']);
//}
?>
