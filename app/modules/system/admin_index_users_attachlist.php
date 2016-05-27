<?php
$alpha=dims_load_securvalue('alpha',dims_const::_DIMS_NUM_INPUT,true,true);
$reset=dims_load_securvalue('reset',dims_const::_DIMS_NUM_INPUT,true,true);

if (isset($reset) && $reset==1) {
	$pattern = '';
	unset($_SESSION['system_alphasel']);
}

if (isset($alpha) && $alpha!='') $_SESSION['system_alphasel'] = $alpha;

if (!isset($_SESSION['system_alphasel'])) {

	switch ($_SESSION['system_level']) {
		case dims_const::_SYSTEM_GROUPS :
			$params = array( ':groupid' => $groupid );
			$select = 	"
					SELECT 		count(dims_user.id) as nbuser
					FROM 		dims_user

					INNER JOIN	dims_group_user ON dims_group_user.id_user = dims_user.id
					AND			dims_group_user.id_group = :groupid
					";
		break;
		case dims_const::_SYSTEM_WORKSPACES :
			$params = array( ':workspaceid' => $workspaceid);
			$select = 	"
					SELECT 		count(dims_user.id) as nbuser
					FROM 		dims_user

					INNER JOIN	dims_workspace_user ON dims_workspace_user.id_user = dims_user.id
					AND			dims_workspace_user.id_workspace = :workspaceid
					";
			break;
	}
	$res=$db->query($select, $params);

	$_SESSION['system_alphasel'] = 1;

	if ($fields = $db->fetchrow($res))
	{
		if ($fields['nbuser'] < 25) $_SESSION['system_alphasel'] = 99;
	}
}

$pattern=dims_load_securvalue('pattern',dims_const::_DIMS_CHAR_INPUT,true,true);
if (!isset($pattern)) $pattern = '';

if ($pattern != '') $_SESSION['system_alphasel'] = 99; // tous
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

	echo $skin->create_tabs('',$tabs_char,$_SESSION['system_alphasel']);
	?>
	</TD>
<TR>

<form method="post" action="<? echo "$scriptenv" ?>" name="form_users_attachlist">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("pattern");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<TR>
	<TD>
	<TABLE CELLPADDING=2 CELLSPACING=0>
	<TR>
		<TD><? echo $_DIMS['cste']['_DIMS_LABEL_USER']; ?> :</TD>
		<TD><input class="text" ID="system_user" name="pattern" type="text" size="15" maxlength="255" value="<? echo $pattern; ?>"></TD>
		<TD><? echo dims_create_button($_DIMS['cste']['_DIMS_FILTER'],"","form_users_attachlist.submit();"); ?></TD>
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
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_ORIGIN']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	$params=array();
	$where = array();

	if ($_SESSION['system_alphasel'] == 99) // tous ou recherche
	{
		if ($pattern != '') {
			$where[] .=  "(dims_user.lastname LIKE :pattern OR dims_user.firstname LIKE :pattern OR dims_user.login LIKE :pattern)";
			$params[':pattern']=$pattern."%";
		}
	}
	else
	{
		$where[] = "dims_user.lastname LIKE :alpha";
		$params[':alpha']=chr($_SESSION['system_alphasel']+96)."%";
	}


	switch ($_SESSION['system_level'])
		{
		case dims_const::_SYSTEM_GROUPS :

				// filtrage sur les groupes partagés

				if (!empty($groups['list'][$groupid]['groups']) && !dims_isadmin()) $where[] = "dims_group_user.id_group IN (".implode(',',$groups['list'][$groupid]['groups']).")";


				$where = (empty($where) ) ? '' : ' '.implode(' AND ', $where);

				//$where = (empty($where) ) ? ' WHERE ' : ' ';
				if ($where !='') $where.=" AND ";
				//else $where= " WHERE ";
				$where.= " dims_user.login !=''";

				//$params = array( ':whereclause' => $where );
				$select =	"
						SELECT 		dims_user.id,
									dims_user.lastname,
									dims_user.firstname,
									dims_user.login

						FROM 		dims_user";

				if (dims_isadmin()) $select.=" LEFT JOIN ";
				else $select.=" INNER JOIN ";

				$select.="			dims_group_user
						ON			dims_group_user.id_user = dims_user.id

						WHERE		".$where."

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
								<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?system_level=org&dims_moduleicon="._SYSTEM_ICON_USERS."&system_usertabid="._SYSTEM_TAB_USERLIST."&groupid=$currentgroup[id]&alpha=".(ord(strtolower($fields['lastname']))-96)."\">".$currentgroup['label']."</a></TD>
								<TD ALIGN=\"CENTER\"><a href=\"$scriptenv?op=attach_user&userid=$fields[id]\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_attach.png\" border=\"0\" alt=\"".$_DIMS['cste']['_DIMS_LABEL_ATTACH']."\"></a></TD>
							</TR>
							";
					}
				}
		break;
		case dims_const::_SYSTEM_WORKSPACES :
				// filtrage sur les groupes partagés
				if (!empty($workspaces['list'][$workspaceid]['groups']) && !dims_isadmin()) $where[] = "dims_group_user.id_group IN (".implode(',',$workspaces['list'][$workspaceid]['groups']).")";

				$where = (empty($where)) ? '' : 'WHERE '.implode(' AND ', $where);

				//$params = array( ':where' => $where );
				$select =	"
						SELECT 		dims_user.id,
									dims_user.lastname,
									dims_user.firstname,
									dims_user.login

						FROM 		dims_user

						INNER JOIN	dims_group_user
						ON			dims_group_user.id_user = dims_user.id

						".$where."

						GROUP BY	dims_user.id
						ORDER BY	dims_user.lastname, dims_user.firstname, dims_user.login
						";

				$result = $db->query($select, $params);

				$currentusers = $workspace->getusers();

				while ($fields = $db->fetchrow($result)) {

						$user = new user();
						$user->fields['id'] = $fields['id'];
						$groups = $user->getgroupsadmin();
						$currentgroup = current($groups);
						echo 	"
							<TR>
								<TD ALIGN=\"CENTER\">$fields[lastname]</TD>
								<TD ALIGN=\"CENTER\">$fields[firstname]</TD>
								<TD ALIGN=\"CENTER\">$fields[login]</TD>
								<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?system_level=org&dims_moduleicon="._SYSTEM_ICON_USERS."&system_usertabid="._SYSTEM_TAB_USERLIST."&groupid=$currentgroup[id]&alpha=".(ord(strtolower($fields['lastname']))-96)."\">$currentgroup[label]</A></TD>";

						if (!array_key_exists($fields['id'],$currentusers))
							echo "<TD ALIGN=\"CENTER\"><a href=\"$scriptenv?op=attach_user&userid=$fields[id]\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_attach.png\" border=\"0\" title=\"".$_DIMS['cste']['_DIMS_LABEL_ATTACH']."\"  alt=\"".$_DIMS['cste']['_DIMS_LABEL_ATTACH']."\"></a></TD>";
						else
							echo "<TD ALIGN=\"CENTER\"><a href=\"$scriptenv?op=attach_user&userid=$fields[id]\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_already_attach.png\" title=\"".$_DIMS['cste']['_DIMS_LABEL_ALREADYATTACH']."\" border=\"0\" alt=\"".$_DIMS['cste']['_DIMS_LABEL_ATTACH']."\"></a></TD>";
						echo "</TR>";
				}
		break;
	}


	?>
	</TABLE>
	</TD>
<TR>

</TABLE>
