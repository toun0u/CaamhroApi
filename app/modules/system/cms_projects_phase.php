<?php
require_once DIMS_APP_PATH . '/modules/system/class_project.php';

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

}

$sel0=$sel1=$sel2="";
$lblp = "";
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

$modif = 0;
if (isset($_SESSION['dims']['currentproject'])) {
	$project = new project();
	$project->open($_SESSION['dims']['currentproject']);

	if($project->fields['id_create'] == $_SESSION['dims']['userid'] || $project->fields['id_resp'] == $_SESSION['dims']['userid']) {
		$modif = 1;
	}
}

//recherche d'autres phases pour le projet courant afin de pouvoir les rattacher entre elles
$sqlt = 'SELECT label, id, id_parent, state FROM dims_task WHERE type = 0 AND id_project = :idproject ORDER BY id';
$rest = $db->query($sqlt, array(
	':idproject' => $_SESSION['dims']['currentproject']
));

/* Le contenu de la page (essentielement le formulaire) */
echo '<div style="width:100%;float:left;">
		<form id="form_task" name="form_task" method="POST">';

// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("op",			"task_save");
$token->field("task_type",	"0");
$token->field("");
echo '	<input type="hidden" name="op" value="task_save">
		<input type="hidden" name="task_type" value="0">
			<table style=\"width:100%;background:#FFFFFF;\" cellpadding=\"5\" cellspacing=\"0\">
				<tr>
					<td>
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</div>
						<div style="float:left;width:180px;">';
							if($modif == 1 ) {
								echo '<input type="text" name="task_label" value="'.$task->fields['label'].'">';
								$token->field("task_label");
							}
							else
								echo $task->fields['label'];
				echo	'</div>
					</td>
					<td>
						<div style="float:left;width:90px;">'.$_DIMS['cste']['_FORM_TASK_START_DATE'].'</div>
						<div style="float:left;width:120px;">';
							if($modif == 1 ) {
								echo '<input type="text" class="text" size="8" name="task_date_start" id="task_date_start" value="'.$datestart.'">&nbsp;

										<a href="#" onclick="javascript:dims_calendar_open(\'task_date_start\', event);">
											<img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0">
										</a>';
								$token->field("task_date_start");
							}
							else
								echo $datestart;
				echo	'</div>
						<div style="float:left;width:90px;">'.$_DIMS['cste']['_END'].'</div>
						<div style="float:left;width:120px;">';
							if($modif == 1 ) {
								echo '<input type="text" class="text" size="8" name="task_date_end" id="task_date_end" value="'.$dateend.'">&nbsp;
										<a href="#" onclick="javascript:dims_calendar_open(\'task_date_end\', event);">
											<img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0">
										</a>';
								$token->field("task_date_end");
							}
							else
								echo $dateend;
				echo	'
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_FORM_TASK_PRIORITY'].'</div>
						<div style="float:left;width:180px;">';
							if($modif == 1 ) {
								echo '<select name="task_priority"><option value="0" '.$sel0.'>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_0'].'</option>
											<option value="1" '.$sel1.'>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_1'].'</option>
											<option value="2" '.$sel2.'>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_2'].'</option>
										</select>';
								$token->field("task_priority");
							}
							else
								echo $_DIMS['cste'][$lblp];
				echo	'

						</div>
					</td>
					<td>
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_PROGRESS'].'</div>
						<div style="float:left;width:180px;">
							<select name="task_progress">';
							$token->field("task_progress");
							for($i=0;$i<=100;$i+=10) {
								if ($task->fields['progress']==$i) $selected="selected";
								else $selected="";

								echo "<option value=\"$i\" $selected>".$i."</option>";
							}

					echo	'</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="float:left;width:90px;">'.$_DIMS['cste']['_FORM_TASK_DURATION'].'</div>';
							if($modif == 1 ) {
								echo '<input type="text" name="task_time" size="4" value="'.$task->fields['time'].'">&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_DAYS'];
								$token->field("task_time");
							}
							else
								echo $task->fields['time'].'&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_DAYS'];
				echo	'</div>
					</td>';
		if($db->numrows($rest)>0) {
			echo	'<td>
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_LINK_PARENT_PHASE'].'</div>
						<div style="float:left;width:180px;">';
							if($modif == 1 ) {
								echo '<select name="task_id_parent">
											<option value="0"></option>';
								$token->field("task_id_parent");
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
					</td>';
		}
		echo	'</tr>
				<tr>
					<td>
						<div style="float:left;width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</div>
						<div style="float:left;width:400px;">';
							if($modif == 1 ) {
								echo '<textarea class="text" type="text" cols="42" rows="3" name="task_description">'.$task->fields['description'].'</textarea>';
								$token->field("task_description");
							}
							else
								echo $task->fields['description'];
				echo	'</div>
					</td>
				</tr>
				<tr>
					<td>';

/*if (isset($_SESSION['dims']['currenttask']) && $_SESSION['dims']['currenttask']>0)
	echo '<div style="float:left;"><input class="button" type="submit" value="'.$_DIMS['cste']['_MODIFY'].'"></div>';
else
	echo '<div style="float:left;"><input class="button" type="submit" value="'.$_DIMS['cste']['_FORM_TASK_OK'].'"></div>';
*/
echo '<div style="float:left;">';
echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.form_task.submit();","","");
echo '</div>';
echo '<div style="float:left;">';
echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],"","javascript:document.location.href='".$scriptenv."?idphase=-1';","","");
echo '</div>';

//echo '
//	</div>
//</div>';

			echo	"</td>
				</tr>
			</table>";

$tokenHTML = $token->generate();
echo $tokenHTML;

echo	"</form>
	</div>";
echo $skin->close_simplebloc();

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_USER_AFFECT'],'100%','','',false);

$ws= new workspace();
$ws->open($_SESSION['dims']['workspaceid']);

$lstusers=$ws->getusers();

// construction des personnes affect�es � ce projet
$tabselusers=array();
//ATTENTION les types sont differents entre dims_task (0 pour phase 1 pour tache) et dims_task_user (1 pour phase 0 pour tache)
if (isset($_SESSION['dims']['currentphase'])) {
	$phase = new task();
	$phase->open($_SESSION['dims']['currentphase']);
	$tabselusers=$phase->getUsers(1);
}

echo '<div style="width: 30%; float: left;">';
if (sizeof($lstusers)>0) {
	echo "<form id=\"form_affect\" name=\"form_affect\" method=\"POST\">";

	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op");
	$token->field("useraffect");
	$tokenHTML = $token->generate();
	echo $tokenHTML;

	echo		"<input type=\"hidden\" name=\"op\" value=\"phase_affect_save\">
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
	echo "</form>";
}
echo '</div>';

//Rattachement direct d'un contact
echo '<div style="float:left; width: 65%;">';
if ($modif == 1 ) {
	$type_rat = "phase";
	require_once DIMS_APP_PATH.'modules/system/desktop_project_detail_contact.php';
}
echo '</div>';

echo $skin->close_simplebloc();

if (isset($task->fields['id']) && $task->fields['id']>0) {
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_COMMENTS']);
		require_once DIMS_APP_PATH.'include/functions/annotations.php';
	dims_annotation(dims_const::_SYSTEM_OBJECT_TASK, $task->fields['id'], $task->fields['label'], $_SESSION['dims']['userid'], $_SESSION['dims']['workspaceid'],$task->fields['id_module'],false);
	echo $skin->close_simplebloc();

	echo $skin->open_simplebloc($_DIMS['cste']['_DOCS']);

		require_once DIMS_APP_PATH.'include/functions/files.php';
	$id_module = $task->fields['id_module'];
	$id_object = dims_const::_SYSTEM_OBJECT_TASK;
	$id_record = $task->fields['id'];;

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
