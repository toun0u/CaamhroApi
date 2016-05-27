<?
	$desktop_ticket=array();
	require_once(DIMS_APP_PATH . "/modules/system/desktop_ticket_menu.php");

	// display all informations
	$desktop_ticket=array();

	$desktop_ticket[dims_const::_DIMS_CSTE_TOVIEW]=array('NB' => $nbtoview,'title' => "<font style=\"font-size:11px\">".$_DIMS['cste']['_DIMS_TOVIEW']." (".$nbtoview.")</font>",'url' => dims_urlencode("admin.php?dims_action=public&dims_mainmenu=0&submenu=1&action=".dims_const::_DIMS_CSTE_TOVIEW),'SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOVIEW) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_receive.png');
	$desktop_ticket[dims_const::_DIMS_CSTE_TOVALID]=array('NB' => $nbtovalidate, 'title' => "<font style=\"font-size:11px\">".$_DIMS['cste']['_DIMS_TOVALID']." (".$nbtovalidate.")</font>",'url' =>  dims_urlencode("admin.php?dims_action=public&dims_mainmenu=0&submenu=1&action=".dims_const::_DIMS_CSTE_TOVALID),'SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOVALID) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_tovalid.png');
	$desktop_ticket[dims_const::_DIMS_CSTE_TOCONFIRM]=array('NB' => $nbtoconfirm, 'title' => "<font style=\"font-size:11px\">".$_DIMS['cste']['_DIMS_CONFIRM_WAIT']." (".$nbtoconfirm.")</font>",'url' =>  dims_urlencode("admin.php?dims_action=public&dims_mainmenu=0&submenu=1&action=".dims_const::_DIMS_CSTE_TOCONFIRM),'SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOCONFIRM) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_toconfirm.png');
	$desktop_ticket[dims_const::_DIMS_CSTE_SEND]=array('NB' => $nbtosent, 'title' => "<font style=\"font-size:11px\">".$_DIMS['cste']['_DIMS_MSG_SENT']." (".$nbtosent.")</font>",'url' =>	dims_urlencode("admin.php?dims_action=public&dims_mainmenu=0&submenu=1&action=".dims_const::_DIMS_CSTE_SEND),'SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_SEND) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_sent.png');

	// display tickets properties
	echo "<ul style=\"margin: 0px;padding: 0px;list-style: none;\">";

	foreach($desktop_ticket as $elem) {
		echo "<li><a href=\"".$elem['url']."\"><img src=\"".$elem['icon']."\" alt=\"\">&nbsp;".$elem['title']."</a></li>";
	}
	echo "</ul>";
?>
