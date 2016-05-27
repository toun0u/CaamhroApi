<?
if (isset($_SESSION['dims']['current_object'])) {
	$cur_moduleid=$_SESSION['dims']['current_object']['id_module'];
}

if (!empty($array_modules)) {
	echo "<table style=\"width:100%;\"><tr><td width=\"28%\">
							<div style=\"margin-left:16px;\"><font class=\"fontgray\">".$_DIMS['cste']['_DIMS_LABEL_MODULES']."</font></div>
						</td>
						<td width=\"72%\"><font class=\"fontgray\">".$_DIMS['cste']['_DIMS_RESULT']."</font></td></tr>";
	// construction des blocks de recherche
	foreach($array_modules as $block) {
		if ($block['label']!="system") {
			if ($cur_moduleid>0 && $cur_moduleid==$block['id_module']) {
				$display="visibility:visible;display:block;";
				$class="modresultvert";
			}
			else {
				$display="visibility:hidden;display:none";
				$class="modresult";
			}

			if (file_exists("./common/modules/".$block['label']."/block_portal.php")) {
				echo "<tr><td width=\"28%\">
						<div id=\"modresult".$block['instanceid']."\" class=\"$class\">
							<img src=\"./common/modules/".$block['label']."./common/img/mod.gif\" alt=\"".$block['instancename']."\">&nbsp;".$block['instancename']."
						</div>
					</td>
					<td width=\"72%\"><span style=\"text-align:left;width:100%\" id=\"ressearch".$block['instanceid']."\"></span></td></tr>
					<tr><td colspan=\"2\">
						<div style=\"padding-left:4px;width:99%;$display\" id=\"content".$block['instanceid']."\"></div>
					</td></tr>
				";
			}
		}
	}
	echo "</table>";
	// switch to specific favorites' choice
	if ($_SESSION['dims']['desktop_collab']==dims_const::_DIMS_CSTE_SURVEY)
		echo "<SCRIPT LANGUAGE=\"javascript\">searchRecursiveFavorites(1);</script>";
	else
		echo "<SCRIPT LANGUAGE=\"javascript\">searchRecursiveFavorites(2);</script>";
}
?>

