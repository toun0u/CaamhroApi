<script language="javascript" src="./include/portal.js"></script>
<script type="text/javascript" language="javascript">
	<?php
		require_once(DIMS_APP_PATH . '/include/javascript.php');
			$sid = session_id();

			$temp_dir = _DIMS_TEMPORARY_UPLOADING_FOLDER;
			$session_dir = $temp_dir.$sid;
			$upload_size_file = $session_dir."/upload_size";
			$upload_finished_file = $session_dir."/upload_finished";

			if (file_exists($upload_size_file)) unlink($upload_size_file);
			if (file_exists($upload_finished_file)) unlink($upload_finished_file);
			$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
		?>
		var uploads = new Array();
		var upload_cell, file_name;
		var count=0;
		var checkCount = 0;
		var check_file_extentions = true;
		var sid = '<? echo $sid; ?>';
		var page_elements = ["toolbar","page_status_bar"];
		var img_path = "../common/img/";
		var path = "";
		var bg_color = false;
		var status;
		var debug = false;
		var param1= '<? echo ($op == 'file_add') ? 'true;' : 'false;'; ?>';
		var param2= '<? echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>';

	//id_evt, id_contact, id_doc (le doc_vierge), id_etape
	function showUploadDoc(id_evt, id_ct, id_doc, id_etape) {
		var retour = dims_xmlhttprequest("index.php", 'action=show_upload&id_evt='+id_evt+'&id_ct='+id_ct+'&id_doc='+id_doc+'&id_etape='+id_etape);
		dims_getelem('dims_popup').innerHTML = retour;
		status = document.getElementById('status');
		<?
		global $dims;
		$rootpath=$dims->getProtocol().$http_host;
		?>
		setVariables("<? echo $rootpath; ?>","<? echo $_DIMS['cste']['_DOC_MSG_UPLOAD_FILE']; ?>","<? echo $_DIMS['cste']['_DOC_MSG_UPLOAD_WAITING']; ?>","<? echo $_DIMS['cste']['_DOC_MSG_COPY_FILE']; ?>","<? $_DIMS['cste']['_DOC_MSG_UPLOAD_ERROR']; ?>","<? echo $_DIMS['cste']['_DOC_MSG_UPLOAD_ERROREXT']; ?>");

		createFileInput();
		//dims_showcenteredpopup("",700,310,'dims_popup');
		var pop = document.getElementById('dims_popup');
		pop.style.display = "block";
		pop.style.visibility = "visible";
		pop.style.width = "690px";
		pop.style.height = "330px";
		pop.style.top = "10%";
		pop.style.left = "10%";
		pop.style.position = "absolute";
	}

	function verif_form() {
		if(
		   dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>", document.getElementById('del_lastname'), "string") &&
		   dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL_FIRSTNAME']; ?>", document.getElementById('del_firstname'), "string") &&
		   dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?>", document.getElementById('del_email'), "email") &&
		   dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL_MOBILE']; ?>", document.getElementById('del_mobile'), "string") &&
		   dims_validatefield("<? echo $_DIMS['cste']['_FAIRS_DATE_PRESENCE']; ?>", document.getElementById('del_date_presence'), "string")
		   )
		{
			document.form_inscrip_niv1.submit();
		}
	}

</script>
<script type="text/javascript" src="/common/js/upload/javascript/uploader.js"></script>
<?php

//Si on envoie un doc par un "input"
if(!empty($_POST)) {
	switch ($_POST['action']) {
		case 'upload_input':
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
			require_once(DIMS_APP_PATH . '/modules/doc/include/global.php');
			require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
			require_once(DIMS_APP_PATH . '/modules/system/class_action.php');

			$id_etap = 0;

			$id_etap = dims_load_securvalue('idEtap',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$id_ct = dims_load_securvalue('idCt',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$id_doc = dims_load_securvalue('idInput',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$id_evt = dims_load_securvalue('idEvt',dims_const::_DIMS_NUM_INPUT,true,true,false);

			if($_FILES['input']['error'] == 0) {

				$_SESSION['dims']['currentaction']=$id_evt;
				$docfile = new docfile();

				$docfile->fields['id_module'] = 1;
				$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$docfile->fields['id_folder'] = -1;
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpuploadedfile = $_FILES['input']['tmp_name'];
				$docfile->fields['name'] = $_FILES['input']['name'];
				$docfile->fields['size'] = filesize($_FILES['input']['tmp_name']);
				$error = $docfile->save();

				$id_newdoc=$docfile->fields['id'];

				// on cr�� l'association entre le doc et l'etape relative au user (dims_mod_business_event_etap_file_user)
				$etap_file = new etap_file_ct();
				$etap_file->init_description();

				$etap_file->fields['id_action'] = $id_evt;
				$etap_file->fields['id_etape'] = $id_etap;
				$etap_file->fields['id_doc'] = $id_doc;
				$etap_file->fields['id_contact'] = $id_ct;
				$etap_file->fields['valide'] = 0;
				$etap_file->fields['id_doc_frontoffice'] = $id_newdoc;
				$etap_file->fields['provenance'] = '_DIMS_LABEL_INET';
				$etap_file->fields['date_reception'] = date("YmdHis");

				$etap_file->save();
			}

			dims_redirect('index.php?submenu=event_record&id_event='.$id_evt.'&id_etap='.$id_etap);
			break;

		case 'changeCond':

			$idEtapUser = dims_load_securvalue('idEtapUser',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$condition = dims_load_securvalue('condition',dims_const::_DIMS_NUM_INPUT,true,true,false);

			$etap_user = new action_etap_ct();
			$etap_user->open($idEtapUser);

			$etap_user->fields['condition'] = $condition;

			if($condition == -1) {
				$etap_user->fields['valide_etape'] = 2;
				$etap_user->fields['date_validation_etape'] = date('YmdHis');
			}
			else {
				$etap_user->fields['valide_etape'] = 0;
				$etap_user->fields['date_validation_etape'] = 0;
			}

			$etap_user->save();
			break;
	}
}

echo '<div id="dims_popup"></div>';

$inscription = new inscription();
$inscription->open($idInscrip);

$sql =	'SELECT
				ee.*,
				eect.id AS id_ee_ct,
				eect.valide_etape,
				eect.date_validation_etape,
				eect.condition as condition_user,
				efile.id AS id_file_etap,
				efile.id_doc AS id_file_doc,
				efile.label AS input_label,
				efile.content AS input_content,
				efile.label_en AS input_label_en,
				efile.content_en AS input_content_en,
				euser.id AS id_file_ct,
				euser.id_contact,
				euser.id_doc AS id_ct_doc,
				euser.valide,
				euser.id_doc_frontoffice,
				euser.provenance,
				euser.date_reception,
				euser.date_validation,
				euser.invalid_content
			FROM
				dims_mod_business_event_etap ee
			LEFT JOIN
				dims_mod_business_event_etap_file efile
				ON
					ee.id = efile.id_etape
			LEFT JOIN
				dims_mod_business_event_etap_file_user euser
				ON
					ee.id = euser.id_etape
				AND
					euser.id_doc = efile.id
				AND
					euser.id_contact = :idcontact1
			LEFT JOIN
				dims_mod_business_event_etap_user eect
				ON
					ee.id = eect.id_etape
				AND
					eect.id_ee_contact = :idcontact2
			WHERE
				ee.id_action = :idaction and type_etape!=1
			ORDER BY
				ee.position,
				efile.id ASC,
				euser.date_reception DESC';

	$res=$db->query($sql, array(':idcontact1' => $_SESSION['dims']['user']['id_contact'],':idcontact2' => $_SESSION['dims']['user']['id_contact'],':idaction' => $id_evt));
	$nb_res = $db->numrows($res);

	if ($nb_res>0) {
		$id_etap_selected = 0;
		$class="trl1";

		$tab_etap = array();
		$tab_ct = array();
		$tab_file = array();

		//Tableau des entrées de documents, et doc versionnés
		$tabInput = array();

		//Selection de l'etape : Mise en session
		if(!isset($_SESSION['event']['etape_sel'])) $_SESSION['event']['etape_sel'] = 0;
		$id_etap_selected = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true, false, $_SESSION['event']['etape_sel']);

		//Booleen pour vérification de l'existence de l'etape (cas de session particulierement)
		$etap_exist = false;

		$cpt_etap_valid = 0;
		while ($value=$db->fetchrow($res)) {
			$cpt_valid = 0;

			if ($id_etap_selected==0) {
				$id_etap_selected=$value['id'];
				$_SESSION['event']['etape_sel'] =$value['id'];
			}

			//donnees concernant les etapes
			$tab_etap[$value['id']]['id']				= $value['id'];
			$tab_etap[$value['id']]['id_action']		= $value['id_action'];
			$tab_etap[$value['id']]['label']			= $value['label'];
			$tab_etap[$value['id']]['position']			= $value['position'];
			$tab_etap[$value['id']]['type_etape']		= $value['type_etape'];
			$tab_etap[$value['id']]['description']		= $value['description'];
			$tab_etap[$value['id']]['condition']		= $value['condition'];
			$tab_etap[$value['id']]['condition_content']= $value['condition_content'];
			$tab_etap[$value['id']]['condition_label_yes']	= $value['condition_label_yes'];
			$tab_etap[$value['id']]['condition_label_no']	= $value['condition_label_no'];
			$tab_etap[$value['id']]['condition_user']	= $value['condition_user'];
			$tab_etap[$value['id']]['paiement']			= $value['paiement'];
			$tab_etap[$value['id']]['date_fin']			= $value['date_fin'];

			// traitement de la langue anglaise // malinski
			if($_SESSION['dims']['currentlang']==2) {
				if (isset($value['label_en']) && $value['label_en']!='') {
					$tab_etap[$value['id']]['label']=$value['label_en'];
				}
				if (isset($value['description_en']) && $value['description_en']!='') {
					$tab_etap[$value['id']]['description']=$value['description_en'];
				}
				if (isset($value['condition_en']) && $value['condition_en']!='') {
					$tab_etap[$value['id']]['condition']=$value['condition_en'];
				}
				if (isset($value['condition_label_yes_en']) && $value['condition_label_yes_en']!='') {
					$tab_etap[$value['id']]['condition_label_yes']=$value['condition_label_yes_en'];
				}
				if (isset($value['condition_label_no_en']) && $value['condition_label_no_en']!='') {
					$tab_etap[$value['id']]['condition_label_no']=$value['condition_label_no_en'];
				}

			}
			//donnees concernant les etapes rattachees au contact courant
			if(isset($value['id_ee_ct']) && !empty($value['id_ee_ct'])) {
				$tab_etap[$value['id']]['id_ee_ct']			= $value['id_ee_ct'];
				$tab_etap[$value['id']]['valide_etape']		= $value['valide_etape'];
				$tab_etap[$value['id']]['date_valid_etape'] = $value['date_validation_etape'];
			}
			else {
				//si on ne recupère rien dans le left join, il faut initialiser les valeurs
				//on verifie d'abord si les etapes existent pour le contact courant (cela evite les doublons)
				$sql_eect = "SELECT id FROM dims_mod_business_event_etap_user WHERE id_etape = :idetape AND id_ee_contact = :idcontact";
				$res_eect = $db->query($sql_eect, array(':idetape' => $value['id'], ':idcontact' => $_SESSION['dims']['user']['id_contact']) );

				if($db->numrows($res_eect) == 0) {
					$etap_ct = new action_etap_ct();
					$etap_ct->init_description();
					$etap_ct->fields['id_etape'] = $value['id'];
					$etap_ct->fields['id_ee_contact'] = $_SESSION['dims']['user']['id_contact'];
					$id_eect = $etap_ct->save();
				}
				else {
					$tab_eect = $db->fetchrow($res_eect);
					$id_eect = $tab_eect['id'];
				}
				$tab_etap[$value['id']]['id_ee_ct']			= $id_eect;
				//$tab_etap[$value['id']]['valide_etape']		= 0;
				$tab_etap[$value['id']]['date_valid_etape'] = '';

			}

			if($value['type_etape'] == 5) {
				//dans le cas du paiement on a besoin des date de facturation et de paiement
				$sql_f = 	"SELECT paiement, date_facturation, date_paiement
							FROM dims_mod_business_event_inscription
							WHERE id_contact = :idcontact
							AND id_action = :idaction";
				$res_f = $db->query($sql_f, array(':idcontact' => $_SESSION['dims']['user']['id_contact'], ':idaction' => $value['id_action']));
				$tab_f = $db->fetchrow($res_f);

				$tab_etap[$value['id']]['paiement']			= $tab_f['paiement'];
				$tab_etap[$value['id']]['date_facturation']	= $tab_f['date_facturation'];
				$tab_etap[$value['id']]['date_paiement']	= $tab_f['date_paiement'];
			}

			//On set par défaut l'etape selectionné a la derniere "non validé"
			//if(empty($id_etap_selected) && $tab_etap[$value['id']]['valide_etape'] != 2)
			//	$id_etap_selected = $value['id'];

			//Cette etape correspond elle a celle selectionnee ?
			if($id_etap_selected == $value['id']) {
				$etap_exist = true;
			}

			//on compte le nombre d'etapes valides
			if($value['valide_etape'] == 2) $cpt_etap_valid++;

			//donnees concernant les docs rattaches aux etapes
			if(isset($value['id_file_etap']) && !empty($value['id_file_etap']))
			{
				$tab_file[$value['id']][$value['id_file_doc']]['id']	= $value['id_file_etap'];
				$tab_file[$value['id']][$value['id_file_doc']]['id_doc']= $value['id_file_doc'];
			}
			//donnees concernant les docs rattaches aux etapes et au contact courant
			if(isset($value['id_file_ct']) && !empty($value['id_file_ct']) && !empty($value['id_file_doc']))
			{
				$tab_ct[$value['id']][$value['id_file_etap']]['id_doc_frontoffice']	= $value['id_doc_frontoffice'];
				$tab_ct[$value['id']][$value['id_file_etap']]['provenance']			= $value['provenance'];
				$tab_ct[$value['id']][$value['id_file_etap']]['valide']				= $value['valide'];
				$tab_ct[$value['id']][$value['id_file_etap']]['date_reception']		= $value['date_reception'];
				$tab_ct[$value['id']][$value['id_file_etap']]['date_validation']	= $value['date_validation'];
				$tab_ct[$value['id']][$value['id_file_etap']]['id_contact']			= $value['id_contact'];
				$tab_ct[$value['id']][$value['id_file_etap']]['id_ct_doc']			= $value['id_ct_doc']; //doc non complete
				$tab_ct[$value['id']][$value['id_file_etap']]['id']					= $value['id_file_ct'];
				$tab_ct[$value['id']][$value['id_file_etap']]['invalid_content']	= $value['invalid_content'];

				if($value['valide']!=0) $cpt_valid++;
			}
			elseif(!empty($value['id_file_doc'])) {
				//si on ne recupère rien dans le left join, il faut initialiser les valeurs
				$file_ct = new etap_file_ct();
				$file_ct->init_description();
				$file_ct->fields['id_etape'] = $value['id'];
				$file_ct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
				$file_ct->fields['id_action'] = $value['id_action'];
				$file_ct->fields['id_doc'] = $value['id_file_doc'];
				$id_newfile = $file_ct->save();

				$tab_ct[$value['id']][$value['id_file_etap']]['id'] = $id_newfile;

				$tab_ct[$value['id']][$value['id_file_etap']]['id_doc_frontoffice']	= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['provenance']			= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['valide']				= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['date_reception']		= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['date_validation']	= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['id_contact']			= $_SESSION['dims']['user']['id_contact'];
				$tab_ct[$value['id']][$value['id_file_etap']]['id_ct_doc']			= $value['id_file_doc'];

			}
			elseif(empty($value['id_file_doc']) && !empty($value['input_label'])) {
				//gestion des inputs

				$tabInput[$value['id']][$value['id_file_etap']]['label'] = $value['input_label'];
				$tabInput[$value['id']][$value['id_file_etap']]['content'] = $value['input_content'];

				if($_SESSION['dims']['currentlang']==2) {
					if (isset($value['input_label_en']) && $value['input_label_en']!='') {
						$tabInput[$value['id']][$value['id_file_etap']]['label']=$value['input_label_en'];
					}
					if (isset($value['input_content_en']) && $value['input_content_en']!='') {
						$tabInput[$value['id']][$value['id_file_etap']]['content']=$value['input_content_en'];
					}
				}
				//Définition l'etat de "l'input" selon le premier elemnt trouvé soit le dernier uploadé
				if(!isset($tabInput[$value['id']][$value['id_file_etap']]['validated'])) {
					/*
					  L'etat de l'input est décalé par rapports aux doc qui lui
					  sont lié. un document est soit refusé, soit en attente,
					  soit validé. Mais si il est inexsistant "il" n'a pas
					  d'état. Par contre L'input en a un. Il faut doc modifier
					  l'echelle entre les deux.
					*/
					if(is_null($value['valide'])) {
						//Si null : aucun document -> input = 0
						$tabInput[$value['id']][$value['id_file_etap']]['validated'] = 0;
					}
					elseif($value['valide'] == -1) {
						//-1 -> a été refusé
						$tabInput[$value['id']][$value['id_file_etap']]['validated'] = $value['valide'];
					}
					elseif($value['valide'] >= 0) {
						//Si 0 -> document en attente de validation; etat input = 1
						//Si 1 -> document validé; etat input = 2
						$tabInput[$value['id']][$value['id_file_etap']]['validated'] = $value['valide']+1;
					}
				}

				if(!empty($value['id_doc_frontoffice'])) {
					$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['id_file_ct'] = $value['id_file_ct'];
					$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['id_doc']		= $value['id_doc_frontoffice'];
					$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['state']		= $value['valide'];
					$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['send_time']	= $value['date_reception'];
					$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['comment']	= $value['invalid_content'];
				}
			}

			if(!empty($value['condition']) || $value['condition_user'] == 1) {

				if((isset($tabInput[$value['id']][$value['id_file_etap']]['validated']) && $tabInput[$value['id']][$value['id_file_etap']]['validated'] < $tab_etap[$value['id']]['valide_etape']) ||
				   !isset($tab_etap[$value['id']]['valide_etape'])) {

					//si on est dans un onglet de paiement
					if($tab_etap[$value['id']]['paiement']) {
						//Si le paiement a ete validé pour l'inscription
						if($inscription->fields['paiement']) {
							if(!isset($tab_etap[$value['id']]['valide_etape']) ||
							   $tab_etap[$value['id']]['valide_etape'] == 2) {
								$tab_etap[$value['id']]['valide_etape'] = 2;
							   }
						}
						//Sinon on passe l'onglet en orange
						else {
							$tab_etap[$value['id']]['valide_etape'] = $tabInput[$value['id']][$value['id_file_etap']]['validated'];
						}
					}
					//Si ce n'est pas un onglet de paiement : cas classique etap=input
					else {
						$tab_etap[$value['id']]['valide_etape'] = $tabInput[$value['id']][$value['id_file_etap']]['validated'];
					}
				}
			}
			elseif($value['condition_user'] == -1)
				$tab_etap[$value['id']]['valide_etape'] = 2;
			elseif(!isset($tab_etap[$value['id']]['valide_etape']))
				$tab_etap[$value['id']]['valide_etape'] = 0;

			//on met la valeur a 1 si elle n'est pas encore enregistrée mais que l'on a deja un ou pls docs valides
			//ce qui permettra d'avoir la puce de la bonne couleur
			if($value['valide_etape'] == 0 && $cpt_valid > 0) $tab_etap[$value['id']]['valide_etape'] = 1;
		}
	}
//l'etape selectionnee n'existe pas ? -> 0 (== Premiere etape)
if(!$etap_exist) {
	$id_etap_selected = 1;
}
?>
<div id="content2_2">
	<div class="title"><?
	if (isset($evt) && $evt->fields['libelle']!="") {
		echo $_DIMS['cste']['_DIMS_LABEL_EVENT']." > ".$evt->fields['libelle'];
	}
	?>
	</div>
</div>
<?
$date_jour	= explode('-', $evt->fields['datejour']);

if($evt->fields['datefin'] == '0000-00-00') {
	$evt->fields['datefin'] = $evt->fields['datejour'];
}

$date_fin	= explode('-', $evt->fields['datefin']);
$same_date = 0;

if($evt->fields['datefin'] == $evt->fields['datejour']) {
	$same_date = 1;
}
?>
<div id="content2_3">
	<table cellpadding="0" cellspacing="0">
			<tr>
				<td style="border-right:2px solid #d6d6d6; vertical-align: top; width:160px;">
					<ul class="menu_etapes">
						<?
						//	on boucle sur l'ensemble des etapes avec statut
						foreach($tab_etap as $etap) {

							$class = '';
							if(empty($id_etap_selected) || $id_etap_selected == $etap['id']) {
								$id_etap_selected = $etap['id'];
								$previous_etap_state = (isset($previous_etap['valide_etape'])) ? $previous_etap['valide_etape'] : 2 ;
								$class = 'selected';
							}

							$puceEtap = '';
							if ($etap['type_etape'] != 1) {
								switch($etap['valide_etape']) {
									case -1:
										$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_red.gif" />';
										break;
									default:
									case 0:
										$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_grey.gif" />';
										break;
									case 1:
										$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_orange.gif" />';
										break;
									case 2:
										$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_green.gif" />';
										break;
								}
							}

							if($etap['condition'] && $etap['condition_user'] == -1) {
								$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_green.gif" />';
							}

							$label=$etap['label'];

							echo '<li><div class="'.$class.'"><a href="/index.php?submenu=event_record&id_event='.$id_evt.'&id_etap='.$etap['id'].'">'.$label.'</a></div></li>';
							$previous_etap = $etap;
						}
						?>
					</ul>
				</td>
<?
			$etap_selected = array();

			if(isset($tab_etap[$id_etap_selected]) &&
				!empty($tab_etap[$id_etap_selected]) &&
				is_array($tab_etap[$id_etap_selected])) {

				if ($tab_etap[$id_etap_selected]['type_etape']==1) {
					// resume
					echo '<table width="95%" align="center" style="font-size:12px;">
							<tr style="font-size:14px;font-weight:bold;text-align:center;">
								<td></td>
								<td>'.$_DIMS['cste']['_DIMS_LABEL_PROGRESS']."</td>
								<td>".$_DIMS['cste']['_TICKET_LIMIT_TIME_VALIDATION']."</td>
							</tr>";
					$color = 'trl1';
					foreach ($tab_etap as $id =>$etapcour) {
						if ($etapcour['type_etape'] != 1) {
							$color = ($color=='trl1') ? 'trl2' : 'trl1';

							echo "<tr class='".$color."'><td><a href='?id_event=".$id_evt."&id_etap=".$etapcour['id']."'>".$etapcour['label']."</a></td>";

							switch($etapcour['valide_etape']) {
								case -1:
									$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_red.gif" />';
									break;
								default:
								case 0:
									$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_grey.gif" />';
									break;
								case 1:
									$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_orange.gif" />';
									break;
								case 2:
									$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_green.gif" />';
									break;
							}

							echo "<td style='text-align:center;'>".$puceEtap."</td>";

							//date limite
							if($etapcour['date_fin'] != '')
								$date_fin_doc = dims_timestamp2local($etapcour['date_fin']);
							else $date_fin_doc['date'] = '';
							echo "<td style='text-align:center;'>".$date_fin_doc['date']."</td>";

							echo "</tr>";
						}

					}

					echo '</table>';
				}
				else {
					$date_fin_etape='';
					$etap_selected = $tab_etap[$id_etap_selected];

					if(!empty($etap_selected['date_fin'])) {
						$nbWeekDiff = 0;
						$color = '#07B304';

						$finTimeStamp = dims_timestamp2unix($etap_selected['date_fin']);
						$actTimeStamp = time();

						$diffTimeStamp = $finTimeStamp - $actTimeStamp;
						$nbWeekDiff = $diffTimeStamp / (60*60*24*7);

						if($nbWeekDiff <= 1)
							$color = '#FF1100';
						elseif($nbWeekDiff <= 2)
							$color = '#FF9700';
						elseif($nbWeekDiff <= 3)
							$color = '#FFCB00';
						else
							$color = '#07B304';

						$date = dims_timestamp2local($etap_selected['date_fin']);
						$date_fin_etape = $date['date'];
					}
					?>

					<td style="padding-left:5px;border-right:2px solid #d6d6d6; vertical-align: top; width: 550px;">
						<div class="section">
							<div class="title"><? echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'];?> :</div>
							<div class="texte_dexcriptif"><? echo $etap_selected['description']; ?></div>

							<?
							if($etap_selected['condition']) {
								$js_yes = 'javascript: if(confirm(\''.addslashes(trim($etap_selected['condition_label_yes'])).'\')) this.form.submit();';
								$js_no = 'javascript: if(confirm(\''.addslashes(trim($etap_selected['condition_label_no'])).'\')) this.form.submit();';

								$checkYes = '';
								$checkNo = '';
								$checkContent = '';

								if($etap_selected['condition_user'] == 1) {
									$checkYes = 'checked="checked"';
									$checkContent = $etap_selected['condition_label_yes'];
								}
								elseif($etap_selected['condition_user'] == -1) {
									$checkNo = 'checked="checked"';
									$checkContent = $etap_selected['condition_label_no'];
								}
								echo '<div id="texte_dexcriptif">
										<form method="post" action="">
											<input type="hidden" name="action" value="changeCond" />
											<input type="hidden" name="idEtapUser" value="'.$etap_selected['id_ee_ct'].'" />
											'.$etap_selected['condition_content'].'
											<input type="radio" name="condition" value="1" id="condition-yes" onclick="'.$js_yes.'" '.$checkYes.' />
											<label for="condition-yes">'.$_DIMS['cste']['_DIMS_YES'].'</label>
											<input type="radio" name="condition" value="-1" id="condition-no"  onclick="'.$js_no.'" '.$checkNo.' />
											<label for="condition-no">'.$_DIMS['cste']['_DIMS_NO'].'</label>
										</form>';

								echo '</div>';
							}
						?>
							<div class="right_object"><? echo $_DIMS['cste']['_INFOS_LIMIT_DATE'];?> : <span class="datedead"><? echo $date_fin_etape;?></span></div>
						</div>


						<?
						if($etap_selected['type_etape'] < 4) {
						?>
						<div class="section">

							<?
							$doc_etape = new docfile();
							if(isset($tab_file[$etap_selected['id']]) &&
									   is_array($tab_file[$etap_selected['id']]) &&
									   (count($tab_file[$etap_selected['id']]) > 0) &&
									   (!$etap_selected['condition'] || ($etap_selected['condition_user'] == 1))) {

								$nbcorrectfile=0;
								foreach($tab_ct[$etap_selected['id']] as $file) {
									$doc_etape->open($tab_file[$etap_selected['id']][$file['id_ct_doc']]['id_doc']);
									$file_extension = strrchr($doc_etape->fields['name'], '.');
									$file_img = '';
									if ($file_extension!='.sla' && $file_extension!='.odt') {
										$nbcorrectfile++;
									}
								}
							}

							// affiche les docs si il y en a
							if ($nbcorrectfile>0) {
								echo '<div class="separateur">&nbsp;</div><div class="title">'.$_DIMS['cste']['_DIMS_LABEL_DOCS_PROPOSED']." :</div>";

							}
							?>
							<div class="available_files">
								<div class="one_file">
									<?
									if(isset($tab_file[$etap_selected['id']]) &&
									   is_array($tab_file[$etap_selected['id']]) &&
									   (count($tab_file[$etap_selected['id']]) > 0) &&
									   (!$etap_selected['condition'] || ($etap_selected['condition_user'] == 1))) {

										foreach($tab_ct[$etap_selected['id']] as $file) {
											$doc_etape->open($tab_file[$etap_selected['id']][$file['id_ct_doc']]['id_doc']);

											$file_extension = strrchr($doc_etape->fields['name'], '.');

											$file_img = '';
											if ($file_extension!='.sla' && $file_extension!='.odt') {
												// test maintenant sur la langue
												$displayfile=false;
												if($_SESSION['dims']['currentlang']==2 && strpos($doc_etape->fields['name'],"_en")>0) {
													$displayfile=true;
												}
												elseif ($_SESSION['dims']['currentlang']==1 && strpos($doc_etape->fields['name'],"_en")===false) {
													$displayfile=true;
												}
												if ($displayfile) {
													switch($file_extension) {
															case '.odt':
																	$file_img = '<img src="./common/modules/system/img/step_pdf32.png" />';
																	break;
															default:
																	$file_img = '<img src="./common/modules/system/img/step_file32.png" />';
																	break;
															case '.doc':
															case '.docx':
															case '.docm':
																	$file_img = '<img src="./common/modules/system/img/step_doc32.png" />';
																	break;
															case '.xls':
															case '.xlsx':
															case '.xlsmw':
																	$file_img = '<img src="./common/modules/system/img/step_xls32.png" />';
																	break;
															case '.pdf':
																	$file_img = '<img src="./common/modules/system/img/step_pdf32.png" />';
																	break;
													}
													//if ($file_extension!=".odt") {
													$url=$doc_etape->getwebpath();
													/*}
													else {
															$url="/index.php?op=events&action=export_odt&id_event=".$id_evt."&id_doc=".$doc_etape->fields['id'].'&id_etap='.$etap_selected['id'];
															$doc_etape->fields['name']=str_replace(".odt",".pdf",$doc_etape->fields['name']);
													}*/

													echo '<div class="link_pdf"><a href="'.$url.'" target="_blank">'.$file_img.$doc_etape->fields['name'].'</a></div>';
												}
											}
										}
									}
									?>
								</div>
							</div>
						</div>
						<div class="separateur">&nbsp;</div>
						<?
						}

						if($etap_selected['valide_etape'] != 2 || empty($etap_selected['date_valid_etape'])) {
						?>
						<div class="section">
							<?
							if($etap_selected['type_etape'] < 4) {

								if(!empty($tabInput[$id_etap_selected]) && (!$etap_selected['condition'] || ($etap_selected['condition_user'] == 1))) {
								$etapInput = $tabInput[$id_etap_selected];
								//dims_print_r($etapInput);

								$style = '';


									foreach($etapInput as $idInput => $input) {
										echo '<div class="files">
												<h3>'.$input['label'].'</h3>
												<form method="post" action="" enctype="multipart/form-data">
													<input type="hidden" name="action" value="upload_input" />
													<input type="hidden" name="idInput" value="'.$idInput.'" />
													<input type="hidden" name="idCt" value="'.$_SESSION['dims']['user']['id_contact'].'" />
													<input type="hidden" name="idEtap" value="'.$id_etap_selected.'" />
													<input type="hidden" name="idEvt" value="'.$id_evt.'" />';

											echo '<input type="file" name="input" />
													<input type="submit" value="'.$_DIMS['cste']['_DIMS_SEND'].'" />';

											echo '</form>
												<div class="files-description">'.
													$input['content'].
												'</div>';

												if(!empty($input['user_doc'])) {
													echo '<div class="files-history">
															<img src="./common/modules/system/img/step_arrow.png" />
															<table style="width:80%">
																<tr>
																	<th>
																		'.$_DIMS['cste']['_DIMS_LABEL_FILE'].'
																	</th>
																	<th>
																		'.$_DIMS['cste']['_DIMS_LABEL_SEND_DATE'].'
																	</th>
																	<th>
																		'.$_DIMS['cste']['_INFOS_STATE'].'
																	</th>
																	<th>
																		'.$_DIMS['cste']['_DIMS_COMMENTS'].'
																	</th>
																</tr>';

													foreach($input['user_doc'] as $userDoc) {
														$doc = new docfile();
														$doc->open($userDoc['id_doc']);

														$dateSend = dims_timestamp2local($userDoc['send_time']);

														$classState = '';
														$state = '';

														switch ($userDoc['state']) {
															default:
															case 0:
																$classState = 'pending';
																$state = $_DIMS['cste']['_DIMS_DOC_VALIDATION_IN_PROGRESS'];
																break;
															case 1:
																$classState = 'validated';
																$state = $_DIMS['cste']['_DIMS_LABEL_VALIDATED'];
																break;
															case -1:
																$classState = 'refused';
																$state = $_DIMS['cste']['_DIMS_LABEL_DISAGREED'];
																break;
														}

														echo '<tr>
																<td>
																	<a href="'.$doc->getwebpath().'">
																		'.$doc->fields['name'].'
																	</a>
																</td>
																<td>
																	'.$dateSend['date'].' '.$dateSend['time'].'
																</td>
																<td class="'.$classState.'">
																	'.$state.'
																</td>
																<td>
																	'.$userDoc['comment'].'
																</td>
															</tr>';

													}
													echo '</table>
												</div>';
												}
										echo '</div>';
									}

									echo '</div>';
								}
							}
						}// fin du test si pas deja validé

						if($etap_selected['type_etape'] == 4) { //cas d'une etape de type formulaire
							require_once(DIMS_APP_PATH . '/modules/events/cms_event_form_niv2_fair_step4.php');
						}

						if($etap_selected['type_etape'] == 5) { //cas d'une etape de type paiement
							require_once(DIMS_APP_PATH . '/modules/events/cms_event_form_niv2_fair_step5.php');
						}

						if($etap_selected['valide_etape'] == 2 && !empty($etap_selected['date_valid_etape'])) {
							$date_str = dims_timestamp2local($etap_selected['date_valid_etape']);

							echo '<div class="validate" style="text-align:center;margin-top:10px;font-weight:bold;">'.$_DIMS['cste']['_DIMS_LABEL_STEP'].' '.strtolower($_DIMS['cste']['_DIMS_LABEL_VALIDATE_ON']).
							' '.$date_str['date'].'</div>';
						}
					}
					?>
					</div>
				</td>

				<td style="padding-left:5px;vertical-align: top;">
					<div class="calendar">
						<div class="period">
							<span class="bigday"><?php echo $date_jour[2]; ?></span>
							<span class="month"><?php echo $_SESSION['cste'][getMonthCste(intval($date_jour[1]))]; ?>,</span>
							<span class="year"><?php echo $date_jour[0]; ?></span>

							<?
							if(!$same_date) {
							?>
							<span style="clear:both;" class="bigday">&nbsp;-&nbsp;</span>
							<span class="bigday"><?php echo $date_fin[2]; ?></span>
							<span class="month"><?php echo $_SESSION['cste'][getMonthCste(intval($date_fin[1]))]; ?>,</span>
							<span class="year"><?php echo $date_fin[0]; ?></span>
							<?
							}
							?>
						</div>
						<div class="title"><span class="label"><? echo $_DIMS['cste']['_LOCATION'];?> : </span><? echo $evt->fields['lieu']; ?></div>
						<div class="calendar_description"><? echo dims_strcut($evt->fields['description'],60); ?></div>
						<div class="separateur">&nbsp;</div>
						<img src="/common/modules/events/img/img_big_five.png" alt="Status : OK" title="Status : OK">
						<!--div class="summary"><? echo $_DIMS['cste']['_DIMS_OBJECT_RESUME'];?></div>
						<table class="table_summary" width="100%">
							<!--?
							//	on boucle sur l'ensemble des etapes avec statut
							foreach($tab_etap as $etap) {
								$class = '';
								if(empty($id_etap_selected) || $id_etap_selected == $etap['id']) {
									$id_etap_selected = $etap['id'];
									$previous_etap_state = (isset($previous_etap['valide_etape'])) ? $previous_etap['valide_etape'] : 2 ;
									$class = 'current';
								}

								$puceEtap = '';
								if ($etap['type_etape'] != 1) {
									switch($etap['valide_etape']) {
										case -1:
											$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_red.gif" />';
											break;
										default:
										case 0:
											$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_grey.gif" />';
											break;
										case 1:
											$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_orange.gif" />';
											break;
										case 2:
											$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_green.gif" />';
											break;
									}
								}

								if($etap['condition'] && $etap['condition_user'] == -1) {
									$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_green.gif" />';
								}

								if($etap['date_valid_etape']!='')
																	$datef = dims_timestamp2local($etap['date_valid_etape']);
																else
																	$datef['date']='';

								echo '<tr><td width="65%">'.$etap['label'].'</td><td>'.$puceEtap.'</td><td>'.$datef.'</td></tr>';
								$previous_etap = $etap;
							}
							?>
						</table-->
					</div>
				</td>
				<?
				}
				?>
			</tr>
		</table>
</div>
<?

if (true) {


		if(count($tab_etap) > 0) {
			$previous_etap_state = 0;
			$previous_etap = array();

			echo '<div id="form-2-fair">
					<div id="steps-nav">
						<ul>';



			echo '</ul>
				</div>
				<div id="step">';


			echo '</div>
				</div>';
		}

?>
</div>

<?php
	}

	$organizer = new user();
	$user=$evt->fields['id_user'];
	$organizer->open($user);

	if(!empty($organizer->fields['email']) || !empty($organizer->fields['phone'])) {
		echo '<div class="contactus" style="width: 100%;">'
				.$_DIMS['cste']['_DIMS_CONTACT_US'].' : ';
		if(!empty($organizer->fields['email']))
			echo '<a href="mailto:'.$organizer->fields['email'].'?subject=Event : '.$evt->fields['libelle'].'">
					'.$organizer->fields['email'].'
					</a>&nbsp;';
		if(!empty($organizer->fields['phone']))
			echo $organizer->fields['phone'];
		echo '</div>';
	}

	echo '<div id="back_home" style="float: left;padding-top: 10px;background-color:white;">';
	echo '<div style="float: left; width: 23%; margin-left: 15px;">
		<a href=\'index.php?op=main&submenu=subscriptions\';" />
		<img style="border: 0px none; float: left; padding-top: 5px;margin-right: 10px;" src="/common/modules/events/img/img_fleche_back.png" />
		<span style="line-height:18px;color: #CC262C;text-decoration: none;text-decoration:underline">'.dims_constant::getVal('BACK_TO_EVENTS_LIST').'</span>
		</a>
		</div>';
	//echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_BACK_TO_HOME'],'./common/img/undo.gif','javascript: href.location="index.php";');
	?>
	<div class="footer_info footer_info_niv2">
		<div class="bloc_info">
			<table>
				<tr>
					<td style="vertical-align:top;"><img style="border: 0px none; float: left; padding-top: 5px;margin-right: 10px;" src="./common/img/icon_info.png" /></td>
					<td style="vertical-align: center;">
					<?= dims_constant::getVal('FAIRS_SUBSCRIPTION_DESCRIPTION'); ?> <a style="color:#CC262C;" href="mailto:Andre.Hansen@eco.etat.lu">Andre.Hansen@eco.etat.lu</a>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php
	echo '</div></div>';
?>
