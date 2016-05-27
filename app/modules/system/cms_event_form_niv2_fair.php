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
		   dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL_LASTNAME']; ?>", document.getElementById('del_lastname'), "string") &&
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
<script type="text/javascript" src="/js/upload/javascript/uploader.js"></script>
<?php

//Si on envoie un doc par un "input"
if(!empty($_POST)) {
	switch ($_POST['action']) {
		case 'upload_input':
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
			require_once(DIMS_APP_PATH . '/modules/doc/include/global.php');
			require_once(DIMS_APP_PATH . '/modules/system/class_action_etap_file_ct.php');
			require_once(DIMS_APP_PATH . '/modules/system/class_action_etap.php');
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

			dims_redirect('index.php?id_event='.$id_evt.'&id_etap='.$id_etap);
			break;

		case 'changeCond':
			require_once(DIMS_APP_PATH . '/modules/system/class_action_etap_ct.php');

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

require_once(DIMS_APP_PATH . '/modules/system/class_action.php');

$id_evt = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true);

//affichage du resume de l'evt
$evt = new action();
$evt->open($id_evt);
echo '<div id="dims_popup"></div>';
echo '<div id="descript_evt" style="font-size:16px;">';
if ($evt->fields['libelle']!="") {
	echo '<h1>'.$evt->fields['libelle'].'</h1>';
}
$date_jour	= split('-', $evt->fields['datejour']);

if($evt->fields['datefin'] == '0000-00-00')
	$evt->fields['datefin'] = $evt->fields['datejour'];

$date_fin	= split('-', $evt->fields['datefin']);

$same_date = 0;

if($evt->fields['datefin'] == $evt->fields['datejour'])
	$same_date = 1;
?>
<div id="left-side">
	<div class="date_fairs">
		<span class="day_fairs">
			<?php echo $date_jour[2]; ?>
		</span>
		<span class="month">
			<?php echo $_SESSION['cste'][getMonthCste(intval($date_jour[1]))]; ?>,
		</span>
		<span class="year">
			<?php echo $date_jour[0]; ?>
		</span>

	<?php
if(!$same_date) {
	?>
	<span class="day_fairs">
		 &nbsp;-&nbsp;
	</span>

		<span class="day_fairs">
			<?php echo $date_fin[2]; ?>
		</span>
		<span class="month">
			<?php echo $_SESSION['cste'][getMonthCste(intval($date_fin[1]))]; ?>,
		</span>
		<span class="year">
			<?php echo $date_fin[0]; ?>
		</span>
	</div>
<?php
}

//echo '<p>'.$evt->fields['description'].'</p>';
echo '</div>';

$inscription = new inscription();
$inscription->open($idInscrip);

//dims_print_r($evt->fields);

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
					euser.id_contact = :idcontact
			LEFT JOIN
				dims_mod_business_event_etap_user eect
				ON
					ee.id = eect.id_etape
				AND
					eect.id_ee_contact = :idcontact
			WHERE
				ee.id_action = :idaction
			ORDER BY
				ee.position,
				efile.id ASC,
				euser.date_reception DESC';
	$res=$db->query($sql, array(
		':idcontact' 	=> $_SESSION['dims']['user']['id_contact'],
		':idaction' 	=> $id_evt
	));
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

			//construction des tableaux de donnees

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

			//donnees concernant les etapes rattachees au contact courant
			if(isset($value['id_ee_ct']) && !empty($value['id_ee_ct'])) {
				$tab_etap[$value['id']]['id_ee_ct']			= $value['id_ee_ct'];
				$tab_etap[$value['id']]['valide_etape']		= $value['valide_etape'];
				$tab_etap[$value['id']]['date_valid_etape'] = $value['date_validation_etape'];
			}
			else {
				//si on ne recupère rien dans le left join, il faut initialiser les valeurs
				require_once(DIMS_APP_PATH . '/modules/system/class_action_etap_ct.php');
				//on verifie d'abord si les etapes existent pour le contact courant (cela evite les doublons)
				$sql_eect = "SELECT id FROM dims_mod_business_event_etap_user WHERE id_etape = :idetape AND id_ee_contact = :idcontact ";
				$res_eect = $db->query($sql_eect, array(
					':idcontact' 	=> $_SESSION['dims']['user']['id_contact'],
					':idetape' 		=> $value['id']
				));

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
							AND id_action = :idaction ";
				$res_f = $db->query($sql_f, array(
					':idcontact' 	=> $_SESSION['dims']['user']['id_contact'],
					':idaction' 	=> $value['id_action']
				));
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
				require_once(DIMS_APP_PATH . '/modules/system/class_action_etap_file_ct.php');
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

				if(($tabInput[$value['id']][$value['id_file_etap']]['validated'] < $tab_etap[$value['id']]['valide_etape']) ||
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

		//l'etape selectionnee n'existe pas ? -> 0 (== Premiere etape)
		if(!$etap_exist) {
			$id_etap_selected = 0;
		}

		if(count($tab_etap) > 0) {
			$previous_etap_state = 0;
			$previous_etap = array();

			echo '<div id="form-2-fair">
					<div id="steps-nav">
						<ul>';

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
	//dims_print_r($etap);
				if($etap['condition'] && $etap['condition_user'] == -1) {
					$puceEtap = '<img width="12" height="12" src="./common/modules/system/img/ico_point_green.gif" />';
				}

				echo '<li class='.$class.'>
						<div class="corner-left"></div>
						<div class="center">
							'.$puceEtap.'
							<a href="?id_event='.$id_evt.'&id_etap='.$etap['id'].'">'.
								$etap['label']
							.'</a>
						</div>
						<div class="corner-right"></div>
					</li>';

				$previous_etap = $etap;
			}

			echo '</ul>
				</div>
				<div id="step">';

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

					foreach ($tab_etap as $id =>$etapcour) {
						if ($etapcour['type_etape'] != 1) {
							$color=($color=='trl1') ? 'trl2' : 'trl1';

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
							$date_fin = dims_timestamp2local($etapcour['date_fin']);
							echo "<td style='text-align:center;'>".$date_fin['date']."</td>";

							echo "</tr>";
						}

					}

					echo '</table>';
				}
				else {
					$etap_selected = $tab_etap[$id_etap_selected];

					echo '<div id="step">
							<div id="step-informations">
								<div id="step-description">
									<h3>'.$_DIMS['cste']['_DIMS_FAIR_STEP_DESCRIPTION'].' :</h3>
									<p>'.
										$etap_selected['description'].
									'</p>';
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
						$date_fin = $date['date'];

						echo '<div id="step-limit">';
						echo $_DIMS['cste']['_DIMS_VALID_FAIR_STEP_TO'].' : ';
						echo '<span style="color: '.$color.'">';
						echo $date_fin;
						echo '</span>';
						echo '</div>';
					}
					echo		'</div>';

					if(isset($tab_file[$etap_selected['id']]) &&
					   is_array($tab_file[$etap_selected['id']]) &&
					   (count($tab_file[$etap_selected['id']]) > 0) &&
					   (!$etap_selected['condition'] || ($etap_selected['condition_user'] == 1))) {

						$doc_etape = new docfile();

						echo '<div id="step-provided-files">
								<h3>'.$_DIMS['cste']['_DIMS_FAIR_AVAILABLE_FILE'].' :</h3>';

						foreach($tab_ct[$etap_selected['id']] as $file) {
							$doc_etape->open($tab_file[$etap_selected['id']][$file['id_ct_doc']]['id_doc']);

							$file_extension = strrchr($doc_etape->fields['name'], '.');

							$file_img = '';
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
							if ($file_extension!=".odt") {
								$url=$doc_etape->getwebpath();
							}
							else {
								$url="/index.php?op=events&action=export_odt&id_event=".$id_evt."&id_doc=".$doc_etape->fields['id'].'&id_etap='.$etap_selected['id'];
								$doc_etape->fields['name']=str_replace(".odt",".pdf",$doc_etape->fields['name']);
							}

							echo '<div class="provided-file">
									<div class="file-icon">
										<a href="'.$url.'">'.
											$file_img.'
										</a>
									</div>
									<div class="file-name">
										<a href="'.$url.'">'.
											$doc_etape->fields['name'].
										'</a>
									</div>
								</div>';

						}
						echo '</div>';

					}
					echo '</div>';


					if($etap_selected['condition']) {
						$js_yes = 'javascript: if(confirm(\''.$etap_selected['condition_label_yes'].'\')) this.form.submit();';
						$js_no = 'javascript: if(confirm(\''.$etap_selected['condition_label_no'].'\')) this.form.submit();';

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

						echo '<div id="step-condition">
								<form method="post" action="">';
						// Sécurisation du formulaire par token
						require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
						$token = new FormToken\TokenField;
						$token->field("action",		"changeCond");
						$token->field("idEtapUser",	$etap_selected['id_ee_ct']);
						$token->field("condition");
						$tokenHTML = $token->generate();
						echo $tokenHTML;
						echo '		<input type="hidden" name="action" value="changeCond" />
									<input type="hidden" name="idEtapUser" value="'.$etap_selected['id_ee_ct'].'" />
									'.$etap_selected['condition_content'].'
									<input type="radio" name="condition" value="1" id="condition-yes" onclick="'.$js_yes.'" '.$checkYes.' />
									<label for="condition-yes">'.$_DIMS['cste']['_DIMS_YES'].'</label>
									<input type="radio" name="condition" value="-1" id="condition-no"  onclick="'.$js_no.'" '.$checkNo.' />
									<label for="condition-no">'.$_DIMS['cste']['_DIMS_NO'].'</label>
								</form>';
						/*if(!empty($checkContent)) {
							echo '<p>'.
									$checkContent.
								'</p>';
						}*/
						echo '</div>';
					}

					if(!empty($tabInput[$id_etap_selected]) && (!$etap_selected['condition'] || ($etap_selected['condition_user'] == 1))) {
						$etapInput = $tabInput[$id_etap_selected];
						//dims_print_r($etapInput);

						$style = '';
						if($hideFile)
							$style = 'style="display: none;"';

						echo '<div id="step-files" '.$style.'>';

						foreach($etapInput as $idInput => $input) {
							echo '<div class="files">
									<h3>'.$input['label'].'</h3>
									<form method="post" action="" enctype="multipart/form-data">';
							// Sécurisation du formulaire par token
							require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
							$token = new FormToken\TokenField;
							$token->field("action",	"upload_input");
							$token->field("idInput",$idInput);
							$token->field("idCt",	$_SESSION['dims']['user']['id_contact']);
							$token->field("idEtap",	$id_etap_selected);
							$token->field("idEvt",	$id_evt);
							$tokenHTML = $token->generate();
							echo $tokenHTML;
							echo '		<input type="hidden" name="action" value="upload_input" />
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
												<table>
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
													$state = $_DIMS['cste']['_DIMS_LABEL_INSC_RUNNING'];
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

					if($etap_selected['type_etape'] == 4) { //cas d'une etape de type formulaire

						echo '<div style="width:100%; overflow:auto;">';

						$disp = "none";
						/***********************************/
						/* Affichage des délégués inscrits */
						/***********************************/

						$sql_d = "SELECT * FROM dims_mod_business_event_etap_delegue WHERE id_action = :idaction AND id_etap = :idetape ";
						$res_d = $db->query($sql_d, array(
							':idetape' 	=> $etap_selected['id'],
							':idaction' => $id_evt
						));
						if($db->numrows($res_d) > 0) {
							//on affiche le tableau de resultats
							$color = "#EEEEEE";
							echo '<table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;">
									<tr style="background-color:#EEEEEE;height:20px;">
										<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</th>
										<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_FIRSTNAME'].'</th>
										<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</th>
										<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_MOBILE'].'</th>
										<th align="left">'.$_DIMS['cste']['_FAIRS_DATE_PRESENCE'].'</th>
										<th></th>
									</tr>';
							while($tab_deg = $db->fetchrow($res_d)) {
								if($color == "#EEEEEE") $color = "#FFFFFF";
								else $color = "#EEEEEE";
								//$date = dims_timestamp2local($tab_deg['date_inscr']);
								$date_deb = dims_timestamp2local($tab_deg['date_presence']);
								if($tab_deg['date_presence_fin'] != '') {
									$date_fin = dims_timestamp2local($tab_deg['date_presence_fin']);
									$date = $_DIMS['cste']['_FROM']." ".$date_deb['date']." ".$_DIMS['cste']['_DIMS_LABEL_A']." ".$date_fin['date'];
								}
								else {
									$date = $_DIMS['cste']['_AT']." ".$date_deb['date'];
								}
								echo '<tr style="background-color:'.$color.';height:20px;">
										<td>'.$tab_deg['lastname'].'</td>
										<td>'.$tab_deg['firstname'].'</td>
										<td>'.$tab_deg['email'].'</td>
										<td>'.$tab_deg['mobile'].'</td>
										<td>'.$date.'</td>
										<td>
											<a href="'.dims_urlencode($dims->getUrlPath().'?action=delete_fairs_delegue&id_event='.$id_evt.'&id_etap='.$etap_selected['id'].'&id_delegue='.$tab_deg['id'],false).'">
												<img src="./common/img/close.png" alt="'.$_DIMS['cste']['_DIMS_DELETE'].'" style="border:none;"/>
											</a>
										</td>
									</tr>';
							}
							echo '<tr>
									<td colspan="5" align="center" style="padding-top:10px;">
										<input type="button" class="submit" onclick="javascript:dims_switchdisplay(\'form_1\');" value="'.$_DIMS['cste']['_ADD_DELEGUE_STAND'].'"/>
									</td>
								  </tr>
								</table>';
						}
						else {
							$disp = "block";
						}

						/****************************/
						/* Affichage du formulaire **/
						/****************************/

						if (!isset($_SESSION['dims']['tmp_nb_insc'])) $_SESSION['dims']['tmp_nb_insc']=1;
						$nb_form = 1;
						$nb_form = dims_load_securvalue('nb_form', dims_const::_DIMS_NUM_INPUT, false, true, false, $_SESSION['dims']['tmp_nb_insc'],$nb_form);
						?>

						<div id="form_1" style="display:<?php echo $disp; ?>;">
								<?php
								//Formulaire niv.1 * nb_inscrip (Pour les personnes s'inscrivant a plusieurs)
								echo  $_DIMS['cste']['_DIMS_EVT_INSCRIPT'];
								global $dims;
								?>

							<form action="<? echo dims_urlencode($dims->getUrlPath().'?action=save_fairs_delegue&id_event='.$id_evt.'&id_etap='.$etap_selected['id'],false); ?>" method="POST" id='form_inscrip_niv1' name="form_inscrip_niv1">
								<?
									// Sécurisation du formulaire par token
									require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
									$token = new FormToken\TokenField;
									$token->field("nb_inscrip",		$nb_form);
									$token->field("del_lastname");
									$token->field("del_firstname");
									$token->field("del_date_presence");
									$token->field("del_date_presence_fin");
									$token->field("del_email");
									$token->field("del_mobile");
									$tokenHTML = $token->generate();
									echo $tokenHTML;
								?>
								<input type="hidden" name="nb_inscrip" value="<?php echo $nb_form ?>" />

								<?php
								//Verification nombre d'inscription positif
								if($nb_form < 1)
									$nb_form = 1;

								$control=false;
								for($i = 0; $i < $nb_form; $i++) {
								?>
								<div class="inscriptions">
									<div class="info_oblig">
										<table>
											<tr>
												<td>
													<label for="del_lastname"><?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?> <span style="color:#FF0000">*</span></label>
												</td>
											</tr>
											<tr>
												<td>
													<input type="text" name="del_lastname" id="del_lastname" value="" class="content"/>
												</td>
											</tr>
											<tr>
												<td>
													<label for="del_firstname"><?php echo $_DIMS['cste']['_DIMS_LABEL_FIRSTNAME']; ?> <span style="color:#FF0000">*</span></label>
												</td>
											</tr>
											<tr>
												<td>
													<input type="text" name="del_firstname" id="del_firstname"	value="" class="content"/>
												</td>
											</tr>
											<tr>
												<td>
													<label for="del_firstname"><?php echo $_DIMS['cste']['_FAIRS_DATE_PRESENCE']; ?> <span style="color:#FF0000">*</span></label>
												</td>
											</tr>
											<tr>
												<td>
													<table width="100%">
														<tr>
															<td><? echo $_DIMS['cste']['_FROM']." "; ?></td>
															<td>
																<select name="del_date_presence" id="del_date_presence" class="content">
																	<option value="">--</option>
																	<?php
																		$sql_d = 	"SELECT datejour
																					FROM dims_mod_business_action
																					WHERE (id = :idaction OR id_parent = :idaction )
																					ORDER BY datejour ASC";
																		$res_d = $db->query($sql_d, array(
																			':idaction' => $id_evt
																		));
																		while($tabd = $db->fetchrow($res_d)) {

																			$date_tmstp = str_replace('-','',$tabd['datejour'])."000000";
																			$date_ymd = dims_timestamp2local($date_tmstp);

																			echo '<option value="'.$date_tmstp.'">'.$date_ymd['date'].'</option>';
																		}
																	?>
																</select>
															</td>
														</tr>
														<tr>
															<td><? echo $_DIMS['cste']['_DIMS_LABEL_A']." "; ?></td>
															<td>
																<select name="del_date_presence_fin" id="del_date_presence_fin" class="content">
																	<option value="">--</option>
																	<?php
																		$sql_d = 	"SELECT datejour
																					FROM dims_mod_business_action
																					WHERE (id = :idaction OR id_parent = :idaction )
																					ORDER BY datejour ASC";
																		$res_d = $db->query($sql_d, array(
																			':idaction' => $id_evt
																		));
																		while($tabd = $db->fetchrow($res_d)) {

																			$date_tmstp = str_replace('-','',$tabd['datejour'])."000000";
																			$date_ymd = dims_timestamp2local($date_tmstp);

																			echo '<option value="'.$date_tmstp.'">'.$date_ymd['date'].'</option>';
																		}
																	?>
																</select>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</div>
									<div class="info_compl">
										<table>
											<tr>
												<td>
													<label for="del_email"><?php echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?> <span style="color:#FF0000">*</span></label>
												</td>
											</tr>
											<tr>
												<td>
													<input type="text" name="del_email" id="del_email" value="" class="content"/>
												</td>
											</tr>
											<tr>
												<td>
													<label for="del_mobile"><?php echo $_DIMS['cste']['_DIMS_LABEL_MOBILE']; ?> <span style="color:#FF0000">*</span></label>
												</td>
											</tr>
											<tr>
												<td>
													<input type="text" name="del_mobile" id="del_mobile" value="" class="content"/><br />
												</td>
											</tr>
										</table>
									</div>
									<p style="clear: both;">
										<span style="color:#FF0000">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span>
									</p>
								</div>
									<?php
									}
									?>
									<div class="save">
									<?php
											echo '<input type="button" value="Submit >" class="submit" onclick="javascript:verif_form();"/>';
									?>
								</div>
							</form>
						</div>
						<?
						echo '</div>';
					}

					if($etap_selected['type_etape'] == 5) { //cas d'une etape de type paiement

						$date_p = '';
						$date_f = '';

						echo	'<div>
									<table cellpadding="10">
										<tr>';
						if(!empty($etap_selected['date_facturation'])) {
							$tab_date_f = dims_timestamp2local($etap_selected['date_facturation']);
							$date_f = $tab_date_f['date'];

							echo '<td align="right">'.$_DIMS['cste']['_DIMS_LABEL_FAIRS_DATE_FAC'].' : </td>
								  <td align="left">'.$date_f.'</td>';
						}
						if(!empty($etap_selected['date_paiement'])) {
							$tab_date_p = dims_timestamp2local($etap_selected['date_paiement']);
							$date_p = $tab_date_p['date'];

							echo '<td align="right">'.$_DIMS['cste']['_DIMS_LABEL_FAIRS_DATE_PAIEMENT'].' : </td>
								  <td align="left">'.$date_p.'</td>';

						}

						if($etap_selected['paiement']) {
							//paiement validé
							echo '<td align="center">'.$_DIMS['cste']['_DIMS_FAIR_VALIDATED_PAIEMENT'].'</td>';
						}
						else {
							//paiement non validé
							echo '<td align="center">'.$_DIMS['cste']['_DIMS_FAIR_NO_PAIEMENT'].'</td>';
						}
						echo '		</tr>
								</table>
							</div>';
					}

					if($etap_selected['valide_etape'] == 2 && !empty($etap_selected['date_valid_etape'])) {
						$date_str = dims_timestamp2local($etap_selected['date_valid_etape']);

						echo '<div class="validate">'.$_DIMS['cste']['_DIMS_LABEL_STEP'].' '.strtolower($_DIMS['cste']['_DIMS_LABEL_VALIDATE_ON']).
						' '.$date_str['date'].'</div>';
					}
				}
			}
			echo '</div>
				</div>';
		}
?>
</div>
<?php
	}

	$organizer = new contact();
	$organizer->open($evt->fields['id_organizer']);

	echo '<div class="contactus">'
			.$_DIMS['cste']['_DIMS_CONTACT_US'].' : ';
	if(!empty($organizer->fields['email']))
		echo '<a href="mailto:'.$organizer->fields['email'].'?subject=I-net Event : '.$evt->fields['libelle'].'">
				'.$organizer->fields['email'].'
				</a>&nbsp;';
	if(!empty($organizer->fields['phone']))
		echo $organizer->fields['phone'];
	echo '</div>';

	echo '<div id="back_home">';
	echo '<input type="button" class="submit" value="'.$_DIMS['cste']['_DIMS_BACK'].' >" onclick="javascript: location.href=\'index.php\';" />';
	//echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_BACK_TO_HOME'],'./common/img/undo.gif','javascript: href.location="index.php";');
	echo '</div></div>';
?>
