<?php

if (!isset($_SESSION['project'])) {
	$_SESSION['project']=array();
	$_SESSION['project']['etape']=1;
	$_SESSION['project']['users']=array();
	$_SESSION['project']['contacts']=array();
	$_SESSION['project']['label']="";
	$_SESSION['project']['description']="";
	$_SESSION['project']['date_start']="";
	$_SESSION['project']['date_end']="";
}

$etape=dims_load_securvalue("etape",dims_const::_DIMS_NUM_INPUT,true,true);
if ($etape>0) $_SESSION['project']['etape']=$etape;
$bgclass="share_tdselected";
// traitement des etapes
echo "<link rel=\"stylesheet\" href=\"./common/modules/system/include/styles.css\" type=\"text/css\"/>
	<div style=\"width:100%;margin:0 auto;border:0px;border-bottom:1px;border-color:#dadada;border-style:solid;\">
	<table style=\"width:100%\" cellpadding=\"0\" cellspacing=\"0\">
		<tr>
			<td class=\"$bgclass\" style=\"width:5%;\"><img src=\"/modules/system/img/1.png\"></td>
			<td class=\"$bgclass\" style=\"width:15%\">
				<a href=\"".dims_urlencode($dims->getScriptEnv()."?op=add_project&etape=1")."\"><img src=\"/modules/system/img/properties.png\" style=\"border:0px;\"></a></td>
			";

if ($_SESSION['project']['etape']>=2) {

echo "		<td style=\"width:10%\">
			<img src=\"/modules/system/img/next.png\"></td>
			<td class=\"$bgclass\" style=\"width:5%\">";
				echo "<img src=\"/modules/system/img/2.png\">";
echo "
			</td>
			<td class=\"$bgclass\" style=\"width:15%\">
				<a href=\"".dims_urlencode($dims->getScriptEnv()."?op=add_project&etape=2")."\"><img src=\"/modules/system/img/users.png\" style=\"border:0px;\"></a>
			</td>";
}
else echo "<td style=\"width:30%\">&nbsp;</td>";

if ($_SESSION['project']['etape']==3) {
	echo "				<td class=\"$bgclass\" style=\"width:10%\"><img src=\"/modules/system/img/next.png\">

			<td class=\"$bgclass\" style=\"width:5%\">";
				echo "<img src=\"/modules/system/img/3.png\"></td>
			<td class=\"$bgclass\" style=\"width:15%\">
				<a href=\"".dims_urlencode($dims->getScriptEnv()."?op=add_project&etape=3")."\"><img src=\"/modules/system/img/file48.png\" style=\"border:0px;\"></a></td>
		</tr>";

}
else echo "<td style=\"width:30%\">&nbsp;</td>";

echo "</table></div>";

switch($_SESSION['project']['etape']) {
	case 1:
		require_once(DIMS_APP_PATH . '/modules/system/desktop_project_create_etape1.php');
		break;
	case 2:
		require_once(DIMS_APP_PATH . '/modules/system/desktop_project_create_etape2.php');
		break;
	case 3:
		require_once(DIMS_APP_PATH . '/modules/system/desktop_project_create_etape3.php');
		break;
}

?>
