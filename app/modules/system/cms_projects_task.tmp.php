<?php
require_once DIMS_APP_PATH . '/modules/system/class_project.php';

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

$priority = '';

switch ($task->fields['priority']) {
	case  0:
		$priority = $_DIMS['cste']['_FORM_TASK_PRIORITY_0'];
	break;
	case  1:
		$priority = $_DIMS['cste']['_FORM_TASK_PRIORITY_1'];
	break;
	case  2:
		$priority = $_DIMS['cste']['_FORM_TASK_PRIORITY_2'];
	break;
}

/* Le contenu de la page (essentielement le formulaire) */
echo '<div class="projects_form">
<form id="form_task" name="form_task" method="POST">';

// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("action", "task_save");
$token->field("task_progress");
$token->field("task_time");
$token->field("task_description");
$tokenHTML = $token->generate();
echo $tokenHTML;

echo '<input type="hidden" name="action" value="task_save">
<div style=\"display:block;float:left;width:45%;\">
<div class="projects_form">
<div class="label">'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</div>
<div class="data">'.$task->fields['label'].'
</div>
<div class="label">'.$_DIMS['cste']['_FORM_TASK_START_DATE'].'</div>
<div class="data">'.$datestart.'
</div>
<div class="label">'.$_DIMS['cste']['_END'].'</div>
<div class="data">'.$dateend.'
</div>
</div>
<div class="projects_form">
<div class="label">'.$_DIMS['cste']['_FORM_TASK_PRIORITY'].'</div>
<div class="data">'.$priority.'
</div>
</div>
<div class="projects_form">
	<div class="label">'.$_DIMS['cste']['_DIMS_LABEL_PROGRESS'].'</div>
	<div class="data">
		<select name="task_progress">
		';

	for($i=0;$i<=100;$i+=10) {
		if ($task->fields['progress']==$i) $selected="selected";
		else $selected="";

		echo "<option value=\"$i\" $selected>".$i."</option>";
	}

echo '
		</select>
	</div>
	<div class="label">'.$_DIMS['cste']['_FORM_TASK_DURATION'].'</div>
	<div class="data">
	<input type="text" name="task_time" size="4" value="'.$task->fields['time'].'">'.$_DIMS['cste']['_DIMS_LABEL_DAYS'].'
	</data>
</div>
<div class="projects_form">
	<div style="width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</div><div style="float:left;width:400px;"><textarea class="text" type="text" cols="42" rows="3" name="task_description">'.$task->fields['description'].'</textarea></div>
</div>
	<div class="projects_form">';
	echo '<div style="float:left;">';
	echo '<input type="submit" class="submit" value="'.$_DIMS['cste']['_DIMS_SAVE'],' >" onclick="javascript:document.form_task.submit();" />';
	echo '</div>';
	echo '<div style="float:left;">';
	echo '<input type="submit" class="submit" value="'.$_DIMS['cste']['_DIMS_BACK'],' >" onclick="javascript:document.location.href='.$scriptenv.'&idtask=-1;" />';
	echo '</div>';

echo '
	</div>
</div>
</div>
</form>
</div>
<div style=\"display:block;float:left;width:55%;\">';

// construction de la liste des affectations des users
//require_once(DIMS_APP_PATH . "/modules/system/class_workspace.php");
$ws= new workspace();
$ws->open($_SESSION['dims']['workspaceid']);

echo "<table width=\"100%\"><tr><td>";
echo $_DIMS['cste']['_DIMS_LABEL_USER_AFFECT'];
echo "<div id=\"usersaffected\" style=\"height:120px;width:100%;overflow:auto;visibility:visible;display:block;\">";

// open current project
$project=new project();
$project->open($_SESSION['dims']['currentproject']);

// get selected project users
$lstusers=$project->getUsers();
// get selected current users
if (isset($task->fields['id']) && $task->fields['id']>0) $tabselusers=$task->getUsers();
else $tabselusers=array();

if (sizeof($lstusers)>0) {
	echo "<table width=\"100%\">";
	foreach ($lstusers as $id=>$user) {
		if(isset($tabselusers[$user['id']]))
			echo "<tr><td>".strtoupper(substr($user['firstname'],0,1)).". ".$user['lastname']."</td></tr>";
	}
	echo "</table>";
}
echo '</div>';

echo "</td></tr></table>
</div>";
echo '<div style="clear: both; width: auto;">';
if (isset($task->fields['id']) && $task->fields['id']>0) {
	$_SESSION['dims']['template_path'] = DIMS_APP_PATH . './common/templates/backoffice/dims_lfb';
	echo $skin->open_simplebloc('');
		require_once DIMS_APP_PATH.'include/functions/annotations.php';
	dims_annotation(dims_const::_SYSTEM_OBJECT_TASK, $task->fields['id'], $task->fields['label'], $_SESSION['dims']['userid'], $_SESSION['dims']['workspaceid'],$task->fields['id_module'],false);
	echo $skin->close_simplebloc();
}
echo '</div>';

?>
