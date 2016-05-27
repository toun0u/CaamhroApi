<?
/*
<script language="javascript">
function afficheDesktopPlanning(params) {
	dims_xmlhttprequest_todiv("admin-light.php","dims_op=xml_desktop_lstplanning"+params,'','desktop_lstplanning');
}

function refreshDesktopPlanning() {
	afficheDesktopPlanning('&cat=-1');
	return(true);
}

function desktop_affiche_planning(params) {
	alert("ici");
	dims_xmlhttprequest_todiv("admin-light.php","dims_op=xml_desktop_planning_month"+params,'','monthview');
}

setInterval("refreshDesktopPlanning()",30000);
</script>
*/
require_onceDIMS_APP_PATH . "/modules/system/include/business.php";

echo "<div id=\"desktop_lstplanning\" style=\"float:left;width:65%\">";
require_once(DIMS_APP_PATH . "/modules/system/desktop_planning.php");
echo "</div>";
/*
echo "<div id=\"desktop_xmlplanning\" style=\"width:35%;float:right;text-align:right;\">
<span id=\"monthview\" style=\"width:205px;padding:0px 0px;\">";
require_once(DIMS_APP_PATH . "/modules/system/desktop_planning_month.php");
echo "</span></div>";
*/
?>
