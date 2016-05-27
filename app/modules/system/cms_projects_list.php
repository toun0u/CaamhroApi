<div style="padding:2px;">
	<div style="width:5%;float:left;">
		<img src="/common/modules/sharefile/img/properties.png">
	</div>
	<div style="width:auto;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
		<?php echo $_DIMS['cste']['_DIMS_PROJECTS']; ?>
	</div>
</div>
<?php
//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PROJECT'],'100%');

require_once(DIMS_APP_PATH . '/modules/system/include/projects_functions.php');

$where = '';
$orderby = '';

/* On regarde si des filtres sont posés */
if (isset($_GET['filtertype'])) $_SESSION['projects']['filtertype'] = dims_load_securvalue('filtertype', dims_const::_DIMS_CHAR_INPUT, true, true, true);
if (!isset($_SESSION['projects']['filtertype'])) $_SESSION['projects']['filtertype'] = 'all';

$filtertype = $_SESSION['projects']['filtertype'];

switch($filtertype)	{

	default:
	case 'all':
		$where = ' 1 ';
		break;

	case 'current':
		$where = " p.state='0'";
		break;

	case 'close':
		$where = " p.state='1'";
		break;

}


/* On regarde le parametre du trie */
if (isset($_GET['sort'])) $_SESSION['projects']['sort'] = dims_load_securvalue('sort', dims_const::_DIMS_CHAR_INPUT, true, true, true);
if (!isset($_SESSION['projects']['sort'])) $_SESSION['projects']['sort'] = 'name';

$sort = $_SESSION['projects']['sort'];

/* Permet de changer l'orde d'affichage du trie si on click une seconde fois sur le m�me trie */
if (!isset($order)) $order = 'desc';
if (!isset($op)) $op = '';

if ($order=="asc") {
	$order="desc";
} else {
	$order="asc";
}

switch($sort)	{

	case 'name':
		$orderby = " order by p.label ".$order;
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
}

/* Requête SQL de selection */
//$sql = 'SELECT
//			p.id,
//			p.label,
//			p.progress,
//			u1.lastname,
//			u1.firstname,
//			concat(u2.lastname, " ", u2.firstname) AS resp,
//			p.date_start,
//			p.date_end,
//			p.state
//		FROM
//			dims_project p,
//			dims_user u1,
//			dims_user u2
//		WHERE
//				p.id_create = u1.id
//			AND
//				p.id_resp=u2.id
//			'.$where.'
//			'.$orderby;
$sql = 'SELECT
			p.id,
			p.label,
			p.progress,
			u1.lastname,
			u1.firstname,
			concat(u2.lastname, " ", u2.firstname) AS resp,
			p.date_start,
			p.date_end,
			p.state
		FROM
			dims_project p
		INNER JOIN
			dims_user u1
			ON
				p.id_create = u1.id
		INNER JOIN
			dims_user u2
			ON
				p.id_resp=u2.id
		INNER JOIN
			dims_project_user pu
			ON
				pu.id_project = p.id
			AND
				pu.id_ref = :userid
		WHERE
			'.$where.'
			'.$orderby;


/* On execute la requete */
$rs = $db->query($sql, array(
	':userid' => $_SESSION['dims']['userid']
));

/* On affiche les resultats de la requete */
$cpt=0;
echo '<table style="width:80%;" cellpadding="0" cellspacing="0">';
echo '<tr class="title"><td>'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td><td>'.$_DIMS['cste']['_DIMS_LABEL_PROGRESS'].'</td><td>'.$_DIMS['cste']['_DIMS_LABEL_RESPONSIBLE'].'</td></tr>';
if($db->numrows($rs)) {
	while ($fields = $db->fetchrow($rs)) {

		/* Changement de skin 1 ligne sur 2 */
		if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0 && $_SESSION['dims']['currentproject']!=$fields['id']) {
			echo '<tr class="projects_row_disabled">';
		}
		else {
			if ($cpt % 2 == 1)
				echo '<tr class="trl1">';
			else
				echo '<tr class="trl2">';
		}

		/* On récupère la valeur des champs. Si celle-ci est vide on le signale */
		echo '<td class="tds" style="width:35%;">';
		echo '<a href="'.dims_urlencode($scriptenv.'?idproject='.$fields['id']).'">'.$fields['label'].'</a></td>';

		echo '<td class="tds" style="width:30%;">';
		echo '<span style="float:left;font-size:12px;">';
		echo display_avancement($fields['progress']);
		echo '</span>';
		echo '</td><td class="tds" style="width:35%;">';
		echo '<img src="./common/img/user.gif" alt=""/>&nbsp;'.strtoupper(substr($fields['firstname'],0,1)).'. '.$fields['lastname'];

		//echo '<div style="float:right;width:80px;text-align:center;"><a href="'.$scriptenv.'?op=project_view_gantt&idproject='.$fields['id'].'""><img src="./common/modules/system/img/open_16.png"></a></div>'
		//.$etat.'</div>';
		echo '</td></tr>';
		$cpt++;
	}
}
else {
	echo '<tr><td colspan="3" align="center">'.$_DIMS['cste']['_DIMS_FRONT_PROJECT_NONE'].'</td></tr>';
}
echo '</table>';

?>
