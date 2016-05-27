
<div style="padding:2px;">
	<span style="width:10%;display:block;float:left;">
		<img src="/common/modules/sharefile/img/properties.png">
	</span>

</div>
<?php
//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PROJECT'],'100%');

require_once(DIMS_APP_PATH . '/modules/system/include/projects_functions.php');

$admview = dims_load_securvalue("admview", dims_const::_DIMS_NUM_INPUT, true, true);

/* On regarde si des filtres sont pos�s */
$where = '';
switch($filtertype)	{

	case 'all':
	break;

	case 'current':
		$where = " and p.state='0'";
	break;

	case 'close':
		$where = " and p.state='1'";
	break;

}

/* On regarde si un trie doit �tre fait */

$orderby = '';
switch($sort)	{

	case 'name':
		$orderby = " order by label ".$order;
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

/* Requ�te SQL de selection */
$params = array();
$sql =	"select		p.id,
					p.label,
					p.progress,
					p.type,
					u1.lastname,
					u1.firstname,
					concat(u2.lastname, ' ', u2.firstname) as resp,
					concat(u3.lastname, ' ', u3.firstname) as resp2,
					concat(u4.lastname, ' ', u4.firstname) as resp3,
					p.date_start,
					p.date_end,
					p.state
		from		dims_project p
		inner join	dims_user u1
		on			u1.id = p.id_create
		left join	dims_user u2
		on			p.id_resp = u2.id
		left join	dims_user u3
		on			p.id_resp2 = u3.id
		left join	dims_user u4
		on			p.id_resp3 = u4.id
		where		p.id_workspace = :idworkspace ";
		$params[':idworkspace'] = $_SESSION['dims']['workspaceid'];

		if(!dims_isadmin() || $admview != 1) {
			$sql .= " and		(
						p.id_create = :userid
					or
						p.id_resp = :userid
					or
						p.id_resp2 = :userid
					or
						p.id_resp3 = :userid
					) ";
			$params[':userid'] = $_SESSION['dims']['userid'];
		}

		$sql .= $where." ".$orderby;

/* On execute la requete */
$rs = $db->query($sql, $params);

/* On affiche les resultats de la requete */
$cpt=0;
echo "<table style=\"width:80%;\" cellpadding=\"0\" cellspacing=\"0\">";
if(dims_isadmin()) {

	if($admview) {
		$checkadm = 'checked="checked"';
		$val_adm = "0";
	}
	else $val_adm = "1";
	echo "<tr>
			<td colspan=\"4\">
				<form id=\"form_adm\" name=\"form_adm\" method=\"post\" action=\"\">";
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("admview");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
	echo 			"<input type=\"checkbox\" id=\"admview\" name=\"admview\" value=\"$val_adm\" $checkadm onclick=\"javascript:document.form_adm.submit();\"/> Afficher tous les projets
				</form>
			</td>
		</tr>";
}
echo "<tr class=\"title\"><td>".$_DIMS['cste']['_DIMS_LABEL_NAME']."</td><td>".$_DIMS['cste']['_TYPE']."</td><td>".$_DIMS['cste']['_DIMS_LABEL_PROGRESS']."</td><td>".$_DIMS['cste']['_DIMS_LABEL_RESPONSIBLE']."(s)</td></tr>";
while ($fields = $db->fetchrow($rs)) {

	/* Changement de skin 1 ligne sur 2 */
	/*if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0 && $_SESSION['dims']['currentproject']!=$fields['id']) {
		echo '	<tr class="projects_row_disabled">';
	}
	else {*/
		if ($cpt % 2 == 1)
			echo '<tr class="trl1">';
		else
			echo '<tr class="trl2">';
	//}

	/* On r�cup�re la valeur des champs. Si celle-ci est vide on le signale */
	echo "<td class=\"tds\" style=\"width:30%;\">";
		echo "<a href=\"".dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&idproject=".$fields['id'])."\">".$fields['label']."</a>
		</td>";
	echo "<td class=\"tds\" style=\"width:25%;\">";
		echo $fields['type']."</td>";
	echo "<td class=\"tds\" style=\"width:20%;\">";
		echo "<span style=\"float:left\">";
		echo display_avancement($fields['progress']);
		echo "</span>";
	echo "</td>
		<td class=\"tds\" style=\"width:25%;\">";
	echo "<img src=\"./common/img/user.gif\" alt=\"\"/>&nbsp;".strtoupper(substr($fields['firstname'],0,1)).". ".$fields['lastname'];
	if($fields['resp'] != '') echo "; ".$fields['resp'];
	if($fields['resp2'] != '') echo "; ".$fields['resp2'];
	if($fields['resp3'] != '') echo "; ".$fields['resp3'];
	echo "</td></tr>";

	$cpt++;
}
echo "</table>";
//echo $skin->close_simplebloc();

?>
