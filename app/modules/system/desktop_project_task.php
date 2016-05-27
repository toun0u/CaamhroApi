<script language="JavaScript" type="text/JavaScript">

function ticketDmdParticipation(id_user, id_project,id_task) {
	dims_xmlhttprequest('admin.php','dims_mainemenu=<? echo dims_const::_DIMS_MENU_PROJECTS; ?>&op=ticket_dmd_participation&id_dmdeur='+id_user+'&id_proj='+id_project+'&id_task='+id_task);
	alert("<? echo $_DIMS['cste']['_DIMS_LABEL_VALID_SEND_TICKET']; ?>");
}

	var timersearch;

	function upKeysearchLink(type) {
		clearTimeout(timersearch);
		timersearch = setTimeout("execSearchLink(\'"+type+"\')", 500);
	}

	function execSearchLink(type) {
		clearTimeout(timersearch);


		var nomsearch = dims_getelem('search_tiers').value;
		var divtoaffich = dims_getelem('dispres_searcht');

		if(nomsearch.length>=2) {
			dims_xmlhttprequest_todiv("admin.php", "op=search_linktoadd&search_name="+nomsearch+"&type_search="+type+"&contact_id=<? echo $contact_id; ?>", "", "dispres_searcht");
			divtoaffich.style.display = "block";
		}
	}
</script>
<?php
require_once DIMS_APP_PATH . '/modules/system/class_project.php';
require_once DIMS_APP_PATH . '/modules/system/class_tiers.php';

if (isset($_SESSION['dims']['currenttask']) && $_SESSION['dims']['currenttask']>0)
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_MODIFY'],'100%','','',false);
else
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_ADD'],'100%','','',false);

/* Si c'est une cr�ation � partir d'une autre t�che,
on initialise avec les dates correspondantes */
if (isset($position) && isset($date)) {

if ($position=="before") {

	$dateend = $date;

	/* - 1 mois */
	list($jour, $mois, $annee) = explode('/', $date);
	$jour = ($jour<10) ? "0".$jour:$jour;
	$mois = ($mois-1<1) ? 12:($mois-1);
	$mois = ($mois<10) ? "0".$mois:$mois;
	$annee = ($annee<10) ? "0".$annee:$annee;

	$datestart = $jour."/".$mois."/".$annee;
		} else {
	$datestart = $date;

	/* + 1 mois */
	list($jour, $mois, $annee) = explode('/', $date);
	$jour = ($jour<10) ? "0".$jour:$jour;
	$mois = ($mois+1>12) ? 01:($mois+1);
	$mois = ($mois<10) ? "0".$mois:$mois;
	$annee = ($annee<10) ? "0".$annee:$annee;

	$dateend = $jour."/".$mois."/".$annee;
}

/* Si c'est une cr�ation 'simple',
on initialise les date � la date du jour et la date du jour + 1 mois */
} else {

	if ($task->fields['date_start']!="") {
		$var=dims_timestamp2local($task->fields['date_start']);
		$datestart=$var['date'];
	}
	else $datestart=dims_getdate();

	if ($task->fields['date_end']!="") {
		$var=dims_timestamp2local($task->fields['date_end']);
		$dateend=$var['date'];
	}
	else $dateend=strftime ('%d/%m/%Y', strtotime ('+ 1 month'));
	$datef=$project->getNextDate();
	$var=dims_timestamp2local($datef);
	$datef=$var['date'];

	if ($task->fields['date_start']!="" && $task->fields['date_start']>0) {
		$var=dims_timestamp2local($task->fields['date_start']);
		$datestart=$var['date'];
	}
	else $datestart=$datef;

	if ($task->fields['date_end']!="" && $task->fields['date_end']>0) {
		$var=dims_timestamp2local($task->fields['date_end']);
		$dateend=$var['date'];
	}
	else $dateend=$datestart;//strftime ('%d/%m/%Y', strtotime ('+ 1 month'));
}

$sel0=$sel1=$sel2="";

switch ($task->fields['priority']) {
	case  0:
		$sel0="selected=\"selected\"";
		$lblp = '_FORM_TASK_PRIORITY_0';
	break;
	case  1:
		$sel1="selected=\"selected\"";
		$lblp = '_FORM_TASK_PRIORITY_1';
	break;
	case  2:
		$sel2="selected=\"selected\"";
		$lblp = '_FORM_TASK_PRIORITY_2';
	break;
}

//traitement des horaires

if(isset($task->fields['heuredeb']) && $task->fields['heuredeb'] != '') {
	$tab_hdeb = explode(":", $task->fields['heuredeb']);
}
if(isset($task->fields['heurefin']) && $task->fields['heurefin'] != '') {
	$tab_hfin = explode(":", $task->fields['heurefin']);
}

$modif = 0;
if (isset($_SESSION['dims']['currentproject'])) {
	$project = new project();
	$project->open($_SESSION['dims']['currentproject']);

	if($project->fields['id_create'] == $_SESSION['dims']['userid']
	   || $project->fields['id_resp'] == $_SESSION['dims']['userid']
	   || $project->fields['id_resp2'] == $_SESSION['dims']['userid']
	   || $project->fields['id_resp3'] == $_SESSION['dims']['userid']
	   ) {
		$modif = 1;
	}
}

//recherche d'autres taches  afin de pouvoir rattacher les taches entre elles
$sqlt = 'SELECT label, id, id_parent, state FROM dims_task WHERE id_project = :idproject ORDER BY type, date_start';
$rest = $db->query($sqlt, array(
	':idproject' => $_SESSION['dims']['currentproject']
));

/* Le contenu de la page (essentielement le formulaire) */
echo '<div class="projects_form">
		<form id="form_task" name="form_task" method="POST">';
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("op",			"task_save");
$token->field("task_type",	"1");
$token->field("task_label");
$token->field("task_hdeb_h");
$token->field("task_hdeb_m");
$token->field("task_date_start");
$token->field("task_date_end");
$token->field("task_priority");
$token->field("task_hfin_h");
$token->field("task_hfin_m");
$token->field("task_progress");
$token->field("task_time");
$token->field("task_nb_place");
$token->field("task_id_parent");
$token->field("task_description");
$token->field("search_tiers");
echo $token->generate();

echo 	'<input type="hidden" name="op" value="task_save">
		<input type="hidden" name="task_type" value="1">
			<table style=\"width:100%;background:#FFFFFF;\" cellpadding=\"5\" cellspacing=\"0\">
				<tr>
					<td width="34%">
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</div>
						<div style="float:left;width:180px;">';
							if($modif == 1 ) {
								echo '<input type="text" name="task_label" value="'.$task->fields['label'].'">';
							}
							else
								echo $task->fields['label'];
				echo	'</div>
					</td>
					<td width="33%">
						<div style="float:left;width:120px;">'.$_DIMS['cste']['_FORM_PROJECT_START_DATE'].'</div>
						<div style="float:left;width:120px;">';
							if($modif == 1 ) {
								echo '<input type="text" class="text" size="10" name="task_date_start" id="task_date_start" value="'.$datestart.'">&nbsp;
										<a href="#" onclick="javascript:dims_calendar_open(\'task_date_start\', event);">
											<img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0">
										</a>';
							}
							else
								echo $datestart;
				echo	'</div>
					</td>
					<td width="33%">
						<div style="float:left;width:120px;">'.$_DIMS['cste']['_DIMS_LABEL_HEUREDEB'].'</div>
						<div style="float:left;width:160px;">';
							if($modif == 1 ) {
								echo '<select name="task_hdeb_h" id="task_hdeb_h" style="width:55px;">
										<option value="">--</option>';
								for($i=0; $i<24; $i++) {
									if(isset($tab_hdeb) && $tab_hdeb[0] == $i) $sel = 'selected="selected"';
									else $sel = "";
									echo '<option value='.$i.' '.$sel.'>'.$i.'</option>';
								}
								echo '</select>';
								echo ' h ';
								echo '<select name="task_hdeb_m" id="task_hdeb_m" style="width:55px;">
										<option value="">--</option>';
								for($j=0; $j<60; $j+=5) {
									$val = substr('00'.$j,-2);
									if(isset($tab_hdeb) && $tab_hdeb[1] == $val) $sel = 'selected="selected"';
									else $sel = "";
									echo '<option value='.$val.' '.$sel.'>'.$val.'</option>';
								}
								echo '</select>';
							}
							else
								echo $tab_hdeb[0]." h ".$tab_hdeb[1];
				echo	'</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_FORM_TASK_PRIORITY'].'</div>
						<div style="float:left;width:180px;">';
							if($modif == 1 ) {
								echo	'<select name="task_priority">
											<option value="0" '.$sel0.'>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_0'].'</option>
											<option value="1" '.$sel1.'>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_1'].'</option>
											<option value="2" '.$sel2.'>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_2'].'</option>
										</select>';
							}
							else
								echo $_DIMS['cste'][$lblp];
				echo	'</div>
					</td>
					<td>
						<div style="float:left;width:120px;">'.$_DIMS['cste']['_END'].'</div>
						<div style="float:left;width:120px;">';
							if($modif == 1 ) {
								echo '<input type="text" class="text" size="10" name="task_date_end" id="task_date_end" value="'.$dateend.'">&nbsp;
										<a href="#" onclick="javascript:dims_calendar_open(\'task_date_end\', event);">
											<img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0">
										</a>';
							}
							else
								echo $dateend;
				echo	'</div>
					</td>
					<td>
						<div style="float:left;width:120px;">'.$_DIMS['cste']['_DIMS_LABEL_HEUREFIN'].'</div>
						<div style="float:left;width:160px;">';
							if($modif == 1 ) {
								echo '<select name="task_hfin_h" id="task_hfin_h" style="width:55px;">
										<option value="">--</option>';
								for($i=0; $i<24; $i++) {
									if(isset($tab_hfin) && $tab_hfin[0] == $i) $sel = 'selected="selected"';
									else $sel = "";
									echo '<option value='.$i.' '.$sel.'>'.$i.'</option>';
								}
								echo '</select>';
								echo ' h ';
								echo '<select name="task_hfin_m" id="task_hfin_m" style="width:55px;">
										<option value="">--</option>';
								for($j=0; $j<60; $j+=5) {
									$val = substr('00'.$j,-2);
									if(isset($tab_hfin) && $tab_hfin[1] == $val) $sel = 'selected="selected"';
									else $sel = "";
									echo '<option value='.$val.' '.$sel.'>'.$val.'</option>';
								}
								echo '</select>';
							}
							else
								echo $datestart;
				echo	'</div>
				</tr>
				<tr>
					<td>
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_PROGRESS'].'</div>
						<div style="float:left;width:180px;">
							<select name="task_progress">';

								for($i=0;$i<=100;$i+=10) {
									if ($task->fields['progress']==$i) $selected="selected";
									else $selected="";

									echo "<option value=\"$i\" $selected>".$i."</option>";
								}

					echo	'</select>
						</div>
					</td>
					<td>
						<div style="float:left;width:120px;">'.$_DIMS['cste']['_FORM_TASK_DURATION'].'</div>
						<div style="float:left;width:120px;">';
							if($modif == 1 ) {
								echo '<input type="text" name="task_time" size="4" value="'.$task->fields['time'].'">'.$_DIMS['cste']['_DIMS_LABEL_DAYS'];
							}
							else
								echo $task->fields['time'].'&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_DAYS'];
				echo	'</div>
					</td>
					<td>
						<div style="float:left;width:360px;">'.$_DIMS['cste']['_DIMS_PROJECT_NB_INTERV'].'</div>
						<div style="float:top;width:180px;padding-left:30px;">
							<input id="task_nb_place" name="task_nb_place" value="'.$task->fields['nb_place'].'">
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</div>
						<div style="float:left;width:400px;">';
							if($modif == 1 ) {
								echo '<textarea class="text" type="text" cols="42" rows="3" name="task_description">'.$task->fields['description'].'</textarea>';
							}
							else
								echo $task->fields['description'];
				echo	'</div>
					</td>';
		if($db->numrows($rest)>0) {
			echo	'<td>
						<div style="float:left;width:120px;">'.$_DIMS['cste']['_DIMS_LABEL_LINK_PARENT_TASK'].'</div>
						<div style="float:left;width:120px;">';
							if($modif == 1 ) {
								echo '<select name="task_id_parent">
											<option value="0"></option>';
								while($tabt = $db->fetchrow($rest)) {
									if ($task->fields['id_parent']==$tabt['id']) $selected="selected";
									else $selected="";

									echo	"<option value=\"".$tabt['id']."\" $selected>".$tabt['label']."</option>";
								}
								echo  '</select>';
							}
							else {
								if($task->fields['id_parent'] != 0) {
									$ptask = new task();
									$ptask->open($task->fields['id_parent']);
									echo $ptask->fields['label'];
								}
								else {
									echo "--";
								}
							}
				echo	'</div>
					</td><td></td>';
		}
		echo	'</tr>';
		if($task->fields['id_tiers'] == '' || $task->fields['id_tiers'] == '0') {
			echo '<tr>
					<td colspan="3">
						<table width="100%" border="0" cellpadding="5" cellspacing="0">
							<tr>
								<td align="right" width="40%">'.$_DIMS['cste']['_DIMS_LABEL_FIND_ENT_TO_LINK'].'</td>
								<td align="left">
									<input type="text" value="" onkeyup="javascript:upKeysearchLink(\'tiers\');" id="search_tiers" name="search_tiers"/>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="dispres_searcht" style="display:none;width:100%">

									</div>
								</td>
							</tr>
						</table>
						</td>
					</tr>';
		}
		else {
			//on recherche les infos de l'entreprise
			$tiers = new tiers();
			$tiers->open($task->fields['id_tiers']);
			echo '<tr>
						<td colspan="3">'.$_DIMS['cste']['_DIMS_LABEL_CONT_ENTRAT']." : ".$tiers->fields['intitule'].' <a href="admin.php?op=task_del_tiers&id_task='.$task->fields['id'].'" style="border:none;"><img src="./common/img/delete.png"/></a></td>
				  </tr>';
		}
		if($modif == 1) {
			echo	'<tr>
						<td colspan="3">
							<div style="float:left;">';
								echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.form_task.submit();","","");
					echo	'</div>';
					echo	'<div style="float:left;">';
								echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],"","javascript:document.location.href='".$scriptenv."?idtask=-1';","","");
					echo	'</div>
						</td>
					</tr>';
		}
		echo '</table>

		</form>
	</div>';

echo $skin->close_simplebloc();

/*echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_USER_AFFECT'],'100%','','',false);

$ws= new workspace();
$ws->open($_SESSION['dims']['workspaceid']);

$lstusers=$ws->getusers();

// construction des personnes affect�es � ce projet
$tabselusers=array();
//ATTENTION les types sont differents entre dims_task (0 pour phase 1 pour tache) et dims_task_user (1 pour phase 0 pour tache)
if (isset($_SESSION['dims']['currenttask'])) {
	$tsk = new task();
	$tsk->open($_SESSION['dims']['currenttask']);
	$tabselusers=$tsk->getUsers(0);
}

echo '<div style="width: 30%; float: left;">';
if (sizeof($lstusers)>0) {

	echo "<form id=\"form_affect\" name=\"form_affect\" method=\"POST\">
				<input type=\"hidden\" name=\"op\" value=\"task_affect_save\">
				<table width=\"100%\">";
				foreach ($lstusers as $id=>$user) {
					$select=(isset($tabselusers[$user['id']])) ? "checked" : "";
					echo	"<tr>
								<td width=\"5%\">
									<input name=\"useraffect[]\" value=\"".$user['id']."\" type=\"checkbox\" ".$select.">
								</td>
								<td>
									".strtoupper(substr($user['firstname'],0,1)).". ".$user['lastname']."
								</td>";
					echo	"</tr>";
				}
		echo	"</table>";
	if ($modif == 1 ) {
		echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.form_affect.submit();","","");
	}
	else {
		echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_PROJ_DMD_PARTICIPATE'],"./common/img/add_user.png","javascript:ticketDmdParticipation('".$_SESSION['dims']['userid']."','".$_SESSION['dims']['currentproject']."','".$_SESSION['dims']['currenttask']."');","","");
	}
	echo "</form>";
}
echo '</div>';

//Rattachement direct d'un contact
echo '<div style="float:left; width: 65%;">';
if ($modif == 1 ) {
	$type_rat = "task";
	require_once DIMS_APP_PATH.'modules/system/desktop_project_detail_contact.php';
}
echo '</div>';

echo $skin->close_simplebloc();*/

if($modif == 1 && isset($_SESSION['dims']['currenttask']) && $_SESSION['dims']['currenttask']>0) {
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_NEWSLETTER_LIST_DMDINSC']);

	//on selectionne toutes les demandes d'inscription relatives a la tache courante
	$sql = "SELECT		a.datejour,
						a.datefin,
						a.heuredeb,
						a.heurefin,
						au.*,
						u.lastname,
						u.firstname,
						u.id_contact,
						u.email
			FROM		dims_mod_business_action a

			INNER JOIN	dims_mod_business_action_utilisateur au
			ON			au.action_id = a.id

			INNER JOIN	dims_user u
			ON			u.id = au.user_id

			WHERE		a.id_task = :idtask
			ORDER BY	au.user_id ASC, a.datejour ASC";
//echo $sql;
	$res = $db->query($sql, array(
		':idtask' => $task->fields['id']
	));
	$nb_res = $db->numrows($res);
	if($nb_res > 0) {
		$tab_dmd = array();
		$nb_participate = 0;
		$nb_unvalid = 0;
		$nb_refused = 0;
		while($tab_d = $db->fetchrow($res)) {
			$tab_dmd[$tab_d['user_id']]['name'] = $tab_d['firstname']." ".$tab_d['lastname'];
			$tab_dmd[$tab_d['user_id']]['id_contact'] = $tab_d['id_contact'];
			$tab_dmd[$tab_d['user_id']]['email'] = $tab_d['email'];

			if(!isset($tab_dmd[$tab_d['user_id']]['etat']['valid'])) $tab_dmd[$tab_d['user_id']]['etat']['valid'] = 0;
			if(!isset($tab_dmd[$tab_d['user_id']]['etat']['unvalid'])) $tab_dmd[$tab_d['user_id']]['etat']['unvalid'] = 0;
			if(!isset($tab_dmd[$tab_d['user_id']]['etat']['refused'])) $tab_dmd[$tab_d['user_id']]['etat']['refused'] = 0;

			$tab_dmd[$tab_d['user_id']]['action'][$tab_d['action_id']]['date_deb'] = $tab_d['datejour'];
			$tab_dmd[$tab_d['user_id']]['action'][$tab_d['action_id']]['date_fin'] = $tab_d['datefin'];
			$tab_dmd[$tab_d['user_id']]['action'][$tab_d['action_id']]['h_deb'] = $tab_d['heuredeb'];
			$tab_dmd[$tab_d['user_id']]['action'][$tab_d['action_id']]['h_fin'] = $tab_d['heurefin'];
			$tab_dmd[$tab_d['user_id']]['action'][$tab_d['action_id']]['date_dmd'] = $tab_d['date_demande'];
			$tab_dmd[$tab_d['user_id']]['action'][$tab_d['action_id']]['participate'] = $tab_d['participate'];

			//repartition de l'etat des actions
			if($tab_d['participate'] == 1 || $tab_d['participate'] == 3) {
				$tab_dmd[$tab_d['user_id']]['etat']['valid'] += 1;
			}

			if($tab_d['participate'] == 0) {
				$tab_dmd[$tab_d['user_id']]['etat']['unvalid'] += 1;
			}

			if($tab_d['participate'] == 2) {
				$tab_dmd[$tab_d['user_id']]['etat']['refused'] += 1;
			}
		}
	}
	else {
		echo "Aucune demande d'inscription pour cette t&acirc;che.";
	}
//dims_print_r($tab_dmd);

	$tab_aff = '<div style="overflow:auto;width:100%;"><table width="100%" cellpadding="0" cellspacing="0">';
	$class = '#EEEEDD';
	foreach($tab_dmd as $user => $info) {
		$notall = 0;
		if($class == '#EEEEDD') $class = '#FFFFFF';
		else $class = '#EEEEDD';
//echo count($info['action'])."<br/>";
		$tab_aff .= '<tr style="background-color:'.$class.';">
						<td style="font-weight:bold;height:30px;">';
		//Affichage de l'etat de validation et des actions possibles
		if($info['etat']['valid'] == count($info['action'])) {
			$tab_aff .= '<img src="./common/modules/system/img/ico_point_green.gif"/>
							<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'view_'.$info['id_contact'].'\');" title="More details">'.$info['name'].' ('.count($info['action']).')</a>
						</td>
						<td>
							<a href="admin.php?op=refuse_all_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'""><img style="border:none;" src="./common/img/delete.png"/>refuse all</a>
						</td>';
		}
		elseif($info['etat']['refused'] == count($info['action'])) {
			$tab_aff .= '<img src="./common/modules/system/img/ico_point_red.gif"/>
							<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'view_'.$info['id_contact'].'\');" title="More details">'.$info['name'].' ('.count($info['action']).')</a>
						</td>
						<td>
							<a href="admin.php?op=valid_all_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'"><img style="border:none;" src="./common/img/checkdo.png"/>accept all</a>
						</td>';
		}
		elseif($info['etat']['unvalid'] == count($info['action'])) {
			$tab_aff .= '<img src="./common/modules/system/img/ico_point_grey.gif"/>
							<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'view_'.$info['id_contact'].'\');" title="More details">'.$info['name'].' ('.count($info['action']).')</a>
						</td>
						<td>
							<a href="admin.php?op=valid_all_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'"><img style="border:none;" src="./common/img/checkdo.png"/>accept all</a>&nbsp;/&nbsp;<a href="admin.php?op=refuse_all_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'""><img style="border:none;" src="./common/img/delete.png"/>refuse all</a>
						</td>';
		}
		else {
			$notall = 1;
			$tab_aff .= '<img src="./common/modules/system/img/ico_point_orange.gif"/>
							<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'view_'.$info['id_contact'].'\');" title="More details">'.$info['name'].' ('.count($info['action']).')</a>
						</td>
						<td>
							<a href="admin.php?op=valid_all_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'"><img style="border:none;" src="./common/img/checkdo.png"/>accept all</a>&nbsp;/&nbsp;<a href="admin.php?op=refuse_all_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'""><img style="border:none;" src="./common/img/delete.png"/>refuse all</a>
						</td>';
		}

		$tab_aff .=		'
					</tr>
					<tr style="background-color:'.$class.';">
						<td colspan="2" align="center">
							<div id="view_'.$info['id_contact'].'" style="display:none;">
							<table widht="90%" style="padding:10px;" width="90%">
								<tr style="background-color:'.$class.';">
									<th align="left"></th>
									<th align="left">Date de la demande</th>
									<th align="left">Date de l\'action</th>
									<th align="left">Heure d&eacute;but</th>
									<th align="left">Heure fin</th>
									<th align="left"></th>';
			foreach($info['action'] as $act_id => $tab_act) {
				$ddeb = explode('-',$tab_act['date_deb']);
				$dfin = explode('-',$tab_act['date_fin']);
				$hdeb = explode(':',$tab_act['h_deb']);
				$hfin = explode(':',$tab_act['h_fin']);
				$ddmd = dims_timestamp2local($tab_act['date_dmd']);
				if($class == '#EEEEDD') $class = '#FFFFFF';
				else $class = '#EEEEDD';
				$tab_aff .= '<tr style="background-color:'.$class.';">
								<td>';
				$do = '';
				switch($tab_act['participate']) {
					case 0: //en attente
						$tab_aff .= '<img src="./common/modules/system/img/ico_point_grey.gif"/>';
						$do = '<a href="admin.php?op=valid_act_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'&id_action='.$act_id.'"><img style="border:none;" src="./common/img/checkdo.png"/></a> /
								<a href="admin.php?op=refuse_act_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'&id_action='.$act_id.'"><img style="border:none;" src="./common/img/delete.png"/></a>';
						break;
					case 3: //Annule en front
					case 1: //accepte

						if($tab_act['participate'] == 3) $tab_aff .= '<img src="./common/img/warning.png"/>';

						$tab_aff .= '<img src="./common/modules/system/img/ico_point_green.gif"/>';
						$do = '<a href="admin.php?op=refuse_act_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'&id_action='.$act_id.'"><img style="border:none;" src="./common/img/delete.png"/></a>';
						break;
					case 2: //refuse
						$tab_aff .= '<img src="./common/modules/system/img/ico_point_red.gif"/>';
						$do = '<a href="admin.php?op=valid_act_insc&id_user='.$user.'&id_task='.$_SESSION['dims']['currenttask'].'&id_action='.$act_id.'"><img style="border:none;" src="./common/img/checkdo.png"/></a>';
						break;
				}
				$tab_aff .= '	</td>
								<td>'.$ddmd['date'].'</td>
								<td>'.$ddeb[2].'/'.$ddeb[1].'/'.$ddeb[0].'</td>
								<td>'.$hdeb[0].'h'.$hdeb[1].'</td>
								<td>'.$hfin[0].'h'.$hfin[1].'</td>
								<td>'.$do.'</td>
							</tr>';
			}
		$tab_aff .= '		</table>
						</td>
					</tr>';
	}
	$tab_aff .= '</table></div>';

	echo $tab_aff;


	echo $skin->close_simplebloc();
}

if (isset($task->fields['id']) && $task->fields['id']>0) {
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_COMMENTS']);
	require_once DIMS_APP_PATH.'include/functions/annotations.php';
		dims_annotation(dims_const::_SYSTEM_OBJECT_TASK, $task->fields['id'], $task->fields['label'], $_SESSION['dims']['userid'], $_SESSION['dims']['workspaceid'],$task->fields['id_module'],false);
	echo $skin->close_simplebloc();

	echo $skin->open_simplebloc($_DIMS['cste']['_DOCS']);

	$id_module = $task->fields['id_module'];
	$id_object = dims_const::_SYSTEM_OBJECT_TASK;
	$id_record = $task->fields['id'];;

		require_once DIMS_APP_PATH.'include/functions/files.php';
	// collecte des fichiers deja ins�r�s
	$lstfiles=dims_getFiles($dims,$id_module,$id_object,$id_record);
	echo dims_createAddFileLink($id_module,$id_object,$id_record,'float: left;');

	echo '<div style="float:left;clear:both;width:100%;"><table>';
	if (!empty($lstfiles)) {
		echo "<tr class=\"trl1\">
			<td style=\"width:30%;padding-left:10px;\">".$_DIMS['cste']['_DOCS']."</td>
			<td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</td>
			<td style=\"width:10%;\">".$_DIMS['cste']['_DIMS_LABEL_CREATE_ON']."</td>
			<td style=\"width:10%;\">".$_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']."</td>
			<td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
									<td></td>
		</tr>";
		$licolor=2;
		foreach ($lstfiles as $file) {
			if ($licolor==1) $licolor=2;
			else $licolor=1;
			$cdate = dims_timestamp2local($file['timestp_create']);
			$mdate = dims_timestamp2local($file['timestp_modify']);
			echo "<tr class=\"trl2\">
				<td style=\"cursor: default;padding-left:10px;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
					<a href=".$file['downloadlink']." title=\"Voir le document.\">"
					.$file['name'].
					"</a>
				</td>
				<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
					.$file['description'].
				"</td>
				<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
					.$cdate['date'].
				"</td>
				<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
					.$mdate['date'].
				"</td>
				<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
					.$file['firstname'].
					" "
					.$file['lastname'].
				"</td>
				<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">";
				if($file['id_user'] == $_SESSION['dims']['userid'] ||
				   $project->fields['id_create'] == $_SESSION['dims']['userid'] ||
				   $project->fields['id_resp'] == $_SESSION['dims']['userid']) {
					echo "<a href=\"javascript:void(0);\" onclick=\"javascript:delete_doc('".$file['id']."')\"><img src=\"./common/img/delete.png\"/></a>";
				}
			echo "</td>
			</tr>";
		}
	}

	echo '</table></div>';

	echo $skin->close_simplebloc();
}
?>
