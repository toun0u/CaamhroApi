<?php
$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);

if (!isset( $_SESSION['dims']['submainmenu']) || $_SESSION['dims']['submainmenu']=="" || (!$currentworkspace['contact'] && $_SESSION['dims']['submainmenu']=="dims_const::_DIMS_SUBMENU_CONTACT"))	{
   if ($currentworkspace['contact']) $_SESSION['dims']['submainmenu']=dims_const::_DIMS_SUBMENU_ACTIVITIES;
   else $_SESSION['dims']['submainmenu']=dims_const::_DIMS_SUBMENU_ACTIVITIES;
}

$op=dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,false);
$submenu=dims_load_securvalue('submenu',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['submainmenu'],$_SESSION['dims']['submainmenu']);

$workspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);

// construction du menu de la homepage
$tabs=array();

$tabs[dims_const::_DIMS_SUBMENU_ACTIVITIES]['title'] = $_DIMS['cste']['_DIMS_LABEL_ACTIVITY'];
$tabs[dims_const::_DIMS_SUBMENU_ACTIVITIES]['url'] = "admin.php?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_ACTIVITIES;
$tabs[dims_const::_DIMS_SUBMENU_ACTIVITIES]['icon'] = "./common/img/activity.png";
$tabs[dims_const::_DIMS_SUBMENU_ACTIVITIES]['width'] = 110;
$tabs[dims_const::_DIMS_SUBMENU_ACTIVITIES]['position'] = 'left';

if (isset($currentworkspace['contact']) && $currentworkspace['contact']==1) {
	$tabs[dims_const::_DIMS_SUBMENU_CONTACT]['title'] = $_DIMS['cste']['_DIMS_LABEL_CONTACTS'];
	$tabs[dims_const::_DIMS_SUBMENU_CONTACT]['url'] = "admin.php?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_CONTACT;
	$tabs[dims_const::_DIMS_SUBMENU_CONTACT]['icon'] = "./common/img/contact.png";
	$tabs[dims_const::_DIMS_SUBMENU_CONTACT]['width'] = 100;
	$tabs[dims_const::_DIMS_SUBMENU_CONTACT]['position'] = 'left';
}

if ($_SESSION['dims']['currentworkspace']['activeticket'] && $_SESSION['dims']['currentworkspace']['activeticket']==1) {
	$tabs[dims_const::_DIMS_SUBMENU_MESSAGE]['title'] = $_DIMS['cste']['_DIMS_LABEL_ADMIN_MESSAGES'];
	$tabs[dims_const::_DIMS_SUBMENU_MESSAGE]['url'] = "admin.php?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_MESSAGE;
	$tabs[dims_const::_DIMS_SUBMENU_MESSAGE]['icon'] = "./common/img/icon_tickets.gif";
	$tabs[dims_const::_DIMS_SUBMENU_MESSAGE]['width'] = 110;
	$tabs[dims_const::_DIMS_SUBMENU_MESSAGE]['position'] = 'left';
}
if (isset($currentworkspace['contact'])  && $currentworkspace['contact']==1) {
	$tabs[dims_const::_DIMS_SUBMENU_VEILLE]['title'] = $_DIMS['cste']['_DIMS_LABEL_VEILLE'];
	$tabs[dims_const::_DIMS_SUBMENU_VEILLE]['url'] = "admin.php?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_VEILLE;
	$tabs[dims_const::_DIMS_SUBMENU_VEILLE]['icon'] = "./common/img/view.png";
	$tabs[dims_const::_DIMS_SUBMENU_VEILLE]['width'] = 90;
	$tabs[dims_const::_DIMS_SUBMENU_VEILLE]['position'] = 'left';
}

if (true || $_SESSION['dims']['currentworkspace']['activesearch'] && $_SESSION['dims']['currentworkspace']['activesearch']==1) {
	$tabs[dims_const::_DIMS_SUBMENU_SEARCH]['title'] = $_DIMS['cste']['_DIMS_LABEL_SEARCH'];
	$tabs[dims_const::_DIMS_SUBMENU_SEARCH]['url'] = "admin.php?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_SEARCH;
	$tabs[dims_const::_DIMS_SUBMENU_SEARCH]['icon'] = "./common/img/search.png";
	$tabs[dims_const::_DIMS_SUBMENU_SEARCH]['width'] = 120;
	$tabs[dims_const::_DIMS_SUBMENU_SEARCH]['position'] = 'left';
}
if (isset($currentworkspace['planning']) && $currentworkspace['planning']==1) {
	$tabs[dims_const::_DIMS_SUBMENU_EVENT]['title'] = $_DIMS['cste']['_DIMS_LABEL_EVENTS'];
	$tabs[dims_const::_DIMS_SUBMENU_EVENT]['url'] = "admin.php?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_EVENT;
	$tabs[dims_const::_DIMS_SUBMENU_EVENT]['icon'] = "./common/img/event.png";
	$tabs[dims_const::_DIMS_SUBMENU_EVENT]['width'] = 140;
	$tabs[dims_const::_DIMS_SUBMENU_EVENT]['position'] = 'left';
}
?>
<table style="width:99%;" cellpadding="0" cellspacing="2">
	<tr>
		<td valign="top" style="width:76%;height:30px;">
			<?php
			if ($_SESSION['dims']['submainmenu']==dims_const::_DIMS_SUBMENU_SEARCH)
				echo "<div id=\"content_onglet_transparent\" style=\"border-bottom:0px;\"><div id=\"menu_content_onglet\">";
			else
				echo "<div id=\"content_onglet\" style=\"border-bottom:0px;\"><div id=\"menu_content_onglet\">";

				//echo $skin->create_onglet($tabs,$submenu,true,'0',"onglet");
			echo "</div>";

			$title="";
			switch($_SESSION['dims']['submainmenu']) {
					case dims_const::_DIMS_SUBMENU_CONTACT :
							require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_contact.php');
							break;
					case dims_const::_DIMS_SUBMENU_MESSAGE:
						echo "<div id=\"desktopticket\" style=\"float:left;width:100%\">";
						require_once(DIMS_APP_PATH . '/modules/system/desktop_ticket.php');
						echo "</div>";
						break;
					case dims_const::_DIMS_SUBMENU_ACTIVITIES:
						if (isset($_SESSION['dims']['current_ticket'])) unset($_SESSION['dims']['current_ticket']);
						require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_activities.php');
						break;
					case dims_const::_DIMS_SUBMENU_PROJECT :
							require_once(DIMS_APP_PATH . '/modules/system/desktop_project.php');
							break;
					case dims_const::_DIMS_SUBMENU_CONTACT:
							require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_contact.php');
							break;
					case dims_const::_DIMS_SUBMENU_EVENT:
							require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_users_events.php');
							break;
					case dims_const::_DIMS_SUBMENU_VEILLE:
							require_once(DIMS_APP_PATH . '/modules/system/lfb_public_contact_watch.php');
							break;
					case dims_const::_DIMS_SUBMENU_SEARCH:
							require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_search.php');
							break;

			}
			echo "</div>"
		?>
		</td>
		<td style="width:20%;" valign="top" rowspan="2">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<div id="desktop_widget_right">
						<?php
						if ($_SESSION['dims']['submainmenu']!=dims_const::_DIMS_SUBMENU_SEARCH)
							require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_widget.php');
						else
							require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_widget_search.php');
						?>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
		 <?php
		if((isset($_SESSION['dims']['current_ticket']) || isset($_SESSION['dims']['current_object'])) && isset($_SESSION['dims']['submainmenu']) && $_SESSION['dims']['submainmenu']!=""
		&&	($_SESSION['dims']['submainmenu']==dims_const::_DIMS_SUBMENU_MESSAGE && ($_SESSION['dims']['desktop_ticket'] != dims_const::_DIMS_CSTE_CREATE))
		)  {
		   // echo $skin->open_widgetbloc($title,'width:100%','','');
			echo "<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;border:#738CAD 1px solid;margin-top:2px;margin-bottom:2px;background:#fcfcfc\"><tr><td>";
			if (isset($_SESSION['dims']['current_object']) && isset($_SESSION['dims']['current_object']['id_module'])) {
				$cur_moduleid=$_SESSION['dims']['current_object']['id_module'];
				$cur_objectid=$_SESSION['dims']['current_object']['id_object'];
				$cur_recordid=$_SESSION['dims']['current_object']['id_record'];
				$displaydetail="visibility:visible;display:block;";
				$displayright="visibility:hidden;display:none;";
			}
			else {
				$displaydetail="visibility:hidden;display:none;";
				$displayright="visibility:visible;display:block;";
			}
			?>
			<div id="desktop_detail_content" style="<? echo $displaydetail; ?>"></div>
			<div id="desktop_right_content" style="<? echo $displayright; ?>">
			</div>
			<?php

			if (!empty($_SESSION['dims']['current_ticket']) && isset($_SESSION['dims']['current_ticket']['id_ticket'])) {
				echo "<script language=\"javascript\">";
				require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');

				$ticket = new ticket();
				$ticketid=$_SESSION['dims']['current_ticket']['id_ticket'];
				$ticket->open($ticketid);
				//echo "Event.observe(window, 'load',viewPropertiesTicket(".$ticket->fields['id'].",".$ticket->fields['id_record'].",".$ticket->fields['id'].",".$ticket->fields['id_module']."));";
				echo "window.onload=viewPropertiesTicket(".$ticket->fields['id'].",".$ticket->fields['id_record'].",".$ticket->fields['id'].",".$ticket->fields['id_module'].");";
				echo "</script>";
			}
			elseif ($cur_recordid>0 && $cur_moduleid>0) {
				echo "<script language=\"javascript\">";
				echo "window.onload=viewPropertiesObject(".$cur_objectid.",".$cur_recordid.",".$cur_moduleid.");";
				echo "</script>";
			}
			echo "</td></tr></table>";
		   // echo $skin->close_widgetbloc();
		}
		?>

		</td>
	</tr>
</table>
