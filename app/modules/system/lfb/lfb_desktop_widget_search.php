<div>
	<?
	echo '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td align="left">';
	echo $skin->open_widgetbloc( $_DIMS['cste']['_DIMS_LABEL_LAST_SEARCH'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', '','26px', '26px', '-15px', '-7px', '', '', '');
	// recherche des dernières recherches
	// on regarde si on a des requetes
	$sql = "select		*
			from		dims_campaign
			where		id_user= :userid
			and			id_workspace= :workspaceid
			and			temporary=1
			order by	timestp_modify desc
			limit		0,5";

	$res=$db->query($sql, array(
		':userid'		=> $_SESSION['dims']['userid'],
		':workspaceid'	=> $_SESSION['dims']['workspaceid']
	));

	if ($db->numrows($res)>0) {
		echo "<ul style=\"padding: 0px 10px;list-style: none;\">";
		while ($f=$db->fetchrow($res)) {
			$datvar=dims_timestamp2local($f['timestp_modify']);
			$chdate=$datvar['date']." - ";
			echo "<li>".$chdate.$f['query']."</li>";
		}
		echo "</ul>";
	}
	echo $skin->close_widgetbloc();
	echo '</td></tr></table>';
	?>
	<div id="desktop_detail_content" style="width:100%">
	<?
	// generation du code si objet
	if (isset($_SESSION['dims']['current_object']['id_record']) && isset($_SESSION['dims']['current_object']['id_object'])) {
		$mod=$dims->getModule($_SESSION['dims']['current_object']['id_module']);
		$modtype=$mod['label'];
		$dims_mod_opfile = DIMS_APP_PATH . "/modules/{$modtype}/op.php";
		$_GET['dims_op']="object_properties";
		if (file_exists($dims_mod_opfile)) require_once $dims_mod_opfile;
		require_once(DIMS_APP_PATH . "/modules/system/object_properties.php");
	}
	?>
	</div>
</div>
