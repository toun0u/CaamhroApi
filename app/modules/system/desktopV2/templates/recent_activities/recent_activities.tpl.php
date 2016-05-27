<?

$id_workspaces = array();
if(!$_SESSION['dims']['desktopfilters']['expand_to_all_workspace']) {
	$id_workspaces[] = $_SESSION['dims']['workspaceid'];
}

if (defined('_DESKTOP_V2_LIMIT_ACTIVITIES'))
	$lstOpp = $desktop->getRecentActivities($id_workspaces, _DESKTOP_V2_LIMIT_ACTIVITIES);
else
	$lstOpp = $desktop->getRecentActivities($id_workspaces);

// on affiche pas les participants par défaut
if (!isset($_SESSION['desktopV2']['content_content']['zone_participants']))
	$_SESSION['desktopV2']['content_content']['zone_participants'] = 0;
?>
<div class="recent_activities">
    <h2 class="h1_recent_activities"><?php echo $_SESSION['cste']['RECENT_EVENTS']; ?></h2>
	<?
	foreach($lstOpp as $activities){
		$activities->display(_DESKTOP_TPL_LOCAL_PATH.'/recent_activities/recent_activities_details.tpl.php');
	}
	?>
</div>
