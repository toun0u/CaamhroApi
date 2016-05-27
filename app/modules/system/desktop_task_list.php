<?php
require_once(DIMS_APP_PATH . '/modules/system/include/projects_functions.php');

/* On regarde si des filtres sont posés */

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

$orderby = '';
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

$user= new user();
$user->open($_SESSION['dims']['userid']);

// get selected project users
$lstuserstask=$user->getTasks();
$lstprojects = $user->getProjects();

$filtreproject=dims_load_securvalue("filtreproject",_DIMS_NUM_INPUT,true,false);

/* construction du select sur le fitre des projets en cours */
echo "<form name=\"frmfilterproject\" action=\"\">";
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("filtreproject");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo 	"<p style=\"width100%;text-align:center;\">"._DIMS_PROJECT_FILTER."&nbsp;
		<select onchange=\"document.frmfilterproject.submit();\" name=\"filtreproject\" id=\"filtreproject\">";
echo "<option value=\"0\">"._DIMS_ALL."</option>";

$sele="";
foreach ($lstprojects as $prj) {
	if ($filtreproject==$prj['id']) $sele="selected";
	else $sele="";

	echo "<option $sele value=\"".$prj['id']."\">".$prj['label']."</option>";
}
echo "</select></p>";

/* On affiche les resultats de la requete */
$cpt=0;

echo "<table style=\"width:100%\" cellpadding=\"0\" cellspacing=\"0\">
	<tr class=\"fontgray\" style=\"text-align:center;\">
	<td class=\"tds\" style=\"width:25%;\">"._PROJECT_LABEL_NAME_PROJECT."</td>
	<td class=\"tds\" style=\"width:25%;\">"._DIMS_TASK."</td>
	<td class=\"tds\" style=\"width:10%;\">"._FORM_TASK_PRIORITY."</td>
	<td class=\"tds\" style=\"width:15%;\">"._FORM_TASK_START_DATE."</td>
	<td class=\"tds\" style=\"width:15%;\">"._FORM_TASK_END_DATE."</td></tr>";

foreach ($lstuserstask as $fields) {
	/* Changement de skin 1 ligne sur 2 */
	if (($filtreproject>0 && $filtreproject==$fields['id_project']) || $filtreproject==0) {
		if ($cpt % 2 == 1)
				echo '	<tr class="projects_row1">';
			else
				echo '	<tr class="projects_row2">';

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

		/* On récupère la valeur des champs. Si celle-ci est vide on le signale */
		echo "<td class=\"tds\"><a href=\"".dims_urlencode($scriptenv."?dims_mainmenu="._DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&idproject=".$fields['id_project'])."\">".$fields['labelproject']."</a></td>";
		echo "<td class=\"tds\"><a href=\"".dims_urlencode($scriptenv."?dims_mainmenu="._DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&idproject=".$fields['id_project']."&idtask=".$fields['id'])."\">".$fields['label']."</a></td>";
		echo "<td class=\"tds\"><span style=\"margin:2px;height:10px;width:50px;background-color:$priority\">&nbsp;</span></td>";
		echo "<td class=\"tds\" style=\"text-align:center;\">".$datestart."</td>";
		echo "<td class=\"tds\" style=\"text-align:center;\">".$dateend."</td>";

		echo "</tr>";
		$cpt++;
	}
}
echo "</table></form>";

/* On ferme le bloc (=la page) */

?>
