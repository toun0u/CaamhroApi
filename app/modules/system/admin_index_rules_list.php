
<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
<form method="post" action="<? echo "$scriptenv" ?>">

<TR>
	<TD>
	<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
	<?
	echo 	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_LABEL']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_RULEFIELD']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_RULEOPERATOR']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_RULEVALUE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_PROFIL']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		<TR><TD COLSPAN=7></TD></TR>
		";

	$params = array( ':groupid' => $groupid );
	$select =	"
			SELECT 		dims_rule.*, dims_rule_type.label as labeltype, dims_profile.id as idprofile, dims_profile.label as labelprofile
			FROM 		dims_rule, dims_rule_type
			LEFT JOIN	dims_profile
			ON		dims_rule.id_profile = dims_profile.id
			WHERE 		dims_rule.id_group = :groupid
			AND dims_rule.id_type= dims_rule_type.id
			order by dims_rule.id_type
			";

	$result = $db->query($select, $params);

	while ($fields = $db->fetchrow($result))
	{

		$action = "<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=delete_rule&rule_id=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMRULEDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/img/ico_delete.gif\" ALT=\"".$_DIMS['cste']['_DELETE']."\" border=\"0\"></A>";

		echo 	"
			<TR>
			<TD ALIGN=\"CENTER\">".$fields['label']."</TD>
			<TD ALIGN=\"CENTER\">".$fields['labeltype']."</TD>
			<TD ALIGN=\"CENTER\">".$fields['field']."</TD>
			<TD ALIGN=\"CENTER\">".$fields['operator']."</TD>
			<TD ALIGN=\"CENTER\">".$fields['value']."</TD>";

			if ($fields['idprofile']>0)
				echo "<TD ALIGN=\"CENTER\">".$fields['labelprofile']."</TD>";
			else
				echo "<TD ALIGN=\"CENTER\">-</TD>";

		echo "<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=modify_rule&rule_id=".$fields['id']."\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/img/crayon.gif\" border=\"0\" ALT=\"".$_DIMS['cste']['_MODIFY']."\"></A>&nbsp;&nbsp;$action</TD>
			</TR>
			";
	}

	?>
	</TABLE>
	</TD>
<TR>

</TABLE>
