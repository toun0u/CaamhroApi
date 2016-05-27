<?

if (!isset($_SESSION['share'])) {
    $_SESSION['share']=array();
    $_SESSION['share']['etape']=1;
    $_SESSION['share']['users']=array();
    $_SESSION['share']['contacts']=array();
    $_SESSION['share']['code']="";
    $_SESSION['share']['title']="";
    $_SESSION['share']['descriptif']="";

	$maxtoday = mktime(0,0,0,date('n'),date('j')+$sharefile_param->fields['nbdays'],date('Y'));
	$dateday=date('d/m/Y',$maxtoday);
	$maxtoday=dims_local2timestamp($dateday);
    $_SESSION['share']['timestp_finished']=$dateday;
    $_SESSION['share']['currentsearch']="";
}

$etape=dims_load_securvalue("etape",dims_const::_DIMS_NUM_INPUT,true,true);
if ($etape>0) $_SESSION['share']['etape']=$etape;
$bgclass="share_tdselected";
// traitement des etapes
echo "<link rel=\"stylesheet\" href=\"./common/modules/sharefile/include/styles.css\" type=\"text/css\"/>
	<div style=\"width:100%;margin:0 auto;border:0px;border-bottom:1px;border-color:#dadada;border-style:solid;\">
	<table style=\"width:100%\" cellpadding=\"0\" cellspacing=\"0\">
		<tr>
			<td class=\"$bgclass\" style=\"width:5%;\">";
if ($_SESSION['share']['etape']>1)
	echo "<img src=\"/modules/sharefile/img/1.png\"></td>";
else
	echo "<img src=\"/modules/sharefile/img/1_selec.png\"></td>";

echo "			<td class=\"$bgclass\" style=\"width:15%\">";

if ($_SESSION['share']['etape']>1)
			echo "<a href=\"".dims_urlencode($dims->getScriptEnv()."?op=add_share&etape=1")."\"><img src=\"/modules/sharefile/img/properties.png\" style=\"border:0px;\"></a></td>";
else
			echo "<img src=\"/modules/sharefile/img/properties.png\" style=\"border:0px;\"></td>";

if ($_SESSION['share']['etape']>=2) {

echo "                  <td style=\"width:10%\">
			<img src=\"/modules/sharefile/img/next.png\"></td>
			<td class=\"$bgclass\" style=\"width:5%\">";
if ($_SESSION['share']['etape']==2)
	echo "<img src=\"/modules/sharefile/img/2_selec.png\">";
else
	echo "<img src=\"/modules/sharefile/img/2.png\">";
echo "
			</td>
			<td class=\"$bgclass\" style=\"width:15%\">
				<a href=\"".dims_urlencode($dims->getScriptEnv()."?op=add_share&etape=2")."\"><img src=\"/modules/sharefile/img/users.png\" style=\"border:0px;\"></a>
			</td>";
}
else echo "<td style=\"width:30%\">&nbsp;</td>";

if ($_SESSION['share']['etape']==3) {
    echo "              <td class=\"$bgclass\" style=\"width:10%\"><img src=\"/modules/sharefile/img/next.png\">

			<td class=\"$bgclass\" style=\"width:5%\">";
				echo "<img src=\"/modules/sharefile/img/3_selec.png\"></td>
			<td class=\"$bgclass\" style=\"width:15%\">
				<a href=\"".dims_urlencode($dims->getScriptEnv()."?op=add_share&etape=3")."\"><img src=\"/modules/sharefile/img/file48.png\" style=\"border:0px;\"></a></td>
		</tr>";

}
else echo "<td style=\"width:30%\">&nbsp;</td>";

echo "</table></div>";

switch($_SESSION['share']['etape']) {
	case 1:
		include_once('./common/modules/sharefile/cms_share_etape1.php');
		break;
	case 2:
		if ($_SESSION['share']['timestp_finished']=="") {
			$maxtoday = mktime(0,0,0,date('n'),date('j')+$sharefile_param->fields['nbdays'],date('Y'));
			$dateday=date('d/m/Y',$maxtoday);
			$maxtoday=dims_local2timestamp($dateday);
		    $_SESSION['share']['timestp_finished']=$dateday;
		}
		include_once('./common/modules/sharefile/cms_share_etape2.php');
		break;
	case 3:
		include_once('./common/modules/sharefile/cms_share_etape3.php');
		break;
}

?>
