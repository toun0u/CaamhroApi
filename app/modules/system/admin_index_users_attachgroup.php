<?
if (isset($reset))
{
	$pattern = '';
	$idtype = '';
}
if (!isset($idtype)) $idtype = '';
if (!isset($pattern)) $pattern = '';
?>

<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
<form method="post" action="<? echo "$scriptenv" ?>" name="form_users_attachgroup">
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
		<TD><? echo $_DIMS['cste']['_GROUP']; ?> :</TD>
		<TD><input class="text" ID="system_user" name="pattern" type="text" size="15" maxlength="255" value="<? echo $pattern; ?>"></TD>
		<TD><? echo dims_create_button($_DIMS['cste']['_DIMS_FILTER'],"","","","form_users_attachgroup.submit();"); ?></TD>
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
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	$lstgrpadmin=array();

	if (dims_isadmin())
	{
		// on  doit proposer la liste du groupe transversal si il y a
		$res=$db->query("select id from dims_group where system=1 and protected =1");

		if ($db->numrows($res)>0)
		{
			while ($gp=$db->fetchrow($res))
			{
				$objgroup=new group();
				$objgroup->open($gp['id']);
				$resar=$objgroup->getgroupchildren();
				foreach ($resar as $obg) array_push($lstgrpadmin,$obg['id']);
			}
		}

	}

	$lstgrpadmin=array_unique($lstgrpadmin);
	$params = array();

	if (!empty($workspaces['list'][$workspaceid]['groups'])){
		$arrayres=array();
		$arrayres=array_unique(array_merge($workspaces['list'][$workspaceid]['groups'],$lstgrpadmin));
		$arrayresid = $db->getParamsFromArray($arrayres, 'id', $params);
		$where[] = "id IN (".$arrayresid.")";
	}else{
		$lstgrpadmin[] = 0;
		$arrayresid = $db->getParamsFromArray($lstgrpadmin, 'id', $params);
		$where[] = "id IN (".$arrayresid.")";
	}
	if (isset($pattern) && !empty($pattern)){
		$params[':pattern'] = array('type'=>PDO::PARAM_STR,'value'=>"%{$pattern}%");
		$where[] = "dims_group.label LIKE :pattern";
	}

	$where = (empty($where)) ? '' : 'WHERE '.implode(' AND ', $where);

	$select =	"
				SELECT 		dims_group.id,
							dims_group.label,
							dims_group.parents
				FROM 		dims_group
				$where
				ORDER BY	dims_group.label
				";

	$result = $db->query($select, $params);

	$currentgroups = $workspace->getgroups();

	while ($fields = $db->fetchrow($result))
	{
		if (!array_key_exists($fields['id'],$currentgroups))
		{
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
					<TD ALIGN=\"CENTER\">$fields[label]</TD>
					<td>$str_parents</td>
					<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=attach_group&orgid=$fields[id]\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_attach.png\" border=\"0\" alt=\"".$_DIMS['cste']['_DIMS_LABEL_ATTACH']."\"></a></TD>
				</TR>
				";
		}
	}

	?>
	</TABLE>
	</TD>
<TR>

</TABLE>
