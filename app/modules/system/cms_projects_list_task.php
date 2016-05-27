<?php
require_once(DIMS_APP_PATH . '/modules/system/include/projects_functions.php');
require_once(DIMS_APP_PATH . '/modules/system/class_project.php');

/* On regarde si des filtres sont posés */
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

/* On regarde si un trie doit être fait */
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

// get selected project users
$lstuserstask=$project->getUsersByTask();

// calcul de ce qui a été fait
$tabtasksmade=$project->getStatusByTask(1);

// calcul de ce qui va etre programme
$tabtasksexpected=$project->getStatusByTask(0);

/* Requête SQL de selection */
$sql =	"select		t.id,
					t.label,
					t.date_start,
					t.date_end,
					t.priority,
					t.state,
					t.progress,
					t.time,
					u.lastname,
					u.firstname
		from		dims_task as t
		inner join	dims_user as u
		on			u.id=t.id_user
		and			t.id_project= :idproject and t.type=1 ".$where."".$orderby."";

/* On execute la requete */
$rs = $db->query($sql, array(
	':idproject' => $_SESSION['dims']['currentproject']
));

/* On affiche les resultats de la requete */
$cpt=0;

echo '<table style="width:100%;background:#FFFFFF; font-size:12px;" cellpadding="5" cellspacing="0">
	<tr class="fontgray" style="text-align:center;font-weight: bold; color:#6699CC;">
	<td class="tds" style="width:20%">'.$_DIMS['cste']['_DIMS_LABEL'].'</td>
	<td class="tds" style="width:4%">'.$_DIMS['cste']['_FORM_TASK_PRIORITY'].'</td>
	<td class="tds" style="width:7%">'.$_DIMS['cste']['_DIMS_LABEL_PROGRESS'].'</td>
	<td class="tds" style="width:7%">'.$_DIMS['cste']['_FORM_TASK_START_DATE'].'</td>
	<td class="tds" style="width:7%">'.$_DIMS['cste']['_END'].'</td>
	<td class="tds" style="width:7%">'.$_DIMS['cste']['_FORM_TASK_TIME_TODO'].'</td>
	<td class="tds" style="width:7%">'.$_DIMS['cste']['_FORM_TASK_TIME'].'</td>
	<td class="tds" style="width:7%">'.$_DIMS['cste']['_FORM_TASK_TIME_EXPECTED'].'</td>
	<td class="tds" style="width:11%">'.$_DIMS['cste']['_FORM_TASK_TIME_ENABLED'].'</td>';
echo '<td class="tds" style="width:3%">'.$_DIMS['cste']['_DIMS_OPTIONS'].'</td></tr>';

while ($fields = $db->fetchrow($rs)) {

	/* Changement de skin 1 ligne sur 2 */
	if (isset($_SESSION['dims']['currenttask']) && $_SESSION['dims']['currenttask']>0 && $_SESSION['dims']['currenttask']!=$fields['id']) {
		echo '	<tr class="projects_row_disabled">';
	}
	else {
		if ($cpt % 2 == 1)
			echo '	<tr class="trl1">';
		else
			echo '	<tr class="trl2">';
	}

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

	echo '<td class="tds" ><a href="'.$scriptenv.'?idtask='.$fields['id'].'">'.$fields['label'].'</a></td>';
	echo "<td class=\"tds\" ><span style=\"display: inline-block;margin:2px;height:10px;width:50px;background-color:$priority\">&nbsp;</span></td>";
	echo "<td class=\"tds\" style=\"text-align:center;\">";
	echo "<span style=\"font-family:'trebuchet MS',Helvetica; font-size: 12px;\">";
	echo display_avancement($fields['progress']);
	echo "</span></td>";
	echo "<td class=\"tds\" style=\"text-align:center;\">".$datestart."</td>";
	echo "<td class=\"tds\" style=\"text-align:center;\">".$dateend."</td>";
	echo "<td class=\"tds\" style=\"text-align:center;\">".$fields['time']." ".$_DIMS['cste']['_DIMS_LABEL_DAYS']."</td>";

	// calcul du fait et prevu
	$cptemade=0;
	$cpteexpected=0;
	if (isset($tabtasksmade[$fields['id']])) $cptemade=$tabtasksmade[$fields['id']];
	if (isset($tabtasksexpected[$fields['id']])) $cpteexpected=$tabtasksexpected[$fields['id']];

	$rest=$fields['time']-$cptemade;
	// calcul si possible ou non => 2 cas soit plus possible avec les ressources affectées
	// ou assez dangereux
	$icon="";
	if ($rest-$cpteexpected) {
		// deja pas assez de jours affectes pour finir
		$icon="<img src=\"./common/img/alert1.gif\" alt=\"\">";
	}
	if (isset($lstuserstask[$fields['id']])) $nbuser=sizeof($lstuserstask[$fields['id']]);
	else $nbuser=0;
	// analyse du nombre de jour qu'il reste entre les deux dates => diff
	// on supprime les jours de repos
	$k = intval($diff/7);
	$nbconges = $k * (7 - _PLANNING_WORKING_DAYS);
	// on ne peut meme pas finir avec les ressources que l'on a => tres dangereux
	$possible=0;
	$chpossible="";

	if ($diff-$nbconges>0) $possible=($diff-$nbconges)*$nbuser;

	if ($possible<$rest) $icon="&nbsp;<img src=\"./common/img/alert2.gif\" alt=\"\">";

	echo "<td class=\"tds\" style=\"text-align:center;\">".$cptemade."</td>";
	echo "<td class=\"tds\" style=\"text-align:center;\">".$cpteexpected."</td>";
	// test si jour alloue
	if ($fields['time']>0) {
		// test si date de fin est supérieure a la date de jour courante
		if ($datecur_timestp>$datefin_timestp) {
			if (abs($diff) > (7 - _PLANNING_WORKING_DAYS)) $chpossible = $_DIMS['cste']['_INFOS_TOLATE']." > ".$_DIMS['cste']['_DIMS_LABEL_A']." ".(intval(abs($diff)/7))." ".$_DIMS['cste']['_DIMS_LABEL_WEEKS'];
			else $chpossible = $_DIMS['cste']['_INFOS_TOLATE']." ".$_DIMS['cste']['_FROM']." ".abs($diff);
		}
		else {
			if ($possible<0) $chpossible = $_DIMS['cste']['_INFOS_END_DATE']." + ".abs($possible);
			elseif ($rest-$possible!=0) $chpossible = $_DIMS['cste']['_DIMS_NEEDED']." ".($rest-$possible)." ".$_DIMS['cste']['_DIMS_LABEL_DAYS'];
			else {
				// calcul si prevu et fait bien réparti ou non
				$quot_date = ($diff*100)/$difftotal;
				$quot_made = ($cptemade*100)/$fields['time'];

				if (abs($quot_made-$quot_date)>10) {// 10% de dépassement
					$icon.="<img src=\"./common/img/alert1.gif\" alt=\"\">";
					$chpossible = $_DIMS['cste']['_DIMS_TOLATE_PREVIEW'];
				}
			}
		}
	}

	echo "<td class=\"tds\">$icon&nbsp;$chpossible</td>";

	//<img src=\"./common/img/user.gif\" alt=\"\"/>&nbsp;".strtoupper(substr($fields['firstname'],0,1)).". ".$fields['lastname']."</td>";

	echo '<td class="tds">';
	if(!isset($lstuserstask[$fields['id']][0][$_SESSION['dims']['userid']]))

		echo '<a href="#" onclick="javascript:dims_confirmlink(\''.dims_urlencode($scriptenv.'?task_id='.$fields['id'].'&action=ask_attach_task').'\',\''.$_DIMS['cste']['_DIMS_PROJECT_ASK_ATTACH'].'\');"><img style="border:0px;" src="./common/img/add_user.png" alt="'.$_DIMS['cste']['_DIMS_PROJECT_ASK_ATTACH'].'"></a>';

	echo '</td>';
	echo '</tr>';
	$cpt++;
}

echo "</table>";

?>
