<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
require_once DIMS_APP_PATH . '/modules/system/class_action.php';

$choice_from = dims_load_securvalue('choice_from', _DIMS_NUM_INPUT, true, true);

$_SESSION['dims']['choice_ct_from'] = $choice_from;

$id_user_from = dims_load_securvalue('id_user_from', _DIMS_NUM_INPUT, true, true);
if ($id_user_from>0) $_SESSION['dims']['search_ct_from']=$id_user_from;

if (!isset($_SESSION['dims']['search_ct_from'])) {
	$user= new user();
	$user->open($_SESSION['dims']['userid']);
	$ct_id_user=$user->fields['id_contact'];
	$_SESSION['dims']['search_ct_from']=$ct_id_user;
}

$links=array();
$contacts=array();
$lstct=array();
$lstctcompare=array();

$contact_id=dims_load_securvalue('xml_id', _DIMS_CHAR_INPUT, true);

$contact_id=str_replace("ct_","",$contact_id);
// appel du flash
ob_end_clean();

/*
<div style="height:70px;">
	<?
	$url= urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."/admin-light.php?dims_op=socialbrowser&xml_id="));
	$logo=urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."/lfb.png"));
	$link='admin-light.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_GRAPH.'&logowidth=152&logoheight=68&logo='.$logo.'&xml_id=ct_'.$contact_id.'&url='.$url.'"';
?>
	<form name="form_search_graph" action="<? echo $link; ?>" method="POST">
	<table style="width:100%">
		<tr>
			<td style="width:25%">
				Contexte<br>
				<?
				// construction de la liste des contacts
				$work=new workspace();
				$work->open($_SESSION['dims']['workspaceid']);
				$lstusers=$work->getusers();
				$check=($choice_from==0) ? 'checked' : '';

				echo "<input type=\"radio\" onclick=\"javascript:document.form_search_graph.submit();\" name=\"choice_from\"".$check." value=\"0\">Par individu&nbsp;
				<select onchange=\"javascript:document.form_search_graph.submit();\" name=\"id_user_from\">";
				foreach ($lstusers as $id=>$user) {
					$select=($_SESSION['dims']['search_ct_from']==$user['id_contact']) ? "selected" : "";
					echo "<option value=\"".$user['id_contact']."\" $select>".strtoupper(substr($user['firstname'],0,1)).". ".$user['lastname']."</option>";
				}
				echo "</select><br>";
				$check=($choice_from==1) ? 'checked' : '';
				echo "<input type=\"radio\" onclick=\"javascript:document.form_search_graph.submit();\" name=\"choice_from\" ".$check." value=\"1\">Par mon entité";

				?>
			</td>
			<td style="width:25%">
				Besoin
			</td>
			<td style="width:25%">
				Profil
			</td>
			<td style="width:25%">
				<img src="./common/img/search.gif" onclick="javascript:document.form_search_graph.submit();" alt="Recherche">
			</td>
		</tr>
	</table>
	</form>
</div>
*/
?>
<script src="<? echo $dims->getProtocol().$dims->getHttpHost(); ?>/socialbrowser/bin/AC_OETags.js" language="javascript"></script>

<!--  BEGIN Browser History required section -->
<script src="./socialbrowser/bin/history/history.js" language="javascript"></script>
<!--  END Browser History required section -->

<style>
body { margin: 0px; overflow:hidden }
</style>
<script language="JavaScript" type="text/javascript">
<!--
// -----------------------------------------------------------------------------
// Globals
// Major version of Flash required
var requiredMajorVersion = 9;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 0;
// -----------------------------------------------------------------------------
// -->
</script>
</head>

<body scroll="no">
<script language="JavaScript" type="text/javascript">
<!--
// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
var hasProductInstall = DetectFlashVer(6, 0, 65);

// Version check based upon the values defined in globals
var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

if ( hasProductInstall && !hasRequestedVersion ) {
	// DO NOT MODIFY THE FOLLOWING FOUR LINES
	// Location visited after installation is complete if installation is required
	var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
	var MMredirectURL = window.location;
	document.title = document.title.slice(0, 47) + " - Flash Player Installation";
	var MMdoctitle = document.title;

	AC_FL_RunContent(
		"src", "playerProductInstall",
		"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"",
		"width", "100%",
		"height", "100%",
		"align", "middle",
		"id", "Social",
		"quality", "high",
		"bgcolor", "#869ca7",
		"name", "Social",
		"allowScriptAccess","sameDomain",
		"type", "application/x-shockwave-flash",
		"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
} else if (hasRequestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(
			"src", "Social",
			"width", "100%",
			"height", "100%",
			"align", "middle",
			"id", "Social",
			"quality", "high",
			"bgcolor", "#869ca7",
			"name", "Social",
			"allowScriptAccess","sameDomain",
			"type", "application/x-shockwave-flash",
			"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
  } else {	// flash is too old or we can't detect the plugin
	var alternateContent = 'Alternate HTML content should be placed here. '
	+ 'This content requires the Adobe Flash Player. '
	+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
	document.write(alternateContent);  // insert non-flash content
  }
// -->
</script>

<noscript>
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			id="Social" width="100%" height="100%"
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
			<param name="movie" value="<? echo $dims->getProtocol().$dims->getHttpHost(); ?>/socialbrowser/bin/Social.swf" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#869ca7" />
			<param name="allowScriptAccess" value="sameDomain" />
			<embed src="<? echo $dims->getProtocol().$dims->getHttpHost(); ?>/socialbrowser/bin/Social.swf" quality="high" bgcolor="#869ca7"
				width="100%" height="100%" name="Social" align="middle"
				play="true"
				loop="false"
				quality="high"
				allowScriptAccess="sameDomain"
				type="application/x-shockwave-flash"
				pluginspage="http://www.adobe.com/go/getflashplayer">
			</embed>
	</object>
</noscript>
</body>


<?
die();
?>
