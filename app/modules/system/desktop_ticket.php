<div id="content_onglet">
	<div id="menu_content_onglet">
	<?
	require_once(DIMS_APP_PATH . "/modules/system/desktop_ticket_menu.php");
	$desktop_ticket=array();
	$tabs[dims_const::_DIMS_SUBMENU_SEARCH]['icon'] = "./common/img/search.png";
	$desktop_ticket[dims_const::_DIMS_CSTE_TOVIEW]=array('NB' => $nbtoview,'title' => $_DIMS['cste']['_DIMS_TOVIEW']." (".$nbtoview.")",'url' => dims_urlencode("admin.php?dims_action=public&dims_mainmenu=0&submenu=1&action=".dims_const::_DIMS_CSTE_TOVIEW),'SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOVIEW) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_receive.png');
	$desktop_ticket[dims_const::_DIMS_CSTE_TOVALID]=array('NB' => $nbtovalidate, 'title' => $_DIMS['cste']['_DIMS_TOVALID']." (".$nbtovalidate.")",'url' => dims_urlencode("admin.php?dims_action=public&dims_mainmenu=0&submenu=1&action=".dims_const::_DIMS_CSTE_TOVALID),'SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOVALID) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_tovalid.png');
	$desktop_ticket[dims_const::_DIMS_CSTE_TOCONFIRM]=array('NB' => $nbtoconfirm, 'title' => $_DIMS['cste']['_DIMS_CONFIRM_WAIT']." (".$nbtoconfirm.")",'url' => dims_urlencode("admin.php?dims_action=public&dims_mainmenu=0&submenu=1&action=".dims_const::_DIMS_CSTE_TOCONFIRM),'SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_TOCONFIRM) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_toconfirm.png');
	$desktop_ticket[dims_const::_DIMS_CSTE_SEND]=array('NB' => $nbtosent, 'title' => $_DIMS['cste']['_DIMS_MSG_SENT']." (".$nbtosent.")",'url' => dims_urlencode("admin.php?dims_action=public&dims_mainmenu=0&submenu=1&action=".dims_const::_DIMS_CSTE_SEND),'SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_SEND) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_sent.png');
	$desktop_ticket[dims_const::_DIMS_CSTE_CREATE]=array('NB' => '', 'title' => $_DIMS['cste']['_DIMS_MSG_CREATE']."</font>", 'javascript' => '','SELECTED' => ($_SESSION['dims']['desktop_ticket'] == dims_const::_DIMS_CSTE_CREATE) ? 'selected' : '', 'position' => 'left', 'width' => '', 'function' => '1','icon'=>'./common/img/mail_create.png');
	echo $skin->create_onglet($desktop_ticket,$_SESSION['dims']['desktop_ticket'],1,'0',"onglet");
	?>
	</div>
	<table cellpadding="2" cellspacing="0" style="width:100%;background:#fcfcfc" >
		<tr>
			<td>
				<div class="contentdesktop" style="width:100%;margin:0 auto;" id="contentdesktopticket">
				<?
					$sumvalidate=0;
					$cur_moduleid=0;
					$tpl_columns=array();
					$array_modules=$dims->getModules($_SESSION['dims']['workspaceid']);
					unset($_SESSION['dims']['current_object']);
					require_once(DIMS_APP_PATH . '/modules/system/public_tickets.php');
				?>
				</div>
			</td>
		</tr>
	</table>
</div>
