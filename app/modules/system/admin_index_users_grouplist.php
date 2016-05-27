<?
$alpha=dims_load_securvalue('alpha',dims_const::_DIMS_NUM_INPUT,true,true);
$reset=dims_load_securvalue('reset',dims_const::_DIMS_NUM_INPUT,true,true);

if (isset($reset)) {
	$pattern = '';
	$idtype = '';
	unset($_SESSION['system_alphasel_group']);
}

$pattern=dims_load_securvalue('pattern',dims_const::_DIMS_CHAR_INPUT,true,true);
if (isset($alpha)) $_SESSION['system_alphasel_group'] = $alpha;

if (!isset($_SESSION['system_alphasel_group']))
{
	$params = array( ':workspaceid' => $workspaceid );
	$select = 	"
				SELECT 		count(dims_group.id) as nbgroup

				FROM 		dims_group

				INNER JOIN	dims_workspace_group
				ON			dims_workspace_group.id_group = dims_group.id
				AND			dims_workspace_group.id_workspace = :workspaceid
				";

	$res=$db->query($select, $params);

	$_SESSION['system_alphasel_group'] = 1;

	if ($fields = $db->fetchrow($res))
	{
		if ($fields['nbgroup'] < 25) $_SESSION['system_alphasel_group'] = 99;
	}
}

if (!isset($idtype)) $idtype = '';

if ($pattern != '' ||  $idtype != '') $_SESSION['system_alphasel_group'] = 99; // tous
?>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD>
	<?
	$tabs_char = array();

	for($i=1;$i<27;$i++)
	{
		$tabs_char[$i]['title'] = chr($i+64);
		$tabs_char[$i]['url'] = "$scriptenv?alpha=$i";
	}
	$tabs_char[99]['title'] = "&nbsp;tous&nbsp;";
	$tabs_char[99]['url'] = "$scriptenv?alpha=99";

	echo $skin->create_tabs('',$tabs_char,$_SESSION['system_alphasel_group']);
	?>
	</TD>
<TR>
<form method="post" action="<? echo "$scriptenv" ?>" name="form_users_grouplist">
<?
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("pattern");
$tokenHTML = $token->generate();
echo $tokenHTML;

$params=array();
$where = array();


$params[':workspaceid']['value']=$workspaceid;
$params[':workspaceid']['type']=PDO::PARAM_INT;

if ($_SESSION['system_alphasel_group'] == 99) // tous ou recherche
{
	unset($_SESSION['system_alphasel_group']);
	if ($pattern != '') {
		$where[] =  "dims_group.label LIKE :pattern";
		$params[':pattern']="%".$pattern."%";
	}
}
else {
	$where[] = "dims_group.label LIKE :alpha";
	$params[':alpha']=chr($_SESSION['system_alphasel_group']+96)."%";
}

$where = (empty($where)) ? '' : 'AND '.implode(' AND ', $where);

$select = 	"
		SELECT 		distinct dims_group.id,
					dims_group.label,
					dims_group.parents,
					dims_profile.label as profile,
					dims_workspace.id as idref,
					dims_workspace.label as labelworkspace,
					dims_workspace_group.adminlevel

		FROM 		dims_group

		INNER JOIN	dims_workspace_group
		ON			dims_workspace_group.id_group = dims_group.id
                ".$where."

		INNER JOIN	dims_workspace
		ON			dims_workspace.id = dims_workspace_group.id_workspace
		AND			dims_workspace.id = :workspaceid

		LEFT JOIN	dims_profile
		ON			dims_profile.id = dims_workspace_group.id_profile

		ORDER BY	dims_group.label";

?>
<TR>
	<TD>
	<TABLE CELLPADDING=2 CELLSPACING=0>
	<TR>
		<TD><? echo $_DIMS['cste']['_GROUP']; ?> :</TD>
		<TD><input class="text" ID="system_user" name="pattern" type="text" size="15" maxlength="255" value="<? echo $pattern; ?>"></TD>
		<TD><? echo dims_create_button($_DIMS['cste']['_DIMS_FILTER'],"","form_users_grouplist.submit();"); ?></TD>
		<!--<TD><? echo dims_create_button($_DIMS['cste']['_DIMS_RESET'],"","form_users_grouplist.submit();"); ?></TD>-->
	</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD>
	<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
	<?
	echo 	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_GROUP']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_PARENTS']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_LEVEL']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_PROFIL']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	$result = $db->query($select, $params);
	$user = new user();

	while ($fields = $db->fetchrow($result))
	{
		$action = "<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=detach_group&org_id=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMGROUPDETACH']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_cut.png\" alt=\"".$_DIMS['cste']['_DIMS_LABEL_DETACH']."\" border=\"0\"></A>";

		$level = $dims_system_levels[$fields['adminlevel']];

		if ($_SESSION['dims']['adminlevel'] >= $fields['adminlevel'])
			$manage_grp = 	"<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=modify_group&org_id=$fields[id]\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" border=\"0\" alt=\"".$_DIMS['cste']['_MODIFY']."\"></A>&nbsp;&nbsp;$action</TD>";
		else
			$manage_grp = 	"<TD ALIGN=\"CENTER\"><img src=\"./common/modules/system/img/ico_noway.gif\" ALT=\"\">&nbsp;&nbsp;<img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_noway.png\" alt=\"\"></TD>";

		$group = new group();
		$array_parents = $group->getparents($fields['parents']);

		$str_parents = '';
		$c = 0;
		foreach($array_parents as $parent)
		{
			if ($c++)
			{
				if ($str_parents != '') $str_parents .= ' > ';
				$str_parents .= $parent['label'];
			}
		}

		echo 	"
				<TR>
					<TD ALIGN=\"CENTER\">".$fields['label']."-".$fields['idref']."</TD>
					<td>$str_parents</td>
					<TD ALIGN=\"CENTER\">$level</TD>
					<TD ALIGN=\"CENTER\">{$fields['profile']}</TD>
					$manage_grp
				</TR>
				";
	}

	?>
	</TABLE>
	</TD>
<TR>

</TABLE>
