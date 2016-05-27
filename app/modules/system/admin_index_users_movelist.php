<?
if (isset($reset))
{
	$pattern = '';
	$idtype = '';
}
if (!isset($idtype)) $idtype = '';
if (!isset($pattern)) $pattern = '';

$types = system_gettypes();
?>

<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
<form method="post" action="<? echo "$scriptenv" ?>">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("pattern");
	$token->field("idtype");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<TR>
	<TD>
	<TABLE CELLPADDING=2 CELLSPACING=0>
	<TR>
		<TD><? echo $_DIMS['cste']['_DIMS_LABEL_USER']; ?> :</TD>
		<TD><input class="text" ID="system_user" name="pattern" type="text" size="15" maxlength="255" value="<? echo $pattern; ?>"></TD>
		<TD>&nbsp;&nbsp;<? echo $_DIMS['cste']['_TYPE']; ?> :</TD>
		<TD>
			<select class="select" name="idtype">
			<option value="0" selected><? echo $_DIMS['cste']['_DIMS_ALL']; ?></option>
			<?
			foreach ($types as $typeid => $typelabel)
			{
				if ($idtype == $typeid) $sel = 'selected';
				else $sel = '';
				echo "<option $sel value=\"$typeid\">$typelabel</option>";
			}
			?>
			</select>
		</TD>
		<TD><input type="submit" value="<? echo $_DIMS['cste']['_DIMS_FILTER']; ?>" class="flatbutton"></TD>
		<TD><input name="reset" type="submit" value="<? echo $_DIMS['cste']['_DIMS_RESET']; ?>" class="flatbutton"></TD>
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
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_FIRSTNAME']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LOGIN']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_ORIGIN']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	if (isset($pattern)) $pattern_search = "%$pattern%";
	else $pattern_search = "%";

	if (!isset($idtype)) $idtype = 0;

	if ($idtype != 0) $filter = " AND dims_user.id_type = $idtype ";
	else $filter = '';

	$parents = str_replace(';',',',$group->fields['parents']);

	$params = array(
		':parents' => $parents,
		':pattern_search' => $pattern_search,
		':filter' => $filter);
	$select =	"
			SELECT 		dims_user.id,
					dims_user.lastname,
					dims_user.firstname,
					dims_user.login,
					dims_user_type.label as type
			FROM 		dims_user,
					dims_group_user
			LEFT JOIN	dims_user_type
			ON		dims_user_type.id = dims_user.id_type
			WHERE 		dims_group_user.id_user = dims_user.id
			AND		dims_group_user.id_group IN (:parents)
			AND		dims_user.login <> 'root'
			AND		(dims_user.lastname LIKE :pattern_search
			OR 		dims_user.firstname LIKE :pattern_search
			OR 		dims_user.login LIKE :pattern_search )
			:filter
			GROUP BY	dims_user.id
			ORDER BY	dims_user.lastname, dims_user.firstname, dims_user.login
			";

	$result = $db->query($select, $params);

	$currentusers = $group->getusers();

	while ($fields = $db->fetchrow($result)) {
		if (!array_key_exists($fields['id'],$currentusers)) {
			$user = new user();
			$user->fields['id'] = $fields['id'];
			$groups = $user->getgroupsadmin();
			$currentgroup = current($groups);

			echo 	"
				<TR>
					<TD ALIGN=\"CENTER\">$fields[lastname]</TD>
					<TD ALIGN=\"CENTER\">$fields[firstname]</TD>
					<TD ALIGN=\"CENTER\">$fields[login]</TD>
					<TD ALIGN=\"CENTER\">$fields[type]</TD>
					<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?groupid=$currentgroup[id]\">$currentgroup[label]</A></TD>
					<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=move_user&userid=$fields[id]\">".$_DIMS['cste']['_DIMS_LABEL_MOVE']."</A></TD>
				</TR>
				";
		}
	}

	?>
	</TABLE>
	</TD>
<TR>

</TABLE>
