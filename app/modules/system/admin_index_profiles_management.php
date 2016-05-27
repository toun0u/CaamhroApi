<?
// all available modules

$ownprofiles = $workspace->getprofiles();

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_PROFILES_AVAILABLE'],'100%');
?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<?
	$color='';//$skin->values['bgline1'];
	echo 	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_LABEL']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_DEFAULT_PROFILE']."</TD>
                        <TD ALIGN=\"CENTER\">".$_DIMS['cste']['_SHARE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	foreach ($ownprofiles AS $index => $profile) {
		$shared = "<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

		$p_green = "<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";

		if ($profile['shared']) $shared = $p_green;
		$modify =  "<a href=\"$scriptenv?tab=profiles&op=modify_profile&profileid={$profile['id']}\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" align=\"middle\" border=\"0\"></a>" ;
		//$delete = "<a href=\"$scriptenv?tab=profiles&op=delete&moduleid={$profile['id']}\">" . '<img src="./common/modules/system/img/ico_delete.gif" align="middle" border="0\"></a>';
		$delete = "<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=delete_profile&profileid={$profile['id']}','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMPROFILEDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_delete.png\" align=\"middle\" border=\"0\"></A>";
		if($profile['def']==0)
			$defaut='<img src="./common/modules/system/img/ico_point_red.gif" align="middle">';
		else
			$defaut='<img src="./common/modules/system/img/ico_point_green.gif" align="middle">';

		echo 	"
			<TR>
				<TD ALIGN=\"CENTER\">$profile[label]</TD>
				<TD ALIGN=\"CENTER\">".dims_strcut($profile['description'],15)."</TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_defaultprofile&profile_id={$profile['id']}\">$defaut</a></TD>
                <TD ALIGN=\"CENTER\"><a href=\"?op=switch_sharedprofile&profile_id={$profile['id']}\">$shared</a></TD>";
                if ($profile['id_workspace']==$_SESSION['system_workspaceid']) {
                    echo "<TD ALIGN=\"CENTER\">$modify&nbsp;/&nbsp;$delete</TD>";
                }
                else {
                    echo "<td>&nbsp;</td>";
                }

		echo	"</TR>";

	}
	?>
	</TABLE>
	</TD>
</TR>
</TABLE>
<?
echo $skin->close_simplebloc();
?>