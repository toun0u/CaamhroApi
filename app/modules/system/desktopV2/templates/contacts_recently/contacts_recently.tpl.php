<?

$id_workspaces = array();
if(!$_SESSION['dims']['desktopfilters']['expand_to_all_workspace']) {
	$id_workspaces[] = $_SESSION['dims']['workspaceid'];
}

if (defined('_DESKTOP_V2_LIMIT_COMPANIES'))
	$lstComp = $desktop->getRecentContacts($id_workspaces, _DESKTOP_V2_LIMIT_COMPANIES);
else
	$lstComp = $desktop->getRecentContacts($id_workspaces);
?>
<div class="companies_recently">
    <h2 class="h1_zone_companies_recently"><?php echo $_SESSION['cste']['CONTACTS_RECENTLY_ACTIVE']; ?></h2>
	<?
	foreach($lstComp as $contact){
		$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/contacts_recently/contacts_recently_details.tpl.php');
	}
	?>
</div>
