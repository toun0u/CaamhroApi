<?php
require_once(DIMS_APP_PATH . '/modules/system/include/projects_functions.php');
require_once(DIMS_APP_PATH . '/modules/system/class_project.php');

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_TASKS'],'100%','','',false);

/*
 *	On regarde si des filtres sont pos�s */
if (!isset($filtertype)) $filtertype="";
$where = '';
switch($filtertype)	{

	case 'all':
	break;

	case 'current':
		$where = " and p.state='En cours'";
	break;

	case 'close':
		$where = " and p.state='Clos'";
	break;

}

/* On regarde si un trie doit �tre fait */
if (!isset($sort)) $sort='startdate';
$orderby = '';
$order="";
switch($sort)	{

	case 'name':
		//$orderby = " order by label ".$order;
		$orderby = " order by date_start,date_end";
	break;

	/* On est obliger d'avoir les dates en format US pour trier */
	case 'startdate':
		$orderby = "order by concat(right(date_start, 4),'-',mid(date_start, 4, 2),'-',left(date_start, 2)) ".$order;
	break;

	case 'enddate':
		$orderby = "order by concat(right(date_end, 4),'-',mid(date_end, 4, 2),'-',left(date_end, 2)) ".$order;
	break;

	case 'avancement':
		$orderby = " order by datediff(concat(right(date_start, 4),'-',mid(date_start, 4, 2),'-',left(date_start, 2)), concat(right(date_end, 4),'-',mid(date_end, 4, 2),'-',left(date_end, 2))) ".$order;
	break;

	default:
		$orderby = " order by date_start,date_end";
	break;
}

/* requete de construction des affectations */
// open current project
$project=new project();
$project->open($_SESSION['dims']['currentproject']);

//droit d'acces
$mod = 0;
if($project->fields['id_create'] == $_SESSION['dims']['userid']
   || $project->fields['id_resp'] == $_SESSION['dims']['userid']
   || $project->fields['id_resp2'] == $_SESSION['dims']['userid']
   || $project->fields['id_resp3'] == $_SESSION['dims']['userid']
   ) {
	$mod = 1;
}

// get selected project users
$lstuserstask=$project->getUsersByTask();

// calcul de ce qui a �t� fait
$tabtasksmade=$project->getStatusByTask(1);

// calcul de ce qui va etre programme
$tabtasksexpected=$project->getStatusByTask(0);

/* Requ�te SQL de selection */
$sql =	"select		t.id,
					t.label,
					t.date_start,
					t.date_end,
					t.priority,
					t.state,
					t.progress,
					t.time,
					p.label as label_parent,
					p.id as phase_id_parent,
					u.lastname,
					u.firstname
		from		dims_task as t
		inner join	dims_user as u
		on			u.id=t.id_user
		and			t.id_project= :idproject
		and			t.type=1
		left join	dims_task as p
		on			p.id = t.id_parent
		".$where." ".$orderby."";

/* On execute la requete */
$rs = $db->query($sql, array(
	':idproject' => $_SESSION['dims']['currentproject']
));

/* On affiche les resultats de la requete */
$cpt=0;

echo "<div style=\"margin:10px;display:block;float:right;\"><a href=\"$scriptenv?op=add_task&idtask=-1\"><img border=\"0\" src=\"./common/img/add.gif\"/>".$_DIMS['cste']['_DIMS_ADD']."</a></div>";
echo "<div style=\"clear:both;float:left;width:100%;\"><table style=\"width:100%;background:#FFFFFF;\" cellpadding=\"5\" cellspacing=\"0\">
	<tr class=\"trl1\" style=\"text-align:center;\">
		<td style=\"width:25%\" align=\"left\">".$_DIMS['cste']['_DIMS_LABEL']."</td>
		<td style=\"width:21%\" align=\"left\">".$_DIMS['cste']['_DIMS_LABEL_PARENTS']."</td>
		<td style=\"width:4%\" align=\"left\">".$_DIMS['cste']['_FORM_TASK_PRIORITY']."</td>
		<td style=\"width:9%\" align=\"left\">".$_DIMS['cste']['_FORM_PROJECT_START_DATE']."</td>
		<td style=\"width:9%\" align=\"left\">".$_DIMS['cste']['_END']."</td>
		<td style=\"width:9%\" align=\"left\">".$_DIMS['cste']['_DIMS_LABEL_PROGRESS']."</td>
		<td style=\"width:9%\" align=\"left\">".$_DIMS['cste']['_DIMS_LABEL_NBATTACH']."</td>
		<td style=\"width:9%\" align=\"left\">".$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NBDMDINSC']."</td>
		<td style=\"width:4%\" align=\"left\">".$_DIMS['cste']['_DIMS_OPTIONS']."</td>
	</tr>";
$class = 'trl1';
while ($fields = $db->fetchrow($rs)) {
	if($class == 'trl1') $class = 'trl2';
	else $class = 'trl1';
	/* Changement de skin 1 ligne sur 2 */
	if (isset($_SESSION['dims']['currenttask']) && $_SESSION['dims']['currenttask']>0 && $_SESSION['dims']['currenttask']!=$fields['id'])
		echo '<tr class="'.$class.'">';
	elseif ($cpt % 2 == 1)
		echo '<tr class="trl1">';
	else
		echo '<tr class="trl2">';


	if ($fields['date_start']!="") {
		$var=dims_timestamp2local($fields['date_start']);
		$datestart=$var['date'];
	}
	else $datestart="";

	if ($fields['date_end']!="") {
		$var=dims_timestamp2local($fields['date_end']);
		$dateend=$var['date'];
	}
	else $dateend="";

	switch ($fields['priority']) {
		case 0: $priority="#6abf50";
		break;
		case 1: $priority="#f3bd56";
		break;
		case 2: $priority="#cd1717";
		break;
	}

	$tabdateend=explode("/",$dateend);
	$datefin_timestp=mktime(0,0,0,$tabdateend[1],$tabdateend[0],$tabdateend[2]);
	$datecur_timestp=mktime(0,0,0,date('n'),date('d'),date('Y'));

	$tabdatestart=explode("/",$datestart);
	$datestart_timestp=mktime(0,0,0,$tabdatestart[1],$tabdatestart[0],$tabdatestart[2]);

	$diff=round((($datefin_timestp-$datecur_timestp)/86400),0);
	$difftotal=round((($datefin_timestp-$datestart_timestp)/86400),0);

	//on calcul le nombre de responsables
	//$nb_resp = 0;
	//$sql_r = "SELECT DISTINCT id_ref FROM dims_task_user WHERE id_task=".$fields['id'];

	//$res_r = $db->query($sql_r);
	//$nb_resp = $db->numrows($res_r);

	/* On r�cup�re la valeur des champs. Si celle-ci est vide on le signale */
	echo "<td class=\"tds\" ><a href=\"$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&idtask=".$fields['id']."\">".$fields['label']."</a></td>";
	echo "<td class=\"tds\" ><a href=\"$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&idtask=".$fields['phase_id_parent']."\">".$fields['label_parent']."</a></td>";
	echo "<td class=\"tds\" ><span style=\"margin:2px;height:10px;width:50px;background-color:$priority\">&nbsp;</span></td>";
	echo "<td class=\"tds\" style=\"text-align:center;\">".$datestart."</td>";
	echo "<td class=\"tds\" style=\"text-align:center;\">".$dateend."</td>";

	/*if (isset($lstuserstask[$fields['id']])) {
		$nbuser=sizeof($lstuserstask[$fields['id']][0]);
	}
	else $nbuser=0;
	echo "<td class=\"tds\" style=\"text-align:center;\">".$nbuser."</td>";*/
	echo "<td class=\"tds\" style=\"text-align:center;\">";
	echo "<span style=\"\">";
	echo display_avancement($fields['progress']);
	echo "</span></td>";
	//calcul du nombre d'inscrits
	$nb_insc = 0;
	$sqli = "	SELECT		DISTINCT au.user_id
				FROM		dims_mod_business_action a
				INNER JOIN	dims_mod_business_action_utilisateur au
				ON			au.action_id = a.id
				AND			au.participate = 1
				WHERE		a.id_task = :idtask ";

	$resi = $db->query($sqli, array(
		':idtask' => $fields['id']
	));
	$nb_insc = $db->numrows($resi);
	echo "<td class=\"tds\" style=\"text-align:center;\">".$nb_insc."</td>";

	//calcul du nombre de demandes d'inscription
	//en utilisant le distinct, on ne compte qu'une demande par user alors qu'il peut y en avoir pls
	$nb_dmd = 0;
	$sqld = "	SELECT		DISTINCT au.user_id
				FROM		dims_mod_business_action a
				INNER JOIN	dims_mod_business_action_utilisateur au
				ON			au.action_id = a.id
				AND			au.participate = 0
				WHERE		a.id_task = :idtask ";

	$resd = $db->query($sqld, array(
		':idtask' => $fields['id']
	));
	$nb_dmd = $db->numrows($resd);
	echo "<td class=\"tds\" style=\"text-align:center;\">".$nb_dmd."</td>";

	echo "<td class=\"tds\">";
	//ATTENTION : on affiche l'option de suppression uniquement si la tache courante n'est parente d'aucune autre tache
	$sql_vp = 'SELECT id FROM dims_task WHERE id_parent = :idparent AND type = 1 AND id_project = :idproject ';
	$res_vp = $db->query($sql_vp, array(
		':idparent' => $fields['id'],
		':idproject' => $_SESSION['dims']['currentproject']
	));
	if(!$db->numrows($res_vp) && $mod == 1) {
		echo '<a href="#" onclick="javascript:dims_confirmlink(\''.dims_urlencode($scriptenv.'?dims_mainmenu='.dims_const::_DIMS_MENU_PROJECTS.'&dims_desktop=block&dims_action=public&idtask='.$fields['id'].'&op=delete_task&task_type=1').'\',\''.$_DIMS['cste']['_DIMS_MSG_CONFIRMTASKDELETE'].'\');"><img style="border:0px;" src="./common/img/del.png"></a>';
	}
	else {
		echo "--";
	}

	echo "</td></tr>";
	$cpt++;
}
echo "</table></div>";

/* On ferme le bloc (=la page) */
echo $skin->close_simplebloc();


$isphase=false;

if (isset($_SESSION['dims']['currenttask']) && $_SESSION['dims']['currenttask']>0) {
	require_once DIMS_APP_PATH . '/modules/system/class_task.php';
	$task = new task();
	$task->open($_SESSION['dims']['currenttask']);
	$isphase=($task->fields['type']==1);
}

if ($isphase>0 || $op=="add_task") {
	require_once(DIMS_APP_PATH . '/modules/system/desktop_project_task.php');
	echo $skin->close_simplebloc();
}

?>
