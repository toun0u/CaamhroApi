<?php

/* Requête SQL : selection des utilisateurs*/
$sql = 	"select id, concat(lastname, ' ', firstname) as name from dims_user";

/* On execute la requete */
$rs = $db->query($sql);

/* On utilise les resultats de la requete pour le formulaire*/
$user_content="";
$user_crea="";
$detaildisplay="";
$detailhide="";

$create_user = new user();
$create_user->open($project->fields['id_create']);

$resp_user = new user();
$resp_user->open($project->fields['id_resp']);

if ($project->fields['date_start']!="") {
	$var=dims_timestamp2local($project->fields['date_start']);
	$datestart=$var['date'];
}
else $datestart=dims_getdate();

if ($project->fields['date_end']!="") {
	$var=dims_timestamp2local($project->fields['date_end']);
	$dateend=$var['date'];
}
else $dateend=strftime ('%d/%m/%Y', strtotime ('+ 1 month'));

/* Le contenu de la page (essentielement le formulaire) */
echo '<div style=\"width:100%;background:#FFFFFF;\">
	<input type="hidden" name="op" value="project_save">
	<table wdith=\"100%\" cellpadding=\"5\">
		<tr><td style="width: 275px;">
			'.$_DIMS['cste']['_FORM_PROJECT_LABEL'].'<br />
			'.$project->fields['label'].'
		</td><td>
			'.$_DIMS['cste']['_FORM_PROJECT_CREATOR'].'
			'.strtoupper(substr($create_user->fields['firstname'],0,1)).". ".$create_user->fields['lastname'].'<br />
			'.$_DIMS['cste']['_PROJECT_MENU_PERSON_IN_CHARGE'].'
			'.strtoupper(substr($resp_user->fields['firstname'],0,1)).". ".$resp_user->fields['lastname'].'
		</td></tr>';
echo '
		<tr>
			<td>
				'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].' :<br />
				'.$project->fields['description'].'
			</td>
			<td>
				'.$_DIMS['cste']['_FORM_PROJECT_START_DATE'].'
				'.$datestart.'<br />
				'.$_DIMS['cste']['_FORM_PROJECT_END_DATE'].'
				'.$dateend.'
			</td>
		</tr>
	</table>
</div>';

$ws= new workspace();
$ws->open($_SESSION['dims']['workspaceid']);

$lstusers=$ws->getusers();

// construction des personnes affectées à ce projet
$tabselusers=array();

if (isset($_SESSION['dims']['currentproject'])) {
	$project = new project();
	$project->open($_SESSION['dims']['currentproject']);
	$tabselusers=$project->getUsers();
}

echo '<div style="width: 30%; margin-top: 30px;">';
echo $_DIMS['cste']['_DIMS_LABEL_USER_AFFECT'];
if (sizeof($lstusers)>0) {
	echo "<table width=\"100%\">";
	foreach ($lstusers as $id=>$user) {
		if(isset($tabselusers[$user['id']]))
		echo "<tr><td>".strtoupper(substr($user['firstname'],0,1)).". ".$user['lastname']."</td></tr>";
	}
	echo "</table>";
}
echo '</div>';

?>
