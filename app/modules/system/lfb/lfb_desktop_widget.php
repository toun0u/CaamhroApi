<div>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<?
	// construction des blocs
	$type_b = array();
	if (isset($_SESSION['dims']['currentworkspace']['activecontact'])  && $_SESSION['dims']['currentworkspace']['activecontact']==1) {
		$type_b[2] = array();
		$type_b[2]['id'] = 3;

		if (_DIMS_SESSIONTIME <= 86400) {
			$timestplimit=date("YmdHis",mktime(0, 0, date("G")*3600+date("i")*60-1*_DIMS_SESSIONTIME, date("m"), date("d"), date("Y")));
		}
		else $timestplimit = date("YmdHis",mktime(0, 0, date("G")*3600+date("i")*60-1*86400, date("m"), date("d"), date("Y")));;

		$sql_pmod = "	SELECT			distinct c.firstname, c.lastname, c.id as id_pers, c.timestp_modify
				FROM			dims_mod_business_contact as c
				INNER JOIN		dims_user as u
				ON				u.id_contact = c.id
				INNER JOIN		dims_connecteduser as cu
				ON				cu.user_id =u.id
				and				cu.timestp > :timestplimit
				and				cu.workspace_id= :workspaceid ";


		$res_p = $db->query($sql_pmod, array(
			':timestplimit' => $timestplimit,
			':workspaceid'	=> $_SESSION['dims']['workspaceid']
		));
		$nb_resp = $db->numrows($res_p);

		$_SESSION['dims']['connectedusers']=$nb_resp;

		if ($_SESSION['dims']['connectedusers']<=1) $conusers=$_SESSION['dims']['connectedusers']." ".$_DIMS['cste']['_DIMS_CONNECTED_USER'];
		else $conusers=$_SESSION['dims']['connectedusers']." ".$_DIMS['cste']['_DIMS_CONNECTED_USERS'];
		$type_b[2]['title'] = $conusers;
		$type_b[2]['id_div'] = "desktopuser";
		$type_b[2]['onclick'] = '';
		$type_b[2]['include'] = DIMS_APP_PATH . "/modules/system/lfb_widget_desktop_contacts.php";
		$type_b[2]['width'] = "100%";
		$type_b[2]['image'] = "./common/img/contacts.png";
	}

	if ($_SESSION['dims']['currentworkspace']['activeticket'] && $_SESSION['dims']['currentworkspace']['activeticket']==1) {
		$type_b[3] = array();
		$type_b[3]['id'] = 1;
		$type_b[3]['title'] = $_DIMS['cste']['_DIMS_LABEL_ADMIN_MESSAGES'];
		$type_b[3]['include'] = DIMS_APP_PATH . "/modules/system/lfb_widget_desktop_tickets.php";
		$type_b[3]['id_div'] = "desktopticket";
		$type_b[3]['onclick'] = dims_const::_DIMS_SUBMENU_MESSAGE;
		$type_b[3]['width'] = "100%";
		$type_b[3]['image'] = "./common/img/widget_email.png";
	}


	$type_b[4] = array();
	$type_b[4]['id'] = 1;
	$type_b[4]['title'] = $_DIMS['cste']['_DIMS_LABEL_TAGS'];
	$type_b[4]['include'] = DIMS_APP_PATH . "/modules/system/lfb/lfb_widget_desktop_tags.php";
	$type_b[4]['id_div'] = "desktoptag";
	$type_b[4]['onclick'] = "admin.php?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_ACTIVITIES."&action=tags";
	$type_b[4]['width'] = "100%";
	$type_b[4]['image'] = "./common/img/widget_tag.png";

	foreach($type_b as $num => $bloc) {
		echo '<tr><td align="left">';
		echo $skin->open_widgetbloc($bloc['title'], 'width:'.$bloc['width'].';', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', $bloc['image'],'26px', '26px', '-15px', '-7px', 'admin.php?dims_action=public&dims_mainmenu=0&submenu='.$bloc['onclick'], '', '');
			if (isset($bloc['include']) && file_exists($bloc['include'])) {
				require_once($bloc['include']);
			}

		echo $skin->close_widgetbloc();
		echo '</td></tr>';
	}
	// test si planning actif ou non
	if (isset($workspace['planning']) && $workspace['planning']) {
	?>
		<script language="javascript">
		function afficheDesktopPlanning(params) {
			dims_xmlhttprequest_todiv("admin-light.php","dims_op=lfb_xml_desktop_lstplanning"+params,'','widget_desktop_lstplanning');
		}

		function refreshDesktopPlanning() {
			afficheDesktopPlanning('&cat=-1');
			//desktop_affiche_planning();
			return(true);
		}

		function desktop_affiche_planning(params) {
			dims_xmlhttprequest_todiv("admin-light.php","dims_op=lfb_xml_desktop_planning_month"+params,'','widget_monthview');
		}

		//setInterval("refreshDesktopPlanning()",30000);
		</script>
		<?
		echo "<tr><td align=\"center\"><div id=\"widget_desktop_xmlplanning\" style=\"width:100%;text-align:center;\">
				<span id=\"widget_monthview\" style=\"width:100%;padding:0px 0px 15px 2px;\">";
		require_once(DIMS_APP_PATH . "/modules/system/crm_desktop_planning_month.php");
		echo "</span></div></td></tr>";
	}
	?>
	</table>
</div>
