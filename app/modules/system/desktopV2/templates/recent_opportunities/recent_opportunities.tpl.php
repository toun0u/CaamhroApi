<?

$id_workspaces = array();
if(!$_SESSION['dims']['desktopfilters']['expand_to_all_workspace']) {
	$id_workspaces[] = $_SESSION['dims']['workspaceid'];
}

if (defined('_DESKTOP_V2_LIMIT_OPPORTUNITIES'))
	$lstOpp = $desktop->getRecentOpportunities($id_workspaces, _DESKTOP_V2_LIMIT_OPPORTUNITIES);
else
	$lstOpp = $desktop->getRecentOpportunities($id_workspaces);

// on affiche pas les participants par défaut
if (!isset($_SESSION['desktopV2']['content_content']['zone_participants']))
	$_SESSION['desktopV2']['content_content']['zone_participants'] = 0;
?>
<div class="recent_opportunities">
    <h2 class="h1_recent_opportunities"><?php echo $_SESSION['cste']['RECENT_OPPORTUNITIES']; ?></h2>
	<?
	foreach($lstOpp as $opportunities){
		$opportunities->display(_DESKTOP_TPL_LOCAL_PATH.'/recent_opportunities/recent_opportunities_details.tpl.php');
	}
	?>
</div>
